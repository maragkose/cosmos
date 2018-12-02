<?php

/**
 * Clase que permite utilizar una tabla de la base de datos como si fuera
 * un objeto. Facilita métodos para recuperar, eliminar, actualizar e insertar registros.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://jharo.net
 * @author Juan Haro <juanharo@gmail.com>
 */
include_once 'fieldSet.php';
include_once 'field.php';
include_once 'dbTableException.php';

class dbTable {
    /**
     * Identificador de clave primaria en MySQL.
     */

    const SQL_PRIMARY_KEY = "PRI";
    const SQL_NULL_FIELD = "NULL";

    protected $tableName;

    /**
     * Campos de la tabla.
     * @var fieldSet
     */
    protected $fields;

    /**
     * El campo identificador de la tabla. 
     * @var field 
     */
    protected $id;

    /**
     * Conjunto de registros devuelto por la última consulta.
     * @var \rowSet
     */
    private $rowSet;

    /**
     * Array de objetos dbTable. Tras mapear una tabla, ésta se guarda en el
     * array y puede ser recuperada posteriormente sin necesidad de crear un
     * nuevo objeto.
     * @var array
     */
    static $loadedTables = array();
    
    /**
     * La tabla no existe en la base de datos. Se utiliza en el modo de creación
     * de tablas.
     * @var booean 
     */
    private $newTable = FALSE;

    /**
     * Crea un nuevo objeto dbTable. Si recibe un nombre de tabla existente, cargará todos
     * sus campos en el objeto de conjunto de campos $fields. Si el nombre de
     * la tabla no coincide con ninguna tabla existente, creará un objeto de 
     * conjunto de campos vacío para la proceder a la creación de nueva tabla.
     * @param string $tableName
     */
    private function __construct($tableName) {
        $this->fields = new fieldSet();
        $this->setTableName($tableName);
        if (!query::tableExists($tableName))
            $this->newTable=TRUE;
        else 
            $this->mapFields();
        
    }

    /**
     * Carga una nueva tabla de la base de datos y la guarda en una variable interna,
     * o recupera una ya cargada.
     * @param string $tableName
     * @return \self
     */
    static function get($tableName) {
        if (array_key_exists($tableName, self::$loadedTables)) {
            return self::$loadedTables[$tableName];
        } else {
            $table = new self($tableName);
            self::$loadedTables[$tableName] = $table;
            return $table;
        }
    }

    /**
     *  Recupera los campos de la tabla y los asigna al array de campos del 
     *  objeto.
     */
    private function mapFields() {

        $descTable = query::describe($this->tableName);
        $numFields = mysqli_affected_rows(sql::getInstance()->getLink());

        while ($row = mysqli_fetch_object($descTable)) {

            if (stripos($row->Type, ")")) {
                $type = explode("(", $row->Type);
                $length = explode(")", $type[1]);
                $length = $length[0];
                $type = $type[0];
            } else {
                //para date, por ejemplo
                $type = $row->Type;
                $length = null;
            }

            $field = new field();
            $field->setName($row->Field);
            $field->setType($type);
            $field->setLength($length);
            $field->setNull($row->Null);
            $field->setExtra($row->Extra);

            if ($row->Key == self::SQL_PRIMARY_KEY or $numFields == 1) {
                $this->setId($field);
                $field->setIsPrimaryKey(True);
            }

            $this->fields->addField($field);
        }
    }

    /**
     * Devuelve el conjunto de registros de la tabla que cumpla con las condiciones
     * recibidas como parámetro.
     * 
     * @param array $conditions
     * @return \rowSet | \tableRow
     */
    function selectAll(array $conditions = array()) {
        $this->rowSet = new rowSet($this->tableName);
        $find = new find($conditions);
        $queryConditions = ($conditions) ? $find->generateQueryConditions() : "";

        $queryResult = query::select($this->fields, $this->tableName, $queryConditions);
        $resultArray = sqlResult::load($queryResult);

        // Devuelve un solo registro
        if (mysqli_num_rows($queryResult) == 1)
            return new tableRow($this->tableName, $resultArray);

        $this->rowSet->loadResultArray($resultArray);
        return $this->rowSet;
    }

