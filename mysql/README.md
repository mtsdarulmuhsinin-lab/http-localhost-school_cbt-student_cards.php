# Menjalankan modul MySQL CBT

Database yang digunakan sesuai keputusan pengelola: **school_cbt**.

## Persiapan

1. Pastikan XAMPP Apache dan MySQL berstatus Running.
2. Buka phpMyAdmin di `http://localhost/phpmyadmin`.
3. Buat database `school_cbt`, collation `utf8mb4_unicode_ci`.
4. Pilih database tersebut, buka Import, lalu impor `database/schema.mysql.sql`.
5. Salin `config/database.example.php` menjadi `config/database.php`.
6. Untuk XAMPP standar, isi host `127.0.0.1`, port `3306`, database `school_cbt`, username `root`; isi password sesuai pengaturan MySQL lokal.
7. Pastikan ekstensi `pdo_mysql` aktif di PHP.

## Membuat admin pertama

Buka `http://localhost/NAMA_FOLDER/mysql/install.php`. Buat nama admin, username, dan password minimal 10 karakter. Setelah berhasil, buka `http://localhost/NAMA_FOLDER/mysql/` untuk login. Hapus file `mysql/install.php` setelah admin dibuat atau batasi aksesnya melalui konfigurasi server.

## Membuat akun

Login sebagai admin, lalu gunakan form Tambah akun:
- Guru: pilih peran Guru.
- Siswa: isi username dengan NIS siswa dan pilih kelas VII/VIII/IX. Password awal minimal 8 karakter.

Untuk keamanan, bagikan password awal melalui jalur pribadi dan ganti password secara berkala.

## Lokasi aplikasi

Aplikasi MySQL awal berada di folder `mysql/`, terpisah dari aplikasi lama di root. Dengan demikian, aplikasi SQLite yang sudah berjalan tidak ditimpa. Contoh URL lokal bila folder proyek bernama `school_cbt`:
- `http://localhost/school_cbt/mysql/install.php`
- `http://localhost/school_cbt/mysql/`

## Batas tahap saat ini

Tahap ini mencakup koneksi MySQL, installer admin, login berbasis peran, pembuatan akun guru/siswa, aktivasi/nonaktifkan akun, dan dashboard awal. Fitur operasional ujian, token, timer server, bank soal bergambar dan multi-jenis, anti-cheat event monitoring, koreksi, laporan jawaban rinci, PDF/Excel akan ditambahkan pada tahap berikutnya. Jangan gunakan untuk ujian resmi sampai modul ujian dan pengujian keamanan selesai.
