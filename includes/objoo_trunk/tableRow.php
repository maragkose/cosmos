<?php

/**
 * Un registro de la tabla.
 * 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://jharo.net
 * @author Juan Haro <juanharo@gmail.com>
 */

include_once 'fieldValidate.php';

class tableRow {

    /**
     * Array de campos y valores de la tabla.
     * @var array 
     */
    var $data;

    /**
     * Nombre de la tabla de la que ha sido devuelto el registro. Se utiliza
     * para tener acceso a los métodos de la clase dbTable sin necesidad de 
     * cargar un nuevo objeto.
     * @var string 
     */
    var $tableName;

    
    function __construct($tableName, $data = "") {
        $this->setData($data);
        $this->setTableName($tableName);
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    /**
     * Permite tener acceso a los campos del array $this->data como si 
     * fueran variables del objeto. Busca entre los campos del array un
     * identficador que coincida con el nombre del campo recibido.
     * 
     * Uso: $tableRow->campo;
     *      
     * @param string $field
     * @return type
     */
    function __get($field) {
        if (key_exists($field, $this->getData()))
            return $this->data[$field];
        if($field=="id"){
            return $this->getIdValue();
        }
    }
    
    function getIdValue(){
        $idFieldName = dbTable::get($this->tableName)->getId()->getName();
        return $this->data[$idFieldName];
    }

    /**
     * Añade valor a un campo o modifica el valor de un campo existente. 
     * Como en __get(), los campos pueden ser recuperados como si fueran
     * variables del objeto.
     * 
     * Uso: $tableRow->campo = "valor";
     * 
     * @param type $field
     * @param type $value
     */
    function __set($fieldName, $fieldValue) {
        $this->data[$fieldName] = $fieldValue;
    }

    /**
     * Actualiza el registro con las nuevas modificaciones. Envía el array
     * de campos interno para efectuar la actualización. Antes de actualizar
     * valida cada uno de los campos del registro.
     * 
     * @return type
     */
    function save() {
        $fieldSet = dbTable::get($this->tableName)->getFields();
        fieldValidate::validateFieldSet($fieldSet, $this->data);
        return dbTable::get($this->tableName)->update($this->data);
    }

    /**
     * Elimina el registro.
     * @return type
     */
    function delete() {
        return dbTable::get($this->tableName)->delete($this->data);
    }

    /**
     * Añade un nuevo registro tras haber especificado el valor de sus campos.
     * Valida los campos antes de añadir el registro en la base de datos.
     * @return type
     */
    function add() {
        $fieldSet = dbTable::get($this->tableName)->getFields();
        fieldValidate::validateFieldSet($fieldSet, $this->data);
        return dbTable::get($this->tableName)->insert($this->data);
    }

}

?>
