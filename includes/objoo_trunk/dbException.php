<?php
class dbException extends Exception{
    
    var $function;
    var $msg;

    function __construct($function,$msg) {
        $this->function = $function;
        $this->msg = $msg;
    }

    function show(){
        printf("Error en [%s] con mensaje '%s'",$this->function,  $this->msg);
    }
    
    function getMsg(){
        return "Error en {$this->function} con mensaje '{$this->msg}'";
    }

}
?>
