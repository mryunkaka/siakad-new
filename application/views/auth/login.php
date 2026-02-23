<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login | SIAKAD SDN Rantau Kanan 2</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/font-awesome/css/font-awesome.min.css'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:300,400,500,600&display=swap" rel="stylesheet">

    <style>
        :root{
            --card-bg: rgba(255,255,255,.12);
            --card-border: rgba(255,255,255,.22);
            --text: #ffffff;
            --muted: rgba(255,255,255,.85);
            --input-bg: rgba(255,255,255,.14);
            --input-border: rgba(255,255,255,.32);
            --accent: #ffd24d;
            --accent-hover: #ffca2b;
        }

        body{
            font-family: 'Poppins', sans-serif;
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: var(--text);
            background:
                linear-gradient(rgba(7,39,66,.80), rgba(7,39,66,.90)),
                url('<?php echo base_url('assets/dist/img/boxed-bg.jpg'); ?>');
            background-size: cover;
            background-position: center;
        }

        .login-card{
            width: min(420px, 100%);
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            box-shadow: 0 10px 40px rgba(0,0,0,.45);
            padding: 34px 34px 22px;
        }

        @supports ((-webkit-backdrop-filter: blur(1px)) or (backdrop-filter: blur(1px))) {
            .login-card{
                -webkit-backdrop-filter: blur(14px);
                backdrop-filter: blur(14px);
            }
        }

        .logo-box{text-align:center;margin-bottom:14px;}
        .logo-circle{
            width: 92px;
            height: 92px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            box-shadow: 0 4px 18px rgba(0,0,0,.4);
        }
        .logo-circle img{width: 68px; max-width: 68px; height: auto; display:block;}

        .school-title{text-align:center;margin-bottom:18px;}
        .school-title h3{margin:0;font-weight:600;letter-spacing:.5px;}
        .school-title small{opacity:.9;}

        .alert-login{
            background: rgba(255,70,70,.18);
            border: 1px solid rgba(255,70,70,.5);
            color: #ffdede;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            margin: 0 0 14px;
            animation: fadeIn .25s ease;
        }
        @keyframes fadeIn{
            from{opacity:0; transform:translateY(-8px);}
            to{opacity:1; transform:translateY(0);}
        }

        .greeting{text-align:center;margin-bottom:14px;font-size:14px;opacity:.92;}
        #clock{font-weight:600;letter-spacing:1px;}

        .form-group{position:relative;margin-bottom:16px;}
        .form-group i{position:absolute;top:13px;left:14px;color:#f0f0f0;}

        .form-control{
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--text);
            height: 46px;
            padding-left: 40px;
            border-radius: 8px;
        }
        .form-control::placeholder{color: rgba(255,255,255,.82);}
        .form-control:focus{
            border-color: rgba(255,255,255,.75);
            box-shadow: 0 0 0 3px rgba(255,255,255,.14);
        }

        .btn-login{
            height: 46px;
            border-radius: 8px;
            background: var(--accent);
            color: #1b2a3a;
            font-weight: 600;
            border: none;
        }
        .btn-login:hover{background: var(--accent-hover);}

        .footer{
            text-align:center;
            font-size: 12px;
            margin-top: 14px;
            opacity: .88;
        }

        @media (max-width: 480px){
            .login-card{padding: 28px 20px 18px; border-radius: 16px;}
            .logo-circle{width: 84px; height: 84px;}
            .logo-circle img{width: 60px; max-width: 60px;}
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo-box">
        <div class="logo-circle">
            <img
                src="<?php echo base_url('assets/dist/img/tutwuri.png'); ?>"
                alt="Logo Tut Wuri Handayani"
                loading="eager"
                decoding="async"
                onerror="this.onerror=null;this.src='<?php echo base_url('assets/dist/img/tutwuri.jpg'); ?>';"
            >
        </div>
    </div>

    <div class="school-title">
        <h3>SIAKAD</h3>
        <small>SDN Rantau Kanan 2</small>
    </div>

    <?php if($this->session->flashdata('error')): ?>
        <div class="alert-login" id="alertError">
            <i class="fa fa-exclamation-triangle"></i>
            <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <div class="greeting">
        <div id="greet"></div>
        <div id="clock"></div>
    </div>

    <?php echo form_open('auth/check_login'); ?>
        <div class="form-group">
            <i class="fa fa-user"></i>
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
        </div>

        <div class="form-group">
            <i class="fa fa-lock"></i>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <button type="submit" name="submit" class="btn btn-login btn-block">Masuk</button>
    </form>

    <div class="footer">
        &copy; <?php echo date('Y');?> Sistem Informasi Akademik Sekolah
    </div>
</div>

<script>
    setTimeout(function(){
        var alert=document.getElementById("alertError");
        if(alert){
            alert.style.transition="opacity 0.5s";
            alert.style.opacity="0";
        }
    },4000);

    function updateClock(){
        const now=new Date();
        const h=now.getHours();
        const m=String(now.getMinutes()).padStart(2,'0');
        const s=String(now.getSeconds()).padStart(2,'0');

        document.getElementById("clock").innerHTML=h+":"+m+":"+s;

        let greet="Selamat Malam";
        if(h>=4 && h<11) greet="Selamat Pagi";
        else if(h>=11 && h<15) greet="Selamat Siang";
        else if(h>=15 && h<18) greet="Selamat Sore";

        document.getElementById("greet").innerHTML=greet;
    }
    setInterval(updateClock,1000);
    updateClock();
</script>

</body>
</html>

