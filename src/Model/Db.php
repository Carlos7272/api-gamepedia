<?php
    namespace App\Model;
    use PDO;
    use PDOException;
    class Db{
        const DB_DRIVER = "mysql";
        const DB_HOST = "localhost";
        const DB_NAME = "juegos_online";
        const DB_LOGIN = "root";
        const DB_PASS = "root";
        public function connectionDB(){
            try {
                $connectionString = self::DB_DRIVER.':host='.self::DB_HOST.';dbname='.self::DB_NAME;
                $connection = new PDO($connectionString, self::DB_LOGIN, self::DB_PASS);
                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $connection;
            }catch (PDOException $ex){
                return $ex->getMessage();
            }
        }
    }