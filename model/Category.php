<?php

require_once 'model/AbstractModel.php';

class Category extends AbstractModel
{
    protected $tableName = 'category';

    protected $typeTableName = 'category_type';

    protected $id;

    protected $name;

    protected $type;

    protected $parentCategoryId;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getParentCategoryId()
    {
        return $this->parentCategoryId;
    }

    public function setParentCategoryId($parentCategoryId)
    {
        $this->parentCategoryId = $parentCategoryId;
    }

    public function getTypes()
    {
        $data = $this->db->select(
            "SELECT * FROM " . $this->typeTableName,
            []
        );

        $types = [];

        foreach ($data as $item) {
            $types[$item['id']] = $item['name'];
        }

        return $types;
    }

    public function getCategoryTree()
    {
        $rootCategories = $this->getList(' WHERE parent_category_id IS NULL');
        $tree = [];

        foreach ($rootCategories as $category) {
            $tree[$category->getType()][$category->getId()] = $category->getChildren();
        }

        return $tree;
    }

    public function getChildren()
    {
        $childrenTree = [];
        $categories = $this->getList(' WHERE parent_category_id = ' . DataBase::SYM_QUERY, [$this->getId()]);

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $childrenTree[$category->getId()] = $category->getChildren();
            }
        }

        return $childrenTree;
    }
}