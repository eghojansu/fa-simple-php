<?php

/**
 * Database
 *
 * Easy access to perform CRUD operation on database
 */
class Database
{
    protected $config;
    protected $pdo;
    protected $errors = [];
    protected $logs = [];
    protected $creating = false;

    /**
     * @param array $config = [
     *        'type'=> 'mysql',
     *        'dsn'=>[
     *            'host'=>'host name',
     *            'dbname'=>'db name',
     *        ],
     *        'username'=>'username',
     *        'password'=>'password',
     *        'options' => [],
     *    ]
     */
    public function __construct(array $config)
    {
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
     * Create the database
     */
    public function create()
    {
        $this->pdo = null;
        $this->creating = true;
        $this->exec('create database if not exists '.$this->config['dsn']['dbname']);
        $this->exec('use '.$this->config['dsn']['dbname']);

        return $this;
    }

    /**
     * Drop database
     */
    public function drop()
    {
        $this->pdo = null;
        $this->creating = true;
        $this->exec('drop database if exists '.$this->config['dsn']['dbname']);
        $this->pdo = null;

        return $this;
    }

    /**
     * Import sql file
     * @param  string $file
     */
    public function import($file)
    {
        if ($sql = App::instance()->read($file)) {
            // TODO: split sql to smaller chunks
            $this->exec($sql);
        }

        return $this;
    }

    /**
     * Get PDO connection
     * @return PDO
     */
    public function pdo()
    {
        if (!$this->pdo) {
            // database configuration
            $db  = $this->config;
            if ($this->creating) {
                unset($db['dsn']['dbname']);
                $this->creating = false;
            }
            // dsn
            $dsn = '';
            foreach ($db['dsn'] as $key => $value) {
                $dsn .= ($dsn?';':'').$key.'='.$value;
            }
            $dsn = $db['type'].':'.$dsn;
            // construct PDO object
            try {
                $this->pdo = new PDO($dsn, $db['username'], $db['password'], $db['options']);
            } catch (Exception $e) {
                echo 'DB connection error!';
                if (App::instance()->get('debug')) {
                    echo PHP_EOL . $e->getMessage();
                }
            }
        }

        return $this->pdo;
    }

    /**
     * Exec sql
     * @param  string $sql
     * @return mixed
     */
    public function exec($sql)
    {
        $pdo = $this->pdo();
        $result = $pdo->exec($sql);
        $info = $pdo->errorInfo();
        $this->log($sql, [], $info);

        return $result;
    }

    /**
     * Get next id for table
     * @param  string $table
     * @param  string $column
     * @param  callable $formatter
     * @param  array  $criteria
     * @return string|int
     */
    public function nextID($table, $column, $formatter = null, array $criteria = [])
    {
        $record = $this->selectOne($column, $table, $criteria, 'order by '.$column.' desc limit 1');
        $nextID = $formatter?call_user_func_array($formatter, [$record]):($record?$record[$column]*1+1:1);

        return $nextID;
    }

    /**
     * Count table records
     * @param  string $table
     * @param  array  $criteria
     * @param  string $options
     * @return int
     */
    public function count($table, array $criteria = [], $options = '')
    {
        $record = $this->select('count(*) as count', $table, $criteria, $options);

        return $record?$record[0]['count']:0;
    }

    /**
     * Paginate table
     * @param  string  $table
     * @param  array   $criteria
     * @param  integer $page
     * @param  integer $limit
     * @param  string  $options
     * @param  string  $column
     * @return array subset
     */
    public function paginate($table, array $criteria = [], $options = '', $page = 1, $limit = 20, $column = '*')
    {
        $page = abs($page);
        $offset = $page * $limit - $limit;
        $options .= ' limit '.$limit.' offset '.$offset;

        $page = [
            'page' => $page,
            'limit' => $limit,
            'start' => $offset + 1,
            'data' => $this->select($column, $table, $criteria, $options),
            'count' => 0,
            'total' => 1,
        ];

        $page['count'] = count($page['data']);
        $page['total'] = (int) ceil($this->count($table, $criteria)/$limit);

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
     * Populate record and transform to key=value pair array
     * @param  string $table
     * @param  string $key      column name as key
     * @param  string|callable|null|array $value    column name as value
     * @param  array  $criteria
     * @param  string $options
     * @return array
     */
    public function populate($table, $key, $value = null, array $criteria = [], $options = '')
    {
        $data = [];
        $records = $this->find($table, $criteria, $options);
        foreach ($records as $record) {
            if (is_null($value)) {
                $v = $record[$key];
            } elseif (is_array($value)) {
                if (empty($value)) {
                    $v = $record;
                } else {
                    $v = [];
                    foreach ($value as $k) {
                        if (!isset($record[$k])) {
                            throw new Exception("Column $k was not exists");
                        }
                        $v[$k] = $record[$k];
                    }
                }
            } elseif (is_callable($value)) {
                $v = call_user_func_array($value, [$record]);
            } else {
                if (!isset($record[$value])) {
                    throw new Exception("Column $k was not exists");
                }
                $v = $record[$value];
            }
            $data[$record[$key]] = $v;
        }

        return $data;
    }

    /**
     * Get log
     * @param  boolean $asString
     * @param  string  $delimiter
     * @return string|array
     */
    public function getLog($asString = true, $delimiter = '<br>')
    {
        return $asString?implode($delimiter, $this->logs):$this->logs;
    }

    /**
     * Get error
     * @param  boolean $asString
     * @param  string  $delimiter
     * @return string|array
     */
    public function getError($asString = true, $delimiter = '<br>')
    {
        if ($asString) {
            $str = '';
            foreach ($this->errors as $error) {
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
        if (PHP_SAPI === 'cli') {
            echo implode(PHP_EOL, $this->logs);
        } else {
            $body = '';
            $no = 1;
            foreach ($this->logs as $key => $value) {
                $body .= '<tr>'
                    . '<td>'.$no.'</td>'
                    . '<td>'.$value.'</td>'
                    . '</tr>';
                $no++;
            }

            echo <<<HTML
<div class="database-log" style="padding: 10px; border-radius: 8px; border: solid 1px #ccc; margin-bottom: 10px">
    <p style="margin: 0 0 10px"><strong>Database Log</strong></p>
    <table style="width: 100%; border-collapse: collapse" border="1">
        <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Query</th>
            </tr>
        </thead>
        <tbody>$body</tbody>
    </table>
</div>
HTML;
        }

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
        if (PHP_SAPI === 'cli') {
            $i = 1;
            foreach ($this->errors as $error) {
                echo ($i===1?null:PHP_EOL).'('.$error[1].') '.$error[2];
            }
        } else {
            $body = '';
            $no = 1;
            foreach ($this->errors as $key => $value) {
                $body .= '<tr>'
                    . '<td>'.$no.'</td>'
                    . '<td>'.$value[0].'</td>'
                    . '<td>'.$value[1].'</td>'
                    . '<td>'.$value[2].'</td>'
                    . '</tr>';
                $no++;
            }

            echo <<<HTML
<div class="database-error" style="padding: 10px; border-radius: 8px; border: solid 1px #ccc; margin-bottom: 10px">
    <p style="margin: 0 0 10px"><strong>Database Error</strong></p>
    <table style="width: 100%; border-collapse: collapse" border="1">
        <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th style="width: 100px">ANSI Code</th>
                <th style="width: 100px">Code</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>$body</tbody>
    </table>
</div>
HTML;
        }

        if ($halt) {
            die;
        }
    }

    /**
     * Log query if debug active
     * @param  string $sql
     * @param  array  $params
     * @param  array  $error
     */
    public function log($sql, array $params, array $error)
    {
        $app = App::instance();
        if ($app->debug()) {
            $no = -1;
            $params = array_merge($params, []);
            $this->logs[] = preg_replace_callback('/(?<qm>\?)|(?<p>:\w+)/', function($match) use (&$no, $params) {
                $no++;
                if (isset($match['qm']) && isset($params[$no])) {
                    return "'".$params[$no]."'";
                } elseif (isset($match['p']) && isset($params[$match['p']])) {
                    return "'".$params[$match['p']]."'";
                } else {
                    return isset($match['qm'])?'?':':'.$match['p'];
                }
            }, $sql);
            if ('00000' !== $error[0]) {
                $this->errors[] = $error;

                if (!$app->get('continueOnDBError')) {
                    $this->dumpError();
                }
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