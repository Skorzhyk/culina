<?php

require_once 'model/AbstractModel.php';

class Ingredient extends AbstractModel
{
    protected $tableName = 'ingredient';

    protected $mapTableName = 'recipe_ingredients';

    protected $id;

    protected $name;

    protected $userId;

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

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserIngredients($userId)
    {
        $list = $this->getList(' WHERE user_id = ' . DataBase::SYM_QUERY . ' OR user_id IS NULL', [$userId]);

        $ingredients = [];
        foreach ($list as $item) {
            $ingredient['id'] = $item->getId();
            $ingredient['name'] = $item->getName();

            $ingredients[] = $ingredient;
        }

        return $ingredients;
    }

    public function getRecipeIngredients($recipeId)
    {
        return $this->db->select(
            "SELECT ri.id, ri.ingredient_id, i.name, ri.amount FROM " . $this->mapTableName . " ri INNER JOIN " . $this->tableName . " i ON ri.ingredient_id = i.id WHERE ri.recipe_id = " . DataBase::SYM_QUERY,
            [$recipeId]
        );
    }

    public function save() {
        $newId = $this->db->query(
            "INSERT INTO " . $this->tableName . " (name, user_id)
            VALUES (" . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ")",
            [$this->name, $this->userId]
        );

        $this->setId($newId);
    }

    public function addToRecipe($map)
    {
        if (empty($map['id'])) {
            $newId = $this->db->query(
                "INSERT INTO " . $this->mapTableName . " (recipe_id, ingredient_id, amount)
            VALUES (" . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY .")",
                [$map['recipe_id'], $map['ingredient_id'], $map['amount']]
            );

            return $newId;
        } else {
            $this->db->query(
                "UPDATE " . $this->mapTableName . " SET recipe_id = " . DataBase::SYM_QUERY . ", ingredient_id = " . DataBase::SYM_QUERY . ", amount = " . DataBase::SYM_QUERY . " WHERE id = " . DataBase::SYM_QUERY,
                [$map['recipe_id'], $map['ingredient_id'], $map['amount'], $map['id']]
            );
        }
    }

    public function deleteFromRecipe($mapId)
    {
        return $this->db->query(
            "DELETE FROM " . $this->mapTableName . " WHERE id = " . DataBase::SYM_QUERY,
            [$mapId]
        );
    }
}