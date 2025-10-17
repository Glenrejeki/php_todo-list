<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>PHP - Aplikasi Todolist</title>
  <<link rel="stylesheet" href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid p-5">
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <h1>Todo List</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTodo">Tambah Data</button>
      </div>
      <hr />
      <table class="table table-striped">
        <thead>
          <tr><th>#</th><th>Aktivitas</th><th>Status</th><th>Tanggal Dibuat</th><th>Tindakan</th></tr>
        </thead>
        <tbody>
        <?php if (!empty($todos)): foreach ($todos as $i => $todo): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($todo['activity']) ?></td>
            <td>
              <?= !empty($todo['status'])
                  ? '<span class="badge bg-success">Selesai</span>'
                  : '<span class="badge bg-danger">Belum Selesai</span>' ?>
            </td>
            <td><?= isset($todo['created_at']) ? date('d F Y - H:i', strtotime($todo['created_at'])) : '-' ?></td>
            <td>
              <button class="btn btn-sm btn-warning"
                onclick="showModalEditTodo(<?= (int)$todo['id'] ?>,'<?= htmlspecialchars(addslashes($todo['activity'])) ?>', <?= (int)$todo['status'] ?>)">Ubah</button>
              <button class="btn btn-sm btn-danger"
                onclick="showModalDeleteTodo(<?= (int)$todo['id'] ?>,'<?= htmlspecialchars(addslashes($todo['activity'])) ?>')">Hapus</button>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5" class="text-center text-muted">Belum ada data tersedia!</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addTodo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Tambah Data Todo</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="?page=create" method="POST">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Aktivitas</label>
          <input type="text" name="activity" class="form-control" placeholder="Contoh: Belajar membuat aplikasi website sederhana" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editTodo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Ubah Data Todo</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="?page=update" method="POST">
      <input name="id" type="hidden" id="inputEditTodoId">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Aktivitas</label>
          <input type="text" name="activity" class="form-control" id="inputEditActivity" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Status</label>
          <select class="form-select" name="status" id="selectEditStatus">
            <option value="0">Belum Selesai</option>
            <option value="1">Selesai</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="deleteTodo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hapus Data Todo</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      Kamu akan menghapus todo <strong class="text-danger" id="deleteTodoActivity"></strong>. Apakah kamu yakin?
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      <a id="btnDeleteTodo" class="btn btn-danger">Ya, Tetap Hapus</a>
    </div>
  </div></div>
</div>

<script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>

<script>
function showModalEditTodo(id, activity, status) {
  document.getElementById('inputEditTodoId').value = id;
  document.getElementById('inputEditActivity').value = activity;
  document.getElementById('selectEditStatus').value = status;
  new bootstrap.Modal(document.getElementById('editTodo')).show();
}
function showModalDeleteTodo(id, activity) {
  document.getElementById('deleteTodoActivity').innerText = activity;
  document.getElementById('btnDeleteTodo').setAttribute('href', `?page=delete&id=${id}`);
  new bootstrap.Modal(document.getElementById('deleteTodo')).show();
}
</script>
</body>
</html>