<?php
require_once("dbException.php");

class fieldException extends dbException{
    
    private $field;
    
    function __construct($msg, field $field) {
        $this->message = $msg;
        $this->field = $field;
    }
    
    function getMsg(){
        return "Error en la validación del campo '{$this->field->getName()}'. ".$this->getMessage();
    }
}

?>