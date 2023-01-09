<?php
    include_once('./assets/php/controller/session.php');
    require_once('./assets/php/views/header.php');
?>
<script type="module">
    import { PrintCart } from './assets/js/renders/cart.js';
    window.addEventListener('load', () => PrintCart());
    window.addEventListener('storage', () => PrintCart());
</script>
<?php
    require_once('./assets/php/views/footer.php');
?>