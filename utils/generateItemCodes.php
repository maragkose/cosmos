<?php

/**
 * Genera todos los identificadores codificados de los ítems del cuestionario.
 * @package utils
 * @author Juan Haro <juanharo@gmail.com>
 * @link http://jharo.net/dokuwiki/testmaker
 * @copyright Copyright 2012 Juan Haro
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class generateItemCodes {

    /**
     * Genera todos los identificadores codificados de un cuestionario.
     * @param array $itemsGroups
     * @param string $parentId Identificador del elemento padre
     * @return array 
     */
    static function generateAll($itemsGroups, $parentId = "") {
        $items = array();
        $parentId = ($parentId) ? $parentId . ItemsGroup::ID_DELIMITER : "";
        foreach ($itemsGroups as $itemsGroup) {
            if ($itemsGroup->getItems()) {
                foreach ($itemsGroup->getItems() as $item) {
                    $itemId = $item->getId();
                    $items[] = $parentId . $itemsGroup->getId() . Item::ID_DELIMITER . $itemId;
                    if ($item->getItemAdjunto()) {
                        $itemId = $item->getItemAdjunto()->getId();
                        $items[] = $parentId . $itemsGroup->getId() . Item::ID_DELIMITER . $itemId;
                    }
                }
            } else if ($itemsGroup->getItemsSubGroups()) {
                $items = array_merge($items, self::generateAll($itemsGroup->getItemsSubGroups(), $itemsGroup->getId()));
            }
        }
        return $items;
    }

    /**
     * Devuelve el identificador de un ítem adjunto.
     * @param type $itemId
     * @return type 
     */
    static function generateItemAdjuntoId($itemId) {
        return $itemId . Item::ID_DELIMITER . Item::ID_ADJUNTO;
    }

}

?>
