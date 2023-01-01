//  EVENTO NADA MAS CARGAR EL DOCUMENTO
window.addEventListener("load", () => {
    //  SI NO SE DETECTA PROTOCOLO HTTPS
    if (location.protocol !== 'https:') {
        //  SE REDIRIGE
        location.replace(`https:${location.href.substring(location.protocol.length)}`);
    }
    window.mainProps = Object.assign({ 'regexUser': new RegExp("<?= USERREGEX ?>")}, window.mainProps);
    window.mainProps = Object.assign({ 'regexMail': new RegExp("<?= MAILREGEX ?>")}, window.mainProps);
    window.mainProps = Object.assign({ 'regexPass': new RegExp("<?= PASSREGEX ?>")}, window.mainProps);
    window.mainProps = Object.assign({ 'regexString': new RegExp("<?= NAMEREGEX ?>")}, window.mainProps);
});
//  
const projectName = () => {
    //  SE OBTIENE PATH
    const pathname = decodeURI(window.location.pathname);
    //  SE ESTABLECE NOMBRE DEL EJERCICIO EN BASE AL PATH
    let exerciseFolder = pathname.substring(0, pathname.lastIndexOf('/')).split('/').map((val, index) => {
        if(index === (pathname.substring(0, pathname.lastIndexOf('/')).split('/').length - 1)){
            return val.substring(0, val.search(/\d/)) + " " + val.substring(val.search(/\d/))
        }
    });
    //  SE DEVUELVE
    return exerciseFolder.pop();
}
//  FUNCION QUE VALIDA UN INPUT FILE
const validateFile = (event) => {
    //  SE CREAN VARIABLES
    const file = event.target, allowedTypes = ['image/jpeg', 'image/png'];
    //  SI NO HAY ARCHIVOS
    if(file.files.length === 0) {
        //  SE MUESTRA ERROR 
        alert("Debe seleccionar un archivo");
        //  SE TERMINA EJECUCION
        return;
    }
    //  SE CREAN VARIABLES
    const firstFile = file.files[0], resType = allowedTypes.find(expected => expected == firstFile.type);
    //  SI EL ARCHIVO INTRODUCIDO NO COINCIDE CON LOS PERMITIDOS
    if(!resType || resType === undefined){
        //  SE MUESTRA ERROR 
        alert("Tipo de archivo no vÃ¡lido");
        //  SE LIBERA INPUT
        file.value = '';
        //  SE TERMINA EJECUCION
        return;
    }
    //  SI SE SOBREPASA EL LIMITE DEL TAMAÃ‘O DEL ARCHIVO
    if(firstFile.size > (1024 * 256)){
        //  SE MUESTRA ERROR 
        alert("El tamaÃ±o supera los 256KB, escoge una imagen de menor tamaÃ±o");
        //  SE LIBERA INPUT
        file.value = '';
        //  SE TERMINA EJECUCION
        return;
    }
}
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
const jsonChecker = (str) => {
    try {
      var json = JSON.parse(str);
      return (typeof json === 'object');
    } catch (e) {
      return false;
    }
}
const removeObjectProperties = function(obj, props) {
    for(var i = 0; i < props.length; i++) {
        if(obj.hasOwnProperty(props[i])) {
            delete obj[props[i]];
        }
    }

};
const resetForms = () => {
    //  POR CADA FORMULARIO SE RESETEA
    Array.from(document.forms).forEach(form => {
        form.reset();
    });
};
const validateForm = e => {
    //
    e.preventDefault();
    //
    for (const element of e.srcElement.querySelectorAll('input[type=text], input[type=email], input[type=password]')) {
        //
        if(window.mainProps.skipCheck) continue;
        //
        if(element.value.trim().length <= 0) {
            //
            alert(`El campo ${element.name} no puede estar vací­o`);
            //
            element.focus();
            //
            return;
        }
        //
        if(element.getAttribute('pattern')){
            //
            const regexVal = new RegExp(element.getAttribute('pattern'));
            //
            if(!regexVal.test(element.value.trim())){
                //
                alert(`El ${element.name} no cumple con los requisitos de validación`);
                //
                element.focus();
                //
                return;
            }
        }
        else if(!element.getAttribute('pattern') && element.type === 'text'){
            //
            if(!window.mainProps.regexUser.test(element.value.trim())) {
                //
                alert(`Nombre de usuario no cumple con los requisitos de validación`);
                //
                element.focus();
                //
                return;
            }
            //
            if(!window.mainProps.regexString.test(element.value.trim())) {
                //
                alert(`El ${element.name} no cumple con los requisitos de validación`);
                //
                element.focus();
                //
                return;
            }
        }
        else if(!element.getAttribute('pattern') && element.type === 'password'){
            //
            if(!window.mainProps.regexPass.test(element.value.trim())) {
                //
                alert(`La contraseña no cumple con los requisitos de validación`);
                //
                element.focus();
                //
                return;
            }
        }else {
            //
            if(!window.mainProps.regexMail.test(element.value.trim())) {
                //
                alert(`La dirección de correo electrónico no cumple con los requisitos de validación`);
                //
                element.focus();
                //
                return;
            }
        }
    }
    return true;
}