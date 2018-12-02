<?php

/**
 * Métodos para la validación de campos. Comprueba si el valor asignado a 
 * un campo se ajusta a las restricciones de tipo de columna y longitud.
 * 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://jharo.net
 * @author Juan Haro <juanharo@gmail.com>
 */
include_once 'fieldException.php';

class fieldValidate {

    /**
     * Tipos de datos numéricos de MySQL.
     * @var array
     */
    public static $numericFields = array(
        "tinyint", "int", "smallint", "mediumint", "bigint", "decimal", "float", "double", "numeric", "integer"
    );

    /**
     * Tipos de datos de cadena de caracteres de MySQL.
     * @var array
     */
    public static $textFields = array(
        "char", "varchar", "binary", "varbinary", "blob", "text", "enum", "set", "tinytext", "mediumtext", "longtext"
    );

    /**
     * Tipos de datos de fecha y hora de MySQL.
     * @var array
     */
    public static $timeFields = array(
        "date", "datetime", "timestamp", "year", "time"
    );

    /**
     * Comprueba si el valor del campo se ajusta al tipo de datos permitido y
     * si no excede la longitud máxima.
     * @param \field $field
     * @param type $fieldValue
     * @throws fieldException | El valor del campo no se ajusta al tipo permitido.
     */
    static function validateField(field $field, $fieldValue) {
        switch ($field->getType()) {
            case in_array($field->getType(), self::$numericFields):
                if (!is_numeric($fieldValue))
                    throw new fieldException("El valor del campo debe ser numérico.", $field);
                break;
            case in_array($field->getType(), self::$textFields):
                if (!is_string($fieldValue))
                    throw new fieldException("El valor del campo debe ser de tipo cadena.", $field);
                break;
            case in_array($field->getType(), self::$timeFields):
                self::validateTimeField($fieldValue);
                break;
        }
        self::validateLength($field, $fieldValue);
    }

    static function validateTimeField($fieldValue) {
        
    }

    /**
     * Valida, campo a campo, un conjunto de campos. Comprueba campos sin valor,
     * si el campo identificador ha sido especificado y si el valor de los campos
     * se ajusta a la longitud y tipo permitido.
     * 
     * @param fieldSet $fieldSet
     * @param array $tableRowData
     * @throws fieldException
     */
    static function validateFieldSet(fieldSet $fieldSet, $tableRowData) {

        foreach ($fieldSet->getFields() as $field) {
            if ($field->getNull() == False) {
                if ($field->getIsPrimaryKey() == True)
                    continue;
                elseif (!key_exists($field->getName(), $tableRowData))
                    throw new fieldException("El campo debe contener un valor.", $field);
            }
            if (key_exists($field->getName(), $tableRowData) && !empty($tableRowData[$field->getName()])) {
                self::validateField($field, $tableRowData[$field->getName()]);
            }
            if ($field->getIsPrimaryKey() == True) {
                if ($field->getExtra() == field::SQL_AUTO_INCREMENT_VALUE)
                    continue;
                elseif (!key_exists($field->getName(), $tableRowData))
                    throw new fieldException("No se ha especificado el valor del campo identificador.", $field);
            }
        }
    }
    
    /**
     * Comprueba si el valor del campo excede la longitud permitida.
     * @param field $field
     * @param type $fieldValue
     * @throws fieldException | Longitud mayor que la permitida.
     */
    static function validateLength(field $field, $fieldValue) {
        if (strlen($fieldValue) > $field->getLength())
            errorRegister("El valor del campo {$field->getName()} tiene una longitud mayor que la permitida ({$field->getLength()}). Puedes modificar el valor por defecto de los campos creados automáticamente en /includes/objoo_trunk/field.php o bien modificar la longitud del campo en la base de datos.");
    }

}

?>
