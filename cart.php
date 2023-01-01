<?php
    include_once('./assets/php/controller/session.php');
    require_once('./assets/php/views/header.php');
?>
<script type="module">
    import { CreateOrder, DeleteCart, GetCart, GetCategories, GetProducts, UpdateCard } from './assets/js/api.js'
    window.addOrder = (event, callback) => {
        event.preventDefault();
        CreateOrder(event.srcElement, (response) => {
            console.log(response)
        });
    }
    window.deleteCart = () => DeleteCart();
    window.getCart = () => GetCart();
    window.getProducts = (callback) => GetProducts((response) => {
        if(callback) callback(response);
    });
    window.getCategories = (callback) => GetCategories((response) => {
        if(callback) callback(response);
    });
    window.updateCart = (order, action = true) => UpdateCard(order, action);
    document.querySelector('ul#menu-header').classList.replace('mb-2', 'mb-4');
</script>
<script>
    window.addEventListener('load', () => {
        printCart();
    }); 
    const printCart = () => {
        const cart = getCart();
        const parent = document.querySelector('section.container > div.row');
        if(cart === null || !Array.isArray(cart) || cart.length === 0) {
            deleteCart();
            parent.innerHTML = `
            <div class="col-12 col-md-8 offset-md-2 text-center text-bold prod-show">
            <div class="alert alert-warning alert-dismissible" role="alert">
                <p class="mt-1 fw-bold">Todavía no hay productos añadidos al carrito</p>
                <div class="mt-5">
                    <a class="text-danger fw-bold" href="./">
                        <span class="text-decoration-underline">Volver atrás</span>
                    </a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="this.parentElement.remove()"></button>
            </div>`;
            return;
        }else {
            const form = document.createElement('form'),
            table = document.createElement('table'),
            tableHead = document.createElement('thead'),
            tableBody = document.createElement('tbody');
            document.querySelector('table')?.remove();
            form.id = 'cart';
            form.name = 'cart';
            tableHead.innerHTML = `
            <tr>
                <th scope="col" class="text-center w-50">Producto</th>
                <th scope="col" colspan="2" class="text-center">Cantidad</th>
                <th scope="col" colspan="2" class="text-center">Acciones</th>
            </tr>
            `;
            for (const [index, val] of Object.entries(cart)) {
                tableBody.innerHTML += `
                    <tr>
                        <td>${val.prod}</td>
                        <td>${val.quantity}</td>
                        <td>
                            <div class="form-outline">
                                <input type="hidden" name="prod[${index}]" value="${val.prod}"/>
                                <input type="hidden" name="quantity[${index}]" value="${val.quantity}"/>
                                <input type="number" id="quantity-${index}"  
                                    class="form-control form-control-lg" 
                                    min="${(val.max - val.quantity) <= 0 ? 0 : 1}" 
                                    max="${val.max}"/>
                                <label class="form-label" for="quantity-${index}">Cantidad</label>
                            </div>
                        </td>
                        <td>
                            <button type="button" ${(val.quantity >= val.max) ? 'disabled' : ''} 
                                class="btn btn-outline-success" onclick="updateItem(${index}, true)">
                                Añadir
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="updateItem(${index})">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                `;
            }
            table.appendChild(tableHead);
            table.appendChild(tableBody);
            table.innerHTML += `
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
            table.classList.add('table', 'table-hover', 'text-center');
            form.addEventListener('submit', (e) => addOrder(e));
            form.append(table);
            if(!parent.querySelector('form#cart')) {
                parent.appendChild(form);
                return;
            }
            parent.replaceChild(parent.appendChild(form), parent.querySelector('form#cart'));
        }
    }
    const updateItem = function(identfier, add = false) {
        const cart = getCart(), 
        number = document.querySelector(`#quantity-${identfier}`),
        value = document.querySelector(`input[name="quantity[${identfier}]"]`).previousElementSibling,
        quantity = number.value.trim(), prod = value.value.trim();
        filter = cart.filter(x => x.prod === prod);
        if((number.value.trim().length === 0 || isNaN(quantity)) && add) return;
        if(!checkItem(filter?.[0], quantity, add)) return;
	    printCart();
        number.value = '';
    }
    const checkItem = (object, newVal, add = false) => {
        newVal = (isNaN(newVal) || newVal.trim().length === 0) ? object.quantity : parseInt(newVal);
        valChanging = !add ? object?.quantity - newVal : newVal + object?.quantity;
        if(object?.quantity > object?.max) return;
        if(valChanging < 0 || valChanging > object?.max) {
            alert(!add ? 'No se pueden eliminar más productos de los que se posee' : "El valor introducido supera al del stock");
            return;
        }
        object.quantity = newVal;
        updateCart(object, add);
        return true;
    }
</script>
<?php
    require_once('./assets/php/views/footer.php');
?>