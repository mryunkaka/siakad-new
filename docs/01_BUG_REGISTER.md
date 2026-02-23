# Bug Register

## Legend
- Severity: `Critical` | `High` | `Medium` | `Low`
- Status: `Open` | `In Progress` | `Fixed` | `Verified`

## Entries

### BUG-001
- Severity: High
- Status: Fixed
- Module: Template/Menu
- File: `application/views/template.php`
- Symptom: `mysqli_sql_exception` SQL syntax near `) AND is_main_menu = 0`
- Root Cause: `id_level_user` kosong menghasilkan query invalid.
- Fix: Cast ke integer + parameter binding + fallback menu kosong.
- Verification:
  - Login user valid -> menu tampil normal.
  - Session invalid -> tidak fatal.

### BUG-002
- Severity: High
- Status: Fixed
- Module: Access Helper
- File: `application/helpers/mylib_helper.php`
- Symptom: Potensi warning/error saat menu URL tidak terdaftar.
- Root Cause: akses `$menu['id']` tanpa validasi `empty`.
- Fix: Validasi menu sebelum query rule + cast `id_level_user`.
- Verification:
  - URL terdaftar -> akses normal.
  - URL tidak terdaftar -> pesan terkontrol (tanpa SQL crash).

### BUG-003
- Severity: High
- Status: Fixed
- Module: Jadwal
- File: `application/controllers/Jadwal.php`
- Symptom: Raw SQL dari `$_GET` (`kd_jurusan`, `kelas`, dll.) rentan SQL injection dan error data.
- Root Cause: concatenation input langsung ke query string.
- Fix: Refactor query ke Query Builder, input request dipindah ke `$this->input->get_post()`/`post()`, cast integer untuk ID penting.
- Verification:
  - `php -l application/controllers/Jadwal.php` -> lolos.
  - Endpoint update jadwal tetap menerima update guru/ruangan/hari/jam tanpa fatal error.
  - Filter `dataJadwal` berjalan via parameter request tanpa raw SQL concatenation.

### BUG-004
- Severity: Medium
- Status: Open
- Module: Laporan/Nilai
- File: `application/controllers/Laporan_nilai.php`, `application/controllers/Nilai.php`
- Symptom: Banyak raw SQL manual, sulit maintain saat migrasi CI4.
- Root Cause: query belum distandarisasi repository/model layer.
- Fix Plan: Pindah query ke model + parameter binding.
