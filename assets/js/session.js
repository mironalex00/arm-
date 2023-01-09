//  SE REEALIZAN IMPORTACIONES
import { RemoveSessionStorage } from './app.js'
//  FUNCION QUE VALIDA USUARIO
async function SignIn(form) {
    let formData = new FormData(form);
    formData.append("auth", "signin");
    const response = await fetch("./assets/php/controller/procesar.php", {
        method: 'POST',
        body: formData,
    });
    if(response.redirected) return true;
    return await response?.json?.() ?? {errors: "El servidor no pudo responder a tu petición"};
}
//  FUNCION QUE REGISTRA USUARIO
async function SignUp(form) {
    let formData = new FormData(form);
    formData.append("auth", "signup");
    const response = await fetch("./assets/php/controller/procesar.php", {
        method: 'POST',
        body: formData,
    });
    return await response?.json?.() ?? {errors: "El servidor no pudo responder a tu petición"};
}
//  FUNCION ENCARGADA DE CERRAR SESION
function LogOut() {
    RemoveSessionStorage();
    window.location.href = './logout';
}
//  SE REALIZAN EXPORTS
export { LogOut, SignIn, SignUp };