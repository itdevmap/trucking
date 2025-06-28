<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
//include "lib.php";

$pq = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='3' ");
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
	$field = 'No SJ';
	$field1 = 'No SJ';
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
			$.get("ajax/deliv_crud.php", {
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
		function Delete(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/deliv_crud.php", {
						id: id, type:"Del_SJ"
					},
					function (data, status) {
						 ReadData();
					}
				);
			}
		}
		function TampilJO(){	
			var cari = $("#cari_data").val();
			$.get("ajax/deliv_crud.php", {cari:cari,  type:"ListOrder_FCL" }, function (data, status) {
				$(".tampil_jo").html(data);
			});
			$('#DataJO').modal('show');
		}
		function ListJO() {	
			var cari = $("#cari_data").val();
			$.get("ajax/deliv_crud.php", {cari:cari, type:"ListOrder_FCL" }, function (data, status) {
				$(".tampil_jo").html(data);
			});
		}
		function PilihOrder(id) {	
			$("#id_detil").val(id);	
			$.post("ajax/fcl_crud.php", {
					id: id, type:"Detil_Data"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#no_jo").val(data.no_jo);
					$("#no_cont").val(data.no_cont);
					$("#id_asal").val(data.id_asal);
					$("#id_tujuan").val(data.id_tujuan);
					$("#jenis_mobil").val(data.jenis_mobil);
					$("#penerima").val(data.penerima);
					$("#biaya_kirim").val(Rupiah(data.biaya_kirim));
					CekRate();	
				}
			);
			$("#DataJO").modal("hide");
		}
		function CekRate()
		{
			var id_asal = $("#id_asal").val();
			var id_tujuan = $("#id_tujuan").val();
			var jenis_mobil = $("#jenis_mobil").val();
			$("#uj").val('');	
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
		function TampilData() 
		{
			document.getElementById("tampil_jo").style.display = 'inline';
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
			$("#id_detil").val('');	
			$("#no_jo").val('');	
			$("#no_cont").val('');
			$("#barang").val('');
			$("#penerima").val('');
			$("#ket").val('');
			$("#no_seal").val('');
			$("#berat").val('');
			$("#vol").val('');
			$("#mode").val('Add');
			$('#Data').modal('show');
		}
		function GetFCL(id){	
			document.getElementById("tampil_jo").style.display = 'none';
			$("#id_sj").val(id);
			$.post("ajax/deliv_crud.php", {
					id: id, type:"Detil_SJ_FCL"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#no_sj").val(data.no_sj);
					$("#id_detil").val(data.id_detil);
					$("#tanggal").val(changeDateFormat(data.tgl_sj));
					$("#no_cont").val(data.no_cont);
					$("#no_seal").val(data.no_seal);
					$("#barang").val(data.barang);
					$("#berat").val(Desimal(data.berat));
					$("#vol").val(Desimal(data.vol));
					$("#id_asal").val(data.id_asal);
					$("#id_tujuan").val(data.id_tujuan);
					$("#jenis_mobil").val(data.jenis_mobil);
					$("#penerima").val(data.penerima);
					$("#id_supir").val(data.id_supir);	
					$("#uj").val(Rupiah(data.uj));	
					$("#ritase").val(Rupiah(data.ritase));	
					$("#ket").val(data.ket);		
					$("#mode").val('Edit');		
				}
			);	
			$('#Data').modal('show');
		}
		function UpdateFCL() {	
			var tanggal = $("#tanggal").val();
			var id_detil = $("#id_detil").val();
			if(tanggal == '' )
			{
				alert("Tanggal harus diisi !..");
			}
			else if(id_detil == '')
			{
				alert("No. Order harus diisi !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id_sj = $("#id_sj").val();	
					var id_detil = $("#id_detil").val();					
					var tanggal = $("#tanggal").val();
					var id_asal = $("#id_asal").val();
					var id_tujuan = $("#id_tujuan").val();
					var barang = $("#barang").val();
					var no_seal = $("#no_seal").val();
					var penerima = $("#penerima").val();
					var id_mobil = $("#id_mobil").val();
					var jenis_mobil = $("#jenis_mobil").val();
					var id_supir = $("#id_supir").val();
					var uj = $("#uj").val();
					var ritase = $("#ritase").val();
					var ket = $("#ket").val();
					var no_cont = $("#no_cont").val();
					var berat = $("#berat").val();
					var vol = $("#vol").val();
					var mode = $("#mode").val();
					$.post("ajax/deliv_crud.php", {
						id_sj:id_sj,
						id_detil:id_detil,
						tanggal:tanggal,
						no_cont:no_cont,
						barang:barang,
						no_seal:no_seal,
						berat:berat,
						vol:vol,
						id_asal:id_asal,
						id_tujuan:id_tujuan,
						penerima:penerima,
						id_mobil:id_mobil,
						jenis_mobil:jenis_mobil,
						id_supir:id_supir,
						uj:uj,
						ket:ket,
						mode:mode,
						ritase:ritase,
						type : "UpdateFCL"
						}, function (data, status) {
						alert(data);
						$("#Data").modal("hide");				
						ReadData(1);
					});
				}
			}	
		}
		function List_Lain(id) {
			$("#id_lain").val(id);
			var mode = $("#mode").val();
			$.get("ajax/deliv_crud.php", {mode:mode, id:id,  type:"List_Lain" }, function (data, status) {
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
			var id_sj = $("#id_lain").val();
			var id = $("#id_biaya").val();
			var id_cost = $("#id_cost").val();
			var biaya = $("#biaya_lain").val();
			var mode = $("#mode_biaya").val();
			$.post("ajax/deliv_crud.php", {
				id_sj:id_sj,
				id:id,
				id_cost:id_cost,
				biaya:biaya,
				mode:mode,
				type : "Add_Lain"
				}, function (data, status) {
				alert(data);
				
				var id = $("#id_lain").val();
				var mode = $("#mode").val();
				$.get("ajax/deliv_crud.php", {mode:mode, id:id,  type:"List_Lain" }, function (data, status) {
					$(".tampil_lain").html(data);
				});
				ReadData(1);
				$("#DataLain").modal("hide");				
				
			});
		}	
		function GetLain(id) {
			$("#id_biaya").val(id);	
			$.post("ajax/deliv_crud.php", {
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
				$.post("ajax/deliv_crud.php", {
						id: id, type:"Del_Lain"
					},
					function (data, status) {
						var id = $("#id_lain").val();
						var mode = $("#mode").val();
						$.get("ajax/deliv_crud.php", {mode:mode, id:id,  type:"List_Lain" }, function (data, status) {
							$(".tampil_lain").html(data);
						});
				
						 ReadData(1);
					}
				);
			}
		}
		function Close(id) {
			var hal = $("#hal").val();
			var conf = confirm("Are you sure to Close ?");
			if (conf == true) {
				$.post("ajax/deliv_crud.php", {
						id: id,type:"Close"
					},
					function (data, status) {
						ReadData(hal);
					}
				);
			}
		}
		function Open(id) {
			var hal = $("#hal").val();
			var conf = confirm("Are you sure to Open ?");
			if (conf == true) {
				$.post("ajax/deliv_crud.php", {
						id: id,type:"Open"
					},
					function (data, status) {
						ReadData(hal);
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
			var win = window.open('sj_excel.php?id='+idx);
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Surat Jalan</b></font></h1></li>					
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
							<option>No SJ</option>
							<option>Asal</option>
							<option>Tujuan</option>
							<option>Supir</option>
							<option>No Polisi</option>
							<option>Jenis Mobil</option>
							<option>No Container</option>
							<option value="<?php echo $field; ?>" selected><?php echo $field; ?></option>
						</select>
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
					</div>
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"></span>
						<select size="1" id="field1"  onchange="ReadData(1)" name="field1" style="padding:4px;margin-right:2px;width: 85px">
							<option>No SJ</option>
							<option>Asal</option>
							<option>Tujuan</option>
							<option>Supir</option>
							<option>No Polisi</option>
							<option>Jenis Mobil</option>
							<option>No Container</option>
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
								style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:2px" type="button"  title = "SJ FCL"
								onClick="javascript:TampilData()">
								<span class="fa  fa-plus-square"></span>
								<b>Created SJ</b>
								</button>	
								<!--
								<button class="btn btn-block btn-primary" 
								style="margin:0px;margin-left:-1px;margin-bottom:0px;border-radius:2px" type="button"  title = "SJ LCL"
								onClick="window.location.href = 'deliv_data.php?id=<?php echo $xy1; ?>' ">
								<span class="fa  fa-plus-square"></span>
								<b>Created SJ (LCL)</b>
								</button>
								-->
							<?php }?>	
							<!--
							<button class="btn btn-block btn-warning" 
								style="margin:0px;margin-left:-1px;margin-bottom:0px;border-radius:2px" type="button"  title = "SJ FCL"
								onClick="javascript:Download()">
								<span class="fa fa-file-text"></span>
								<b>Download Data</b>
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
	
	<div class="modal fade" id="Data"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">	
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">							
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Surat Jalan</b>
							</div>	
							<br>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. SJ :</b></span>
								<input type="text"  id ="no_sj" style="text-align: center;width:22%" readonly  >
								<input type="hidden" id="id_sj"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="mode"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id_detil"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>						
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tanggal :</b></span>
								<input type="text"  id ="tanggal" style="text-align: center;width:22%" readonly  >
							</div>
							<div  id="tampil_jo" style="display:none;">
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Order :</b></span>
									<input type="text"  id ="no_jo" style="text-align: center;width:22%"  readonly >
									<button class="btn btn-block btn-primary" id="btn_jox"
										style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-3px" type="button" 
										onClick="javascript:TampilJO()">
										<span class="glyphicon glyphicon-search"></span>
									</button>
									
								</div>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Nama Barang :</b></span>								
								<input type="text" id="barang" value="" style="text-transform: uppercase;text-align: left;width:80%;"   >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Berat :</b></span>
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
								<textarea id="penerima"
								style="resize:none;width: 80%; height: 70px; font-size: 11px; line-height: 12px; 
								border: 1px solid #4; padding: 5px;"  ></textarea>	
							</div>							
							<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Polisi :</b></span>
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
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Jenis :</b></span>
								<select id="jenis_mobil" onchange="CekRate()" style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_jenis_mobil_tr where status = '1' order by nama  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['nama'];?>" ><?php echo $d1['nama'];?></option>
									<?php }?>
								</select>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Supir :</b></span>
								<select id="id_supir"  style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_supir_tr where status = '1' order by nama_supir  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_supir'];?>" ><?php echo $d1['nama_supir'];?></option>
									<?php }?>
								</select>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Uang Jalan :</b></span>								
								<input type="text" id="uj" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Ritase :</b></span>								
								<input type="text" id="ritase" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  readonly>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Remarks :</b></span>
								<input type="text" id="ket" value="" style="text-align: left;width:80%;"   >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="UpdateFCL()">
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
									&nbsp;&nbsp;&nbsp;<b>Find No Order/No Container : </b>
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
									<input type="hidden"  id ="id_lain" name="id" value=""   >
								</span>								
							</div>	
							<?php if($m_add == '1'){?>
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
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
