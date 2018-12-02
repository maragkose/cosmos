<?php
/**
 * Clase que crea etiquetas por defecto (numeradas y/o con valores
 * mínimo y máximo) a partir de la información recibida de un archivo de cuestionario.
 * 
 * @package Etiquetas
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

class EtiquetasDefault extends Etiquetas {
    
    /**
     * Etiquetas de los atributos mínimo (primera) y máximo (última) en 
     * el archivo de cuestionario.
     */
    const ETIQUETA_MIN_FIELD = "min";
    const ETIQUETA_MAX_FIELD = "max";

    protected $etiquetasData;

    /**
     * Dependiendo de la información del array generará etiquetas con valores
     * por defecto, o con valores por defecto y etiquetas de mínimo y máximo
     * personalizadas.
     * @param array $etiquetasData
     * @return type 
     */
    public function __construct(array $etiquetasData) {

        $this->setEtiquetasData($etiquetasData);
        if ($this->getEtiquetaMaxLabel() && $this->getEtiquetaMinLabel())
            return $this->setEtiquetasWithMinAndMax();
        else
            return $this->setEtiquetasDefault();
    }

    public function getEtiquetasData() {
        return $this->etiquetasData;
    }

    public function setEtiquetasData($etiquetasData) {
        $this->etiquetasData = $etiquetasData;
    }

    public function getNumEtiquetas() {
        return $this->etiquetasData[self::NUM_ETIQUETAS_FIELD];
    }

    public function getEtiquetaMinLabel() {
        if (array_key_exists(self::ETIQUETA_MIN_FIELD, $this->getEtiquetasData()))
            return $this->etiquetasData[self::ETIQUETA_MIN_FIELD];
    }

    public function getEtiquetaMaxLabel() {
        if (array_key_exists(self::ETIQUETA_MAX_FIELD, $this->getEtiquetasData()))
            return $this->etiquetasData[self::ETIQUETA_MAX_FIELD];
    }

    /**
     * Genera etiquetas en las que el id y el valor son iguales. Genera tantas
     * como se especifique en el campo NUM_ETIQUETAS_FIELD del archivo
     */
    function setEtiquetasDefault() {

        for ($n = self::ID_INICIO; $n <= $this->getNumEtiquetas(); $n++)
            $this->addEtiqueta($n);
    }

    /**
     * Crea etiquetas con id's sucesivas a partir de 1 y con un valor para el
     * el primer y el último id especificado en los valores min y max 
     * del archivo.
     */
    public function setEtiquetasWithMinAndMax() {

        for ($n = self::ID_INICIO; $n <= $this->getNumEtiquetas(); $n++) {
            // Primera etiqueta
            if ($n == self::ID_INICIO) {
                $this->addEtiqueta($this->getEtiquetaMinLabel(), $n);
                continue;
            }
            // Última etiqueta
            if ($n == $this->getNumEtiquetas()) {
                $this->addEtiqueta($this->getEtiquetaMaxLabel(), $n);
                continue;
            }
            $this->addEtiqueta("", $n);
        }
    }

}

?>
