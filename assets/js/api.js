//  SE IMPORTAN DE APP
import { RemoveSessionStorage, JsonChecker } from './app.js';

//  FUNCION ENCARGADA DE AÑADIR UNA ORDEN
function CreateOrder(form, callback) {
    let xhr = new XMLHttpRequest(), formData = new FormData(form);
    xhr.responseType = 'json';
    formData.append("orders", "addorder");
    xhr.open("POST", "./assets/php/controller/procesar.php");
    xhr.send(formData);
    xhr.onload = function() {
        if(callback) {
            callback(xhr.response ?? {errors: "El servidor no pudo responder a tu petición"});
        }else {
            throw new Error("No callback provided")
        }
    };
}
//  FUNCION ENCARGADA DE ELIMINAR CARRITO
function DeleteCart() { RemoveSessionStorage('cart') }
//  FUNCION QUE OBTIENE CARRITO
function GetCart() {
    const result = sessionStorage.getItem("cart");
    if(!JsonChecker(result)) return null;
    return JSON.parse(result)?.sort(function(a, b){
        let x = a.prod.toLowerCase();
        let y = b.prod.toLowerCase();
        return x > y ? 1 : x < y ? - 1 : 0;
    }) ?? null;
}
//  FUNCION QUE OBTIENE PRODUCTOS
function GetProducts(callback) {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', './assets/php/controller/procesar.php?products');
    xhr.responseType = 'json';
    xhr.send();
    xhr.onload = function() {
        if(callback && xhr.response) {
            callback(xhr.response);
        }else {
            callback({errors: "El servidor no pudo responder a tu petición"});
        }
    };
}
//  FUNCION QUE OBTIENE CATEGORIAS
function GetCategories(callback) {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', './assets/php/controller/procesar.php?categories');
    xhr.responseType = 'json';
    xhr.send();
    xhr.onload = function() {
        if(callback && xhr.response) {
            callback(xhr.response);
        }else {
            callback({errors: "El servidor no pudo responder a tu petición"});
        }
    };
}
//  ACTUALIZA EL CARRITO
function UpdateCart(order, add = true) {
    let cart = GetCart();
    if(cart === null) {
        sessionStorage.setItem("cart", JSON.stringify([order]));
    }else{
        const index = cart.findIndex(x => x.prod == order.prod);
        if(index === -1) {
            cart.push(order);
        }else {
            if(add) {
                cart[index].quantity += order.quantity;
            }else {
                cart[index].quantity -= order.quantity;
                if(cart[index].quantity <= 0) {
                    cart.splice(index, 1);
                }
            }
        }
        sessionStorage.setItem("cart", JSON.stringify(cart));
    }
    const cartStorageEvent = new StorageEvent('storage', { key: 'cart' });
    window.dispatchEvent(cartStorageEvent);
}
//  EXPORTACION DE MODULOS
export { CreateOrder, DeleteCart, GetCart, UpdateCart, GetProducts, GetCategories };