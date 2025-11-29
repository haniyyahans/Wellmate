<?php

class Controller {
    
    function model($model) {
        require_once('model/Model.class.php');
        require_once("model/$model.class.php");
        return new $model(); 
    }
    
    function view($view, $data = []) {
        foreach ($data as $key => $value) {
            $$key = $value;
        }
        include("view/$view");
    }
    
    function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    function setSession($key, $value) {
        $this->startSession();
        $_SESSION[$key] = $value;
    }
    
    function getSession($key) {
        $this->startSession();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }
    
    function removeSession($key) {
        $this->startSession();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}