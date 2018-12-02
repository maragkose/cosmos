<?php
/**
 * Clase que recupera los parámetros generales del cuestionario (título, estado,
 * descripción, etc.) de un archivo de texto. De este modo se puede utilizar
 * información del cuestionario (p. ej. mensaje de agradecimiento) sin necesidad
 * de cargar todo el conjunto de items. Aunque al heredar los métodos y atributos
 * de Form, también puede usarse para contener todos los items. 
 * 
 * @package Form
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

class FormInfoLoader extends Form {

    const FORM_FIELD = "quiz";
    const ID_FIELD = "id";
    const TITLE_FIELD = "title";
    const DESCRIPTION_FIELD = "description";
    const THANKS_FIELD = "thanks";
    const ESTADO_FIELD = "state";
    const INTRO_FIELD = "intro";
    const STYLE_SHEET_FIELD = "style_sheet";
    const SEND_TXT_FIELD = "txt_send";
    const START_TXT_FIELD = "txt_start";
    const SELECT_TXT_FIELD = "txt_select";
    const NEXT_PAGE_TXT_FIELD = "txt_next";
    const VALIDATION_ERRORS_TXT_FIELD = "txt_validate";

    public $formInfoData;

    /**
     * $formData es la información recuperada de la cabecera del archivo de 
     * cuestionario. $id es el nombre del cuestionario.
     * @param array $formData
     * @param string $id 
     */
    function __construct(&$formData, $id) {
        $this->setFormInfoData($formData);
        parent::setId($id);

        // Si en el archivo de cuestionario se han especificado los parámetros
        // de configuración, éstos son recuperados.

        if (isset($this->formInfoData)) {
            $this->setEstado();
            $this->setTitle();
            $this->setDescription();
            $this->setThanks();
            $this->setIntro();
            $this->setStyleSheet();
            $this->setSelectTxt();
            $this->setStartTxt();
            $this->setSendTxt();
            $this->setNextPageTxt();
            $this->setValidationErrorsTxt();
        }
    }

    /**
     * Recupera la información de la cabecera del cuestionario y la elimina del array
     * de datos donde se encuentran los grupos de ítems.
     * @param type $formInfoData
     */
    public function setFormInfoData(&$formData) {
        if (array_key_exists(self::FORM_FIELD, $formData)) {
            $this->formInfoData = $formData[self::FORM_FIELD];
            unset($formData[self::FORM_FIELD]);
        }
    }

    public function setDescription() {
        if (array_key_exists(self::DESCRIPTION_FIELD, $this->getFormData()))
            $this->description = $this->formInfoData[self::DESCRIPTION_FIELD];
    }

    public function setTitle() {
        if (array_key_exists(self::TITLE_FIELD, $this->getFormData()))
            $this->title = $this->formInfoData[self::TITLE_FIELD];
    }

    public function setThanks() {
        if (array_key_exists(self::THANKS_FIELD, $this->getFormData()))
            $this->thanks = $this->formInfoData[self::THANKS_FIELD];
    }

    public function setEstado() {
        if (array_key_exists(self::ESTADO_FIELD, $this->getFormData()))
            $this->estado = $this->formInfoData[self::ESTADO_FIELD];
    }
    
    /**
     * Asigna el nombre de la hoja de estilos seleccionada a la variable $styleSheet del cuestionario.
     * Si la hoja de estilos no se encuentra en el directorio, carga la hoja de
     * estilos predeterminada (styles.css) y registra el error en el archivo log. 
     */
    public function setStyleSheet() {
        if (array_key_exists(self::STYLE_SHEET_FIELD, $this->getFormData()))
        {
            $styleSheet = $this->formInfoData[self::STYLE_SHEET_FIELD];
            
            if(file_exists(HTML_DIR.DIRECTORY_SEPARATOR.$styleSheet))
                $this->styleSheet = $styleSheet;
            else
                errorRegister ("La hoja de estilos $styleSheet no se encuentra en el directorio ".HTML_DIR);
                
        }
        
    }
    
    public function setStartTxt() {
         if (array_key_exists(self::START_TXT_FIELD, $this->getFormData()))
            $this->startTxt = $this->formInfoData[self::START_TXT_FIELD];
    }
    
    public function setSendTxt() {
         if (array_key_exists(self::SEND_TXT_FIELD, $this->getFormData()))
            $this->sendTxt = $this->formInfoData[self::SEND_TXT_FIELD];
    }
    
    public function setSelectTxt() {
         if (array_key_exists(self::SELECT_TXT_FIELD, $this->getFormData()))
            $this->selectTxt = $this->formInfoData[self::SELECT_TXT_FIELD];
    }
    
    public function setValidationErrorsTxt() {
         if (array_key_exists(self::VALIDATION_ERRORS_TXT_FIELD, $this->getFormData()))
            $this->validationErrorsTxt = $this->formInfoData[self::VALIDATION_ERRORS_TXT_FIELD];
    }
    
    public function setNextPageTxt() {
        if (array_key_exists(self::NEXT_PAGE_TXT_FIELD, $this->getFormData()))
            $this->nextPageTxt = $this->formInfoData[self::NEXT_PAGE_TXT_FIELD];
    }

    /*
     * Valores permitidos para la etiqueta 'intro': 's'(True) y 'n'(False)
     */
    public function setIntro() {
        if (array_key_exists(self::INTRO_FIELD, $this->getFormData())) {
            if ($this->formInfoData[self::INTRO_FIELD] == "s") {
                $this->intro = True;
            } else if ($this->formInfoData[self::INTRO_FIELD] == "n") {
                $this->intro = False;
            }
        }
    }

    public function getFormData() {
        return $this->formInfoData;
    }

}

?>
