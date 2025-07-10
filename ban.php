<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
//include "lib.php";

$pq = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='23' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

if(!isset($_SESSION['id_user'])  ||  $m_view != '1'  ){
 header('location:logout.php'); 
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$hal='1';	
	$field = $_POST['field'];
	$search_name = $_POST['search_name'];
	$tgl1 = $_POST['tgl1'];
	$tgl2 = $_POST['tgl2'];
	$paging = $_POST['paging'];
	$stat = $_POST['stat'];
}
else
{	
	$tahun= date("Y") ;
	$tgl1= date("01-01-$tahunx");
	$tgl2= date("31-12-$tahun");
	$paging='15';
	$hal='1';
	$field = 'No Seri';
}

?>


<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $aplikasi; ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="icon" type="image/png" sizes="16x16" href="img/pav.png">
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
		$(document).ready(function () {
			var date_input=$('input[name="tgl1"]'); 
			var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
			date_input.datepicker({
				format: 'dd-mm-yyyy',
				container: container,
				todayHighlight: true,
				autoclose: true,
			})
			var date_input=$('input[name="tgl2"]'); 
			var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
			date_input.datepicker({
				format: 'dd-mm-yyyy',
				container: container,
				todayHighlight: true,
				autoclose: true,
			})	
			$("#tanggal").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});		
			$("#tanggal_rotasi").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});			
			var hal = $("#hal").val();
			ReadData(hal);
		});	
		function changeDateFormat(inputDate){
			var splitDate = inputDate.split('-');
			if(splitDate.count == 0){
				return null;
			}
			var year = splitDate[0];
			var month = splitDate[2];
			var day = splitDate[1]; 
			return month + '-' + day + '-' + year;
		}		
		function Desimal(num) {
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
			//return (((sign)?'':'-') + '' + num);
			
		}
		function Rupiah(num) {
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
			//return (((sign)?'':'-') + '' + num + '.' + cents);
			return (((sign)?'':'-') + '' + num);
			
		}
		function isNumber(evt) {
			evt = (evt) ? evt : window.event;
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode > 31 && (charCode < 46 || charCode > 57)) {
				return false;
			}
			return true;
		}
		function ReadData(hal) 
		{
			var tgl1 = $("#tgl1").val();
			var tgl2 = $("#tgl2").val();	
			var cari = $("#search_name").val();
			var paging = $("#paging").val();	
			var field = $("#field").val();
			$.get("ajax/ban_crud.php", {
				tgl1:tgl1, 
				tgl2:tgl2, 
				field:field,
				paging:paging,
				cari:cari,
				hal:hal,
				type:"Read" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}	
		function Delete(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/ban_crud.php", {
						id: id, type:"Del_Ban"
					},
					function (data, status) {
						 ReadData();
					}
				);
			}
		}
		function TampilData() 
		{
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth()+1; 
			var yyyy = today.getFullYear();
			var jam = today.getHours();
			var menit = today.getMinutes();
			if(dd<10){
				dd='0'+dd
			} 
			if(mm<10){
				mm='0'+mm
			} 	
			var today = dd+'-'+mm+'-'+yyyy;
			$("#tanggal").val(today);
			$("#no_seri").val('');
			$("#merk_ban").val('');
			$("#ketebalan_ban").val('');
			$("#km_ban").val('');
			$("#posisi_ban").val('');
			$("#mode").val('Add');
			$('#Data').modal('show');
		}
		function AddBan() {	
			var ketebalan_ban = $("#ketebalan_ban").val();
			
			if(!$("#tanggal").val()){
				alert("Tanggal harus diisi !..");
			}
			else if(!$("#no_seri").val()){
				alert("No. Seri Ban harus diisi !..");
			}
			else if(!$("#jenis_ban").val()){
				alert("Jenis Ban harus diisi !..");
			}
			else if(!$("#merk_ban").val()){
				alert("Merk Ban harus diisi !..");
			}
			else if(ketebalan_ban <= 0){
				alert("Ketebalan Ban harus diisi !..");
			}
			else if(!$("#posisi_ban").val()){
				alert("Posisi Ban harus diisi !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id = $("#id").val();
					var tanggal = $("#tanggal").val();
					var id_mobil = $("#id_mobil").val();
					var no_seri = $("#no_seri").val();
					var jenis_ban = $("#jenis_ban").val();
					var merk_ban = $("#merk_ban").val();
					var ketebalan_ban = $("#ketebalan_ban").val();
					var km_ban = $("#km_ban").val();
					var posisi_ban = $("#posisi_ban").val();
					var mode = $("#mode").val();
					var hal = $("#hal").val();
					$.post("ajax/ban_crud.php", {
						id:id,
						tanggal:tanggal,
						id_mobil:id_mobil,
						km_ban:km_ban,
						no_seri:no_seri,
						jenis_ban:jenis_ban,
						merk_ban:merk_ban,
						ketebalan_ban:ketebalan_ban,
						posisi_ban:posisi_ban,
						mode:mode,
						type : "Add_Ban"
						}, function (data, status) {
						alert(data);
						$("#Data").modal("hide");				
						ReadData(hal);
					});
				}
			}	
		}
		function GetBan(id) {
			$("#id").val(id);	
			$.post("ajax/ban_crud.php", {
					id: id, type:"Detil_Ban"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#tanggal").val(changeDateFormat(data.tanggal));
					$("#id_mobil").val(data.id_mobil);
					$("#no_seri").val(data.no_seri);
					$("#merk_ban").val(data.merk_ban);
					$("#km_ban").val(Rupiah(data.km));
					$("#ketebalan_ban").val(Rupiah(data.ketebalan));
					$("#posisi_ban").val(data.posisi);
					$("#mode").val('Edit');
				}
			);
			$("#Data").modal("show");
		}
		
		function GetRotasi(id) {
			$("#id_ban").val(id);	
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth()+1; 
			var yyyy = today.getFullYear();
			if(dd<10){
				dd='0'+dd
			} 
			if(mm<10){
				mm='0'+mm
			} 	
			var today = dd+'-'+mm+'-'+yyyy;
			$("#tanggal_rotasi").val(today);
			$.post("ajax/ban_crud.php", {
					id: id, type:"Detil_Ban"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					$("#no_seri_rotasi").val(data.no_seri);
					$("#id_mobil_rotasi").val(data.id_mobil);
				}
			);
			$("#DataRotasi").modal("show");
		}
		
		function AddRotasi() {	
			var ketebalan_rotasi = $("#ketebalan_rotasi").val();
			if(!$("#tanggal_rotasi").val()){
				alert("Tanggal harus diisi !..");
			}
			else if(!$("#jenis_pekerjaan").val()){
				alert("Jenis Pekerjaan harus diisi !..");
			}
			else if(!$("#posisi_rotasi").val()){
				alert("Posisi harus diisi !..");
			}
			else if(ketebalan_rotasi <= 0){
				alert("Ketebalan Ban harus diisi !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id_ban = $("#id_ban").val();
					var tanggal = $("#tanggal_rotasi").val();
					var id_mobil = $("#id_mobil_rotasi").val();
					var jenis_pekerjaan = $("#jenis_pekerjaan").val();
					var posisi = $("#posisi_rotasi").val();
					var ketebalan = $("#ketebalan_rotasi").val();
					var km = $("#km_rotasi").val();
					var ket = $("#ket").val();
					var hal = $("#hal").val();
					$.post("ajax/ban_crud.php", {
						id_ban:id_ban,
						tanggal:tanggal,
						id_mobil:id_mobil,
						jenis_pekerjaan:jenis_pekerjaan,
						posisi:posisi,
						ketebalan:ketebalan,
						km:km,
						ket:ket,
						type : "Add_Rotasi"
						}, function (data, status) {
						alert(data);
						$("#DataRotasi").modal("hide");				
						ReadData(hal);
					});
				}
			}	
		}
		
		function ListRotasi(id) {	
			$("#id_detil").val(id);
			$.get("ajax/ban_crud.php", {id:id,  type:"ListRotasi" }, function (data, status) {
				$(".tampil_rotasi").html(data);
				});
			$("#ListRotasi").modal("show");
		}
		
		
		function DelRotasi(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/ban_crud.php", {
						id: id, type:"Del_Rotasi"
					},
					function (data, status) {
						 var id = $("#id_detil").val();
						 $.get("ajax/ban_crud.php", {id:id,  type:"ListRotasi" }, function (data, status) {
							$(".tampil_rotasi").html(data);
						});
					}
				);
			}
		}
		function Download() 
		{
			var tgl1 = $("#tgl1").val();
			var tgl2 = $("#tgl2").val();	
			var cari = $("#search_name").val();
			var field = $("#field").val();
			var id = tgl1+'|'+tgl2+'|'+field+'|'+cari;
			var idx = btoa(id);
			var win = window.open('ban_excel.php?id='+idx);
		}	
		function DownloadDetil() 
		{
			var tgl1 = $("#tgl1").val();
			var tgl2 = $("#tgl2").val();	
			var cari = $("#search_name").val();
			var field = $("#field").val();
			var id = tgl1+'|'+tgl2+'|'+field+'|'+cari;
			var idx = btoa(id);
			var win = window.open('ban_detil_excel.php?id='+idx);
		}	
		function Download_Rotasi() 
		{
			var id = $("#id_detil").val();
			var idx = btoa(id);
			var win = window.open('ban_rotasi_excel.php?id='+idx);
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
		
		<form method="post" name ="myform"  class="form-horizontal" > 
		<div class="content-wrapper" style="min-height:750px">
			<br>
			<ol class="breadcrumb">
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Tire Monitoring Data</b></font></h1></li>					
			</ol>
			<br>
			
			
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Date :</b></span>
						<input type="text"  id ="tgl1" name="tgl1" value="<?php echo $tgl1; ?>" 
						style="text-align: center;width:85px" onchange="ReadData(1)" readonly >
						&nbsp;&nbsp;<b>s.d</b>&nbsp;&nbsp;
						<input type="text"  id ="tgl2" name="tgl2" value="<?php echo $tgl2; ?>" 
						style="text-align: center;width:85px" onchange="ReadData(1)" readonly >	
					</div>	
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Filter By</b></span>
						<select size="1" id="field"  onchange="ReadData(1)" name="field" style="padding:4px;margin-right:2px;width: 85px">
							<option>No Seri</option>
							<option>Type</option>
							<option>Brand</option>
							<option>No Police</option>
							<option value="<?php echo $field; ?>" selected><?php echo $field; ?></option>
						</select>
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
						<input type="hidden"  id ="hal" name="hal" value="<?php echo $hal; ?>" style="text-align: left;width:5%"  >
						<button class="btn btn-block btn-primary" style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:2px;padding-top:6px;padding-bottom:6px" 
							type="submit">
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</div>
					<br>	
				</div>
            </div>
			
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;background:#fff !important;">	
					<div style="width:100%;background: #fff;" class="input-group" >
						<span class="input-group-addon" style="width:50%;text-align:left;padding:0px">
							<?php if ($m_add == '1'){
								$xy1="Add|";
								$xy1=base64_encode($xy1);?>
								<button class="btn btn-block btn-success" 
								style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:2px" type="button"  title = "Created Order"
								onClick="javascript:TampilData()">
								<span class="fa  fa-plus-square"></span>
								<b>Add New</b>
								</button>	
								
							<?php }?>		
							<button class="btn btn-block btn-warning" 
								style="margin:0px;margin-left:-1px;margin-bottom:0px;border-radius:2px" type="button"  title = ""
								onClick="javascript:Download()">
								<span class="fa fa-file-text"></span>
								<b>Download Recap</b>
							</button>	
							<button class="btn btn-block btn-warning" 
								style="margin:0px;margin-left:-1px;margin-bottom:0px;border-radius:2px" type="button"  title = ""
								onClick="javascript:DownloadDetil()">
								<span class="fa fa-file-text"></span>
								<b>Download Detail</b>
							</button>	
						</span>
						<span class="input-group-addon" style="width:50%;text-align:right;padding:0px;background:#fff">
						Row Page :&nbsp;
						<select size="1" id="paging"  name="paging" onchange="ReadData(1)" style="padding:4px;margin-right:2px">
							<?php 
							$tampil1="select * from m_paging  order by baris";
							$hasil1=mysqli_query($koneksi, $tampil1);       
							while ($data1=mysqli_fetch_array($hasil1)){  
							?>
							<option><?php echo $data1['baris'];?></option>
							<?php }?>
							<option value="<?php echo $paging; ?>" selected><?php echo $paging; ?></option>
						</select>	
						</span>	
					</div>		
				</div>
            </div>			
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;background:#fff !important;">	
					<div class="table-responsive mailbox-messages" style="min-height:10px">									
						<div class="tampil_data"></div>
					</div>
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
	
	<div class="modal fade" id="Data"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">	
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;New Tire Data</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Date :</b></span>
								<input type="text" id="tanggal"  value="" style="padding:4px;text-align: center;width:98px;border:1px solid rgb(169, 169, 169);background:#eee"  />
							</div>							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Tire Series:</b></span>
								<input type="text" id="no_seri"  value="" style="text-transform: uppercase;
								text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tire Type :</b></span>
								<select id="jenis_ban"  style="width: 80%;padding:4px">
									<option value="ORI" >ORI</option>
									<option value="VULL" >VULL</option>
								</select>	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tire Brand :</b></span>
								<input type="text" id="merk_ban"  value="" style="text-transform: uppercase;
								text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tire Thickness :</b></span>
								<input type="text" id="ketebalan_ban"  value="" style="text-align: center;width:15%;border:1px solid rgb(169, 169, 169)" onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)" />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Kilometer :</b></span>
								<input type="text" id="km_ban"  value="" style="text-align: center;width:15%;border:1px solid rgb(169, 169, 169)" onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" />	
							</div>							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Police :</b></span>
								<select id="id_mobil"  style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_mobil_tr where status = '1' order by no_polisi  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_mobil'];?>" ><?php echo $d1['no_polisi'];?></option>
									<?php }?>
								</select>	
								<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="mode"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
							</div>		
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tire Position :</b></span>
								<input type="text" id="posisi_ban"  value="" style="text-transform: uppercase;
								text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />	
							</div>
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddBan()">
								<span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save</b>&nbsp;&nbsp;</button>	
								<button type="button" class="btn btn-danger" data-dismiss="modal">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Cancel</b></button>	
							</div>
							<br>
						</div>
					</div>			
				</div>
			</div>
		</div>	
    </div>
	
	
	<div class="modal fade" id="ListRotasi"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document" style="width:65%;">
			<div class="modal-content" style="background: none;">
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Rotation Data</b>
							</div>	
							<div  class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:50%;text-align:left;padding:0px;background: none;">									
									<input type="hidden"  id ="id_detil"    >
								</span>								
							</div>	
							<button class="btn btn-block btn-warning" 
								style="margin:0px;margin-left:-1px;margin-bottom:0px;border-radius:2px" type="button"  title = ""
								onClick="javascript:Download_Rotasi()">
								<span class="fa fa-file-text"></span>
								<b>Download</b>
							</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-left:1px; margin-bottom:2px">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Close</b></button>	
							<div class="table-responsive mailbox-messages" >									
								<div class="tampil_rotasi"></div>
							</div>
							
						</div>
					</div>		
				</div>	
			</div>
		</div>	
    </div>
	
	<div class="modal fade" id="DataRotasi"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">	
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Tire Rotation Data</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Date :</b></span>
								<input type="text" id="tanggal_rotasi"  value="" style="padding:4px;text-align: center;width:85px;border:1px solid rgb(169, 169, 169);background:#eee"  />
								<input type="hidden" id="id_ban"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Tire Series :</b></span>
								<input type="text" id="no_seri_rotasi"  value="" style="text-transform: uppercase;
								text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" readonly />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Police :</b></span>
								<select id="id_mobil_rotasi"  style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_mobil_tr where status = '1' order by no_polisi  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_mobil'];?>" ><?php echo $d1['no_polisi'];?></option>
									<?php }?>
								</select>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Type of work :</b></span>
								<input type="text" id="jenis_pekerjaan"  value="" style="text-transform: uppercase;
								text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />	
							</div>	
											
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Position :</b></span>
								<input type="text" id="posisi_rotasi"  value="" style="text-transform: uppercase;
								text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tire Thickness :</b></span>
								<input type="text" id="ketebalan_rotasi"  value="" style="text-align: center;width:15%;border:1px solid rgb(169, 169, 169)" onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)" />	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Kilometer :</b></span>
								<input type="text" id="km_rotasi"  value="" style="text-align: center;width:15%;border:1px solid rgb(169, 169, 169)" onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Information :</b></span>
								<input type="text" id="ket"  value="" style="text-transform: uppercase;
								text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddRotasi()">
								<span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save</b>&nbsp;&nbsp;</button>	
								<button type="button" class="btn btn-danger" data-dismiss="modal">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Cancel</b></button>	
							</div>
							<br>
						</div>
					</div>			
				</div>
			</div>
		</div>	
    </div>
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
