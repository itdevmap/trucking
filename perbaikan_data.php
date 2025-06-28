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
	$id_spk = $_POST['id_spk'];	
	$tgl_spk = $_POST['tgl_spk'];	
	$jam_mulai = $_POST['jam_mulai'];
	$menit_mulai = $_POST['menit_mulai'];
	$jam_selesai = $_POST['jam_selesai'];
	$menit_selesai = $_POST['menit_selesai'];
	$no_spk = trim(addslashes(strtoupper($_POST['no_spk'])));
	$id_mobil = $_POST['id_mobil'];
	$jenis = $_POST['jenis'];
	$km = $_POST['km'];
	$ket = addslashes(trim($_POST['ket']));
	$tgl_spkx = ConverTglSql($tgl_spk);
	
	$km = str_replace(",","", $km);
	
	if($mode == 'Add' )
	{
		
		$ptgl = explode("-", $tgl_spk);
		$tg = $ptgl[0];
		$bl = $ptgl[1];
		$th = $ptgl[2];	
		$query = "SELECT max(right(no_spk,4)) as maxID FROM t_spk where  year(tanggal) = '$th'  ";
		$hasil = mysqli_query($koneksi, $query);    
		$data  = mysqli_fetch_array($hasil);
		$idMax = $data['maxID'];
		if ($idMax == '9999'){
			$idMax='0000';
		}
		$noUrut = (int) $idMax;   
		$noUrut++;  
		if(strlen($noUrut)=='1'){
			$noUrut="000$noUrut";
			}elseif(strlen($noUrut)=='2'){
			$noUrut="00$noUrut";
			}elseif(strlen($noUrut)=='3'){
			$noUrut="0$noUrut";
		}   
		$year = substr($th,2,2);
		$no_spk = "SPK$year$bl-$noUrut";
		
		$sql = "INSERT INTO  t_spk (no_spk, tanggal, jam_mulai, menit_mulai, jam_selesai, menit_selesai,
				id_mobil, km, jenis, ket, created) 
				values
				('$no_spk', '$tgl_spkx', '$jam_mulai', '$menit_mulai', '$jam_selesai', '$menit_selesai',
				'$id_mobil', '$km', '$jenis', '$ket', '$id_user')";
			$hasil= mysqli_query($koneksi, $sql);
		
		$sql = mysqli_query($koneksi, "select max(id_spk)as id from t_spk ");			
		$row = mysqli_fetch_array($sql);
		$id_spk = $row['id'];
		
	}else{
		
		$sql = "update t_spk set 
					tanggal = '$tgl_spkx',
					jam_mulai = '$jam_mulai',
					menit_mulai = '$menit_mulai',
					jam_selesai = '$jam_selesai',
					menit_selesai = '$menit_selesai',
					id_mobil = '$id_mobil',
					km = '$km',
					jenis = '$jenis',
					ket = '$ket'
					where id_spk = '$id_spk'	";
			$hasil=mysqli_query($koneksi,$sql);
	
		
	}
	
	$cat ="Data saved...";
	$xy1="Edit|$id_spk|$cat";
	$xy1=base64_encode($xy1);
	header("Location: perbaikan_data.php?id=$xy1");
}
else
{	
	$idx = $_GET['id'];	
	$x=base64_decode($idx);
	$pecah = explode("|", $x);
	$mode= $pecah[0];
	$id_spk = $pecah[1];
	$cat = $pecah[2];
}

