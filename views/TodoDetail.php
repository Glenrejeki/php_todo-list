<?php
// views/TodoDetail.php
if (!isset($todo) || !$todo) {
    echo "<div class='container mt-4'><div class='alert alert-warning'>Todo tidak ditemukan.</div></div>";
    return;
}
?>
<div class="container mt-4">
  <h2><?= htmlspecialchars($todo['title']) ?></h2>
  <p><?= nl2br(htmlspecialchars($todo['description'])) ?></p>
  <p><strong>Status:</strong> <?= $todo['is_finished'] ? 'Selesai' : 'Belum Selesai' ?></p>
  <p><strong>Dibuat:</strong> <?= htmlspecialchars($todo['created_at']) ?></p>
  <a class="btn btn-secondary" href="index.php">Kembali</a>
</div>
PHP
