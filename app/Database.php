<?php

/**
 * Database
 *
 * Easy access to perform CRUD operation on database
 */
class Database
{
    protected $app;
    protected $config;
    protected $pdo;
    protected $errors = [];
    protected $logs = [];

    /**
     * @param array $config = [
     *        'type'=> 'mysql',
     *        'dsn'=>[
     *            'host'=>'host name',
     *            'dbname'=>'db name',
     *        ],
     *        'username'=>'username',
     *        'password'=>'password',
     *        'options' => []
     *    ]
     */
    public function __construct(App $app, array $config)
    {
        $this->app    = $app;
        $this->config = array_replace_recursive([
            'type'     => 'mysql',
            'dsn' => [
                'host' => 'localhost',
                'dbname' => null,
            ],
            'username' => 'root',
            'password' => 'root',
            'options'  => [],
        ], $config);
    }

    /**
     * Get PDO connection
     */
    public function pdo()
    {
        if (!$this->pdo) {
            // database configuration
            $db  = $this->config;
            // dsn
            $dsn = '';
            foreach ($db['dsn'] as $key => $value) {
                $dsn .= ($dsn?';':'').$key.'='.$dbname;
            }
            $dsn = $db['type'].':'.$dsn;
            // construct PDO object
            try {
                $this->pdo = new PDO($dsn, $db['username'], $db['password'], $db['options']);
            } catch (Exception $e) {
                echo 'DB connection error!';
                if ($this->app->get('debug')) {
                    echo PHP_EOL . $e->getMessage();
                }
            }
        }

        return $this->pdo;
    }

    /**
     * Get next id for table
     * @param  string $table
     * @param  string $column
     * @param  array  $criteria
     * @param  callable $formatter
     * @return string|int
     */
    public function nextID($table, $column, array $criteria = [], $formatter = null)
    {
        $record = $this->selectOne($column, $table, $criteria, 'order by '.$column.' desc limit 1');
        $nextID = $formatter?call_user_func_array($formatter, [$record]):($record?$record[$column]*1+1:1);

        return $nextID;
    }

    /**
     * Paginate table
     * @param  string  $table
     * @param  array   $criteria
     * @param  integer $page
     * @param  integer $limit
     * @param  string  $column
     * @return array subset
     */
    public function paginate($table, array $criteria = [], $page = 1, $limit = 20, $column = '*')
    {
        $page = abs($page);
        $offset = $page * $limit - $limit;
        $options = 'limit '.$limit.' offset '.$offset;

        $page = [
            'page' => $page,
            'limit' => $limit,
            'start' => $offset + 1,
            'data' => $this->select($column, $table, $criteria, $options),
            'count' => 0,
            'total' => 1,
        ];

        if (($page['count'] = count($page['data'])) === $limit) {
            $count = $this->select('count(*) as count', $table, $criteria, '');
            $page['total'] = (int) ceil($count[0]['count']/$limit);
        }

        return $page;
    }

    /**
     * Find record in table
     * @param  string $table
     * @param  array  $criteria
     * @param  string $options
     * @return array
     */
    public function find($table, array $criteria = [], $options = '')
    {
        return $this->select('*', $table, $criteria, $options);
    }

    /**
     * Find one record
     * @param  string $table
     * @param  array  $criteria
     * @param  string $options
     * @return array
     */
    public function findOne($table, array $criteria = [], $options = '')
    {
        return $this->selectOne('*', $table, $criteria, $options);
    }

    /**
     * Select one record
     * @param  string $column
     * @param  string $table
     * @param  array  $criteria
     * @param  string $options
     * @return array
     */
    public function selectOne($column, $table, array $criteria = [], $options = '')
    {
        if (false === strpos(strtolower($options), 'limit')) {
            $options .= ' limit 1';
        }

        $records = $this->select($column, $table, $criteria, $options);

        return $records?$records[0]:[];
    }

    /**
     * Select records
     * @param  string $column
     * @param  string $table
     * @param  array  $criteria
     * @param  string $options
     * @return array
     */
    public function select($column, $table, array $criteria = [], $options = '')
    {
        $sql = 'select '.$column.' from '.$table;
        if ($criteria) {
            $sql .= ' where '.array_shift($criteria);
        }
        $sql .= ' '.$options;

        $query = $this->pdo()->prepare($sql);
        $query->execute($criteria);
        $this->log($sql, $criteria, $query->errorInfo());

        return $query->fetchAll(PDO::FETCH_ASSOC)?:[];
    }

