<?php
/**
 * Recibe información extraida de un archivo de cuestionario y crea un objeto Item
 * con propiedades adicionales. 
 * 
 * @package Item
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ItemExtendedLoader extends ItemLoader {
    
    /**
     * Nombre permitido para el campo de formato de respuesta en el archivo 
     * de cuestionario.
     */

    const TIPO_RESPUESTA_FIELD = "formato";

    /**
     * En ítems con propiedades adicionales se debe especifiar el campo en 
     * el que se encuentra la pregunta. 
     */
    const PREGUNTA_FIELD = "texto";

    /**
     * Nombre permitido para el campo de opciones de respuesta. 
     */
    const OPCIONES_RESPUESTA_FIELD = "respuestas";

    /**
     * Nombre permitido para el campo de ítem adjunto. 
     */
    const ADJUNTO_FIELD = "item_adjunto";
    
    /**
     * Nombre permitido para el campo de respuesta obligatoria. 
     */
    const IS_REQUIRED_FIELD = "obligatorio";

    /**
     * Hereda el constructor de ItemLoader e incorpora la asigación 
     * de opciones de respuesta.
     * @param string $itemId
     * @param array $itemData 
     * @param boolean $idFromTexto
     */
    function __construct($itemId, array $itemData,$idFromTexto) {
        parent::__construct($itemId, $itemData, $idFromTexto);
        $this->setOpcionesRespuesta();
        $this->setItemAdjunto();
        $this->setRequired();
    }

    /**
     * Busca el campo TIPO_RESPUESTA_FIELD en el array y lo asigna
     * a $tipoRespuesta.
     */
    function setTipoRespuesta() {
        if (array_key_exists(self::TIPO_RESPUESTA_FIELD, $this->itemData))
            $this->tipoRespuesta = $this->itemData[self::TIPO_RESPUESTA_FIELD];
    }

    /**
     * Busca el campo PREGUNTA_FIELD en el array y lo asigna
     * a $textoPregunta.
     */
    function setTextoPregunta() {

        if (array_key_exists(self::PREGUNTA_FIELD, $this->itemData))
            $this->textoPregunta = $this->itemData[self::PREGUNTA_FIELD];
        else
            die("El item {$this->getId()} debe contener una pregunta");
    }

    /**
     * Busca el campo OPCIONES_RESPUESTA_FIELD y asigna las opciones de respuesta
     * a un ítem. 
     */
    function setOpcionesRespuesta() {
        if (array_key_exists(self::OPCIONES_RESPUESTA_FIELD, $this->itemData)) {
            $respuestasData = $this->itemData[self::OPCIONES_RESPUESTA_FIELD];
            $this->opcionesRespuesta = Respuestas::getInstance($respuestasData, $this->getId());
        }
    }

    /**
     * Busca el campo ADJUNTO_FIELD y asigna un ítem adjunto.
     */
    function setItemAdjunto() {
        if (array_key_exists(self::ADJUNTO_FIELD, $this->itemData)) {
            $itemAdjuntoData = $this->itemData[self::ADJUNTO_FIELD];
            //id del item adjunto: itemPadreId__adjunto
            $this->itemAdjunto = ItemLoader::getInstance(generateItemCodes::generateItemAdjuntoId($this->getId()), $itemAdjuntoData);
            // Se asigna $seed para identificar ítems adjuntos con el mismo texto y
            // formato de respuesta pertenecientes a diferentes ítems de un mismo subconjunto.
            $this->itemAdjunto->setSeed(microtime());
        }
    }
    
    /**
     * Comprueba si el ítem es de respuesta obligatoria.
     */
    function setRequired() {
        if (array_key_exists(self::IS_REQUIRED_FIELD, $this->itemData)) {
            if ($this->itemData[self::IS_REQUIRED_FIELD] == "s") {
                $this->required = True;
            } else if ($this->itemData[self::IS_REQUIRED_FIELD] == "n") {
                $this->required = False;
            }
        }
    }

}

?>