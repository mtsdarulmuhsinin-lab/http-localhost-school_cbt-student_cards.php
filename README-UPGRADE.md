# School CBT — jalur pengembangan MySQL

Repositori ini sedang ditingkatkan bertahap. Aplikasi lama di `index.php` tetap menggunakan SQLite agar instalasi yang sudah ada tidak langsung rusak. Fondasi MySQL baru tersedia pada branch `feature/cbt-upgrade`.

## Isi tahap fondasi

- `database/schema.mysql.sql`: skema MySQL untuk admin, guru, siswa, kelas VII–IX, mata pelajaran, ujian, bank soal, pilihan jawaban, percobaan ujian, jawaban siswa, dan log pelanggaran.
- `config/database.example.php`: contoh konfigurasi koneksi PDO MySQL.
- Skema soal mendukung pilihan ganda, benar/salah, dan menjodohkan; kolom gambar tersedia untuk soal dan opsi.
- Log pengawasan menyimpan kejadian tab tersembunyi, keluar layar penuh, salin/tempel, menu konteks, dan gangguan jaringan.

## Persiapan di XAMPP

1. Jalankan Apache dan MySQL.
2. Buka phpMyAdmin: `http://localhost/phpmyadmin`.
3. Buat database bernama `school_cbt` dengan collation `utf8mb4_unicode_ci`.
4. Pilih database `school_cbt`, lalu tab Import.
5. Impor file `database/schema.mysql.sql`.
6. Salin `config/database.example.php` menjadi `config/database.php`, lalu sesuaikan host, nama database, username, dan password MySQL.
7. Pastikan ekstensi PHP `pdo_mysql` aktif pada XAMPP.

## Keamanan sebelum dipakai ujian sungguhan

- Jangan gunakan akun demo `admin/admin123` atau `1234567890/siswa123` untuk data nyata.
- Buat akun admin melalui installer yang aman pada tahap berikutnya; jangan menaruh password default di source code.
- Jangan publikasikan `config/database.php`, file database, atau folder unggahan.
- Gunakan HTTPS jika aplikasi dipasang pada server publik.
- Lakukan backup database sebelum migrasi atau pembaruan.
- Pengawasan browser hanya membantu mendeteksi indikasi (misalnya perpindahan tab); tidak dapat menjamin kecurangan mustahil.

## Status pengembangan

Ini adalah **fondasi skema dan konfigurasi**, belum pengganti aplikasi lama. Modul login MySQL, manajemen guru/siswa, bank soal bergambar, token, timer server-side, penilaian, laporan detail, dan ekspor PDF/Excel perlu dihubungkan ke skema ini pada tahap implementasi berikutnya. Jangan arahkan pengguna ke aplikasi MySQL sebelum modul tersebut selesai dan diuji.

## Prinsip teknis

Aplikasi PHP harus menggunakan PDO MySQL dan prepared statements untuk query yang menerima input pengguna. Password disimpan dengan `password_hash()` dan diverifikasi dengan `password_verify()`. Regenerasi session ID setelah login, token CSRF, validasi upload gambar, otorisasi per peran, dan penilaian di sisi server wajib dipertahankan.
