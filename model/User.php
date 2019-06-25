<?php

require_once 'model/AbstractModel.php';

class User extends AbstractModel
{
    protected $tableName = 'user';

    protected $id;

    protected $email;

    protected $password;

    protected $name;

    protected $surname;

    protected $favorites = '';

    protected $admin = 0;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    public function getFavorites()
    {
        return !empty($this->favorites) ? explode(',', $this->favorites) : [];
    }

    public function setFavorites($favorites)
    {
        $this->favorites = !empty($favorites) ? implode(',', $favorites) : '';
    }

    public function getAdmin()
    {
        return $this->admin;
    }

    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    public function save() {
        if (empty($this->getId())) {
            $newId = $this->db->query(
                "INSERT INTO " . $this->tableName . " (email, password, name, surname, favorites, admin)
            VALUES (" . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ", " . DataBase::SYM_QUERY . ")",
                [$this->email, $this->password, $this->name, $this->surname, $this->favorites, $this->admin]
            );

            if ($newId === false) {
                throw new Exception('Пользователь с указанным адресом электронной почты уже существует.');
            }

            $this->setId($newId);
        } else {
            $this->db->query(
                "UPDATE " . $this->tableName . " SET password = " . DataBase::SYM_QUERY . ", name = " . DataBase::SYM_QUERY . ", surname = " . DataBase::SYM_QUERY . ", favorites = " . DataBase::SYM_QUERY . ", admin = " . DataBase::SYM_QUERY . " WHERE id = " . DataBase::SYM_QUERY,
                [$this->password, $this->name, $this->surname, $this->favorites, $this->admin, $this->id]
            );
        }
    }

    public static function login($email, $password)
    {
        $user = new self();

        $user->load($email, 'email');

        if (empty($user->getId())) {
            throw new Exception('Пользователь с указанным адресом электронной почты не найден.');
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new Exception('Неверный пароль.');
        }

        return $user;
    }
}