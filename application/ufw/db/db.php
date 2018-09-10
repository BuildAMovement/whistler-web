<?php
namespace ufw\db;

/**
 *
 * @property mixed insert_id
 * @property int num_rows
 * @property int rows_affected
 *          
 */
class db
{

    protected $db_connections = array();

    protected$last_used_conn = null, $sw, $sw_total;

    protected static $instance;

    public $result, $errno, $last_query;

    public $query_log = false, $query_log_data = array();

    const QT_SELECT = 1;

    const QT_INSERT = 2;

    const QT_UPDATE = 3;

    const DB_ASSOC = MYSQLI_ASSOC;

    const DB_NUM = MYSQLI_NUM;

    const DB_BOTH = MYSQLI_BOTH;

    protected function __construct()
    {
        $this->db_connections = include APPLICATION_PATH . '/lib/__dbconns.php';
        if (getenv('DBLOG_ENV')) {
            register_shutdown_function(array(
                'db',
                'query_log_table'
            ));
            $this->query_log = true;
            $this->sw = new \ufw\utils\stopwatch();
            $this->sw_total = new \ufw\utils\stopwatch();
        }
    }

    /**
     *
     * Magic method __get(), specially supported values:
     * - insert_id - returns last inserted id
     * - rows_affected - result of mysqli_affected_rows();
     *
     * @param string $name            
     */
    public function __get($name)
    {
        switch ($name) {
            case 'insert_id':
                list ($value) = mysqli_fetch_array(mysqli_query($this->last_used_conn['resource'], "SELECT LAST_INSERT_ID()"), self::DB_NUM);
                break;
            case 'num_rows':
                if (is_resource($this->result))
                    $value = mysqli_num_rows($this->result);
                else
                    $value = null;
                break;
            case 'rows_affected':
                $value = mysqli_affected_rows($this->last_used_conn['resource']);
                break;
            default:
                $value = null;
        }
        return $value;
    }

    /**
     *
     * @return db
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
            // postavi logfile
            self::$instance->logfile = APPLICATION_PATH . "/log/error_dblog.tmp";
        }
        return self::$instance;
    }

    public function set_connection($conn_ident, $host, $user, $pass, $autoconnect = false, $dbname = false, $newlink = false)
    {
        $this->db_connections[$conn_ident] = array(
            'ident' => $conn_ident,
            'host' => $host,
            'user' => $user,
            'pass' => $pass,
            'dbname' => $dbname,
            'newlink' => $newlink
        );
        if ($autoconnect) {
            $this->connect($conn_ident);
        }
    }

    /**
     * Konektuje se na bazu ako nije ranije ili ako je postavljen reconnect parametar
     *
     * @param mixed $conn_ident
     *            resource ili string
     * @param boolean $reconnect            
     * @return resource
     */
    public function connect($conn_ident, $reconnect = false)
    {
        if (is_resource($conn_ident) && !$reconnect) {
            mysqli_ping($conn_ident);
            return $conn_ident;
        } else {
            $cn = & $this->db_connections[$conn_ident];
            if ($cn && (!$cn['resource'] || $reconnect)) {
                $cn['resource'] = mysqli_connect($cn['host'], $cn['user'], $cn['pass']);
                if ($cn['dbname'] && $cn['resource'])
                    mysqli_select_db($cn['resource'], $cn['dbname']);
                $this->last_used_conn = & $this->db_connections[$conn_ident];
                mysqli_query($cn['resource'], "SET NAMES {$cn['collation']}");
            }
            return $cn['resource'];
        }
    }

    public function fetch_all($query, $class_name = null, $conn_ident = null, $index_field = null, $one_property_only = '', $file_invoker = NULL, $line_invoker = NULL)
    {
        $this->result = $this->query($query, $conn_ident, $file_invoker, $line_invoker);
        if (!$class_name || !class_exists($class_name) || !is_subclass_of($class_name, '\ufw\model\record\base')) {
            $class_name = 'stdClass';
        }
        $buf = array();
        if ($one_property_only) {
            if ($index_field) {
                while ($row = $this->fetch_object($this->result, $class_name)) {
                    $buf[$row->$index_field] = $row->$one_property_only;
                }
            } else {
                while ($row = $this->fetch_object($this->result, $class_name)) {
                    $buf[] = $row->$one_property_only;
                }
            }
        } else {
            if ($index_field) {
                while ($row = $this->fetch_object($this->result, $class_name)) {
                    $buf[$row->$index_field] = $row;
                }
            } else {
                while ($row = $this->fetch_object($this->result, $class_name)) {
                    $buf[] = $row;
                }
            }
        }
        return $buf;
    }

    public function fetch_array($result, $mode = self::DB_ASSOC)
    {
        return mysqli_fetch_array($result, $mode);
    }

    public function fetch_object($result, $class_name = "stdClass", array $params = array())
    {
        if (is_subclass_of($class_name, '\ufw\model\record\base')) {
            $data = mysqli_fetch_array($result, self::DB_ASSOC);
            return $data ? new $class_name($data) : null;
        } else {
            return mysqli_fetch_object($result);
        }
    }

