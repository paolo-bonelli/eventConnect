<?php

class Controller {

    public function model($model) {
        // Load model file
        require_once '../app/models/' . $model . '.php'; 
        return new $model();
    }

    public function view($view, $data = []) {
        // Load view
        require_once '../app/views/' . $view . '.php';
    }

}

?>