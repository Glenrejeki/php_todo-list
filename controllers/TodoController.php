<?php
require_once __DIR__ . '/../models/TodoModel.php';

class TodoController {

  private function redirect($url) {
    header("Location: $url");
    exit; // pastikan eksekusi berhenti setelah redirect
  }

public function index() {
  $filter = $_GET['filter'] ?? 'all';
  $q = trim($_GET['q'] ?? '');
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

  $m = new TodoModel();
  $todos = $m->getTodos($filter, $q);

  $selectedTodo = null;
  if ($id > 0) {
    $selectedTodo = $m->find($id);
  }

  $flash = $_GET['msg'] ?? '';
  include __DIR__ . '/../views/TodoView.php';
}


  public function create() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $title = trim($_POST['title'] ?? '');
      $desc  = trim($_POST['description'] ?? '');
      // Checkbox biasanya mengirim 'on' atau tidak dikirim sama sekali.
      // Simpan sebagai integer 1/0 agar konsisten di DB.
      $done  = isset($_POST['is_finished']) ? 1 : 0;

      $m = new TodoModel();
      if ($title === '' || $m->titleExists($title)) {
        // gunakan redirect agar user kembali ke halaman utama dan lihat pesan
        $this->redirect('index.php?msg=Judul+kosong/duplikat');
      }

      // pastikan model menerima 1/0 untuk is_finished
      $m->create($title, $desc, $done);

      $this->redirect('index.php?msg=Tambah+berhasil');
    }

    // jika ingin menampilkan form create terpisah
    include __DIR__ . '/../views/TodoCreate.php';
  }

  public function update() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id    = (int)($_POST['id'] ?? 0);
      $title = trim($_POST['title'] ?? '');
      $desc  = trim($_POST['description'] ?? '');
      $done  = isset($_POST['is_finished']) ? 1 : 0;

      $m = new TodoModel();
      if ($id <= 0 || $title === '' || $m->titleExists($title, $id)) {
        $this->redirect('index.php?msg=Update+gagal+(judul+kosong/duplikat)');
      }

      $m->update($id, $title, $desc, $done);

      $this->redirect('index.php?msg=Update+berhasil');
    }

    // tampilkan form update (ambil data dulu)
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
      echo "ID tidak valid.";
      return;
    }
    $m = new TodoModel();
    $todo = $m->find($id);
    if (!$todo) {
      echo "Todo tidak ditemukan.";
      return;
    }
    include __DIR__ . '/../views/TodoUpdate.php';
  }

  public function delete() {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
      (new TodoModel())->delete($id);
    }
    $this->redirect('index.php?msg=Hapus+berhasil');
  }

  /**
   * COMPAT: jika masih ada link lama ?page=detail&id=..., kita arahkan
   * ke index dengan query ?id=... agar detail tampil di panel kanan.
   */
  public function detail() {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $qs = [];
    if ($id > 0) $qs[] = 'id=' . $id;
    if (!empty($_GET['filter'])) $qs[] = 'filter=' . urlencode($_GET['filter']);
    if (!empty($_GET['q'])) $qs[] = 'q=' . urlencode($_GET['q']);
    $this->redirect('index.php' . (empty($qs) ? '' : '?' . implode('&', $qs)));
  }

  /** AJAX: terima urutan ID baru (JSON: {order:[...ids...]}) */
  public function reorder() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      header('Content-Type: application/json');
      echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
      exit;
    }

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $order = [];

    if (is_array($data) && isset($data['order']) && is_array($data['order'])) {
      $order = array_map('intval', $data['order']);
    } elseif (isset($_POST['order']) && is_array($_POST['order'])) {
      // fallback ke form-data 'order[]'
      $order = array_map('intval', $_POST['order']);
    } else {
      http_response_code(400);
      header('Content-Type: application/json');
      echo json_encode(['ok' => false, 'error' => 'Payload invalid']);
      exit;
    }

    $m = new TodoModel();
    $ok = $m->reorder($order) ? true : false;

    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok]);
    exit;
  }
}
