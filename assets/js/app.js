//  FUNCION ENCARGADA DE ELIMINAR UNA O VARIAS ENTRADAS SESSIONSTORAGE
function RemoveSessionStorage(...entries){
    entries = entries.length <= 0 ? sessionStorage : Object.entries(
        Object.values(entries)).reduce(
        (obj, [key, value]) => ({ ...obj, [value]: key }), {}
    );
    Object.keys(entries).forEach(entry => sessionStorage.removeItem(entry));
}
//  SE REALIZAN EXPORTS
export { RemoveSessionStorage };