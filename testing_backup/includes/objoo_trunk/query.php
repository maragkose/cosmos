<?php

/**
 * Clase para realizar consultas en la base de datos.
 * 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://jharo.net
 * @author Juan Haro <juanharo@gmail.com>
 */
require_once("sql.php");
require_once("queryException.php");

class query {

    /**
     * Ejecuta la consulta sql recibida como parámetro.
     * 
     * @param string $query
     * @return type $result_query
     * @throws queryException
     */
    static function execute($query) {
        $sql = sql::getInstance();

        if (!$sql->getLink()) {
            $msg = "La conexión está cerrada";
            throw new queryException(__FUNCTION__, $msg);
        }
        $resultQuery = @mysqli_query($sql->getLink(), $query);
        if (!$resultQuery) {
            $msg = "No se pudo realizar la consulta '$query'. " . mysqli_error($sql->getLink());
            throw new queryException(__FUNCTION__, $msg);
        } else {
            $msg = "Se ejecutó con éxito la consulta '$query'";
            logger::writeLog(__FUNCTION__, $msg);
            return $resultQuery;
        }
    }

    /**
     * Busca y devuelve los registros de la base de datos que coincidan con los
     * parámetros pasados. 
     * 
     * SELECT $fields FROM $table $conditions
     * 
     * @param string $fields
     * @param string $table
     * @param string $conditions
     * @return $result_query;
     */
    static function select($fields, $table, $conditions = "") {
        $sql = sql::getInstance();

        $query = sprintf("SELECT %s FROM %s %s", mysqli_real_escape_string($sql->getLink(), $fields), mysqli_real_escape_string($sql->getLink(), $table), $conditions);

        // Elimina espacios en blanco que excedan un carácter.
        $query = preg_replace('/\s\s+/', ' ', $query);

        $queryResult = self::execute($query);
        if (@!mysqli_num_rows($queryResult)) {
            $msg = "No se encontró ningún resultado al ejecutar la consulta  '$query'";
            throw new queryException(__FUNCTION__, $msg);
        }
        return $queryResult;
    }

    /**
     * Actualiza los registros de la base de datos que coincidan con los parámetros
     * recibidos. 
     * 
     * UPDATE $table SET $field=$value ... WHERE $conditions
     * 
     * @param string $table
     * @param array $fields
     * @param string $conditions
     * @return type
     */
    static function update($table, array $fields, $conditions) {
        $update = "UPDATE " . $table . " SET ";
        foreach ($fields as $field => $value) {
            $value = mysqli_real_escape_string(sql::getInstance()->getLink(), $value);
            $update.="$field = '$value',";
        }
        // substr() es necesario para no incluir la última coma.
        $update = substr($update, 0, -1) . " WHERE " . $conditions;
        return self::execute($update);
    }

    /**
     * Añade un nuevo registro en la base de datos.
     * 
     * INSERT INTO $table ($fields) VALUES ($values)
     * 
     * @param string $table
     * @param array $fields
     * @return type
     */
     static function insert($table, array $fields) {
        $fieldsSql = "";
        $valuesSql = "";
        $insert = "INSERT INTO " . $table . " ";
        foreach ($fields as $field => $value) {
            $fieldsSql.=$field . ",";
            $value = mysqli_real_escape_string(sql::getInstance()->getLink(), $value);
            $valuesSql.="'$value',";
        }
        // substr() es necesario para no incluir la última coma.
        $fieldsSql = "(" . substr($fieldsSql, 0, -1) . ")";
        $valuesSql = "(" . substr($valuesSql, 0, -1) . ")";
        $insert.=$fieldsSql . " VALUES " . $valuesSql;
        return self::execute($insert);
    }

    /**
     * Elimina los registros de la base de datos que coincidan con las condiciones
     * recibibas como parámetro.
     * 
     * DELETE FROM $table WHERE $conditions
     * 
     * @param string $table
     * @param string $conditions
     * @return type
     */
    static function delete($table, $conditions) {
        $delete = "DELETE FROM " . $table . " WHERE " . $conditions;
        return self::execute($delete);
    }

    /**
     * Devuelve información sobre los campos de la tabla recibida como
     * parámetro.
     * 
     * @param string $table
     * @return type
     */
    static function describe($table) {
        $desc = "DESC $table";
        return self::execute($desc);
    }

    // comprueba si existe la tabla y devuelve true o false
    static function tableExists($tableName) {
        $query = "SHOW TABLES LIKE '" . $tableName . "'";
        $queryResult = self::execute($query);
        if (@!mysqli_num_rows($queryResult))
            return False;
        elseif (mysqli_num_rows($queryResult))
            return True;
    }

    static function alterTable() {
        
    }

    static function createTable($tableName, fieldSet $fieldSet) {

        $createQuery = "CREATE TABLE $tableName ";
        $columnDefinition = "";
        foreach ($fieldSet->getFields() as $field) {
            if ($columnDefinition)
                $columnDefinition.=",";
            $name = $field->getName();
            $type = $field->getType();
            $length = $field->getLength();
            $isPrimaryKey = ($field->getIsPrimaryKey()) ? "PRIMARY KEY" : "";
            $extra = $field->getExtra();
            $columnDefinition.="$name $type($length) $isPrimaryKey $extra";
        }
        $createQuery.="($columnDefinition)";

        self::execute($createQuery);
    }
    
    static function deleteTable($tableName){
        $deleteQuery = "DROP TABLE IF EXISTS $tableName";
        self::execute($deleteQuery);
    }
    
    static function renameTable($oldTableName, $newTableName){
        if(self::tableExists($oldTableName)){
            $renameQuery = "RENAME TABLE {$oldTableName} to $newTableName";
            self::execute($renameQuery);
        }else{
            $msg = "La tabla que intentas renombrar ($oldTableName) no existe.'";
            throw new queryException(__FUNCTION__, $msg);
        }
            
    }
    
    static function dbExists($dbName){
        $query = "SHOW DATABASES LIKE '" . $dbName . "'";
        $queryResult = self::execute($query);
        if (@!mysqli_num_rows($queryResult))
            return False;
        elseif (mysqli_num_rows($queryResult))
            return True;
    }
    
    static function createDb($dbName){
       $createQuery = "CREATE DATABASE $dbName";
       self::execute($createQuery);
    }

}