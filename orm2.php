<?php

abstract class DbModel
{
    protected static $table;
    protected static $conn;

    public static function setConnection($conn)
    {
        self::$conn = $conn;
    }

    public static function table()
    {
        // TODO: auto guess table name based on class name
        $class = get_called_class();
        return $class::$table;
    }

    public static function all($conn)
    {
        /* query  begin */
        $results = (new QueryBuilder(self::$conn, self::table()))->get();
        return $results;
        /* query  end */


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
    public function get()
    {
        $stmt = $this->conn->prepare($this->query);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function __construct($conn, $table)
    {
        $this->conn = $conn;
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->query = "SELECT * FROM $table";
    }
}


// var_dump(Post::all()[0]->title);

$connection_string = "mysql:host=localhost;dbname=test_db";
$user = 'root';
$password = '';
$conn = new PDO($connection_string, $user, $password);

// var_dump((new QueryBuilder($conn, 'posts'))->get());
DbModel::setConnection($conn);
var_dump(Post::all());