<?php 
$path = get_path();
$appname = mb_convert_case(APP->NAME, MB_CASE_TITLE, 'UTF-8');
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$last_request_uri = mb_convert_case(basename($request_uri), MB_CASE_TITLE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://cdn-production-opera-website.operacdn.com/staticfiles/assets/images/favicon/favicon.12c955371a4b.ico" type="image/x-icon">
    <title><?= $appname . (basename(parse_url($path, PHP_URL_PATH)) === $last_request_uri ? '' : ' - ' . $last_request_uri) ?></title>
    <!-- CSS FRAMEWORKS -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://ka-f.fontawesome.com/releases/v6.2.0/css/free-v4-font-face.min.css">
    <link rel="stylesheet" href="https://ka-f.fontawesome.com/releases/v6.2.0/css/free-v5-font-face.min.css">
    <link rel="stylesheet" href="https://ka-f.fontawesome.com/releases/v6.2.0/css/free-v4-shims.min.css">
    <link rel="stylesheet" href="https://ka-f.fontawesome.com/releases/v6.2.0/css/free.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.1/mdb.min.css" />
    <!-- CSS WEBSITE -->
    <link rel="stylesheet" href="./assets/css/main.css">
    <!-- SCRIPTS    -->
    <script  type="module">
        import { LogOut } from './assets/js/session.js'
        if(document.querySelector('#no-script')) document.querySelector('#no-script').remove();
        if(document.querySelector('#main').style) document.querySelector('#main').removeAttribute('style');
        window.mainProps = Object.assign({ 'regexEmail': new RegExp("<?= MAILREGEX ?>")}, window.mainProps);
        window.mainProps = Object.assign({ 'regexPassword': new RegExp("<?= PASSREGEX ?>")}, window.mainProps);
        window.mainProps = Object.assign({ 'regexText': new RegExp("<?= NAMEREGEX ?>")}, window.mainProps);
        window.mainProps = Object.assign({ 'regexUser': new RegExp("<?= USERREGEX ?>")}, window.mainProps);
        window.logout = () => LogOut();
    </script>
    <script src="./assets/js/main.js"></script>
</head>
<body>
<div id="main" style="display: none;">
<?php
    if(isset($_SESSION['user'])) {
        echo <<<HTML
            <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
            <div class="container-fluid">
                <a class="navbar-brand text-uppercase" href="$path">$appname</a>
                <button class="navbar-toggler" type="button" 
                    data-bs-toggle="collapse" data-bs-target="#navbarToggle" 
                    aria-controls="navbarToggle" aria-expanded="false" 
                    aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarToggle">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="menu-header">
                        
                    </ul>
                    <div class="d-flex flex-row justify-content-between justify-content-lg-end bd-highlight">
                        <p id="user" class="m-2 me-5 text-uppercase font-weight-bold text-truncate" style="max-width: 12em">
            HTML;
                echo $_SESSION['user']->data->userFullname . ' ' . $_SESSION['user']->data->userSurname;
            echo <<<HTML
                        </p>
                        <a class="btn btn-outline-danger text-uppercase fw-bolder" style="padding: 10px 20px" aria-current="page" onclick="logout()">
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </nav> 
        <section class="container mt-5">
            <div class="row"></div>
        </section>
        HTML;
    }
?>
<!--    HERE STARTS YOUR APPLICATION -->