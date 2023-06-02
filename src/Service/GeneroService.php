<?php
    namespace App\Service;
    use App\Model\Genero;
    class GeneroService
    {
        private $model;
        public function __construct(){
            $this->model = new Genero();
        }
        public function retrieve(){
            return $this->model->retrieve();
        }

        public function deleteById($id){
            return $this->model->deleteById($id);
        }

        public function updateById($id, $body){
            return $this->model->updateById($id, $body);
        }

        public function create($body){
            return $this->model->create($body);
        }

        public function exist($id){
            return $this->model->exist($id);
        }
    }