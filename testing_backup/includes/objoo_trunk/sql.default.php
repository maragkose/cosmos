<?php
/**
 * Clase que crea un objeto de conexión a la base de datos MySQL.
 * 
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://jharo.net
 * @author Juan Haro <juanharo@gmail.com>
 */


require_once("logger.php");
require_once("sqlException.php");


class sql {

    private $host;
    private $user;
    private $db;
    private $link;
    static $instance;
    
    /*
     * Registro de eventos. Si el valor es 'true', cada evento de la clase
     * quedará registrado.
     */
    const LOG_ENABLED = False;
    const SQL_DEF_HOST = "localhost";
    const SQL_DEF_USER = "root";

    /**
     * Inicializa las variables del objeto y conecta con la base de datos. 
     * Si recibe como parámetro un nombre de base de datos, intentará conectarse
     * a ella.
     * 
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $db
     */
    private function __construct($host=self::SQL_DEF_HOST, $user=self::SQL_DEF_USER, $pass='', $db='') {
        $this->setHost($host);
        $this->setUser($user);
        $this->connect($pass);
        if ($db)
            $this->setDb($db);
    }
    
    /**
     * Crea un nuevo objeto de la clase o devuelve una conexión activa.
     * Requiere parametros de conexión para crear un nuevo objeto. 
     * 
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $db
     * @return sql
     */
    public static function getInstance($host='', $user='', $pass='', $db='')
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($host,$user,$pass,$db);
        }
        return self::$instance;
    }
    
    /**
     * Conecta con la base de datos y crea un enlace ($link). 
     * Recibe como parámetro la contraseña del usuario.
     * 
     * @param string $pass
     * @throws sqlException
     */
    public function connect($pass) {
        $this->link = @mysqli_connect($this->getHost(), $this->getUser(), $pass, $this->getDb());

        if (!$this->link) {
            $msg = "No se pudo realizar la conexión en {$this->getUser()}@{$this->getHost()}. ".mysqli_connect_error();
            throw new sqlException(__FUNCTION__, $msg);
        } else {
            logger::writeLog(__FUNCTION__, "Conexión establecida en {$this->getUser()}@{$this->getHost()}");
        }
    }

    /**
     * Selecciona la base de datos.
     * 
     * @param string $db
     * @throws sqlException
     */
    public function setDb($db) {
        if (!@mysqli_select_db($this->getLink(), $db)) {
            $msg = "No se pudo conectar con la base de datos '$db'. ".  mysqli_error($this->getLink());
            throw new sqlException(__FUNCTION__, $msg);
        } else {
            $this->db = $db;
            logger::writeLog(__FUNCTION__, "Base de datos '{$this->getDb()}' seleccionada");
        }
    }
    
    /**
     * Cierra la conexión con la base de datos y elimina el enlace activo.
     * Mantiene el valor del resto de variables (host, user, db, etc.)
     * @throws sqlException
     */
    public function disconnect() {
        if (!@mysqli_close($this->getLink())) {
            $msg = "No se pudo cerrar la conexión en {$this->getUser()}@{$this->getHost()}";
            throw new sqlException(__FUNCTION__, $msg);
        } else {
            $this->link = NULL;
            logger::writeLog(__FUNCTION__, "Conexión cerrada en {$this->getUser()}@{$this->getHost()}");
        }
    }
    
    /**
     *  Al finalizar la ejecución se muestra el registro de acciones.
     */
    function __destruct() {
        if(self::LOG_ENABLED)
            logger::showLog();
    }
    
    public function getHost() {
        return $this->host;
    }

    public function setHost($host) {
        $this->host = $host;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getDb() {
        return $this->db;
    }

    public function getLink() {
        return $this->link;
    }


}



?>
