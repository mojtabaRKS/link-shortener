<?php

namespace Core\Database;

use Core\Exceptions\ModelNotFoundException;

abstract class Model
{
    protected $connection;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public const TIMESTAMPS = [
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $wheres = [];

    public function __construct()
    {
        $this->connection = Connection::getInstance();
        $this->attributes = array_flip($this->attributes);
    }

    /**
     * @param array $data
     */
    public function save(array $data)
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->attributes)) {
                $this->attributes[$key] = $value;
            }
        }

        foreach (self::TIMESTAMPS as $timestamp) {
            $this->attributes[$timestamp] = date('Y-m-d H:i:s');
        }
        
        $this->saveToDb();
    }
    
    protected function saveToDb()
    {
        $query = "INSERT INTO {$this->table} (" .
            implode(', ', array_keys($this->attributes)) .
            ") VALUES (" .
            implode(', ', array_fill(0, count($this->attributes), '?')) .
            ")";
        
        $this->connection->getConnection()
            ->prepare($query)
            ->execute(array_values($this->attributes));
    }

    public function find($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->connection->getConnection()->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if (!$result) {
            throw new ModelNotFoundException($id);
        }

        return $result;
    }

    public function where($attribute, $value, $operator = '=', $logic = 'AND')
    {
        $this->wheres[] = [
            'attribute' => $attribute,
            'value' => $value,
            'operator' => $operator,
            'logic' => $logic,
        ];

        return $this;
    }
    
    public function exists()
    {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE " .
            implode(' ', array_map(function ($where) {
                return "{$where['attribute']} {$where['operator']} ?";
            }, $this->wheres));
        
        $stmt = $this->connection->getConnection()->prepare($query);
        $stmt->execute(array_map(function ($where) {
            return $where['value'];
        }, $this->wheres));
        $result = $stmt->fetch();
        
        return $result;
    }

}
