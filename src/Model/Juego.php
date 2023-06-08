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
        public function retrieveByFilter($name, $idPlatform, $idGender, $asc){
            $sql = "SELECT * FROM juegos WHERE 'una condicion x' = 'una condicion x'";
            if (!is_null($name)) $sql = $sql." AND nombre='".$name."'";
            if (!is_null($idPlatform)) $sql = $sql." AND id_plataforma=".$idPlatform;
            if (!is_null($idGender)) $sql = $sql." AND id_genero=".$idGender;
            if ($asc === "true") $sql = $sql." ORDER BY nombre ASC";
            else $sql = $sql." ORDER BY nombre DESC";
            $stmt = $this->connection->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        public function deleteById($id){
            $sql = "DELETE FROM juegos WHERE id = :id";
            $stmt= $this->connection->prepare($sql);
            $stmt->execute(['id' => $id]);
        }

        public function updateById($id, $body){
            $sql = "UPDATE juegos SET ";
            $stmt= $this->connection->prepare($sql);

            $vector = [];
            if (isset($body["name"])){
                $vector[] = $body["name"];
                $sql = $sql."nombre=?, ";
            }
            if (isset($body["image"])){
                $vector[] = $body["image"];
                $sql = $sql."imagen=?, ";
            }
            if (isset($body["type_image"])){
                $vector[] = $body["type_image"];
                $sql = $sql."tipo_imagen=?, ";
            }
            if (isset($body["description"])){
                $vector[] = $body["description"];
                $sql = $sql."descripcion=?, ";
            }
            if (isset($body["url"])){
                $vector[] = $body["url"];
                $sql = $sql."url=?, ";
            }
            if (isset($body["id_gender"])){
                $vector[] = $body["id_gender"];
                $sql = $sql."id_genero=?, ";
            }
            if (isset($body["id_platform"])){
                $vector[] = $body["id_platform"];
                $sql = $sql."id_plataforma=?, ";
            }
            $sql = rtrim($sql, ', ');
            $sql = $sql." WHERE id = ?";

            $vector[] = $id;
            $stmt= $this->connection->prepare($sql);
            $stmt->execute($vector);
        }

        public function create($body){
            $sql = "INSERT INTO juegos (nombre, imagen, tipo_imagen, descripcion, url, id_genero, id_plataforma) VALUES (?,?,?,?,?,?,?)";
            $stmt= $this->connection->prepare($sql);

            $empty = " ";
            $name = $body['name'];
            $image = $body['image'];
            $type_image = $body['type_image'] ?? $empty;
            $description = $body['description'] ?? null;
            $url = $body['url'] ?? $empty;
            $id_gender = $body['id_gender'];
            $id_platform = $body['id_platform'];
            $stmt->execute([$name, $image, $type_image, $description, $url, $id_gender, $id_platform]);
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