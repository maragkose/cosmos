<?php

/**
 * Funciones para la gestión y registro de errores. 
 * @package utils
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */


/**
 * Envía un correo con el mensaje de error de la excepción e información 
 * recogida de las cabeceras http.
 * @param Exception $exception 
 */
function sendErrorToMail($error) {
    if(!MAIL_SENDING)
        return False;
    echo $error;
    $mailBody = "ERROR: ".$error . PHP_EOL;
    $mailBody.= date("d/m/y H:i:s"). PHP_EOL;
    $mailBody.= arrayToString(UserInfo::getServerData());
    
    $mailSended = @mail(MAIL_RECEIVER, "Error", $mailBody);
    if (!$mailSended)
        errorRegister("El correo no pudo ser enviado a " . EMAIL, MAIL_SEND_ERROR);
}

/**
 * Guarda el registro de error en el log de errores.
 * @param string $error 
 */
function errorRegister($error) {
    if(!SAVE_TEXT_FILE){
        return False;
    }
    $file = @fopen(RESULTS_DIR_PATH . DIRECTORY_SEPARATOR . ERROR_LOG_FILENAME, "ab+");
    $date = date("d/m/y H:i:s");
    @fwrite($file, $date . "-" . $error . PHP_EOL);
    @fclose($file);
}

/**
 * Convierte un array en una cadena.
 * @param array $results
 * @param string $resultsString
 * @return string 
 */
function arrayToString($results, $resultsString = "") {

    foreach ($results as $field => $value) {
        if (is_array($value))
            $resultsString.="$field=>" . PHP_EOL . resultsArrayToString($value);
        else
            $resultsString.="$field=>$value" . PHP_EOL;
    }

    return $resultsString;
}

?>
