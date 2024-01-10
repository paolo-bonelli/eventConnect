<?php
/**
 * PHP opening tag indicating start of PHP code.
*/

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

class Crud {

  private $db;

  private function getDb() {
    $this->db = new mysqli('localhost', 'username', 'password', 'database');
    return $this->db;
  }

  public function create($table, $data) {
    // Connect to the database
    $db = $this->getDb();
    
    // Prepare column names for the INSERT statement
    $columns = implode(',', array_keys($data));
    
    // Prepare placeholder values for the INSERT statement
    $values = implode(',', array_fill(0, count($data), '?'));

    // Sanitize the table name to prevent SQL injection
    $table = filter_var($table, FILTER_SANITIZE_STRING);

    // Sanitize each data value to prevent SQL injection
    $sanitized_data = array_map('filter_var', $data, array_fill(0, count($data), FILTER_SANITIZE_STRING));

    // Construct INSERT statement with sanitized table name and placeholder values
    $stmt = $db->prepare("INSERT INTO $table ($columns) VALUES ($values)");
    
    // Bind sanitized data values to placeholders
    $stmt->bind_param(str_repeat('s', count($sanitized_data)), ...array_values($sanitized_data));
    
    // Execute statement
    $stmt->execute();
}


  public function read($table, $criteria = []) {
    $db = $this->getDb();
    $where = '';
    if (!empty($criteria)) {
      $where = 'WHERE ' . implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($criteria)));
    }
    
    // Sanitize table name
    $table = filter_var($table, FILTER_SANITIZE_STRING);
    
    // Sanitize criteria values
    $sanitized_criteria = array_map('filter_var', $criteria, array_fill(0, count($criteria), FILTER_SANITIZE_STRING));
    
    $stmt = $db->prepare("SELECT * FROM $table $where");
    if (!empty($sanitized_criteria)) {
      $stmt->bind_param(str_repeat('s', count($sanitized_criteria)), ...array_values($sanitized_criteria)); 
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  }

  public function update($table, $data, $criteria) {
    $db = $this->getDb();
    $set = implode(',', array_map(fn($k) => "$k = ?", array_keys($data)));
    $where = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($criteria)));
    
    // Sanitize table name
    $table = filter_var($table, FILTER_SANITIZE_STRING);
    
    // Sanitize data values
    $sanitized_data = array_map('filter_var', $data, array_fill(0, count($data), FILTER_SANITIZE_STRING));
    
    // Sanitize criteria values
    $sanitized_criteria = array_map('filter_var', $criteria, array_fill(0, count($criteria), FILTER_SANITIZE_STRING));
    
    $values = array_merge(array_values($sanitized_data), array_values($sanitized_criteria));
    $stmt = $db->prepare("UPDATE $table SET $set WHERE $where");
    $stmt->bind_param(str_repeat('s', count($values)), ...$values);
    $stmt->execute();
  }

  public function delete($table, $criteria) {
    $db = $this->getDb();
    $where = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($criteria)));
    
    // Sanitize table name
    $table = filter_var($table, FILTER_SANITIZE_STRING);
    
    // Sanitize criteria values
    $sanitized_criteria = array_map('filter_var', $criteria, array_fill(0, count($criteria), FILTER_SANITIZE_STRING));
    
    $stmt = $db->prepare("DELETE FROM $table WHERE $where"); 
    $stmt->bind_param(str_repeat('s', count($sanitized_criteria)), ...array_values($sanitized_criteria));
    $stmt->execute();
  }

}




?>