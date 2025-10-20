<?php
$page = $_GET['page'] ?? 'index';
require_once __DIR__ . '/../controllers/TodoController.php';
$c = new TodoController();
switch ($page) {
  case 'create':  $c->create();  break;
  case 'update':  $c->update();  break;
  case 'delete':  $c->delete();  break;
  case 'detail':  $c->detail();  break;
  case 'reorder': $c->reorder(); break;    // AJAX SortableJS
  default:        $c->index();
}
