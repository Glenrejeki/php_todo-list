<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>PHP - Aplikasi Todolist</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap (lokal) -->
  <link rel="stylesheet" href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons (CDN, optional untuk ikon kecil) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Ctext y='14' font-size='14'%3E✅%3C/text%3E%3C/svg%3E">

  <style>
    body { background: #f7f8fa; }
    .app-card { border: 0; border-radius: 1.25rem; box-shadow: 0 6px 20px rgba(0,0,0,.06); }
    .brand-pill { background: linear-gradient(135deg,#eef4ff,#f6faff); border: 1px solid #e7eeff; }
    .table > :not(caption) > * > * { vertical-align: middle; }
    .line-clamp-2 {
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .row-hover:hover { background: #f9fbff; }
    .drag-handle { cursor: grab; user-select: none; opacity: .6; }
    .empty-state {
      border: 1px dashed #d8dee9; border-radius: 1rem; padding: 2rem; background: #fff;
    }
  </style>
</head>
<body>
<div class="container py-4">

  <!-- Header / Brand -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-3">
      <div class="brand-pill px-3 py-2 rounded-4 fw-semibold text-primary">
        <i class="bi bi-check2-square me-1"></i> Todo List
      </div>
      <?php if (!empty($flash)): ?>
        <span class="badge bg-info-subtle text-info border border-info-subtle">
          <?= htmlspecialchars($flash) ?>
        </span>
      <?php endif; ?>
    </div>
    <button class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addTodo">
      <i class="bi bi-plus-lg me-1"></i> Tambah Data
    </button>
  </div>

  <!-- Card -->
  <div class="card app-card">
    <div class="card-body p-3 p-md-4">

      <!-- Filter + Search -->
      <form class="row g-2 align-items-center" method="GET" action="index.php">
        <input type="hidden" name="page" value="index">

        <div class="col-12 col-md-auto">
          <label class="form-label mb-0 small text-muted">Filter Status</label>
          <select class="form-select rounded-3" name="filter" onchange="this.form.submit()">
            <?php
              $opts = ['all'=>'Semua','todo'=>'Belum Selesai','done'=>'Selesai'];
              foreach ($opts as $k=>$v) {
                $sel = ($k === ($filter ?? 'all')) ? 'selected' : '';
                echo "<option value='$k' $sel>$v</option>";
              }
            ?>
          </select>
        </div>

        <div class="col-12 col-md">
          <label class="form-label mb-0 small text-muted">Pencarian</label>
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input class="form-control" type="search" name="q"
                   value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Cari judul atau deskripsi...">
            <button class="btn btn-outline-primary">Cari</button>
          </div>
        </div>
      </form>

      <!-- Tabel -->
      <div class="table-responsive mt-3">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:56px" class="text-muted"><i class="bi bi-grip-vertical"></i></th>
              <th>Judul</th>
              <th>Deskripsi</th>
              <th style="width:140px">Status</th>
              <th style="width:240px" class="text-end">Tindakan</th>
            </tr>
          </thead>
          <tbody id="todoTable">
          <?php if (!empty($todos)): foreach ($todos as $i => $todo): ?>
            <tr class="row-hover" data-id="<?= (int)$todo['id'] ?>">
              <td class="text-muted">
                <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
              </td>
              <td class="fw-semibold">
                <?= htmlspecialchars($todo['title']) ?>
                <div class="small text-muted">
                  ID: <?= (int)$todo['id'] ?>
                </div>
              </td>
              <td>
                <div class="line-clamp-2 text-muted">
                  <?= htmlspecialchars($todo['description'] ?? '') ?>
                </div>
              </td>
              <td>
                <?= !empty($todo['is_finished'])
                    ? '<span class="badge rounded-pill px-3 bg-success-subtle text-success border border-success-subtle">Selesai</span>'
                    : '<span class="badge rounded-pill px-3 bg-secondary-subtle text-secondary border border-secondary-subtle">Belum</span>' ?>
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-info rounded-3 me-1"
                   href="?page=detail&id=<?= (int)$todo['id'] ?>">
                  <i class="bi bi-info-circle"></i> Detail
                </a>

                <button class="btn btn-sm btn-outline-warning rounded-3 me-1"
                  onclick="showModalEditTodo(
                    <?= (int)$todo['id'] ?>,
                    '<?= htmlspecialchars(addslashes($todo['title'])) ?>',
                    '<?= htmlspecialchars(addslashes($todo['description'] ?? '')) ?>',
                    <?= !empty($todo['is_finished']) ? 1 : 0 ?>
                  )">
                  <i class="bi bi-pencil-square"></i> Ubah
                </button>

                <a class="btn btn-sm btn-outline-danger rounded-3"
                   href="?page=delete&id=<?= (int)$todo['id'] ?>"
                   onclick="return confirm('Hapus todo ini?')">
                  <i class="bi bi-trash3"></i> Hapus
                </a>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr>
              <td colspan="5">
                <div class="empty-state text-center">
                  <div class="fs-1 mb-2">🗒️</div>
                  <h6 class="mb-1">Belum ada data tersedia</h6>
                  <p class="text-muted mb-3">Tambah todo pertamamu dengan tombol “Tambah Data”.</p>
                  <button class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addTodo">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Data
                  </button>
                </div>
              </td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addTodo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4">
    <div class="modal-header">
      <h5 class="modal-title"><i class="bi bi-plus-square me-1"></i> Tambah Todo</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="?page=create" method="POST">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Judul <span class="text-danger">*</span></label>
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
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editTodo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4">
    <div class="modal-header">
      <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Ubah Todo</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="?page=update" method="POST">
      <input type="hidden" name="id" id="editId">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Judul <span class="text-danger">*</span></label>
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
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Popper + Bootstrap (lokal) -->
<script src="/assets/vendor/bootstrap-5.3.8-dist/js/popper.min.js"></script>
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

  const tbody = document.getElementById('todoTable');
  if (tbody) {
    new Sortable(tbody, {
      animation: 150,
      handle: '.drag-handle',
      onEnd: () => {
        const order = Array.from(tbody.querySelectorAll('tr')).map(tr => tr.dataset.id);
        fetch('?page=reorder', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ order })
        });
      }
    });
  }
</script>

</body>
</html>
