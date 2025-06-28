<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
//include "lib.php";

$pq = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='25' ");
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
	$hal = $_POST['hal'];
	$search_name1 = $_POST['search_name1'];
	$search_name2 = $_POST['search_name2'];
	$field1 = $_POST['field1'];
	$field2 = $_POST['field2'];
	$paging = $_POST['paging'];
}
else
{	
	$paging='15';
	$hal='1';
	$field1 = 'Asal';
	$field2 = 'Tujuan';
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
			//return (((sign)?'':'-') + '' + num + '.' + cents);
			return (((sign)?'':'-') + '' + num );
		}
		function isNumber(evt) {
			evt = (evt) ? evt : window.event;
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode > 31 && (charCode < 46 || charCode > 57)) {
				return false;
			}
			return true;
		}
		$(document).ready(function () {
			var hal = $("#hal").val();
			ReadData(hal);
		});
		function ReadData(hal) {
			
			var cari1 = $("#search_name1").val();
			var cari2 = $("#search_name2").val();
			var field1 = $("#field1").val();
			var field2 = $("#field2").val();
			var paging = $("#paging").val();	
			$.get("ajax/price_crud.php", {paging:paging,cari1:cari1, cari2:cari2, field1:field1, field2:field2, hal:hal, type:"Read" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}
		function GetData(id) {
			$("#id").val(id);	
			$.post("ajax/price_crud.php", {
					id: id, type:"Detil_Data"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_asal").val(data.id_asal);
					$("#id_tujuan").val(data.id_tujuan);
					$("#jenis_mobil").val(data.jenis_mobil);
					$("#rate").val(Desimal(data.rate));
					$("#uj").val(Desimal(data.uj));
					$("#ritase").val(Desimal(data.ritase));
					$("#stat").val(data.status);
					$("#mode").val('Edit');
				}
			);
			$("#Data").modal("show");
		}

		function add() {	
			var r = confirm("Are you sure ?...");
			if (r == true) {	
				var id = $("#id").val();
				var id_asal = $("#id_asal").val();
				var id_tujuan = $("#id_tujuan").val();
				var rate = $("#rate").val();
				var jenis_mobil = $("#jenis_mobil").val();
				var uj = $("#uj").val();
				var ritase = $("#ritase").val();
				var stat = $("#stat").val();
				var mode = $("#mode").val();
				var hal = $("#hal").val();
				$.post("ajax/price_crud.php", {
					id:id,
					id_asal:id_asal,
					id_tujuan:id_tujuan,
					jenis_mobil:jenis_mobil,
					rate:rate,
					ritase:ritase,
					uj:uj,
					stat:stat,
					mode:mode,
					stat:stat,
					type : "Add_Data"
					}, function (data, status) {
					alert(data);
					$("#Data").modal("hide");				
					ReadData(hal);
				});
			}
		}

		function Tampil(){	
			ReadData('1');
		}
		function TampilData() 
		{
			$("#rate").val('');
			$("#uj").val('');
			$("#ritase").val('');
			$("#mode").val('Add');
			$('#Data').modal('show');
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Price List</b></font></h1></li>					
			</ol>
			<br>
		
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
					</div>
					<br>	
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Filter By :</b></span>
						<select size="1" id="field1"  name="field1" style="padding:4px;margin-right:2px;width: 85px">
							<option>Asal</option>
							<option>Tujuan</option>
							<option>Jenis</option>
							<option value="<?php echo $field1; ?>" selected><?php echo $field1; ?></option>
						</select>
						<input type="text"  id ="search_name1" name="search_name1" value="<?php echo $search_name1; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
					</div>	
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"></span>
						<select size="1" id="field2"  name="field2" style="padding:4px;margin-right:2px;width: 85px">
							<option>Asal</option>
							<option>Tujuan</option>
							<option>Jenis</option>
							<option value="<?php echo $field2; ?>" selected><?php echo $field2; ?></option>
						</select>
						<input type="text"  id ="search_name2" name="search_name2" value="<?php echo $search_name2; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
						<input type="hidden"  id ="hal" name="hal" value="<?php echo $hal; ?>" style="text-align: left;width:5%"  >
						
						<button class="btn btn-block btn-primary" 
								style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:2px;padding-top:6px;padding-bottom:6px" type="submit" 
								>
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
							<?php if ($m_add == '1'){?>
							<button class="btn btn-block btn-success" 
								style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:2px" type="button" 
								onClick="javascript:TampilData()">
								<span class="fa  fa-plus-square"></span>
								<b>Add New</b>
							</button>	
							<?php }?>								
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Rate</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Asal :</b></span>
								<select size="1" id="id_asal"  style="padding:4px;margin-right:2px;width:75%">
									<?php 
									$t1="select * from m_kota_tr where status = '1'  order by nama_kota";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){  
									?>
									<option value="<?php echo $d1['id_kota'];?>"><?php echo $d1['nama_kota'];?></option>
									<?php }?>
								</select>	
								<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="mode"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tujuan :</b></span>
								<select size="1" id="id_tujuan"  style="padding:4px;margin-right:2px;width:75%">
									<?php 
									$t1="select * from m_kota_tr where status = '1'  order by nama_kota";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){  
									?>
									<option value="<?php echo $d1['id_kota'];?>"><?php echo $d1['nama_kota'];?></option>
									<?php }?>
								</select>		
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Type :</b></span>
								<select size="1" id="jenis_mobil"  style="padding:4px;margin-right:2px;width:75%">
									<?php 
									$t1="select * from m_jenis_mobil_tr where status = '1'  order by nama";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){  
									?>
									<option value="<?php echo $d1['nama'];?>"><?php echo $d1['nama'];?></option>
									<?php }?>
								</select>		
							</div>		
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Price :</b></span>
								<input type="text" id="rate" value="" style="text-align: right;width:20%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Uang Jalan :</b></span>
								<input type="text" id="uj" value="" style="text-align: right;width:20%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Ritase :</b></span>
								<input type="text" id="ritase" value="" style="text-align: right;width:20%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Status :</b></span>
								<select id="stat"  style="width: 20%;">
									<option value="1" >Active</option>
									<option value="0" >In Active</option>
								</select>						
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="add()">
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
	
	
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