    public function free()
    {
        mysqli_free_result($this->result);
    }

    public function simple_insert($table_name, $params, $conn_ident = null, $file_invoker = NULL, $line_invoker = NULL, $query_execute = true)
    {
        if (class_exists($table_name) && is_subclass_of($table_name, '\ufw\model\record\base')) {
            $class_name = $table_name;
            $table_name = $class_name::getTableName();
        }
        $query = "INSERT INTO $table_name SET " . self::subquery($params);
        if ($query_execute)
            return $this->query($query, $conn_ident, $file_invoker, $line_invoker);
        else
            return $query;
    }

    public function simple_insert_update($table_name, $params, $conn_ident = null, $file_invoker = NULL, $line_invoker = NULL, $query_execute = true)
    {
        if (class_exists($table_name) && is_subclass_of($table_name, '\ufw\model\record\base')) {
            $class_name = $table_name;
            $table_name = $class_name::getTableName();
        }
        $subq = self::subquery($params);
        $query = "INSERT INTO $table_name SET $subq ON DUPLICATE KEY UPDATE $subq";
        if ($query_execute) {
            return $this->query($query, $conn_ident, $file_invoker, $line_invoker);
        } else {
            return $query;
        }
    }

    public function simple_select($table_name, $params, $info_hashclass = '\ufw\info_hash', $id_field_name = 'id', $hash_field_name = 'name', $conn_ident = null, $file_invoker = NULL, $line_invoker = NULL)
    {
        $class_name = "stdClass";
        if (class_exists($table_name) && is_subclass_of($table_name, '\ufw\model\record\base')) {
            $class_name = $table_name;
            $table_name = $class_name::getTableName();
        }
        $query = $this->simple_select_query($table_name, $params);
        /**
         *
         * @var \ufw\info_hash|\ufw\info_hasharray $data
         */
        $data = new $info_hashclass($id_field_name, $hash_field_name);
        $data->set_data($query, $class_name, $conn_ident);
        return $data;
    }

    public function simple_select_query($table_name, $params)
    {
        if (!isset($params['where'])) {
            $params['where'] = $params;
        }
        if (!isset($params['fields']))
            $params['fields'] = '*';
        if (is_array($params['fields']))
            $params['fields'] = join(', ', $params['fields']);
        if (!($subq = self::subquery($params['where'], ' AND '))) {
            $subq = '1';
        }
        return "SELECT {$params['fields']} FROM $table_name WHERE $subq " . (isset($params['orderby']) ? 'ORDER BY ' . join(', ', (array) $params['orderby']) . ' ' : '') . (isset($params['limit']) ? 'LIMIT ' . $params['limit'] : '');
    }

    public function simple_update($table_name, $params, $conn_ident = null, $file_invoker = NULL, $line_invoker = NULL)
    {
        if (class_exists($table_name) && is_subclass_of($table_name, '\ufw\model\record\base')) {
            $class_name = $table_name;
            $table_name = $class_name::getTableName();
        }
        return $this->simple_update_pk($table_name, $params, array(
            'id' => $params['id']
        ), $conn_ident, $file_invoker, $line_invoker);
    }

    public function simple_update_pk($table_name, $params, $params_pk, $conn_ident = null, $file_invoker = NULL, $line_invoker = NULL)
    {
        if (class_exists($table_name) && is_subclass_of($table_name, '\ufw\model\record\base')) {
            $class_name = $table_name;
            $table_name = $class_name::getTableName();
        }
        $query = "UPDATE $table_name SET " . self::subquery($params) . " WHERE " . self::subquery($params_pk, ' AND ');
        return $this->query($query, $conn_ident, $file_invoker, $line_invoker);
    }

    /**
     *
     * @param string $table_name            
     * @param array $params_pk            
     * @param string $conn_ident            
     * @param string $file_invoker            
     * @param string $line_invoker            
     */
    function simple_delete($table_name, $params_pk, $conn_ident = null, $file_invoker = NULL, $line_invoker = NULL)
    {
        if (class_exists($table_name) && is_subclass_of($table_name, '\ufw\model\record\base')) {
            $class_name = $table_name;
            $table_name = $class_name::getTableName();
        }
        $query = "DELETE FROM $table_name WHERE " . self::subquery($params_pk, ' AND ');
        return $this->query($query, $conn_ident, $file_invoker, $line_invoker);
    }

