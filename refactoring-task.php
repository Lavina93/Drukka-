<?php

class MysqliDatabaseDriver
{
    public function query($queryString, array $parameters): array
    {
        return [['id' => 1, 'username' => 'msqli-test']];
    }
}

class PdoDatabaseDriver
{
    public function query($queryString, array $parameters): array
    {
        return [['id' => 2, 'username' => 'pdo-test']];
    }
}

class UserModel
{
    public $id;
    public $username;
}

class UserManager
{
    public static $database;
  
    public function __construct()
    {

        switch(get_cfg_var("conection_type")){
            case "msqli":
               self::$database = new MysqliDatabaseDriver();
            break;
            case "pdo":
             self::$database = new PdoDatabaseDriver();
             break;
            default:
             throw new Exception('Invalid driver.');
            break;

    }

    public static function listUsers()
    {
        return array_map([new self(), 'hydrateUserRecord'], self::$database->query('SELECT * FROM users', []));
    }

    private function hydrateUserRecord(array $record): UserModel
    {
        $user = new UserModel();

        foreach ($record as $key => $value)
        {
            if (property_exists('UserModel',$key )) 
            {
                $user->$key = $value;
            }
        }

        return $user;
    }
}

class UserController{
    public function listUsers()
    {
        return UserManager::listUsers();
    }
}

//main
$controller = new UserController();
var_dump($controller->listUsers());