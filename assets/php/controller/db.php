<?php
/**
 *  FUNCION PUNTERO, DEVUELVE INSTANCIA DE LA CONEXION DE LA BASE DE DATOS
*/
function &getConnectionDB(){
    try {
        $bdConn =  new PDO(DB_INFO->DB_ENGINE . ':host='. DB_INFO->DB_HOST .'; dbname='. DB_INFO->DB_NAME .';charset=utf8', DB_INFO->DB_USER, DB_INFO->DB_PASS);
        $bdConn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $bdConn->setAttribute(PDO::ATTR_PERSISTENT, false);
        $bdConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        http_response_code(500);
        $message = "No se pudo efectuar conexión con la base de datos, código de error: SQLSTATE-" . $e->getCode();
        echo json_encode(array('errors' => $message), JSON_UNESCAPED_UNICODE );
        die();
    }
    return $bdConn;
};
/**
 * EFECTUA CREACION DE ENTRADAS CONTRA LA BASE DE DATOS
*/
function addData(stdClass $table, array $parentEntries, array $args, mixed $foreignTables = null, ...$skippables){
    $pdo = getConnectionDB(); 
    if(count($skippables) > 0) $table->data = array_diff_ukey($table->data, array_flip($skippables), fn($a, $b) => $a <=> $b);
    $args = array_udiff($args, $parentEntries, fn($a, $b) => ($a['id'] ?? $a[$table->id]) <=> ($b['id'] ?? $b[$table->id]));
    $args = array_values(array_map("unserialize", array_unique(array_map("serialize", $args))));
    if(sizeof($args) === 0) return;
    $sql = "INSERT INTO " . $table->name . " (?)" . (is_null($foreignTables) ? ' VALUES ' : ' ');
    try {
        foreach ($args as $index => $value) {
            if($index === 0) {
                $table->data = array_slice(array_keys($table->data), 0, sizeof($value));
                $sql = str_replace('?', implode(', ', $table->data), $sql);
            }
            if(sizeof($table->data) !== sizeof($value)) {
                return("Ocurrió un error al intentar añadir " . $value['id'] . " a la tabla '" . $table->name . "'");
            }
            $index += 1;
            if(!is_null($foreignTables)){
                $sql .= 'SELECT ' . (implode(', ', array_map(function($key, $val) use($index, $value, $foreignTables){
                    $fkTableName = current(array_filter(array_keys($foreignTables), fn($v) => ($v === $key)));
                    if(is_string($fkTableName)){
                        $fkTable = current(array_keys($foreignTables[$fkTableName]));
                        $fkId = current(array_values($foreignTables[$fkTableName]));
                        $fkLooked = current(array_reverse(array_values($foreignTables[$fkTableName])));
                        if(sizeof($value) === array_search($val, array_values($value)) + 1){
                            return "$fkTable.$fkId FROM $fkTable WHERE LOWER($fkTable.$fkLooked) = LOWER(" . (':' . $key . '_' . $index) . ')';
                        }
                    }
                    return ':' . $key . '_' . $index;                        
                }, array_keys($value), array_values($value))));
                $sql .= ($index === (sizeof($args)) ? ';' : ' UNION ALL ');
            }else {
                $sql .= '(' . (implode(', ', array_map(fn($v) => (':' . $v . '_' . $index), array_keys($value)))) . ')';
                $sql .= ($index === (sizeof($args)) ? ';' : ', ');
            }
        }
        $pdo->beginTransaction();
        $stmt = $pdo->prepare($sql);
        foreach ($args as $index => $user) {
            foreach ($user as $key => $value) {
                $stmt->bindValue((':' . $key . '_' . ($index + 1)), $value, PDO::PARAM_STR);
            }
        }
        if($stmt->execute() && sizeof($args) > 0){
            $pdo->commit();
            return true;
        }
        return false;
    }catch (InvalidArgumentException|PDOException|Exception $e) {
        $pdo->rollBack();
        return($e->getMessage());
    }
}
/**
 * EFECTUA CREACION DE ENTRADAS SOBRE LA TABLA DE LAS CATEGORIAS
*/
function addCategory(array $args) {
    $response = array_key_assoc_check($args, 'id', 'desc');
    if(sizeof($response['errors']) !== 0) {
        $response['data'] = false;
        return $response;
    }
    return addData((object) retrieveTableInformation('categories', 'catName'), getCategories(), $args, null, 'catId');
}
/**
 * EFECTUA CREACION DE ENTRADAS SOBRE LA TABLA DE LOS PRODUCTOS
*/
function addProduct(array $args) {
    $response = array_key_assoc_check($args, 'id', 'description', 'price', 'stock', 'category');
    if(sizeof($response['errors']) !== 0) {
        $response['data'] = false;
        return $response;
    }
    $foreignTables = array(
        'category' => [ 'categories' => 'catId', 'looked' => 'catName']
    );
    return addData((object) retrieveTableInformation('products', 'product'), getProducts() ?? array(), $args, $foreignTables, 'prodId');
}
/**
 * EFECTUA CREACION DE ENTRADAS SOBRE LA TABLA DE LOS USUARIOS
*/
function addUser(array $args) {
    $response = array_key_assoc_check($args, 'id', 'pass', 'fullname', 'surname', 'mail');
    if(sizeof($response['errors']) !== 0) {
        $response['data'] = false;
        return $response;
    }
    return addData((object) retrieveTableInformation('user', 'userId'), getUsers(), $args, null);
}
/**
 * FUNCION ENCARGADA DE DEVOLVER INFORMACION DE UNA TABLA CON RESPECTO AL ESQUEMA DE LA BASE DE DATOS INTRODUCIDA
*/
function retrieveTableInformation(string $actualTable, string $id = null){
    $bdConn = getConnectionDB();
    $actualDB = $bdConn->query('select database()')->fetchColumn();
    $actualTable = sanitize_string($actualTable, true);
    $query = "SELECT COLUMN_NAME AS COL, COLUMN_TYPE AS SIZE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?;";
    $stmt = $bdConn->prepare($query);
    $stmt->bindParam(1, $actualDB, PDO::PARAM_STR);
    $stmt->bindParam(2, $actualTable, PDO::PARAM_STR);
    $stmt->execute();
    $queryRes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $queryRes = array_map(function($value) {
        preg_match('/.*\(([^)]*)\)/', $value, $out);
        if($out){
            $value = get_last_dynamic_array($out);
        }
        elseif(preg_match('(date|time|year)', $value, $out)){
            $value = 20;
        }
        return intval(preg_replace("/[^\d]/Dm", '', $value));
    }, $queryRes);
    return array('name' => $actualTable, 'id' =>  $id ?? array_key_first($queryRes), 'data' => $queryRes);
}
/**
 * EFECTUA CONSULTAS SELECT CONTRA LA BASE DE DATOS
*/
function getData(bool $all = true, String $tableName, string $locator = '', array $args = array()){
    if(sizeof($args) === 0 && !$all) return;
    $pdo = getConnectionDB();
    $sql = "SELECT * FROM $tableName" . ($all !== true ? " WHERE $locator IN " : '');
    try {
        $args = array_values(array_map("unserialize", array_unique(array_map("serialize", $args))));
        foreach ($args as $index => $value) {
            $resCheck = check_params_req($value, 'id');
            if($resCheck !== true) throw new InvalidArgumentException('Error en el argumento Nº ' . $index + 1 . ', razón: ' . $resCheck);
            $bindKey = array_search($value, $args) + 1;
            $value = array_filter(array_map( 
                fn($val) => array_search($val, array_keys($value)) === 0 ? ':' . $val . '_' . $bindKey : null , array_keys($value)
            ), fn($val) => $val !== null);
            if(!$all) {
                $sql .= ($bindKey === 1 ? '(' : '') . (implode(', ', $value)) . ($bindKey === (sizeof($args)) ? ');' : ', ');
            }
        }
        $pdo->beginTransaction();
        $stmt = $pdo->prepare($sql);
        if(!$all) {
            foreach ($args as $index => $parent) {
                foreach ($parent as $key => $child) {
                    if(array_search($key, array_keys($parent)) === 0) $stmt->bindValue((':' . $key . '_' . ($index + 1)), $child, PDO::PARAM_STR);
                }
            }
        }
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($res === false || sizeof($res) === 0){
            $res = null;
        }
        $stmt->closeCursor();
        return $res;
    } catch (InvalidArgumentException $e) {
        echo  $e->getMessage();
        return false;
    } catch (PDOException $e) {
        $message = "No se pudo consultar, razón del error SQLSTATE-" . $e->getCode() . '.';
        echo $message;
        $pdo->rollBack();
        return false;
    }catch(Exception $e) {
        echo "Ha ocurrido un error desconocido: " . $e->getCode();
        $pdo->rollBack();
        return false;
    }
}
/**
 * EFECTUA LA CONSULTA TODAS LAS CATEGORIAS CONTRA LA BASE DE DATOS ->user_categories
*/
function getCategories(String $tableName = 'categories') {
    return getData(true, $tableName);
}
/**
 * EFECTUA LA CONSULTA DE UNA CATEGORIA CONTRA LA BASE DE DATOS
*/
function getCategory(array $args){
    return check_req_res($args, getData(false, 'categories', 'catName', $args), 'catName');
}
/**
 * EFECTUA LA CONSULTA TODAS LAS CATEGORIAS CON PRODUCTOS CONTRA LA BASE DE DATOS
*/
function getActiveCategories(String $tableName = 'user_categories'){ 
    return getCategories($tableName);
}
/**
 * EFECTUA LA CONSULTA DE UNA CATEGORIA ACTIVA CONTRA LA BASE DE DATOS
*/
function getActiveCategory(array $args){
    return check_req_res($args, getData(false, 'user_categories', 'category', $args), 'category');
}
/**
 * RETRIEVES ALL THE PRODUCTS
*/
function getProducts(String $tableName = 'user_products'){
    return getData(true, $tableName);
}
/**
 * RETRIEVES ALL THE PRODUCTS BY HIS IDENTIFIER
*/
function getProduct(array $args){
    return check_req_res($args, getData(false, 'user_products', 'product', $args), 'product');
}
/**
 * EFECTUA LA CONSULTA TODAS LAS CATEGORIAS CONTRA LA BASE DE DATOS ->user_categories
*/
function getUsers(String $tableName = 'user') {
    return getData(true, $tableName);
}
/**
 * RETRIEVES ALL THE USER INFORMATION BY HIS IDENTIFIER
*/
function getUser(array $args){
    return check_req_res($args, getData(false, 'user', 'userId', $args), 'userId');
}
/**
 * RETRIEVES ALL THE USER CREDENTIALS BY HIS IDENTIFIER
*/
function getUserCredentials(array $args){
    $response = check_req_res($args, getData(false, 'user', 'userId', $args), 'userId') ?? null;
    if(!is_null($response)){
        $response['data'] = array_map(function($v) {
            return array_slice_keys( $v, array('userId', 'userPass', 'userMail') );
        }, $response['data']);
    }
    return $response;
}
/**
 * RETRIEVES USER ORDER HISTORY BY USER IDENTIFIERS AND DATE FILTERED
*/
function getUserOrdersFiltered(string $date = '', array $args){
    $response = check_req_res($args, getData(false, 'user_orders', 'user', $args), 'user');
    if(strlen(trim($date)) > 0) {
        $response['data'] = array_values(array_filter(array_map(function($v) use($date) {
            if($v['date'] == date('Y-m-d', strtotime($date))) return $v;
        }, $response['data'])));
    }
    return $response;
}
/**
 * RETRIEVES USER ORDER HISTORY BY HIS IDENTIFIER
*/
function getUserOrders(string $id, string $date = ''){
    return getUserOrdersFiltered($date, array('id' => $id));
}
/**
 * RETRIEVES USER ORDER HISTORY BY HIS IDENTIFIER AND DATE
*/
function getUserOrdersByDate(string $id, $date = 'now'){
    return getUserOrders($id, $date);
}
?>