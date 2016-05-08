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
        $results = self::query()->get(get_called_class());
        return $results;
    }

    public static function find($id)
    {
        // TODO: not hardcode 'id' field name
        $result = self::query()->where("id = $id")->getFirst(get_called_class());
        return $result;
    }

    public static function query()
    {
        return new QueryBuilder(self::table(), get_called_class());
    }

    public function save()
    {
        // $this->
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

    public function get()
    {
        $stmt = self::$conn->prepare($this->query);

        $stmt->execute();
        return $this->resultClass ? $stmt->fetchAll(PDO::FETCH_CLASS, $this->resultClass) : $stmt->fetchAll();
    }

    public function getFirst()
    {
        $results = $this->limit(1)->get();
        return $results ? $results[0] : null;
    }

    public function limit($limit)
    {
        $this->query .= " LIMIT 1";
        return $this;
    }

    public function where($condition)
    {
        $this->query .= " WHERE $condition";
        return $this;
    }

    public function __construct($table, $resultClass = null)
    {
        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->query = "SELECT * FROM $table";
        $this->resultClass = $resultClass;
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
// var_dump((new QueryBuilder('posts'))->get());
var_dump(User::all());
var_dump(User::find(1));
var_dump(User::query()->where("name LIKE '%ete%'")->get());

// save
$user = User::find(1);
die($user->id);

