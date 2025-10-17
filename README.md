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
CREATE TABLE todo (
    id SERIAL PRIMARY KEY,
    activity VARCHAR(250) NOT NULL,
    status SMALLINT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buat trigger function untuk update otomatis kolom updated_at
CREATE OR REPLACE FUNCTION update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Buat trigger agar kolom updated_at terupdate otomatis saat update data
CREATE TRIGGER update_todo_timestamp
BEFORE UPDATE ON todo
FOR EACH ROW
EXECUTE FUNCTION update_timestamp();
