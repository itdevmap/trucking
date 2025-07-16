<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	//include "lib.php";

	$pq = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='24' ");
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
		$field1 = $_POST['field1'];
		$search_name1 = $_POST['search_name1'];
	}
	else
	{	
		$tahun= date("Y") ;
		$tgl1= date("01-01-$tahunx");
		$tgl2= date("31-12-$tahun");
		$paging='10';	
		$hal='1';
		$field = 'No Polisi';
		$field1 = 'Jenis Pekerjaan';
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
			var field1 = $("#field1").val();
			var cari1 = $("#search_name1").val();
			$.get("ajax/perbaikan_crud.php", {
				tgl1:tgl1, 
				tgl2:tgl2, 
				field:field,
				paging:paging,
				cari:cari,
				field1:field1,
				cari1:cari1,
				hal:hal,
				type:"Read" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}	
		function Delete(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/perbaikan_crud.php", {
						id: id, type:"Del_SPK"
					},
					function (data, status) {
						 ReadData(1);
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
			$("#jam_mulai").val(jam);
			$("#menit_mulai").val(menit);
			$("#jam_selesai").val(jam);
			$("#menit_selesai").val(menit);
			$("#mode").val('Add');
			$('#Data').modal('show');
		}
		function AddData() {	
			if(!$("#no_spk").val()){
				alert("No. SPK harus diisi !..");
			}
			else if(!$("#tanggal").val()){
				alert("Tanggal harus diisi !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id = $("#id").val();
					var no_spk = $("#no_spk").val();
					var tanggal = $("#tanggal").val();
					var jam_mulai = $("#jam_mulai").val();
					var menit_mulai = $("#menit_mulai").val();
					var jam_selesai = $("#jam_selesai").val();
					var menit_selesai = $("#menit_selesai").val();
					var id_mobil = $("#id_mobil").val();
					var tempelan = $("#tempelan").val();
					var km = $("#km").val();
					var jenis_spk = $("#jenis_spk").val();
					var part = $("#part").val();
					var ket = $("#ket").val();
					var mode = $("#mode").val();
					var hal = $("#hal").val();
					$.post("ajax/perbaikan_crud.php", {
						id:id,
						no_spk:no_spk,
						tanggal:tanggal,
						jam_mulai:jam_mulai,
						menit_mulai:menit_mulai,
						jam_selesai:jam_selesai,
						menit_selesai:menit_selesai,
						id_mobil:id_mobil,
						tempelan:tempelan,
						km:km,
						jenis_spk:jenis_spk,
						part:part,
						ket:ket,
						mode:mode,
						type : "Add_Data"
						}, function (data, status) {
						alert(data);
						$("#Data").modal("hide");				
						ReadData(1);
					});
				}
			}	
		}
		function GetData(id) {
			$("#id").val(id);	
			$.post("ajax/perbaikan_crud.php", {
					id: id, type:"Detil_Data"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#tanggal").val(changeDateFormat(data.tanggal));
					$("#no_spk").val(data.no_spk);
					$("#jam_mulai").val(data.jam_mulai);
					$("#menit_mulai").val(data.menit_mulai);
					$("#jam_selesai").val(data.jam_selesai);
					$("#menit_selesai").val(data.menit_selesai);
					$("#id_mobil").val(data.id_mobil);
					$("#tempelan").val(data.tempelan);
					$("#km").val(Desimal(data.km));
					$("#jenis_spk").val(data.jenis_spk);
					$("#part").val(data.part);
					$("#ket").val(data.ket);
					$("#mode").val('Edit');
				}
			);
			$("#Data").modal("show");
		}
		function GetImg(id) {
		    $("#id2").val(id);
			$.post("ajax/perbaikan_crud.php", {
					id: id, type:"Detil_Data"
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
					url: "upload_spk.php",
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
		});
		function Download() 
		{
			var tgl1 = $("#tgl1").val();
			var tgl2 = $("#tgl2").val();	
			var cari = $("#search_name").val();
			var field = $("#field").val();
			var field1 = $("#field1").val();
			var cari1 = $("#search_name1").val();
			var id = tgl1+'|'+tgl2+'|'+field+'|'+cari+'|'+field1+'|'+cari1;
			var idx = btoa(id);
			var win = window.open('spk_excel.php?id='+idx);
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Car Repair Data</b></font></h1></li>					
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
						<span class="input-group-addon" style="text-align:right;"><b>Filter By :</b></span>
						<select size="1" id="field"  onchange="ReadData(1)" name="field" style="padding:4px;margin-right:2px;width: 85px">
							<option>No SPK</option>
							<option>No Police</option>
							<option>Type</option>
							<option value="<?php echo $field; ?>" selected hidden><?php echo $field; ?></option>
						</select>
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
					</div>
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"></span>
						<select size="1" id="field1"  onchange="ReadData(1)" name="field1" style="padding:4px;margin-right:2px;width: 85px">
							<option>No SPK</option>
							<option>No Police</option>
							<option>Type</option>
							<option value="<?php echo $field1; ?>" selected hidden><?php echo $field1; ?></option>
						</select>
						<input type="text"  id ="search_name1" name="search_name1" value="<?php echo $search_name1; ?>" 
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
								onClick="window.location.href = 'perbaikan_data.php?id=<?php echo $xy1; ?>' ">
								<span class="fa  fa-plus-square"></span>
								<b>Add New</b>
								</button>	
							<?php }?>	
							<button class="btn btn-block btn-warning" 
								style="margin:0px;margin-left:-1px;margin-bottom:0px;border-radius:2px" type="button"  title = ""
								onClick="javascript:Download()">
								<span class="fa fa-file-text"></span>
								<b>Download</b>
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data SPK</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. SPK :</b></span>
								<input type="text" id="no_spk"  value="" style="text-transform: uppercase;
								text-align: center;width:24.5%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="mode"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Date :</b></span>
								<input type="text" id="tanggal"  value="" style="padding:4px;text-align: center;width:24.5%;border:1px solid rgb(169, 169, 169);background:#eee"  />
								
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Start :</b></span>
								<select size="1" id="jam_mulai"  style="padding:4px;margin-right:2px">
									<?php 
										for ($x = 1; $x <= 24; $x++) {  
											$n = $x;
										if(strlen($n)== '1')
										{
											$n = "0$n";
										}
									?>
									<option><?php echo $n;?></option>
									<?php }?>
								</select>	
								:
								<select size="1" id="menit_mulai"  style="padding:4px;margin-right:2px">
									<?php 
										for ($x = 1; $x <= 59; $x++) {  
											$n = $x;
										if(strlen($n)== '1')
										{
											$n = "0$n";
										}
									?>
									<option><?php echo $n;?></option>
									<?php }?>
								</select>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Finish :</b></span>
								<select size="1" id="jam_selesai"  style="padding:4px;margin-right:2px">
									<?php 
										for ($x = 1; $x <= 24; $x++) {  
											$n = $x;
										if(strlen($n)== '1')
										{
											$n = "0$n";
										}
									?>
									<option><?php echo $n;?></option>
									<?php }?>
								</select>	
								:
								<select size="1" id="menit_selesai"  style="padding:4px;margin-right:2px">
									<?php 
										for ($x = 1; $x <= 59; $x++) {  
											$n = $x;
										if(strlen($n)== '1')
										{
											$n = "0$n";
										}
									?>
									<option><?php echo $n;?></option>
									<?php }?>
								</select>	
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
									
							</div>		
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Patch (Tempelan) :</b></span>
								<input type="text" id="tempelan"  value="" style="text-transform: uppercase;
								text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Kilometer :</b></span>
								<input type="text" id="km"  value="" style="text-align: right;width:20%;border:1px solid rgb(169, 169, 169)" onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)" />	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Type of work :</b></span>
								<select id="jenis_spk"  style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_jenis_spk where status = '1' order by nama  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['nama'];?>" ><?php echo $d1['nama'];?></option>
									<?php }?>
								</select>
							</div>							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Part Usage :</b></span>
								<input type="text" id="part"  value="" style="text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />	
							</div>
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Information :</b></span>
								<textarea name="ket" id="ket"
								style="resize:none;width: 80%; height: 80px; font-size: 11px; line-height: 12px; 
								border: 1px solid #444; padding: 5px;"  ></textarea>		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddData()">
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
									<button type="submit" class="btn btn-success" >&nbsp;&nbsp;Simpan&nbsp;&nbsp;</button>	
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
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	<script type="text/javascript" src="js/bootstrap-imgupload.min.js"></script>
	<script type="text/javascript">
        $('.imgupload').imgupload();
    </script>
  </body>
</html>
