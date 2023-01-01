<?php
    //  SE REQUIERE ARCHIVO PHP DE FUNCIONES
    require_once('./assets/php/functions.php');
    //  SE ARRANCA SESION PARA EL ARCHIVO
    session_start();
    //  SE LIBERAN TODAS LAS VARIABLES DENTREO DE LA SESION
    session_unset();
    //  SE DESTRUYE SESION
    session_destroy();
    //  SE ESCRIBEN LOS CAMBIOS Y SE ELIMINA
    session_write_close();
    //  SE ELIMINA LA COOKIE
    setcookie(session_name(), '', - 1);
    //  SE REALIZA REDIRECCION PERMANENTE
    header('Location: '. get_path() . '/', true, 302);
    //  SE CIERRA SCRIPT
    exit();
?>