<?php
    //  SE IMPORTAN
    include_once('./assets/php/controller/session.php');
    require_once('./assets/php/views/header.php');
?>
<link rel="stylesheet" href="./assets/css/products.css">
<script type="module">
    //  SE IMPORTA
    import { PrintCategories, PrintProducts, UpdateGUI } from './assets/js/renders/index.js';
    //  EVENTOS
    window.addEventListener('load', () => {
        PrintCategories();
        PrintProducts();
        UpdateGUI();
    });
    window.addEventListener('storage', () => { UpdateGUI() });
</script>
<?php require_once('./assets/php/views/footer.php') ?>