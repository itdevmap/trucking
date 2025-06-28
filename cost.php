<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
//include "lib.php";

$pq = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='13' ");
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
			var hal = $("#hal").val();
			ReadData(hal);
		});
		function ReadData(hal) {
			
			var cari = $("#search_name").val();
			var paging = $("#paging").val();	
			$.get("ajax/cost_crud.php", {paging:paging,cari:cari,hal:hal, type:"Read" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}
		function GetData(id) {
			$("#id").val(id);	
			$.post("ajax/cost_crud.php", {
					id: id, type:"detil"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#cost_name").val(data.nama_cost);
					$("#stat").val(data.status);
					$("#sim").val(data.sapitemcode_sim);
					$("#ama").val(data.sapitemcode_ama);
					$("#ptl").val(data.sapitemcode_ptl);
					$("#aa").val(data.sapitemcode_aa);
					$("#mode").val('Edit');
				}
			);
			$("#Data").modal("show");
		}

		function add() {	
			if(!$("#cost_name").val()){
				alert("Cost Name must fill !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id = $("#id").val();
					var cost_name = $("#cost_name").val();
					var stat = $("#stat").val();
					var st = $("#st").val();
					var sim = $("#sim").val();
					var ama = $("#ama").val();
					var ptl = $("#ptl").val();
					var aa = $("#aa").val();
					var mode = $("#mode").val();
					var hal = $("#hal").val();
					
					$.post("ajax/cost_crud.php", {
						id:id,
						cost_name:cost_name,
						mode:mode,
						st:st,
						stat:stat,
						sim:sim,
						ama:ama,
						ptl:ptl,
						aa:aa,
						type : "add"
						}, function (data, status) {
						alert(data);
						$("#Data").modal("hide");				
						ReadData(hal);
					});
				}
			}	
		}

		function Tampil(){	
			ReadData('1');
		}
		function TampilData() 
		{
			$("#cost_name").val('');
			$("#sim").val('');
			$("#ama").val('');
			$("#ptl").val('');
			$("#aa").val('');
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Biaya</b></font></h1></li>					
			</ol>
			<br>
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
					</div>
					<br>					
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Find Biaya :</b></span>
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Cost</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Nama Biaya :</b></span>
								<input type="text" id="cost_name"  value="" style="text-transform: uppercase;
								text-align: left;width:75%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="mode"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Code SIM :</b></span>
								<input type="text" id="sim"  value="" style="text-transform: uppercase;
								text-align: left;width:75%;border:1px solid rgb(169, 169, 169)" />	
							</div>		
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Code AMA :</b></span>
								<input type="text" id="ama"  value="" style="text-transform: uppercase;
								text-align: left;width:75%;border:1px solid rgb(169, 169, 169)" />	
							</div>		
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Code PTL :</b></span>
								<input type="text" id="ptl"  value="" style="text-transform: uppercase;
								text-align: left;width:75%;border:1px solid rgb(169, 169, 169)" />	
							</div>		
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Code AA :</b></span>
								<input type="text" id="aa"  value="" style="text-transform: uppercase;
								text-align: left;width:75%;border:1px solid rgb(169, 169, 169)" />	
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
