<?php
/**
 * Carga subconjuntos de ítems que se añaden al conjunto padre.
 * @package ItemsGroup
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
*/
class ItemsSubGroupLoader extends ItemsGroup {

    private $itemsGroupData;
    
    /**
     *  A partir de la información proporcionada por un ítem padre que posee un 
     * conjunto de ítems, carga un subconjunto que es añadido al conjunto
     * que pertenece el ítem padre.
     * El subconjunto adquiere el identificador y título especificado para el
     * ítem padre.
     * 
     * @param Item $itemPadre
     */
    function __construct(Item $itemPadre) {
        $this->setId($itemPadre->getId());
        $this->setTitle($itemPadre->getTextoPregunta());
        $this->setItemsGroupData($itemPadre->getItemData());
        $this->loadAndAddItems();
    }


    
    private function getItemsGroupData() {
        return $this->itemsGroupData;
    }
    
    /**
     * itemsGroupData es el array con toda la información del grupo de items
     * extraido del archivo.
     * @param array $itemsGroupData
     */
    private function setItemsGroupData($itemsGroupData) {
         if (array_key_exists(ItemLoader::SUB_ITEMS_FIELD,$itemsGroupData))
            $this->itemsGroupData = $itemsGroupData[ItemLoader::SUB_ITEMS_FIELD];
    }
    

    /**
     * Carga un array de items en el subconjunto.
     * Crea un nuevo item por cada elemento del array de itemsData y lo añade
     * al array de items.
     */
    public function loadAndAddItems() {

        foreach ($this->getItemsGroupData() as $itemId => $itemData) {
            $item = ItemLoader::getInstance($itemId, $itemData);
            // $seed permite identificar ítems con idénticos atributos
            // pertenecientes a diferentes subconjuntos. Se utiliza como
            // $seed el id del subconjunto.
            $item->setSeed($this->getId());
            $this->addItem($item);
        }
    }



}

?>
