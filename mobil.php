<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	include "lib.php";

	$pq 	= mysqli_query($koneksi, "SELECT * FROM m_role_akses_tr WHERE id_role = '$id_role' AND id_menu ='11' ");
	$rq=mysqli_fetch_array($pq);	

	$m_edit = $rq['m_edit'];
	$m_add 	= $rq['m_add'];
	$m_del 	= $rq['m_del'];
	$m_view = $rq['m_view'];
	$m_exe 	= $rq['m_exe'];

	if(!isset($_SESSION['id_user'])  ||  $m_view != '1'  ){
		header('location:logout.php'); 
	}

	if($_SERVER['REQUEST_METHOD'] == "POST"){	
		$hal = $_POST['hal'];
		$field = $_POST['field'];
		$search_name = $_POST['search_name'];
		$paging = $_POST['paging'];
		$status = $_POST['status'];
	}else{	
		$paging='25';
		$hal='1';
		$field = 'No. Polisi';
		$status='All';
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
			$("#tgl_stnk").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			$("#tgl_kir").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			$("#tgl_card").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			var hal = $("#hal").val();
			ReadData(hal);
		});
		function ReadData(hal) {
			var search_name = $("#search_name").val();
			var field = $("#field").val();
			var paging = $("#paging").val();
			$.get("ajax/mobil_crud.php", {field:field, paging:paging,search_name:search_name,hal:hal, type:"Read" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}
		function TampilData(){
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
			$("#tgl_stnk").val(today);
			$("#tgl_kir").val(today);
			$("#tgl_card").val(today);
			$("#telp").val('');
			$("#no_polisi").val('');
			$("#chasing").val('');
			$("#merk").val('');
			$("#tahun_buat").val('');
			$("#tahun_rakit").val('');
			$("#silinder").val('');
			$("#warna_truck").val('');
			$("#no_rangka").val('');
			$("#no_mesin").val('');
			$("#no_bpkb").val('');
			$("#no_kabin").val('');
			$("#iden").val('');
			$("#warna_tnkb").val('');
			$("#bbm").val('');
			$("#berat_max").val('');
			$("#no_reg").val('');
			$("#asuransi").val('');
			$("#pajak").val('');
			$("#mode").val('Add');
			$('#Data').modal('show');
			document.getElementById("id_supir").disabled = false;
			document.getElementById("btnSave").disabled = false;
			document.getElementById("tgl_stnk").disabled = false;
			document.getElementById("tgl_kir").disabled = false;
			document.getElementById("no_polisi").disabled = false;
			document.getElementById("telp").disabled = false;
			document.getElementById("merk").disabled = false;
			document.getElementById("tahun_buat").disabled = false;
			document.getElementById("tahun_rakit").disabled = false;
			document.getElementById("silinder").disabled = false;
			document.getElementById("warna_truck").disabled = false;
			document.getElementById("no_rangka").disabled = false;
			document.getElementById("no_mesin").disabled = false;
			document.getElementById("no_bpkb").disabled = false;
			document.getElementById("no_kabin").disabled = false;
			document.getElementById("iden").disabled = false;
			document.getElementById("warna_tnkb").disabled = false;
			document.getElementById("bbm").disabled = false;
			document.getElementById("berat_max").disabled = false;
			document.getElementById("no_reg").disabled = false;
			document.getElementById("stat").disabled = false;
		}
		function add() {	
			if(!$("#no_polisi").val()){
				alert("No. Polisi harus diisi !..");
			}
			else{
				var id 			= $("#id").val();
				var no_polisi 	= $("#no_polisi").val();
				var tgl_stnk 	= $("#tgl_stnk").val();
				var tgl_kir 	= $("#tgl_kir").val();
				var tgl_card 	= $("#tgl_card").val();
				var merk 		= $("#merk").val();
				var tahun_buat 	= $("#tahun_buat").val();
				var tahun_rakit = $("#tahun_rakit").val();
				var silinder 	= $("#silinder").val();
				var warna_truck = $("#warna_truck").val();
				var no_rangka 	= $("#no_rangka").val();
				var no_mesin 	= $("#no_mesin").val();
				var no_bpkb 	= $("#no_bpkb").val();
				var no_kabin 	= $("#no_kabin").val();
				var iden 		= $("#iden").val();
				var warna_tnkb 	= $("#warna_tnkb").val();
				var bbm 		= $("#bbm").val();
				var berat_max 	= $("#berat_max").val();
				var no_reg 		= $("#no_reg").val();
				var stat 		= $("#stat").val();
				var mode 		= $("#mode").val();
				var hal 		= $("#hal").val();

				var asuransi = $("#asuransi").val();
				var pajak 	 = $("#pajak").val();

				$.post("ajax/mobil_crud.php", {
					id:id,
					no_polisi:no_polisi,
					tgl_stnk:tgl_stnk,
					tgl_kir:tgl_kir,
					tgl_card:tgl_card,
					merk:merk,	
					tahun_buat:tahun_buat,
					tahun_rakit:tahun_rakit,
					silinder:silinder,
					warna_truck:warna_truck,
					no_rangka:no_rangka,
					no_mesin:no_mesin,
					no_bpkb:no_bpkb,
					no_kabin:no_kabin,
					iden:iden,
					warna_tnkb:warna_tnkb,
					bbm:bbm,
					berat_max:berat_max,
					no_reg:no_reg,					
					mode:mode,
					stat:stat,

					asuransi:asuransi,
					pajak:pajak,

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
			$.post("ajax/mobil_crud.php", {
					id: id, type:"detil"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#tgl_stnk").val(changeDateFormat(data.tgl_stnk));
					$("#tgl_kir").val(changeDateFormat(data.tgl_kir));
					$("#no_polisi").val(data.no_polisi);
					$("#chasing").val(data.chasing);
					$("#merk").val(data.merk);
					$("#tahun_buat").val(data.tahun_buat);
					$("#tahun_rakit").val(data.tahun_rakit);
					$("#silinder").val(formatCurrency(data.silinder));
					$("#warna_truck").val(data.warna_truck);
					$("#no_rangka").val(data.no_rangka);
					$("#no_mesin").val(data.no_mesin);
					$("#no_bpkb").val(data.no_bpkb);
					$("#no_kabin").val(data.no_kabin);
					$("#iden").val(data.iden);
					$("#warna_tnkb").val(data.warna_tnkb);
					$("#bbm").val(data.bbm);
					$("#berat_max").val(data.berat_max);
					$("#no_reg").val(data.no_reg);
					$("#stat").val(data.status);

					$("#asuransi").val(data.asuransi);
					$("#pajak").val(data.pajak);
					
					$("#mode").val('Edit');		
				}
			);
			$("#Data").modal("show");
		}
	
		function GetImg(id) {
		    $("#id2").val(id);
			$.post("ajax/mobil_crud.php", {
					id: id, type:"detil"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#photo_lama").val(data.kop);
				}
			);
			$("#DataPhoto").modal("show");
		}
		function GetDoc(id) {
		    $("#id3").val(id);
			$.post("ajax/mobil_crud.php", {
					id: id, type:"detil"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#nama3").val(data.no_polisi);
				}
			);
			$("#DataUpload").modal("show");
		}
		$(document).ready(function (e) {
			$("#formx").on('submit',(function(e) {
				e.preventDefault();
				$.ajax({
					url: "upload_photo_mobil.php",
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
					url: "upload_doc_mobil.php",
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
		
		
		<form method="post" name ="myform" action="mobil.php?action=cari" class="form-horizontal" > 
			<div class="content-wrapper" style="min-height:750px">
				<br>
				<ol class="breadcrumb">
					<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Car Data</b></font></h1></li>					
				</ol>
				<br>
				<div class="col-md-12" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
						<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								<b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
						</div>
						<br>		
							
						<div style="width:100%" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Filter Data :</b></span>
							<select size="1" id="field"  name="field" style="padding:4px;margin-right:3px;width: 85px" onchange="ReadData(1)">
								<option>No. Police</option>
								<option>Brand</option>
								<option>Year</option>
								<option value="<?php echo $field; ?>" selected hidden><?php echo $field; ?></option>
							</select>
							<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
							style="text-align: left;margin-left:0px;width:200px" onkeypress="ReadData(1)" >
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
	
	<!-- =================== ADD DATA MOBIL =================== -->
	<div class="modal fade" id="Data"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Car Data XXX</b>
							</div>	
							<br>
							<input type="hidden" id="id"/>	
							<input type="hidden" id="mode"/>	
							<input type="hidden" id="tgl_card"/>	
							<input type="hidden" id ="no_reg" name="no_reg" >
							<input type="hidden" id ="iden" name="iden" >
							<input type="hidden" id ="no_kabin" name="no_kabin">

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Police :</b></span>
								<input type="text"  id ="no_polisi" name="no_polisi" value="" maxlength="15"
								style="text-transform: uppercase;text-align: center;width:22%"  >	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Brand/Type :</b></span>
								<input type="text"  id ="merk" name="merk" value="" 
								style="text-transform: uppercase;text-align: left;width:85%"  >
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Production year :</b></span>
								<input type="text"  id ="tahun_buat" name="tahun_buat" value="" maxlength="4"
								style="text-transform: uppercase;text-align: center;width:10%"  onkeypress="return isNumber(event)" >
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Year of Assembly :</b></span>
								<input type="text"  id ="tahun_rakit" name="tahun_rakit" value="" maxlength="4"
								style="text-transform: uppercase;text-align: center;width:10%" onkeypress="return isNumber(event)" >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Fill the Cylinder :</b></span>
								<input type="text"  id ="silinder" name="silinder" value="" maxlength="15"
								style="text-transform: uppercase;text-align: right;width:20%" 
								onBlur ="this.value=formatCurrency(this.value);" onkeypress="return isNumber(event)" >
								CC
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Car Color :</b></span>
								<input type="text"  id ="warna_truck" name="warna_truck" value="" maxlength="30"
								style="text-transform: uppercase;text-align: center;width:30%"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>No. Frame/NIK :</b></span>
								<input type="text"  id ="no_rangka" name="no_rangka" value="" 
								style="text-transform: uppercase;text-align: left;width:85%" >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>No. Machine :</b></span>
								<input type="text"  id ="no_mesin" name="no_mesin" value="" 
								style="text-transform: uppercase;text-align: left;width:85%"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>No. BPKB :</b></span>
								<input type="text"  id ="no_bpkb" name="no_bpkb" value="" 
								style="text-transform: uppercase;text-align: left;width:85%"  >
								
							</div>							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Color TNKB :</b></span>
								<input type="text"  id ="warna_tnkb" name="warna_tnkb" value="" maxlength="30"
								style="text-transform: uppercase;text-align: center;width:30%"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Fuel :</b></span>
								<select id="bbm" name="bbm" style="width: 85%;" <? echo $dis;?>>
									<?php
									$t1="SELECT * from m_bbm order by nama ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
										<option ><?php echo $d1['nama'];?></option>
									<?php }?>
								</select>						
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Maximum Weight :</b></span>
								<input type="text"  id ="berat_max" name="berat_max" value="" 
								style="text-align: left;width:50%"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Validity Period STNK :</b></span>
								<input type="text" id="tgl_stnk"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" readonly />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Validity Period KIR :</b></span>
								<input type="text" id="tgl_kir"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" readonly />	
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Validity  Insurance :</b></span>
								<input type="date" id="asuransi" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" />	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Validity Period Tax :</b></span>
								<input type="date" id="pajak" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" />	
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
								<button type="button" id="btnSave" class="btn btn-primary" onclick="add()">&nbsp;&nbsp;Save&nbsp;&nbsp;</button>	
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
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Police :</b></span>
								<input type="text"  id ="nama3" name="nama" value="<?echo $nama; ?>"
								style="text-transform: uppercase;text-align: left;width:30%" disabled >	
								<input type="hidden" id="id3"  name="id3" value=""   />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>BKPB :</b></span>
								<input type='file' name='bpkp' style="height:26px;padding:4px;width:80%;font-family:tahoma;font-size:11px" id='file' class='form-control' ><br>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>STNK :</b></span>
								<input type='file' name='stnk' style="height:26px;padding:4px;width:80%;font-family:tahoma;font-size:11px" id='file' class='form-control' ><br>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>KIR :</b></span>
								<input type='file' name='kir' style="height:26px;padding:4px;width:80%;font-family:tahoma;font-size:11px" id='file' class='form-control' ><br>
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
