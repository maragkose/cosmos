<?php

/**
 * Clase que permite obtener información de las cabeceras HTTP.
 * @package utils
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

class UserInfo {

    static function getServerData() {
        return $_SERVER;
    }

    static function getIp() {
        return $_SERVER["REMOTE_ADDR"];
    }

    static function getRequestTime() {
        return time();
    }

    static function getLanguage() {
        return $_SERVER["HTTP_ACCEPT_LANGUAGE"];
    }

    static function getUserAgent() {
        return $_SERVER["HTTP_USER_AGENT"];
    }

}

?>
