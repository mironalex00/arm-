<?php
#   THE API ALWAYS RETRIEVES JSON RESPONSE AND HTTP
/**
 *  RETRIEVES ALL THE CATEGORIES 
*/
function getAPICategories(){
    $response = array_map(function ($val) {
        return array(
            'name' => $val['catName'],
            'desc' => $val['catDesc']
        );
    }, getCategories() ?? array());
    usort($response, fn($a, $b) => $a['name'] <=> $b['name']);
    return json_encode( sizeof($response) === 0 ? array('errors' => 'No existen categorías') : $response, JSON_UNESCAPED_UNICODE );
}
/**
 *  RETRIEVES ALL THE CATEGORIES IN THE FILTER GIVEN 
*/
function getAPICategory(array|string|null $args){
    if(!is_array($args) || is_null($args)) $args = array($args);
    $args = array_map(fn($v) => array('id' => $v), array_filter($args));
    if(count($args) === 0) {
        http_response_code(400);
        return json_encode(array('errors' => 'No data provided'));
    }
    $apiResponse = getCategory($args);
    if(isset($apiResponse['errors']) &&  sizeof($apiResponse['errors']) > 0) {
        http_response_code(500);
        return json_encode(array_slice($apiResponse, 0, 1), JSON_UNESCAPED_UNICODE );
    }else if(is_null($apiResponse)) {
        http_response_code(404);
        return json_encode(array('errors' => 'Las categorías introducidas no existen'), JSON_UNESCAPED_UNICODE );
    }
    $apiResponse = array_map(function ($val) {
        return array(
            'name' => $val['catName'],
            'desc' => $val['catDesc']
        );
    }, $apiResponse['data'] ?? array());
    usort($apiResponse, fn($a, $b) => $a['name'] <=> $b['name']);
    return json_encode( $apiResponse, JSON_UNESCAPED_UNICODE );
}
/**
 *  RETRIEVES ALL THE ACTIVE CATEGORIES 
*/
function getAPIActiveCategories(){
    $response = array_map(function ($val) {
        return array(
            'name' => $val['category'],
            'desc' => $val['desc']
        );
    }, getActiveCategories() ?? array());
    usort($response, fn($a, $b) => $a['name'] <=> $b['name']);
    return json_encode( sizeof($response) === 0 ? array('errors' => 'No existen categorías') : $response, JSON_UNESCAPED_UNICODE );
}
/**
 *  RETRIEVES ALL THE ACTIVE CATEGORIES IN THE FILTER GIVEN 
*/
function getAPIActiveCategory(array|string|null $args){
    if(!is_array($args) || is_null($args)) $args = array($args);
    $args = array_map(fn($v) => array('id' => $v), array_filter($args));
    if(count($args) === 0) {
        http_response_code(400);
        return json_encode(array('errors' => 'No data provided'));
    }
    $apiResponse = getActiveCategory($args);
    if(isset($apiResponse['errors']) &&  sizeof($apiResponse['errors']) > 0) {
        http_response_code(500);
        return json_encode(array_slice($apiResponse, 0, 1), JSON_UNESCAPED_UNICODE );
    }else if(is_null($apiResponse)) {
        http_response_code(404);
        return json_encode(array('errors' => array('Las categorías introducidas no existen')), JSON_UNESCAPED_UNICODE );
    }
    $apiResponse = array_map(function ($val) {
        return array(
            'name' => $val['category'],
            'desc' => $val['desc']
        );
    }, $apiResponse['data'] ?? array());
    usort($apiResponse, fn($a, $b) => $a['name'] <=> $b['name']);
    return json_encode( $apiResponse, JSON_UNESCAPED_UNICODE );
}
/**
 *  ADD CATEGORIES GIVEN THROUGH ARRAY DATA
*/
function addAPICategory(array|string|null $categories, array|string|null $descriptions) {
    if(is_null($categories) || is_null($descriptions)) {
        http_response_code(400);
        return json_encode(array('errors' => 'No data provided'));
    }
    if(!is_array($categories)) $categories = array($categories);
    if(!is_array($descriptions)) $descriptions = array($descriptions);
    $data = array_map(function($v, $i) use($descriptions){
        if(isset($v) && !is_null($v) && isset($descriptions[$i]) && !is_null($descriptions[$i])){
            return array('id' => $v, 'desc' => $descriptions[$i]);
        }
    }, array_values($categories), array_keys($categories));
    $apiResponse = addCategory($data);
    if(isset($apiResponse['errors']) &&  sizeof($apiResponse['errors']) > 0) {
        http_response_code(500);
        return json_encode(array_slice($apiResponse, 0, 1), JSON_UNESCAPED_UNICODE );
    }else if(is_null($apiResponse)) {
        http_response_code(401);
        return json_encode(array('errors' => array('Las categorías introducidas ya existen')), JSON_UNESCAPED_UNICODE );
    }
    return json_encode(array('msg' => "Categorías añadidas con éxito") , JSON_UNESCAPED_UNICODE );
}
?>