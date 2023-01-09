//  EVENTO NADA MAS CARGAR EL DOCUMENTO
window.addEventListener("load", () => {
    //  SI NO SE DETECTA PROTOCOLO HTTPS
    if (location.protocol !== 'https:') {
        //  SE REDIRIGE
        location.replace(`https:${location.href.substring(location.protocol.length)}`);
    }
});
const togglePassword = (element) => {
    //  SE GUARDA ELEMENTO PREVIO DE PASSWORD (O SI HA SIDO CAMBIADO DE TEXT)
    const password = element.previousElementSibling.firstElementChild;
    //  SI CONTIENE CLASE OCULTA
    if(element.querySelector('i').classList.contains('fa-eye-slash')){
        //  SE CAMBIA A VISIBLE
        element.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');
        //  SE CAMBIA PASSWORD A TIPO TEXTO
        password.setAttribute('type', 'text');
    }else{
        //  SI ESTA VISIBLE, SE CAMBIA A OCULTA
        element.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');
        //  SE CAMBIA TEXTO A TIPO PASSWORD
        password.setAttribute('type', 'password');
    }
    //  SE ESTABLECE TIMEOUT DE 1MS (POR SINCRONIA)
    setTimeout(() => {
        //  SE LE DA FOTO AL CAMPO DE LA PASSWORD
        password.focus();
    }, 1);
}
const resetForms = () => {
    //  POR CADA FORMULARIO SE RESETEA
    Array.from(document.forms).forEach(form => {
        form.reset();
    });
};