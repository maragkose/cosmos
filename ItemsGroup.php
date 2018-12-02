<?php

/**
 * Clase que crea objetos formados por conjuntos de ítems. 
 * 
 * @todo Por corregir. Algunos métodos heredados no siguen la estructura de inputs
 * de las clases padre, lo cual provoca errores de tipo STRICT. 
 * 
 * @package ItemsGroup
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ItemsGroup {
    /**
     * Se utiliza en la vista para codificar el identificador de los items.
     * Ej.: grupo__item1 
     * Ej.: grupo__subgrupo__item1
     */

    const ID_DELIMITER = "__";
    /**
     * El identificador del elemento html donde se mostrára el conjunto de ítems.
     * Se utiliza conjuntamente con un valor numérico para asignar el orden de los
     * conjuntos de ítems mostrados en la plantilla html. 
     */
    const ID_HTML = "itemsGroup";

    /**
     * Los items que forman el grupo.
     * @var array 
     */
    protected $items = array();

    /**
     * Subconjuntos de ItemsGroup que pertenecen a un ItemsGroup padre.
     */
    protected $itemsSubGroups = array();

    /**
     * El título del conjunto. Se muestra en el encabezado de cada conjunto.
     * @var string 
     */
    protected $title;

    /**
     * Descripción del conjunto. Se muestra bajo el título.
     * @var string 
     */
    protected $description;

    /**
     * Las etiquetas del grupo. Es un objeto de la clase Etiquetas.
     * Se inicializa a NULL ya que puede existir un grupo de ítems que no
     * contenga etiquetas (p.ej, si se compone de ítems con opción de respuesta
     * múltiple).
     * @var Etiquetas
     */
    public $etiquetas = array();

    /**
     * El identificador del conjunto.
     * Se utiliza para codificar los ítems.
     * Ej. idGrupo__idPregunta
     * @var string
     */
    protected $id;
    
    /**
     * Ruta de la imagen que será mostrada en la cabecera del conjunto, bajo
     * el título y la descripción.
     * @var string 
     */
    protected $image;
    
    /**
     * El conjunto será mostrado en una página nueva.
     * @var boolean 
     */
    protected $newPage = False;
    
    /**
     * Los identificadores de ítems del conjunto serán generados a partir de
     * su texto.
     * @var boolean
     */
    protected $idsFromTexto = False;

    /**
     * Identificador por el que se comienzan a numerar los subconjuntos. 
     */

    const SUBGROUP_ID_INICIO = 1;
    /**
     * Identificador por el que se comienzan a numerar los ítems
     */
    const ITEM_ID_INICIO = 1;

    /**
     * El constructor requiere un identificador para el grupo de ítems. 
     * Opcionalmente puede recibir un objeto de etiquetas y el título 
     * que será asignado al conjunto.
     * @param string id
     * @param array|integer|Etiquetas $etiquetas
     * @param string $title 
     */
    function __construct($id, $etiquetas = "", $title = "") {
        $this->setId($id);
        if ($etiquetas)
            $this->setEtiquetas($etiquetas);
        if ($title)
            $this->setTitle($title);
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getItems() {
        if ($this->items)
            return $this->items;
        elseif (!$this->items && !$this->itemsSubGroups)
            die("El conjunto {$this->getId()} no contiene ítems");
    }

    /**
     * Añade un nuevo item al array de ítem.
     * La variable $item puede ser un objeto Item o bien una cadena de texto.
     * Si la variable $item es una cadena de texto, crea un nuevo objeto ítem 
     * al que le asigna ese texto. Si no recibe identificador, se le asignará un 
     * identificador númerico (número de ítems + 1).
     * @param Item|string $item 
     * @param itemId
     */
    public function addItem($item, $id = NULL) {
        if ($id===NULL)
            $id = sizeof($this->items) ? sizeof($this->items) + 1 : self::ITEM_ID_INICIO;
        if ($item instanceof Item) {
            if ($item->getId() === NULL)
                $item->setId($id);
            $this->items[$id] = $item;
        } else {
            $newItem = new Item($item);
            $newItem->setId($id);
            $this->items[$id] = $newItem;
        }
    }

    /**
     * Devuelve los subconjuntos de ítems.
     * @return array 
     */
    public function getItemsSubGroups() {
        return $this->itemsSubGroups;
    }

    /**
     * Añade un nuevo subconjunto.
     * @param ItemsGroup $itemsSubGroup 
     */
    public function addItemsSubGroup(ItemsGroup $itemsSubGroup) {
        // Si es el primer Subconjunto le asigna el primer id (SUBGROUP_ID_INICIO).
        // Se sustituye el valor de inicio predeterminado en la inserción
        // de elementos en un array (0)
        if (!$this->itemsSubGroups)
            $this->itemsSubGroups[self::SUBGROUP_ID_INICIO] = $itemsSubGroup;
        else
            $this->itemsSubGroups[] = $itemsSubGroup;
    }

    /**
     * Recupera el identificador completo de un ítem del conjunto. 
     * El identificador completo se utiliza en el atributo 'name' de los 
     * elementos del formulario html.
     * La búsqueda se realiza comparando el hash del ítem pasado con el hash
     * de cada uno de los ítems del conjunto.
     * Por ahora sólo acepta dos niveles de profundidad: Group_SubGroup_Item
     * @param Item $item 
     * return string | False
     */
    public function getFullItemId(Item $itemToFind) {
        if ($this->getItems()) {
            foreach ($this->getItems() as $item) {
                if ($item->getHash() == $itemToFind->getHash())
                    return $this->id . self::ID_DELIMITER . $item->getId();
                // También busca entre los ítems adjuntos.
                // Devuelve el identificador del ítem+ID_ADJUNTO
                if ($item->getItemAdjunto() && $item->getItemAdjunto()->getHash() == $itemToFind->getHash())
                    return $this->id . self::ID_DELIMITER . $item->getId() . self::ID_DELIMITER . Item::ID_ADJUNTO;
            }
        }
        // Si no se encuentra entre los ítems del conjunto, o si el conjunto no
        // posee ítems, busca en los subconjuntos.
        if ($this->getItemsSubGroups()) {
            foreach ($this->getItemsSubGroups() as $itemsSubGroup) {
                // El condicional impide que devuelva False
                if ($itemsSubGroup->getFullItemId($itemToFind))
                    return $this->id . self::ID_DELIMITER . $itemsSubGroup->getFullItemId($itemToFind);
            }
        }

        return False;
    }

    /**
     * Recupera las etiquetas contenidas en el objeto Etiquetas almacenado
     * en la variable interna.
     * @return type 
     */
    public function getEtiquetas() {
        return $this->etiquetas->getEtiquetas();
    }

    /**
     * Crea un objeto de Etiquetas a partir de la información recibida.
     * También permite la asignación de un objeto Etiquetas.
     * @param type $etiquetas 
     */
    public function setEtiquetas($etiquetas) {
        if ($etiquetas instanceof Etiquetas)
            $this->etiquetas = $etiquetas;
        else
            $this->etiquetas = Etiquetas::getInstance($etiquetas);
    }
    
    public function getImage() {
        return $this->image;
    }
    
    /**
     * Recibe la ruta de la imagen.
     * @param string $image
     */
    public function setImage($image) {
        $this->image = $image;
    }
    
    public function getNewPage() {
        return $this->newPage;
    }
    
    /**
     * $newPage = True | False
     * @param boolean $newPage
     */
    public function setNewPage($newPage) {
        $this->newPage = $newPage;
    }

    public function getIdsFromTexto() {
        return $this->idsFromTexto;
    }
    
    /**
     * $idsFromTexto = True | False
     * @param boolean $idsFromTexto
     */
    public function setIdsFromTexto($idsFromTexto) {
        $this->idsFromTexto = $idsFromTexto;
    }



}

?>
