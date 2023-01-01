<?php
//  
require_once('controller/db_credentials.php');
//  
require_once('controller/db.php');
//  CONSTANTES
DEFINE('USERREGEX', '^[a-z0-9]([._](?![._])|[a-z0-9]){2,12}[a-z0-9]$');
DEFINE('PASSREGEX', '^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,}$');
DEFINE('MAILREGEX', '(?![_.-])((?![_.-][_.-])[a-z\d_.-]){0,63}[a-z\d]@((?!-)((?!--)[a-z\d-]){0,63}[a-z\d]\.){1,2}([a-z]{2,14}\.)?[a-z]{2,14}$');
DEFINE('NAMEREGEX', '^[a-zA-Z0-9\s]([._](?![._])|[a-zA-Z0-9\s]){2,}[a-zA-Z-1-9\s]$');
DEFINE('CURRENTDATE', date('Y-m-d'));
DEFINE('MINORDATE', date('Y-m-d', strtotime('last day of December this year -18 years')));
DEFINE('ADULTDATE', date('Y-m-d', strtotime('first day of January this year -50 years')));
DEFINE('MAXFILESIZE', 256);
DEFINE('MAXFILEWIDTH', 400);
DEFINE('MAXFILEHEIGHT', 400);
/**
 * FUNCION QUE DEVUELVE EL ÚLTIMO ARRAY DE UN ARRAY MULTIDIMENSIONAL
*/
function get_last_dynamic_array(mixed $array) {
    if (is_array(end($array))) {
        $array = get_last_dynamic_array(end($array));
    }
    return $array;
}
/**
 * FUNCION QUE DEVUELVE EL ÚLTIMO ELEMENTO DE UN ARRAY MULTIDIMENSIONAL
*/
function get_last_dynamic_array_val(mixed $array) {
    if (is_array($array)) {
        $array = array_slice($array, -1, 1);
        foreach ($array as $value) {
            $array = get_last_dynamic_array_val($value);
        }
    }
    return $array;
}
/**
 * FUNCION QUE RECORTA UN ARRAY POR SUS CLAVES
*/
function array_slice_keys($array, $keys = null) {
    if ( empty($keys) ) {
        $keys = array_keys($array);
    }
    if ( !is_array($keys) ) {
        $keys = array($keys);
    }
    if ( !is_array($array) ) {
        return array();
    } else {
        return array_intersect_key($array, array_fill_keys($keys, '1'));
    }
}
/**
 * FUNCION QUE MODIFICA EL ÚLTIMO ELEMENTO DE UN ARRAY MULTIDIMENSIONAL
*/
function setLastDynamicArray(mixed &$array, mixed $find , mixed $replace) {
    //  SI NO ES ARRAY SE RETORNA EL REEMPLAZO
    if(!is_array($array)) return $replace;
    //  SI NO, SE REALIZA BUSQUEDA RECURSIVA PASANDO COMO PUNTERO EL ARRAY Y PARAMS OPCIONALES
    array_walk_recursive($array, function(&$array) use($find, $replace) {
        //  SI HAY COINCIDENCIA
        if($array == $find) {
            //  REEMMPLAZA
            $array = $replace;
        }
    });
    //  SE RETORNA
    return $array;
}
/**
 * FUNCION ENCARGADA DE LIMPIAR EL PARÁMETRO RECIBIDO
*/
function sanitize_string(mixed $param, bool $special = false) {
    //  
    mb_internal_encoding("UTF-8");
    mb_regex_encoding("UTF-8");
    //  SI ES NUMERICO DIRECTAMENTE SE RETORNA
    if(is_numeric($param)) return $param;
    //  SI CONTIENE CARACTERES ESPECIALES
    if($special){
        //  SE RETORNAN TODOS LOS VALORES CON CARACTERES ESPECIALES ADMITIDOS Y ALFANUMERICOS 
        return trim(mb_ereg_replace("[\"']", '', mb_ereg_replace('/[^a-zA-Z0-9-_.@\s]/', '', urldecode(html_entity_decode(strip_tags($param))))));
    };
    //  SI NO, SE RETORNA TODOS LOS VALORES NO NUMERICOS Y NO SPECIAL_CHARS
    return trim(mb_ereg_replace('/"\'/i', '', mb_ereg_replace('/[^A-Za-z0-9\s]/', '', urldecode(html_entity_decode(strip_tags($param))))));
}
/**
 * 
*/
function multiple_regex_checker(string $regex, ...$params){
    //  POR CADA PARAM
    foreach ($params as $key => $value) {
        //  SI EL RESULTADO NO ES POSITIVO
        if(!regex_checker($value, $regex)){
            //  SE DEVUELVE ERROR EN CASO DE NO CUMPLIR LOS REQUISITOS
            return 'El valor de ' . $value . ' no es válido o compatible con la validación.';
        }
    }
    //  
    return true;
}
/**
 * DEVUELVE LA COMPROBACIÓN POR REGEX DEL VALOR RECIBIDO
*/
function regex_checker(mixed $value, string $regex) {
    //  SI EL RESULTADO NO ES POSITIVO
    if(!preg_match($regex, $value)){
        //  SE DEVUELVE ERROR EN CASO DE NO CUMPLIR LOS REQUISITOS
        return false;
    }
    //  
    return true;
}
/**
 * FUNCION QUE COMPRUEBA FECHA RECIBIDA CONTRA ACTUAL Y RANGOS
*/
function check_adult(mixed $date){
    //  SI EL VALOR DE LA FECHA ES ACTUAL
    if($date === CURRENTDATE){
        //  SE ESTABLECE CODIGO DE RESPUESTA
        http_response_code(400);
        //  SE RETORNA
        exit('La fecha no puede ser la actual');
    }
    //  SI LA FECHA SOBREPASA MAXIMO
    if($date > MINORDATE){
        //  SE ESTABLECE CODIGO DE RESPUESTA
        http_response_code(400);
        //  SE RETORNA
        exit('Se debe tener mayoría de edad para el registro');
    }
    //  SI LA FECHA NO LLEGA AL MINIMO
    if($date < ADULTDATE){
        //  SE ESTABLECE CODIGO DE RESPUESTA
        http_response_code(400);
        //  SE RETORNA
        exit('La fecha introducida no es válida o no cumple con los criterios');
    }
}
/**
 * FUNCION QUE PROCESA UN ARCHIVO
*/
function handle_file(array $file){
    //  SE DFEFINE UNA CONSTANTE DE ARRAY CON LOS TIPOS DE IMAGEN VALIDOS
    DEFINE('ALLOWED_TYPES', array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'));
    //
    if ( $file['error'] !== 0 ) {
        //  SE ESTABLECE CODIGO DE RESPUESTA
        http_response_code(400);
        //  SWITCH CASE POR LOS ERRORES
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "El archivo subido excede el valor máximo que el servidor puede procesar";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "El archivo subido excede el valor máximo que el servidor puede procesar";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "Error en la subida del archivo";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No se ha recibido ningún archivo";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "No se puede procesar el archivo";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "No se puede procesar el archivo";
                break;
            default:
                $message = "Error en la subida, razón desconocida";
                break;
        }
        //  SE RETORNA
        exit($message);
    }
    // SE COMPRUEBA QUE EL TAMAÑO DEL ARCHIVO NO SOBREPASE EL LIMITE
    if ($file['size'] > (1024 * MAXFILESIZE)) {
        //  SE ESTABLECE CODIGO DE RESPUESTA
        http_response_code(400);
        //  SE RETORNA
        exit('El tamaño del archivo no debe sobrepasar los 256KB');
    }
    //  SE CREA INSTANCIA DE OBJETO FINFO PARA OBTENER INFORMACION DEL ARCHIVO
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    //  SE COMPRUEBA EL TIPO DE ARCHIVO, SI NO SE ENCUENTRA DENTRO DE LOS PERMITIDOS
    if ( false === array_search( $finfo->file($file['tmp_name']), ALLOWED_TYPES, true) ) {
        //  SE ESTABLECE CODIGO DE RESPUESTA
        http_response_code(400);
        //  SE RETORNA
        exit('El formato del archivo no es el esperado');
    }
    //  SE GUARDA ALTURA Y ANCHURA DE LA IMAGEN
    list($width, $height) = getimagesize($file["tmp_name"]);
    //  SI EL TAMAÑO NO COINCIDE
    if($width > MAXFILEWIDTH || $height > MAXFILEHEIGHT){
        //  SE ESTABLECE CODIGO DE RESPUESTA
        http_response_code(400);
        //  SE RETORNA
        exit('El tamaño no debe superar los 400x400 píxeles');
    }
}
/**
 * FUNCION ENCARGADA DE COMPROBAR PARAMETROS 
*/
function check_params_req($REQ, ...$params){
    if(!isset($REQ) || sizeof($REQ) === 0) return 'No se ha recibido nada' . trim(array_search($REQ, $GLOBALS));
    foreach ($params as $param) {
        if(!isset($REQ[$param]) || empty($REQ[$param]) || strlen($REQ[$param]) <= 0){
            return 'Se requiere de ' . $param . ' o no es válido su valor';
        }
    }
    return true;
}
/**
 * 
*/
function check_req_res(array $req, mixed $res, string $filterBy) {
    if(!is_array($res)) return $res;
    $req = array_values(array_map("unserialize", array_unique(array_map("serialize", $req))));
    $response = array('errors' => array(), 'data' => $res);
    foreach ($req as $key => $val) {
        if(array_search($val['id'], array_column($res, $filterBy)) === false) {
            if (stripos(json_encode($response['errors']), $val['id']) === false) {
                array_push($response['errors'], 'La entrada "' . $val['id'] . '" no existe');
            }
        }
    }
    return $response;
}
/**
 * FUNCION ENCARGADA DE COMPROBAR EXISTENCIA DENTRO DE ARRAY
*/
function array_key_assoc_check(array $array, ...$filterBy){
    if(sizeof($filterBy) === 0) return $array;
    $array = array_values(array_map("unserialize", array_unique(array_map("serialize", $array))));
    $response = array('errors' => array(), 'data' => array());
    foreach ( new RecursiveIteratorIterator(new RecursiveArrayIterator($array), RecursiveIteratorIterator::SELF_FIRST) as $key => $val ){
        if(is_array($val)){
            $val = array_filter($val, 'is_string', ARRAY_FILTER_USE_KEY);
            if(count(array_intersect_key(array_flip($filterBy), $val)) !== count($filterBy)){
                array_push(
                    $response['errors'], 
                    'Entrada/s "' . implode(', ', $filterBy) . '" con identificador "' . array_values($val)[0] . '" son obligatorias'
                );
            }else {
                array_push($response['data'], $val);
            }
        }
    }
    return $response;
}
/**
 *  SE RECOGE PATH, FORMADA POR EL HOST, RUTA SUBDIRECTORIOS HASTA ARCHIVO 
*/
function get_path(string $pattern = "(\b(?<=\/)\w+(_)+\w+\b(?=\/))"){
    $path = 'https://' . $_SERVER['HTTP_HOST'] . (substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
    if(!isset($_SERVER['HTTP_REFERER']) || isset($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER'] = $path;
    preg_match("/$pattern/", $_SERVER['HTTP_REFERER'], $matches, PREG_OFFSET_CAPTURE);
    $wordFounded = reset($matches)[0] ?? reset($matches);
    $indexFounded = strrpos($_SERVER['HTTP_REFERER'], $wordFounded);
    return substr($_SERVER['HTTP_REFERER'], 0, (strlen($wordFounded) + $indexFounded));
}
?>