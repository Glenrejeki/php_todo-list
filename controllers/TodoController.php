<?php
require_once __DIR__ . '/../models/TodoModel.php';

class TodoController {
  public function index() {
    $todoModel = new TodoModel();
    $todos = $todoModel->getAllTodos();
    include __DIR__ . '/../views/TodoView.php';
  }

  public function create() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $activity = trim($_POST['activity'] ?? '');
      if ($activity !== '') (new TodoModel())->createTodo($activity);
    }
    header('Location: index.php');
  }

  public function update() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'] ?? null;
      $activity = $_POST['activity'] ?? '';
      $status = $_POST['status'] ?? 0;
      if ($id !== null) (new TodoModel())->updateTodo($id, $activity, $status);
    }
    header('Location: index.php');
  }

  public function delete() {
    if (isset($_GET['id'])) (new TodoModel())->deleteTodo($_GET['id']);
    header('Location: index.php');
  }
}