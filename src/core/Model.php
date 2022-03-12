<?php

namespace Core;

abstract class Model
{
    protected $connection;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public const TIMESTAMPS = [
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    public function __construct()
    {
        $this->connection = Database::getInstance();
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
}
