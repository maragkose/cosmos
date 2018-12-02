<?php

/**
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
//$i = microtime();
// Para recuperar el id de la sesión y guardarlo en el registro
session_start();
include_once "Form.php";
include_once "utils/save_results.php";
include_once "utils/error_register.php";
include_once "utils/LoadForm.php";


/**
 * El array de resultados contiene los campos de $_POST recibidos de
 * la vista del cuestionario
 */
if (!isset($_POST["form_name"])) {
    include (HTML_DIR . "/thanks_alt.html");
    die();
}

$results = $_POST;
// Campos con información adicional sobre el participante.
$results["user_ip"] = UserInfo::getIp();
$results["os_language"] = UserInfo::getLanguage();
$results["time_start"] = $_POST["time_start"];
$results["time_start_hr"] = date("d/m/y H:i:s", $_POST["time_start"]);
$results["time_end"] = UserInfo::getRequestTime();
$results["time_end_hr"] = date("d/m/y H:i:s", UserInfo::getRequestTime());
$results["session_id2"] = session_id();
$results["user_agent"] = UserInfo::getUserAgent();
//$serialized = serialize($results);
//echo $serialized;
//print_r(unserialize($serialized));

try {
    $loader = new loadForm($_POST["form_name"]);
    $form = $loader->loadForm();
    $results["test_name"] = $form->getTitle();
    $savingName = $_POST["personal_data__Surname"] . '_' . $_POST["personal_data__Name"] . '_' . $_POST["personal_data__ID"];
    // Recupera y codifica todos los identificadores de ítems del cuestionario
    $formFields = generateItemCodes::generateAll($form->getItemsGroups());
    // Añade los campos de información adicional
    $fields = array_merge($formFields, array_keys($results));
    // Elimina campos duplicados
    $fields = array_unique($fields);
} catch (Exception $e) {
    errorRegister($e->getMessage());
    sendErrorToMail($e->getMessage());
}





/**
 * Funciones para el guardado de resultados. 
 */
if (SAVE_TEXT_FILE) {
    try {
        saveResultsToFileText($savingName, $results, $fields);
    } catch (Exception $e) {
        errorRegister($e->getMessage());
        sendErrorToMail($e->getMessage());
    }

    try {
        saveRawResults($savingName, $results);
    } catch (Exception $e) {
        errorRegister($e->getMessage());
        sendErrorToMail($e->getMessage());
    }
}

try {
    switch (DATABASE_SYSTEM):
        case MYSQL:
            saveResultsToMySQL($savingName, $results, $fields);
            break;
        case MONGODB:
            saveResultsToMongoDb($results, $savingName);
            break;
        case NULL:
            continue;
    endswitch;
} catch (dbException $e) {
    errorRegister($e->getMsg());
    sendErrorToMail($e->getMsg());
} catch (MongoConnectionException $e) {
    errorRegister($e->getMessage());
    sendErrorToMail($e->getMessage());
}


if (MAIL_SENDING) {
    try {

        sendResultsToMail($results);
    } catch (Exception $e) {
        echo $e->getMessage();
        errorRegister($e->getMessage());
        sendErrorToMail($e->getMessage());
    }
}

try {
// Carga el archivo de cuestionario para recuperar algunos campos 
// que se mostrarán en la vista de agradecimiento (título y campo de agradecimiento).
    $loader->getThanksPage();
} catch (Exception $e) {
    errorRegister($e->getMessage());
    sendErrorToMail($e->getMessage());
// Si ocurre algún error al cargar el cuestionario, muestra una vista 
// alternativa de agradecimiento sin los campos de cuestionario (título y 
// campo de agradecimiento).
    include HTML_DIR . "/thanks_alt.html";
}
//$f = microtime();
//echo $f - $i;
?>
