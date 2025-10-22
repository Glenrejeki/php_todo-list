<?php
// views/TodoDetail.php
if (!isset($todo) || !$todo) {
  echo "<div class='container py-5'>
          <div class='alert alert-warning shadow-sm rounded-3'>
            <div class='d-flex align-items-center gap-2'>
              <span class='fs-5'>⚠️</span>
              <div><strong>Todo tidak ditemukan.</strong></div>
            </div>
            <div class='mt-3'>
              <a href='index.php' class='btn btn-sm btn-outline-secondary'>Kembali</a>
            </div>
          </div>
        </div>";
  return;
}
?>
<div class="container py-4">
  <a href="index.php" class="btn btn-sm btn-outline-secondary mb-3">
    ← Kembali
  </a>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
      <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
          <h2 class="h4 mb-1"><?= htmlspecialchars($todo['title']) ?></h2>
          <div class="small text-muted">
            Dibuat: <?= htmlspecialchars($todo['created_at']) ?>
          </div>
        </div>
        <span class="badge px-3 py-2 rounded-pill
            <?= !empty($todo['is_finished']) ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' ?>">
          <?= !empty($todo['is_finished']) ? 'Selesai' : 'Belum Selesai' ?>
        </span>
      </div>

      <?php if (!empty($todo['description'])): ?>
        <hr class="my-4">
        <div class="fs-6 lh-base">
          <?= nl2br(htmlspecialchars($todo['description'])) ?>
        </div>
      <?php else: ?>
        <hr class="my-4">
        <div class="text-muted fst-italic">Tidak ada deskripsi.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
