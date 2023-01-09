# Ejercicio Tienda del Tema 04

Ejercicio cuya función es poder realizar y visualizar pedidos de una tienda. 

Esta tienda incluye un carrito cuyos productos y cantidades se pueden modificar.

## Requisitos
1. Tener instalado los siguientes servicios:
    - Apache
    - PHP
2. Tener habilitado el m&oacute;dulo HTTPS

Los servicios XAMPP, MAMPP o LAMPP incluyen empaquetados estos servicios sin necesidad de modificaciones. 
## Instalaci&oacute;n y configuraci&oacute;n

1. Descomprir el proyecto en la ruta del servidor Apache
2. Iniciar los servicios **Apache** y **MySQL**
3. Modificar los siguientes archivos:
    - [**db_credentials.php**](./assets/php/controller/db_credentials.php)
    Estos archivos contienen un objeto el cual se ha de modificar con los valores que se indica:
    ```
    (object)array (
        "DB_ENGINE" => '',
        "DB_NAME" => '',
        "DB_HOST" => '',
        "DB_USER" => '',
        "DB_PASS" => '',
    );
    ```
    **MODIFIQUE EXCLUSIVAMENTE EL _HOST, USER Y PASS_**
4. Importar la base de datos [SQL](./assets/php/controller/important_files/db.sql)
4. Entrar dentro de la siguiente [URL](https://localhost/Tema_04/)
5. Disfrutar de la aplicaci&oacute;n

# Propuestas de mejora

## Funcionalidades

1. La API no está completa, falta terminar la inserción de pedidos en el backend

# Licencias
[MIT](https://choosealicense.com/licenses/mit/)