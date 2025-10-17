<?php
$page = $_GET['page'] ?? 'index';
require_once __DIR__ . '/../controllers/TodoController.php';
$todoController = new TodoController();
switch ($page) {
  case 'create': $todoController->create(); break;
  case 'update': $todoController->update(); break;
  case 'delete': $todoController->delete(); break;
  default: $todoController->index();
}