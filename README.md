# SIAKAD SD - Panduan Penggunaan

SIAKAD SD adalah aplikasi akademik sekolah berbasis CodeIgniter 3 untuk mengelola data siswa, guru, kelas, kurikulum, jadwal, nilai, wali kelas, dan pembayaran.

README ini ditulis untuk membantu pengguna baru memahami:
- apa fungsi aplikasi
- siapa saja role yang tersedia
- urutan setup yang benar
- alur kerja tiap menu utama
- catatan penting dari data contoh bawaan project

## Ringkasan

Fungsi utama aplikasi:
- mengelola master data sekolah
- mengatur tahun akademik aktif
- menyusun kurikulum per jurusan dan tingkatan
- membuat jadwal pelajaran
- input nilai oleh guru
- akses wali kelas untuk melihat kelasnya
- pencatatan pembayaran oleh admin/keuangan

Teknologi:
- PHP 8.1+
- CodeIgniter 3
- MySQL / MariaDB
- AdminLTE / jQuery / DataTables

## Role Pengguna

Role yang tersedia di database contoh:

| ID Level | Role | Fungsi Utama |
| --- | --- | --- |
| `1` | Admin | Mengelola seluruh data akademik dan konfigurasi |
| `2` | Wali Kelas | Melihat portal wali kelas, data siswa kelas, dan laporan nilai |
| `3` | Guru | Melihat jadwal mengajar dan input nilai |
| `4` | Keuangan | Mengelola pembayaran siswa |

Catatan:
- hak akses menu ditentukan oleh tabel `tabel_menu` dan `tbl_user_rule`
- beberapa menu juga punya pembatasan tambahan di controller
- contoh: menu pembayaran hanya boleh diakses Admin dan Keuangan

## Akun Demo

Data contoh bawaan:

| Role | Username | Password |
| --- | --- | --- |
| Administrator | `zuhri` | `123456` |
| Wali Kelas | `adam` | `123456` |
| Guru | `dita` | `123456` |
| Keuangan | `putri` | `123456` |

Catatan:
- login bisa berasal dari `tbl_user` atau `tbl_guru`
- untuk role Guru dan Wali Kelas, aplikasi mencoba menghubungkan akun ke data guru berdasarkan `username`

## Struktur Menu

Menu utama yang umum muncul untuk admin:
- `Dashboard`
- `Data Siswa`
- `Data Guru`
- `Data Master`
- `Jadwal Pelajaran`
- `Peserta Didik`
- `Walikelas`
- `Pengguna Sistem`
- `Menu`
- `Nilai`
- `Laporan Nilai`
- `Form Pembayaran`

Submenu pada `Data Master`:
- `Mata Pelajaran`
- `Ruangan Kelas`
- `Tingkatan Kelas`
- `Jurusan`
- `Tahun Akademik`
- `Kelas`
- `Kurikulum`

Menu khusus wali kelas:
- `Portal Wali Kelas`
- `Siswa Kelas`

## Persyaratan Sistem

- PHP 8.1 sampai 8.4
- MySQL / MariaDB
- Apache atau web server lain
- `mod_rewrite` aktif direkomendasikan

## Instalasi

1. Letakkan project di folder web server.
2. Buat database, misalnya `pis_akademik`.
3. Import file `database/pis_akademik.sql`.
4. Atur koneksi database di `application/config/database.php`.
5. Atur `base_url` di `application/config/config.php`.
6. Pastikan folder `uploads/` bisa ditulis jika memakai upload file pembayaran atau import.
7. Buka aplikasi, misalnya `http://localhost:2027/`.

## Konfigurasi Penting

File yang paling sering diubah:
- `application/config/database.php`
- `application/config/config.php`
- `application/config/routes.php`

Yang perlu diperiksa saat pertama kali setup:
- host database
- username dan password database
- nama database
- `base_url`

## Alur Setup Awal

Agar aplikasi bisa dipakai tanpa error data kosong, urutan yang disarankan adalah:

1. Login sebagai Admin.
2. Cek `Tahun Akademik` dan pastikan ada satu yang aktif.
3. Cek `Jurusan`.
4. Cek `Tingkatan Kelas`.
5. Cek `Kelas`.
6. Cek `Mata Pelajaran`.
7. Cek `Ruangan Kelas`.
8. Cek `Data Guru`.
9. Isi `Kurikulum`.
10. Generate `Jadwal Pelajaran`.
11. Tentukan `Walikelas`.
12. Baru lanjut ke input nilai, portal wali kelas, dan pembayaran.

Jika salah satu data master di atas belum lengkap, menu turunan biasanya tampil kosong walaupun halaman bisa dibuka.

## Alur Kerja per Modul

### 1. Data Siswa

Digunakan untuk:
- tambah siswa
- edit siswa
- import siswa dari Excel
- melihat siswa aktif
- proses naik kelas

Saran alur:
1. pastikan `Kelas` sudah ada
2. input siswa manual atau import Excel
3. cek kelas aktif siswa
4. gunakan fitur naik kelas saat pergantian tahun ajaran

