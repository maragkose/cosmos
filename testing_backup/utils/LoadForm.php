<?php
/**
 * Clase que facilita métodos para la carga de un cuestionario. 
 * @package utils
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class loadForm {
    
    /**
     * El nombre del archivo de cuestionario.
     * @var string 
     */
    protected $fileName;
    /**
     * Todo el contenido del cuestionario
     * @var array 
     */
    protected $formData;
    /**
     * El cuestionario cargado
     * @var Form 
     */
    protected $form;
    
    /**
     * Carga todo el contenido del cuestionario en un array y crea un objeto
     * Form con la información de la cabecera del cuestionario (estado de 
     * publicación, mensaje de agradecimiento, etc.).
     * @param type $fileName 
     */
    public function __construct($fileName) {
        $this->setFileName($fileName);
        $this->loadFormData();
    }
    
    /**
     * Carga todo el contenido del archivo de cuestionario mediante la librería
     * YAML en la variable $formData.
     * @throws Exception El cuestionario no existe o el usuario no tiene permisos de lectura.
     */
    public function loadFormData() {
        $fullFormPath = self::getFullFormPath($this->fileName);
        if (!file_exists($fullFormPath))
            throw new Exception("El cuestionario {$this->fileName} no existe.", FORM_NOT_FOUND);
        if (!is_readable($fullFormPath))
            throw new Exception("El cuestionario no tiene permisos de lectura", NO_READABLE_FORM_DIR);
        $this->formData = Spyc::YAMLLoad($fullFormPath);
    }
    
    /**
     * Carga en $form la información de la cabecera del cuestionario y lo devuelve
     * @return Form 
     */
    public function loadFormInfo() {
        if (!isset($this->form))
            $this->form = new FormInfoLoader($this->formData, $this->fileName);
        return $this->form;
    }
    
    /**
     * Carga el cuestionario en la variable interna $form y lo devuelve.
     * @return type 
     */
    public function loadForm() {
        $this->loadFormInfo();
        foreach ($this->formData as $itemsGroupId => $itemsGroupData) {
            $itemsGroup = new ItemsGroupLoader($itemsGroupId, $itemsGroupData);
            $this->form->addItemsGroup($itemsGroup);
        }
        return $this->form;
    }
    
    /**
     * Muestra el cuestionario generado en HTML. 
     */
    public function getHTML() {
        $form = $this->loadForm();
        include(HTML_DIR . DIRECTORY_SEPARATOR . "form.html");
    }
    
    public function getFileName() {
        return $this->fileName;
    }

    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }
    
    /**
     * Muestra al participante una página de agradecimiento por su colaboración. 
     */
    public function getThanksPage(){
        $form = $this->form;
        include(HTML_DIR . DIRECTORY_SEPARATOR . "thanks.html");
    }


    /**
     * Devuelve la ruta completa de acceso al archivo de cuestionario.
     * @param string $fileName Nombre del cuestionario
     * @return string 
     */
    static function getFullFormPath($fileName) {
        $fullYMLFilePath = FORM_DIR . DIRECTORY_SEPARATOR . $fileName . "." . DEFAULT_FILE_EXTENSION;
        return $fullYMLFilePath;
    }

    /**
     * Gestiona la carga de un cuestionario. Si el cuestionario que se desea cargar
     * está publicado, se mostrará una copia guardada en formato HTML (caché). 
     * @param string $fileName
     * @param type $forceNoCache Si la copia guardada no es accesible.
     * @return \loadFormCache|\self 
     */
    static function getInstance($fileName, $forceNoCache = FALSE) {
        $loader = new self($fileName);
        // Carga los parámetros generales del cuestionario
        $loader->loadFormInfo();
        if ($loader->form->getEstado() == Form::ESTADO_PUBLICADO && !$forceNoCache)
            return new loadFormCache($fileName);
        return $loader;
    }
    
    

}
?>
