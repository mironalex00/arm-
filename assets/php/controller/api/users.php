<?php
#   THE API ALWAYS RETRIEVES JSON RESPONSE AND HTTP CODE
/**
 *  RETRIEVES ALL THE USER CREDENTIALS
*/
function getAPIUserCredentials(array $data) {
    if(sizeof(array_filter($data)) === 0) {
        http_response_code(400);
        return json_encode(array('errors' => 'No data provided'));
    }
    $apiResponse = getUserCredentials($data);
    if(isset($apiResponse['errors']) &&  sizeof($apiResponse['errors']) > 0) {
        http_response_code(500);
        return json_encode(array_slice($apiResponse, 0, 1), JSON_UNESCAPED_UNICODE );
    }else if(is_null($apiResponse)) {
        http_response_code(401);
        return json_encode(array('errors' => array('Credenciales de usuario/s incorrectas')), JSON_UNESCAPED_UNICODE );
    }
    return $apiResponse['data'];
}
/**
 *  ADD PRODUCTS GIVEN THROUGH ARRAY DATA
*/
function addAPIUser(array|string|null $user, array|string|null $pass, array|string|null $fname, array|string|null $sname, array|string|null $mail){
    if(is_null($user) || is_null($pass) || is_null($fname) || is_null($sname) || is_null($mail)) {
        http_response_code(400);
        return json_encode(array('errors' => 'No data provided'));
    }
    if(!is_array($user)) $user = array($user);
    if(!is_array($pass)) $pass = array($pass);
    if(!is_array($fname)) $fname = array($fname);
    if(!is_array($sname)) $sname = array($sname);
    if(!is_array($mail)) $mail = array($mail);
    $data = array_map(function($v, $i) use($pass, $fname, $sname, $mail){
        if(
            isset($v) && !is_null($v) &&
            isset($pass[$i]) && !is_null($pass[$i]) && 
            isset($fname[$i]) && !is_null($fname[$i]) &&
            isset($sname[$i]) && !is_null($sname[$i]) &&
            isset($mail[$i]) && !is_null($mail[$i])
        ){
            return array(
                'id' => $v, 'pass' => md5($pass[$i]), 
                'fullname' => $fname[$i], 'surname' => $sname[$i], 
                'mail' => $mail[$i]
            );
        }
    }, array_values($user), array_keys($user));
    $apiResponse = addUser($data);
    if(isset($apiResponse['errors']) &&  sizeof($apiResponse['errors']) > 0) {
        http_response_code(500);
        return json_encode(array_slice($apiResponse, 0, 1), JSON_UNESCAPED_UNICODE );
    }else if(is_null($apiResponse)) {
        http_response_code(403);
        return json_encode(array('errors' => array('Los usuarios introducidos ya existen')), JSON_UNESCAPED_UNICODE );
    }else if(is_string($apiResponse)) {
        http_response_code(500);
        return json_encode(array('errors' => array($apiResponse)), JSON_UNESCAPED_UNICODE );
    }else if(is_bool($apiResponse) && $apiResponse) {
        http_response_code(201);
        return json_encode(array(
            'message' => array('Se han añadido con éxito los usuarios'),
            'action' => array('redirect' => get_path() . '/auth')
        ), JSON_UNESCAPED_UNICODE );
    }else {
        http_response_code(500);
        return json_encode(array('errors' => array('No se pudieron añadir uno o varios usuarios')), JSON_UNESCAPED_UNICODE );
    }
}
?>