<?php
/**
 * Un conjunto de campos de una tabla de la base de datos.
 * 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://jharo.net
 * @author Juan Haro <juanharo@gmail.com>
 */

class fieldSet{
    
    /**
     * Campos del conjunto.
     * Es un array formado por objetos field y donde cada fila está identificada
     * con el nombre del campo.
     * array(
     *      nombre_campo => objeto field
     * )
     * @var array
     */
    private $fields = array();
    
    public function __construct() {
    }
    
    /**
     * Añade un nuevo campo al conjunto. El identificador del campo (clave en
     * el array) será su nombre.
     * @param \field $field
     */
    public function addField(field $field){
        $this->fields[$field->getName()]=$field;
    }
    
    /**
     * Recupera el campo que coincide con el nombre pasado como parámetro.
     * @param string $fieldName
     */
    public function getField($fieldName){
        if(array_key_exists($fieldName, $this->fields))
            return $this->fields[$fieldName];
    }
    
    /**
     * Devuelve el array de objetos field.
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Devuelve un array con los nombres de los campos.
     * @return array
     */
    public function getFieldNames(){
        return array_keys($this->fields);
    }
    
    /**
     * Devuelve una cadena con los campos separados por comas. Se utiliza para
     * realizar consultas.
     * 
     * Uso: 
     * $fieldSet = new fieldSet();
     * echo $fieldSet; // "field1,field2,field3"
     * 
     * @return string
     */
    public function __toString() {
        $fieldNames = $this->getFieldNames();
        return implode(",", $fieldNames);
    }
    
}
?>