### 2. Data Guru

Digunakan untuk:
- menyimpan data guru
- menyambungkan akun guru dengan username login

Catatan penting:
- untuk role Guru atau Wali Kelas, sebaiknya `username` di `Data Guru` sama dengan `username` akun login
- jika tidak sinkron, beberapa fitur berbasis `id_guru` bisa gagal mendeteksi data guru

### 3. Mata Pelajaran

Digunakan untuk menyimpan daftar mapel.

Catatan:
- tabel mapel bawaan hanya berisi kode dan nama mapel
- pemetaan mapel ke jurusan/tingkatan dilakukan lewat `Kurikulum`, bukan lewat tabel mapel langsung

### 4. Ruangan Kelas

Digunakan untuk menentukan ruangan yang nanti dipakai di jadwal.

### 5. Tingkatan Kelas

Digunakan untuk level kelas, misalnya:
- `7`
- `8`
- `9`

Catatan penting:
- data di `Kelas` bergantung ke `Tingkatan Kelas`
- jika data tingkatan rusak atau tidak cocok, beberapa view seperti `view_kelas` bisa kosong

### 6. Jurusan

Digunakan untuk kategori jurusan, misalnya:
- `IPA`
- `IPS`

### 7. Kelas

Digunakan untuk menyimpan kombinasi:
- kode kelas
- nama kelas
- tingkatan
- jurusan

Contoh:
- `7-A1` untuk kelas IPA
- `7-A2` untuk kelas IPS

### 8. Tahun Akademik

Digunakan untuk:
- menentukan tahun ajaran aktif
- menentukan semester aktif

Catatan penting:
- banyak modul membaca data dari tahun akademik aktif
- pastikan hanya ada satu data dengan `is_aktif = Y`

### 9. Kurikulum

Kurikulum adalah jantung pembentukan jadwal.

Modul ini dipakai untuk:
- menambah nama kurikulum
- menentukan aktif / tidak aktif
- menambah detail kurikulum per mapel, jurusan, dan tingkatan

Alur yang benar:
1. buka `Data Master > Kurikulum`
2. tambah atau pilih kurikulum
3. klik `View Detail`
4. pilih filter `Jurusan` dan `Tingkatan`
5. klik `Tambah Data`
6. tambahkan mapel satu per satu ke detail kurikulum

Catatan penting:
- `Generate Jadwal` mengambil data dari `tbl_kurikulum_detail`
- jika detail kurikulum untuk jurusan tertentu kosong, jadwal jurusan tersebut tidak akan terbentuk
- pada database contoh repo ini, data `tbl_kurikulum_detail` bawaan hanya berisi entri `IPA`
- artinya, untuk `IPS` Anda harus menambah detail kurikulum sendiri terlebih dahulu

### 10. Jadwal Pelajaran

Halaman ini dipakai untuk dua tahap:

Tahap 1, generate slot jadwal:
1. buka menu `Jadwal Pelajaran`
2. klik tombol `Generate Jadwal`
3. pilih `Kurikulum`
4. pilih `Semester`
5. klik `Generate Data`

Tahap 2, isi detail jadwal:
1. pilih `Jurusan`
2. pilih `Tingkatan Kelas`
3. pilih `Kelas`
4. sistem akan menampilkan daftar mapel hasil generate
5. isi `Guru`
6. isi `Ruangan`
7. isi `Hari`
8. isi `Jam`

Catatan penting:
- di aplikasi saat ini tidak ada form tambah jadwal manual satu baris dari nol
- data jadwal dibuat massal dari kurikulum, lalu diedit di halaman jadwal
- jika tombol generate diklik berulang, potensi data ganda bisa terjadi karena belum ada validasi duplikasi yang ketat
- jika jurusan tertentu tidak muncul, hampir selalu penyebabnya adalah detail kurikulum jurusan tersebut belum ada

### 11. Walikelas

Modul ini dipakai untuk menetapkan guru sebagai wali kelas pada tahun akademik aktif.

Ketergantungan:
- data guru harus ada
- data kelas harus ada
- tahun akademik aktif harus ada

### 12. Portal Wali Kelas

Dipakai oleh role Wali Kelas untuk melihat ringkasan kelas yang menjadi tanggung jawabnya.

Agar modul ini bekerja:
- akun wali kelas harus punya `id_guru`
- guru tersebut harus terpasang di `tbl_walikelas`
- harus ada tahun akademik aktif

Jika tidak cocok, aplikasi akan menampilkan pesan bahwa data wali kelas belum ditemukan.

### 13. Siswa Kelas

Dipakai wali kelas untuk melihat daftar siswa di kelasnya.

### 14. Nilai

Dipakai guru untuk:
- melihat daftar kelas mengajar
- masuk ke form penilaian
- menyimpan nilai siswa

Ketergantungan:
- jadwal sudah ada
- guru pada jadwal sudah terisi
- siswa pada kelas tersebut sudah ada