    /**
     * Devuelve el registro de la tabla que coincida con el identificador recibido
     * como parámetro.
     * @param type $id
     * @return \tableRow
     */
    function selectOne($id) {

        $queryResult = query::select($this->fields, $this->tableName, "WHERE {$this->id->getName()}='$id'");
        $rowData = sqlResult::load($queryResult);
        return new tableRow($this->tableName, $rowData);
    }

    /**
     * Actualiza los registros de la tabla con la información recibida.
     * @param array $rowData
     * @return type
     */
    function update(array $rowData) {
        $idFieldName = $this->id->getName();
        $idValue = $rowData[$idFieldName];
        return query::update($this->tableName, $rowData, "$idFieldName = '$idValue'");
    }

    /**
     * Elimina el registro de la tabla que coincida con el identificador contenido
     * en el array $rowData.
     * 
     * @param array $rowData
     * @return type
     */
    function delete($rowData) {
        $idFieldName = $this->id->getName();
        $idValue = $rowData[$idFieldName];
        return query::delete($this->tableName, "$idFieldName = '$idValue'");
    }

    /**
     * Añade un nuevo registro en la tabla.
     * @param array $rowData
     * @return type 
     */
    function insert($rowData) {
        return query::insert($this->tableName, $rowData);
    }

    public function getTableName() {
        return $this->tableName;
    }

    private function setTableName($tableName) {
        $this->tableName = $tableName;
    }
    
    
    /**
     * Elimina el objeto de tabla cargado en la variable $loadedTables
     */
    function unsetTable(){
        unset(self::$loadedTables[$this->tableName]);
    }

    /**
     * Devuelve el array de campos. Si recibe el nombre de un campo existente
     * en la tabla, devuelve su objeto field.
     * 
     * @param string $fieldName
     * @return \fieldSet | \field
     * @throws dbTableException El campo no existe en la tabla.
     */
    public function getFields($fieldName = "") {
        if ($fieldName) {
            if (in_array($fieldName, $this->getFieldNames()))
                return $this->fields->getField($fieldName);
            else
                throw new dbTableException(__CLASS__ . __FUNCTION__, "El campo '$fieldName' no existe en la tabla '{$this->tableName}'");
        }
        return $this->fields;
    }

    /**
     * Devuelve un array con los nombres de los campos que forman la tabla.
     * Alias de $this->getFields()->getFieldNames()
     * @return array
     */
    public function getFieldNames() {
        return $this->getFields()->getFieldNames();
    }

    /**
     * Devuelve el campo identificador de la tabla.
     * @return \field
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Establece el campo identificador de la tabla.
     * @param \field $id
     */
    private function setId(field $id) {
        $this->id = $id;
    }
    
    /**
     * Devuelve el campo que su nombre coincida con el recibido como parámetro.
     * Si no encuentra ningún campo con ese nombre, creará un nuevo campo en
     * el conjunto y devolverá el objeto creado.
     * @param string $fieldName
     * @return \field
     */
    public function __get($fieldName) {
        if($this->fields->getField($fieldName))
            return $this->fields->getField($fieldName);
        else{
            $field = new field();
            $field->setName($fieldName);
            $this->fields->addField($field);
            return $field;
        }
    }
    
    /**
     *  Crea una tabla nueva con los campos que se han especificado, o modifica
     *  los campos de una tabla existente.
     */
    public function save(){
        if($this->newTable)
            query::createTable($this->tableName, $this->fields);
        else
            query::alterTable ($this->tableName, $this->fields);
    }
    
    public function isNew() {
        return $this->newTable;
    }

}

?>