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
            if ($stmt->rowCount()>0)
                return $stmt->fetchAll(\PDO::FETCH_OBJ);    // por qué cómo object? Es sólo el juego o también los otros datos?
            $stmt = null;                                               //ver que pasa si no hay datos
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
            $sql = "INSERT INTO generos (nombre, id) VALUES (?,?)";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute($this->valuesQuery($body));
        }

        private function valuesQuery($body){
            $name = $body['name'];
            // se necesita mandar en un []? No lo creo. Cómo no sé a dónde va, entonces prefiero dejarlo así hasta que sepa.
            return [$name];
        }
    }