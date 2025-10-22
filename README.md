# ✅ Latihan PHP – Aplikasi Todo List (CRUD + PostgreSQL)

Aplikasi Todo List sederhana menggunakan **PHP Native (tanpa framework)** dengan arsitektur **MVC sederhana** dan database **PostgreSQL**. Aplikasi ini memiliki fitur CRUD (Create, Read, Update, Delete) dan sudah menggunakan tampilan Bootstrap.

---

## 🚀 Teknologi yang Digunakan
| Komponen        | Versi / Teknologi |
|-----------------|-------------------|
| Bahasa Pemrograman | PHP 8.x |
| Database        | PostgreSQL 16 |
| Frontend        | Bootstrap 5.3 |
| Arsitektur      | MVC sederhana |
| Web Server      | PHP Built-in Server (`php -S`) |

---

## ⚙️ Konfigurasi Database PostgreSQL

Buat database `db_todo`, lalu jalankan SQL berikut:

```sql
-- Ganti ke schema public
SET search_path TO public;

-- 1️⃣ Buat tabel utama todo
CREATE TABLE IF NOT EXISTS public.todo (
  id SERIAL PRIMARY KEY,
  title VARCHAR(250) NOT NULL,
  description TEXT DEFAULT '',
  is_finished BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  sort_order INT NOT NULL DEFAULT 0
);

-- 2️⃣ Unik untuk title (case-insensitive)
CREATE UNIQUE INDEX IF NOT EXISTS todo_title_unique
ON public.todo (LOWER(title));

-- 3️⃣ Trigger update otomatis kolom updated_at
CREATE OR REPLACE FUNCTION update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at := CURRENT_TIMESTAMP;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS update_todo_timestamp ON public.todo;
CREATE TRIGGER update_todo_timestamp
BEFORE UPDATE ON public.todo
FOR EACH ROW
EXECUTE FUNCTION update_timestamp();

-- 4️⃣ Inisialisasi nilai sort_order
UPDATE public.todo
SET sort_order = id
WHERE sort_order = 0;

-- 5️⃣ Pastikan semua constraint aktif
ALTER TABLE public.todo
  ALTER COLUMN title SET NOT NULL,
  ALTER COLUMN is_finished SET NOT NULL;

