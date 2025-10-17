<?php
require_once __DIR__ . '/../config.php';

class TodoModel {
  private $conn;
  public function __construct() {
    $this->conn = pg_connect('host='.DB_HOST.' port='.DB_PORT.' dbname='.DB_NAME.' user='.DB_USER.' password='.DB_PASSWORD);
    if (!$this->conn) die('Koneksi database gagal');
  }

  public function getAllTodos() {
    $q = 'SELECT * FROM todo ORDER BY id';
    $r = pg_query($this->conn, $q);
    $rows = [];
    if ($r && pg_num_rows($r) > 0) while ($x = pg_fetch_assoc($r)) $rows[] = $x;
    return $rows;
  }

  public function createTodo($activity) {
    return pg_query_params($this->conn, 'INSERT INTO todo (activity) VALUES ($1)', [$activity]) !== false;
  }

  public function updateTodo($id, $activity, $status) {
    return pg_query_params($this->conn, 'UPDATE todo SET activity=$1, status=$2 WHERE id=$3', [$activity, $status, $id]) !== false;
  }

  public function deleteTodo($id) {
    return pg_query_params($this->conn, 'DELETE FROM todo WHERE id=$1', [$id]) !== false;
  }
}