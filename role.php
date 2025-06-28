<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
include "lib.php"; 


if(!isset($_SESSION['id_user'])  ||  $m_view != '1'  ){
 //header('location:logout.php'); 
}

if($_GET['action']=='cari')
{
	$hal='1';	
	$search_name = $_POST[search_name];
}
else
{
	$search_name= $search_name;
	$hal='1';
}

?>


<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><? echo $aplikasi; ?></title>
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
		
		function TampilData() 
		{			
			$("#nama_cabang").val('');
			$("#pimpinan").val('');
			$("#alamat").val('');
			$("#telp").val('');
			$("#email").val('');
			$("#mode").val('Add');
			$('#Data').modal('show');
		}
		$(function(){
			$("#tanggal").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
		});
		function formatCurrency(num) {
			num = num.toString().replace(/\$|\,/g,'');
			if(isNaN(num))
				num = "0";
				sign = (num == (num = Math.abs(num)));
				num = Math.floor(num*100+0.50000000001);
				cents = num%100;
				num = Math.floor(num/100).toString();
			if(cents<10)
				cents = "0" + cents;
				for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
					num = num.substring(0,num.length-(4*i+3))+','+
					num.substring(num.length-(4*i+3));
			return (((sign)?'':'-') + '' + num + '.' + cents);
		}
		function isNumber(evt) {
			evt = (evt) ? evt : window.event;
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode > 31 && (charCode < 46 || charCode > 57)) {
				return false;
			}
			return true;
		}
    </script>
	
  </head>
  <body class="hold-transition skin-blue sidebar-mini sidebar-collapse" onload="initMap()">
	
	<div class="wrapper">
		<header class="main-header">
			<? include "header.php"; ?>	 
		</header>
		<aside class="main-sidebar">
			<? include "menu.php" ; ?>	
		</aside>	
		
		
		<form method="post" name ="myform" action="role.php?action=cari" class="form-horizontal" > 
		<div class="content-wrapper">   
			<section class="content">
				<br>
				<ol class="breadcrumb">
					<li><h1><i class="fa fa-gear"></i><b>&nbsp;&nbsp;<font size="4"><b>Data Role</b></font></h1></li>					
				</ol>
				<br>
				<div class="col-md-6" style="width:98%;border:1px solid #ddd;padding:5px">
				
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><? echo $id ?>Cari Nama Role:</span>						
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" style="text-align: left;width:20%"  >
						<input type="hidden"  id ="hal" name="hal" value="<? echo $hal; ?>" style="text-align: left;width:5%"  >
						<button class="btn btn-success" style="margin-top:-3px" type="submit" >
							<span class=" glyphicon glyphicon-search"></span>
							Cari
						</button>					
					</div>
				</div>
				<div class="col-md-6" style="min-height:60px;width:98%;border:1px solid #ddd;padding:5px">		
					<? if ($m_add == '1'){
						$xy1="Add|$row[id_role]|$page";
						$xy1=base64_encode($xy1);
						$link = "'role_data.php?id=$xy1'";?>
						<button class="btn btn-block btn-default" 
							style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:0px" type="button" 
							onClick="window.location.href = <? echo $link;?> " >
							<span class=" glyphicon glyphicon-plus"></span>
							Tambah Role
						</button>			
					<?}?>	
					<div class="table-responsive mailbox-messages" style="min-height:60px">									
						<div class="tampil_data"></div>
					</div>
				</div>
			</section>	
		</div>    
		</form>	
	</div>	
	
	
	
	
	<div class="modal fade" id="Data"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
							
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:20px;border:1px solid #ddd;border-radius:5px;padding:5px">					
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;background:none;min-width:150px">#NAMA CABANG :</span>
							<input type="text" id="nama_cabang"  value="" style="text-transform: uppercase;;text-align: left;width:360px;border:1px solid rgb(169, 169, 169)" maxlength ="20" />	
							<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
							<input type="hidden" id="mode"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;background:none;min-width:150px">PEMIMPIN CABANG :</span>
							<input type="text" id="pimpinan"  value="" style="text-transform: uppercase; 
							text-align: left;width:360px;border:1px solid rgb(169, 169, 169)"   />	
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;">ALAMAT :</span>
							<textarea name="alamat" id="alamat"
							style="resize:none;width: 360px; height: 74px; font-size: 11px; line-height: 12px; 
							border: 1px solid #4; padding: 5px;" ><? echo $alamat; ?></textarea>
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;background:none;min-width:150px">TELP :</span>
							<input type="text" id="telp"  value="" style="text-align: left;width:360px;border:1px solid rgb(169, 169, 169)"   />	
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;background:none;min-width:150px">EMAIL :</span>
							<input type="text" id="email"  value="" style="text-align: left;width:360px;border:1px solid rgb(169, 169, 169)"   />	
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;background:none;min-width:150px">STATUS :</span>
							<select id="stat"  style="width: 30%;">
								<option value="1" >Aktif</option>
								<option value="0" >Tidak Aktif</option>
							</select>						
						</div>
					</div>
					<button type="button" class="btn btn-success" onclick="add()">&nbsp;&nbsp;SIMPAN&nbsp;&nbsp;</button>
								
				</div>
				
			</div>
		</div>	
    </div>

	
	
	
	<script type="text/javascript" src="ajax/role.js"></script> 
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
