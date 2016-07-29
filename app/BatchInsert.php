<?php

/**
 * Batch insert
 */
class BatchInsert
{
    protected $db;
    protected $table;
    protected $chunk;
    protected $counter = 0;
    protected $queryExecuted = 0;
    protected $last = true;
    protected $log;
    protected $info;
    protected $data = [];

    /**
     * Construct
     * @param Database $db
     * @param string   $table
     * @param integer  $chunk chunk size
     */
    public function __construct(Database $db, $table, $chunk = 20)
    {
        $this->db = $db;
        $this->table = $table;
        $this->chunk = $chunk;
    }

    /**
     * Add data
     * @param array $data
     */
    public function add(array $data)
    {
        $this->data[] = $data;
        $this->counter++;

        // to prevent memory over usage
        if ($this->counter % $this->chunk === 0) {
            // auto execute
            $this->last = false;
            $this->execute();
            $this->last = true;
        }

        return $this;
    }

    /**
     * Merge data
     * @param  array  $data
     */
    public function merge(array $data)
    {
        foreach ($data as $datum) {
            $this->add($datum);
        }

        return $this;
    }

    /**
     * Data count
     * @return int
     */
    public function count()
    {
        return $this->counter;
    }

    /**
     * Execute
     * @return bool
     */
    public function execute()
    {
        if ($this->data) {
            $pdo = $this->db->pdo();
            $first = $this->prepare([array_shift($this->data)]);
            $query = $pdo->prepare($first['sql']);
            $query->execute($first['data']);
            $info = $query->errorInfo();
            $success = $info[0] === '00000';

            if ($success) {
                $this->queryExecuted++;
                $next = $this->prepare($this->data);
                $query = $pdo->prepare($next['sql']);
                $query->execute($next['data']);
                $info = $query->errorInfo();
                $success = $info[0] === '00000';
                $this->data = [];
                $this->queryExecuted++;
            }
            $this->log = "$first[sql] {repeated {queryExecuted} time(s)}";
            $this->info = $info;
        }
        if ($this->last) {
            $this->db->log(str_replace('{queryExecuted}', $this->queryExecuted, $this->log), [], $this->info);
        }

        return $this->queryExecuted > 0;
    }

    /**
     * Generate sql insert batch
     * @param  array  $data  array or array
     * @return array        [sql=>'sql',data=>[]]
     */
    protected function prepare(array $data)
    {
        $first = reset($data);
        $sql = 'insert into '.$this->table.' ('.implode(',', array_keys($first)).') values ';
        $placeholder = '('.str_repeat('?,', count($first)-1).'?),'.PHP_EOL;
        $params = [];
        foreach ($data as $key => $value) {
            $sql .= $placeholder;
            $params = array_merge($params, array_values($value));
        }
        $sql = rtrim($sql, PHP_EOL);
        $sql = rtrim($sql, ',');

        return [
            'sql'=>$sql,
            'data'=>$params,
        ];
    }
}