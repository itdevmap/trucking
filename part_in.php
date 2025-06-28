<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
//include "lib.php";

$pq = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='12' ");
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
	$paging='15';
	$hal='1';
	$field = 'Description';
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
			var field = $("#field").val();
			$.get("ajax/part_crud.php", {
				tgl1:tgl1, 
				tgl2:tgl2, 
				field:field,
				paging:paging,
				cari:cari,
				hal:hal,
				type:"Read_In" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}	
		function TampilPart(){	
			var cari = $("#cari_part").val();
			cari='';
			$.get("ajax/part_crud.php", {cari:cari, type:"ListPart_In" }, function (data, status) {
				$(".tampil_part").html(data);
			});
			$('#DataPart').modal('show');
		}
		function ListPart() {		
			var cari = $("#cari_part").val();
			$.get("ajax/part_crud.php", {cari:cari, type:"ListPart_In" }, function (data, status) {
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
					$("#unit").val(data.unit);
				}
			);
			$("#DataPart").modal("hide");
		}
		function TampilData() 
		{
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth()+1; 
			var yyyy = today.getFullYear();
			var jam = today.getHours();
			var menit = today.getMinutes();
			if(dd<10){
				dd='0'+dd
			} 
			if(mm<10){
				mm='0'+mm
			} 	
			var today = dd+'-'+mm+'-'+yyyy;
			$("#tanggal").val(today);
			$("#id_part").val('');
			$("#nama_part").val('');
			$("#no_po").val('');
			$("#qty").val('');
			$("#unit").val('');
			$("#mode").val('Add');
			$('#Data').modal('show');
		}
		function AddData() {	
			var qty = $("#qty").val();
			if(!$("#tanggal").val()){
				alert("Tanggal harus diisi !..");
			}
			else if(!$("#id_part").val()){
				alert("Description harus diisi !..");
			}
			else if(!$("#no_po").val()){
				alert("No PO harus diisi !..");
			}
			else if(qty <= 0){
				alert("Qty harus diisi !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id = $("#id").val();
					var id_part = $("#id_part").val();
					var tanggal = $("#tanggal").val();
					var no_po = $("#no_po").val();
					var qty = $("#qty").val();
					var mode = $("#mode").val();
					$.post("ajax/part_crud.php", {
						id_part:id_part,
						tanggal:tanggal,
						no_po:no_po,
						qty:qty,
						id:id,
						mode:mode,
						type : "Add_In"
						}, function (data, status) {
						alert(data);
						$("#Data").modal("hide");	
						ReadData(1);
					});
				}
			}	
		}
		function DelData(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/part_crud.php", {
						id: id, type:"Del_In"
					},
					function (data, status) {
						 ReadData(1);
					}
				);
			}
		}
		function Download() 
		{
			var tgl1 = $("#tgl1").val();
			var tgl2 = $("#tgl2").val();	
			var cari = $("#search_name").val();
			var paging = $("#paging").val();	
			var field = $("#field").val();
			var id = tgl1+'|'+tgl2+'|'+field+'|'+cari;
			var idx = btoa(id);
			var win = window.open('part_in_excel.php?id='+idx);
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Spare Part</b></font></h1></li>					
			</ol>
			<br>
			
			<div class="col-md-12" style="width:99%;border:0px solid #ddd;padding:5px">					
				<div style="width:99%;border-bottom:2px solid #83a939;background:none;margin-left:-5px;margin-top:-5px;margin-bottom:-9px" class="input-group">	
						<?php
							$xy1="$mode|$id_joc";
							$xy1=base64_encode($xy1);
							$link1 = "part.php?id=$xy1";
							$link2 = "part_in.php?id=$xy1";
							$link3 = "part_out.php?id=$xy1";
						?>
					<div id="tabs5" >
						<ul> 
							<li ><?php echo "<a href=$link1>"; ?><span><b>Data Stok </b></span></a></li> 
							<li id="current"><?php echo "<a href=$link2>"; ?><span><b>Data In</b></span></a></li>
							<li ><?php echo "<a href=$link3>"; ?><span><b>Data Out</b></span></a></li>
						</ul>
					</div>	
				</div>					
			</div>	
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
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Filter By</b></span>
						<select size="1" id="field"  onchange="ReadData(1)" name="field" style="padding:4px;margin-right:2px;width: 85px">
							<option>Item Number</option>
							<option>Description</option>
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
							<?php if ($m_add == '1'){
								$xy1="Add|";
								$xy1=base64_encode($xy1);?>
								<button class="btn btn-block btn-success" 
								style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:2px" type="button"  title = "Created Order"
								onClick="javascript:TampilData()">
								<span class="fa  fa-plus-square"></span>
								<b>Add New</b>
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Spare Part In</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tanggal :</b></span>
								<input type="text" id="tanggal"  value="" style="padding:4px;text-align: center;width:85px;border:1px solid rgb(169, 169, 169);background:#eee"  />
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Item Description :</b></span>
								<input type="text"  id ="nama_part" name="nama" style="text-align: left;width:70%" readonly  >
								<button class="btn btn-block btn-primary" id="cost"
									style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-1px" type="button" 
									onClick="javascript:TampilPart()">
									<span class="glyphicon glyphicon-search"></span>
								</button>	
								<input type="hidden" id="id_part"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="mode"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
							</div>
						
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. PO :</b></span>
								<input type="text" id="no_po"  value="" style="text-transform: uppercase;
								text-align: left;width:76%;border:1px solid rgb(169, 169, 169)"  />	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Qty :</b></span>
								<input type="text" id="qty"  value="" style="text-align: center;width:15%;border:1px solid rgb(169, 169, 169)" 
								onkeypress="return isNumber(event)" />	
								<input type="text" id="unit"  value="" style="text-transform: uppercase;
								text-align: left;width:15%;border:1px solid rgb(169, 169, 169)" readonly  />
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddData()">
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
									<input type="text"  id ="cari_part" name="cari_part" value="<?php echo $cari_part; ?>" 
									style="text-align: left;width:200px" onkeypress="ListPart()" >
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
