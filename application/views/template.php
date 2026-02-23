<!DOCTYPE html>

<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>SIAKAD SDN Rantau Kanan 2</title>
<meta content="width=device-width, initial-scale=1" name="viewport">

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/Ionicons/css/ionicons.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/skins/_all-skins.min.css">

<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

/* ================= GLOBAL ================= */
body{
    font-family:'Poppins',sans-serif !important;
    background:#f4f6f9;
}

/* ================= HEADER ================= */
.main-header .logo{
    background:#0b2f4a !important;
    color:#fff !important;
    font-weight:700;
    letter-spacing:1px;
    font-size:18px;
}

.main-header .navbar{
    background:#0b2f4a !important;
}

.main-header .logo:hover{
    background:#082438 !important;
}

.sidebar-toggle{ color:#fff !important; }

/* ================= SIDEBAR ================= */
.main-sidebar{
    background:#ffffff !important;
    width:260px !important;
    border-right:1px solid #e1e1e1;
}

.content-wrapper,.main-footer{
    margin-left:260px !important;
}

/* ===== USER PANEL ===== */
.sidebar .user-panel{
    display:flex !important;
    align-items:center !important;
    padding:18px 15px !important;
    height:auto !important;
    border-bottom:1px solid #eaeaea;
}

.sidebar .user-panel .image{
    flex-shrink:0;
}

.sidebar .user-panel .image img{
    width:52px;
    height:52px;
    border-radius:50%;
    border:3px solid #0b2f4a;
}

.sidebar .user-panel .info{
    position:relative !important;
    left:auto !important;
    width:auto !important;
    padding-left:14px !important;
    white-space:normal !important;
    overflow:visible !important;
}

.sidebar .user-panel .info p{
    margin:0 !important;
    color:#222 !important;
    font-weight:600;
    font-size:14px;
    line-height:18px;
    white-space:normal !important;
    word-break:break-word !important;
}

.sidebar .user-panel .info .role{
    display:block;
    margin-top:4px;
    font-size:12px;
    color:#0b2f4a;
    font-weight:500;
}

/* ===== MENU ===== */
.sidebar-menu>li.header{
    color:#8c8c8c;
    font-size:11px;
    font-weight:700;
    letter-spacing:1px;
    padding:14px 15px 6px;
}

.sidebar-menu>li>a{
    color:#333 !important;
    font-size:14px;
    padding:12px 15px;
    border-left:4px solid transparent;
}

.sidebar-menu>li>a:hover{
    background:#f1f6fb !important;
    border-left:4px solid #0b2f4a;
    color:#0b2f4a !important;
}

.sidebar-menu>li.active>a{
    background:#e3edf7 !important;
    border-left:4px solid #0b2f4a;
    color:#0b2f4a !important;
    font-weight:600;
}

/* SUBMENU */
.treeview-menu{
    background:#fafafa;
}

.treeview-menu>li>a{
    color:#555 !important;
    padding:10px 15px 10px 38px;
    font-size:13px;
}

.treeview-menu>li>a:hover{
    background:#edf4fb !important;
    color:#0b2f4a !important;
}

.treeview-menu>li.active>a{
    background:#dbe8f5 !important;
    color:#0b2f4a !important;
    font-weight:600;
}

/* CONTENT */
.content-wrapper{
    background:#f4f6f9;
}

/* FOOTER */
.main-footer{
    background:#ffffff;
    border-top:1px solid #ddd;
}
/* ===== FIX HOVER USER NAVBAR ===== */
.navbar-nav > .user-menu > .dropdown-toggle:hover,
.navbar-nav > .user-menu > .dropdown-toggle:focus {
    background: transparent !important;
}

.navbar-nav > .user-menu > .dropdown-toggle:hover span,
.navbar-nav > .user-menu > .dropdown-toggle:focus span {
    color: #e5e7eb !important; /* warna normal seperti awal */
}
.navbar-nav > .user-menu > .dropdown-toggle:hover {
    background: rgba(255,255,255,0.08) !important;
    border-radius: 6px;
}
/* ===== CUSTOM LOGOUT BUTTON ===== */
.logout-btn{
    background: linear-gradient(135deg,#dc3545,#b02a37);
    border: none;
    font-weight: 600;
    border-radius: 10px;
    padding: 10px 0;
    font-size: 14px;
    color: #fff !important;
    transition: 0.3s ease;
}

.logout-btn:hover{
    opacity: 0.9;
    color:#fff !important;
}

</style>

</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php
$level = $this->session->userdata('id_level_user');
switch($level){
    case 1: $role="Administrator"; break;
    case 2: $role="Wali Kelas"; break;
    case 3: $role="Guru"; break;
    case 4: $role="Keuangan"; break;
    default: $role="Pengguna Sistem";
}
?>

<header class="main-header">
<a href="<?php echo site_url('tampilan_utama'); ?>" class="logo">
<span class="logo-mini"><b>S</b>D</span>
<span class="logo-lg"><b>SIAKAD</b> SD</span>
</a>

<nav class="navbar navbar-static-top">
<a href="#" class="sidebar-toggle" data-toggle="push-menu"></a>

<div class="navbar-custom-menu">
<ul class="nav navbar-nav">

<li class="dropdown user user-menu">
<a href="#" class="dropdown-toggle" data-toggle="dropdown">
<img src="<?php echo base_url(); ?>assets/dist/img/pasfotogur.png" class="user-image">
<span class="hidden-xs" style="color:#e5e7eb; font-weight:500;">
<?php echo $this->session->userdata('nama_lengkap'); ?>
</span>

</a>

<ul class="dropdown-menu dropdown-menu-right" style="padding:0; border-radius:12px; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,0.15); border:none;">

<li class="user-header" style="background:linear-gradient(135deg,#0b2f4a,#124a70); padding:25px 15px; text-align:center; color:#fff;">

    <img src="<?php echo base_url(); ?>assets/dist/img/user2-160x160.jpg" 
         class="img-circle" 
         style="width:90px; height:90px; border:4px solid #fff; box-shadow:0 4px 10px rgba(0,0,0,0.1);">

    <p style="margin-top:12px; font-weight:600; color:#fff; font-size:16px;">
        <?php echo $this->session->userdata('nama_lengkap'); ?>
        <br>
        <small style="color:#cbd5e1; font-size:13px;">
            <?php echo $role; ?>
        </small>
    </p>

</li>


  <li class="user-footer" style="background:#ffffff; padding:18px;">
    <?php echo anchor(
        'auth/logout',
        '<i class="fa fa-sign-out"></i> Logout',
        'class="btn btn-block logout-btn"'
    ); ?>
</li>


</ul>

</li>

</ul>
</div>
</nav>
</header>

<!-- SIDEBAR -->

<aside class="main-sidebar">
<section class="sidebar">

<div class="user-panel">
<div class="image">
<img src="<?php echo base_url(); ?>assets/dist/img/pasfotogur.png">
</div>

<div class="info">
<p><?php echo $this->session->userdata('nama_lengkap'); ?></p>
<span class="role"><?php echo $role; ?></span>
</div>
</div>

<ul class="sidebar-menu" data-widget="tree">
<li class="header">MENU NAVIGASI</li>

<?php $current_controller = $this->uri->segment(1); ?>

<li class="<?php echo ($current_controller == 'tampilan_utama' || $current_controller == '') ? 'active' : ''; ?>">
<a href="<?php echo site_url('tampilan_utama'); ?>">
<i class="fa fa-home"></i> <span>Dashboard</span>
</a>
</li>

<?php
$id_level_user = (int) $this->session->userdata('id_level_user');
$main_menu = array();
if ($id_level_user > 0)
{
	$sql_menu = "SELECT * FROM tabel_menu WHERE id IN
	(SELECT id_menu FROM tbl_user_rule WHERE id_level_user = $id_level_user)
	AND is_main_menu = 0";

	$main_menu = $this->db->query($sql_menu)->result();
}

foreach ($main_menu as $main){

$submenu = $this->db->get_where('tabel_menu',['is_main_menu'=>$main->id]);
$is_active = ($current_controller == $main->link) ? 'active' : '';

if($submenu->num_rows()>0){

$submenu_active='';
foreach($submenu->result() as $sub){
if($current_controller == $sub->link){ $submenu_active='active menu-open'; }
}

echo "<li class='treeview $submenu_active'>";
echo "<a href='#'>
<i class='".$main->icon."'></i>
<span>".$main->nama_menu."</span>
<span class='pull-right-container'>
<i class='fa fa-angle-left pull-right'></i>
</span></a>";

echo "<ul class='treeview-menu'>";
foreach($submenu->result() as $sub){
$sub_active=($current_controller==$sub->link)?'active':'';
echo "<li class='$sub_active'>".anchor($sub->link,"<i class='".$sub->icon."'></i> ".$sub->nama_menu)."</li>";
}
echo "</ul></li>";

}else{
echo "<li class='$is_active'>".anchor($main->link,"<i class='".$main->icon."'></i> <span>".$main->nama_menu."</span>")."</li>";
}
}
?>

</ul>
</section>
</aside>

<div class="content-wrapper">
<section class="content-header">
<h1>Dashboard <small>Sistem Akademik</small></h1>
</section>

<?php echo $contents; ?>

</div>

<footer class="main-footer">
<strong>© <?php echo date('Y');?> SIAKAD SDN Rantau Kanan 2</strong>
</footer>

</div>

<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="<?php echo base_url(); ?>assets/dist/js/adminlte.min.js"></script>

</body>
</html>
