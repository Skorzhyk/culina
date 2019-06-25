<?php

require_once 'model/AbstractModel.php';

class Step extends AbstractModel
{
    protected $tableName = 'step';

    protected $id;

    protected $recipeId;

    protected $orderNumber;

    protected $image = '';

    protected $video = '';

    protected $description;

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

    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function setVideo($video)
    {
        $this->video = $video;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function save() {
        if (empty($this->getId())) {
            $newId = $this->db->query(
                "INSERT INTO " . $this->tableName . " (recipe_id, order_number, image, video, description)
            VALUES (" . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ")",
                [$this->recipeId, $this->orderNumber, $this->image, $this->video, $this->description]
            );

            $this->setId($newId);
        } else {
            $this->db->query(
                "UPDATE " . $this->tableName . " SET order_number = " . DataBase::SYM_QUERY . ", image = " . DataBase::SYM_QUERY . ", video = " . DataBase::SYM_QUERY . ", description = " . DataBase::SYM_QUERY . " WHERE id = " . DataBase::SYM_QUERY,
                [$this->orderNumber, $this->image, $this->video, $this->description, $this->id]
            );
        }
    }
}