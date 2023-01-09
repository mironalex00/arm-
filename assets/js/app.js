//  FUNCION QUE VALIDA SI UN STRING ES JSON
const JsonChecker = (str) => {
    try {
      var json = JSON.parse(str);
      return (typeof json === 'object');
    } catch (e) {
      return false;
    }
}
//  FUNCION ENCARGADA DE PINTAR UN ELEMENTO DE ALERTA HTML
const PrintAlert = function(hideBackBtn = false, message, parent, type = 0) {
    const alertType = type === 0 ? 'success' : type === 1 ? 'danger' : 'warning';
    parent.innerHTML = `
    <div class="alert alert-${alertType} alert-dismissible col-12 col-md-8 offset-md-2 text-center text-bold prod-show" role="alert">
        <p class="mt-1 fw-bold">${message}</p>
        <div class="mt-5 ${hideBackBtn ? 'd-none' : ''}">
            <a class="alert-link" href="./">
                <span class="text-decoration-underline">Volver atrás</span>
            </a>
        </div>
        <button type="button" 
            class="btn-close" data-bs-dismiss="alert" 
            aria-label="Close" onclick="this.parentElement.remove()">
        </button>
    </div>`;
}
//  FUNCION QUE ELIMINA PROPIEDAD DE UN OBJETO POR SU CLAVE
const RemoveObjectProperty = function(obj, props) {
    for(var i = 0; i < props.length; i++) {
        if(obj.hasOwnProperty(props[i])) {
            delete obj[props[i]];
        }
    }

};
//  FUNCION ENCARGADA DE ELIMINAR UNA O VARIAS ENTRADAS SESSIONSTORAGE
const RemoveSessionStorage = (...entries) => {
    entries = entries.length <= 0 ? sessionStorage : Object.entries(
        Object.values(entries)).reduce(
        (obj, [key, value]) => ({ ...obj, [value]: key }), {}
    );
    Object.keys(entries).forEach(entry => sessionStorage.removeItem(entry));
}
//  
const RetrieveProject = () => {
    const pathname = decodeURI(window.location.pathname);
    let exerciseFolder = pathname.substring(0, pathname.lastIndexOf('/')).split('/').map((val, index) => {
        if(index === (pathname.substring(0, pathname.lastIndexOf('/')).split('/').length - 1)){
            return val.substring(0, val.search(/\d/)) + " " + val.substring(val.search(/\d/))
        }
    });
    return exerciseFolder.pop();
}
//  FUNCION QUE VALIDA UN INPUT FILE
const ValidateInpFile = (...args) => {
    const [file] = args , allowedTypes = ['image/jpeg', 'image/png'];
    if(file.files.length === 0) {
        return "Debe seleccionar un archivo";
    }
    const firstFile = file.files[0], resType = allowedTypes.find(expected => expected == firstFile.type);
    if(!resType || resType === undefined){
        file.value = '';
        return "Tipo de archivo no soportado";
    }
    if(firstFile.size > (1024 * 256)){
        file.value = '';
        return "El magnitud del archivo supera los 256KB, escoge otra imagen";
    }
    return true;
}
//  FUNCION QUE VALIDA FORMULARIO DEVOLVIENDO CALLBACK
const ValidateForm = ( event, callback ) => {
    if(callback && ('preventDefault' in event)) {
        event.preventDefault();
        for(const element of event.srcElement.querySelectorAll('input')){
            if(window.mainProps.skipCheck) break;
            if(["email", "password", "text"].includes(element.type)){
                if(element.value.trim().length <= 0) {
                    element.focus();
                    return callback(`El campo ${element.name} no puede estar vací­o`);
                }
                if(element.getAttribute('pattern')){
                    const regexVal = new RegExp(element.getAttribute('pattern'));
                    if(!regexVal.test(element.value.trim())){
                        element.focus();
                        return callback(`El ${element.name} no cumple con los requisitos de validación`);
                    }
                }
                const regex = `regex${element.type.charAt(0).toUpperCase() + element.type.slice(1)}`;
                if(!window.mainProps[regex].test(element.value.trim())) {
                    element.focus();
                    return callback(`El ${element.name} no cumple con los requisitos de validación`);
                }
            }else if(element.type === 'file'){
                const fileRes = ValidateInpFile(element);
                if(fileRes !== true) return callback(fileRes);
            }
            return callback(true);
        }
    }else {
        throw new Error("No callback or event provided..."); 
    }
}
//  SE REALIZAN EXPORTS
export { JsonChecker, PrintAlert, RemoveObjectProperty, RetrieveProject, RemoveSessionStorage, ValidateForm };