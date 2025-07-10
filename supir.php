<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
include "lib.php";

$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='11' ");
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
	$hal = $_POST['hal'];
	$status = $_POST['status'];
	$search_name = $_POST['search_name'];
	$paging = $_POST['paging'];
}
else
{	
	$paging='25';
	$hal='1';
	$status = 'All';
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
		function isNumber(evt) {
			evt = (evt) ? evt : window.event;
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode > 31 && (charCode < 46 || charCode > 57)) {
				return false;
			}
			return true;
		}
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
			return (((sign)?'':'-') + '' + num );
			//return (((sign)?'':'-') + '' + num + '.' + cents);
		}
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
		$(document).ready(function () {
			$("#tanggal_lahir").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			$("#masa_berlaku").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			$("#tanggal_masuk").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			$("#tanggal_keluar1").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			var hal = $("#hal").val();
			ReadData(hal);
		});
		function ReadData(hal) {
			var search_name = $("#search_name").val();
			var paging = $("#paging").val();			
			$.get("ajax/supir_crud.php", {paging:paging,search_name:search_name,hal:hal, type:"Read" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}
		function Tampil(){	
			var hal = $("#hal").val();
			ReadData(hal);
		}
		function TampilData() 
		{
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
			$("#tanggal_masuk").val(today);
			$("#nama").val('');
			$("#tempat_lahir").val('');
			$("#alamat").val('');
			$("#phone").val('');
			$("#tanggal_lahir").val('');
			$("#no_ktp").val('');
			$("#mode").val('Add');
			$('#Data').modal('show');
		}
		function add() {	
			if(!$("#nama").val()){
				alert("Nama Supir harus diisi !..");
			}
			else if(!$("#no_ktp").val()){
				alert("No. KTP harus diisi !..");
			}
			else
			{
				var id = $("#id").val();
				var nama = $("#nama").val();
				var no_ktp = $("#no_ktp").val();
				var tempat_lahir = $("#tempat_lahir").val();
				var tanggal_lahir = $("#tanggal_lahir").val();
				var tanggal_masuk = $("#tanggal_masuk").val();
				var kelamin = $("#kelamin").val();
				var agama = $("#agama").val();
				var perkawinan = $("#perkawinan").val();
				var sponsor = $("#sponsor").val();
				var alamat = $("#alamat").val();
				var telp = $("#telp").val();
				var no_sim = $("#no_sim").val();
				var jenis_sim = $("#jenis_sim").val();
				var masa_berlaku = $("#masa_berlaku").val();
				var mode = $("#mode").val();
				var hal = $("#hal").val();
				var stat = $("#stat").val();
				$.post("ajax/supir_crud.php", {
					id:id,
					nama:nama,
					no_ktp:no_ktp,
					tempat_lahir:tempat_lahir,
					tanggal_masuk:tanggal_masuk,
					tanggal_lahir:tanggal_lahir,
					sponsor:sponsor,
					kelamin:kelamin,	
					agama:agama,
					perkawinan:perkawinan,
					alamat:alamat,
					telp:telp,
					no_sim:no_sim,
					stat:stat,
					jenis_sim:jenis_sim,
					masa_berlaku:masa_berlaku,	
					mode:mode,
					type : "add"
					}, function (data, status) {
					alert(data);
					$("#Data").modal("hide");				
					ReadData(hal);
				});
			}	
		}
		function GetData(id) {
			$("#id").val(id);	
			$.post("ajax/supir_crud.php", {
					id: id, type:"detil"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#nama").val(data.nama_supir);
					$("#no_ktp").val(data.no_ktp);
					$("#tanggal_lahir").val(changeDateFormat(data.tanggal_lahir));
					$("#tempat_lahir").val(data.tempat_lahir);
					$("#kelamin").val(data.kelamin);
					$("#agama").val(data.agama);
					$("#perkawinan").val(data.perkawinan);
					$("#alamat").val(data.alamat);
					$("#telp").val(data.telp);
					$("#no_sim").val(data.no_sim);
					$("#jenis_sim").val(data.jenis_sim);
					$("#masa_berlaku").val(changeDateFormat(data.masa_berlaku));
					$("#stat").val(data.status);
					$("#mode").val('Edit');			
				}
			);
			$("#Data").modal("show");
		}
		
		function GetStatus(id) {
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
			$("#tanggal_keluar1").val(today);
			$("#id1").val(id);	
			$.post("ajax/supir_crud.php", {
					id: id, type:"detil"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#nama1").val(data.nama_supir);					
					$("#alasan1").val(data.alasan);
					$("#mode1").val('Edit');			
				}
			);
			document.getElementById("nama1").disabled = true;
			$("#DataStatus").modal("show");
		}
		function Update_Status() {	
			if(!$("#alasan1").val()){
				alert("Alasan Keluar harus diisi !..");
			}
			else
			{
				var id = $("#id1").val();
				var tanggal_keluar = $("#tanggal_keluar1").val();
				var alasan = $("#alasan1").val();
				$.post("ajax/supir_crud.php", {
					id:id,
					tanggal_keluar:tanggal_keluar,
					alasan:alasan,
					type : "Update_Status"
					}, function (data, status) {
					alert(data);
					$("#DataStatus").modal("hide");				
					ReadData(1);
				});
			}	
		}
		function GetDoc(id) {
		    $("#id3").val(id);
			$.post("ajax/supir_crud.php", {
					id: id, type:"detil"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#nama3").val(data.nama_supir);
				}
			);
			$("#DataUpload").modal("show");
		}
		
		function GetImg(id) {
		    $("#id2").val(id);
			$.post("ajax/supir_crud.php", {
					id: id, type:"detil"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#photo_lama").val(data.kop);
				}
			);
			$("#DataPhoto").modal("show");
		}
		$(document).ready(function (e) {
			$("#formx").on('submit',(function(e) {
				e.preventDefault();
				$.ajax({
					url: "upload_photo_supir.php",
					type: "POST",
					data:  new FormData(this),
					contentType: false,
					cache: false,
					processData:false,
					beforeSend : function()
					{				
						$("#err").fadeOut();
					},
					success: function(data)
					{				
						if(data=='invalid')
						{
							$("#err").html("Image tidak boleh kosong !").fadeIn();
						}
						else
						{					
							$("#formx")[0].reset();	
							$("#DataPhoto").modal("hide");
							ReadData(1);
						}
					},
					error: function(e) 
					{
						$("#err").html(e).fadeIn();
					} 	        
			   });
			}));
			
			$("#form_doc").on('submit',(function(e) {
				e.preventDefault();
				$.ajax({
					url: "upload_doc_supir.php",
					type: "POST",
					data:  new FormData(this),
					contentType: false,
					cache: false,
					processData:false,
					beforeSend : function()
					{				
						$("#err").fadeOut();
					},
					success: function(data)
					{			
						alert("Data Saved..");	
						$("#form_doc")[0].reset();	
						$("#DataUpload").modal("hide");
						ReadData(1);
					},
					error: function(e) 
					{
						$("#err").html(e).fadeIn();
					} 	        
			   });
			}));
		});
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
		
		
		<form method="post" name ="myform" action="supir.php?action=cari" class="form-horizontal" > 
		<div class="content-wrapper" style="min-height:750px">
			<br>
			<ol class="breadcrumb">
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Driver Data</b></font></h1></li>					
			</ol>
			<br>
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
					</div>
					<br>		
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Find Driver :</b></span>
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
						style="text-align: left;margin-left:-5px;width:200px" onkeypress="ReadData(1)" >
						<input type="hidden"  id ="hal" name="hal" value="<?php echo $hal; ?>" style="text-align: left;width:5%"  >						
						<button class="btn btn-block btn-primary" 
							style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:2px;padding:6px" type="submit" 
							onClick="window.location.href = 'paket_data.php?id=<?php echo $xy1; ?>' ">
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</div>
					<br>	
				</div>
            </div>
			
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;background:#fff !important;">	
					<div style="width:100%;background: #fff;" class="input-group" >
						<span class="input-group-addon" style="width:50%;text-align:left;padding:0px;background:#fff;">
							<?php if ($m_add == '1'){?>
							<button class="btn btn-block btn-success" 
								style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:3px" type="button" 
								onClick="javascript:TampilData()">
								<span class="fa  fa-plus-square"></span>
								<b>Add New</b>
							</button>	
							<?php }?>
						</span>
						<span class="input-group-addon" style="width:50%;text-align:right;padding:0px;background:#fff">
						Row Page :&nbsp;
						<select size="1" id="paging"  name="paging" onchange="Tampil()" style="padding:4px;margin-right:2px">
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Driver Data</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Name :</b></span>
								<input type="text"  id ="nama" name="nama" value=""
								style="text-transform: uppercase;text-align: left;width:80%"  >	
								<input type="hidden" id="id"   value=""  />	
								<input type="hidden" id="mode"   value=""  />	
								<input type="hidden"  id ="tanggal_masuk"  
								style="text-align: center;width:20%"   >
							</div>	
						
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>No. KTP :</b></span>
								<input type="text"  id ="no_ktp" name="no_ktp" value="" 
								style="text-transform: uppercase;text-align: left;width:80%" >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Date place of birth :</b></span>
								<input type="text"  id ="tempat_lahir" name="tempat_lahir" value="" 
								style="text-transform: uppercase;text-align: left;width:57.3%" >&nbsp;,
								<input type="text"  id ="tanggal_lahir" name="tanggal_lahir" value="" 
								style="text-align: center;width:20%"   >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Gender :</b></span>
								<select id="kelamin" name="kelamin" style="width: 80%;height:26px">
									<option >Male</option>
									<option >Female</option>
								</select>						
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Religion :</b></span>
								<select id="agama" name="agama" style="width: 80%;height:26px" >
									<?php
									$tampil1="select * from m_agama order by id  ";
									$hasil1=mysqli_query($koneksi, $tampil1);       
									while ($data1=mysqli_fetch_array($hasil1)){?>
									<option ><?php echo $data1['nama'];?></option>
									<?php }?>
								</select>						
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Marital Status :</b></span>
								<select id="perkawinan" name="perkawinan" style="width: 80%;height:26px" >
									<option >Marry</option>
									<option >Not married</option>
									<option >Widower (Duda)</option>
									<option >Widow (Janda)</option>
								</select>						
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Address :</b></span>
								<textarea name="alamat" id="alamat"
								style="resize:none;width: 80%; height: 58px; font-size: 11px; line-height: 12px; 
								border: 1px solid #444; padding: 5px;"  ></textarea>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>No. Telp :</b></span>
								<input type="text"  id ="telp" name="telp" value="" 
								style="text-transform: uppercase;text-align: left;width:80%" >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>No. SIM :</b></span>
								<input type="text"  id ="no_sim" name="no_sim" value="" 
								style="text-transform: uppercase;text-align: left;width:80%"  >
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Type of SIM :</b></span>
								<select id="jenis_sim" name="jenis_sim" style="width: 80%;height:26px">
									<option >A</option>
									<option >BI</option>
									<option >BII</option>
									<option >C</option>
									<option >D</option>
									<option >A General</option>
									<option >BI General</option>
									<option >BII General</option>
								</select>						
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Validity period of SIM :</b></span>
								<input type="text"  id ="masa_berlaku" name="masa_berlaku" value="" 
								style="text-align: center;width:20%"   >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Status :</b></span>
								<select id="stat"  style="width: 80%;padding:4px">
									<option value="1" >Active</option>
									<option value="0" >In Active</option>
								</select>						
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-primary" onclick="add()">&nbsp;&nbsp;Save&nbsp;&nbsp;</button>		
								<button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>								
							</div>
							<br>
						</div>
					</div>			
				</div>
			
			</div>
		</div>	
    </div>
	
	
	<div class="modal fade" id="DataPhoto"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">	
					<form id="formx" action="" method="post" enctype="multipart/form-data">
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">	
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#403b3b96;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Photo</b>
							</div>	
							<br>	
							<input type="hidden" id="id2"   name="idy" value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
							<input type="hidden" id="photo_lama" name = "photo_lama"   style="text-align: left;width:30%;"    >	
							
							<div class="imgupload panel panel-default">															
								<div class="file-tab panel-body">
									<div>
										<button type="button" class="btn btn-default btn-file">
											<span>Select Photo</span>
											<input type="file" name="image" id ="uploadImage" >											
										</button>	
										<button type="button" class="btn btn-default">Remove</button>		
									</div>
									<div id="err"></div>
								</div>	
							</div>							
							<div style="width:100%" class="input-group">
								<span class="input-group-addon" style="text-align:left;background:none;min-width:150px">
									<button type="submit" class="btn btn-success" >&nbsp;&nbsp;Save&nbsp;&nbsp;</button>	
									<button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>	
								</span>												
							</div>	
							<br>
						</div>
					</div>
					</form>
				</div>			
			</div>
		</div>	
    </div>
	
	<div class="modal fade" id="DataUpload"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">
					<form id="form_doc" action="" method="post" enctype="multipart/form-data">
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Upload</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Name :</b></span>
								<input type="text"  id ="nama3" name="nama" value="<?echo $nama; ?>"
								style="text-transform: uppercase;text-align: left;width:80%" disabled >	
								<input type="hidden" id="id3"  name="id3" value=""   />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>KTP :</b></span>
								<input type='file' name='ktp' style="height:26px;padding:4px;width:80%;font-family:tahoma;font-size:11px" id='file' class='form-control' ><br>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Family card (KK):</b></span>
								<input type='file' name='kk' style="height:26px;padding:4px;width:80%;font-family:tahoma;font-size:11px" id='file' class='form-control' ><br>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SIM :</b></span>
								<input type='file' name='sim' style="height:26px;padding:4px;width:80%;font-family:tahoma;font-size:11px" id='file' class='form-control' ><br>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="submit" class="btn btn-primary" >&nbsp;&nbsp;Save&nbsp;&nbsp;</button>
								<button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>								
							</div>
							<br>
						</div>
					</div>		
					</form>	
				</div>
			</div>
		</div>	
    </div>
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	<script type="text/javascript" src="js/bootstrap-imgupload.min.js"></script>
	<script type="text/javascript">
        $('.imgupload').imgupload();
    </script>
  </body>
</html>
