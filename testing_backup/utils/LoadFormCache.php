<?php

/**
 * Clase que facilita métodos para la carga de una copia guardada del cuestionario en HTML. 
 * @package utils
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class loadFormCache extends loadForm {

    public function __construct($fileName) {
        $this->setFileName($fileName);
    }

    /**
     * Muestra la copia en HTML del cuestionario generado.
     * @return type 
     */
    public function getHTML() {
        if (!$this->existsCache())
            return $this->newCache();
        include CACHE_DIR . DIRECTORY_SEPARATOR . $this->fileName . ".html";
    }

    /**
     * Crea un archivo HTML con el contenido del cuestionario generado.
     * @throws Exception No pudo ser creado
     */
    public function newCache() {
        if (!SAVE_TEXT_FILE) {
            return False;
        }
        $fp = @fopen(CACHE_DIR . DIRECTORY_SEPARATOR . $this->fileName . ".html", "ab+");
        if (!$fp)
            throw new Exception("La caché no pudo ser creada", CACHE_CREATION_FAIL);

        ob_start();

        $this->loadFormData();
        parent::getHTML();

        fwrite($fp, ob_get_contents());
        fclose($fp);

        ob_end_flush();
    }

    /**
     * Comprueba si existe una copia guardada en HTML del cuestionario.
     * @return type 
     */
    public function existsCache() {
        $fileCache = CACHE_DIR . DIRECTORY_SEPARATOR . $this->fileName . ".html";
        return file_exists($fileCache) && is_readable($fileCache);
    }

}

?>
