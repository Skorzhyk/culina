<?php

require_once 'model/AbstractModel.php';

class RecipeImage extends AbstractModel
{
    protected $tableName = 'recipe_gallery';

    protected $id;

    protected $recipeId;

    protected $image;

    protected $orderNumber;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getRecipeId()
    {
        return $this->recipeId;
    }

    public function setRecipeId($recipeId)
    {
        $this->recipeId = $recipeId;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    public function save() {
        if (empty($this->getId())) {
            $newId = $this->db->query(
                "INSERT INTO " . $this->tableName . " (recipe_id, image, order_number)
            VALUES (" . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ")",
                [$this->recipeId, $this->image, $this->orderNumber]
            );

            $this->setId($newId);
        } else {
            $this->db->query(
                "UPDATE " . $this->tableName . " SET image = " . DataBase::SYM_QUERY . " WHERE id = " . DataBase::SYM_QUERY,
                [$this->image, $this->id]
            );
        }
    }
}