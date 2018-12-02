<?php
/**
 * Gestiona la manipulación de registros devueltos tras una consulta.
 * 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://jharo.net
 * @author Juan Haro <juanharo@gmail.com>
 */
class sqlResult{
    
    /**
     * Recibe el resultado de una consulta y lo carga en un array. 
     * Devuelve un array con el siguiente formato:
     * array(
     *      campo => valor,
     *      campo2 = valor2
     * )
     * 
     * @param mysqli_result $result
     * @return array
     */
    static function load(mysqli_result $result){
        
        $resultArray = array();
        
        if(mysqli_num_rows($result)==1)
            return mysqli_fetch_assoc($result);
        
        while($row = mysqli_fetch_assoc($result)){
            array_push($resultArray, $row);
        }    
        
        return $resultArray;
    }
    
    
}
?>
