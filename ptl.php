<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
//include "lib.php";

$pq = mysqli_query($koneksi,"select * from m_role_akses where id_role = '$id_role'  and id_menu ='22' ");
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
	$paging='25';
	$hal='1';
	$stat = 'Request';
	$field = 'No PR';
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
			$("#tgl_bc").datepicker({
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
		function ReadData(hal) 
		{
			var tgl1 = $("#tgl1").val();
			var tgl2 = $("#tgl2").val();	
			var cari = $("#search_name").val();
			var stat = $("#stat").val();
			var paging = $("#paging").val();
			var field = $("#field").val();
			$.get("ajax/ptl_crud.php", {
				tgl1:tgl1, 
				tgl2:tgl2, 
				field:field,
				paging:paging,
				cari:cari,
				stat:stat,
				hal:hal,
				type:"Read" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}		
		function GetData(id) {
			
			$("#id_bc").val(id);
			$.post("ajax/ptl_crud.php", {
					id: id, type:"Detil_BC"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#tgl_bc").val(changeDateFormat(data.tgl_bc));
					$("#etd").val(changeDateFormat(data.etd));		
					$("#eta").val(changeDateFormat(data.eta));					
					$("#nama_kota").val(data.nama_kota);	
					$("#id_jo").val(data.id_jo);
					$("#no_cont").val(data.no_cont);
					$("#feet").val(data.feet);
					$("#jo_no").val(data.jo_no);
					$("#no_bc").val(data.no_bc);
					$("#nama_cust").val(data.nama_cust);
					$("#ket").val(data.ket_bc);			
					$("#mode").val('Edit');	
				}
			);
			$("#Data").modal("show");
		}
		function AddData() {	
			var ket_batal = $("#ket_batal").val();
			if(ket_batal == '')
			{
				alert("Alasan Pembatalan harus diisi !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {
					var id_bc = $("#id_bc").val();
					var ket_batal = $("#ket_batal").val();
					$.post("ajax/ptl_crud.php", {
						id_bc:id_bc,
						ket_batal:ket_batal,
						type : "Add_Batal"
						}, function (data, status) {
						//alert(data);	
						var hal = $("#hal").val();				
						ReadData(1);
						$("#Data").modal("hide");
							//CetakKwitansi(data);
					});
				}
			}			
		}
		function GetOrder(id) {
			
			$("#id_bc1").val(id);
			$.post("ajax/ptl_crud.php", {
					id: id, type:"Detil_BC"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#tgl_bc1").val(changeDateFormat(data.tgl_bc));
					$("#etd1").val(changeDateFormat(data.etd));		
					$("#eta1").val(changeDateFormat(data.eta));					
					$("#nama_kota1").val(data.nama_kota);	
					$("#id_tujuan1").val(data.id_kota);
					$("#id_asal1").val('4');
					$("#id_jo1").val(data.id_jo);
					$("#no_cont1").val(data.no_cont);
					$("#feet1").val(data.feet);
					$("#jo_no1").val(data.jo_no);
					$("#no_bc1").val(data.no_bc);
					$("#nama_cust1").val(data.nama_cust);
					$("#ket1").val(data.ket_bc);			
					$("#mode1").val('Edit');	
					CekRate();
				}
			);
			$("#DataOrder").modal("show");
		}
		function SaveOrder() {
			var conf = confirm("Are you sure to Add Order ?");
			if (conf == true) {
				var jenis_mobil = $("#jenis1").val();	
				var biaya_kirim = $("#biaya_kirim1").val();	
				var id = $("#id_bc1").val();	
				$.post("ajax/ptl_crud.php", {
						id: id, jenis_mobil, biaya_kirim, type:"Add_Order"
					},
					function (data, status) {
						$("#DataOrder").modal("hide");
						 ReadData(1);
					}
				);
			}
		}
		function CekRate()
		{
			var id_asal = $("#id_asal1").val();
			var id_tujuan = $("#id_tujuan1").val();
			var jenis_mobil = $("#jenis1").val();
			//alert(id_tujuan);
			$("#biaya_kirim1").val('');	
			$.post("ajax/fcl_crud.php", {
				id_asal: id_asal, id_tujuan:id_tujuan, jenis_mobil:jenis_mobil, type:"Cek_Rate"
				},
				function (data, status) {
					var data = JSON.parse(data);					
					$("#biaya_kirim1").val(Rupiah(data.rate));
					
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
		function ListDoc(id) {	
			$("#id").val(id);
			$.get("ajax/ptl_crud.php", {id:id,  type:"ListDoc" }, function (data, status) {
				$(".tampil_doc").html(data);
				});
			$("#DaftarDoc").modal("show");
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>PR Order PTL</b></font></h1></li>					
			</ol>
			<br>
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Date:</b></span>
						<input type="text"  id ="tgl1" name="tgl1" value="<?php echo $tgl1; ?>" 
						style="text-align: center;width:85px" onchange="ReadData(1)" readonly >
						&nbsp;&nbsp;<b>To</b>&nbsp;&nbsp;
						<input type="text"  id ="tgl2" name="tgl2" value="<?php echo $tgl2; ?>" 
						style="text-align: center;width:85px" onchange="ReadData(1)" readonly >	
					</div>	
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Status :</b></span>
						<select id="stat" name ="stat"  style="width: 85px;padding:4px" onchange="ReadData(1)" >
							<option >Request</option>
							<option >Approved</option>
							<option >Reject</option>
							<option >All</option>
							<option value="<?php echo $stat;?>" selected ><?php echo $stat;?></option>
						</select>	
					</div>	
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Filter By:</b></span>
						<select size="1" id="field"  name="field" style="padding:4px;margin-right:2px;width: 85px">							
							<option>No PR</option>
							<option>JOB No</option>
							<option>Customer</option>
							<option>No Container</option>
							<option>Tujuan</option>
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
													
						</span>
						<span class="input-group-addon" style="width:50%;text-align:right;padding:0px;background:#fff">
						Row Page:&nbsp;
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Cancel PTL</b>
							</div>	
							<br>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Date :</b></span>
								<input type="text" id="tgl_bc"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" readonly />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No PR :</b></span>
								<input type="text" id="no_bc"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" readonly />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>#No Job :</b></span>
								<input type="text"  id ="jo_no" name="jo_no" value="<?php echo $jo_no; ?>" 
								style="text-align: center;width:22%" readonly  >
								
								<input type="hidden"  id ="id_jo" name="id_jo" value="<?php echo $id_jo; ?>" >
								<input type="hidden"  id ="id_bc" name="id_bc" value="<?php echo $id_bc; ?>" >
								<input type="hidden"  id ="mode" name="mode" value="<?php echo $mode; ?>" >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Customer :</b></span>
								<input type="text"  id ="nama_cust" name="nama_cust" value="<?php echo $nama_cust; ?>" 
								style="text-align: left;width:80%" readonly <?php echo $dis;?> >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>ETD :</b></span>
								<input type="text" id="etd"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" disabled />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>ETA :</b></span>
								<input type="text" id="eta"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" disabled />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>No. Container :</b></span>
								<input type="text"  id ="no_cont" name="" value="<?php echo $nama_cust; ?>" 
								style="text-align: left;width:22%" readonly <?php echo $dis;?> >
								<input type="text"  id ="feet" name="" value="<?php echo $nama_cust; ?>" 
								style="text-align: left;width:10%" readonly <?php echo $dis;?> >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tujuan :</b></span>
								<input type="text" id="nama_kota"  value="" style="text-align: left;width:80%;border:1px solid rgb(169, 169, 169);background:#eee" disabled />
							</div>
							<!--
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Keterangan :</b></span>
								<input type="text-align" id="ket"   style="text-align: left;width:80%;"  disabled >
							</div>	
							-->
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Alasan Pembatalan :</b></span>
								<input type="text-align" id="ket_batal"   style="text-align: left;width:80%;"   >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddData()">
								<span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save</b>&nbsp;&nbsp;</button>	
								<button type="button" class="btn btn-danger" data-dismiss="modal">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Cancel</button>	
							</div>
							<br>
						</div>
					</div>								
				</div>
			</div>
		</div>	
    </div>
	
	<div class="modal fade" id="DataOrder"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">	
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">							
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Booking PTL</b>
							</div>	
							<br>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Date :</b></span>
								<input type="text" id="tgl_bc1"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" readonly />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No PR :</b></span>
								<input type="text" id="no_bc1"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" readonly />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>#No Job :</b></span>
								<input type="text"  id ="jo_no1" name="jo_no" value="<?php echo $jo_no; ?>" 
								style="text-align: center;width:22%" readonly  >
								
								<input type="hidden"  id ="id_jo1" name="id_jo" value="<?php echo $id_jo; ?>" >
								<input type="hidden"  id ="id_bc1" name="id_bc" value="<?php echo $id_bc; ?>" >
								<input type="hidden"  id ="mode1" name="mode" value="<?php echo $mode; ?>" >
								<input type="hidden"  id ="id_tujuan1" name="id_jo" value="<?php echo $id_jo; ?>" >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Penerima :</b></span>
								<input type="text"  id ="nama_cust1" name="nama_cust" value="<?php echo $nama_cust; ?>" 
								style="text-align: left;width:80%" readonly <?php echo $dis;?> >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>ETD :</b></span>
								<input type="text" id="etd1"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" disabled />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>ETA :</b></span>
								<input type="text" id="eta1"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" disabled />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>No. Container :</b></span>
								<input type="text"  id ="no_cont1" name="" value="<?php echo $nama_cust; ?>" 
								style="text-align: left;width:22%" readonly <?php echo $dis;?> >
								<input type="text"  id ="feet1" name="" value="<?php echo $nama_cust; ?>" 
								style="text-align: left;width:10%" readonly <?php echo $dis;?> >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Asal :</b></span>
								<select id="id_asal1"  onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
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
								<input type="text" id="nama_kota1"  value="" style="text-align: left;width:80%;border:1px solid rgb(169, 169, 169);background:#eee" disabled />
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Jenis Mobil :</b></span>
								<select id="jenis1"  onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_jenis_mobil_tr where status = '1' order by nama   ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['nama'];?>" ><?php echo $d1['nama'];?></option>
									<?php }?>
								</select>	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Biaya Kirim :</b></span>
								<input type="text" id="biaya_kirim1" style="text-align: right;width:20%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  readOnly>
							</div>
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="SaveOrder()">
								<span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save</b>&nbsp;&nbsp;</button>	
								<button type="button" class="btn btn-danger" data-dismiss="modal">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Cancel</button>	
							</div>
							<br>
						</div>
					</div>								
				</div>
			</div>
		</div>	
    </div>
	
	<div class="modal fade" id="DaftarDoc"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document" style="width:750px;">
			<div class="modal-content" style="background: none">
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;List Document</b>
							</div>	
							<div  class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:50%;text-align:left;padding:0px;background: none;">									
									<input type="hidden"  id ="id" name="id" value=""   >
								</span>								
							</div>	
							
							<button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-left:-1px; margin-bottom:2px;padding-top;-2px;padding:0px;padding-right:5px;padding-left:3px;padding-bottom:-2px">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Close</b></button>	
							<div class="table-responsive mailbox-messages" >									
								<div class="tampil_doc"></div>
							</div>
							
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
