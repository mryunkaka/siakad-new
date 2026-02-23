# TODO & Checklist Eksekusi

## A. Stabilization (CI3 di PHP 8.4)
- [x] Perbaiki SQL menu saat `id_level_user` kosong.
- [x] Tambah guard akses menu pada helper.
- [x] Refactor semua raw SQL berisiko di controller `Jadwal`.
- [ ] Refactor raw SQL `Nilai` dan `Laporan_nilai`.
- [ ] Aktifkan logging CI (`log_threshold`) untuk staging.
- [ ] Buat data seed minimal untuk uji CRUD otomatis.

## B. CRUD Test Matrix

### Auth
- [ ] Login admin
- [ ] Login guru
- [ ] Logout
- [ ] Session timeout

### Master Data
- [ ] CRUD Guru
- [ ] CRUD Siswa
- [ ] CRUD Kelas
- [ ] CRUD Mapel
- [ ] CRUD Ruangan
- [ ] CRUD User + Rule menu

### Akademik
- [ ] Generate jadwal
- [ ] Update jadwal (guru/ruang/hari/jam)
- [ ] Input nilai
- [ ] Cetak laporan nilai

## C. Migrasi CI4
- [ ] Buat project CI4 baru untuk porting bertahap.
- [ ] Definisikan layer service/repository bersama.
- [ ] Port module Auth + Session.
- [ ] Port module Dashboard + Menu.
- [ ] Port module Master Data.
- [ ] Port module Jadwal/Nilai/Laporan.
- [ ] Regression test end-to-end.

## D. Aturan Konsistensi Kode
- [ ] Hindari nama variabel ganda untuk entitas sama.
- [ ] Semua input masuk lewat validasi.
- [ ] Semua query parameterized.
- [ ] Semua bug baru dicatat di `docs/01_BUG_REGISTER.md`.
