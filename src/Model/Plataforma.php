<?php
    namespace App\Model;
    class Plataforma{
        private $connection;
        public function __construct(){
            $db = new Db();
            $this->connection = $db->connectionDB();
        }
        public function retrieve(){
            $sql = "SELECT * FROM plataformas";
            $stmt =  $this->connection->query($sql);
            if ($stmt->rowCount()>0)
                return $stmt->fetchAll(\PDO::FETCH_OBJ);
            $stmt = null;                                               //ver que pasa si no hay datos
        }
        public function deleteById($id){
            $sql = "DELETE FROM plataformas WHERE id = :id";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute(['id' => $id]);
        }

        public function updateById($id, $body){
            $sql = "UPDATE plataformas SET nombre=? WHERE id=?";
            $stmt= $this->connection->prepare($sql);
            $vector = $this->valuesQuery($body);
            $vector[] = $id;
            $stmt->execute($vector);
        }

        public function create($body){
            $sql = "INSERT INTO plataformas (nombre) VALUES (?)";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute($this->valuesQuery($body));
        }

        private function valuesQuery($body){
            $name = $body['name'];
            return [$name];
        }

        public function exist($id){
            $sql = "SELECT * FROM plataformas WHERE id= :id";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt ->rowCount() > 0;
        }
    }