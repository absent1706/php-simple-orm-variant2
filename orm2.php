<?php

abstract class DbModel
{
    protected static $table;

    public static function all()
    {
        /* query  begin */
            $connection_string = "mysql:host=localhost;dbname=test_db";
            $user = 'root';
            $password = '';
            $conn = new PDO($connection_string, $user, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $currentClass = get_called_class();
            $stmt = $conn->prepare("select * from ".$currentClass::$table);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'Post');
        /* query  end */


    }
}

class Post extends DbModel
{
    protected static $table = 'posts';


}

class QueryBuilder
{

}


// var_dump(Post::all());
var_dump(Post::all()[0]->title);