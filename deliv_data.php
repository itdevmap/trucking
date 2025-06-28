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
	$id_sj = $_POST['id_sj'];	
	$tgl_sj = $_POST['tgl_sj'];	
	$id_asal = $_POST['id_asal'];
	$id_tujuan = $_POST['id_tujuan'];
	$jenis_mobil = $_POST['jenis_mobil'];
	$id_mobil = $_POST['id_mobil'];
	$no_cont = trim(addslashes(strtoupper($_POST['no_cont'])));
	$id_supir = $_POST['id_supir'];
	$uj = $_POST['uj'];
	$ritase = $_POST['ritase'];
	$ket = addslashes(trim($_POST['ket']));
	$tgl_sjx = ConverTglSql($tgl_sj);
	
	$uj = str_replace(",","", $uj);
	$ritase = str_replace(",","", $ritase);
	
	if($mode == 'Add' )
	{
		$ptgl = explode("-", $tgl_sj);
		$tg = $ptgl[0];
		$bl = $ptgl[1];
		$th = $ptgl[2];	
		$query = "SELECT max(right(no_sj,5)) as maxID FROM t_jo_sj_tr where  year(tgl_sj) = '$th'  ";
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
		$no_sj = "SJ-$year$noUrut";
		
		$sql = "INSERT INTO  t_jo_sj_tr (tipe, no_sj, tgl_sj, no_cont, id_asal, id_tujuan, jenis_mobil, id_mobil, id_supir, uj, ritase, ket, created) values
				('LCL', '$no_sj', '$tgl_sjx', '$no_cont', '$id_asal', '$id_tujuan', '$jenis_mobil', '$id_mobil', '$id_supir', '$uj','$ritase',  '$ket', '$id_user')";
			$hasil= mysqli_query($koneksi, $sql);
		
		$sql = mysqli_query($koneksi, "select max(id_sj)as id from t_jo_sj_tr ");			
		$row = mysqli_fetch_array($sql);
		$id_sj = $row['id'];
		
	}else{
		
		$sql = "update t_jo_sj_tr set 
					tgl_sj = '$tgl_sjx',
					id_asal = '$id_asal',
					no_cont = '$no_cont',
					id_tujuan = '$id_tujuan',
					jenis_mobil = '$jenis_mobil',
					id_mobil = '$id_mobil',
					id_supir = '$id_supir',
					uj = '$uj',
					ket = '$ket',
					ritase = '$ritase'
					where id_sj = '$id_sj'	";
			$hasil=mysqli_query($koneksi,$sql);
	
		
	}
	
	$cat ="Data saved...";
	$xy1="Edit|$id_sj|$cat";
	$xy1=base64_encode($xy1);
	header("Location: deliv_data.php?id=$xy1");
}
else
{	
	$idx = $_GET['id'];	
	$x=base64_decode($idx);
	$pecah = explode("|", $x);
	$mode= $pecah[0];
	$id_sj = $pecah[1];
	$cat = $pecah[2];
}

