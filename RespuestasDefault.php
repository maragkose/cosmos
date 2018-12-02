<?php

/**
 * Clase que crea una secuencia de opciones de respuesta numéricas comprendida entre 
 * un valor mínimo y máximo.
 * 
 * @package Respuestas
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */


class RespuestasDefault extends Respuestas {

    protected $respuestasData;

    /**
     * El constructor llama a la función principal despues de asignar el 
     * array de información recibida a una variable interna.
     * @param array $etiquetasData
     * @return type 
     */
    public function __construct(array $respuestasData) {
        $this->setRespuestasData($respuestasData);
        return $this->setRespuestasWithinMinAndMax();
    }

    public function getRespuestasData() {
        return $this->respuestasData;
    }

    public function setRespuestasData($respuestasData) {
        $this->respuestasData = $respuestasData;
    }
    
    /**
     * Devuelve el valor mínimo de la secuencia numérica.
     * @return integer
     */
    public function getRespuestaMinValue() {
        if (array_key_exists(self::RESPUESTAS_MIN_FIELD, $this->getRespuestasData())) {
            return $this->respuestasData[self::RESPUESTAS_MIN_FIELD];
        }
    }
    
    /**
     * Devuel el valor máximo de la secuencia numérica.
     * @return integer
     */
    public function getRespuestaMaxValue() {
        if (array_key_exists(self::RESPUESTAS_MAX_FIELD, $this->getRespuestasData()))
            return $this->respuestasData[self::RESPUESTAS_MAX_FIELD];
    }

    /**
     * Crea respuestas númericas con valores sucesivos desde
     * RESPUESTAS_MIN_FIELD hasta RESPUESTAS_MAX_FIELD y las añade al array
     * de respuestas.
     */
    public function setRespuestasWithinMinAndMax() {
        for ($n = $this->getRespuestaMinValue(); $n <= $this->getRespuestaMaxValue(); $n++)
            $this->addRespuesta($n, $n);
    }

}

?>
