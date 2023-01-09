//  SE IMPORTA
import { GetCategories, GetProducts, GetCart, UpdateCart } from '../api.js';
import { PrintAlert, RemoveObjectProperty } from '../app.js';
//  -   -   -   -   -   -   GLOBAL VARIABLES   -   -   -   -   -   -   //
const search = window.location.search.match(/(\b(?<=\=)\w+\b(?=))/);
//  -   -   -   -   -   -   FUNCTIONS   -   -   -   -   -   -   //
//  
const PrintCategories = () => {
    const mainMenu = document.querySelector('ul#menu-header');
    if(!mainMenu) throw new Error('Header element not provided, please do not modify the DOM');
    GetCategories((res) => {
        mainMenu.innerHTML = '';
        if(Array.isArray(res)){
            const newUl = document.createElement('ul'), visible = 3;
            let html = '';
            for (const [index, value] of Object.entries(res)) {
                const bolded = search && value.name.toLowerCase().startsWith(search[0]) ? 'fw-bold' : '';
                if(index >= visible){
                    if(index == visible){
                        html += `
                        <li class="nav-item dropdown highlight noselect">
                            <span class="nav-link dropdown-toggle" role="button" aria-expanded="true" data-bs-toggle="dropdown">
                                Más
                            </span>
                            <ul class="dropdown-menu">
                        `;                      
                    }
                    html += `
                        <li>
                        <a class="dropdown-item ${bolded}" href="?category=${value.name.toLowerCase()}">
                            ${value.name}
                        </a>
                        </li>
                    `;
                    if(index == (res.length - 1)){
                        html += `
                            </ul>
                        </li>
                        `;
                        newUl.innerHTML += html;
                    }
                }else {
                    newUl.innerHTML += `
                    <li class="nav-item">
                        <a class="nav-link ${bolded}" href="?category=${value.name.toLowerCase()}">
                            ${value.name}
                        </a>
                    </li>
                    `;
                }
            }
            mainMenu.innerHTML = newUl.innerHTML;
        }
    });
}
//  
const PrintProducts = () => {
    const parent = document.querySelector('section.container > div.row');
    if(!parent) throw new Error("The parent provided does not existe or can not be located");
    GetProducts((res) => {
        if(res.errors){
            if(res.errors.startsWith('No existen productos')){
                PrintAlert(true, 'Todavía no hay productos añadidos al catálogo', parent, null);
            }else {
                PrintAlert(true, res.errors.errors.toString(), parent, null);
            }
            return;
        }
        let products = res.filter(prod => prod.category.trim().toLowerCase().includes(search?.[0] ?? prod.category.trim().toLowerCase()));
        if(products.length === 0) {
            PrintAlert(false, 'La categoría introducida no existe', parent, 1);
        }
        const cart = GetCart();
        for (const [id, product] of Object.entries(products)) {
            const prod = document.createElement('div'), form = document.createElement('form'),
            ivaless_price = (product.price - ((product.price * 21) / 100)).toFixed(2),
            item = cart?.find(el => el?.prod === product?.product);
            prod.classList.add('col-10', 'col-sm-6', 'col-xl-3');
            prod.classList.add('offset-1', 'offset-sm-0', `${id < (products.length - 1) ? 'mb-1' : 'mb-5'}`, 'prod-show', 'user-select-none');
            prod.setAttribute("role", "button");
            form.id = `product-${id}`;
            form.name = `product-${id}`;
            form.classList.add('h-25rem');
            form.innerHTML = `
            <div class="card text-justify rounded-3 border-secondary m-4 h-100" style="min-height: 10rem">
                <div class="card-header text-capitalize fw-bolder text-center">${product.product}</div>
                <div class="card-body py-2 px-4">
                    <div class="grid">
                        <div class="header w-100 overflow-auto">
                            <p class="card-text user-select-auto" 
                                ${product.description.length > 80 ? 'style="text-align: justify"' : ''}>
                                ${product.description}
                            </p>
                        </div>
                        <div class="body w-100 text-center">
                            <div class="form-outline">
                                <input type="hidden" name="prod" value="${product.product}"/>
                                <input type="number" name="quantity" id="quantity-${id}" 
                                    class="form-control form-control-lg" 
                                    min="${product.stock === (item?.quantity ?? 0) ? 0 : 1}" 
                                    max="${product.stock - (item?.quantity ?? 0)}"/>
                                <label class="form-label" for="quantity-${id}">Introduzca cantidad</label>
                            </div>
                        </div>
                        <div class="footer w-100 text-center">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-8 col-md-6">
                                        <div class="mt-2 me-auto iva-price text-truncate" title="Precio: ${product.price}€">
                                            ${product.price}€
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-6 text-end">
                                        <div class="ivaless-price">
                                            <span class="titulo">Sin IVA</span>
                                            <p class="fw-bold" title="Precio sin iva: ${ivaless_price}€">
                                                ${ivaless_price}€
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-outline-primary"> 
                        Añadir al carrito 
                    </button>
                </div>
            </div>`;
            form.addEventListener('submit', addToCart);
            prod.appendChild(form);
            parent.appendChild(prod);
        }
    });
}
//  
const addToCart = (e) => {
    e.preventDefault();
    let prodElem = e.srcElement.prod, quantityElem = e.srcElement.quantity,
    prod = prodElem?.value.trim(), quantity = quantityElem?.value.trim(),
    min = quantityElem.min, max = quantityElem.max;
    e.target.reset();
    if(quantity.length === 0 || isNaN(quantity)){
        alert("Debes introducir un valor numérico");
        return;
    }else if(min < 0 || min > max){
        alert("Ha ocurrido un error, refresca la página");
        return;
    }else if(min === '0' || max === '0'){
        alert("No puedes realizar un pedido de este producto puesto que no hay stock");
        return;
    }
    quantity = parseInt(quantity), min = parseInt(min), max = parseInt(max);
    UpdateGUI({...{target: quantityElem.id}, ...{max, min, prod, quantity}});
}
//
const UpdateGUI = (req = null) => {
    if(req && typeof req === 'object'){
        UpdateCart(req);
        alert("Añadido al carrito correctamente");
    }
    const cart = GetCart(), newCartBtn = document.createElement('a'), 
    icon = document.createElement('i'), actualCartBtn = document.querySelector('a#btnCart');
    if(Array.isArray(cart) && cart.length > 0) {    
        cart.forEach(prod => {
            const input = document.querySelector(`input[type="number"]#${prod?.target}`);
            if(!input) return;
            input.setAttribute('max', prod.max - prod.quantity);
            input.setAttribute('min', prod?.min ?? 1);
        });
        newCartBtn.id = "btnCart";
        newCartBtn.setAttribute('href', './cart');
        newCartBtn.classList.add('border-0', 'border-circle', 'bg-warning', 'text-light');
        icon.classList.add('fas', 'fa-shopping-cart', 'py-3');
        newCartBtn.appendChild(icon);
        if(!actualCartBtn) document.querySelector('div#main').appendChild(newCartBtn);
    }else {
        actualCartBtn?.remove?.();
    }
}
//  SE EXPORTA
export { PrintCategories, PrintProducts, UpdateGUI }