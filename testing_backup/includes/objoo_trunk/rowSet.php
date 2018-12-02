<?php
/**
 * Un conjunto de registros devueltos de una consulta en la base de datos.
 * 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://jharo.net
 * @author Juan Haro <juanharo@gmail.com>
 */
class rowSet {

    /**
     * Array de objetos tableRow. Cada uno representa un registro.
     * @var array
     */
    private $rows = array();
    /**
     * Nombre de la tabla de la que ha sido devuelto el registro. Se utiliza
     * para tener acceso a los métodos de la clase dbTable sin necesidad de 
     * cargar un nuevo objeto.
     * @var string 
     */
    private $tableName;

    function __construct($tableName) {
        $this->setTableName($tableName);
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    public function getRows() {
        return $this->rows;
    }

    /**
     * Recibe un array con la información de un registro de la tabla y crea
     * un nuevo objeto tableRow que añade al array de registros.
     * @param array $rowData
     */
    public function addRow(array $rowData) {
        $this->rows[] = new tableRow($this->tableName, $rowData);
    }

    /**
     * Recibe un array de resultados producto de una consulta mysql y crea un
     * nuevo registro por cada una de sus filas. Los registros creados se añaden
     * al array de registros.
     * 
     * @param array $resultArray
     */
    public function loadResultArray(array $resultArray) {
        foreach ($resultArray as $rowData)
            $this->addRow($rowData);
    }

    public function countRows() {
        return count($this->rows);
    }

    /**
     * Elimina todos los registros del conjunto. Llama al método delete() de
     * los registros.
     */
    public function delete() {
        foreach ($this->rows as $row)
            $row->delete();
    }

    /**
     * Actualiza todos los registros del conjunto con la información que recibe
     * del array $updateData. El array debe seguir el siguiente formato:
     * 
     * campo_a_modificar => nuevo_valor
     * 
     * Actualiza cada registro haciendo uso de su método save().
     * 
     * @param type $updateData
     */
    public function update($updateData) {

        foreach ($this->getRows() as $row) {
            foreach ($updateData as $field => $value){
                $row->$field = $value;
            }
            $row->save();
        }
    }

}

?>
