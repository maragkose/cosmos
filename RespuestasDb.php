<?php

require_once("includes/objoo_trunk/include.php");

/**
 * Clase que carga las opciones de respuesta de un ítem almacenadas en una
 * base de datos.
 * 
 * @package Respuestas
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class RespuestasDb extends Respuestas {
    /**
     * Datos de configuración de la base de datos de respuestas. 
     * Nombre del campo que contiene el texto de la opción de respuesta.
     * Nombre del campo que contiene el identificador de la respuesta.
     */

    const RESPUESTA_TEXT_FIELD_DB = "texto";
    const RESPUESTA_ID_FIELD_DB = "id";

    /**
     * Comprueba el sistema de base de datos seleccionado (MySQL o MongoDB) y 
     * recupera las opciones de respuesta de un ítem de la base de datos.
     * @param array $etiquetasData
     * @return type 
     */
    public function __construct($itemId) {
        try {
            switch (DATABASE_SYSTEM):
                case MYSQL:
                    $this->getRespuestasFromMySQL($itemId);
                    break;
                case MONGODB:
                    $this->getRespuestasFromMongoDB($itemId);
                    break;
                case NULL:
                    throw new Exception("Las opciones de respuesta del ítem $itemId no pueden ser recuperadas. No se ha seleccionado ningún sistema de base de datos.");
            endswitch;
        } catch (dbException $e) {
            errorRegister($e->getMsg());
            sendErrorToMail($e->getMsg());
            die();
        } catch (MongoConnectionException $e) {
            errorRegister($e->getMessage());
            sendErrorToMail($e->getMessage());
            die();
        }
    }

    /**
     * Recupera las opciones de respuesta de un ítem almacenadas en una collection de la base de datos MongoDB y las
     * añade al array de respuestas.
     */
    public function getRespuestasFromMongoDB($collectionName) {
        $m = new Mongo();
        $db = $m->selectDb(MONGODB_RESPUESTAS_DB_NAME);
        $dbCollection = $db->selectCollection($collectionName);
        if(!$dbCollection->find()->count())
            throw new MongoConnectionException("La collection $collectionName no contiene opciones de respuesta");
            
        foreach ($dbCollection->find() as $resultado) {
            $textoRespuesta = $resultado[self::RESPUESTA_TEXT_FIELD_DB];
            $idRespuesta = $resultado[self::RESPUESTA_ID_FIELD_DB];
            $this->addRespuesta($textoRespuesta, $idRespuesta);
        }
    }

    /**
     * Recupera las opciones de respuesta de un ítem almacenadas en una tabla de la base de datos MySQL y las
     * añade al array de respuestas.
     * @return array 
     */
    public function getRespuestasFromMySQL($tableName) {
        sql::getInstance(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_RESPUESTAS_DB_NAME);
        $respuestas = dbTable::get($tableName)->selectAll();
        foreach($respuestas->getRows() as $respuesta){
            $this->addRespuesta($respuesta->data[self::RESPUESTA_TEXT_FIELD_DB],$respuesta->data[self::RESPUESTA_ID_FIELD_DB]);
        }
    }

}

?>
