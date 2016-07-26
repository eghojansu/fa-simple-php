<?php

/**
 * Batch insert
 */
class BatchInsert
{
    protected $db;
    protected $table;
    protected $chunk;
    protected $data = [];

    /**
     * Construct
     * @param Database $db
     * @param string   $table
     * @param integer  $chunk chunk size
     */
    public function __construct(Database $db, $table, $chunk = 100)
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
        return count($this->data);
    }

    /**
     * Execute
     * @return bool
     */
    public function execute()
    {
        $pdo = $this->db->pdo();
        $data = $this->data;
        $first = $this->prepare([array_shift($data)]);
        $query = $pdo->prepare($first['sql']);
        $query->execute($first['data']);
        $info = $query->errorInfo();
        $success = $info[0] === '00000';

        $queryExecuted = 0;
        if ($success) {
            $queryExecuted++;
            $chunksData = array_chunk($data, $this->chunk);
            foreach ($chunksData as $key => $data) {
                $current = $this->prepare($data);
                $query = $pdo->prepare($current['sql']);
                $query->execute($current['data']);
                $info = $query->errorInfo();
                $success = $info[0] === '00000';
                if (!$success) {
                    break;
                }
                $queryExecuted++;
            }
            unset($chunksData,$current);
        }
        $log = "$first[sql] {repeated $queryExecuted time(s)}";
        $this->db->log($log, [], $info);

        return $success;
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