### 15. Laporan Nilai

Dipakai untuk melihat atau mencetak hasil nilai siswa.

### 16. Form Pembayaran

Dipakai Admin dan Keuangan untuk:
- mencatat pembayaran
- edit pembayaran
- cetak kwitansi

Data yang biasa diisi:
- tanggal
- siswa
- jenis pembayaran
- nominal
- metode
- status
- keterangan
- bukti pembayaran

## Alur Operasional yang Direkomendasikan

Untuk sekolah yang baru mulai menggunakan aplikasi ini, urutan kerja paling aman adalah:

1. isi semua master data
2. tentukan tahun akademik aktif
3. isi data guru
4. isi data kelas
5. isi kurikulum per jurusan dan tingkatan
6. generate jadwal
7. lengkapi guru, ruangan, hari, jam di jadwal
8. tetapkan wali kelas
9. input atau import siswa
10. input nilai
11. gunakan portal wali kelas dan laporan
12. gunakan pembayaran bila dibutuhkan

## Seeder

Tersedia controller `Seeder` untuk membantu memperbaiki sebagian data menu dan relasi guru/wali kelas.

Fungsi utamanya:
- memastikan menu wali kelas tersedia
- memastikan rule akses level 2 tersedia
- menghubungkan akun `tbl_user` level 2/3 ke `tbl_guru` berdasarkan username
- mencoba mengisi slot wali kelas kosong untuk tahun akademik aktif

Catatan:
- seeder ini membantu struktur akses
- seeder ini tidak mengisi detail kurikulum IPS, tidak membuat jadwal otomatis, dan tidak melengkapi master data sekolah

## Catatan Data Contoh Project

Beberapa hal yang perlu dipahami dari database contoh:

- data bawaan belum selalu lengkap untuk semua alur
- jurusan `IPS` ada di data kelas
- tetapi detail kurikulum `IPS` bawaan tidak tersedia
- akibatnya generate jadwal `IPS` tidak menghasilkan data sampai detail kurikulum IPS diisi manual

Jadi jika menemukan halaman kosong, cek dulu:
- apakah master data relasi sudah lengkap
- apakah tahun akademik aktif ada
- apakah kurikulum detail sudah diisi
- apakah akun login sudah terhubung ke data guru

## Troubleshooting

### Halaman terbuka tapi tabel kosong

Cek:
- data master terkait ada atau tidak
- view database seperti `view_kelas` / `view_walikelas` menghasilkan data atau tidak
- data referensi `jurusan`, `tingkatan`, `kelas`, dan `tahun akademik` cocok

### Jadwal tidak muncul untuk jurusan tertentu

Penyebab paling umum:
- `tbl_kurikulum_detail` untuk jurusan tersebut belum ada

Solusi:
- buka `Kurikulum`
- masuk ke `View Detail`
- tambahkan mapel untuk jurusan dan tingkatan yang dibutuhkan
- generate ulang jadwal

### Wali kelas tidak bisa masuk portal

Cek:
- akun login punya `id_guru`
- username akun sama dengan username pada `Data Guru`
- ada data `tbl_walikelas` untuk tahun akademik aktif

### Guru tidak melihat jadwal mengajar

Cek:
- jadwal sudah dibuat
- `id_guru` pada jadwal sudah terisi
- akun guru terhubung dengan benar

### Login berhasil tapi menu tidak lengkap

Cek:
- role user
- rule akses pada `tbl_user_rule`
- struktur menu pada `tabel_menu`

## Catatan Kompatibilitas PHP 8

Project ini sudah disesuaikan agar dapat berjalan di PHP 8.1+.

Perbaikan yang telah dilakukan di repo ini meliputi:
- penyesuaian beberapa library lama agar kompatibel dengan PHP 8
- perbaikan syntax dan error parsing di beberapa controller
- penyesuaian agar login, menu, dan beberapa modul utama dapat berjalan lebih stabil

## Rekomendasi Belajar untuk Pengguna Baru

Jika baru pertama kali memakai aplikasi ini, pelajari dalam urutan berikut:

1. pahami menu dan role
2. pahami master data
3. pahami hubungan `Jurusan -> Tingkatan -> Kelas`
4. pahami hubungan `Kurikulum -> Jadwal`
5. pahami hubungan `Guru -> Jadwal -> Nilai`
6. pahami hubungan `Wali Kelas -> Portal Wali Kelas`

Urutan ini akan membuat aplikasi lebih mudah dipelajari dibanding langsung mencoba semua menu sekaligus.

## Penutup

SIAKAD ini paling stabil jika dipakai dengan pendekatan berurutan:
- lengkapi data master
- aktifkan tahun akademik
- isi kurikulum
- generate jadwal
- baru operasikan modul akademik lainnya

Jika ingin mengembangkan project ini lebih lanjut, area yang paling layak ditingkatkan adalah:
- validasi duplikasi generate jadwal
- wizard setup awal
- auto-clone kurikulum antar jurusan
- dokumentasi menu per role
- validasi integritas data master
