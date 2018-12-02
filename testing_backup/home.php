<?php
require_once "config.php";
require_once "../core/html_api.php";

function checkPermissions($dir){
    if(is_readable($dir) && is_writable($dir))
        return "Sí";
    else
        return "No";
}

function isReadable($dir){
    if(is_readable($dir))
        return "Sí";
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Testmaker: aplicación para crear cuestionarios online</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="html/styles_home.css" media="all">
        <link rel="stylesheet" type="text/css" href="html/print.css" media="print">
        <style>
            .dir{
                padding:3px;
                background-color: #ddd;
            }
            </style>
    </head>

    <body>
        <div class="form-title">
            <h2>Testmaker: comprobación de permisos de escritura y lectura</h2>
        </div>
        <div>
            <p>Permisos de escritura y lectura en <strong>/<?php echo RESULTS_DIR_PATH?></strong>: <?php echo checkPermissions(RESULTS_DIR_PATH)?></p>
         <p>Permisos de lectura en <strong>/<?php echo FORM_DIR?></strong>: <?php echo isReadable(FORM_DIR)?></p>
         <p>Permisos de escritura y lectura en <strong>/<?php echo CACHE_DIR?></strong>: <?php echo checkPermissions(CACHE_DIR)?></p>
        </div>
        <div>
            <h2>Enlaces</h2>
        <ul>
            <li><a href="http://jharo.net/dokuwiki">Documentación de la aplicación</a></li>
            <li><a href="http://notepad-plus-plus.org/">Editor de texto recomendado</a></li>
            <li><a href="checkFields.php">Detección de caracteres no válidos en los identificadores de un cuestionario</a>
        </ul>
        </div>
          
    </body>
</html>


