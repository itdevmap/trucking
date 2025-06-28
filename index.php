<?php
session_set_cookie_params(3600);
session_start();
$tanggalku=date("Y-m-d H:m:s");
include "koneksi.php"; 

 
if($_SERVER['REQUEST_METHOD'] == "POST"){
	$userid=$_POST['userid']; 
	$pass=$_POST['pass'];  
	$passx= md5($_POST['pass']);
	
	$sql = mysqli_query($koneksi,"SELECT * from m_user_tr WHERE id_user ='$userid' AND password='$passx' and status ='1' ");
	$cek = mysqli_num_rows($sql);
	if($cek > 0 ){	
		$_SESSION['id_user'] = $userid;	
		header('location:main.php');
	}else{
		$cat = "Username/password anda salah !...";
	}
}else{
	$cat = '';
}


?>

<html lang="en">
	<head>  
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>TRUCKING EKSPEDISI</title>
	<link rel="icon" type="image/png" sizes="16x16" href="img/pav.png">
    <link href="css/style_login.css" rel="stylesheet"> 
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<script>
		function checkvalue() { 
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			var userid = document.getElementById('userid').value; 
			var password = document.getElementById('pass').value; 
			if(!userid.match(/\S/)) {
				alert ('UserID harus diisi..');				
				return false;
			}else if(!password.match(/\S/)) {
				alert ('Password harus diisi..');
				return false;
			} else {
				return true;
			}
		}
	</script>
	<style>@import url('https://fonts.googleapis.com/css?family=Mukta');
		body{
		  font-family: 'Tahoma', sans-serif;
			height:100vh;
			min-height:550px;
			background-image: url(http://www.planwallpaper.com/static/images/Free-Wallpaper-Nature-Scenes.jpg);
			background-repeat: no-repeat;
			background-size:cover;
			background-position:center;
			position:relative;
			overflow-y: hidden;
		}
		a{
		  text-decoration:none;
		  color:#444444;
		}
		.login-reg-panel{
			position: relative;
			top: 50%;
			transform: translateY(-50%);
			//text-align:center;
			width:50%;
			right:0;left:0;
			margin:auto;
			height:250px;
			//background-color: rgba(236, 48, 20, 0.9);
			background-image:url(css/img/back1.jpg);
			background-size: cover;
			border:1px solid #fff;
		}
		.white-panel{
			background-color: rgba(255,255, 255, 1);
			height:350px;
			position:absolute;
			border-radius:5px;
			top:-50px;
			width:48%;
			right:calc(50% - 50px);
			transition:.3s ease-in-out;
			z-index:0;
			box-shadow: 0 0 15px 9px #00000096;
		}
		
		
		.register-info-box{
			width:30%;
			padding:0 50px;
			top:20%;
			right:0;
			position:absolute;
			text-align:left;
			
		}
		
		@media (max-width: 767px)
		{
			.white-panel {
				background-color: rgba(255,255, 255, 1);
				height:200px;
				position:absolute;
				top:-50px;
				width:60%;
				right:calc(50% - 50px);
				transition:.3s ease-in-out;
				z-index:0;
				box-shadow: 0 0 15px 9px #00000096;
			}
			.login-reg-panel{
				position: relative;
				top: 50%;
				transform: translateY(-50%);
				text-align:center;
				width:90%;
				right:0;left:0;
				margin:auto;
				height:140px;
				background-color: rgba(236, 48, 20, 0.9);
			}
		}
	</style>
	</head>
	<body style ="background-image:url(css/img/back.jpg)">	
	
	
		<div class="login-reg-panel">
					
			
							
			<div class="white-panel">
				<form method="post" name ="myform" action="index.php?action=xy"  onsubmit="return checkvalue(this)" >
					<div class="card-group">
						<div class="card p-4"  style ="border:none">
							<div class="card-body" >
								<br>
						  	   	<img src="css/img/logo.png" style="width:100%">		
								<br>								
								<?php if ($cat != ''){?>										
								 <p class="text-muted" style="text-align:right;color:#f63c3a  !important;margin-bottom:10px" ><i class="fa fa-exclamation-circle"></i> <?php echo $cat; ?></p>
								<?php }?>
								
								<div class="input-group mb-3">
									<div class="input-group-prepend">									
										<span class="input-group-text"><i class="fa fa-user"></i></span>
									</div>
									<input class="form-control" type="text" id="userid" name ="userid" placeholder="Username">
								</div>
								<div class="input-group mb-4">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="fa fa-lock"></i></i></span>
									</div>
									<input class="form-control" type="password" id="pass" name="pass" placeholder="Password">
								</div>
								<div class="row">
									<div class="col-12">
									<button class="btn btn-primary px-4" type="submit" style="width:100%">
									<i class="fa fa-key"></i><b> LOGIN</b></button>
								</div>							
							</div>	
						</div>
					</div>
			   </form>
			</div>
		</div>	
	
	
	</body>
</html>