if($mode == 'Add')
{
	$tgl_spk = date('d-m-Y');
	$no_spk = '-- Auto --';
	
}else{
	
	$pq = mysqli_query($koneksi, "select t_spk.*, m_mobil_tr.no_polisi
		  from 
		  t_spk left join m_mobil_tr on t_spk.id_mobil = m_mobil_tr.id_mobil
		  where t_spk.id_spk = '$id_spk'  ");
	$rq=mysqli_fetch_array($pq);	
	$no_spk = $rq['no_spk'];
	$tgl_spk = ConverTgl($rq['tanggal']);
	$id_mobil = $rq['id_mobil'];
	$no_polisi = $rq['no_polisi'];
	$jam_mulai = $rq['jam_mulai'];
	$menit_mulai = $rq['menit_mulai'];
	$jam_selesai = $rq['jam_selesai'];
	$menit_selesai = $rq['menit_selesai'];
	$km = number_format($rq['km'],0);
	$ket = str_replace("\'","'",$rq['ket']);
	$jenis = $rq['jenis'];
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
			var date_input=$('input[name="tgl_spk"]'); 
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
			ReadData();
		});	
		function ReadData() {	
			var id_spk = $("#id_spk").val();
			var mode = $("#mode").val();
			$.get("ajax/perbaikan_crud.php", {mode:mode,id_spk:id_spk, type:"Read_Data" }, function (data, status) {
				$(".tampil_data").html(data);
			});
		}
		function checkvalue() {
			var no_spk = document.getElementById('no_spk').value; 
			var id_mobil = document.getElementById('id_mobil').value; 
			var km = document.getElementById('km').value; 
			var id_mobil = document.getElementById('id_mobil').value; 
			var jenis = document.getElementById('jenis').value; 
			
			if(no_spk == '') {
				alert ('No. SPK harus diisi..');				
				return false;	
			}else if(id_mobil == '') {
				alert ('No Polisi harus diisi..');				
				return false;	
			}else if(km <= '') {
				alert ('Kilometer harus diisi..');				
				return false;
			}else if(jenis == '') {
				alert ('Jenis  Pekerjaan harus diisi..');				
				return false;	
			}else{
				return true;
			}	
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
		function TampilPart(){	
			var cari = $("#cari_part").val();
			cari='';
			$.get("ajax/part_crud.php", {cari:cari, type:"ListPart" }, function (data, status) {
				$(".tampil_part").html(data);
			});
			$('#DataPart').modal('show');
		}
		function ListPart() {		
			var cari = $("#cari_part").val();
			$.get("ajax/part_crud.php", {cari:cari, type:"ListPart" }, function (data, status) {
				$(".tampil_part").html(data);
			});
		}
		function PilihPart(id) {		
			$.post("ajax/part_crud.php", {
					id: id, type:"Detil_Data"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_part").val(data.id_part);
					$("#nama_part").val(data.nama);
					$("#unit_qty").val(data.unit);
					$("#unit_stok").val(data.unit);
					var sisa = data.masuk - data.keluar;
					$("#stok").val(sisa);
				}
			);
			$("#DataPart").modal("hide");
		}
		function TampilData(){	
			$("#modex").val('Add');
			$("#qty").val('');
			$("#stok").val('');
			$("#unit_qty").val('');
			$("#unit_stok").val('');
			$("#id_part").val('');
			$("#nama_part").val('');
			$('#Data').modal('show');
		}
		function AddData() {	
			var qty = $("#qty").val();
			var stok = $("#stok").val();		
			qty=Number(qty);	
			stok=Number(stok);	
			if(!$("#id_part").val()){
				alert("Item Spare Part harus diisi !..");
			}else if(qty <= 0){
				alert("Qty Penggunaan harus diisi !..");
			}else if(qty > stok){
				alert("Qty Penggunaan tidak boleh melebihi Stok  !..");
			}	
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id_spk = $("#id_spk").val();			
					var id_part = $("#id_part").val();
					var qty = $("#qty").val();
					$.post("ajax/perbaikan_crud.php", {
						id_spk:id_spk,
						id_part:id_part,
						qty:qty,
						type : "Add_Data"
						}, function (data, status) {
						//alert(data);
						$("#Data").modal("hide");				
						ReadData();
					});
				}
			}	
		}
		function DelData(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/perbaikan_crud.php", {
						id: id, type:"Del_Data"
					},
					function (data, status) {
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Perbaikan Mobil</b></font></h1></li>					
			</ol>
			<br>
			<?php if($cat != '') {?>
			<div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
				<i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo "$cat"; ?></font>
			</div>
			<?php }?>
			
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;height:205px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
					text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Data SPK</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>#No SPK :</b></span>
						<input type="text"  id ="no_spk" name="no_spk" value="<?php echo $no_spk; ?>" 
						style="text-align: center;width:18.5%"  readonly >						
						<input type="hidden"  id ="id_spk" name="id_spk" value="<?php echo $id_spk; ?>" >	
						<input type="hidden"  id ="mode" name="mode" value="<?php echo $mode; ?>" >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Tanggal SPK :</b></span>
						<input type="text"  id ="tgl_spk" name="tgl_spk" value="<?php echo $tgl_spk; ?>" 
						style="text-align: center;width:18.5%" readonly <?php echo $dis;?>  >
					</div>				
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Start :</b></span>
						<select size="1" id="jam_mulai" name="jam_mulai" style="padding:4px;margin-right:2px">
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
							<option value="<?php echo $jam_mulai;?>" selected><?php echo $jam_mulai;?></option>
						</select>	
						:
						<select size="1" id="menit_mulai"  name="menit_mulai" style="padding:4px;margin-right:2px">
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
								<option value="<?php echo $menit_mulai;?>" selected><?php echo $menit_mulai;?></option>
						</select>	
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Finish :</b></span>
						<select size="1" id="jam_selesai" name="jam_selesai" style="padding:4px;margin-right:2px">
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
							<option value="<?php echo $jam_selesai;?>" selected><?php echo $jam_selesai;?></option>
						</select>	
						:
						<select size="1" id="menit_selesai" name="menit_selesai" style="padding:4px;margin-right:2px">
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
							<option value="<?php echo $menit_selesai;?>" selected><?php echo $menit_selesai;?></option>	
						</select>	
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>No. Polisi :</b></span>
						<select id="id_mobil" name="id_mobil" <?php echo $dis;?> style="width: 40%;padding:4px">
							<?php
							$t1="select * from m_mobil_tr where status = '1' order by no_polisi  ";
							$h1=mysqli_query($koneksi, $t1);       
							while ($d1=mysqli_fetch_array($h1)){?>
								<option value="<?php echo $d1['id_mobil'];?>" ><?php echo $d1['no_polisi'];?></option>
							<?php }?>
							<option value="<?php echo $id_mobil;?>" selected><?php echo $no_polisi;?></option>
						</select>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<b>Kilometer  :</b>&nbsp;
						<input type="text" id="km"  name="km" value="<?php echo $km;?>" style="text-align: center;width:15%;border:1px solid rgb(169, 169, 169)" onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" />
					</div>
					
					<br>	
				</div>
            </div>
			
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;height:205px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Description</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>Jenis Pekerjaan :</b></span>
						<select id="jenis" name="jenis" onchange="jenis" <?php echo $dis;?> style="width: 80%;padding:4px">
							<?php
							$t1="select * from  m_jenis_spk where status = '1' order by nama  ";
							$h1=mysqli_query($koneksi, $t1);       
							while ($d1=mysqli_fetch_array($h1)){?>
							<option value="<?php echo $d1['nama'];?>" ><?php echo $d1['nama'];?></option>
							<?php }?>
							<option value="<?php echo $jenis;?>" selected><?php echo $jenis;?></option>
						</select>
					</div>
					
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>Keterangan :</b></span>
						<textarea name="ket" id="ket"
						style="resize:none;width: 80%; height: 110px; font-size: 11px; line-height: 12px; 
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
								<b>Add Spare Part</b>
							</button>
						<?php }?>
						<div class="table-responsive mailbox-messages" style="min-height:10px">									
							<div class="tampil_data"></div>
						</div>	
					</div>
				</div>
			<?php }?>
				
			<?php
				$link = "perbaikan.php?id=$xy1";
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Spare Part</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Item Spare Part :</b></span>
								<input type="text"  id ="nama_part"  style="text-align: left;width:70%" readonly  >
								<button class="btn btn-block btn-primary" id="cost"
									style="padding:6px;margin-top:-3px;border-radius:2px" type="button" 
									onClick="javascript:TampilPart()">
									<span class="glyphicon glyphicon-search"></span>
								</button>	
								<input type="hidden" id="id_part"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="modex"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Qty Stock :</b></span>
								<input type="text" id="stok" value="" style="text-align: center;width:12%;border:1px solid rgb(169, 169, 169)"
								onkeypress="return isNumber(event)" readonly	/>	
								<input type="text" id="unit_stok" value="" style="text-align: center;width:12%;border:1px solid rgb(169, 169, 169)" readonly	/>	
							</div>							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Qty Penggunaan :</b></span>
								<input type="text" id="qty" value="" style="text-align: center;width:12%;border:1px solid rgb(169, 169, 169)"
								onkeypress="return isNumber(event)"	/>	
								<input type="text" id="unit_qty" value="" style="text-align: center;width:12%;border:1px solid rgb(169, 169, 169)" readonly	/>	
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
									&nbsp;&nbsp;&nbsp;<b>Search : </b>
									<input type="text"  id ="cari_part"	style="text-align: left;width:200px" onkeypress="ListPart()" >
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
								<span class="input-group-addon" style="width:50%;text-align:right;padding:0px">
													
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
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
