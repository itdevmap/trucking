<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	//include "lib.php";

	$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'   ");
	$rq=mysqli_fetch_array($pq);	
	$m_edit = $rq['m_edit'];
	$m_add = $rq['m_add'];
	$m_del = $rq['m_del'];
	$m_view = $rq['m_view'];
	$m_exe = $rq['m_exe'];

	if(!isset($_SESSION['id_user'])   ){
	header("location:logout.php"); 
	}

	if($_GET['action']=='cari')
	{	
		$tahun = $_POST[tahun];
		$bulan = $_POST[bulan];
		$hal = $_POST[hal];
	}
	else
	{
		$tahun= date('Y');
		$bulan= date('m');
		$hal = '1';
	}


	//$pq = mysql_query("select sum(tagihan_idr) as t_idr, sum(tagihan_usd) as t_usd, sum(bayar_idr) as b_idr, sum(bayar_usd) as b_usd 
	//                  from t_jo_bill where status = '0'  ");

?>


<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $aplikasi; ?></title>
	<link rel="icon" type="image/png" sizes="16x16" href="img/pav.png">
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
	 
  </head>
  <body class="hold-transition skin-blue sidebar-mini sidebar-collapse" onload="initMap()">
	
	<div class="wrapper">
		<header class="main-header">
			<?php include "header.php"; ?>	 
		</header>
		<aside class="main-sidebar">
			<?php include "menu.php" ; ?>	
		</aside>	
		
		<div class="content-wrapper" style="min-height:300px;background-image: url('img/main.jpg');background-size: cover;">
			
		</div>
		
		
	</div>	
		
	
	
	<script src="ajax/highcharts.js"></script>
	<script src="ajax/exporting.js"></script>
	<script type="text/javascript" src="ajax/dashboard.js"></script> 
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	<script>
      $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();

      });
    </script>
  </body>
</html>