if($mode == 'Add')
{
	$no_sj = '-- Auto -- ';
	$tgl_sj = date('d-m-Y');
	$jenis = 0;
	$pq = mysqli_query($koneksi,"select * from m_jenis_mobil_tr where id_jenis = '1'   ");
	$rq=mysqli_fetch_array($pq);
	$jenis_mobil = $rq['nama'];
	$pq = mysqli_query($koneksi,"select * from m_kota_tr where id_kota = '4'");
	$rq=mysqli_fetch_array($pq);	
	$id_asal = $rq['id_kota'];
	$nama_asal = $rq['nama_kota'];
	
}else{
	
	$pq = mysqli_query($koneksi, "select t_jo_sj_tr.*, m_kota_tr.nama_kota as asal,
		  m_kota1.nama_kota as tujuan, m_mobil_tr.no_polisi, m_supir_tr.nama_supir
		  from 
		  t_jo_sj_tr left join m_kota_tr on t_jo_sj_tr.id_asal = m_kota_tr.id_kota
		  left join m_kota_tr as m_kota1 on t_jo_sj_tr.id_tujuan = m_kota1.id_kota
		  left join m_mobil_tr on t_jo_sj_tr.id_mobil = m_mobil_tr.id_mobil
		  left join m_supir_tr on t_jo_sj_tr.id_supir = m_supir_tr.id_supir
		  where t_jo_sj_tr.id_sj = '$id_sj'  ");
	$rq=mysqli_fetch_array($pq);	
	$no_sj = $rq['no_sj'];
	$tgl_sj = ConverTgl($rq['tgl_sj']);
	$id_asal = $rq['id_asal'];
	$no_cont = $rq['no_cont'];
	$nama_asal = $rq['asal'];
	$id_tujuan = $rq['id_tujuan'];
	$nama_tujuan = $rq['tujuan'];
	$ket = str_replace("\'","'",$rq['ket']);
	$jenis_mobil = $rq['jenis_mobil'];
	$id_mobil = $rq['id_mobil'];
	$no_polisi = $rq['no_polisi'];
	$id_supir = $rq['id_supir'];
	$nama_supir = $rq['nama_supir'];	
	$uj = number_format($rq['uj'],0);
	$ritase = number_format($rq['ritase'],0);
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
			$("#tgl_sj").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			ReadData();
		});	
		function ReadData() {	
			var id_sj = $("#id_sj").val();
			var mode = $("#mode").val();
			$.get("ajax/deliv_crud.php", {mode:mode,id_sj:id_sj, type:"Read_Order" }, function (data, status) {
				$(".tampil_data").html(data);
			});
		}
		function checkvalue() {
			var id_asal = document.getElementById('id_asal').value; 
			var id_tujuan = document.getElementById('id_tujuan').value; 
			var id_supir = document.getElementById('id_supir').value; 
			var id_mobil = document.getElementById('id_mobil').value; 
			
			if(id_asal == '') {
				alert ('Asal harus diisi..');				
				return false;	
			}else if(id_tujuan == '') {
				alert ('Tujuan harus diisi..');				
				return false;	
			}else if(id_mobil == '') {
				alert ('No. Polisi harus diisi..');				
				return false;	
			}else if(id_supir == '') {
				alert ('Supir harus diisi..');				
				return false;	
			}else{
				return true;
			}	
		}
		function CekRate() 
		{	
			var id_asal = $("#id_asal").val();
			var id_tujuan = $("#id_tujuan").val();
			var jenis_mobil = $("#jenis_mobil").val();
			$("#uj").val('');	
			$("#ritase").val('');	
			
			$.post("ajax/deliv_crud.php", {
				id_asal: id_asal, id_tujuan:id_tujuan, jenis_mobil:jenis_mobil, type:"Cek_Rate"
				},
				function (data, status) {
					var data = JSON.parse(data);
					
					$("#uj").val(Rupiah(data.uj));
					$("#ritase").val(Rupiah(data.ritase));
					
				}
			);
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
		
		function TampilOrder(){	
			$("#cari").val('');
			ListOrder();
			$('#DataOrder').modal('show');
		}
		function ListOrder() {	
			var cari = $("#cari").val();
			$.get("ajax/deliv_crud.php", {cari:cari,  type:"ListOrder_LCL" }, function (data, status) {
				$(".tampil_order").html(data);
				$("#hal").val(hal);
			});
		}
		function PilihOrder(id) {	
			var id_sj = $("#id_sj").val();		
			//alert(id+'-'+id_sj);
			$.post("ajax/deliv_crud.php", {
				id:id,
				id_sj:id_sj,
				type : "Add_SJ"
				}, function (data, status) {
					//alert(data);
					$("#DataOrder").modal("hide");				
					ReadData();
			});
		}
		function DelOrder(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/deliv_crud.php", {
						id: id, type:"Del_Order"
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Surat Jalan</b></font></h1></li>					
			</ol>
			<br>
			<?php if($cat != '') {?>
			<div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
				<i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo "$cat"; ?></font>
			</div>
			<?php }?>
			
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;height:235px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
					text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Data SJ</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>#No SJ :</b></span>
						<input type="text"  id ="no_sj" name="no_sj" value="<?php echo $no_sj; ?>" 
						style="text-align: center;width:16%" readonly <?php echo $dis;?> >						
						<input type="hidden"  id ="id_sj" name="id_sj" value="<?php echo $id_sj; ?>" >	
						<input type="hidden"  id ="mode" name="mode" value="<?php echo $mode; ?>" >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Tanggal :</b></span>
						<input type="text"  id ="tgl_sj" name="tgl_sj" value="<?php echo $tgl_sj; ?>" 
						style="text-align: center;width:16%" readonly <?php echo $dis;?>  >
					</div>				
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Asal :</b></span>
						<select id="id_asal" name="id_asal" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
							<?php
							$t1="select * from m_kota_tr where status = '1' order by nama_kota  ";
							$h1=mysqli_query($koneksi, $t1);       
							while ($d1=mysqli_fetch_array($h1)){?>
							<option value="<?php echo $d1['id_kota'];?>" ><?php echo $d1['nama_kota'];?></option>
							<?php }?>
							<option value="<?php echo $id_asal;?>" selected ><?php echo $nama_asal;?></option>
						</select>
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Tujuan :</b></span>
						<select id="id_tujuan" name="id_tujuan" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
							<?php
							$t1="select * from m_kota_tr where status = '1' order by nama_kota  ";
							$h1=mysqli_query($koneksi, $t1);       
							while ($d1=mysqli_fetch_array($h1)){?>
							<option value="<?php echo $d1['id_kota'];?>" ><?php echo $d1['nama_kota'];?></option>
							<?php }?>
							<option value="<?php echo $id_tujuan;?>" selected><?php echo $nama_tujuan;?></option>
						</select>
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Remarks :</b></span>
						<textarea name="ket" id="ket"
						style="resize:none;width: 80%; height: 50px; font-size: 11px; line-height: 12px; 
						border: 1px solid #4; padding: 5px;" <?php echo $dis;?> ><?php echo $ket; ?></textarea>
					</div>
					<br>	
				</div>
            </div>
			
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;height:235px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Description</b>
					</div>
					<br>	
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>Container :</b></span>
						<input type="text" id="no_cont" name="no_cont" value="<?php echo $no_cont;?>" style="text-transform: uppercase;text-align: left;width:16%;"  >
					</div>	
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>Jenis Mobil :</b></span>
						<select id="jenis_mobil" name="jenis_mobil" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
							<?php
							$t1="select * from m_jenis_mobil_tr where status = '1' order by nama  ";
							$h1=mysqli_query($koneksi, $t1);       
							while ($d1=mysqli_fetch_array($h1)){?>
							<option value="<?php echo $d1['nama'];?>" ><?php echo $d1['nama'];?></option>
							<?php }?>
							<option value="<?php echo $jenis_mobil;?>" selected><?php echo $jenis_mobil;?></option>
						</select>
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>No. Polisi :</b></span>
						<select id="id_mobil" name="id_mobil" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
							<?php
							$t1="select * from m_mobil_tr where status = '1' order by no_polisi  ";
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
							$t1="select * from m_supir_tr where status = '1' order by nama_supir  ";
							$h1=mysqli_query($koneksi, $t1);       
							while ($d1=mysqli_fetch_array($h1)){?>
							<option value="<?php echo $d1['id_supir'];?>" ><?php echo $d1['nama_supir'];?></option>
							<?php }?>
							<option value="<?php echo $id_supir;?>" selected><?php echo $nama_supir;?></option>
						</select>
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>Uang Jalan :</b></span>
						<input type="text" id="uj" name="uj" value="<?php echo $uj;?>" style="text-align: right;width:16%;" 
						onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)" readonly >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;min-width:160px"><b>Ritase :</b></span>
						<input type="text" id="ritase" name="ritase" value="<?php echo $ritase;?>" style="text-align: right;width:16%;" 
						onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)" readonly >
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
								onClick="javascript:TampilOrder()"  <?php echo $dis;?> <?php echo $dis_copy;?> >
								<span class="fa  fa-plus-square"></span>
								<b>Add Data Order</b>
							</button>
						<?php }?>
						<div class="table-responsive mailbox-messages" style="min-height:10px">									
							<div class="tampil_data"></div>
						</div>	
					</div>
				</div>
			<?php }?>
				
			<?php
				$link = "deliv.php?id=$xy1";
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
	
	<div class="modal fade" id="DataOrder"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none; width:700px">	
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Order</b>
							</div>	
							<br>
							<div style="width:100%" class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>No. Order :</b>&nbsp;&nbsp;
									<input type="text"  id ="cari" name="cari" value="<?php echo $cari; ?>" 
									style="text-align: left;width:200px" onkeypress="ListOrder()" >
									<button class="btn btn-block btn-primary" 
									style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:2px;padding:5px" 
									onClick="javascript:ListOrder()" ">
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
								<div class="tampil_order"></div>
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
