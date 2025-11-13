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
		$id_sewa 	= $_POST['id_sewa'];	
		$id_biaya 	= $_POST['id_biaya'];	
		$id_cust 	= $_POST['id_cust'];
		$id_quo 	= $_POST['id_quo'];
		$ket 		= addslashes(trim($_POST['ket']));
		$tgl_sjx 	= ConverTglSql($tgl_sj);
		$tanggal 	= $_POST['tanggal'];	
		$tanggalx 	= ConverTglSql($tanggal);

		$rowid 		= $_POST['rowid'];	
		
		$uj 		= str_replace(",","", $uj);
		$ritase 	= str_replace(",","", $ritase);
		
		if($mode == 'Add' ){
			$ptgl 	= explode("-", $tanggal);
			$tg 	= $ptgl[0];
			$bl 	= $ptgl[1];
			$th 	= $ptgl[2];	
			$query 	= "SELECT max(right(no_sewa,5)) AS maxID FROM t_ware_sewa WHERE year(tanggal) = '$th'";
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
			$year = substr($thn,2,2);
			$no_sewa = "SO-$year$noUrut";
			
			// ======== INSERT RENT ========
			$q_insert_rent = "
				INSERT INTO t_ware_sewa 
					(sap_rowid, no_sewa, id_quo, id_cust, ket, created, tanggal, id_cost) 
				VALUES 
					('$rowid', '$no_sewa', '$id_quo', '$id_cust', '$ket', '$id_user', '$tanggalx', '$id_biaya')
			";
			$hasil = mysqli_query($koneksi, $q_insert_rent);
			$id_sewa = mysqli_insert_id($koneksi);

			$q_ware = "SELECT 
					CONCAT(
						DATE_FORMAT(t_ware_data.tanggal, '%d'), '.', 
						DATE_FORMAT(t_ware_data.tanggal, '%m'), '.', 
						t_ware_data_detil.no_cont
					) AS batch,
					t_ware_data.tanggal,
					t_ware.kode,
					t_ware.nama, 
					t_ware_data_detil.masuk - t_ware_data_detil.keluar AS qty,
					(t_ware_data_detil.masuk - t_ware_data_detil.keluar) * t_ware.vol AS cbm,
					t_ware_quo.max_cbm,
					t_ware_quo.harga_sewa
				FROM t_ware
				INNER JOIN t_ware_data_detil ON t_ware_data_detil.id_ware = t_ware.id_ware
				INNER JOIN t_ware_data ON t_ware_data.id_data = t_ware_data_detil.id_data
				INNER JOIN t_ware_quo ON t_ware_quo.id_quo = t_ware.id_quo
				WHERE t_ware.id_quo = '$id_quo'
				AND t_ware_data_detil.no_cont != ''";

			// echo $q_ware;
			// exit;
			$r_ware = mysqli_query($koneksi, $q_ware);

			while ($row = mysqli_fetch_assoc($r_ware)) {
				$kode  		= $row['kode'];
				$nama  		= $row['nama'];
				$qty   		= $row['qty'];
				$cbm   		= round($row['cbm'],2);
				$batch 		= $row['batch'];
				$tanggal 	= $row['tanggal'];
				$max_cbm 	= $row['max_cbm'];
				$harga_sewa = $row['harga_sewa'];

				$q_insert_sewa_detail = "INSERT INTO t_ware_sewa_detail 
						(id_sewa, tanggal, batch, itemcode, itemname, qty, cbm, max_cbm, harga_sewa)
					VALUES 
						('$id_sewa', '$tanggal', '$batch', '$kode', '$nama', '$qty', '$cbm','$max_cbm','$harga_sewa')
				";
				mysqli_query($koneksi, $q_insert_sewa_detail);
			}
			
			if (!$hasil){
				$cat ="Data Sewa Customer untuk Periode tersebut sudah terdaftar...";
				$xy1="Add|$id_sewa|$cat";
				$xy1=base64_encode($xy1);
				header("Location: ware_sewa_data.php?id=$xy1");
			}else{
				$sql = mysqli_query($koneksi, "SELECT max(id_sewa)as id from t_ware_sewa ");			
				$row = mysqli_fetch_array($sql);
				$id_sewa = $row['id'];
			
				$cat = "Data saved...";
				$xy1 = "Edit|$id_sewa|$cat";
				$xy1=  base64_encode($xy1);
				header("Location: ware_sewa_data.php?id=$xy1");
			}
		}else{
			
			$sql = "UPDATE t_ware_sewa SET 
						tanggal = '$tanggalx',
						id_cost = '$id_biaya',
						ket = '$ket'
						WHERE id_sewa = '$id_sewa'	";
			$hasil=mysqli_query($koneksi,$sql);
			
			$cat ="Data saved...";
			$xy1="Edit|$id_sewa|$cat";
			$xy1=base64_encode($xy1);
			header("Location: ware_sewa_data.php?id=$xy1");
		}
		
		
	} else{	
		$idx = $_GET['id'];	
		$x=base64_decode($idx);
		$pecah = explode("|", $x);
		$mode= $pecah[0];
		$id_sewa = $pecah[1];
		$cat = $pecah[2];
	}

	if($mode == 'Add'){
		$no_sewa = '-- Auto -- ';
		$bln = date('m');
		$thn = date('Y');
		$tanggal = date('d-m-Y');
	}else{
		
		$pq = mysqli_query($koneksi, "SELECT 
				t_ware_sewa.*, 
				m_cust_tr.nama_cust, 
				t_ware_quo.quo_no, 
				m_cost_tr.nama_cost,
				sap_project.kode_project
			FROM t_ware_sewa 
			LEFT JOIN m_cust_tr ON t_ware_sewa.id_cust = m_cust_tr.id_cust
			LEFT JOIN t_ware_quo ON t_ware_sewa.id_quo = t_ware_quo.id_quo
			LEFT JOIN m_cost_tr ON t_ware_sewa.id_cost = m_cost_tr.id_cost
			LEFT JOIN sap_project ON sap_project.rowid = t_ware_sewa.sap_rowid
			where t_ware_sewa.id_sewa = '$id_sewa'  ");

		$rq			= mysqli_fetch_array($pq);	
		$no_sewa 	= $rq['no_sewa'];
		$tanggal 	= ConverTgl($rq['tanggal']);
		$id_biaya 	= $rq['id_cost'];
		$quo_no 	= $rq['quo_no'];
		$nama_biaya = $rq['nama_cost'];
		$id_quo 	= $rq['id_quo'];
		$tgl_sj 	= ConverTgl($rq['tanggal']);
		$id_cust 	= $rq['id_cust'];
		$nama_cust 	= $rq['nama_cust'];
		$ket 		= str_replace("\'","'",$rq['ket']);
		$disx 		= "Disabled";

		$rowid 		  = $rq['sap_rowid'];
		$kode_project = $rq['kode_project'];
	}

	if($mode == 'View'){
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
			var id_sewa = $("#id_sewa").val();
			var id_quo = $("#id_quo").val();
			var mode = $("#mode").val();
			$.get("ajax/ware_crud.php", {mode:mode,id_sewa:id_sewa, id_quo:id_quo, type:"Read_Sewa_Data" }, function (data, status) {
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
			$.get("ajax/cust_crud.php", {cari:cari,  type:"ListCust_Quo" }, function (data, status) {
				$(".tampil_cust").html(data);
				$("#hal").val(hal);
			});
		}
		function PilihCust(id_quo) {	
		
			$.post("ajax/cust_crud.php", {
					id_quo: id_quo, type:"DetilCust_Quo"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					$("#nama_cust").val(data.nama_cust);
					$("#id_cust").val(data.id_cust);
					$("#id_quo").val(id_quo);
					$("#quo_no").val(data.quo_no);
					//CekRate();
				}
			);
			$("#DaftarCust").modal("hide");
		}
		function checkvalue() {
			var id_cust = document.getElementById('id_cust').value; 
			var id_biaya = document.getElementById('id_biaya').value;
			if(id_cust == '') {
				alert ('Customer harus diisi..');				
				return false;	
			}else if(id_biaya == '') {
				alert ('Jenis Sewa harus diisi..');				
				return false;		
			}else{
				return true;
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
					<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Sewa</b></font></h1></li>					
				</ol>
				<br>
				<?php if($cat != '') {?>
				<div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
					<i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo "$cat"; ?></font>
				</div>
				<?php }?>
				
				<div class="col-md-6" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;height:250px">					
						<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
						text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-list"></i>&nbsp;Data </b>
						</div>
						<br>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>#No SO :</b></span>
							<input type="text"  id ="no_sewa" name="no_sewa" value="<?php echo $no_sewa; ?>" 
							style="text-align: center;width:23.5%" readonly <?php echo $dis;?> >						
							<input type="hidden"  id ="id_sewa" name="id_sewa" value="<?php echo $id_sewa; ?>" >
							<input type="hidden"  id ="mode" name="mode" value="<?php echo $mode; ?>" >
						
						</div>
						
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Tanggal :</b></span>
							<input type="text"  id ="tanggal" name="tanggal" value="<?php echo $tanggal; ?>" 
							style="text-align: center;width:23.5%" readonly <?php echo $dis;?>  >
						</div>	
						
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>No Quo :</b></span>
							<input type="text"  id ="quo_no" name="quo_no" value="<?php echo $quo_no; ?>" 
							style="text-align: center;width:23.5%" readonly <?php echo $dis;?> >		
							<input type="hidden"  id ="id_quo" name="id_quo" value="<?php echo $id_quo; ?>" >
							<button class="btn btn-block btn-primary"  <?php echo $disx;?>
								style="padding:6px;margin-top:-4px;border-radius:0px;margin-left:-1px" type="button" 
								onClick="javascript:TampilCust()">
								<span class="glyphicon glyphicon-search"></span>
							</button>	
						</div>					
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Customer :</b></span>
							<input type="text"  id ="nama_cust" name="nama_cust" value="<?php echo $nama_cust; ?>" 
							style="text-align: left;width:70%;font-weight:bold" readonly <?php echo $dis;?> >
							<input type="hidden"  id ="id_cust" name="id_cust" value="<?php echo $id_cust; ?>" >
							
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Jenis Sewa :</b></span>
							<select size="1" id="id_biaya"  name="id_biaya"  style="width:70%;padding:4px;margin-right:2px">
								<?php 
								$tampil1="SELECT * from m_cost_tr where status = '1'  order by nama_cost";
								$hasil1=mysqli_query($koneksi, $tampil1);       
								while ($data1=mysqli_fetch_array($hasil1)){  
								?>
								<option value="<?php echo $data1['id_cost']; ?>"><?php echo $data1['nama_cost'];?></option>
								<?php }?>
								<option value="<?php echo $id_biaya; ?>" selected><?php echo $nama_biaya; ?></option>
							</select>
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Project :</b></span>
							<input type="hidden" name="rowid" id="rowid" value="<?php echo $rowid; ?>" >
							<input type="text" name="sap_project" id="sap_project" style="text-transform: uppercase;text-align: left;width:70%;" value="<?php echo $kode_project; ?>"  readonly>
								
							<button class="btn btn-block btn-primary" id="po" style="padding:6px 12px 6px 12px; ;margin-top:-3px;border-radius:2px;margin-left:5px" type="button" onClick="javascript:TampilSAP()">
								<span class="glyphicon glyphicon-search"></span>
							</button>
						</div>
						<br>	
					</div>
				</div>
				
				<div class="col-md-6" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;height:250px">					
						<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-list"></i>&nbsp;Remark</b>
						</div>
						<br>	
						
						<div style="width:100%;" class="input-group">
						
							<textarea name="ket" id="ket"
							style="margin-left:10px;resize:none;width: 95%; height: 130px; font-size: 11px; line-height: 12px; 
							border: 1px solid #4; padding: 5px;" <?php echo $dis;?> ><?php echo $ket; ?></textarea>
						</div>
						<br>	
					</div>
				</div>
			
				<?php if($mode != 'Add'){?>	
					<div class="col-md-12" >
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:195px">
							<?php if($mode == 'Editx'){?>
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
					$link = "ware_sewa.php?id=$xy1";
					$xy1="$id_sewa";
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
							onClick="window.open('cetak_sewa_ware.php?id=<?php echo $idx;?>','blank')" >
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
	

	<!-- ========= MODAL CUST ========= -->
	<div class="modal fade" id="DaftarCust"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document" style="width:60%">
			<div class="modal-content" style="background: none">	
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Quotation Customer</b>
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
	
	<!-- ========= MODAL SAP PROJECT ========= -->
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
