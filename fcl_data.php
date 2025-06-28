<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
include "lib.php";

if(!isset($_SESSION['id_user'])  ){
 header('location:logout.php'); 
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{		
	$mode = $_POST['mode'];
	$id_jo = $_POST['id_jo'];	
	$tgl_jo = $_POST['tgl_jo'];	
	$id_cust = $_POST['id_cust'];
	$no_po = trim(addslashes(strtoupper($_POST['no_po'])));
	$ket = addslashes(trim($_POST['ket']));
	$tgl_jox = ConverTglSql($tgl_jo);
	
	if($mode == 'Add' )
	{
		$ptgl = explode("-", $tgl_jo);
		$tg = $ptgl[0];
		$bl = $ptgl[1];
		$th = $ptgl[2];	
		$query = "SELECT max(right(no_jo,5)) as maxID FROM t_jo_tr where  year(tgl_jo) = '$th' and tipe = 'FCL' ";
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
		$no_jo = "FCL-$year$noUrut";
		
		$sql = "INSERT INTO  t_jo_tr (tipe, no_jo, tgl_jo, id_cust, no_po, ket, created) values
				('FCL', '$no_jo', '$tgl_jox', '$id_cust', '$no_po', '$ket', '$id_user')";
			$hasil= mysqli_query($koneksi, $sql);
		
		$sql = mysqli_query($koneksi, "select max(id_jo)as id from t_jo_tr ");			
		$row = mysqli_fetch_array($sql);
		$id_jo = $row['id'];
		
	}else{
		
		$sql = "update t_jo_tr set 
					tgl_jo = '$tgl_jox',
					id_cust = '$id_cust',
					no_po = '$no_po',
					ket = '$ket'
					where id_jo = '$id_jo'	";
			$hasil=mysqli_query($koneksi,$sql);
	
		
	}
	
	$cat ="Data saved...";
	$xy1="Edit|$id_jo|$cat";
	$xy1=base64_encode($xy1);
	header("Location: fcl_data.php?id=$xy1");
}
else
{	
	$idx = $_GET['id'];	
	$x=base64_decode($idx);
	$pecah = explode("|", $x);
	$mode= $pecah[0];
	$id_jo = $pecah[1];
	$cat = $pecah[2];
}

if($mode == 'Add')
{
	$no_jo = '-- Auto -- ';
	$tgl_jo = date('d-m-Y');
	
	
}else{
	
	$pq = mysqli_query($koneksi, "select t_jo_tr.*, m_cust_tr.nama_cust
		  from 
		  t_jo_tr left join m_cust_tr on t_jo_tr.id_cust = m_cust_tr.id_cust
		  where t_jo_tr.id_jo = '$id_jo'  ");
	$rq=mysqli_fetch_array($pq);	
	$no_jo = $rq['no_jo'];
	$tgl_jo = ConverTgl($rq['tgl_jo']);
	$id_cust = $rq['id_cust'];
	$nama_cust = $rq['nama_cust'];
	$no_po = $rq['no_po'];
	$ket = $rq['ket'];
	$disx = 'Disabled';
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
			$("#tgl_jo").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			ReadData();
		});	
		function ReadData() {	
			var id_jo = $("#id_jo").val();
			var mode = $("#mode").val();
			$.get("ajax/fcl_crud.php", {mode:mode,id_jo:id_jo, type:"Read_Detil" }, function (data, status) {
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
				}
			);
			$("#DaftarCust").modal("hide");
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
		function CekStatus(cb) {
			var checkBox = document.getElementById("jenis_po");
			$("#id_jo_cont").val('');
			$("#no_cont").val('');
			if (checkBox.checked == true){
				$("#jenisx").val('1');
				document.getElementById("tampil_jo").style.display = 'inline';
				document.getElementById('no_cont').readOnly = true;
			} else {
				$("#jenisx").val('0');
				document.getElementById("tampil_jo").style.display = 'none';
				document.getElementById('no_cont').readOnly = false;
			
			}
		}	
		function TampilJO(){	
			var cari = $("#cari_data").val();
			$.get("ajax/fcl_crud.php", {cari:cari,  type:"ListCont_PTL" }, function (data, status) {
				$(".tampil_jo").html(data);
			});
			$('#DataJO').modal('show');
		}
		function ListJO() {	
			var cari = $("#cari_data").val();
			$.get("ajax/fcl_crud.php", {cari:cari, type:"ListCont_PTL" }, function (data, status) {
				$(".tampil_jo").html(data);
			});
		}
		function PilihJO(id) {	
			$.post("ajax/fcl_crud.php", {
					id: id, type:"Detil_Cont_PTL"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_jo_cont").val(data.id_cont);
					$("#no_cont").val(data.no_cont);
				}
			);
			$("#DataJO").modal("hide");
		}
		function TampilData() 
		{
			$("#id_jo_cont").val('');
			$("#no_cont").val('');
			$("#jenisx").val('0');
			document.getElementById("tampil_jo").style.display = 'none';
			document.getElementById('no_cont').readOnly = false;
			document.getElementById("jenis_po").checked = false;
			$("#id_asal").val('4');
			$("#penerima").val('');
			$("#biaya_kirim").val('');
			$("#modex").val('Add');
			CekRate();
			$('#Data').modal('show');
		}
		function CekRate()
		{
			var id_asal = $("#id_asal").val();
			var id_tujuan = $("#id_tujuan").val();
			var jenis_mobil = $("#jenis").val();
			var id_cust = $("#id_cust").val();
			//alert(id_cust);
			$("#biaya_kirim").val('0');	
			$.post("ajax/fcl_crud.php", {
				id_cust:id_cust, id_asal: id_asal, id_tujuan:id_tujuan, jenis_mobil:jenis_mobil, type:"Cek_Rate_Cust"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					//alert(data.status);
					if(data.status == 200)
					{
						CekRate_Umum();
					}
					else
					{
						$("#biaya_kirim").val(Rupiah(data.rate));
					}
					
					
				}
			);
		}
		function CekRate_Umum()
		{
			//alert('ddd');
			var id_asal = $("#id_asal").val();
			var id_tujuan = $("#id_tujuan").val();
			var jenis_mobil = $("#jenis").val();
			
			$("#biaya_kirim").val('');	
			$.post("ajax/fcl_crud.php", {
				id_asal: id_asal, id_tujuan:id_tujuan, jenis_mobil:jenis_mobil, type:"Cek_Rate"
				},
				function (data, status) {
					var data = JSON.parse(data);					
					$("#biaya_kirim").val(Rupiah(data.rate));
					
				}
			);
		}
		function AddData() {
			var id = $("#id").val();
			var id_jo = $("#id_jo").val();
			var id_jo_cont = $("#id_jo_cont").val();
			var jenisx = $("#jenisx").val();
			var id_asal = $("#id_asal").val();
			var id_tujuan = $("#id_tujuan").val();
			var jenis = $("#jenis").val();
			var penerima = $("#penerima").val();
			var biaya_kirim = $("#biaya_kirim").val();
			var no_cont = $("#no_cont").val();
			var mode = $("#modex").val();
			//alert(id_tujuan);
			
			if(jenisx == '1' && id_jo_cont == '')
			{
				alert ("No Container harus diisi !..");				
			}
			else
			{
				$.post("ajax/fcl_crud.php", {
				id:id,
				id_jo:id_jo,
				id_jo_cont:id_jo_cont,
				no_cont:no_cont,
				id_asal:id_asal,
				id_tujuan:id_tujuan,
				jenis:jenis,
				penerima:penerima,
				biaya_kirim:biaya_kirim,
				mode:mode,
				type : "Add_Detil"
				}, function (data, status) {
					alert(data);
					$("#Data").modal("hide");				
					ReadData();
				});
			}
			
		}	
		function GetData(id) {
			$("#id").val(id);	
			$.post("ajax/fcl_crud.php", {
					id: id, type:"Detil_Data"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_asal").val(data.id_asal);
					$("#id_tujuan").val(data.id_tujuan);
					$("#jenis").val(data.jenis_mobil);
					$("#penerima").val(data.penerima);
					$("#biaya_kirim").val(Rupiah(data.biaya_kirim));
					$("#modex").val('Edit');							
				}
			);
			$("#Data").modal("show");
		}
		function DelDetil(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/fcl_crud.php", {
						id: id, type:"Del_Detil"
					},
					function (data, status) {
						 ReadData();
					}
				);
			}
		}
		function List_Lain(id) {
			$("#id_detil").val(id);
			var mode = $("#mode").val();
			$.get("ajax/fcl_crud.php", {mode:mode, id:id,  type:"List_Lain" }, function (data, status) {
				$(".tampil_lain").html(data);
				});
			$("#DaftarLain").modal("show");
		}
		function TampilLain() 
		{
			$("#biaya_lain").val('');
			$("#mode_biaya").val('Add');
			$('#DataLain').modal('show');
		}
		function AddLain() {
			var id_detil = $("#id_detil").val();
			var id = $("#id_biaya").val();
			var id_cost = $("#id_cost").val();
			var biaya = $("#biaya_lain").val();
			var mode = $("#mode_biaya").val();
			$.post("ajax/fcl_crud.php", {
				id_detil:id_detil,
				id:id,
				id_cost:id_cost,
				biaya:biaya,
				mode:mode,
				type : "Add_Lain"
				}, function (data, status) {
				alert(data);
				
				var id = $("#id_detil").val();
				var mode = $("#mode").val();
				$.get("ajax/fcl_crud.php", {mode:mode, id:id,  type:"List_Lain" }, function (data, status) {
					$(".tampil_lain").html(data);
				});
				ReadData();
				$("#DataLain").modal("hide");				
				
			});
		}	
		function GetLain(id) {
			$("#id_biaya").val(id);	
			$.post("ajax/fcl_crud.php", {
					id: id, type:"Detil_Lain"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_cost").val(data.id_cost);
					$("#biaya_lain").val(Rupiah(data.biaya));
					$("#mode_biaya").val('Edit');							
				}
			);
			$("#DataLain").modal("show");
		}
		function DelLain(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/fcl_crud.php", {
						id: id, type:"Del_Lain"
					},
					function (data, status) {
						var id = $("#id_detil").val();
						var mode = $("#mode").val();
						$.get("ajax/fcl_crud.php", {mode:mode, id:id,  type:"List_Lain" }, function (data, status) {
							$(".tampil_lain").html(data);
						});
				
						 ReadData();
					}
				);
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
		
		<form method="post" name ="myform"  class="form-horizontal" onsubmit="return checkvalue(this)" > 
		<div class="content-wrapper" style="min-height:750px">
			<br>
			<ol class="breadcrumb">
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Order</b></font></h1></li>					
			</ol>
			<br>
			<?php if($cat != '') {?>
			<div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
				<i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo "$cat"; ?></font>
			</div>
			<?php }?>
			
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;height:185px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
					text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Data Order</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>#No Order :</b></span>
						<input type="text"  id ="no_jo" name="no_jo" value="<?php echo $no_jo; ?>" 
						style="text-align: center;width:16%" readonly <?php echo $dis;?> >						
						<input type="hidden"  id ="id_jo" name="id_jo" value="<?php echo $id_jo; ?>" >	
						<input type="hidden"  id ="mode" name="mode" value="<?php echo $mode; ?>" >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Tanggal :</b></span>
						<input type="text"  id ="tgl_jo" name="tgl_jo" value="<?php echo $tgl_jo; ?>" 
						style="text-align: center;width:16%" readonly <?php echo $dis;?>  >
					</div>				
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Customer :</b></span>
						<input type="text"  id ="nama_cust" name="nama_cust" value="<?php echo $nama_cust;?>" style="text-align: left;width:70.5%" readonly  >
						<button class="btn btn-block btn-primary" id="btn_custx"
							style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-1px" type="button" 
							onClick="javascript:TampilCust()" <?php echo $disx;?> >
							<span class="glyphicon glyphicon-search"></span>
						</button>
						<input type="hidden" id="id_cust"  name="id_cust" value="<?php echo $id_cust;?>" 
						style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />		
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. PO :</b></span>
						<input type="text"  id ="no_po" name= "no_po" value="<?php echo $no_po;?>" style="text-transform: uppercase;text-align: left;width:75%;padding:4px" <?php echo $dis;?> >
					</div>
					<br>	
				</div>
            </div>
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;height:185px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
					text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Remarks</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						
						<textarea name="ket" id="ket"
						style="margin-left:10px;resize:none;width: 97%; height: 110px; font-size: 11px; line-height: 12px; 
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
								<b>Add Data</b>
							</button>
						<?php }?>
						<div class="table-responsive mailbox-messages" style="min-height:10px">									
							<div class="tampil_data"></div>
						</div>	
					</div>
				</div>
			<?php }?>	
			<?php
				$link = "fcl.php?id=$xy1";
				$xy1="$id_jo";
				$idx=base64_encode($xy1);
			?>
			<div class="col-md-12" >
				<div style="width:98%;background:none;margin-left:0;margin-top:0px;border-top:0px;border-bottom:0px" class="input-group">
					<?php if($mode != 'View'){?>
				<button type="submit" class="btn btn-success"><span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save</b>&nbsp;&nbsp;</button>	
				<?php }?>
				<button type="button" class="btn btn-danger" onclick="window.location.href='<?php echo $link; ?>'"><span class="fa fa-backward"></span>&nbsp;&nbsp;<b>Back</b></button>	
				
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Order</b>
							</div>	
							<br>
						
							
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Asal :</b></span>
								<select id="id_asal" name="id_asal" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_kota_tr where status = '1' order by nama_kota  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_kota'];?>" ><?php echo $d1['nama_kota'];?></option>
									<?php }?>
								</select>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tujuan :</b></span>
								<select id="id_tujuan" name="id_tujuan" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_kota_tr where status = '1' order by nama_kota  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_kota'];?>" ><?php echo $d1['nama_kota'];?></option>
									<?php }?>
								</select>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Jenis :</b></span>
								<select id="jenis" name="jenis" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_jenis_mobil_tr where status = '1' order by nama   ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['nama'];?>" ><?php echo $d1['nama'];?></option>
									<?php }?>
								</select>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Container :</b></span>
								<input type="text"  id ="no_cont" style="text-transform: uppercase;text-align: left;width:80%"   >
								<div  id="tampil_jo" style="display:none;">
								<button class="btn btn-block btn-primary" id="btn_jox"
										style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-3px" type="button" 
										onClick="javascript:TampilJO()">
										<span class="glyphicon glyphicon-search"></span>
									</button>
								</div>	
								<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="modex"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id_jo_cont"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								&nbsp;
								<input type="hidden"  id="jenis_po" style="margin-bottom:0px;" value="1"  onclick='CekStatus(this);'> 
								<input type="hidden" id="jenisx"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id_sj"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id_jo_cont"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Penerima :</b></span>
								<input type="text" id="penerima"  value="" style="text-transform: uppercase;text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />	
							</div>		
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Biaya Kirim :</b></span>
								<input type="text" id="biaya_kirim" style="text-align: right;width:20%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  readOnly>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddData()">
								<span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save</b>&nbsp;&nbsp;</button>	
								<button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-left:1px">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Cancel</b></button>	
							</div>
							<br>
						</div>
					</div>			
				</div>
			
			</div>
		</div>	
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
	
	<div class="modal fade" id="DaftarLain"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document" style="width:750px;">
			<div class="modal-content" style="background: none">
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Biaya Lainnya</b>
							</div>	
							<div  class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:50%;text-align:left;padding:0px;background: none;">									
									<input type="hidden"  id ="id_detil" name="id" value=""   >
								</span>								
							</div>	
							<?php if($mode == 'Edit'){?>
							<button class="btn btn-block btn-success" 
								style="margin-left:1px; margin-bottom:2px" type="button" 
								onClick="javascript:TampilLain()"   >
								<span class="fa  fa-plus-square"></span>
								<b>Add Data</b>
							</button>
							<?php }?>
							<button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-left:-1px; margin-bottom:2px">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Close</b></button>	
							<div class="table-responsive mailbox-messages" >									
								<div class="tampil_lain"></div>
							</div>
							
						</div>
					</div>		
				</div>	
			</div>
		</div>	
    </div>
	
	<div class="modal fade" id="DataLain"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Biaya Lainnya</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Nama Biaya :</b></span>
								<select id="id_cost" style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_cost_tr where status = '1' and id_cost <> '1' order by nama_cost  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_cost'];?>" ><?php echo $d1['nama_cost'];?></option>
									<?php }?>
								</select>	
								<input type="hidden" id="id_biaya"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="mode_biaya"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Biaya :</b></span>
								<input type="text" id="biaya_lain" style="text-align: right;width:20%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddLain()">
								<span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save</b>&nbsp;&nbsp;</button>	
								<button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-left:1px">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Cancel</b></button>	
							</div>
							<br>
						</div>
					</div>			
				</div>
			
			</div>
		</div>	
    </div>
	
	<div class="modal fade" id="DataJO"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
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
									&nbsp;&nbsp;&nbsp;<b>Find  : </b>
									<input type="text"  id ="cari_data" name="cari_data" value="<?php echo $cari_data; ?>" 
									style="text-align: left;width:200px" onkeypress="ListJO()"  >
									<button class="btn btn-block btn-primary" 
									style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:0px;pading:0px;padding:5px" type="button" 
									onClick="javascript:ListJO()" ">
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
								<div class="tampil_jo"></div>
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
