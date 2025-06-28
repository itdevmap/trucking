<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
include "lib.php"; 


if(!isset($_SESSION['id_user'])  ){
 header("location:logout.php"); 
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$pass1=$_POST['pass1'];
	$pass2=$_POST['pass2'];
	$pass3=$_POST['pass3'];
	$passy=$_POST['pass3'];
	
	$pass1=md5($_POST['pass1']);
	$pass2=md5($_POST['pass2']);
	$pass3=md5($_POST['pass3']);
		
	$tampil1="SELECT * FROM m_user_tr where id_user ='$id_user' and password='$pass1'";
	$hasil1=mysqli_query($koneksi, $tampil1);
	$result1=mysqli_fetch_row($hasil1);
	if(empty($result1)) {
		$cat="Password lama salah !..";
	}elseif($pass2 <> $pass3) {
		$cat="Password baru tidak sama dengan yang diulang !..";
	}else{
		$perintah = "UPDATE m_user_tr SET password ='$pass2',pass='$passy' where id_user ='$id_user'" ;
		$hasil = mysqli_query($koneksi, $perintah); 
		$cat ="Password berhasil diganti....";
	}			
			
}

//$dis = 'disabled';
?>


<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $aplikasi; ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	
	<link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/ionicons.min.css">
    <link rel="stylesheet" href="css/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="css/dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="css/plugins/iCheck/flat/blue.css">
    <link rel="stylesheet" href="css/plugins/morris/morris.css">
    <link rel="stylesheet" href="css/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <link rel="stylesheet" href="css/plugins/datepicker/datepicker3.css">
    <link rel="stylesheet" href="css/plugins/daterangepicker/daterangepicker-bs3.css">
    <link rel="stylesheet" href="css/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
	<link rel="stylesheet" href="css/plugins/select2/select2.min.css">
	<script src="css/plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>

	<style>
		.datepicker{z-index:1151 !important;}
	</style>
	<script>	
		function checkvalue() {
			var pass1 = document.getElementById('pass1').value; 
			var pass2 = document.getElementById('pass2').value; 
			var pass3 = document.getElementById('pass3').value; 
			if(!pass1.match(/\S/)) {
				alert ('Password lama harus diisi..');				
				return false;	
			}else if(!pass2.match(/\S/)) {
				alert ('Password baru harus diisi..');				
				return false;		
			}else if(!pass3.match(/\S/)) {
				alert ('Ulangi Password harus diisi..');				
				return false;	
			} else {
				return true;
			}	
		}		
    </script>
	
  </head>
  <body class="hold-transition skin-blue sidebar-mini sidebar-collapse" onload="initMap()">
	
	<div class="wrapper">
		<header class="main-header">
			<?php include "header.php"; ?>	 
		</header>
		<aside class="main-sidebar">
			<?php include "menu.php" ; ?>	
		</aside>	
		
		
		<form method="post" name ="myform" action="gantipassword.php?action=simpan" class="form-horizontal" onsubmit="return checkvalue(this)" > 
		<div class="content-wrapper" style="min-height:750px">
			<br>
			<ol class="breadcrumb">
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Ganti Password</b></font></h1></li>					
			</ol>
			<?php if($cat != '') {?>
			<div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
				<i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo $cat; ?></font>
			</div>
			<?php }?>
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
					<br>					
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;width:200px;"><b>PASSWORD LAMA :</span>
						<input type="password" id="pass1" name="pass1" value="" style="text-align: left;width:150px;" >					
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;width:200px;">PASSWORD BARU :</span>
						<input type="password" id="pass2" name="pass2" value="" style="text-align: left;width:150px;" >					
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;width:200px;">ULANGI PASSWORD BARU :</span>
						<input type="password" id="pass3" name="pass3" value="" style="text-align: left;width:150px;" >					
					</div>
					<br>	
				</div>
            </div>
			<div class="col-md-12" >
				<div style="width:98%;background:none;margin-left:0;margin-top:0px;border-top:0px;border-bottom:0px" class="input-group">
					<input type="submit" id="btn-login" value="GANTI PASSWORD" name="B1" class="btn btn-success" <?php echo $dis; ?>  >								
				</div>
			</div>
			
			<div style="width:100%;border:none;background:none" class="input-group">
					<span class="input-group-addon" style="text-align:right;background:none"></span>						
				</div>
				<div style="width:100%;border:none;background:none" class="input-group">
					<span class="input-group-addon" style="text-align:right;background:none"></span>						
				</div>
				<div style="width:100%;border:none;background:none" class="input-group">
					<span class="input-group-addon" style="text-align:right;background:none"></span>						
				</div>
		</div>		
		</form>
	</div>	
	
	
	<script type="text/javascript" src="ajax/user.js"></script> 
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
	<script type="text/javascript" src="js/bootstrap-imgupload.min.js"></script>
    <script type="text/javascript">
        $('.imgupload').imgupload();
    </script>
	
	<script>
      $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();

      });
    </script>
  </body>
</html>