    /**
     * Insert record
     * @param  string $table
     * @param  array  $data
     * @return bool
     */
    public function insert($table, array $data)
    {
        $data = array_filter($data, [$this, 'clearEmpty']);
        $sql = 'insert into '.$table.'('.implode(',', array_keys($data)).')'
             . ' values ('.str_repeat('?,', count($data)-1).'?)';
        $params = array_values($data);

        $query = $this->pdo()->prepare($sql);
        $query->execute($params);
        $this->log($sql, $params, $query->errorInfo());

        return '00000'===$query->errorCode();
    }

    /**
     * Update record
     * @param  string $table
     * @param  array  $data
     * @param  array  $criteria
     * @return bool
     */
    public function update($table, array $data, array $criteria = [])
    {
        $data = array_filter($data, [$this, 'clearEmpty']);
        $sql = 'update '.$table.' set ';
        $params = array_values($data);
        foreach ($data as $key => $value) {
            $sql .= $key .' = ?,';
        }
        $sql = rtrim($sql, ',');

        if ($criteria) {
            $sql .= ' where '.array_shift($criteria);
            $params = array_merge($params, array_values($criteria));
        }

        $query = $this->pdo()->prepare($sql);
        $query->execute($params);
        $this->log($sql, $params, $query->errorInfo());

        return '00000'===$query->errorCode();
    }

    /**
     * Delete record
     * @param  string $table
     * @param  array  $criteria
     * @return array
     */
    public function delete($table, array $criteria = [])
    {
        $sql = 'delete from '.$table;
        $params = [];

        if ($criteria) {
            $sql .= ' where '.array_shift($criteria);
            $params = array_merge($params, array_values($criteria));
        }

        $query = $this->pdo()->prepare($sql);
        $query->execute($params);
        $this->log($sql, $params, $query->errorInfo());

        return '00000'===$query->errorCode();
    }

    /**
     * Get log
     * @param  boolean $asString
     * @param  string  $delimiter
     * @return string|array
     */
    public function getLog($asString = true, $delimiter = PHP_EOL)
    {
        return $asString?implode($delimiter, $this->$logs):$this->$logs;
    }

    /**
     * Get error
     * @param  boolean $asString
     * @param  string  $delimiter
     * @return string|array
     */
    public function getError($asString = true, $delimiter = PHP_EOL)
    {
        if ($asString) {
            $str = '';
            foreach ($this->$errors as $error) {
                $str .= $delimiter.'('.$error[1].') '.$error[2];
            }

            return $str;
        }

        return $this->$errors;
    }

    /**
     * Dump log
     * @param  boolean $halt
     */
    public function dumpLog($halt = false)
    {
        echo '<pre>';
        echo $this->getLog();
        echo '</pre>';

        if ($halt) {
            die;
        }
    }

    /**
     * Dump error
     * @param  boolean $halt
     */
    public function dumpError($halt = false)
    {
        echo '<pre>';
        var_dump($this->$errors);
        echo '</pre>';

        if ($halt) {
            die;
        }
    }

    /**
     * Populate record and transform to key=value pair array
     * @param  string $table
     * @param  string $key      column name as key
     * @param  string $value    column name as value
     * @param  array  $criteria
     * @param  string $options
     * @return array
     */
    public function populate($table, $key, $value, array $criteria = [], $options = '')
    {
        $data = [];
        $records = $this->find($table, $criteria, $options);
        foreach ($records as $record) {
            $data[$record[$key]] = $record[$value];
        }

        return $data;
    }

    /**
     * Log query if debug active
     * @param  string $sql
     * @param  array  $params
     * @param  array  $error
     */
    protected function log($sql, array $params, array $error)
    {
        if ($this->app->get('debug')) {
            $no = -1;
            $params = array_merge($params, []);
            $this->logs[] = preg_replace_callback('/(?<qm>\?)|(?<p>:\w+)/', function($match) use (&$no, $params) {
                $no++;
                if (isset($match['qm']) && isset($params[$no])) {
                    return "'".$params[$no]."'";
                } elseif (isset($match['p']) && isset($params[$match['p']])) {
                    return "'".$params[$match['p']]."'";
                } else {
                    return null;
                }
            }, $sql);
            if ('00000' !== $error[0]) {
                $this->errors[] = $error;
            }
        }
    }

    /**
     * Check is value is empty for used with array_filter
     * @param  string $data
     * @return bool
     */
    protected function clearEmpty($data)
    {
        return !($data === '' or $data === null);
    }
}