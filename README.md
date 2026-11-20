# CBT MTs Darul Muhsinin NW Surabaya Utara

Aplikasi CBT ringan berbasis PHP 8+ dan SQLite dalam satu file. Tidak memerlukan framework, Composer, atau layanan database terpisah.

## Menjalankan

1. Pastikan PHP 8+ aktif dengan ekstensi `PDO_SQLite`.
2. Salin/unggah `index.php` ke folder web yang dapat ditulis oleh PHP (agar `cbt.sqlite` dapat dibuat).
3. Buka folder tersebut melalui server PHP, misalnya `php -S localhost:8000`, lalu kunjungi `http://localhost:8000`.

Aplikasi membuat database dan contoh ujian saat pertama dijalankan.

## Akun demo

- Guru/admin: `admin` / `admin123`
- Siswa kelas 7: `1234567890` / `siswa123`

**Sebelum digunakan sungguhan:** ganti password demo, buat akun individual untuk siswa, gunakan HTTPS, atur backup database, dan pastikan folder database tidak dapat diakses langsung dari web. Kredensial contoh hanya untuk uji coba.

## Fitur

- Login siswa menggunakan NISN/username dan password; sesi login dan password hash.
- Timer berdasarkan batas waktu server; ujian otomatis dikumpulkan ketika waktu habis.
- Navigasi soal, simpan jawaban otomatis, penanda ragu-ragu, dan lanjutkan ujian yang belum selesai.
- Guru membuat ujian kelas 7–9 beserta mata pelajaran umum, keagamaan, atau muatan lokal, durasi, dan pilihan pengacakan soal/opsi.
- Pembuatan soal pilihan ganda empat opsi dan rekap nilai siswa.
- Penyimpanan SQLite persisten.

## Catatan

Ini adalah fondasi aplikasi yang bisa dijalankan, bukan sistem ujian berisiko tinggi yang telah diaudit. Pengelolaan akun siswa, kebijakan retensi data, pengujian beban, dan pengamanan/backup server perlu disesuaikan sebelum peluncuran produksi.
