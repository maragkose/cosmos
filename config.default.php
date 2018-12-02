<?php
/**
 * Configuración de la aplicación
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @package config
 */

##### Registro de resultados #####

// Guardar los resultados, errores y datos de acceso en archivos de texto.
define("SAVE_TEXT_FILE", True);

##### Bases de datos #####

/* Sistema de base de datos que será usado para guardar los resultados y, opcionalmente,
 * cargar las respuestas de algunos ítems. 
 * Los sistemas de bases de datos disponibles son:
 * * MySQL
 * * MongoDB
 */

//Constantes utilizadas para escoger sistema de base de datos
define("MYSQL",1);
define("MONGODB",2);

// Sistema de base de datos seleccionado. NULL desactiva el guardado en base de datos.
define("DATABASE_SYSTEM",NULL);

### Configuración MySQL ###

define("MYSQL_HOST","");
define("MYSQL_USER","");
define("MYSQL_PASS","");

// Base de datos MySQL donde se guardan las opciones de respuesta. 

define("MYSQL_RESPUESTAS_DB_NAME", "testmaker_respuestas");

// Base de datos MySQL donde se guardarán los resultados. 

define("MYSQL_RESULTS_DB", "testmaker");

### Configuración MongoDB ###

// Base de datos MongoDB donde se guardan las opciones de respuesta. 

define("MONGODB_RESPUESTAS_DB_NAME", "testmaker_respuestas");

// Base de datos MongoDB donde se guardarán los resultados. 

define("MONGO_RESULTS_DB", "testmaker");

##### Configuración de directorios, archivos y extensiones. #####

// Directorio donde se encuentran las plantillas html para generar 
// la vista de los cuestionarios. 

define("HTML_DIR", "html");

// Directorio donde se encuentran los archivos de cuestionario. 

define("FORM_DIR", "cuestionarios");

// Directorio donde se encuentran las imágenes de los cuestionarios

define("IMAGE_DIR", "imagenes");

// Directorio donde se guardan los ficheros de acceso a los cuestionarios y
// los ficheros de resultados. 

define("RESULTS_DIR_PATH", "resultados");

// Directorio con los cuestionarios almacenados en html. 

define("CACHE_DIR", "cache");

// Extensión por defecto de los archivos de cuestionario. 

define("DEFAULT_FILE_EXTENSION", "yml");

// Se utiliza para delimitar registros en archivos csv.

define("CSV_COLUMN_DELIMITER", "#");


// Extensión utilizada para el archivo de texto de resultados. 

define("TEXT_RESULTS_EXTENSION", "csv");

// Extensión utilizada para el archivo de texto de resultados en bruto.

define("RAW_TEXT_RESULTS_EXTENSION", "raw");


// Nombre del archivo de registro de errores

define("ERROR_LOG_FILENAME","log");

##### Configuración para la notificación de errores y resultados vía mail. #####
// Servicio activo (TRUE o FALSE).

define("MAIL_SENDING", False);

// Destinatario de correo.

define("MAIL_RECEIVER", "");

// Asunto del correo 

define("MAIL_SUBJECT", "Resultados del cuestionario");

##### Códigos de excepción y reporte de errores #####

// Especifica qué errores serán mostrados 
define("ERROR_REPORTING_LEVEL",0);

define("FORM_NOT_FOUND", 1);
define("CACHE_CREATION_FAIL", 2);
define("RESULTS_TEXT_FILE_ERROR", 3);
define("MAIL_SEND_ERROR", 4);
define("NO_WRITABLE_RESULTS_DIR", 5);
define("NO_WRITABLE_CACHE_DIR", 6);
define("NO_READABLE_FORM_DIR", 7);

##### Otros #####

define("ESTADO_DESARROLLO", "devel");

// Nombre de la variable $_GET donde se especifica el nombre del cuestionario
define("CUESTIONARIO_GET_VAR", "cuestionario");

// Textos utilizados en los botones y mensajes de notificación del cuestionario
define("TXT_SEND_FORM","Enviar el cuestionario");
define("TXT_START_FORM","Iniciar el cuestionario");
define("TXT_DEFAULT_SELECT_OPTION","Seleccionar");
define("TXT_DEFAULT_NEXT_PAGE","Pasar a la siguiente página");
define("TXT_VALIDATION_ERRORS","Todos los campos son obligatorios. Comprueba que has respondido a todos.");

// Número de caracteres del texto del ítem utilizados para generar el 
// identificador (se aplica si el atributo texto_ids del conjunto es 's'). 
// Atención: el límite de caracteres para el nombre de una variable enviada mediante POST es 70.
// El siguiente valor sólo limita la extensión del identificador del ítem, sin
// tener en cuenta el número de caracteres del identificador de conjunto, subconjuntos,
// ítems adjuntos, etc.
define("TEXT_ID_CHAR_LIMIT", 30);
?>
