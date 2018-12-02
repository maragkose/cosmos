<?php
/**
* Clase que crea las etiquetas de un conjunto de ítems.
* Permite la creación de etiquetas numeradas o con valores mínimo y máximo.
* @package Etiquetas 
* @author Juan Haro <juanharo@gmail.com>
* @link http://jharo.net/dokuwiki/testmaker
* @copyright Copyright 2012 Juan Haro
* @license http://www.opensource.org/licenses/mit-license.php MIT License
*/


class Etiquetas {
    
    /**
     * Identificador por el que se comienzan a numerar las etiquetas. 
     */
    const ID_INICIO = 1;
    /**
     * Valor de la primera clave del array de etiquetas extraido del archivo.
     */
    const DEFAULT_FILE_INIT_NUM = 0;
    /**
     * Campo en el archivo que indica el número de etiquetas a generar. 
     * Sólo es necesario para generar etiquetas por defecto. 
     */
    const NUM_ETIQUETAS_FIELD = "num";

    protected $etiquetas;
    
    
    /**
     * Crea un nuevo objeto de etiquetas estándar.
     * Si $etiquetasData es un array, lo carga directamente en la variable interna.
     * Si recibe un entero genera un array de etiquetas numeradas.
     * @param array|integer $etiquetasData 
     */
    private function __construct($etiquetasData = "") {
        
        if (is_array($etiquetasData))
            $this->setEtiquetas($etiquetasData) ;
        
        else if(is_integer($etiquetasData))
            $this->generateDefault ($etiquetasData);
    }
    
    
    public function getEtiquetas() {
        return $this->etiquetas;
    }
    
    /**
     * Añade una etiqueta y gestiona la inserción de etiquetas sin identificador.
     * @param string $value
     * @param type $id 
     */
    public function addEtiqueta($value, $id = NULL) {

        if ($id === NULL)
            $this->addEtiquetaWithoutId($value);
        else
            $this->etiquetas[$id] = $value;
    }

    
    /**
     * Numera y añade una nueva etiqueta que ha sido pasada sin identificador.
     * Continúa la numeración de la última etiqueta añadida.
     * @param string $etiquetaValue 
     */
    private function addEtiquetaWithoutId($etiquetaValue) {
        // Si es la primera etiqueta le asigna el primer id (ID_INICIO)
        if (!$this->etiquetas)
            $this->etiquetas[self::ID_INICIO] = $etiquetaValue;
        // Recupera el último identificador y le suma una unidad
        else {
            $lastId = key(array_slice($this->etiquetas, -1, 1, TRUE));
            $id = $lastId + 1;
            $this->etiquetas[$id] = $etiquetaValue;
        }
    }
    
    /**
     * Devuelve los valores de las etiquetas. (P. ej., "Nunca", "A veces", "Siempre")
     * @return array 
     */
    public function getValues(){
        return array_values($this->etiquetas);
    }
    
    /**
     * Devuelve los identificadores de las etiquetas. Los ids se utilizan
     * para puntuar cada item en una escala likert (1,2,3,4,5)
     * @return array
     */
    public function getIds(){
        return array_keys($this->etiquetas);
    }
    
    
    /**
     * Se implementa en la subclase EtiquetasDefault. Se incluye aquí para 
     * no añadir código de verificación en la vista.
     * @return type 
     */
    public function getEtiquetaMinLabel(){
        return;
    }
    
    /**
     * Se implementa en la subclase EtiquetasDefault. Se incluye aquí para 
     * no añadir código de verificación en la vista.
     * @return type 
     */
    public function getEtiquetaMaxLabel(){
        return;
    }

    /**
     * Recibe un array de etiquetas y lo carga.
     * Además comprueba si los identificadores vienen por defecto o han sido
     * especificados en el documento. 
     * Si vienen por defecto, los actualiza siguiendo una numeración que se 
     * inicia con ID_INICIO.
     * @param array $etiquetas 
     */
    public function setEtiquetas(array $etiquetas) {
        
        // Si el primer identificador es 0, se trata de un array sin
        // identificadores especificados y se debe readaptar.
        reset($etiquetas);
        if(key($etiquetas)===self::DEFAULT_FILE_INIT_NUM){
            foreach($etiquetas as $etiquetaValue)
                $this->addEtiquetaWithoutId($etiquetaValue);
        }
        else
            $this->etiquetas = $etiquetas;
        
    }
    
    /**
     * Genera etiquetas en las que el id y el valor son iguales. Genera tantas
     * como se especifique en la variable $numEtiquetas.
     * Similar al método EtiquetasDefault::setDefaultEtiquetas(), pero sin 
     * necesidad de utilizar la carga desde archivo.
     * @param integer $numEtiquetas 
     */
    public function generateDefault($numEtiquetas){
        for ($n = self::ID_INICIO; $n <= $numEtiquetas; $n++)
            $this->addEtiqueta($n);
    }
    
    /**
     * Crea un nuevo objeto de etiquetas.
     * Si $etiquetasData es un array en el que se especifica un número de 
     * etiquetas, se crea un objeto de etiquetas numeradas.
     * En cualquier otro caso crea un objeto etiquetas estándar.
     * @param array $etiquetasData 
     */
    static function getInstance($etiquetasData = ""){
        // EtiquetasDefault sólo puede instanciarse por medio de un array
        // con la información necesaria.
        if (is_array($etiquetasData) && 
                array_key_exists(self::NUM_ETIQUETAS_FIELD,$etiquetasData))
            return new EtiquetasDefault($etiquetasData);

        else 
            return new self($etiquetasData);
    }
    
    
}

?>
