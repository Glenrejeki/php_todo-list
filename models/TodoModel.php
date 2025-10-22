<?php
// models/TodoModel.php
require_once __DIR__ . '/../config.php';

class TodoModel {
    private $conn;

    public function __construct() {
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $port = defined('DB_PORT') ? DB_PORT : '5432';
        $dbname = defined('DB_NAME') ? DB_NAME : 'db_todo';
        $user = defined('DB_USER') ? DB_USER : (defined('DB_USER') ? DB_USER : 'postgres');
        $pass = defined('DB_PASSWORD') ? DB_PASSWORD : (defined('DB_PASS') ? DB_PASS : '');

        $connStr = "host=$host port=$port dbname=$dbname user=$user password=$pass";
        $this->conn = pg_connect($connStr);
        if (!$this->conn) {
            die("Gagal koneksi ke Postgres. Periksa config.php");
        }
    }

    public function getTodos($filter = 'all', $q = '') {
        $sql = "SELECT id, title, description, is_finished, sort_order, created_at FROM todo";
        $conds = [];
        $params = [];
        $idx = 1;

        if ($filter === 'done' || $filter === 'finished') {
            $conds[] = "is_finished = TRUE";
        } elseif ($filter === 'todo' || $filter === 'unfinished') {
            $conds[] = "is_finished = FALSE";
        }

        if ($q !== '') {
            $conds[] = "(LOWER(title) LIKE LOWER(\$${idx}::text) OR LOWER(description) LIKE LOWER(\$${idx}::text))";
            $params[] = '%' . $q . '%';
            $idx++;
        }

        if (count($conds) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conds);
        }

        $sql .= " ORDER BY sort_order ASC, id ASC";

        if (count($params) > 0) {
            $res = pg_query_params($this->conn, $sql, $params);
        } else {
            $res = pg_query($this->conn, $sql);
        }

        if (!$res) return [];

        $rows = [];
        while ($r = pg_fetch_assoc($res)) {
            $r['is_finished'] = ($r['is_finished'] === 't' || $r['is_finished'] === true) ? 1 : 0;
            $rows[] = $r;
        }
        return $rows;
    }

    public function find($id) {
        $sql = "SELECT id, title, description, is_finished, sort_order, created_at FROM todo WHERE id = $1 LIMIT 1";
        $res = pg_query_params($this->conn, $sql, [$id]);
        if (!$res) return false;
        $row = pg_fetch_assoc($res);
        if ($row) {
            $row['is_finished'] = ($row['is_finished'] === 't' || $row['is_finished'] === true) ? 1 : 0;
            return $row;
        }
        return false;
    }

    public function titleExists($title, $exceptId = null) {
        if ($exceptId) {
            $sql = "SELECT 1 FROM todo WHERE LOWER(title)=LOWER($1) AND id <> $2 LIMIT 1";
            $res = pg_query_params($this->conn, $sql, [$title, $exceptId]);
        } else {
            $sql = "SELECT 1 FROM todo WHERE LOWER(title)=LOWER($1) LIMIT 1";
            $res = pg_query_params($this->conn, $sql, [$title]);
        }
        if (!$res) return false;
        return (pg_fetch_row($res) !== false);
    }

    public function create($title, $description, $is_finished) {
        $is = ($is_finished ? 't' : 'f');
        $sql = "INSERT INTO todo (title, description, is_finished, sort_order, created_at, updated_at)
                VALUES ($1, $2, $3::boolean, COALESCE((SELECT MAX(sort_order) FROM todo), 0) + 1, NOW(), NOW())";
        $res = pg_query_params($this->conn, $sql, [$title, $description, $is]);
        return $res !== false;
    }

    public function update($id, $title, $description, $is_finished) {
        $is = ($is_finished ? 't' : 'f');
        $sql = "UPDATE todo
                SET title = $2, description = $3, is_finished = $4::boolean, updated_at = NOW()
                WHERE id = $1";
        $res = pg_query_params($this->conn, $sql, [$id, $title, $description, $is]);
        return $res !== false;
    }

    public function delete($id) {
        $sql = "DELETE FROM todo WHERE id = $1";
        $res = pg_query_params($this->conn, $sql, [$id]);
        return $res !== false;
    }

    public function reorder(array $ids) {
        if (empty($ids)) return false;
        pg_query($this->conn, 'BEGIN');
        $pos = 1;
        foreach ($ids as $id) {
            $id = (int)$id;
            $res = pg_query_params($this->conn, "UPDATE todo SET sort_order = $1 WHERE id = $2", [$pos, $id]);
            if (!$res) {
                pg_query($this->conn, 'ROLLBACK');
                return false;
            }
            $pos++;
        }
        pg_query($this->conn, 'COMMIT');
        return true;
    }
}
