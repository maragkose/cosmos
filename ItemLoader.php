<?php

/**
 * Recibe información extraida de un fichero de texto e instancia un objeto Item.
 * Si el ítem es un conjunto, carga el contenido del subconjunto.
 * 
 * @package Item
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ItemLoader extends Item {
    /**
     * Nombre permitido para el campo de subconjunto de ítems.
     * Es posible crear un subconjunto de ítems dependientes de uno de los 
     * ítems del conjunto principal. Este subconjunto debe mantener la misma
     * estructura que mantienen los ítems dentro del conjunto principal. 
     * En los ítems del subconjutno pueden especificarse las mismas propiedades
     * que en cualquier ítem (pregunta, tipo de respuesta, respuestas posibles, etc.)
     * 
     * P. ej.
     * conjunto_principal:
     *      items:
     *          item_padre_subconjunto:
     *              items:
     *                  subitem_item_padre:
     *                      texto: "Texto del sub�tem"
     *                      formato: abierta | likert | ...
     *                      ...
     */

    const SUB_ITEMS_FIELD = "items";

    protected $itemData;

    /**
     * Genera un objeto item a partir de la información recibida.
     * En un ítem sin propiedades adicionales, $itemData sólo 
     * contiene el texto del ítem.
     * El parámetro $idFromTexto indica si el identificador debe
     * ser generado a partir del texto del ítem.
     * @param string $itemId
     * @param string $itemData 
     * @param boolean $idFromTexto
     */
    public function __construct($itemId, $itemData, $idFromTexto) {
        $this->setItemData($itemData);
        $this->setTextoPregunta();
        if ($idFromTexto){
            $this->setId(self::textoToId($this->getTextoPregunta()));
        }
        else
            $this->setId($itemId);
        $this->setTipoRespuesta();
    }

    public function getItemData() {
        return $this->itemData;
    }

    public function setItemData($itemData) {
        $this->itemData = $itemData;
    }

    /**
     * En un ítem sin propiedades adicionales, $itemData sólo 
     * contiene el texto del ítem. Las subclases lo implementan de forma particular.
     */
    public function setTextoPregunta() {
        $this->textoPregunta = $this->getItemData();
    }

    /**
     * Este método lo implementan de forma particular las subclases
     */
    public function setTipoRespuesta() {
        
    }

    /**
     * Según la información contenida en $itemData, crea un objeto
     * item estándard, uno con propiedades adicionales (p. ej., tipo de 
     * respuesta) o un subconjunto de ítems.
     * El parámetro $idFromTexto indica si el identificador debe
     * ser generado a partir del texto del ítem.
     * @param type $itemId
     * @param array|string $itemData
     * @param boolean $idFromTexto
     * @return \ItemExtendedLoader|\ItemsSubGroupLoader|\self 
     */
    public static function getInstance($itemId, $itemData, $idFromTexto = False) {
        if (is_array($itemData)) {
            // Si el ítem posee un subconjunto, lo carga y lo devuelve
            if (self::hasItemsSubGroup($itemData)) {
                $itemPadre = new ItemExtendedLoader($itemId, $itemData, $idFromTexto);
                return new ItemsSubGroupLoader($itemPadre);
            } else {
                return new ItemExtendedLoader($itemId, $itemData, $idFromTexto);
            }
        }
        return new self($itemId, $itemData, $idFromTexto);
    }

    /**
     * Comprueba si el ítem posee un subconjunto de ítems.
     * @param array $itemData
     * @return boolean
     */
    static function hasItemsSubGroup(array $itemData) {
        return array_key_exists(self::SUB_ITEMS_FIELD, $itemData);
    }

    /**
     * Adapta una cadena de texto para poder ser utilizada como identificador.
     * @param string $texto
     */
    static function textoToId($texto) {
        // Limita el número de caracteres utilizados para generar el identificador
        $texto = substr($texto, 0, TEXT_ID_CHAR_LIMIT);
        $vocReemplazar = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ", "À", "È", "Ì", "Ò", "Ù", "à", "è", "ì", "ò", "ù", "ç", "Ç", "â", "ê", "î", "ô", "û", "Â", "Ê", "Î", "Ô", "Û", "ü", "ö", "Ö", "ï", "ä", "ë", "Ü", "Ï", "Ä", "Ë");
        $vocReemplazo = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E");
        $texto = str_replace($vocReemplazar, $vocReemplazo, $texto);
        $otrosCaracteres = array("·", "'", "/", "!", "¡", "¿", "?", ".", "-", ";", ",", ":", "(", ")", "\"", "@", " ");
        $texto = str_replace($otrosCaracteres, "_", $texto);
        return $texto;
    }

}

?>