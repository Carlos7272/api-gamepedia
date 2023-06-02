<?php
    namespace App\Model;
    class Juego{
        private $connection;
        public function __construct(){
            $db = new Db();
            $this->connection = $db->connectionDB();
        }
        public function retrieve(){
            $sql = "SELECT * FROM juegos";
            $stmt =  $this->connection->query($sql);
            if ($stmt->rowCount()>0)
                return $stmt->fetchAll(\PDO::FETCH_OBJ);
            $stmt = null;
        }
        public function retrieveByFilter($name, $idPlatform, $idGender, $order){
            $sql = "SELECT * FROM juegos WHERE nombre=:name AND id_plataforma=:idPlatform AND id_genero=:idGender ORDER BY nombre ASC";
            if ($order == 'desc' || $order == 'DESC')
                $sql = "SELECT * FROM juegos WHERE nombre=:name AND id_plataforma=:idPlatform AND id_genero=:idGender ORDER BY nombre DESC";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(['name' => $name, 'idPlatform' => $idPlatform ,'idGender' => $idGender]);
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        public function deleteById($id){
            $sql = "DELETE FROM juegos WHERE id = :id";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute(['id' => $id]);
        }

        public function updateById($id, $body){
            $sql = "UPDATE juegos SET nombre=?, imagen=?, tipo_imagen=?, descripcion=?, url=?, id_genero=?, id_plataforma=? WHERE id=?";
            $stmt= $this->connection->prepare($sql);
            $vector = $this->valuesQuery($body);
            $vector[] = $id;
            $stmt->execute($vector);
        }

        public function create($body){
            $sql = "INSERT INTO juegos (nombre, imagen, tipo_imagen, descripcion, url, id_genero, id_plataforma) VALUES (?,?,?,?,?,?,?)";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute($this->valuesQuery($body));
        }

        private function valuesQuery($body){
            $name = $body['name'];
            $image = $body['image'];
            $type_image = $body['type_image'];
            $description = $body['description'];
            $url = $body['url'];
            $id_gender = $body['id_gender'];
            $id_platform = $body['id_platform'];
            return [$name, $image, $type_image, $description, $url, $id_gender, $id_platform];
        }

        public function exist($id){
            $sql = "SELECT * FROM juegos WHERE id= :id";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt ->rowCount() > 0;
        }
    }