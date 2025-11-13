<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	include "lib.php";

	if(!isset($_SESSION['id_user'])  ){
		header('location:logout.php'); 
	}

	if($_SERVER['REQUEST_METHOD'] == "POST"){		

		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// exit;

		$mode 		= $_POST['mode'];
		$id_data 	= $_POST['id_data'];	
		$tgl_sj 	= $_POST['tgl_sj'];	
		$id_cust 	= $_POST['id_cust'];
		$gudang 	= trim(addslashes(strtoupper($_POST['gudang'])));
		$no_do 		= trim(addslashes(strtoupper($_POST['no_do'])));
		$id_mobil 	= $_POST['id_mobil'];
		$id_supir 	= $_POST['id_supir'];
		$supir 		= trim(addslashes(strtoupper($_POST['supir'])));
		$no_polisi 	= trim(addslashes(strtoupper($_POST['no_polisi'])));
		$telp 		= trim(addslashes(strtoupper($_POST['telp'])));
		$ket 		= addslashes(trim($_POST['ket']));
		$tgl_sjx 	= ConverTglSql($tgl_sj);
		$jenis_sj 	= $_POST['jenis_sj'];
		$sj_vendor 	= $_POST['sj_vendor'];
		$rowid 		= $_POST['rowid'];
		
		$uj 		= str_replace(",","", $uj);
		$ritase 	= str_replace(",","", $ritase);
		
		if($jenis_sj == '1'){
			$id_mobil = 0;
			$id_supir = 0;
		} else{
			$pq 	= mysqli_query($koneksi, "SELECT 
						* 
					FROM m_mobil_tr 
					WHERE id_mobil = '$id_mobil' ");
			$rq		= mysqli_fetch_array($pq);

			$no_polisi = $rq['no_polisi'];
			$pq 	= mysqli_query($koneksi, "SELECT 
						* 
					FROM m_supir_tr 
					WHERE id_supir = '$id_supir' ");
			$rq		= mysqli_fetch_array($pq);

			$supir 	= $rq['nama_supir'];
			$telp 	= $rq['telp'];
		}
		
		
		if($mode == 'Add' ){
			$ptgl 	= explode("-", $tgl_sj);
			$tg 	= $ptgl[0];
			$bl 	= $ptgl[1];
			$th 	= $ptgl[2];	
			
			$query 	= "SELECT max(right(no_doc,5)) AS maxID 
					FROM t_ware_data WHERE year(tanggal) = '$th' AND jenis = '1' ";
			$hasil 	= mysqli_query($koneksi, $query);    
			$data  	= mysqli_fetch_array($hasil);
			$idMax 	= $data['maxID'];

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
			$no_sj = "SJWH-$year$noUrut";
			
			$query = "SELECT max(right(no_ref,5)) AS maxID 
					FROM t_ware_data 
					WHERE year(tanggal) = '$th' 
						AND jenis = '1' 
						AND id_cust = '$id_cust' ";
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
			$no_ref = "RF-$year$noUrut";
			
			$sql = "INSERT INTO  t_ware_data 
				(rowid, jenis, tanggal, no_doc, id_cust, jenis_sj, gudang, id_mobil, id_supir, no_polisi, supir, telp, ket, created, no_do, no_ref, sj_vendor) 
				VALUES ('$rowid','1', '$tgl_sjx', '$no_sj',  '$id_cust', '$jenis_sj', '$gudang', '$id_mobil', '$id_supir', '$no_polisi', '$supir', '$telp','$ket', '$id_user', '$no_do', '$no_ref', '$sj_vendor')";
				$hasil= mysqli_query($koneksi, $sql);
			
			$sql = mysqli_query($koneksi, "SELECT max(id_data)as id from t_ware_data ");			
			$row = mysqli_fetch_array($sql);
			$id_data = $row['id'];
			
		} else{
			$sql = "update t_ware_data set 
						jenis_sj = '$jenis_sj',
						tanggal = '$tgl_sjx',
						id_cust = '$id_cust',
						gudang = '$gudang',
						id_mobil = '$id_mobil',
						id_supir = '$id_supir',
						supir = '$supir',
						no_polisi = '$no_polisi',
						telp = '$telp',
						ket = '$ket',
						no_do = '$no_do'
						where id_data = '$id_data'	";
				$hasil=mysqli_query($koneksi,$sql);
		
			
		}
		
		$cat ="Data saved...";
		$xy1="Edit|$id_data|$cat";
		$xy1=base64_encode($xy1);
		header("Location: ware_out_data.php?id=$xy1");
	}
	else{	
		$idx = $_GET['id'];	
		$x=base64_decode($idx);
		$pecah = explode("|", $x);
		$mode= $pecah[0];
		$id_data = $pecah[1];
		$cat = $pecah[2];
	}

	if($mode == 'Add'){
		$no_sj = '-- Auto -- ';
		$no_ref = '-- Auto -- ';
		$tgl_sj = date('d-m-Y');
		
		
	}else{
		
		$pq = mysqli_query($koneksi, "SELECT 
				t_ware_data.*, 
				m_cust_tr.nama_cust,
				sap_project.kode_project
			FROM t_ware_data 
			LEFT JOIN m_cust_tr ON t_ware_data.id_cust = m_cust_tr.id_cust
			LEFT JOIN sap_project ON sap_project.rowid = t_ware_data.rowid
			WHERE t_ware_data.id_data = '$id_data'");
		$rq=mysqli_fetch_array($pq);	

		$no_sj 			= $rq['no_doc'];
		$jenis_sj 		= $rq['jenis_sj'];
		$jenis_cross 	= $rq['jenis_cross'];
		$tgl_sj 		= ConverTgl($rq['tanggal']);
		$id_cust 		= $rq['id_cust'];
		$no_ref 		= $rq['no_ref'];
		$nama_cust 		= $rq['nama_cust'];
		$gudang 		= $rq['gudang'];
		$id_mobil 		= $rq['id_mobil'];
		$no_polisi 		= $rq['no_polisi'];
		$ket 			= str_replace("\'","'",$rq['ket']);
		$id_supir 		= $rq['id_supir'];
		$nama_supir 	= $rq['supir'];
		$telp 			= $rq['telp'];
		$supir 			= $rq['supir'];
		$no_do 			= $rq['no_do'];
		$rowid 			= $rq['rowid'];
		$kode_project 	= $rq['kode_project'];
		$sj_vendor 		= $rq['sj_vendor'];
		$disx 			= 'Disabled';

		if($jenis_sj == '1')
		{
			$ceklist = 'checked';
		}
		if($jenis_cross == '1')
		{
			$ceklistx = 'checked';
		}
	}

	if($mode == 'View')
	{
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
			var date_input=$('input[name="jo_date"]'); 
			var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
			date_input.datepicker({
				format: 'dd-mm-yyyy',
				container: container,
				todayHighlight: true,
				autoclose: true,
			})
			$("#tgl_sj").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			var jenis_sj = $("#jenis_sj").val();
			if (jenis_sj == '1'){
				document.getElementById("tampil_vendor").style.display = 'inline';
				document.getElementById("tampil_pt").style.display = 'none';
			} else {
				document.getElementById("tampil_vendor").style.display = 'none';
				document.getElementById("tampil_pt").style.display = 'inline';
			}
			ReadData();
		});	
		function ReadData() {
			var id_data = $("#id_data").val();
			var mode = $("#mode").val();
			
			$.get("ajax/ware_crud.php", {mode:mode,id_data:id_data, type:"Read_Out_Data" }, function (data, status) {
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
		function CekStatus(cb) {
			var checkBox = document.getElementById("jenis");
			if (checkBox.checked == true){
				$("#jenis_sj").val('1');
				document.getElementById("tampil_vendor").style.display = 'inline';
				document.getElementById("tampil_pt").style.display = 'none';
				$("#no_polisi").val('');
				$("#supir").val('');
			} else {
				$("#jenis_sj").val('0');
				document.getElementById("tampil_vendor").style.display = 'none';
				document.getElementById("tampil_pt").style.display = 'inline';
				$("#id_mobil").val('');
				$("#no_polisi").val('');
				$("#id_supir").val('');
				$("#id_mobil").val('');
				//CekRate();
			}
		}	
		
		function checkvalue() {
			var id_cust = document.getElementById('id_cust').value; 
			var id_supir = document.getElementById('id_supir').value; 
			var id_mobil = document.getElementById('id_mobil').value; 
			var jenis_sj = document.getElementById('jenis_sj').value;	
			var no_polisi = document.getElementById('no_polisi').value;
			var supir = document.getElementById('supir').value;
			
			if(id_cust == '') {
				alert ('Customer harus diisi..');				
				return false;	
			}else if(id_mobil == '' && jenis_sj != '1'  || id_mobil == '0' && jenis_sj != '1' ) {
				alert ('No. Polisi harus diisi..');				
				return false;
			}else if(id_supir == '' && jenis_sj != '1' || id_supir == '0' && jenis_sj != '1' ) {
				alert ('Supir harus diisi..');				
				return false;			
			}else if(no_polisi == '' && jenis_sj == '1' ) {
				alert ('No Polisi harus diisi..');				
				return false;	
			}else if(supir == '' && jenis_sj == '1' ) {
				alert ('Supir harus diisi..');				
				return false;		
			}else{
				return true;
			}	
		}
		function TampilPart(){
			var cari = $("#cari_part").val();
			var filter = $("#filter").val();
			var id_cust = $("#id_cust").val();
			cari='';
			$.get("ajax/ware_crud.php", {cari:cari, filter:filter, id_cust:id_cust, type:"ListPart_In" }, function (data, status) {
				$(".tampil_part").html(data);
			});
			$('#DataPart').modal('show');
		}
		function ListPart() {
			var cari = $("#cari_part").val();
			var filter = $("#filter").val();
			var id_cust = $("#id_cust").val();
			$.get("ajax/ware_crud.php", {cari:cari, filter:filter, id_cust:id_cust, type:"ListPart_In" }, function (data, status) {
				$(".tampil_part").html(data);
			});
		}
		function PilihPart(id) {
			$("#id_detil_masuk").val(id);
			$.post("ajax/ware_crud.php", {
					id: id, type:"Detil_Data_In"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_ware").val(data.id_ware);
					$("#nama").val(data.nama);
					$("#kode").val(data.kode);
					$("#unit").val(data.unit);
					$("#no_cont").val(data.no_cont);
					var sisa = data.masuk - data.est_keluar;
					$("#qty_stok").val(sisa);
					$("#qty").val(sisa_qty);
				}
			);
			$("#DataPart").modal("hide");
		}
		function TampilData(){
			document.getElementById("tampil_ware").style.display = 'inline';
			$("#modex").val('Add');
			$("#qty").val('');
			$("#no_cont").val('');
			$("#vol").val('');
			$("#qty_stok").val('');
			$("#vol_stok").val('');
			$("#id_ware").val('');
			$("#id_detil_masuk").val('');
			$("#nama").val('');
			$("#kode").val('');
			$("#rem").val('');
			$('#Data').modal('show');
		}
		function AddData() {
			var qty = $("#qty").val();
			var qty_stok = $("#qty_stok").val();
			qty=Number(qty);				
			qty_stok=Number(qty_stok);	
			if(!$("#id_ware").val()){
				alert("Item Number harus diisi !..");
			}else if(qty <= 0){
				alert("Qty harus diisi !..");
			}else if(qty > qty_stok){
				alert("Qty Keluar tidak boleh melebihi Qty Stok  !..");
		
			}	
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id_data = $("#id_data").val();			
					var id_detil_masuk = $("#id_detil_masuk").val();
					var id_ware = $("#id_ware").val();
					var qty = $("#qty").val();
					var qty_lama = $("#qty_lama").val();
					var rem = $("#rem").val();
					var mode = $("#modex").val();
					var id = $("#id").val();
					//alert(id_detil_masuk);
					$.post("ajax/ware_crud.php", {
						id_data:id_data,
						id:id,
						id_detil_masuk:id_detil_masuk,
						id_ware:id_ware,
						qty:qty,
						qty_lama:qty_lama,
						rem:rem,
						mode:mode,
						type : "Add_Out"
						}, function (data, status) {
						//alert(data);
						$("#Data").modal("hide");				
						ReadData();
					});
				}
			}	
		}
		function GetData(id) {
			$("#id").val(id);	
			document.getElementById("tampil_ware").style.display = 'none';
			$.post("ajax/ware_crud.php", {
					id: id, type:"Detil_Data_Out"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					
					var qty_sisa = Number(data.est) - Number(data.keluar) ;
					qty_sisa=Number(data.msk) -  Number(qty_sisa);
					$("#id_ware").val(data.id_ware);
					$("#id_detil_masuk").val(data.id_detil_masuk);
					$("#kode").val(data.kode);
					$("#nama").val(data.nama);
					$("#id_masuk").val(data.id_masuk);
					$("#no_cont").val(data.no_cont);
					$("#qty").val(data.keluar);
					$("#qty_lama").val(data.keluar);
					$("#qty_stok").val(qty_sisa);
					$("#rem").val(data.rem);
					$("#modex").val('Edit');
				}
			);
			$("#Data").modal("show");
		}
		function DelData(id) {
			//alert(id);
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/ware_crud.php", {
						id: id, type:"Del_Data_Out"
					},
					function (data, status) {
						 ReadData();
					}
				);
			}
		}



	// ============ SAP PROJECT ============
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Delivery Outbound</b></font></h1></li>					
			</ol>
			<br>
			<?php if($cat != '') {?>
			<div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
				<i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo "$cat"; ?></font>
			</div>
			<?php }?>
			
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:275px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
					text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Data SJ</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>#No SJ :</b></span>
						<input type="text"  id ="no_sj" name="no_sj" value="<?php echo $no_sj; ?>" 
						style="text-align: center;width:20%" readonly <?php echo $dis;?> >						
						<input type="hidden"  id ="id_data" name="id_data" value="<?php echo $id_data; ?>" >	
						<input type="hidden"  id ="mode" name="mode" value="<?php echo $mode; ?>" >
						
						&nbsp;&nbsp;
						<input type="checkbox" <?php echo $dis;?> id="jenis" style="margin-bottom:0px;" value="1"  onclick='CekStatus(this);' <?php echo $ceklist;?> > &nbsp;<b>Vendor</b>
						<input type="hidden" id="jenis_sj" name="jenis_sj"  value="<?php echo $jenis_sj;?>" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>No Ref :</b></span>
						<input type="text"  id ="no_ref" name="no_ref" value="<?php echo $no_ref; ?>" 
						style="text-align: center;width:20%" readonly <?php echo $dis;?> >	
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Tanggal :</b></span>
						<input type="text"  id ="tgl_sj" name="tgl_sj" value="<?php echo $tgl_sj; ?>" 
						style="text-align: center;width:20%" readonly <?php echo $dis;?>  >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Customer :</b></span>
						<input type="hidden" name="id_cust" id="id_cust" value="<?php echo $id_cust; ?>" >
						<input type="text" name="nama_cust" id="nama_cust" style="text-transform: uppercase;text-align: left;width:70%;" value="<?php echo $nama_cust; ?>"  readonly>
							
						<button class="btn btn-block btn-primary" id="po" style="padding:6px 12px 6px 12px; ;margin-top:-3px;border-radius:2px;margin-left:5px" type="button" onClick="javascript:TampilCust()">
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Project :</b></span>
						<input type="hidden" name="rowid" id="rowid" value="<?php echo $rowid; ?>" >
						<input type="text" name="sap_project" id="sap_project" style="text-transform: uppercase;text-align: left;width:70%;" value="<?php echo $kode_project; ?>"  readonly>
							
						<button class="btn btn-block btn-primary" id="po" style="padding:6px 12px 6px 12px; ;margin-top:-3px;border-radius:2px;margin-left:5px" type="button" onClick="javascript:TampilSAP()">
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>No. DO :</b></span>
						<input type="text" id="no_do" name="no_do" value="<?php echo $no_do;?>" 
						style="text-transform: uppercase;text-align: left;width:79.5%;" <?php echo $dis;?> >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Gudang Penerima :</b></span>
						<input type="text" id="gudang" name="gudang" value="<?php echo $gudang;?>" 
						style="text-transform: uppercase;text-align: left;width:79.5%;" <?php echo $dis;?> >
					</div>	
					<br>	
				</div>
            </div>
			
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:275px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Description</b>
					</div>
					<br>	
					<div  id="tampil_pt" style="display:inline;">
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>No. Polisi :</b></span>
						<select id="id_mobil" name="id_mobil" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
							<?php
							$t1="SELECT * from m_mobil_tr where status = '1' order by no_polisi  ";
							$h1=mysqli_query($koneksi, $t1);       
							while ($d1=mysqli_fetch_array($h1)){?>
								<option value="<?php echo $d1['id_mobil'];?>" ><?php echo $d1['no_polisi'];?></option>
							<?php }?>
							<option value="<?php echo $id_mobil;?>" selected><?php echo $no_polisi;?></option>
						</select>
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>Supir :</b></span>
						<select id="id_supir" name="id_supir"  <?php echo $dis;?> style="width: 80%;padding:4px">
							<?php
							$t1="SELECT * from m_supir_tr where status = '1' order by nama_supir  ";
							$h1=mysqli_query($koneksi, $t1);       
							while ($d1=mysqli_fetch_array($h1)){?>
							<option value="<?php echo $d1['id_supir'];?>" ><?php echo $d1['nama_supir'];?></option>
							<?php }?>
							<option value="<?php echo $id_supir;?>" selected><?php echo $nama_supir;?></option>
						</select>
					</div>
					</div>
					<div  id="tampil_vendor" style="display:none;">
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;min-width:160px"><b>No. Polisi :</b></span>
							<input type="text"  id ="no_polisi" name="no_polisi" value="<?php echo $no_polisi; ?>" 
							style="text-transform: uppercase;text-align: left;width:80%"  <?php echo $dis;?>  >
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;min-width:160px"><b>Supir :</b></span>
							<input type="text"  id ="supir" name="supir" value="<?php echo $supir; ?>" 
							style="text-transform: uppercase;text-align: left;width:80%"  <?php echo $dis;?>  >
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;min-width:160px"><b>No. Telp :</b></span>
							<input type="text"  id ="telp" name="telp" value="<?php echo $telp; ?>" 
							style="text-transform: uppercase;text-align: left;width:80%"  <?php echo $dis;?>  >
						</div>
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>SJ Vendor :</b></span>
						<input type="text"  id ="sj_vendor" name="sj_vendor" value="<?php echo $sj_vendor; ?>" 
						style="text-transform: uppercase;text-align: left;width:80%"  <?php echo $dis;?>  >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>Remarks :</b></span>
						<textarea name="ket" id="ket"
						style="resize:none;width: 80%; height: 80px; font-size: 11px; line-height: 12px; 
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
				$link = "ware_out.php?id=$xy1";
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
						onClick="window.open('cetak_sj_ware.php?id=<?php echo $idx;?>','blank')" >
						<span class="fa fa-print "></span>
						<b>Print SJ</b>
					</button>	
					<button class="btn btn-block btn-warning" 
						style="margin:0px;margin-bottom:0px;margin-left:1px;border-radius:2px;" type="button" 
						onClick="window.open('cetak_inv_ware.php?id=<?php echo $idx;?>','blank')" >
						<span class="fa fa-print "></span>
						<b>Print SO</b>
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Barang</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Item Number :</b></span>
								<input type="text"  id ="kode"  style="text-align: left;width:70%" readonly  >
								<div  id="tampil_ware" style="display:none;">
								<button class="btn btn-block btn-primary" id="cost"
									style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-5px" type="button" 
									onClick="javascript:TampilPart()">
									<span class="glyphicon glyphicon-search"></span>
								</button>	
								</div>
								<input type="hidden" id="id_detil_masuk"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id_ware"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="modex"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Item Description :</b></span>
								<input type="text"  id ="nama"  style="text-align: left;width:70%" readonly  >
								
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No Container :</b></span>
								<input type="text"  id ="no_cont"  style="text-align: left;width:70%" readonly  >
								
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Qty Stock :</b></span>
								<input type="text" id="qty_stok" value="" style="text-align: center;width:12%;border:1px solid rgb(169, 169, 169)"
								onkeypress="return isNumber(event)" readonly	/>
							</div>		
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Qty Keluar :</b></span>
								<input type="text" id="qty" value="" style="text-align: center;width:12%;border:1px solid rgb(169, 169, 169)"
								onkeypress="return isNumber(event)" 	/>
								<input type="hidden" id="qty_lama"  value="" style="text-align: center;width:15%;border:1px solid rgb(169, 169, 169)" 
								onkeypress="return isNumber(event)" />
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Remark :</b></span>
								<input type="text" id="rem"  value="" style="text-align: left;width:70%;border:1px solid rgb(169, 169, 169)" />	
							</div>				
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success" onclick="AddData()">&nbsp;&nbsp;Save&nbsp;&nbsp;</button>	
								<button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>	
							</div>
							<br>
						</div>
					</div>			
				</div>
			
			</div>
		</div>	
    </div>
	
	<div class="modal fade" id="DataPart"  role="dialog" aria-labelledby="myModalLabel">
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
									<select size="1" id="filter"  onchange="ListPart()" name="field1" style="padding:3px;margin-right:2px;width: 125px">
										<option>Item Number</option>
										<option>Item Description</option>
										<option>No Container</option>
									</select>
									<input type="text"  id ="cari_part" name="cari_part" value="<?php echo $cari_part; ?>" 
									style="text-align: left;width:200px;margin-left:-1px" onkeypress="ListPart()" >
									<button class="btn btn-block btn-primary" 
									style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:0px;padding:5px" type="button" 
									onClick="javascript:ListPart()" ">
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
								<div class="tampil_part"></div>
							</div>
							<br>
						</div>
					</div>								
				</div>
			</div>
		</div>	
    </div>

	<!-- ============ MODAL SAP ============ -->
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
