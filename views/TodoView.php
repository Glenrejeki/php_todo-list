<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>PHP - Aplikasi Todolist</title>
  <link rel="stylesheet" href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Ctext y='14' font-size='14'%3E✅%3C/text%3E%3C/svg%3E">

</head>
<body>
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 my-0">Todo List</h1>
    <?php if (!empty($flash)): ?>
      <span class="badge bg-info text-dark"><?= htmlspecialchars($flash) ?></span>
    <?php endif; ?>
  </div>

  <!-- Filter + Search -->
  <form class="row g-2 mt-3" method="GET" action="index.php">
    <input type="hidden" name="page" value="index">
    <div class="col-auto">
      <select class="form-select" name="filter" onchange="this.form.submit()">
        <?php
          $opts = ['all'=>'Semua','todo'=>'Belum Selesai','done'=>'Selesai'];
          foreach ($opts as $k=>$v) {
            $sel = ($k === ($filter ?? 'all')) ? 'selected' : '';
            echo "<option value='$k' $sel>$v</option>";
          }
        ?>
      </select>
    </div>
    <div class="col-auto">
      <input class="form-control" type="search" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Cari judul/desk...">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary">Cari</button>
    </div>
  </form>

  <!-- Tombol Tambah -->
  <div class="mt-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTodo">Tambah Data</button>
  </div>

  <div class="table-responsive mt-3">
    <table class="table table-striped align-middle">
      <thead><tr>
        <th style="width:60px">#</th>
        <th>Judul</th>
        <th>Deskripsi</th>
        <th style="width:140px">Status</th>
        <th style="width:220px">Tindakan</th>
      </tr></thead>
      <tbody id="todoTable">
      <?php if (!empty($todos)): foreach ($todos as $i => $todo): ?>
        <tr data-id="<?= (int)$todo['id'] ?>">
          <td class="text-muted"><?= $i+1 ?></td>
          <td><?= htmlspecialchars($todo['title']) ?></td>
          <td class="text-truncate" style="max-width: 420px"><?= htmlspecialchars($todo['description'] ?? '') ?></td>
          <td>
            <?= !empty($todo['is_finished']) ? '<span class="badge bg-success">Selesai</span>' : '<span class="badge bg-secondary">Belum</span>' ?>
          </td>
          <td>
            <a class="btn btn-sm btn-info" href="?page=detail&id=<?= (int)$todo['id'] ?>">Detail</a>
            <button class="btn btn-sm btn-warning"
              onclick="showModalEditTodo(<?= (int)$todo['id'] ?>,
                '<?= htmlspecialchars(addslashes($todo['title'])) ?>',
                '<?= htmlspecialchars(addslashes($todo['description'] ?? '')) ?>',
                <?= !empty($todo['is_finished']) ? 1 : 0 ?>)">Ubah</button>
            <a class="btn btn-sm btn-danger" href="?page=delete&id=<?= (int)$todo['id'] ?>"
               onclick="return confirm('Hapus todo ini?')">Hapus</a>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="5" class="text-center text-muted">Belum ada data tersedia!</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addTodo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Tambah Todo</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="?page=create" method="POST">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Judul</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="is_finished" value="1" id="addDone">
          <label class="form-check-label" for="addDone">Tandai selesai</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editTodo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Ubah Todo</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="?page=update" method="POST">
      <input type="hidden" name="id" id="editId">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Judul</label>
          <input type="text" name="title" id="editTitle" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" id="editDesc" class="form-control" rows="3"></textarea>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="is_finished" value="1" id="editDone">
          <label class="form-check-label" for="editDone">Tandai selesai</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div></div>
</div>

<script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
<!-- SortableJS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
function showModalEditTodo(id, title, desc, done) {
  document.getElementById('editId').value = id;
  document.getElementById('editTitle').value = title;
  document.getElementById('editDesc').value = desc;
  document.getElementById('editDone').checked = !!done;
  new bootstrap.Modal(document.getElementById('editTodo')).show();
}

// Drag & Drop persist order
const tbody = document.getElementById('todoTable');
new Sortable(tbody, {
  animation: 150,
  onEnd: () => {
    const order = Array.from(tbody.querySelectorAll('tr')).map(tr => tr.dataset.id);
    fetch('?page=reorder', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ order })
    });
  }
});
</script>
</body>
</html>
