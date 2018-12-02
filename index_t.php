<?php
/**
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
include_once "Form.php";
include_once "utils/LoadForm.php";
include_once "utils/LoadFormCache.php";
include_once 'utils/error_register.php';
include_once "utils/access.php";
include_once "../core.php";

auth_reauthenticate();
#access_ensure_project_level( CANDIDATE );
/**
 * Este archivo automatiza la carga de un test.
 * Carga el test con el nombre especificado en el 
 * parametro GET 'test'
 * Uso: url?index.php?test=nombre_archivo
 */
if (!isset($_GET["test"])) {
    include("home.php");
    die("<div class='warning'>No se ha especificado un nombre de test. Para cargar un test a partir
        de un archivo de test utiliza el siguiente enlace: index.php?test=nombre_test. 
        Recuerda que nombre_test es el nombre del archivo de test
        sin incluir la extensión .yml.</div>");
}


try {
    $fileName = $_GET[CUESTIONARIO_GET_VAR];
    $loader = loadForm::getInstance($fileName);
    $loader->getHTML();
    accessRegister($fileName);
} catch (Exception $e) {
    switch ($e->getCode()) {
        case FORM_NOT_FOUND:
            echo $e->getMessage();
            break;
        case NO_READABLE_FORM_DIR:
            break;
        case CACHE_CREATION_FAIL:
            // Forzar la carga del formulario (TRUE), sin acceder a la caché
            $loader = loadForm::getInstance($fileName, TRUE);
            $loader->getHTML();
            break;
    }
    errorRegister($e->getMessage());
    sendErrorToMail($e->getMessage());
}
?>

