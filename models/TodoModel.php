<?php
require_once __DIR__ . '/../config.php';

class TodoModel {
  private $conn;
  public function __construct() {
    $this->conn = pg_connect(
      'host='.DB_HOST.' port='.DB_PORT.' dbname='.DB_NAME.' user='.DB_USER.' password='.DB_PASSWORD
    );
    if (!$this->conn) die('Koneksi database gagal: ' . pg_last_error());
  }

  /** Ambil list todo dengan filter & search, urut berdasar sort_order lalu created_at */
  public function getTodos(string $filter = 'all', string $q = ''): array {
    $where = [];
    $params = [];
    if ($filter === 'done')      { $where[] = 'is_finished = TRUE'; }
    elseif ($filter === 'todo')  { $where[] = 'is_finished = FALSE'; }

    if ($q !== '') {
      $params[] = '%'.mb_strtolower($q).'%';
      $where[]  = '(LOWER(title) LIKE $'.count($params).' OR LOWER(COALESCE(description, \'\')) LIKE $'.count($params).')';
    }

    $sql = 'SELECT id, title, description, is_finished, created_at, updated_at
            FROM public.todo';
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' ORDER BY sort_order ASC, created_at DESC, id DESC';

    $res = $params ? pg_query_params($this->conn, $sql, $params) : pg_query($this->conn, $sql);
    $rows = [];
    if ($res) while ($r = pg_fetch_assoc($res)) $rows[] = $r;
    return $rows;
  }

  /** Cek judul unik (case-insensitive). $excludeId untuk update. */
  public function titleExists(string $title, ?int $excludeId = null): bool {
    if ($excludeId) {
      $res = pg_query_params($this->conn,
        'SELECT 1 FROM public.todo WHERE LOWER(title)=LOWER($1) AND id<>$2 LIMIT 1',
        [$title, $excludeId]
      );
    } else {
      $res = pg_query_params($this->conn,
        'SELECT 1 FROM public.todo WHERE LOWER(title)=LOWER($1) LIMIT 1',
        [$title]
      );
    }
    return $res && pg_num_rows($res) > 0;
  }

  public function create(string $title, string $description = '', bool $is_finished = false): bool {
    return pg_query_params($this->conn,
      'INSERT INTO public.todo (title, description, is_finished, sort_order)
       VALUES ($1, $2, $3, COALESCE((SELECT MAX(sort_order)+1 FROM public.todo), 1))',
      [$title, $description, $is_finished]
    ) !== false;
  }

  public function update(int $id, string $title, string $description, bool $is_finished): bool {
    return pg_query_params($this->conn,
      'UPDATE public.todo SET title=$1, description=$2, is_finished=$3 WHERE id=$4',
      [$title, $description, $is_finished, $id]
    ) !== false;
  }

  public function delete(int $id): bool {
    return pg_query_params($this->conn, 'DELETE FROM public.todo WHERE id=$1', [$id]) !== false;
  }

  public function find(int $id): ?array {
    $res = pg_query_params($this->conn,
      'SELECT id, title, description, is_finished, created_at, updated_at, sort_order
       FROM public.todo WHERE id=$1', [$id]);
    return ($res && pg_num_rows($res) === 1) ? pg_fetch_assoc($res) : null;
  }

  /** Persist urutan baru: $orders = [id1,id2,id3,...] */
  public function reorder(array $orders): bool {
    if (empty($orders)) return true;
    // CASE WHEN id=.. THEN pos ...
    $case = [];
    $params = [];
    $pos = 1;
    foreach ($orders as $id) {
      $case[] = 'WHEN id=$'.count($params)+1 .' THEN '.($pos++);
      $params[] = (int)$id;
    }
    $sql = 'UPDATE public.todo SET sort_order = CASE '.implode(' ', $case).' END WHERE id = ANY($'.count($params)+1 .')';
    $params[] = '{'.implode(',', array_map('intval',$orders)).'}';
    return pg_query_params($this->conn, $sql, $params) !== false;
  }
}
