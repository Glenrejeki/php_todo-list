<?php
// public/index.php

// 1) Load config & DB connection (hasilkan $pdo)
require_once dirname(__DIR__) . '/config.php';

// 2) Load Controller (pastikan file ada)
require_once dirname(__DIR__) . '/controllers/TodoController.php';

// 3) Simple router via ?page=
$page = $_GET['page'] ?? 'index';

// 4) Injeksi PDO ke controller (lebih rapi)
$c = new TodoController($pdo);

/*
  Rute yang didukung:
  - ?page=index            -> daftar todo (default)
  - ?page=create           -> POST buat todo
  - ?page=update           -> POST update todo
  - ?page=delete&id=...    -> GET/POST hapus todo
  - ?page=detail&id=...    -> GET detail todo
  - ?page=reorder          -> POST (AJAX) reorder via SortableJS
*/

switch ($page) {
  case 'create':  $c->create();  break;
  case 'update':  $c->update();  break;
  case 'delete':  $c->delete();  break;
  case 'detail':  $c->detail();  break;
  case 'reorder': $c->reorder(); break;
  default:        $c->index();   break;
}
