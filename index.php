<?php
    include_once('./assets/php/controller/session.php');
    require_once('./assets/php/views/header.php');
    echo "\n" . <<<HTML
    <link rel="stylesheet" href="./assets/css/products.css">
    <script type="module">
        import { GetCategories, GetProducts, UpdateCard, GetCart } from './assets/js/api.js'
        window.getProducts = (callback) => GetProducts((response) => { if(callback) callback(response) });
        window.getCart = () => GetCart();
        window.getCategories = (callback) => GetCategories((response) => { if(callback) callback(response) });
        window.updateCart = (order, action = true) => UpdateCard(order, action);
    </script>
    HTML;
?>
<script>
    const search = window.location.search.match(/(\b(?<=\=)\w+\b(?=))/);
    const printCategories = () => {
        const mainMenu = document.querySelector('ul#menu-header');
        getCategories((res) => {
            if(Array.isArray(res)){
                const newUl = document.createElement('ul');
                mainMenu.innerHTML = '';
                for (const [index, value] of Object.entries(res)) {
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    li.classList.add('nav-item');
                    a.classList.add('nav-link');
                    a.setAttribute('href', `?category=${value.name.toLowerCase()}`);
                    if(search && value.name.toLowerCase().startsWith(search[0])){
                        a.classList.add('fw-bold')
                    }
                    a.textContent = value.name
                    li.appendChild(a)
                    if(index > 4){
                        const subLi = li.cloneNode(true);
                        li.innerHTML = '';
                        if(index == 5) {
                            const span = document.createElement('span');
                            newUl.classList.add('dropdown-menu');
                            span.classList.add('nav-link', 'dropdown-toggle');
                            span.setAttribute('role', 'button');
                            span.setAttribute('aria-expanded', 'false');
                            span.setAttribute('data-bs-toggle', 'dropdown');
                            span.textContent = 'Más';
                            li.classList.add('dropdown', 'highlight', 'noselect');
                            li.appendChild(span);
                            mainMenu.appendChild(li);
                        }
                        subLi.removeAttribute('class');
                        subLi.querySelector('a').classList.replace('nav-link', 'dropdown-item');
                        newUl.appendChild(subLi);
                        if((parseInt(index) + 1) === res.length){
                            mainMenu.querySelector('li:last-child').appendChild(newUl);
                        }
                    }else {
                        mainMenu.appendChild(li);
                    }
                }
            }else{
                mainMenu.innerHTML = '';
            }
        })
    }
    const printProducts = () => {
        const parent = document.querySelector('section.container > div.row');
        getProducts((res) => {
            if(res.errors){ 
                if(res.errors.startsWith('No existen productos')){
                    parent.innerHTML = `
                    <div class="alert alert-warning alert-dismissible col-12 col-md-8 offset-md-2 text-center text-bold prod-show" role="alert">
                        <strong>Todavía no hay productos añadidos al catálogo</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="this.parentElement.remove()"></button>
                    </div>`;
                }else {
                    parent.innerHTML = `
                    <div class="alert alert-warning alert-dismissible col-12 col-md-8 offset-md-2 text-center text-bold prod-show" role="alert">
                        <strong>${res.errors.errors.toString()}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="this.parentElement.remove()"></button>
                    </div>`;
                }
                return;
            }
            let products = res.filter(prod => prod.category.trim().toLowerCase().includes(search?.[0] ?? prod.category.trim().toLowerCase()));
            if(products.length === 0) {
                parent.innerHTML = `
                <div class="alert alert-danger alert-dismissible col-12 col-md-8 offset-md-2 text-center text-bold prod-show" role="alert">
                    <strong>La categoría introducida no existe</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="this.parentElement.remove()"></button>
                </div>`;
            }
            const cart = getCart();
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
        })
    }
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
        updateGUI({...{target: quantityElem.id}, ...{max, min, prod, quantity}});
    }
    const updateGUI = (req = null) => {
        const cart = getCart(),
        item = cart?.find(el => el?.prod === req?.prod),
        input = document.querySelector(`input[type="number"]#${req?.target}`),
        newCartBtn = document.createElement('a'), icon = document.createElement('i'), actualCartBtn = document.querySelector('a#btnCart');
        newCartBtn.id = "btnCart";
        newCartBtn.setAttribute('href', './cart');
        newCartBtn.classList.add('border-0', 'border-circle', 'bg-warning', 'text-light');
        icon.classList.add('fas', 'fa-shopping-cart', 'py-3');
        newCartBtn.appendChild(icon);
        if(actualCartBtn) actualCartBtn.remove();
        if(!req && !cart) return;
        if(req){
            let actual = req.max - (req?.quantity);
            input.setAttribute('max', actual);
            input.setAttribute('min', actual <= 0 ? 0 : req.min);
            removeObjectProperties(req, ['target']);
            updateCart(req);
            alert("Añadido al carrito correctamente");
        }
        document.querySelector('div#main').appendChild(newCartBtn);
    }
    window.addEventListener('load', () => {
        printCategories();
        printProducts();
        updateGUI();
    })
</script>
<?php 
    require_once('./assets/php/views/footer.php');
?>