    /**
     * Static function
     *
     * @param array $array            
     * @param string $glue            
     */
    public static function subquery($array, $glue = ",\n")
    {
        $query = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $query[] = "$key IN (" . join(', ', self::quote($value)) . ")";
            } else {
                $query[] = "$key = " . self::quote($value);
            }
        }
        return join($glue, $query);
    }

    /**
     *
     * @param string $query            
     * @param mixed $conn_ident
     *            string ili resource - identifikacije konekcije ili sam resurs konekcije
     * @param string $file_invoker            
     * @param string $line_invoker            
     * @param integer $retries            
     */
    public function query($query, $conn_ident = null, $file_invoker = null, $line_invoker = null, $retries = 0)
    {
        if ($this->query_log)
            $this->sw->reset();
        
        $query_type = self::QT_SELECT;
        if (preg_match('/^\s*(insert|delete|update|replace)\s/i', $query)) {
            $query_type = self::QT_UPDATE;
            if (preg_match('/^\s*(insert|replace)\s/i', $query)) {
                $query_type = self::QT_INSERT;
            }
        }
        
        if (is_resource($conn_ident)) {
            $dbhandle = $conn_ident;
        } else {
            if (!$conn_ident && !$this->last_used_conn)
                $conn_ident = 'default';
            if (isset($conn_ident) && $conn_ident && strcmp($conn_ident, '__last_used')) {
                $this->last_used_conn = & $this->db_connections[$conn_ident];
            }
            $dbhandle = $this->connect($this->last_used_conn['ident']);
        }
        $this->last_query = $query;
        $this->result = mysqli_query($dbhandle, $query);
        $this->errno = mysqli_errno($dbhandle);
        if ($this->errno && ($this->errno != 1062)) { // 1062 - duplicate entry
            if ((($this->errno == 2006) || ($this->errno == 2013)) && !$retries) { // Mysql server gone away (2006), Lost connection to Mysql during query (2013)
                $this->connect($conn_ident, true);
                $this->query($query, $conn_ident, $file_invoker, $line_invoker, $retries++);
            } else {
                $str = "Date and time: " . date("Y-m-d H:i:s") . "\n" . "Conn: {$this->last_used_conn['ident']}\n" . "Query: " . $query . "\n" . "Error: " . mysqli_error($dbhandle) . " ($this->errno)\n" . ($file_invoker && $line_invoker ? "File: Line $line_invoker in $file_invoker\n" : "") . "http://{$_SERVER['SERVER_NAME']}{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}\n" . ($_SERVER['HTTP_REFERER'] ? "Referer: {$_SERVER['HTTP_REFERER']}\n" : '') . ($_SERVER['HTTP_USER_AGENT'] ? "Browser: {$_SERVER['HTTP_USER_AGENT'] }\n" : '');
                if ($_POST) {
                    $str .= "POST: ";
                    foreach ($_POST as $key => $value)
                        $str .= "$key=$value&";
                    $str .= "\n";
                }
                error_log("$str\n", 3, $this->logfile);
            }
        }
        
        if ($this->query_log) {
            $this->query_log_data[] = array(
                'query' => $query,
                'rows' => $this->errno ? 'err: ' . $this->errno : ($query_type == QT_SELECT ? $this->num_rows : $this->rows_affected),
                'file' => @strncmp($file_invoker, APPLICATION_PATH) ? $file_invoker : substr($file_invoker, strlen(APPLICATION_PATH)),
                'line' => $line_invoker,
                'time' => $this->sw->elapsed_hr(),
                'total' => $this->sw_total->elapsed_hr()
            );
        }
        
        return $this->result;
    }

    /**
     * Staticka funkcija
     * Vraca escape-ovan $value, pogodan za upotrebu u sql stejtmentima
     *
     * @param mixed $value            
     * @return string
     */
    public static function quote($value)
    {
        if (is_int($value)) {
            return $value;
        } elseif (is_float($value)) {
            return sprintf('%F', $value);
        } elseif (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = static::quote($val);
            }
            return $value;
        } elseif (!strcmp($value, 'nil')) {
            return 'NULL';
        } elseif ($value instanceof \ufw\db\expression) {
            return (string) $value;
        }
        if (is_resource(self::instance()->last_used_conn['resource'])) {
            $ret = mysqli_real_escape_string(self::instance()->last_used_conn['resource'], $value);
            if (!$ret && $value) {
                // umrela konekcija, reotvori je
                $resource = self::instance()->connect(self::instance()->last_used_conn['ident'], true);
                if ($resource)
                    $ret = mysqli_real_escape_string($resource, $value);
                else
                    $ret = addslashes($value);
            }
            return "'$ret'";
        } else {
            return "'" . addslashes($value) . "'";
        }
    }

    public static function query_log_table()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            return;
        $dbc = static::instance();
        echo '<style type="text/css"> .query-log-table TH, .query-log-table TD { padding: 4px; } </style>';
        echo '<table class="query-log-table" border="1" style="border-collapse: collapse; width: 990px;">';
        foreach ($dbc->query_log_data as $key => $row) {
            if ($key == 0) {
                echo '<tr><th>No.</th><th>' . join('</th><th>', array_keys($row)) . '</th></tr>';
            }
            echo '<tr><td>', ($key + 1), '</td><td>', join('</td><td>', $row), '</td></tr>';
        }
        echo '</table>';
    }
}