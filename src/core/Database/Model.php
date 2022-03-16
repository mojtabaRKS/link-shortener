<?php

namespace Core\Database;

use PDO;
use Core\Concerns\Arrayable;
use Core\Exceptions\ModelNotFoundException;

abstract class Model implements Arrayable
{
    protected $connection;

    protected $primaryKey = 'id';

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
        
        $id = $this->saveToDb();

        return $this->find($id);
    }

    public function find($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->connection->getConnection()->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            throw new ModelNotFoundException($id);
        }

        return $this->map($result);
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
        $query = "SELECT COUNT(id) FROM {$this->table} WHERE " .
            implode(' ', array_map(function ($where) {
                return "{$where['attribute']} {$where['operator']} ?";
            }, $this->wheres));
        
        $stmt = $this->connection->getConnection()->prepare($query);
        $stmt->execute(array_map(function ($where) {
            return $where['value'];
        }, $this->wheres));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return intval($result['COUNT(id)']) > 0;
    }

    public function first()
    {
        $query = "SELECT * FROM {$this->table}";

        foreach ($this->wheres as $index => $where) {
            if ($index !== 0) {
                $query .= " {$where['logic']} ";
            }else {
                $query .= " WHERE ";
            }

            $query .= "{$where['attribute']} {$where['operator']} ?";
        }

        $stmt = $this->connection->getConnection()->prepare($query);

        $stmt->execute(array_map(function ($where) {
            return $where['value'];
        }, $this->wheres));
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->map($result);
    }

    public function get()
    {
        $query = "SELECT * FROM {$this->table}";
        
        if (count($this->wheres)) {
            $query .= "WHERE " .
                implode(' ', array_map(function ($where) {
                    return "{$where['attribute']} {$where['operator']} ?";
                }, $this->wheres));
        }

        $stmt = $this->connection->getConnection()->prepare($query);
        $stmt->execute(array_map(function ($where) {
            return $where['value'];
        }, $this->wheres));
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, array $data)
    {
        $query = "UPDATE {$this->table} SET " .
            implode(', ', array_map(function ($key) {
                return "{$key} = ?";
            }, array_keys($data))) .
            " WHERE id = ?";
        
        $stmt = $this->connection->getConnection()->prepare($query);
        return $stmt->execute(array_merge(array_values($data), [$id]));
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->connection->getConnection()->prepare($query);
        $stmt->execute([$id]);
    }

    public static function query()
    {
        return new static;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        if ($name == $this->primaryKey) {
            return $this->attributes[$this->primaryKey];
        }

        return null;
    }

    public function toArray(): array
    {
        return $this->attributes;
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

        return $this->connection->getConnection()->lastInsertId();
    }

    private function map(array $data)
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->attributes)) {
                $this->attributes[$key] = $value;
            }

            if ($key == $this->primaryKey) {
                $this->attributes[$this->primaryKey] = $value;
            }
        }

        return $this;
    }
}
