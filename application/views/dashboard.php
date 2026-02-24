<?php
date_default_timezone_set('Asia/Makassar');

$jam = date('H:i');

$masuk1     = "07:00";
$istirahat  = "09:30";
$masuk2     = "10:00";
$pulang     = "12:00";

/* ================= FUNCTION STATUS ================= */

function statusWaktu($jam,$masuk1,$istirahat,$masuk2,$pulang){
    if($jam >= $masuk1 && $jam < $istirahat){
        return 'Jam Pelajaran';
    } elseif($jam >= $istirahat && $jam < $masuk2){
        return 'Jam Istirahat';
    } elseif($jam >= $masuk2 && $jam <= $pulang){
        return 'Jam Pelajaran';
    } else {
        return 'Jam Pulang';
    }
}

function getProgress($jam,$masuk1,$pulang){
    $current = strtotime($jam);
    $start   = strtotime($masuk1);
    $end     = strtotime($pulang);

    if($current <= $start) return 0;
    if($current >= $end) return 100;

    $total = $end - $start;
    $jalan = $current - $start;

    return round(($jalan / $total) * 100);
}

$status   = statusWaktu($jam,$masuk1,$istirahat,$masuk2,$pulang);
$progress = getProgress($jam,$masuk1,$pulang);
?>

<style>

/* ================= HEADER ================= */

.welcome-box{
    background:linear-gradient(135deg,#2f5f80,#3c8dbc 60%,#58a9d6);
    color:white;
    border-radius:18px;
    padding:26px 30px;
    margin-bottom:35px;
    box-shadow:0 10px 30px rgba(0,0,0,.15);
}

.welcome-title{
    font-size:24px;
    font-weight:700;
}

.welcome-sub{
    font-size:14px;
    opacity:.9;
}

.big-clock{
    font-size:30px;
    font-weight:700;
    margin-top:8px;
    letter-spacing:2px;
}

.logo-box img{
    width:95px;
    background:white;
    padding:6px;
    border-radius:14px;
    box-shadow:0 8px 18px rgba(0,0,0,.25);
}

/* ================= STAT CARD ================= */

.stat-card{
    background:#fff;
    border-radius:18px;
    padding:22px;
    margin-bottom:25px;
    box-shadow:0 6px 18px rgba(0,0,0,.10);
    transition:.25s;
    text-align:center;
}

.stat-card:hover{
    transform:translateY(-5px);
}

.stat-icon{
    font-size:38px;
    margin-bottom:10px;
}

.stat-title{
    font-size:14px;
    color:#6c757d;
}

.stat-value{
    font-size:30px;
    font-weight:700;
}

.stat-action{
    display:inline-block;
    margin-top:10px;
    padding:6px 18px;
    border-radius:20px;
    font-size:13px;
    background:#f1f5f9;
    color:#333;
}

/* ================= STATUS MODERN ================= */

.status-box{
    background:#fff;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    margin-top:20px;
}

.status-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.status-main{
    font-size:20px;
    font-weight:700;
    color:#2f5f80;
}

.status-percent{
    font-size:26px;
    font-weight:700;
    color:#3c8dbc;
}

.progress-container{
    height:10px;
    background:#e5e7eb;
    border-radius:10px;
    overflow:hidden;
    margin-bottom:25px;
}

.progress-bar-fill{
    height:100%;
    background:linear-gradient(90deg,#2f5f80,#3c8dbc);
    transition:.4s;
}

.timeline-item{
    padding:14px 18px;
    border-radius:12px;
    margin-bottom:12px;
    background:#f8fafc;
    display:flex;
    justify-content:space-between;
    border:1px solid #e5e7eb;
    transition:.25s;
}

.timeline-item.active{
    background:#eef6fb;
    border-left:4px solid #3c8dbc;
    font-weight:600;
}

</style>

<section class="content">

<!-- ================= HEADER ================= -->

<div class="welcome-box">
    <div class="row">
        <div class="col-md-8 col-xs-8">
            <div class="welcome-title">
                Selamat Datang, <?php echo $this->session->userdata('nama_lengkap'); ?>
            </div>
            <div class="welcome-sub">
                Sistem Informasi Akademik Sekolah
            </div>
            <div class="big-clock" id="jam"></div>
            <div><?php echo date('l, d F Y'); ?></div>
        </div>

        <div class="col-md-4 col-xs-4 text-right logo-box">
            <img src="<?php echo base_url('assets/dist/img/tutwuri2.jpg'); ?>">
        </div>
    </div>
</div>

<!-- ================= STATISTIK ================= -->

<div class="row">

<div class="col-md-3">
<a href="<?php echo site_url('user'); ?>">
<div class="stat-card">
<i class="fa fa-id-badge stat-icon text-aqua"></i>
<div class="stat-title">Pengguna Sistem</div>
<div class="stat-value text-aqua"><?php echo $user['hasil']; ?></div>
<span class="stat-action">Buka</span>
</div>
</a>
</div>

<div class="col-md-3">
<a href="<?php echo site_url('siswa'); ?>">
<div class="stat-card">
<i class="fa fa-users stat-icon text-red"></i>
<div class="stat-title">Data Siswa</div>
<div class="stat-value text-red"><?php echo $siswa['hasil']; ?></div>
<span class="stat-action">Buka</span>
</div>
</a>
</div>

<div class="col-md-3">
<a href="<?php echo site_url('guru'); ?>">
<div class="stat-card">
<i class="fa fa-user stat-icon text-green"></i>
<div class="stat-title">Data Guru</div>
<div class="stat-value text-green"><?php echo $guru['hasil']; ?></div>
<span class="stat-action">Buka</span>
</div>
</a>
</div>

<div class="col-md-3">
<a href="<?php echo site_url('ruangan'); ?>">
<div class="stat-card">
<i class="fa fa-building stat-icon text-yellow"></i>
<div class="stat-title">Ruangan Kelas</div>
<div class="stat-value text-yellow"><?php echo $ruangan['hasil']; ?></div>
<span class="stat-action">Buka</span>
</div>
</a>
</div>

</div>

<!-- ================= STATUS SEKOLAH ================= -->

<div class="status-box">

    <div class="status-header">
        <div class="status-main">
            <?php echo $status; ?>
        </div>
        <div class="status-percent">
            <?php echo $progress; ?>%
        </div>
    </div>

    <div class="progress-container">
        <div class="progress-bar-fill" style="width:<?php echo $progress; ?>%"></div>
    </div>

    <div class="timeline-item <?php if($jam >= $masuk1 && $jam < $istirahat) echo 'active'; ?>">
        <span>07:00 - 09:30</span>
        <span>Pelajaran 1</span>
    </div>

    <div class="timeline-item <?php if($jam >= $istirahat && $jam < $masuk2) echo 'active'; ?>">
        <span>09:30 - 10:00</span>
        <span>Istirahat</span>
    </div>

    <div class="timeline-item <?php if($jam >= $masuk2 && $jam <= $pulang) echo 'active'; ?>">
        <span>10:00 - 12:00</span>
        <span>Pelajaran 2</span>
    </div>

    <div class="timeline-item <?php if($jam > $pulang) echo 'active'; ?>">
        <span>12:00 </span>
        <span>Pulang</span>
    </div>

</div>

</section>

<script>
function updateJam(){
    const now = new Date();
    const jam   = String(now.getHours()).padStart(2,'0');
    const menit = String(now.getMinutes()).padStart(2,'0');
    const detik = String(now.getSeconds()).padStart(2,'0');
    document.getElementById('jam').innerHTML = jam+":"+menit+":"+detik;
}
setInterval(updateJam,1000);
updateJam();
</script>
