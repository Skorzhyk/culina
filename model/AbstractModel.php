<?php

require_once 'DataBase.php';

class AbstractModel
{
    protected $tableName;

    protected $db;

    public function __construct()
    {
        $this->db = DataBase::getDB();
    }

    public function load($value, $field = 'id')
    {
        $data = $this->db->selectRow(
            "SELECT * FROM " . $this->tableName . " WHERE " . $field . " = " . DataBase::SYM_QUERY,
            [$value]
        );

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $field = self::convertToCamelCase($key);
                $this->$field = $value;
            }
        }

        return $this;
    }

    public function delete()
    {
        return $this->db->query(
            "DELETE FROM " . $this->tableName . " WHERE id = " . DataBase::SYM_QUERY,
            [$this->getId()]
        );
    }

    public function getList($filter = '', $filterParams = [])
    {
        $data = $this->db->select(
            "SELECT * FROM " . $this->tableName . $filter,
            $filterParams
        );

        $list = [];
        if (!empty($data)) {
            foreach ($data as $itemData) {
                $className = get_class($this);
                $model = new $className();

                foreach ($itemData as $key => $value) {
                    $field = self::convertToCamelCase($key);
                    $model->$field = $value;
                }

                $list[$model->getId()] = $model;
            }
        }

        return $list;
    }

    private static function convertToCamelCase($string)
    {
        $parts = explode('_', $string);

        $result = array_shift($parts);

        if (!empty($parts)) {
            foreach ($parts as $part) {
                $result .= ucfirst($part);
            }
        }

        return $result;
    }
}