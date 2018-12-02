<?php
/**
 * Cada uno de los ítems que componen el cuestionario.
 * 
 * @todo Por corregir. Algunos métodos heredados no siguen la estructura de inputs
 * de las clases padre, lo cual provoca errores de tipo STRICT. 
 * 
 * @package Item
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
*/
class Item {
    
    /**
     * Formatos de respuesta.
     */
    const TIPO_LIKERT = "likert";
    const TIPO_ABIERTA = "abierta";
    const TIPO_ABIERTA_AMPLIA = "abierta_amplia";
    const TIPO_BINARIA = "binaria";
    const TIPO_SELECCION_UNICA = "unica";
    const TIPO_SELECCION_UNICA_DESPLEGABLE = "desplegable";
    const TIPO_SELECCION_MULTIPLE = "multiple";
    const TIPO_SELECCION_MULTIPLE_MULTILINEA = "multiple_multilinea";
    
    /**
     * Identificador del ítem adjunto.
     * Ej: "item_principal_ID_ADJUNTO"
     * @var string
     */
    const ID_ADJUNTO = "adjunto";
    
    /**
     * Se utiliza para codificar el identificador de los ítems.
     * Ej.: item__adjunto 
     */
    const ID_DELIMITER = "__";
    
    /**
     * Se utiliza en combinación con otras variables para crear identificadores
     * únicos. 
     * 
     * @var type 
     */
    protected $seed = null;
    
    protected $textoPregunta;

    /**
     * El identificador del ítem dentro del grupo. Se utiliza en combinación
     * con los identificadores de conjuntos y subconjuntos, generando un 
     * identificador único. Ejemplo: idConjunto__idItem, 
     * idConjunto__idSubConjunto__idItem .
     */
    protected $id = NULL;
    /**
     * El formato de respuesta del ítem.
     * @var string
     */
    protected $tipoRespuesta=self::TIPO_LIKERT;
    /**
     * Esta variable contiene opciones de respuesta del ítem.
     * Para ítems de selección única o múltiple. 
     * @var Respuestas
     */
    public $opcionesRespuesta = null;
    
    /**
     * Ítem que proporciona información adicional a la respuesta del ítem
     * principal.
     * @var self
     */
    protected $itemAdjunto = NULL;
    
    /**
     * Determina si es obligatorio responder al ítem.
     * Por defecto todos los ítems son de respuesta obligatoria.
     * @var boolean
     */
    protected $required = TRUE;
    

    /**
     * El constructor recibe el texto del ítem y, opcionalmente, 
     * un identificador. Si no recibe identificador, le será asignado uno
     * numérico en función del número de ítems que formen el conjunto al que
     * sea añadido.
     * @param string $textoPregunta
     * @param type $id 
     */
    function __construct($textoPregunta, $id = NULL) {
        $this->setTextoPregunta($textoPregunta);
        if ($id)
            $this->setId($id);
    }

    function getTextoPregunta() {
        return $this->textoPregunta;
    }


    function setTextoPregunta($textoPregunta) {
        if (!is_string($textoPregunta))
            die("$textoPregunta debe ser una cadena de texto");
        $this->textoPregunta = $textoPregunta;
    }
    
    /**
     * Identificador único del ítem. Utilizado para realizar búsquedas desde
     * el conjunto de ítems. Si el ítem pertenece a un subconjunto, se utiliza
     * una variable adicional para generarlo ($seed).
     * 
     * @return string
     */
    function getHash(){
        return md5($this->getTextoPregunta().$this->getId().$this->getTipoRespuesta().$this->getSeed());
    }

    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function getTipoRespuesta() {
        return $this->tipoRespuesta;
    }

    function setTipoRespuesta($tipoRespuesta) {
        $this->tipoRespuesta = $tipoRespuesta;
    }
    
    function getOpcionesRespuesta(){
        return $this->opcionesRespuesta->getRespuestas();
    }
    
    /**
     * Carga las opciones de respuesta del ítem. 
     * @param \Respuestas | array $respuestasPosibles
     */
    function setOpcionesRespuesta($opcionesRespuesta){
         if ($opcionesRespuesta instanceof Respuestas)
            $this->opcionesRespuesta = $opcionesRespuesta;
        else
            $this->opcionesRespuesta = Respuestas::getInstance($opcionesRespuesta);
    }
    
    public function getItemAdjunto() {
        return $this->itemAdjunto;
    }
    
    /**
     * Asigna un objeto item como adjunto al ítem principal. Su identificador
     * es generado a partir de la combinación del identificador del ítem principal 
     * y una palabra clave que indica que es un ítem adjunto (itemId__adjunto).
     * @param Item $itemAdjunto
     */
    public function setItemAdjunto(Item $itemAdjunto) {
        $this->itemAdjunto = $itemAdjunto;
        $this->itemAdjunto->setId(generateItemCodes::generateItemAdjuntoId($this->id));
    }
    
    /**
     * Comprueba si el ítem es de respuesta obligatoria.
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }

    public function setRequired($required) {
        $this->required = $required;
    }

    public function getSeed() {
        return $this->seed;
    }

    public function setSeed($seed) {
        $this->seed = $seed;
    }



}

?>