<?php
/**
 * Recibe información extraida del archivo de cuestionario, crea un objeto ItemsGroup
 * y añade todos los items que lo forman. También se encarga de crear el objeto
 * Etiquetas del conjunto según la información recibida del archivo de texto.
 * 
 * @package ItemsGroup
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
*/
class ItemsGroupLoader extends ItemsGroup {

    /**
     * Campos del archivo de cuestionario
     */
    const TITLE_FIELD = "title";
    const DESCRIPTION_FIELD = "description";
    const ITEMS_FIELD = "items";
    const ETIQUETAS_FIELD = "labels";
    const NEW_PAGE_FIELD = "new_page";
    const IMAGE_FIELD = "image";
    const IDS_FROM_TEXTO_FIELD = "texto_ids";

    private $itemsGroupData;

    /**
     * Crea un grupo y le añade los ítems que lo componen según la información
     * recibida.
     * @param type $id
     * @param type $itemsGroupData 
     */
    function __construct($id, $itemsGroupData) {
        $this->setId($id);
        $this->setItemsGroupData($itemsGroupData);
        $this->loadTitle();
        $this->loadDesctiption();
        $this->loadEtiquetas();
        $this->loadImage();
        $this->loadNewPage();
        $this->loadIdsFromTexto();
        $this->loadAndAddElements();
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
        $this->itemsGroupData = $itemsGroupData;
    }
    
    
    /**
     * Devuelve el campo ítems del grupo.
     * Si no hay ítems, detiene la ejecución.
     * @return array 
     */
    private function getItemsFromGroupData() {
        if (!array_key_exists(self::ITEMS_FIELD, $this->itemsGroupData))
            die("El grupo {$this->id} no contiene items");

        return $this->itemsGroupData[self::ITEMS_FIELD];
    }
    

    /**
     * Carga un array de ítems o de subconjuntos en el conjunto.
     */
    public function loadAndAddElements() {

        foreach ($this->getItemsFromGroupData() as $itemId => $itemData) {
            // El tercer parámetro determina si el identificador debe ser generado
            // a partir del texto del ítem.
            $item = ItemLoader::getInstance($itemId, $itemData, $this->idsFromTexto);
            // Si el ítem es un conjunto, lo añade al array de subconjuntos.
            if ($item instanceof ItemsGroup)
                $this->addItemsSubGroup($item);
            else
                $this->addItem($item);
        }
    }

    /**
     * Comprueba si el campo IDS_FROM_TEXTO_FIELD se encuentra en el array.
     * Si el valor del campo es 's', el texto de cada ítem será utilizado como
     * identificador (adaptándolo para suprimir caracteres extraños).
     * @return boolean
     */
    function loadIdsFromTexto(){
        if (array_key_exists(self::IDS_FROM_TEXTO_FIELD, $this->itemsGroupData)){
            if ($this->itemsGroupData[self::IDS_FROM_TEXTO_FIELD] == "s") {
                $this->setIdsFromTexto(True);
            } 
        }
    }

    /**
     * Busca en el array un campo con id TITLE_FIELD
     * Si lo encuentra, lo asigna a $title.
     */
    function loadTitle() {
        if (array_key_exists(self::TITLE_FIELD, $this->itemsGroupData))
            $this->title = $this->itemsGroupData[self::TITLE_FIELD];
    }
    
    /**
     * Busca en el array un campo con id DESCRIPTION_FIELD
     * Si lo encuentra, lo asigna a $description.
     */
    function loadDesctiption() {
        if (array_key_exists(self::DESCRIPTION_FIELD, $this->itemsGroupData))
            $this->description = $this->itemsGroupData[self::DESCRIPTION_FIELD];
    }

    /**
     * Carga las etiquetas del archivo.
     * Si el campo ETIQUETAS_FIELD tiene un valor numérico, genera tantas 
     * etiquetas como indica ese valor.  
     */
    function loadEtiquetas() {
        
        if (array_key_exists(self::ETIQUETAS_FIELD, $this->itemsGroupData)) {

            $etiquetas = $this->itemsGroupData[self::ETIQUETAS_FIELD];
            $this->setEtiquetas(Etiquetas::getInstance($etiquetas));
        }
    }
    
    /**
     * Busca en el array un campo con id IMAGE_FIELD
     * Si lo encuentra, lo asigna a $image;
     */
    function loadImage(){
        if (array_key_exists(self::IMAGE_FIELD, $this->itemsGroupData))
            $this->image = $this->itemsGroupData[self::IMAGE_FIELD];
    }
    
    /**
     * Busca en el array un campo con id NEW_PAGE_FIELD
     * Valores: 's' => True; otros => False
     */
    function loadNewPage(){
        if (array_key_exists(self::NEW_PAGE_FIELD, $this->itemsGroupData))
            if ($this->itemsGroupData[self::NEW_PAGE_FIELD] == "s") {
                $this->newPage = True;
            } else{
                $this->newPage = False;
            }
    }

}

?>
