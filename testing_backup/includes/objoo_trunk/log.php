<?php

class log {

    private static $log_data;

    static function writeLog($action, $msg) {
        $action.=sizeof(self::$log_data)+1;
        self::$log_data[$action] = $msg;
    }
    
    static function showLog(){
        echo "</br>Registro de acciones:</br>";
        foreach (self::$log_data as $action => $msg) {
            echo "[$action]: ".$msg."</br>";
        }
    }

}

?>
