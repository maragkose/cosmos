<?php

/**
 * Las opciones respuestas de un ítem.
 * @package Respuestas 
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

class Respuestas {
    
    /**
     * Identificador por el que se comienzan a numerar las respuestas. 
     */
    const ID_INICIO = 1;

    /**
     * La key inicial por defecto del array de respuestas extraido del archivo.
     */
    const DEFAULT_FILE_INIT_NUM = 0;

    /**
     * Campos de valores mínimo y máximo para respuestas numéricas. 
     */
    const RESPUESTAS_MIN_FIELD = "min";
    const RESPUESTAS_MAX_FIELD = "max";

    /**
     *  Valor en el campo de opciones de respuesta utilizado para que
     * las respuestas de ese ítem sean cargadas desde la base de datos.
     */
    const DB_RESPUESTAS_VALUE = "bd";
    
    /**
     * Delimitador, que se sitúa a continuación de DB_RESPUESTAS_VALUE, a partir
     * del cual se especifica el nombre de la tabla donde se encuentran las
     * opciones de respuesta del ítem.
     */
    const DB_RESPUESTAS_DELIMITER = ".";

    protected $respuestas;

    /**
     * Indica si los ids del array de respuestas han sido especificados.
     * @var boolean 
     */
    protected $idDefined = True;

    /**
     * Crea un nuevo objeto de respuestas estándar.
     * @param array $respuestasData 
     */
    private function __construct($respuestasData = "") {
        $this->setRespuestas($respuestasData);
    }

    public function getRespuestas() {
        return $this->respuestas;
    }

    /**
     * Añade una respuesta y gestiona la inserción de respuestas sin id.
     * @param type $value
     * @param type $id 
     */
    public function addRespuesta($value, $id = NULL) {

        if ($id === NULL)
            $this->addRespuestaWithoutId($value);
        else
            $this->respuestas[$id] = $value;
    }

    /**
     * Las respuestas sin identificador utilizan el texto de la respuesta como
     * identificador. Es necesario adaptar el texto para evitar problemas en
     * la plantilla html.
     * @param string $respuestaValue. 
     */
    private function addRespuestaWithoutId($respuestaValue) {
        $respuestaId = $this->respuestaValueToId($respuestaValue);
        $this->respuestas[$respuestaId] = $respuestaValue;
    }

    /**
     * Adapta el texto de una respuesta para que pueda ser utilizado como id.
     * @param string $respuestaValue
     * @return string 
     */
    private function respuestaValueToId($respuestaValue) {
        $respuestaValue = str_ireplace(" ", "_", $respuestaValue);
        $respuestaValue = str_ireplace("-", "", $respuestaValue);
        return $respuestaValue;
    }

    /**
     * Devuelve el texto de las respuestas. 
     * @return array 
     */
    public function getValues() {
        return array_values($this->respuestas);
    }

    /**
     * Deveulve los identificadores de las respuestas. 
     * @return array
     */
    public function getIds() {
        return array_keys($this->respuestas);
    }

    /**
     * Se implementa en la subclase RespuestasDefault. 
     * @return type 
     */
    public function getRespuestaMinValue() {
        return;
    }

    /**
     * Se implementa en la subclase RespuestasDefault.
     * @return type 
     */
    public function getRespuestaMaxValue() {
        return;
    }

    /**
     * Permite pasar un array de respuestas.
     * Además comprueba si los identificadores vienen por defecto o han sido
     * especificados en el documento. 
     * Si vienen por defecto, los actualiza siguiendo una numeración que se 
     * inicia con ID_INICIO.
     * @param array $respuestas 
     */
    public function setRespuestas(array $respuestas) {

        // Si el primer identificador es 0, se trata de un array sin
        // identificadores especificados y se debe readaptar.
        reset($respuestas);
        if (key($respuestas) === self::DEFAULT_FILE_INIT_NUM) {
            // Se cambia el estado de setIdDefined a False para así indicar
            // que se trata de un array de respuestas con ids por defecto.
            $this->setIdDefined(False);
            foreach ($respuestas as $respuestaValue)
                $this->addRespuestaWithoutId($respuestaValue);
        }
        else
            $this->respuestas = $respuestas;
    }

    /**
     * Indica si los ids del array de respuestas han sido especificados.
     * @param boolean $defined 
     */
    public function setIdDefined($defined) {
        $this->idDefined = $defined;
    }

    /**
     * Crea un nuevo objeto de respuestas.
     * Si $respuestasData es un array en el que se especifican los valores mínimo
     * y máximo entre los cuales generar una secuencia númerica, entonces se crea
     * un objeto RespuestasDefault.
     * Si el primer valor del array $respuestasData es el valor por defecto 
     * DB_RESPUESTAS_VALUE, entonces se crea un objeto de carga de respuestas
     * mediante base de datos.
     * En cualquier otro caso crea un objeto etiquetas estándar.
     * @param array $respuestasData 
     */
    static function getInstance($respuestasData = "") {
        if (array_key_exists(self::RESPUESTAS_MIN_FIELD, $respuestasData) &&
                array_key_exists(self::RESPUESTAS_MAX_FIELD, $respuestasData))
            return new RespuestasDefault($respuestasData);
        else if (array_key_exists(0, $respuestasData) && strpos($respuestasData[0],self::DB_RESPUESTAS_VALUE) !== False){
            $tableName = explode(self::DB_RESPUESTAS_DELIMITER, $respuestasData[0]);
            return new RespuestasDb($tableName[1]);
        }
        else
            return new self($respuestasData);
    }

}

?>
