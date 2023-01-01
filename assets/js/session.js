//  SE REEALIZAN IMPORTACIONES
import { RemoveSessionStorage } from './app.js'
//  FUNCION QUE VALIDA USUARIO
function SignIn(form, callback) {
    let xhr = new XMLHttpRequest();
    let formData = new FormData(form);
    xhr.responseType = 'json';
    formData.append("auth", "signin");
    xhr.open("POST", "./assets/php/controller/procesar.php");
    xhr.send(formData);
    xhr.onload = function() {
        if (xhr.status == 200) {
            if(callback) callback(xhr.response);
        }else {
            callback(xhr?.response ?? {errors: "El servidor no pudo responder a tu petición"});
        }
    };
}
//  FUNCION QUE REGISTRA USUARIO
function SignUp(form, callback) {
    let xhr = new XMLHttpRequest();
    let formData = new FormData(form);
    xhr.responseType = 'json';
    formData.append("auth", "signup");
    xhr.open("POST", "./assets/php/controller/procesar.php");
    xhr.send(formData);
    xhr.onload = function() {
        if (xhr.status == 200) {
            if(sessionStorage.getItem('user'))
            if(callback) callback(xhr.response);
        }else {
            callback(xhr?.response ?? {errors: "El servidor no pudo responder a tu petición"});
        }
    };
}
//  FUNCION ENCARGADA DE CERRAR SESION
function LogOut() {
    RemoveSessionStorage();
    window.location.href = './logout';
}
//  SE REALIZAN EXPORTS
export { LogOut, SignIn, SignUp };