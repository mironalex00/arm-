<?php
    DEFINE('APP', (object)array (
        'NAME' => 'Mercaroñas',
        'AUTHORS' => (object) array(
            'DESIGNER' => (object) array(
                'NAME' => 'ALEXANDRU RAMON MIRON',
                'SOCIALS' => array(
                    'EMAIL' => '',
                    'INSTAGRAM' => '',
                )
            ),
            'MANAGER' => (object) array(
                'NAME' => 'ALEXANDRU RAMON MIRON',
                'SOCIALS' => array(
                    'EMAIL' => '',
                    'INSTAGRAM' => '',
                )
            )
        ),
        'DEVELOPERS' => array(
            (object) array(
                'NAME' => 'ALEXANDRU RAMON MIRON',
                'SOCIALS' => array(
                    'EMAIL' => '',
                    'INSTAGRAM' => '',
                )
            )
        )
    ));
    require_once(__DIR__. '/../functions.php');
    //
    session_start();
    //  
    if (session_status() == PHP_SESSION_NONE) {
        //  
        if(get_path() !== $_SERVER['HTTP_REFERER']) {
            //  SE REALIZA REDIRECCION PERMANENTE
            header('Location: '. get_path() . '/', true, 302);
            //  
            exit();
        }
    }
    if(isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        if($user->logindate !== CURRENTDATE) {
            $usr_id = $user->data->userId;
            $result = getUserCredentials(array(array('id' => $usr_id)));
            if($result === null) {
                //  SE REALIZA REDIRECCION PERMANENTE
                header('Location: '. get_path() . '/logout.php', true, 302);
            }
        }
        $actual = 'https://' . ($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        $expected = (get_path() . '/auth');
        if($actual === $expected){
            header('Location: '. get_path(), true, 308);
        }
    }else {
        $actual = 'https://' . ($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        $expected = (get_path() . '/auth');
        if($actual !== $expected){
            header('Location: '. get_path() . '/auth', true, 308);
        }
    }
?>