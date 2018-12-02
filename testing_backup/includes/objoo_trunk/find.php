<?php
/**
 * Recibe un array con condiciones para realizar una consulta en la base de datos
 * y genera una cadena de texto adaptada a MySQL.
 * 
 * Ejemplo de array de entrada:
 * array(
 *      "where"=>array(
 *                      "nombre"=>"fulano",
 *                      "edad"=>">18"),
 *      "order_by"=>"edad",
 *      "order_type"=>"asc",
 *      "limit"=>100
 * )
 * Cadena de texto generada: "WHERE nombre = 'fulano' AND edad > 18 ORDER BY edad ASC LIMIT 100"
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://jharo.net
 * @author Juan Haro <juanharo@gmail.com>
 */
class find {
    
    /**
     * Etiquetas de los campos del array que recibe como parámetro de entrada.
     */
    const ORDER_BY_ARRAY_FIELD = "order_by";
    const ORDER_TYPE_ARRAY_FIELD = "order_type";
    const LIMIT_ARRAY_FIELD = "limit";
    const WHERE_ARRAY_FIELD = "where";

    /**
     * Palabras clave utilizadas por MySQL en las consultas.
     */
    const ORDER_BY_SQL_FIELD = "ORDER BY";
    const LIMIT_SQL_FIELD = "LIMIT";
    const WHERE_SQL_FIELD = "WHERE";

    private $conditionsData = array();
    private $orderBy;
    private $orderType;
    private $limit;
    private $where;

    /**
     * Busca en el array recibido los campos de condiciones de la query y asgina
     * su valor a las variables del objeto. 
     * 
     * @param array $conditionsData
     */
    function __construct(array $conditionsData) {
        $this->setConditionsData($conditionsData);
        $this->setLimit();
        $this->setOrderBy();
        $this->setOrderType();
        $this->setWhere();
    }

    public function getConditionsData() {
        return $this->conditionsData;
    }

    public function setConditionsData($conditionsData) {
        $this->conditionsData = $conditionsData;
    }

    public function getOrderBy() {
        if ($this->orderBy)
            return self::ORDER_BY_SQL_FIELD . " {$this->orderBy}";
    }

    public function setOrderBy() {
        if (array_key_exists(self::ORDER_BY_ARRAY_FIELD, $this->conditionsData))
            $this->orderBy = $this->conditionsData[self::ORDER_BY_ARRAY_FIELD];
    }

    public function getOrderType() {
        if ($this->orderType)
            return $this->orderType;
    }

    public function setOrderType() {
        if (array_key_exists(self::ORDER_TYPE_ARRAY_FIELD, $this->conditionsData))
            $this->orderType = $this->conditionsData[self::ORDER_TYPE_ARRAY_FIELD];
    }

    public function getLimit() {
        if ($this->limit)
            return self::LIMIT_SQL_FIELD . " $this->limit";
    }

    public function setLimit() {
        if (array_key_exists(self::LIMIT_ARRAY_FIELD, $this->conditionsData))
            $this->limit = $this->conditionsData[self::LIMIT_ARRAY_FIELD];
    }

    /**
     * Crea una cadena de texto con las condiciones de la cláusula where. 
     * @return string
     */
    public function getWhere() {
        if (!$this->where)
            return "";
        $whereClause = "";
        foreach ($this->where as $field => $value) {
            switch (true) {
                case stripos($value, 'LIKE') === 0:
                    $compare = "LIKE ";
                    break;
                case stripos($value, '>') === 0:
                    $compare = ">";
                    break;
                case stripos($value, '<') === 0:
                    $compare = "<";
                    break;
                case stripos($value, '!=') === 0:
                    $compare = "!=";
                    break;
                default:
                    $compare = "=";
                    break;
            }
            // Elimina el comparador de la cadena $value
            $value = str_ireplace($compare, '', $value);
            if ($whereClause)
                $whereClause.=" AND ";
            $whereClause.="$field $compare '$value'";
        }
        return self::WHERE_SQL_FIELD . " $whereClause";
    }

    public function setWhere() {
        if (array_key_exists(self::WHERE_ARRAY_FIELD, $this->conditionsData))
            $this->where = $this->conditionsData[self::WHERE_ARRAY_FIELD];
    }

    /**
     * Genera una cadena de texto con las condiciones de la consulta adaptada
     * a MySQL.
     * @return type
     */
    public function generateQueryConditions() {
        $queryConditions = "{$this->getWhere()} {$this->getOrderBy()} 
        {$this->getOrderType()} {$this->getLimit()}";

        return $queryConditions;
    }

}

?>
