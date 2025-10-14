<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	include "lib.php";

	if(!isset($_SESSION['id_user'])  ) {
		header('location:logout.php'); 
	}

	if($_SERVER['REQUEST_METHOD'] == "POST") {
		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// die();

		$mode = $_POST['mode'];
		$id_data = $_POST['id_data'];	
		$tanggal = $_POST['tanggal'];	
		$id_cust = $_POST['id_cust'];
		$ket = addslashes(trim($_POST['ket']));
		$tanggalx = ConverTglSql($tanggal);
		$sap_rowid 	= $_POST['rowid'];
		
		if($mode == 'Add' ){
			$ptgl = explode("-", $tanggal);
			$tg = $ptgl[0];
			$bl = $ptgl[1];
			$th = $ptgl[2];	
			$query = "SELECT max(right(no_doc,5)) as maxID FROM  t_ware_data where  year(tanggal) = '$th' and jenis = '0' ";
			$hasil = mysqli_query($koneksi, $query);    
			$data  = mysqli_fetch_array($hasil);
			$idMax = $data['maxID'];
			if ($idMax == '99999'){
				$idMax='00000';
			}
			$noUrut = (int) $idMax;   
			$noUrut++;  
			if(strlen($noUrut)=='1'){
				$noUrut="0000$noUrut";
				}elseif(strlen($noUrut)=='2'){
				$noUrut="000$noUrut";
				}elseif(strlen($noUrut)=='3'){
				$noUrut="00$noUrut";
				}elseif(strlen($noUrut)=='4'){
				$noUrut="0$noUrut";
			}   
			$year = substr($th,2,2);
			$no_doc = "$year$bl$noUrut";
			
			$sql = "INSERT INTO  t_ware_data (jenis, rowid, tanggal, no_doc, id_cust, ket, created) VALUES ('0', '$sap_rowid', '$tanggalx', '$no_doc', '$id_cust', '$ket', '$id_user')";
			$hasil= mysqli_query($koneksi, $sql);
			
			$sql = mysqli_query($koneksi, "SELECT max(id_data)as id from t_ware_data ");			
			$row = mysqli_fetch_array($sql);
			$id_data = $row['id'];
			
		}else{
			
			$sql = "UPDATE t_ware_data SET 	ket = '$ket' WHERE id_data = '$id_data'	";
			$hasil=mysqli_query($koneksi,$sql);
		}
		
		$cat ="Data saved...";
		$xy1="Edit|$id_data|$cat";
		$xy1=base64_encode($xy1);
		header("Location: ware_in_data.php?id=$xy1");
	} else{
		$idx = $_GET['id'];	
		$x=base64_decode($idx);
		$pecah = explode("|", $x);
		$mode= $pecah[0];
		$id_data = $pecah[1];
		$cat = $pecah[2];
	}

	if($mode == 'Add') {
		$no_doc = '-- Auto -- ';
		$tanggal = date('d-m-Y');
		
	} else{
		
		$pq = mysqli_query($koneksi, "SELECT 
				t_ware_data.*, 
				m_cust_tr.nama_cust,
				sap_project.rowid,
				sap_project.kode_project
			FROM t_ware_data 
			LEFT JOIN m_cust_tr ON t_ware_data.id_cust = m_cust_tr.id_cust
			LEFT JOIN sap_project ON sap_project.rowid = t_ware_data.rowid
			WHERE t_ware_data.id_data = '$id_data' ");
		$rq=mysqli_fetch_array($pq);	

		$no_doc 	  = $rq['no_doc'];
		$tanggal 	  = ConverTgl($rq['tanggal']);
		$id_cust	  = $rq['id_cust'];
		$nama_cust 	  = $rq['nama_cust'];
		$ket 		  = $rq['ket'];
		$disx 		  = 'Disabled';
		$rowid		  = $rq['rowid'];
		$kode_project = $rq['kode_project'];
	}

	if($mode == 'View') {
		$dis = "Disabled";
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
			var date_input=$('input[name="tanggal"]'); 
			var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
			date_input.datepicker({
				format: 'dd-mm-yyyy',
				container: container,
				todayHighlight: true,
				autoclose: true,
			})
			ReadData();
		});	
		function ReadData() {
			var id_data	= $("#id_data").val();
			var mode = $("#mode").val();
			$.get("ajax/ware_crud.php", {mode:mode,id_data:id_data, type:"Read_In_Data" }, function (data, status) {
				$(".tampil_data").html(data);
			});
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
		function TampilCust(){	
			$("#cari").val('');
			ListCust();
			$('#DaftarCust').modal('show');
		}
		function ListCust() {	
			var cari = $("#cari").val();
			$.get("ajax/cust_crud.php", {cari:cari,  type:"ListCust" }, function (data, status) {
				$(".tampil_cust").html(data);
				$("#hal").val(hal);
			});
		}
		function PilihCust(id) {	
			$.post("ajax/cust_crud.php", {
					id: id, type:"DetilData"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					$("#nama_cust").val(data.nama_cust);
					$("#id_cust").val(id);
					CekRate();
				}
			);
			$("#DaftarCust").modal("hide");
		}
		function TampilBarang(){	
			var id_cust = $("#id_cust").val();
			var cari = $("#cari_barang").val();
			var filter = $("#filter").val();
			cari='';
			$.get("ajax/ware_crud.php", {filter:filter, cari:cari, id_cust:id_cust, type:"List_Barang" }, function (data, status) {
				$(".tampil_barang").html(data);
			});
			$('#DataBarang').modal('show');
		}
		function ListBarang() {	
			var filter = $("#filter").val();
			var cari = $("#cari_barang").val();
			var id_cust = $("#id_cust").val();
			$.get("ajax/ware_crud.php", {filter:filter, cari:cari, id_cust:id_cust, type:"List_Barang" }, function (data, status) {
				$(".tampil_barang").html(data);
			});
		}
		function PilihBarang(id) {		
			$.post("ajax/ware_crud.php", {
					id: id, type:"Detil_Data_Barang"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_ware").val(data.id_ware);
					$("#nama").val(data.nama);
					$("#kode").val(data.kode);
					$("#nama_cust").val(data.nama_cust);
					$("#unit").val(data.unit);
				}
			);
			$("#DataBarang").modal("hide");
		}
		function checkvalue() {
			var id_cust = document.getElementById('id_cust').value; 
			if(id_cust == '') {
				alert ('Customer harus diisi..');				
				return false;						
			}else{
				return true;
			}	
		}
		function TampilData() {
			$("#id_ware").val('');
			$("#kode").val('');
			$("#no_cont").val('');
			$("#unit").val('');
			$("#unitx").val('');
			$("#nama").val('');
			$("#qty").val('');
			$("#vol").val('');
			$("#modex").val('Add');
			$('#Data').modal('show');
		}
		function AddData() {	
			var qty = $("#qty").val();
			if(!$("#no_cont").val()){
				alert("No. Container harus diisi !..");
			}
			else if(!$("#id_ware").val()){
				alert("Item Number harus diisi !..");
			}
			else if(qty <= 0){
				alert("Qty harus diisi !..");
			}			
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id = $("#id_detil").val();
					var id_ware = $("#id_ware").val();
					var id_data = $("#id_data").val();
					var no_cont = $("#no_cont").val();
					var qty = $("#qty").val();
					var id_lokasi = $("#id_lokasi").val();
					var mode = $("#modex").val();					
					$.post("ajax/ware_crud.php", {
						id_ware:id_ware,
						no_cont:no_cont,
						qty:qty,
						id_lokasi:id_lokasi,
						id:id,
						id_data:id_data,
						mode:mode,
						type : "Add_Data_In"
						}, function (data, status) {
						alert(data);
						$("#Data").modal("hide");	
						ReadData(1);
					});
				}
			}	
		}
		function GetData(id) {
			$("#id_detil").val(id);	
			$.post("ajax/ware_crud.php", {
					id: id, type:"Detil_Data_In"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#no_cont").val(data.no_cont);
					$("#id_ware").val(data.id_ware);
					$("#kode").val(data.kode);
					$("#nama").val(data.nama);
					$("#qty").val(data.masuk);
					$("#unit").val(data.unit);
					$("#id_lokasi").val(data.id_lokasi);
					$("#modex").val('Edit');
				}
			);
			$("#Data").modal("show");
		}

	// ========= SAP PROJECT =========
		function TampilSAP(){
			$cari = $("#cari_SAP").val('');
			ListSAP();
			$('#DaftarSAP').modal('show');
		}
		function ListSAP() {
			var cari = $("#cari_SAP").val();
			$.get("ajax/jo_crud.php", {cari:cari,  type:"ListSAPWH" }, function (data, status) {
				$(".tampil_SAP").html(data);
			});
		}
		function PilihSAP(id) {
			$.post("ajax/jo_crud.php", {
					id: id, type:"DetilSAP"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					$("#sap_project").val(data.kode_project);
					$("#rowid").val(data.rowid);
				}
			);
			$("#DaftarSAP").modal("hide");
		}
		function AddSAP() {
			$.get("ajax/po_crud.php", { type: "AddProject" }, function (res) {
				$("#sap_project").val(res.newKode);
				$("#rowid").val(res.rowid);

				$("#DaftarSAP").modal("hide");
			}, "json");
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
		
		<form method="post" name ="myform"  class="form-horizontal" onsubmit="return checkvalue(this)" > 
		<div class="content-wrapper" style="min-height:750px">
			<br>
			<ol class="breadcrumb">
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Inbound</b></font></h1></li>					
			</ol>
			<br>
			<?php if($cat != '') {?>
			<div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
				<i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo "$cat"; ?></font>
			</div>
			<?php }?>
			
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:200px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
					text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Data Inbound</b>
					</div>
					<br>
					<input type="hidden" id ="id_data" name="id_data" value="<?php echo $id_data; ?>" >	
					<input type="hidden" id ="mode" name="mode" value="<?php echo $mode; ?>" >
					<input type="hidden" id ="rowid" name="rowid" value="<?php echo $rowid; ?>" >

					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>#No Doc :</b></span>
						<input type="text"  id ="no_doc" name="no_doc" value="<?php echo $no_doc; ?>" 
						style="text-align: center;width:20%" readonly <?php echo $dis;?> >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Tanggal :</b></span>
						<input type="text"  id ="tanggal" name="tanggal" value="<?php echo $tanggal; ?>" 
						style="text-align: center;width:20%" readonly <?php echo $dis;?> <?php echo $disx;?> >
					</div>				
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Customer :</b></span>

						<input type="text"  id ="nama_cust" name="nama_cust" value="<?php echo $nama_cust; ?>" style="text-transform: uppercase;text-align: left;width:70%;" readonly <?php echo $dis;?> >

						<input type="hidden"  id ="id_cust" name="id_cust" value="<?php echo $id_cust; ?>" >

						<button class="btn btn-block btn-primary" <?php echo $disx;?> style="padding:6px 12px 6px 12px; ;margin-top:-3px;border-radius:2px;margin-left:5px" type="button" onClick="javascript:TampilCust()">
							<span class="glyphicon glyphicon-search"></span>
						</button>	
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Project :</b></span>
						<input type="text" name="sap_project" id="sap_project" style="text-transform: uppercase;text-align: left;width:70%;" value="<?php echo $kode_project; ?>"  readonly>
							
						<button class="btn btn-block btn-primary" id="po" style="padding:6px 12px 6px 12px; ;margin-top:-3px;border-radius:2px;margin-left:5px" type="button" onClick="javascript:TampilSAP()" <?php echo $disx;?> >
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</div>
					<br>	
				</div>
            </div>
			
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:200px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Remarks</b>
					</div>
					<br>	
					
					<div style="width:100%;" class="input-group">
						
						<textarea name="ket" id="ket"
						style="margin-left:10px;resize:none;width: 95%; height: 80px; font-size: 11px; line-height: 12px; 
						border: 1px solid #4; padding: 5px;" <?php echo $dis;?> ><?php echo $ket; ?></textarea>
					</div>
					<br>	
				</div>
            </div>
		
			<?php if($mode != 'Add'){?>	
				<div class="col-md-12" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:195px">
						<?php if($mode == 'Edit'){?>
							<button class="btn btn-block btn-success" 
								style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:2px" type="button" 
								onClick="javascript:TampilData()"  <?php echo $dis;?> <?php echo $dis_copy;?> >
								<span class="fa  fa-plus-square"></span>
								<b>Add Barang</b>
							</button>
						<?php }?>
						<div class="table-responsive mailbox-messages" style="min-height:10px">									
							<div class="tampil_data"></div>
						</div>	
					</div>
				</div>
			<?php }?>
				
			<?php
				$link = "ware_in.php?id=$xy1";
				$xy1="$id_data";
				$idx=base64_encode($xy1);
			?>
			<div class="col-md-12" >
				<div style="width:98%;background:none;margin-left:0;margin-top:0px;border-top:0px;border-bottom:0px" class="input-group">
					<?php if($mode != 'View'){?>
				<button type="submit" class="btn btn-success"><span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save</b>&nbsp;&nbsp;</button>	
				<?php }?>
				<button type="button" class="btn btn-danger" onclick="window.location.href='<?php echo $link; ?>'"><span class="fa fa-backward"></span>&nbsp;&nbsp;<b>Back</b></button>	
				<?php if($mode != 'Add' ){?>
					<button class="btn btn-block btn-warning" 
						style="margin:0px;margin-bottom:0px;margin-left:1px;border-radius:2px;" type="button" 
						onClick="window.open('cetak_po_masuk.php?id=<?php echo $idx;?>','blank')" >
						<span class="fa fa-print "></span>
						<b>Print</b>
					</button>	
					<?php }?>
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
	
	<div class="modal fade" id="DaftarCust"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">	
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Customer</b>
							</div>	
							<br>
							<div style="width:100%" class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Search :</b>&nbsp;&nbsp;
									<input type="text"  id ="cari" name="cari" value="<?php echo $cari; ?>" 
									style="text-align: left;width:200px" onkeypress="ListCust()" >
									<button class="btn btn-block btn-primary" 
									style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:2px;padding:5px" 
									onClick="javascript:ListCust()" ">
									<span class="glyphicon glyphicon-search"></span>
									</button>
									<button class="btn btn-block btn-danger" 
									style="margin:0px;margin-left:-2px;margin-bottom:3px;border-radius:2px;padding:5px"  
									data-dismiss="modal" >
									<span class="glyphicon glyphicon-remove"></span>
									</button>
								</span>
								<span class="input-group-addon" style="width:80%;text-align:right;padding:0px">									
								</span>
							</div>							
							<div class="table-responsive mailbox-messages" >									
								<div class="tampil_cust"></div>
							</div>
							<br>
						</div>		
					</div>		
				</div>	
			</div>
		</div>	
    </div>
	
	<div class="modal fade" id="Data"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">	
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Inbound</b>
							</div>	
							<br>
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Container :</b></span>
								<input type="text" id="no_cont"  value="" style="text-transform: uppercase;
								text-align: left;width:80%;border:1px solid rgb(169, 169, 169)"  />	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Item Number :</b></span>
								<input type="text"  id ="kode" name="kode" style="text-align: left;width:74%" readonly  >
								<button class="btn btn-block btn-primary" id="cost"
									style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-1px" type="button" 
									onClick="javascript:TampilBarang()">
									<span class="glyphicon glyphicon-search"></span>
								</button>	
								<input type="hidden" id="id_ware"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id_detil"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="modex"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Item Description :</b></span>
								<input type="text"  id ="nama" name="nama" style="text-align: left;width:80%" readonly  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Qty :</b></span>
								<input type="text" id="qty"  value="" style="text-align: center;width:15%;border:1px solid rgb(169, 169, 169)" 
								onkeypress="return isNumber(event)" />	
								<input type="text"  id ="unit" name="nama" style="padding:3px;text-align: center;width:10%">
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Lokasi :</b></span>
								<select id="id_lokasi"  style="width: 80%;padding:4px">
									<?php
									$tampil1="select * from m_lokasi_ware where status = '1' order by nama  ";
									$hasil1=mysqli_query($koneksi, $tampil1);       
									while ($data1=mysqli_fetch_array($hasil1)){?>
									<option value="<?php echo $data1['id_lokasi'];?>" ><?php echo $data1['nama'];?></option>
									<?php }?>
								</select>
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
	
	<div class="modal fade" id="DataBarang"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document" style="width:60%">
			<div class="modal-content" style="background: none">	
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Search Data</b>
							</div>	
							<br>
							<div style="width:100%" class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:150%;text-align:left;padding:0px">
									&nbsp;&nbsp;&nbsp;<b>Filter By : </b>
									<select size="1" id="filter"  onchange="ListBarang()" name="field1" style="padding:3px;margin-right:0px;width: 105px">
										<option>Item Number</option>
										<option>Item Description</option>
									</select>
									<input type="text"  id ="cari_barang" name="cari_barang" value="<?php echo $cari_part; ?>" 
									style="text-align: left;width:200px" onkeypress="ListBarang()" >
									<button class="btn btn-block btn-primary" 
									style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:0px;padding:5px" type="button" 
									onClick="javascript:ListBarang()" ">
									<span class="glyphicon glyphicon-search"></span>
									</button>
									<button class="btn btn-block btn-danger" 
									style="margin:0px;margin-left:-2px;margin-bottom:3px;border-radius:2px;padding:5px"  
									data-dismiss="modal" >
									<span class="glyphicon glyphicon-remove"></span>
									</button>
								</span>
							</div>							
							<div class="table-responsive mailbox-messages" >									
								<div class="tampil_barang"></div>
							</div>
							<br>
						</div>
					</div>								
				</div>
			</div>
		</div>	
    </div>


	<!-- ======== MODAL SAP ======== -->
		<div class="modal fade" id="DaftarSAP"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">

								<div class="small-box bg" style="font-size:12px;font-family:'Arial';color:#fff;margin:0;background-color:#4783b7;text-align:left;padding:5px;">
									<b><i class="fa fa-list"></i>&nbsp;Data SAP Project</b>
								</div>

								<div style="display:flex;align-items:center;gap:10px;margin-top:10px;">
									<label style="margin:0;width: 100px;"><b>Search :</b></label>
									<input type="text" id="cari_SAP" name="cari_SAP" value="<?php echo $cari; ?>" style="width:40%" onkeypress="ListSAP()">

									<button class="btn btn-primary" style="padding:6px 10px;" onClick="ListSAP()">
										<span class="glyphicon glyphicon-search"></span> Search
									</button>
									<button class="btn btn-success" style="padding:6px 10px;" onClick="AddSAP()">
										<span class="glyphicon glyphicon-plus"></span> Project
									</button>
									<button class="btn btn-danger" style="padding:6px 10px;" data-dismiss="modal">
										<span class="glyphicon glyphicon-remove"></span> Close
									</button>
								</div>

								<input type="hidden" id="jenis_project" value="">

								<div class="table-responsive mailbox-messages" style="margin-top:15px;">
									<div class="tampil_SAP"></div>
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
