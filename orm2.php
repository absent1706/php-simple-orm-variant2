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

    public static function getColumnNames()
    {
        return DB::getColumnNames(self::table());
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
        // var_dump($this->id);die();
        if (isset($this->id)) {
            $this->_update();
        }
        else {
            $this->_insert();
        }
    }

    protected function _insert()
    {
        // TODO
    }

    protected function _update()
    {
        // TODO
    }

    // TODO: delete()
}

class Post extends DbModel
{
    protected static $table = 'posts';

    public function user()
    {
        // TODO: implement standard belongsTo method that returns the same query
        // method will look like return $this->belongsTo('User');
        return User::query()->where('id = '.$this->user_id);
    }

}

class User extends DbModel
{
    protected static $table = 'users';

    public function posts()
    {
        // TODO: implement standard hasMany method that returns the same query
        // method will look like return $this->hasMany('Post');
        return Post::query()->where('user_id = '.$this->id);
    }
}

class QueryBuilder
{
    public function get()
    {
        $stmt = DB::getConnection()->prepare($this->query);

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
        $this->query = "SELECT * FROM $table";
        $this->resultClass = $resultClass;
    }
}

class DB
{
    protected static $conn;

    public static function setConnection($conn)
    {
        self::$conn = $conn;
        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getConnection()
    {
        return self::$conn;
    }

    public static function getColumnNames($table)
    {
        // Works ONLY for MySQL
        $sql = 'select column_name from information_schema.columns where table_schema="'.self::dbName().'" and table_name="'.$table.'"';
        $stmt = self::$conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function dbName()
    {
        return self::$conn->query('select database()')->fetchColumn();
    }
}



// var_dump(Post::all()[0]->title);

// app config
$connection_string = "mysql:host=localhost;dbname=test_db";
$user = 'root';
$password = '';
$conn = new PDO($connection_string, $user, $password);

// bootstrap.php
DB::setConnection($conn);

// controller
(new QueryBuilder('posts'))->get();
User::all();
User::find(1);
User::query()->where("name LIKE '%ete%'")->get();

// save
$user = User::find(1);
$user->save();

$user2 = new User;
$user2->name = "Alex";
$user2->fdgfdgdfg='fd';
$user2->save();

User::getColumnNames();

// relations
$user = User::find(1);
$user->posts()->get();

Post::find(2)->user()->getFirst();

// TODO: make next code work: $game->home_team->name . It will be evaluated to $game->home_team()->getFirst()->name. Use __get() magic
