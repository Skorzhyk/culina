<?php

require_once 'model/AbstractModel.php';
require_once 'model/User.php';

class Comment extends AbstractModel
{
    protected $tableName = 'recipe_comment';

    protected $id;

    protected $text;

    protected $recipeId;

    protected $userId;

    protected $userName;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getRecipeId()
    {
        return $this->recipeId;
    }

    public function setRecipeId($recipeId)
    {
        $this->recipeId = $recipeId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function save() {
        if (empty($this->getId())) {
            $newId = $this->db->query(
                "INSERT INTO " . $this->tableName . " (text, recipe_id, user_id)
            VALUES (" . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ")",
                [$this->text, $this->recipeId, $this->userId]
            );

            $this->setId($newId);
        } else {
            $this->db->query(
                "UPDATE " . $this->tableName . " SET text = " . DataBase::SYM_QUERY . ", recipe_id = " . DataBase::SYM_QUERY . ", user_id = " . DataBase::SYM_QUERY . " WHERE id = " . DataBase::SYM_QUERY,
                [$this->text, $this->recipeId, $this->userId, $this->id]
            );
        }
    }

    public function getList($filter = '', $filterParams = [])
    {
        $comments = parent::getList($filter, $filterParams);

        $userObject = new User();
        $users = $userObject->getList();

        foreach ($comments as $comment) {
            $user = $users[$comment->getUserId()];
            $comment->setUserName($user->getName() . ' ' . $user->getSurname());
        }

        return $comments;
    }
}