<?php

class Model {
    protected $db;
    
    function __construct() {
        try {
            $host = 'localhost:3306';
            $user = 'root';
            $pass = '';
            $db = 'wellmate';
            
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->db = new mysqli($host, $user, $pass, $db);
            
            $this->db->set_charset('utf8mb4');

        } catch (Exception $e) {
            error_log("Database connection failed: " . $e->getMessage());
            $this->db = null;
        }
    }
    
    protected function escape($value) {
        if ($this->db === null) {
            return htmlspecialchars($value);
        }
        
        return $this->db->real_escape_string($value);
    }
    
    protected function query($sql) {
        if ($this->db === null) {
            throw new Exception("Database connection not available");
        }
        
        $result = $this->db->query($sql);
        
        if (!$result && $this->db->errno != 0) {
            throw new Exception("Query error: " . $this->db->error);
        }
        
        return $result;
    }
    
    protected function isConnected() {
        return $this->db !== null && $this->db->ping();
    }
}