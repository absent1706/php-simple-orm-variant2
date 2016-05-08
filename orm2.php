<?php

abstract class DbModel
{
    protected static $table;

    public static function table()
    {
        // TODO: auto guess table name based on class name
        $class = get_called_class();
        return $class::$table;
    }

    public static function all()
    {
        $results = (new QueryBuilder(self::table()))->get(get_called_class());
        return $results;
    }
}

class Post extends DbModel
{
    protected static $table = 'posts';
}

class User extends DbModel
{
    protected static $table = 'users';
}

class QueryBuilder
{
    protected static $conn;

    public static function setConnection($conn)
    {
        self::$conn = $conn;
    }

    public function get($resultClass = null)
    {
        $stmt = self::$conn->prepare($this->query);

        $stmt->execute();
        return $resultClass ? $stmt->fetchAll(PDO::FETCH_CLASS, $resultClass) : $stmt->fetchAll();
    }

    public function __construct($table)
    {
        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->query = "SELECT * FROM $table";
    }
}


// var_dump(Post::all()[0]->title);

// app config
$connection_string = "mysql:host=localhost;dbname=test_db";
$user = 'root';
$password = '';
$conn = new PDO($connection_string, $user, $password);

// bootstrap.php
QueryBuilder::setConnection($conn);

// controller
var_dump((new QueryBuilder('posts'))->get());
var_dump(User::all());