//  SE REALIZAN IMPORTACIONES DE LOS COMPONENTES NECESARIOS de API Y APP
import { CreateOrder, DeleteCart, GetCart, GetCategories, GetProducts, UpdateCart } from '../api.js';
import { PrintAlert } from '../app.js';
//  FUNCION ENCARGADA DE PINTAR EL HTML
const PrintCart = () => {
    const cart = GetCart();
    const parent = document.querySelector('section.container > div.row');
    if(!parent) throw new Error("The parent provided does not existe or can not be located");
    if(parent.hasChildNodes) parent.innerHTML = '';
    if(cart === null || !Array.isArray(cart) || cart.length === 0) {
        DeleteCart();
        PrintAlert(false, 'Todavía no hay productos añadidos al carrito', parent, null)
        return;
    }else {
        const form = document.createElement('form'),
        table = document.createElement('table');
        form.id = 'cart';
        form.name = 'cart';
        table.innerHTML = `
        <thead>
            <tr>
                <th scope="col" class="text-center w-50">
                    Producto
                </th>
                <th scope="col" colspan="2" class="text-center">
                    Cantidad
                </th>
                <th scope="col" colspan="2" class="text-center">
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody>${GenerateTBody(cart)}</tbody>
        <tfoot>
            <tr>
                <td colspan="4">
                    <button class="btn btn-success" type="submit">
                        Realizar pedido
                    </button>
                </td>
            </tr>
        </tfoot>
        `;
        AddEventsListener(table);
        table.classList.add('table', 'table-hover', 'text-center');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            CreateOrder(e.target);
        });
        form.append(table);
        parent.replaceChild(parent.appendChild(form), parent.querySelector('form#cart'));
    }
}
//  FUNCION ENCARGADA DE AÑADIR LOS EVENTOS
const AddEventsListener = (parent) => {
    parent.querySelectorAll('button[id^="btn-add"]').forEach(element => {
        const id = element.id.substring(element.id.length - 1);
        element.addEventListener('click', e => UpdateItem(id, true))
    });
    parent.querySelectorAll('button[id^="btn-rm"]').forEach(element => {
        const id = element.id.substring(element.id.length - 1);
        element.addEventListener('click', e => UpdateItem(id, false))
    });
}
//  FUNCION QUE GENERA TBODY
const GenerateTBody = function(cart){
    const tbody = document.createElement('tbody');
    for (const [id, val] of Object.entries(cart)) {
        const isDisabled = (val.quantity >= val.max) ? 'disabled' : '';
        tbody.innerHTML += `
            <tr>
                <td>${val.prod}</td>
                <td>${val.quantity}</td>
                <td>
                    <div class="form-outline">
                        <input type="hidden" name="prod[${id}]" value="${val.prod}"/>
                        <input type="number" id="quantity-${id}"  
                            class="form-control form-control-lg" 
                            min="${(val.max - val.quantity) <= 0 ? 0 : 1}" 
                            max="${val.max}"/>
                        <label class="form-label" for="quantity-${id}">Cantidad</label>
                        <input type="hidden" name="quantity[${id}]" value="${val.quantity}"/>
                    </div>
                </td>
                <td>
                    <button id="btn-add-${id}" type="button" ${isDisabled} class="btn btn-outline-success p-0">
                        <i class="fa-solid fa-plus py-2 px-4" title="Añada producto"></i>
                    </button>
                    <button id="btn-rm-${id}" type="button" class="btn btn-outline-danger p-0">
                        <i class="fa-regular fa-trash-can py-2 px-4" title="Elimine producto"></i>
                    </button>
                </td>
            </tr>
        `;
    }
    return tbody.innerHTML;
}
//  FUNCION QUE ACTUALIZA LOS ITEM DE LA INTERFAZ
const UpdateItem = function(id, add = false) {
    const cart = GetCart(), 
    prodq = document.querySelector(`#quantity-${id}`),
    prodn = prodq?.previousElementSibling,
    prodqVal = prodq?.value?.trim?.(), prodnVal = prodn?.value?.trim?.(),
    prod = cart.filter(x => x.prod === prodnVal)?.[0];
    if((prodq.value.trim().length === 0 || isNaN(prodqVal)) && add) return;
    let valChanging = !add ? prod?.quantity - (parseInt(prodqVal)) : (parseInt(prodqVal)) + prod?.quantity;
    if(prod?.quantity > prod?.max) return;
    if(valChanging < 0 || valChanging > prod?.max) {
        alert(!add ? 'No se pueden eliminar más productos de los que se posee' : "El valor introducido supera al del stock");
        prodq.focus();
    }else {
        prod.quantity = isNaN(parseInt(prodqVal)) && !add ? prod.quantity : parseInt(prodqVal);
        UpdateCart(prod, add);
    }
}
//  SE EXPORTA
export { PrintCart }