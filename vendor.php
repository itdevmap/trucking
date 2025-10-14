<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
include "lib.php";

$pq = mysqli_query($koneksi, "SELECT * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='9' ");
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
	$field = $_POST['field'];
	$search_name = $_POST['search_name'];
	$paging = $_POST['paging'];
}
else
{	
	$paging='25';
	$hal='1';
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
			$("#tanggal").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			var hal = $("#hal").val();
			ReadData(hal);
		});
		function ReadData(hal) {
			var search_name = $("#search_name").val();
			var field = $("#field").val();
			var paging = $("#paging").val();
			$.get("ajax/vendor_crud.php", {field:field, paging:paging,search_name:search_name,hal:hal, type:"read" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}
		function Tampil(){	
			var hal = $("#hal").val();
			ReadData(hal);
		}
		function TampilData() 
		{
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
			$("#nama_vendor").val('');
			$("#unit").val('');
			$("#caption").val('');
			$("#kontak").val('');
			$("#no_npwp").val('');
			$("#alamat").val('');
			$("#batas").val('');
			$("#term").val('0');
			$("#telp").val('');
			$("#email").val('');
			$("#status").val('1');
			$("#mode").val('Add');
			$('#Data').modal('show');
		}
		function AddData() {
			if(!$("#nama_vendor").val()){
				alert("Vendor Name must fill !..");
			}
			else
			{
				var id_vendor = $("#id_vendor").val();
				var nama_vendor = $("#nama_vendor").val();
				var caption = $("#caption").val();
				var kontak = $("#kontak").val();
				var alamat = $("#alamat").val();
				var telp = $("#telp").val();
				var stat = $("#stat").val();
				var ppn = $("#ppn").val();
				var mode = $("#mode").val();
				var email = $("#email").val();
				var tanggal = $("#tanggal").val();
				$.post("ajax/vendor_crud.php", {
					id_vendor:id_vendor,
					nama_vendor:nama_vendor,
					caption:caption,
					kontak:kontak,
					alamat:alamat,
					telp:telp,
					email:email,
					tanggal:tanggal,
					mode:mode,
					stat:stat,
					ppn:ppn,
					type : "AddData"
					}, function (data, status) {
					alert(data);
					$("#Data").modal("hide");				
					ReadData(1);
				});
			}	
		}
		function GetData(id) {
			$("#id_vendor").val(id);	
			$.post("ajax/vendor_crud.php", {
					id: id, type:"DetilData"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#nama_vendor").val(data.nama_vendor);
					$("#caption").val(data.caption);
					$("#kontak").val(data.kontak);
					$("#alamat").val(data.alamat);
					$("#telp").val(data.telp);					
					$("#email").val(data.email);
					$("#stat").val(data.status);
					$("#ppn").val(data.ppn);
					$("#mode").val('Edit');							
				}
			);
			$("#Data").modal("show");
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
		
		
		<form method="post" name ="myform" action="vendor.php?action=cari" class="form-horizontal" > 
		<div class="content-wrapper" style="min-height:750px">
			<br>
			<ol class="breadcrumb">
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Vendor</b></font></h1></li>					
			</ol>
			<br>
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
					</div>
					<br>					
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Find Vendor :</b></span>
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
						style="text-align: left;margin-left:-5px;width:200px" onkeypress="ReadData(1)" >
						<input type="hidden"  id ="hal" name="hal" value="<?php echo $hal; ?>" style="text-align: left;width:5%"  >						
						<button class="btn btn-block btn-primary" 
							style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:2px;padding:6px" type="submit" 
							onClick="window.location.href = 'paket_data.php?id=<?php echo $xy1; ?>' ">
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</div>
					<br>	
				</div>
            </div>
			
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;background:#fff !important;">	
					<div style="width:100%;background: #fff;" class="input-group" >
						<span class="input-group-addon" style="width:50%;text-align:left;padding:0px;background:#fff;">
							<?php if ($m_add == '1'){?>
							<!-- <button class="btn btn-block btn-success" 
								style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:3px" type="button" 
								onClick="javascript:TampilData()">
								<span class="fa  fa-plus-square"></span>
								<b>Add New</b>
							</button>	 -->
							<?php }?>
						</span>
						<span class="input-group-addon" style="width:50%;text-align:right;padding:0px;background:#fff">
						Row Page :&nbsp;
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Vendor</b>
							</div>	
							<br>

							<input type="hidden" id="id_vendor"/>
							<input type="hidden" id="mode"/>
							<input type="hidden" id="tanggal">

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Code :</b></span>								
								<input type="text" id="caption"  maxlength="7" value="" style="text-transform: uppercase;text-align: left;width:20%;border:1px solid rgb(169, 169, 169)" readonly/>	
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Vendor Name :</b></span>
								<input type="text" id="nama_vendor"  value="" style="text-transform: uppercase;
								text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" readonly/>	
							</div>	
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Contact Person :</b></span>								
								<input type="text" id="kontak"  value="" style="text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" readonly/>	
							</div>		
											
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Address :</b></span>
								<textarea name="alamat" id="alamat"
								style="resize:none;width: 80%; height: 84px; font-size: 11px; line-height: 12px; 
								border: 1px solid #444; padding: 5px;" ></textarea>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Phone :</b></span>
								<input type="text" id="telp"  value="" style="text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Email :</b></span>
								<input type="text" id="email"  value="" style="text-align: left;width:80%;border:1px solid rgb(169, 169, 169)" />	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>PPN :</b></span>
								<input type="number" id="ppn"  value="0" style="text-align: right;width:40%;border:1px solid rgb(169, 169, 169)" />	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Status :</b></span>
								<select id="stat"  style="width: 80%;">
									<option value="1" >Active</option>
									<option value="0" >In Active</option>
								</select>						
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
	
	

	

	
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
