<?php
require_once __DIR__ . '/../models/TodoModel.php';

class TodoController {

  public function index() {
    $filter = $_GET['filter'] ?? 'all';             // all|done|todo
    $q      = trim($_GET['q'] ?? '');
    $m = new TodoModel();
    $todos = $m->getTodos($filter, $q);

    // untuk pesan error/sukses sederhana via query string
    $flash = $_GET['msg'] ?? '';
    include __DIR__ . '/../views/TodoView.php';
  }

  public function create() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $title = trim($_POST['title'] ?? '');
      $desc  = trim($_POST['description'] ?? '');
      $done  = isset($_POST['is_finished']) ? (bool)$_POST['is_finished'] : false;

      $m = new TodoModel();
      if ($title === '' || $m->titleExists($title)) {
        header('Location: index.php?msg=Judul+kosong/duplikat');
        return;
      }
      $m->create($title, $desc, $done);
    }
    header('Location: index.php?msg=Tambah+berhasil');
  }

  public function update() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id    = (int)($_POST['id'] ?? 0);
      $title = trim($_POST['title'] ?? '');
      $desc  = trim($_POST['description'] ?? '');
      $done  = isset($_POST['is_finished']) ? (bool)$_POST['is_finished'] : false;

      $m = new TodoModel();
      if ($id <= 0 || $title === '' || $m->titleExists($title, $id)) {
        header('Location: index.php?msg=Update+gagal+(judul+kosong/duplikat)');
        return;
      }
      $m->update($id, $title, $desc, $done);
    }
    header('Location: index.php?msg=Update+berhasil');
  }

  public function delete() {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) (new TodoModel())->delete($id);
    header('Location: index.php?msg=Hapus+berhasil');
  }

  public function detail() {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $todo = (new TodoModel())->find($id);
    include __DIR__ . '/../views/TodoDetail.php';   // view baru (lihat di bawah)
  }

  /** AJAX: terima urutan ID baru (JSON: {order:[...ids...]}) */
  public function reorder() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $ok = false;
    if (isset($data['order']) && is_array($data['order'])) {
      $ok = (new TodoModel())->reorder(array_map('intval', $data['order']));
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok]);
  }
}
