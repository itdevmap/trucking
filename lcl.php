<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
//include "lib.php";

$pq = mysqli_query($koneksi,"select * from m_role_akses where id_role = '$id_role'  and id_menu ='2' ");
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
	$field = 'No Order';
	$field1 = 'No Order';
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
			$.get("ajax/lcl_crud.php", {
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
		
	
		function CekStatus(cb) {
			var checkBox = document.getElementById("jenis_po");
			$("#id_jo").val('');
			$("#no_jo").val('');
			$("#id_cust").val('');
			$("#nama_cust").val('');
			if (checkBox.checked == true){
				$("#jenis").val('1');
				document.getElementById("tampil_jo").style.display = 'inline';
				document.getElementById("btn_cust").style.display = 'none';
			} else {
				$("#jenis").val('0');
				document.getElementById("tampil_jo").style.display = 'none';
				document.getElementById("btn_cust").style.display = 'inline';
			
			}
		}	
		function TampilJO(){	
			var cari = $("#cari_data").val();
			$.get("ajax/lcl_crud.php", {cari:cari,  type:"ListJO" }, function (data, status) {
				$(".tampil_jo").html(data);
			});
			$('#DataJO').modal('show');
		}
		function ListJO() {	
			var cari = $("#cari_data").val();
			$.get("ajax/lcl_crud.php", {cari:cari, type:"ListJO" }, function (data, status) {
				$(".tampil_jo").html(data);
			});
		}
		function PilihJO(id) {	
			$.post("ajax/lcl_crud.php", {
					id: id, type:"DetilJO"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_jo").val(data.id_jo);
					$("#no_jo").val(data.jo_no);
					$("#id_cust").val(data.id_cust);
					$("#nama_cust").val(data.nama_cust);
				}
			);
			$("#DataJO").modal("hide");
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
			$("#tanggal").val(today);	
			$("#id_jo").val('');
			$("#id_cust").val('');
			$("#nama_cust").val('');
			$("#alamat").val('');
			$("#nama_barang").val('');
			$("#qty").val('');
			$("#berat").val('');
			$("#vol").val('');
			$("#mode").val('Add');
			$("#no_order").val('-- auto --');
			$("#biaya_kirim").val('');
			$("#ket").val('');
			document.getElementById("tampil_jo").style.display = 'none';
			document.getElementById("btn_cust").style.display = 'inline';
			document.getElementById("jenis_po").checked = false;
			$('#Data').modal('show');
		}
		function AddData() {	
			var tanggal = $("#tanggal").val();
			var penerima = $("#penerima").val();
			var nama_cust = $("#nama_cust").val();
			var nama_barang = $("#nama_barang").val();
			var qty = $("#qty").val();
			var berat = $("#berat").val();
			if(tanggal == '' )
			{
				alert("Tanggal harus diisi !..");
			}
			else if(nama_cust == '')
			{
				alert("Customer harus diisi !..");
			}
			else if(penerima == '')
			{
				alert("penerima harus diisi !..");
			}
			else if(nama_barang == '')
			{
				alert("Nama Barang harus diisi !..");
			}
			else if(qty <= 0)
			{
				alert("Qty harus diisi !..");
			}
			else if(berat <= 0)
			{
				alert("Berat harus diisi !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id = $("#id").val();					
					var id_jo = $("#id_jo").val();
					var no_jo = $("#no_jo").val();
					var tanggal = $("#tanggal").val();
					var id_cust = $("#id_cust").val();
					var nama_cust = $("#nama_cust").val();
					var id_asal = $("#id_asal").val();
					var id_tujuan = $("#id_tujuan").val();
					var penerima = $("#penerima").val();
					var alamat_penerima = $("#alamat_penerima").val();
					var nama_barang = $("#nama_barang").val();
					var qty = $("#qty").val();
					var unit = $("#unit").val();
					var berat = $("#berat").val();
					var vol = $("#vol").val();
					var biaya_kirim = $("#biaya_kirim").val();
					var ket = $("#ket").val();
					var mode = $("#mode").val();
					var jenis = $("#jenis").val();
					$.post("ajax/lcl_crud.php", {
						id:id,
						id_jo:id_jo,
						no_jo:no_jo,
						jenis:jenis,
						tanggal:tanggal,
						id_cust:id_cust,
						nama_cust:nama_cust,
						id_asal:id_asal,
						id_tujuan:id_tujuan,
						alamat_penerima:alamat_penerima,
						penerima:penerima,
						nama_barang:nama_barang,
						qty:qty,
						unit:unit,
						berat:berat,
						vol:vol,	
						biaya_kirim:biaya_kirim,
						ket:ket,	
						mode:mode,
						type : "Add_Order"
						}, function (data, status) {
						alert(data);
						$("#Data").modal("hide");				
						ReadData();
					});
				}
			}	
		}
		function GetData(id) {			
			$("#id").val(id);
			$.post("ajax/lcl_crud.php", {
					id: id, type:"Detil_Order"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#no_order").val(data.no_jo);	
					$("#tanggal").val(changeDateFormat(data.tgl_jo));
					$("#id_jo").val(data.id_jo_fw);
					$("#no_jo").val(data.jo_no);
					$("#id_cust").val(data.id_cust);
					$("#nama_cust").val(data.nama_cust);
					$("#id_asal").val(data.id_asal);
					$("#id_tujuan").val(data.id_tujuan);
					$("#alamat_penerima").val(data.alamat_penerima);
					$("#penerima").val(data.penerima);
					$("#nama_barang").val(data.nama_barang);
					$("#qty").val(data.qty);					
					$("#unit").val(data.unit);
					$("#berat").val(Desimal(data.berat));					
					$("#vol").val(Desimal(data.vol));	
					$("#biaya_kirim").val(Rupiah(data.biaya_kirim));	
					$("#jenis").val(data.jenis);	
					$("#ket").val(data.ket);
					if(data.jenis == '1')
					{
						document.getElementById("tampil_jo").style.display = 'inline';
						document.getElementById("btn_cust").style.display = 'none';
						document.getElementById("jenis_po").checked = true;
					}else{
						document.getElementById("tampil_jo").style.display = 'none';
						document.getElementById("btn_cust").style.display = 'inline';
						document.getElementById("jenis_po").checked = false;
					}
					$("#mode").val('Edit');
					
				}
			);
			$("#Data").modal("show");
		}
		function Delete(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/lcl_crud.php", {
						id: id, type:"Del_Order"
					},
					function (data, status) {
						 ReadData();
					}
				);
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
			var win = window.open('lcl_excel.php?id='+idx);
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Order (LCL)</b></font></h1></li>					
			</ol>
			<br>
			
			
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Tanggal :</b></span>
						<input type="text"  id ="tgl1" name="tgl1" value="<?php echo $tgl1; ?>" 
						style="text-align: center;width:85px" onchange="ReadData(1)" readonly >
						&nbsp;&nbsp;<b>s.d</b>&nbsp;&nbsp;
						<input type="text"  id ="tgl2" name="tgl2" value="<?php echo $tgl2; ?>" 
						style="text-align: center;width:85px" onchange="ReadData(1)" readonly >	
					</div>	
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Status :</b></span>
						<select id="stat" name ="stat"  style="width: 85px;padding:5px" onchange="ReadData(1)" >
							<option >Open</option>
							<option >Close</option>
							<option >All</option>
							<option value="<?php echo $stat;?>" selected ><?php echo $stat;?></option>
						</select>	
					</div>		
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Filter By :</b></span>
						<select size="1" id="field"  onchange="ReadData(1)" name="field" style="padding:4px;margin-right:2px;width: 85px">
							<option>No Order</option>
							<option>No SJ</option>
							<option>Customer</option>
							<option>Asal</option>
							<option>Tujuan</option>
							<option>Nama Barang</option>
							<option value="<?php echo $field; ?>" selected><?php echo $field; ?></option>
						</select>
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
					</div>
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"></span>
						<select size="1" id="field1"  onchange="ReadData(1)" name="field1" style="padding:4px;margin-right:2px;width: 85px">
							<option>No Order</option>
							<option>No SJ</option>
							<option>Customer</option>
							<option>Asal</option>
							<option>Tujuan</option>
							<option>Nama Barang</option>
							<option value="<?php echo $field1; ?>" selected><?php echo $field1; ?></option>
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
								onClick="window.location.href = 'lcl_data.php?id=<?php echo $xy1; ?>' ">
								<span class="fa  fa-plus-square"></span>
								<b>Created Order</b>
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Order</b>
							</div>	
							<br>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Order :</b></span>
								<input type="text"  id ="no_order" style="text-align: center;width:20%" readonly  >
								<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="mode"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								&nbsp;
								<input type="hidden"  id="jenis_po" style="margin-bottom:0px;" value="1"  onclick='CekStatus(this);'> &nbsp;<b>
								<input type="hidden" id="jenis"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>						
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tanggal :</b></span>
								<input type="text"  id ="tanggal" style="text-align: center;width:20%" readonly  >
							</div>
							<div  id="tampil_jo" style="display:none;">
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. JO :</b></span>
									<input type="text"  id ="no_jo" style="text-align: center;width:20%"  readonly >
									<button class="btn btn-block btn-primary" id="btn_jox"
										style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-3px" type="button" 
										onClick="javascript:TampilJO()">
										<span class="glyphicon glyphicon-search"></span>
									</button>
									<input type="hidden" id="id_jo"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />							
								</div>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Customer :</b></span>
								<input type="text"  id ="nama_cust" style="text-align: left;width:80%" readonly  >
								<div  id="btn_cust" style="display:none;margin">
								<button class="btn btn-block btn-primary" id="btn_custx"
									style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-5px" type="button" 
									onClick="javascript:TampilCust()">
									<span class="glyphicon glyphicon-search"></span>
								</button>
								</div>
								<input type="hidden" id="id_cust"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Asal :</b></span>
								<select id="id_asal" onchange="CekRate()" style="width: 80%;padding:4px">
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
								<select id="id_tujuan" onchange="CekRate()" style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_kota_tr where status = '1' order by nama_kota  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_kota'];?>" ><?php echo $d1['nama_kota'];?></option>
									<?php }?>
								</select>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Penerima :</b></span>								
								<input type="text" id="penerima" value="" style="text-align: left;width:80%;"   >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Alamat :</b></span>								
								<textarea name="alamat_penerima" id="alamat_penerima"
								style="resize:none;width: 80%; height: 55px; font-size: 11px; line-height: 12px; 
								border: 1px solid #4; padding: 5px;" ></textarea>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Nama Barang :</b></span>								
								<input type="text" id="nama_barang" value="" style="text-align: left;width:80%;"   >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Qty :</b></span>								
								<input type="text" id="qty" onkeypress="return isNumber(event)" value="" style="text-align: center;width:20%;"   >	
								<select id="unit" name ="unit"  style="width: 155px;padding:4.5px" >							
									<?php
									$tampil1="select * from m_paket order by nama_paket  ";
									$hasil1=mysqli_query($koneksi, $tampil1);       
									while ($data1=mysqli_fetch_array($hasil1)){?>
									<option value="<?php echo $data1['nama_paket'];?>" ><?php echo $data1['nama_paket'];?></option>
									<?php }?>
								</select>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Berat :</b></span>								
								<input type="text" id="berat" value="0" style="text-align: right;width:20%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  > Kg	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Vol :</b></span>								
								<input type="text" id="vol" value="0" style="text-align: right;width:20%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  > M3	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Biaya Kirim :</b></span>								
								<input type="text" id="biaya_kirim" value="0" style="text-align: right;width:20%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Remarks :</b></span>								
								<input type="text" id="ket" value="" style="text-align: left;width:80%;"   >	
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
	
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
