<?php
    //  RERQUIRED FILES
    require_once('./session.php');
    require_once('./api/categories.php');
    require_once('./api/products.php');
    require_once('./api/orders.php');
    require_once('./api/users.php');

    // REQUIRED HEADERS
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
    //  IF API DID NOT RECIEVED DATA REDIRECT
    if(sizeof($_GET) === 0 && sizeof($_POST) === 0){
        header('Location: '. get_path(), true, 308);
        return;
    }
    $expected = array('auth', 'categories', 'products', 'orders');
    $key = array_key_first(array_filter($_REQUEST, fn($v) => !is_bool(array_search($v, $expected)), ARRAY_FILTER_USE_KEY));
    $action = $_REQUEST[$key] ?? null;
    //  IF API HAS NO TOKEN (RIGHT NOW WORKING WITH COOKIES AND SESSION)
    if(!isset($_SESSION['user']) && !isset($_POST['auth'])){
        http_response_code(403);
        echo json_encode(array('errors' => 'Por favor inicia sesión'), JSON_UNESCAPED_UNICODE );
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        if(!isset($_POST[$key])){
            http_response_code(400);
            die(json_encode(array('errors' => 'No data provided or invalid parameters'), JSON_UNESCAPED_UNICODE ));
        }
        switch ($_POST) {
            case $key === 'categories' && $action === 'addcat':
                echo addAPICategory($_POST['category'] ?? null, $_POST['desc'] ?? null);
            return;
            case $key === 'orders' && $action === 'addorder':
                //  AÑADIR API ORDEN


                var_dump($_POST, $_SESSION['user']);
            return;
            case $key === 'products' && $action === 'addprod':
                echo addAPIProduct($_POST['prod'] ?? null, $_POST['desc'] ?? null, $_POST['price'] ?? null, $_POST['stock'] ?? null, $_POST['cat'] ?? null);
            return;
            case $key === 'auth' && $action === 'signin':
                $user = $_POST['user'] ?? null;
                $pass = $_POST['pass'] ?? null;
                if(!is_array($user)) $user = array($user);
                if(!is_array($pass)) $pass = array($pass);
                $data = array_map(function($v, $i) use($pass){
                    if(isset($v) && !is_null($v) && isset($pass[$i]) && !is_null($pass[$i])){
                        return array('id' => $v, 'pass' => md5($pass[$i]));
                    }
                }, array_values($user), array_keys($user));
                $apiResponse = getAPIUserCredentials($data);
                if(!is_array($apiResponse)){
                    http_response_code(403);
                    echo $apiResponse;
                    return;
                }
                $userInp = (object) reset($data);
                $userOut = (object) reset($apiResponse);
                if($userInp->pass !== $userOut->userPass) {
                    http_response_code(403);
                    echo json_encode(array('errors' => array('Credenciales de usuario/s incorrectas')), JSON_UNESCAPED_UNICODE );
                    return;
                }else{
                    $userOut = (object) reset(getUser($data)['data']);
                    unset($userOut->userPass);
                    $_SESSION['user'] = (object) array('online' => true, 'logindate' => CURRENTDATE, 'data' => $userOut);
                    echo json_encode(array( 'action' => array('redirect' => get_path() . '/')), JSON_UNESCAPED_UNICODE );
                }
            return;
            case $key === 'auth' && $action === 'signup':
                echo addAPIUser($_POST['user'] ?? null, $_POST['pass'] ?? null, $_POST['fname'] ?? null, $_POST['sname'] ?? null, $_POST['mail'] ?? null);
            return;
        }
    }else {
        if(!isset($_GET[$key])){
            http_response_code(400);
            die(json_encode(array('errors' => 'No data provided or invalid parameters'), JSON_UNESCAPED_UNICODE ));
        }
        switch ($_GET) {
            case $key === 'categories' && $action === '':
                echo getAPIActiveCategories();
            return;
            case $key === 'categories' && $action === 'fcat':
                echo getAPICategory($_GET['category'] ?? null);
            return;
            case $key === 'categories' && $action === 'facat':
                echo getAPIActiveCategory($_GET['category'] ?? null);
            return;
            case $key === 'categories' && $action === 'gncat':
                echo getAPICategories();
            return;
            case $key === 'products' && $action === '':
                echo getAPIProducts();
            return;
            case $key === 'products' && $action === 'fprods':
                echo getAPIProduct($_GET['product'] ?? null);
            return;
            case $key === 'orders' && $action === '':
    
            return;
            case $key === 'orders' && $action === 'gforders':
    
            return;
        }
    }
?>