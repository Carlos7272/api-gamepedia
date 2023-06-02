<?php
    namespace App\Model;
    class Genero{
        private $connection;
        public function __construct(){
            $db = new Db();
            $this->connection = $db->connectionDB();
        }
        public function retrieve(){
            $sql = "SELECT * FROM generos";
            $stmt =  $this->connection->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        public function deleteById($id){
            $sql = "DELETE FROM generos WHERE id = :id";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute(['id' => $id]);
        }

        public function updateById($id, $body){
            $sql = "UPDATE generos SET nombre=? WHERE id=?";
            $stmt= $this->connection->prepare($sql);
            $vector = $this->valuesQuery($body);
            $vector[] = $id;
            $stmt->execute($vector);
        }

        public function create($body){
            $sql = "INSERT INTO generos (nombre) VALUES (?)";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute($this->valuesQuery($body));
        }

        private function valuesQuery($body){
            $name = $body['name'];
            return [$name];
        }

        public function exist($id){
            $sql = "SELECT * FROM generos WHERE id= :id";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt ->rowCount() > 0;
        }
    }