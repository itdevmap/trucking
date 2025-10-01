<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
//include "lib.php";

$pq = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='7' ");
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
	$stat = 'All';
	$field = 'Customer';
	$field1 = 'Quo No';
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
	
	<!-- ---------------------- LEAFLET ---------------------- -->
	<!-- <link rel="stylesheet" href="leaflet/leaftet.css">
	<script src="leaflet/leaflet.js" type="text/javascript"></script> -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>


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
			return (((sign)?'':'-') + '' + num + '.' + cents);
			//return (((sign)?'':'-') + '' + num);
			
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

		function ReadData(hal) 
		{
			var tgl1 = $("#tgl1").val();
			var tgl2 = $("#tgl2").val();	
			var cari = $("#search_name").val();
			var paging = $("#paging").val();	
			var stat = $("#stat").val();
			var field = $("#field").val();
			var field1 = $("#field1").val();
			var cari1 = $("#search_name1").val();
			$.get("ajax/quo_crud.php", {
				tgl1:tgl1, 
				tgl2:tgl2, 
				field:field,
				stat:stat,
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

		function DelQuo(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/quo_crud.php", {
						id: id, type:"Del_Quo"
					},
					function (data, status) {
						 ReadData(1);
					}
				);
			}
		}

		function DelData(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/quo_crud.php", {
						id: id, type:"Del_Detil"
					},
					function (data, status) {
						 ReadData(1);
					}
				);
			}
		}

		function Confirm(id) {
			var hal = $("#hal").val();
			var conf = confirm("Are you sure to Executed ?");
			if (conf == true) {
				$.post("ajax/quo_crud.php", {
						id: id,type:"Executed"
					},
					function (data, status) {
						ReadData(hal);
					}
				);
			}
		}

		function CekRate()
		{
			var id_asal = $("#id_asal").val();
			var id_tujuan = $("#id_tujuan").val();
			var jenis_mobil = $("#jenis_mobil").val();
			//alert(jenis_mobil);
			$("#uj").val('');	
			$.post("ajax/deliv_crud.php", {
				id_asal: id_asal, id_tujuan:id_tujuan, jenis_mobil:jenis_mobil, type:"Cek_Rate"
				},
				function (data, status) {
					var data = JSON.parse(data);
					
					$("#uj").val(Rupiah(data.uj));
					$("#ritase").val(Rupiah(data.ritase));
					$("#km").val(Rupiah(data.km));
				}
			);
		}

		function GetOrder(id) 
		{
			var jenis_role = $("#jenis_role").val();
			if (jenis_role == '2'){
				document.getElementById("tampil_uj").style.display = 'none';
			} else {
				document.getElementById("tampil_uj").style.display = 'inline';
			}
			
			$.post("ajax/quo_crud.php", {
					id: id, type:"Detil_Data"
			},
			function (data, status) {
				var data = JSON.parse(data);
				$("#id_cust").val(data.id_cust);
				$("#id_asal").val(data.id_asal);
				$("#nama_asal").val(data.asal);
				$("#id_tujuan").val(data.id_tujuan);
				$("#nama_tujuan").val(data.tujuan);
				$("#jenis_mobil").val(data.jenis_mobil);
				$("#biaya").val(Rupiah(data.harga));
				CekRate();					
			}
			);
			
			$("#no_sj").val('-- Auto --');
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
			$("#tanggal").val(today);	
			$("#id_detil").val(id);	
			$("#no_do").val('');	
			$("#no_cont").val('');
			$("#id_mobil").val('');
			$("#id_supir").val('');
			$("#barang").val('');
			$("#penerima").val('');
			$("#ket").val('');
			$("#no_seal").val('');
			$("#berat").val('');
			$("#vol").val('');
			$("#mode").val('Add');
			$('#Data').modal('show');
		}

		function AddOrder() {
			var tanggal = $("#tanggal").val();
			var no_do = $("#no_do").val();

			if (tanggal === '') {
				alert("Tanggal harus diisi !..");
			} else if (no_do === '') {
				alert("No. DO/PO harus diisi !..");
			} else {
				var r = confirm("Are you sure ?...");
				if (r === true) {
					var id_detil = $("#id_detil").val();	
					var id_cust = $("#id_cust").val();					
					var tanggal = $("#tanggal").val();
					var no_do = $("#no_do").val();
					var penerima = $("#penerima").val();
					var id_asal = $("#id_asal").val();
					var id_tujuan = $("#id_tujuan").val();
					var jenis_mobil = $("#jenis_mobil").val();
					var biaya = $("#biaya").val();
					var uj = $("#uj").val();
					var ritase = $("#ritase").val();
					var ket = $("#ket").val();
					var sap_project = $("#sap_project").val();
					var barang = $("#barang").val();
					var berat = $("#berat").val();
					var vol = $("#vol").val();
					var no_cont = $("#no_cont").val();
					var no_seal = $("#no_seal").val();
					var id_mobil = $("#id_mobil").val();
					var id_supir = $("#id_supir").val();

					$.post("ajax/quo_crud.php", {
						id_detil: id_detil,
						id_cust: id_cust,
						tanggal: tanggal,
						no_do: no_do,
						penerima: penerima,
						id_asal: id_asal,
						id_tujuan: id_tujuan,
						jenis_mobil: jenis_mobil,						
						biaya: biaya,
						uj: uj,
						ritase: ritase,
						ket: ket,
						sap_project: sap_project,
						barang: barang,
						berat: berat,
						vol: vol,
						no_cont: no_cont,						
						no_seal: no_seal,						
						id_mobil: id_mobil,						
						id_supir: id_supir,
						type: "AddOrder",
					}, function (data, status) {
						alert(data);
						$("#Data").modal("hide");				
						ReadData(1);
					});
				}
			}	
		}

		function Download() 
		{
			var tgl1 = $("#tgl1").val();
			var tgl2 = $("#tgl2").val();	
			var cari = $("#search_name").val();
			var stat = $("#stat").val();
			var field = $("#field").val();
			var field1 = $("#field1").val();
			var cari1 = $("#search_name1").val();
			var id = tgl1+'|'+tgl2+'|'+stat+'|'+field+'|'+cari+'|'+field1+'|'+cari1;
			var idx = btoa(id);
			var win = window.open('fcl_excel.php?id='+idx);
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Quotation</b></font></h1></li>					
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
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Status :</b></span>
						<select id="stat" name ="stat"  style="width: 85px;padding:5px" onchange="ReadData(1)" >
							<option >In Progress</option>
							<option >Executed</option>
							<option >All</option>
							<option value="<?php echo $stat;?>" selected ><?php echo $stat;?></option>
						</select>	
					</div>
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Filter By :</b></span>
						<select size="1" id="field"  onchange="ReadData(1)" name="field" style="padding:4px;margin-right:2px;width: 85px">
							<option>Quo No</option>
							<option>Customer</option>
							<option>Origin</option>
							<option>Destination</option>
							<option>Type</option>
							<option value="<?php echo $field; ?>" selected hidden><?php echo $field; ?></option>
						</select>
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
					</div>
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"></span>
						<select size="1" id="field1"  onchange="ReadData(1)" name="field1" style="padding:4px;margin-right:2px;width: 85px">
							<option>Quo No</option>
							<option>Customer</option>
							<option>Origin</option>
							<option>Destination</option>
							<option>Type</option>
							<option value="<?php echo $field1; ?>" selected hidden><?php echo $field1; ?></option>
						</select>
						<input type="text"  id ="search_name1" name="search_name1" value="<?php echo $search_name1; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
						<input type="hidden"  id ="hal" name="hal" value="<?php echo $hal; ?>" style="text-align: left;width:5%"  >
						<input type="hidden"  id ="jenis_role" name="jenis_role" value="<?php echo $id_role; ?>" style="text-align: left;width:5%"  >
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
								onClick="window.location.href = 'quo_data.php?id=<?php echo $xy1; ?>' ">
								<span class="fa  fa-plus-square"></span>
								<b>Created Quotation</b>
								</button>
							<?php }?>	
							<!--
							<button class="btn btn-block btn-warning" 
								style="margin:0px;margin-left:-1px;margin-bottom:0px;border-radius:2px" type="button"  title = ""
								onClick="javascript:Download()">
								<span class="fa fa-file-text"></span>
								<b>Download</b>
							</button>	
							-->	
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

	<!-- --------------------------- MODAL ADD ORDER --------------------------- -->
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
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Order :</b></span>
								<input type="text"  id ="no_sj" style="text-align: center;width:22%" readonly  >
								<input type="hidden" id="id_cust"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="mode"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id_detil"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>						
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Date :</b></span>
								<input type="text"  id ="tanggal" style="text-align: center;width:22%" readonly  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Project :</b></span>
								<select id="sap_project" <?php echo $dis;?> style="width: 80%;padding:4px">
									<option value="" hidden></option>
									<?php
									$t1="SELECT * FROM sap_project ORDER BY kode_project";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['rowid'];?>" ><?php echo $d1['kode_project'];?></option>
									<?php }?>
								</select>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. DO/PO :</b></span>
								<input type="text" id="no_do" value="" style="text-transform: uppercase;text-align: left;width:80%;"   >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Receiver :</b></span>
								<textarea id="penerima"
								style="resize:none;width: 80%; height: 70px; font-size: 11px; line-height: 12px; 
								border: 1px solid #444; padding: 5px;"  ></textarea>	
							</div>

							
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Itemname :</b></span>								
									<input type="text" id="barang" value="" style="text-transform: uppercase;text-align: left;width:80%;"   >	
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Weight :</b></span>
									<input type="text" id="berat" value="0" style="text-align: right;width:22%;" 
									onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  > &nbsp;<b>KG</b>	
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Vol :</b></span>
									<input type="text" id="vol" value="0" style="text-align: right;width:22%;" 
									onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  > &nbsp;<b>M3</b>	
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Container :</b></span>
									<input type="text"  id ="no_cont" style="text-transform: uppercase;text-align: center;width:22%"  >														
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Seal :</b></span>
									<input type="text"  id ="no_seal" style="text-transform: uppercase;text-align: center;width:22%"  >														
								</div> 
							

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Origin :</b></span>
								<input type="hidden" id="id_asal"   value=""  />
								<input type="text" id="nama_asal" value="" style="text-transform: uppercase;text-align: left;width:80%;"  readonly >
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Destination :</b></span>
								<input type="hidden" id="id_tujuan"   value=""  />
								<input type="text" id="nama_tujuan" value="" style="text-transform: uppercase;text-align: left;width:80%;"  readonly >
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Type :</b></span>
								<input type="text" id="jenis_mobil" value="" style="text-transform: uppercase;text-align: left;width:80%;"  readonly >
							</div>	
							
								<div style="width:100%;" class="input-group">
										<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Police Num :</b></span>
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
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Driver :</b></span>
									<select id="id_supir"  style="width: 80%;padding:4px">
										<?php
										$t1="select * from m_supir_tr where status = '1' order by nama_supir  ";
										$h1=mysqli_query($koneksi, $t1);       
										while ($d1=mysqli_fetch_array($h1)){?>
										<option value="<?php echo $d1['id_supir'];?>" ><?php echo $d1['nama_supir'];?></option>
										<?php }?>
									</select>
								</div>
							
							<div  id="tampil_uj" style="display:none;">
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Deliv. Cost :</b></span>								
									<input type="text" id="biaya" value="0" style="text-align: right;width:22%;" 
									onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly >	
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Road Fee :</b></span>								
									<input type="text" id="uj" value="0" style="text-align: right;width:22%;" 
									onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly >	
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Ritase :</b></span>								
									<input type="text" id="ritase" value="0" style="text-align: right;width:22%;" onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly>	
								</div>
								<input type="hidden" id="km" value="0" style="text-align: right;width:22%;" onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Remarks :</b></span>
								<input type="text" id="ket" value="" style="text-align: left;width:80%;"   >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddOrder()">
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
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
