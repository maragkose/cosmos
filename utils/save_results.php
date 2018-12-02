<?php

include_once "includes/objoo_trunk/include.php";

/**
 * Funciones para automatizar el registro de los resultados. 
 * @package utils
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Recibe los resultados del cuestionario enviados por el participante y los
 * guarda en una base de datos MySQL. Comprueba si existe una tabla donde
 * guardarlos y, en caso afirmativo, si sus campos se corresponden con los
 * del cuestionario ($formFields).
 * Si los campos de la tabla son diferentes a los campos del cuestionario, se guarda
 * una copia de la tabla y se crea una nueva que incluya todos los
 * campos del cuestionario.
 * 
 * @param string $tableName
 * @param array $results Los resultados del cuestionario enviados por el participante
 * @param array $formFields Todos los campos que componen el cuestionario
 */
function saveResultsToMySQL($tableName, $results, $formFields) {
    sql::getInstance(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
    // Crea la base de datos donde se guardarán los resultados si ésta no existe.
    if (!query::dbExists(MYSQL_RESULTS_DB))
        query::createDB(MYSQL_RESULTS_DB);
    // Selecciona la base de datos de resultados
    sql::getInstance()->setDb(MYSQL_RESULTS_DB);
    // Crea la tabla si no existe.
    if (dbTable::get($tableName)->isNew())
        createMySQLTable($tableName, $formFields);
    // Si los campos recibidos no coinciden con los de la base de datos
    $difFields = count(array_diff($formFields, dbTable::get($tableName)->getFieldNames()));
    if ($difFields) {
        // Guarda una copia de la tabla (nombre_tabla_diames_horaminutos)
        query::renameTable($tableName, $tableName . "_" . date("dm_hi"));
        dbTable::get($tableName)->unsetTable();
        // Crea una nueva tabla
        createMySQLTable($tableName, $formFields);
    }
    // Guarda los resultados
    $row = new tableRow($tableName);

    foreach ($results as $field => $value) {
        if (is_array($value))
            $value = implode(",", $value);
        $row->$field = (string) $value;
    }
    $row->add();
}

/**
 * Crea una nueva tabla en la base de datos MySQL con los campos del cuestionario
 * como columnas.
 * @param string $tableName
 * @param array $fields Los campos del cuestionario
 */
function createMySQLTable($tableName, $fields) {
    $table = dbTable::get($tableName);
    foreach ($fields as $field) {
        $table->$field;
    }
    $table->save();
}

/**
 * Guarda los resultados en la colección $collectionName de la base de datos
 * MongoDB.
 * @param array $results
 * @param string $collectionName
 */
function saveResultsToMongoDb($results, $collectionName) {

    $m = new Mongo();
    $db = $m->selectDb(MONGO_RESULTS_DB);
    $dbCollection = $db->selectCollection($collectionName);
    $dbCollection->insert($results);
}

/**
 * Guarda los resultados del cuestionario en un archivo de texto separado
 * por comas (csv). Si los campos del cuestionario han sido modificados, guarda
 * una copia del archivo de resultados existente y crea uno nuevo con los campos
 * recibidos en $fields.
 * 
 * @param string $savingName Nombre del archivo a guardar
 * @param array $results Los resultados del cuestionario enviados por el participante
 * @param array $formFields Todos los campos que componen el cuestionario
 */
function saveResultsToFileText($savingName, $results, $fields) {
    if (!existsResultsFile($savingName))
        $file = newResultsFile($savingName, $fields);

    $file = openResultsFile($savingName, "ab+");
    $row = "";

    $resultsFileFields = getResultsFileHeader($file);
    $difFields = count(array_diff($fields, $resultsFileFields));

    if ($difFields) {
        fclose($file);
        // Guarda una copia del archivo de resultados (nombre_archivo_diames_horaminutos)
        rename(RESULTS_DIR_PATH . DIRECTORY_SEPARATOR . $savingName . "." . TEXT_RESULTS_EXTENSION, RESULTS_DIR_PATH . DIRECTORY_SEPARATOR . $savingName . "_" . date("dm_hi") . "." . TEXT_RESULTS_EXTENSION);
        $file = newResultsFile($savingName, $fields);
        $resultsFileFields = getResultsFileHeader($file);
    }
    
    // Recorre todos los campos de la cabecera y comprueba si se encuentran en el array de resultados
    // Si el campo existe en el array de resultados, añade una nueva columna con el valor recibido en el archivo csv.
    // Si no existe, crea una columna vacía.
    foreach ($resultsFileFields as $field) {
        if ($row)
            $row.=CSV_COLUMN_DELIMITER;
        if (key_exists($field, $results)) {
            if (is_array($results[$field]))
                $row.="\"" . implode(",", $results[$field]) . "\"";
            else
                $row.="\"" . $results[$field] . "\"";
        }
        else
            $row.="";
    }

    fwrite($file, $row . PHP_EOL);
    fclose($file);
}

function saveRawResults($savingName, $results) {
    $file = @fopen(RESULTS_DIR_PATH . DIRECTORY_SEPARATOR . $savingName . "." . RAW_TEXT_RESULTS_EXTENSION, "ab");
    if (!$file)
        throw new Exception("No se pudo abrir el archivo de resultados en bruto del cuestionario $savingName", RESULTS_TEXT_FILE_ERROR);
    fwrite($file, serialize($results).PHP_EOL);
    fclose($file);
}

/**
 * Crea un nuevo archivo de texto de resultados y escribe la cabecera 
 * de campos.
 * @param type $savingName
 * @param type $fields Todos los campos que componen el cuestionario
 * @return resource $file 
 */
function newResultsFile($savingName, $fields) {
    $file = openResultsFile($savingName, "wb+");
    writeResultsFileHeader($file, $fields);
    return $file;
}

/**
 * Escribe la cabecera de campos en el archivo de resultados. La cabecera
 * establece las columnas del archivo.
 * @param type $file
 * @param type $fieldNames Todos los campos que componen el cuestionario
 */
function writeResultsFileHeader($file, $fieldNames) {
    fwrite($file, implode(CSV_COLUMN_DELIMITER, $fieldNames) . PHP_EOL);
}

/**
 * Lee el archivo de resultados y devuelve un array con los campos de la cabecera del archivo de resultados.
 * @param resource $file
 * @return array 
 */
function getResultsFileHeader($file) {

    fseek($file, 0);
    $header = fgets($file);
    // El salto de línea del header se contabiliza como un espacio en blanco.
    $header = rtrim($header);
    return explode(CSV_COLUMN_DELIMITER, $header);
}

/**
 * Comprueba si existe el archivo de resultados.
 * @param string $savingName
 * @return boolean true|false 
 */
function existsResultsFile($savingName) {
    return file_exists(RESULTS_DIR_PATH . DIRECTORY_SEPARATOR . $savingName . "." . TEXT_RESULTS_EXTENSION);
}

/**
 * Abre el archivo de resultados y devuelve un puntero al mismo.
 * @param string $fileName
 * @param string $mode Modo de apertura
 * @return resource
 * @throws Exception No se pudo abrir el archivo de resultados
 */
function openResultsFile($fileName, $mode = 'r') {
    $filePath = RESULTS_DIR_PATH . DIRECTORY_SEPARATOR . $fileName . "." . TEXT_RESULTS_EXTENSION;
    $file = @fopen($filePath, $mode);
    if (!$file)
        throw new Exception("No se pudo abrir el archivo de resultados del cuestionario $fileName", RESULTS_TEXT_FILE_ERROR);
    return $file;
}

/**
 * Convierte un array en una cadena.
 * @param array $results
 * @param string $resultsString
 * @return string 
 */
function resultsArrayToString($results, $resultsString = "") {

    foreach ($results as $field => $value) {
        if (is_array($value))
            $resultsString.="$field=>" . PHP_EOL . resultsArrayToString($value);
        else
            $resultsString.="$field=>$value" . CSV_COLUMN_DELIMITER . PHP_EOL;
    }

    return $resultsString;
}

/**
 * Envía los resultados a una dirección de correo electrónico.
 * @param array $results
 * @throws Exception 
 */
function sendResultsToMail($results) {
    if (!MAIL_SENDING)
        return False;
    $mailSended = mail(MAIL_RECEIVER, MAIL_SUBJECT, resultsArrayToString($results));
    if (!$mailSended)
        throw new Exception("El correo no pudo ser enviado a " . EMAIL, MAIL_SEND_ERROR);
}
?>
