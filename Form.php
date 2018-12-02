<?php
// Archivo de configuración 
include_once "config.php";

// Activa o desactiva el informe de errores. El valor de ERROR_REPORTING_LEVEL se encuentra en config.php
error_reporting(ERROR_REPORTING_LEVEL);

include_once "Item.php";
include_once "ItemsGroup.php";
include_once "ItemLoader.php";
include_once "Etiquetas.php";
include_once "EtiquetasDefault.php";
include_once "Respuestas.php";
include_once "RespuestasDb.php";
include_once "RespuestasDefault.php";
include_once "ItemExtendedLoader.php";
include_once "ItemsGroupLoader.php";
include_once "ItemsSubGroupLoader.php";
include_once "FormInfoLoader.php";
include_once "includes/spyc.php";
include_once "utils/UserInfo.php";
include_once "utils/generateItemCodes.php";



/**
 * La clase principal. Incluye todas las clases necesarias para la creación 
 * de un cuestionario. Al crear una clase nueva es necesario hacer referencia
 * a ella desde esta clase. 
 * 
 * @package Form
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @todo Por corregir. Algunos métodos heredados no siguen la estructura de inputs
 * de las clases padre, lo cual provoca errores de tipo STRICT. 
 */

class Form {

    const DEFAULT_TITLE = "Cuestionario";
    const DEFAULT_THANKS = "Gracias por colaborar";
    const DEFAULT_ESTADO = self::ESTADO_DESARROLLO;
    const DEFAULT_DESCRIPTION = "";
    const DEFAULT_STYLE_SHEET = "styles.css";

    /**
     *  Se muestra una página de introducción antes de iniciar el cuestionario.
     *  En ella se incluyen el título y descripción del cuestionario.
     */
    const DEFAULT_INTRO = False;
    /**
     * Estado en el que se encuentra el cuestionario.
     * Se utiliza para generar una copia guardada en html del cuestionario
     * generado que será la que se mostrará al participante, aligerando así la carga.
     *      - DESARROLLO: en desarrollo.
     *      - PUBLICADO: versión final.  
     */
    const ESTADO_DESARROLLO = "desarrollo";
    const ESTADO_PUBLICADO = "publicado";

    /**
     * Identificador por el que se comienzan a numerar los conjuntos. 
     */
    const ID_INICIO = 1;

    protected $itemsGroups;
    protected $id;
    protected $title = self::DEFAULT_TITLE;
    protected $description = self::DEFAULT_DESCRIPTION;

    /**
     * Mensaje de agradecimiento al participante.
     * @var string 
     */
    protected $thanks = self::DEFAULT_THANKS;
    protected $estado = self::DEFAULT_ESTADO;
    protected $intro = self::DEFAULT_INTRO;
    /**
     * Hoja de estilos que utiliza el cuestionario.
     * @var string 
     */
    protected $styleSheet = self::DEFAULT_STYLE_SHEET;
    
    /**
     * Textos utilizados en los mensajes y botones.
     * El valor predeterminado de estos textos se encuentra en config.php. 
     * Pueden ser especificados en el archivo de cuestionario.
     */
    protected $startTxt = TXT_START_FORM;
    protected $sendTxt = TXT_SEND_FORM;
    protected $selectTxt = TXT_DEFAULT_SELECT_OPTION;
    protected $nextPageTxt = TXT_DEFAULT_NEXT_PAGE;
    protected $validationErrorsTxt = TXT_VALIDATION_ERRORS;

    /**
     * Crea un nuevo cuestionario.
     * Requiere un identificador.
     * @param string $id
     */
    function __construct($id) {
        $this->setId($id);
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getItemsGroups() {
        if ($this->itemsGroups)
            return $this->itemsGroups;
        die("No se ha definido ningún conjunto de ítems");
    }

    public function addItemsGroup(ItemsGroup $itemsGroup) {
        // Si es el primer conjunto le asigna el primer id (ID_INICIO).
        // Se sustituye el valor de inicio predeterminado en la inserción
        // de elementos en un array (0)
        if (!$this->itemsGroups)
            $this->itemsGroups[self::ID_INICIO] = $itemsGroup;
        else
            $this->itemsGroups[] = $itemsGroup;
    }

    /**
     * Devuelve uno de los conjuntos de ítems del cuestionario.
     * Opcionalmente pemite eliminarlo del conjunto de grupos.
     * @param string|integer $itemsGroupId
     * @param boolean $remove  Eliminar el grupo de items del formulario
     * @return ItemsGroup $itemsGroup 
     */
    public function getItemsGroupById($itemsGroupId, $remove = FALSE) {

        foreach ($this->getItemsGroups() as $itemsGroup) {
            if ($itemsGroup->getId() === $itemsGroupId)
                break;
        }
        if ($remove)
            $this->removeItemsGroupById($itemsGroupId);
        return $itemsGroup;
    }

    /**
     * Elimina un grupo de ítems del conjunto y reinicia su numeración.
     * @param string|integer $itemsGroupId
     */
    public function removeItemsGroupById($itemsGroupId) {

        foreach ($this->getItemsGroups() as $arrayKey => $itemsGroup) {
            if ($itemsGroup->getId() === $itemsGroupId) {
                unset($this->itemsGroups[$arrayKey]);
                $this->resetKeysItemsGroups();
            }
        }
    }

    /**
     * Reinicia la numeración de identificadores en el conjunto de grupos.
     * La numeración de identificadores se utiliza para mostrar de forma
     * ordenada los conjuntos de ítems en la vista, tal como aparecen 
     * ordenados en el archivo de texto o en el orden que han sido creados.
     */
    public function resetKeysItemsGroups() {
        $this->itemsGroups = array_values($this->itemsGroups);
    }
    
    /**
     * Devuelve el número de páginas del cuestionario.
     * Por defecto el valor es 1.
     * @return integer
     */
    public function getNumPages(){
        $numPages = 1;
        foreach($this->getItemsGroups() as $itemsGroup){
            $numPages = ($itemsGroup->getNewPage()) ? $numPages+1 : $numPages;
        }
        return $numPages;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getThanks() {
        return $this->thanks;
    }

    public function setThanks($thanks) {
        $this->thanks = $thanks;
    }

    public function getIntro() {
        return $this->intro;
    }

    public function setIntro($intro) {
        $this->intro = $intro;
    }
    
    public function getStyleSheet() {
        return $this->styleSheet;
    }

    public function setStyleSheet($styleSheet) {
        $this->styleSheet = $styleSheet;
    }
    
    public function getStartTxt() {
        return $this->startTxt;
    }

    public function setStartTxt($startTxt) {
        $this->startTxt = $startTxt;
    }

    public function getSendTxt() {
        return $this->sendTxt;
    }

    public function setSendTxt($sendTxt) {
        $this->sendTxt = $sendTxt;
    }

    public function getSelectTxt() {
        return $this->selectTxt;
    }

    public function setSelectTxt($selectTxt) {
        $this->selectTxt = $selectTxt;
    }

    public function getValidationErrorsTxt() {
        return $this->validationErrorsTxt;
    }

    public function setValidationErrorsTxt($validationErrorsTxt) {
        $this->validationErrorsTxt = $validationErrorsTxt;
    }
    
    public function getNextPageTxt() {
        return $this->nextPageTxt;
    }

    public function setNextPageTxt($nextPageTxt) {
        $this->nextPageTxt = $nextPageTxt;
    }





}

?>
