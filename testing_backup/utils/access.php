<?php
/**
* @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
* @copyright Copyright 2012 Juan Haro
* @license http://www.opensource.org/licenses/mit-license.php MIT License
* @package utils
*/

session_start();

/**
 * Registra en un archivo de texto cada usuario que accede al cuestionario.
 * Guarda la session_id, hora de acceso y un identificador de referencia (si
 * está incluido en la url).
 * @param $formName El cuestionario para el que se registra el acceso
 * @return 0 Si el id de referencia indica que es un acceso de prueba. 
 * @throws Exception 
 */
function accessRegister($formName){
    if(!SAVE_TEXT_FILE){
        return False;
    }
    $accessData = array();
    
    // La referencia. Se usa para identificar enlaces enviados a diferentes muestras.
    // Se puede especificar qué referencia no se debe registrar (NO_REGISTER_REF)
    if(isset($_GET["ref"])){
       array_push($accessData,  $_GET["ref"]); 
    }
    
    // idCuestionario_access
    @$file = fopen(RESULTS_DIR_PATH.DIRECTORY_SEPARATOR.$formName.".access", "ab+");
    
    if(!$file)
        throw new Exception("No se pudo abrir el archivo de acceso del cuestionario $formName");
    
    fwrite($file,PHP_EOL);
    
    // Si hay una sesión iniciada, se registra el identificador de sesión
    if(session_id())
        array_push($accessData,  session_id());
    array_push($accessData, UserInfo::getIp());
    array_push($accessData, date("d/m/y H:i:s",UserInfo::getRequestTime()));
    fwrite($file, implode(CSV_COLUMN_DELIMITER, $accessData));
    
    fclose($file);
}
?>
