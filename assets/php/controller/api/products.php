<?php
#   THE API ALWAYS RETRIEVES JSON RESPONSE AND HTTP CODE
/**
 *  RETRIEVES ALL THE PRODUCTS
*/
function getAPIProducts(){
    $response = getProducts();
    if(is_null($response)) {
        http_response_code(404);
        return json_encode(array('errors' => 'No existen productos para mostrar'), JSON_UNESCAPED_UNICODE );
    }
    usort($response, fn($a, $b) => $a['product'] <=> $b['product']);
    return json_encode($response, JSON_UNESCAPED_UNICODE );
}
/**
 *  RETRIEVES ALL THE PRODUCTS IN THE FILTER GIVEN
*/
function getAPIProduct(array|string|null $args){
    if(!is_array($args) || is_null($args)) $args = array($args);
    $args = array_map(fn($v) => array('id' => $v), array_filter($args));
    if(count($args) === 0) {
        http_response_code(400);
        return json_encode(array('errors' => 'No data provided'));
    }
    $apiResponse = getProduct($args);
    if(isset($apiResponse['errors']) &&  sizeof($apiResponse['errors']) > 0) {
        http_response_code(500);
        return json_encode(array_slice($apiResponse, 0, 1), JSON_UNESCAPED_UNICODE );
    }else if(is_null($apiResponse)) {
        http_response_code(404);
        return json_encode(array('errors' => 'No existen uno o varios productos seleccionados'), JSON_UNESCAPED_UNICODE );
    }
    $apiResponse = $apiResponse['data'];
    usort($apiResponse, fn($a, $b) => $a['product'] <=> $b['product']);
    return json_encode($apiResponse, JSON_UNESCAPED_UNICODE );
}
/**
 *  ADD PRODUCTS GIVEN THROUGH ARRAY DATA
*/
function addAPIProduct(array|string|null $prod, array|string|null $desc, array|string|null $price, array|string|null $stock, array|string|null $cat){
    if(is_null($prod) || is_null($desc) || is_null($price) || is_null($stock) || is_null($cat)) {
        http_response_code(400);
        return json_encode(array('errors' => 'No data provided'));
    }
    if(!is_array($prod)) $prod = array($prod);
    if(!is_array($desc)) $desc = array($desc);
    if(!is_array($price)) $price = array($price);
    if(!is_array($stock)) $stock = array($stock);
    if(!is_array($cat)) $cat = array($cat);
    $data = array_map(function($v, $i) use($desc, $price, $stock, $cat){
        if(
            isset($v) && !is_null($v) &&
            isset($desc[$i]) && !is_null($desc[$i]) && 
            isset($price[$i]) && !is_null($price[$i]) &&
            isset($stock[$i]) && !is_null($stock[$i]) &&
            isset($cat[$i]) && !is_null($cat[$i])
        ){
            return array(
                'id' => $v, 'description' => $desc[$i], 
                'price' => $price[$i], 'stock' => $stock[$i], 
                'category' => $cat[$i]
            );
        }
    }, array_values($prod), array_keys($prod));
    $apiResponse = addProduct($data);
    if(isset($apiResponse['errors']) &&  sizeof($apiResponse['errors']) > 0) {
        http_response_code(500);
        return json_encode(array_slice($apiResponse, 0, 1), JSON_UNESCAPED_UNICODE );
    }else if(is_null($apiResponse)) {
        http_response_code(403);
        return json_encode(array('errors' => 'Los productos introducidos ya existen'), JSON_UNESCAPED_UNICODE );
    }else if(is_string($apiResponse)) {
        http_response_code(500);
        return json_encode(array('errors' => array($apiResponse)), JSON_UNESCAPED_UNICODE );
    }else if(is_bool($apiResponse) && $apiResponse) {
        return json_encode(array('message' => 'Se han añadido con éxito los productos'), JSON_UNESCAPED_UNICODE );
    }else {
        http_response_code(500);
        return json_encode(array('errors' => 'No se pudieron añadir uno o varios productos'), JSON_UNESCAPED_UNICODE );
    }
}
?>