<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
//include "lib.php";

$pq = mysqli_query($koneksi,"select * from m_role_akses where id_role = '$id_role'  and id_menu ='40' ");
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
	$field1 = $_POST['field1'];
	$search_name = $_POST['search_name'];
	$search_name1 = $_POST['search_name1'];
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
	$stat = 'All';
	$field = 'No PR';
	$field1 = 'Job No';
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
			$("#tgl_ambil").datepicker({
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
			var cari1 = $("#search_name1").val();
			var stat = $("#stat").val();
			var paging = $("#paging").val();
			var field = $("#field").val();
			var field1 = $("#field1").val();
			$.get("ajax/bc_crud.php", {
				tgl1:tgl1, 
				tgl2:tgl2, 
				field:field,
				paging:paging,
				cari:cari,
				cari1:cari1,
				field1:field1,
				stat:stat,
				hal:hal,
				type:"Read" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}		
		function TampilJO(){	
			var cari = $("#cari_data").val();
			var filter = $("#filter").val();
			var kode = $("#kode").val();
			$.get("ajax/bc_crud.php", {kode:kode, cari:cari,  filter:filter, type:"ListJO" }, function (data, status) {
				$(".tampil_jo").html(data);
			});
			$('#DataJO').modal('show');
		}
		function ListJO() {	
			var cari = $("#cari_data").val();
			var filter = $("#filter").val();
			var kode = $("#kode").val();
			$.get("ajax/bc_crud.php", {kode:kode, cari:cari, filter:filter, type:"ListJO" }, function (data, status) {
				$(".tampil_jo").html(data);
			});
		}
		function PilihJO(id) {	
			$.post("ajax/bc_crud.php", {
					id: id, type:"Detil_JO"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#nama_cust").val(data.nama_cust);
					$("#id_jo_cont").val(data.id_cont);
					$("#jo_no").val(data.jo_no);
					$("#no_ref").val(data.kode+'-'+data.no_ref);
					$("#no_cont").val(data.no_cont);
					$("#feet").val(data.feet);
					//alert(data.nama_cust);
					
				}
			);
			$("#DataJO").modal("hide");
		}	
		function Delete(id) {
			var hal = $("#hal").val();
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/bc_crud.php", {
						id: id,type:"Delete"
					},
					function (data, status) {
						ReadData(hal);
					}
				);
			}
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
			$("#tgl_bc").val(today);
			//$("#tgl_ambil").val(today);
			$("#mode").val('Add');
			$("#alamat_ambil").val('');
			$("#no_bc").val('-- Auto --');
			$("#id_bc").val('');
			$("#id_jo").val('');
			$("#no_ref").val('');
			$("#jo_no").val('');
			$("#no_cont").val('');
			$("#tgl_ambil").val('');
			$("#feet").val('');
			$("#nama_cust").val('');
			$('#Data').modal('show');
		}
		function AddData() {	
			var tgl_bc = $("#tgl_bc").val();
			var tgl_ambil = $("#tgl_ambil").val();
			var alamat_ambil = $("#alamat_ambil").val();
			var id_jo_cont = $("#id_jo_cont").val();
			if(tgl_bc == '')
			{
				alert("Date harus diisi !..");
			}
			else if(id_jo_cont == '' )
			{
				alert("No Job harus diisi!..");
			}	
			else if(tgl_ambil == '' )
			{
				alert("Tanggal Pengambilan harus diisi!..");
			}
			else if(alamat_ambil == '' )
			{
				alert("Alamat Pengambilan harus diisi!..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {
					var id_jo_cont = $("#id_jo_cont").val();
					var tgl_bc = $("#tgl_bc").val();
					var id_bc = $("#id_bc").val();
					var id_kota = $("#id_kota").val();
					var tgl_ambil = $("#tgl_ambil").val();
					var alamat_ambil = $("#alamat_ambil").val();
					var ket = $("#ket").val();
					var mode = $("#mode").val();
					$.post("ajax/bc_crud.php", {
						id_jo_cont:id_jo_cont,
						tgl_bc:tgl_bc,
						id_bc:id_bc,
						id_kota:id_kota,
						tgl_ambil:tgl_ambil,
						alamat_ambil:alamat_ambil,
						ket:ket,
						mode:mode,
						type : "Add_Data"
						}, function (data, status) {
						alert(data);	
						var hal = $("#hal").val();				
						ReadData(1);
						$("#Data").modal("hide");
							//CetakKwitansi(data);
					});
				}
			}			
		}
		function GetData(id) {
			
			$("#id_bc").val(id);
			$.post("ajax/bc_crud.php", {
					id: id, type:"Detil_Data"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#tgl_bc").val(changeDateFormat(data.tgl_bc));	
					$("#tgl_ambil").val(changeDateFormat(data.tgl_ambil));
					$("#id_kota").val(data.id_kota);	
					$("#id_jo_cont").val(data.id_jo_cont);
					$("#no_cont").val(data.no_cont);
					$("#alamat_ambil").val(data.alamat_ambil);
					$("#feet").val(data.feet);
					$("#jo_no").val(data.jo_no);
					$("#no_ref").val(data.kode+'-'+data.no_ref);
					$("#no_bc").val(data.no_bc);
					$("#nama_cust").val(data.nama_cust);
					$("#ket").val(data.ket_bc);			
					$("#mode").val('Edit');	
				}
			);
			$("#Data").modal("show");
		}
		function ListDoc(id) {	
			$("#id").val(id);
			$.get("ajax/bc_crud.php", {id:id,  type:"ListDoc" }, function (data, status) {
				$(".tampil_doc").html(data);
				});
			$("#DaftarDoc").modal("show");
		}
		function GetDoc() {
			var id = $("#id").val();
			$("#id_bc1").val(id);
			$.post("ajax/bc_crud.php", {
					id: id, type:"Detil_Data"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#no_bc1").val(data.no_bc);
				}
			);
			$("#DataUpload").modal("show");
		}
		$(document).ready(function (e) {
			$("#form_doc").on('submit',(function(e) {
				e.preventDefault();
				$.ajax({
					url: "upload_bc.php",
					type: "POST",
					data:  new FormData(this),
					contentType: false,
					cache: false,
					processData:false,
					beforeSend : function()
					{				
						$("#err").fadeOut();
					},
					success: function(data)
					{			
						alert("Data Saved..");	
						
						var id = $("#id").val();
						$.get("ajax/bc_crud.php", {id:id,  type:"ListDoc" }, function (data, status) {
							$(".tampil_doc").html(data);
						});
				
						$("#form_doc")[0].reset();	
						$("#DataUpload").modal("hide");
						
					},
					error: function(e) 
					{
						$("#err").html(e).fadeIn();
					} 	        
			   });
			}));
		});
		function DelDoc(id) {
			
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/bc_crud.php", {
						id: id,type:"DelDoc"
					},
					function (data, status) {
						var id = $("#id").val();
						$.get("ajax/bc_crud.php", {id:id,  type:"ListDoc" }, function (data, status) {
							$(".tampil_doc").html(data);
						});
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
		
		<form method="post" name ="myform"  class="form-horizontal" > 
		<div class="content-wrapper" style="min-height:750px">
			<br>
			<ol class="breadcrumb">
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Purchase Request </b></font></h1></li>					
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
					<!--
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
					-->
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Filter By:</b></span>
						<select size="1" id="field"  name="field" style="padding:4px;margin-right:2px;width: 85px">							
							<option>No PR</option>
							<option>JOB No</option>
							<option>Kode Project</option>
							<option>No Ref</option>
							<option>Customer</option>
							<option>No Container</option>
							<option>Tujuan</option>
							<option value="<?php echo $field; ?>" selected><?php echo $field; ?></option>
						</select>
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
					</div>
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"></span>
						<select size="1" id="field1"  name="field1" style="padding:4px;margin-right:2px;width: 85px">							
							<option>No PR</option>
							<option>JOB No</option>
							<option>Kode Project</option>
							<option>No Ref</option>
							<option>Customer</option>
							<option>No Container</option>
							<option>Tujuan</option>
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
								style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:2px" type="button"  title = "Create Data"
								onClick="javascript:TampilData()">
								<span class="fa  fa-plus-square"></span>
								<b>Create PR</b>
								</button>	
							<?php }?>									
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
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Booking PTEJ</b>
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
								<button class="btn btn-block btn-primary"  
									style="padding:6px;margin-top:-2px;border-radius:1px;margin-left:-1px" type="button" 
									onClick="javascript:TampilJO(1)">
									<span class="glyphicon glyphicon-search"></span>
								</button>	
								<input type="hidden"  id ="id_jo_cont" name="id_jo" value="<?php echo $id_jo; ?>" >
								<input type="hidden"  id ="id_bc" name="id_bc" value="<?php echo $id_bc; ?>" >
								<input type="hidden"  id ="mode" name="mode" value="<?php echo $mode; ?>" >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>No Ref :</b></span>
								<input type="text"  id ="no_ref" name="no_ref" value="<?php echo $jo_no; ?>" 
								style="text-align: center;width:22%" readonly  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Customer :</b></span>
								<input type="text"  id ="nama_cust" name="nama_cust" value="<?php echo $nama_cust; ?>" 
								style="text-align: left;width:80%" readonly <?php echo $dis;?> >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>No. Container :</b></span>
								<input type="text"  id ="no_cont" name="" value="<?php echo $nama_cust; ?>" 
								style="text-align: left;width:22%" readonly <?php echo $dis;?> >
								<input type="text"  id ="feet" name="" value="<?php echo $nama_cust; ?>" 
								style="text-align: left;width:10%" readonly <?php echo $dis;?> >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tgl Pengambilan :</b></span>
								<input type="text" id="tgl_ambil"  value="" style="text-align: center;width:22%;border:1px solid rgb(169, 169, 169);background:#eee" readonly />		
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Alamat Pengambilan :</b></span>
								<textarea name="alamat_ambil" id="alamat_ambil"
								style="resize:none;width: 80%; height: 70px; font-size: 11px; line-height: 12px; 
								border: 1px solid #444; padding: 5px;" ></textarea>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Tujuan :</b></span>
								<select id="id_kota"  style="width: 80%;padding:4px">
									<?php
									$tampil1="select * from m_kota_tr order by nama_kota  ";
									$hasil1=mysqli_query($koneksi, $tampil1);       
									while ($data1=mysqli_fetch_array($hasil1)){?>
									<option value="<?php echo $data1['id_kota'];?>" ><?php echo $data1['nama_kota'];?></option>
									<?php }?>
								</select>
							</div>
							<!--
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Keterangan :</b></span>
								<input type="text-align" id="ket"   style="text-align: left;width:80%;"   >
							</div>	
							-->
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
	
	
	<div class="modal fade" id="DataJO"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document" style="width:50%">
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
									&nbsp;&nbsp;&nbsp;<b>Filter By  : </b>
									<select size="1" id="kode" onchange="ListJO()" name="" style="padding:4px;margin-right:0px;width: 85px">	
										<option>All</option>	
										<?php
										$tampil1="select * from m_kode_project order by kode  ";
										$hasil1=mysqli_query($koneksi, $tampil1);       
										while ($data1=mysqli_fetch_array($hasil1)){?>
										<option value="<?php echo $data1['kode'];?>" ><?php echo $data1['kode'];?></option>
										<?php }?>
										
									</select>
									<select size="1" id="filter"  onchange="ListJO()" name="" style="padding:4px;margin-left:-2px;width: 85px">	
										<option>No Ref</option>	
										<option>Job No</option>										
										<option>No Cont</option>
										
									</select>
									<input type="text"  id ="cari_data" name="cari_data" value="<?php echo $cari_data; ?>" 
									style="text-align: left;width:200px;margin-left:-4px;padding:4px" onkeypress="ListJO()"  >
									<button class="btn btn-block btn-primary" 
									style="margin:0px;margin-left:-4px;margin-bottom:3px;border-radius:0px;pading:0px;padding:5px" type="button" 
									onClick="javascript:ListJO()" ">
									<span class="glyphicon glyphicon-search"></span>
									</button>
									<button class="btn btn-block btn-danger" 
									style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:2px;padding:5px"  
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
							<?php if($m_add == '1'){?>
							<button class="btn btn-block btn-success" 
								style="margin-left:1px; margin-bottom:2px;padding-top:-2px;padding:0px;padding-right:5px;padding-left:3px;padding-bottom:-2px" type="button" 
								onClick="javascript:GetDoc()"   >
								<span class="fa  fa-plus-square"></span>
								<b>Add Doc</b>
							</button>
							<?php }?>
							<button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-left:-1px; margin-bottom:2px;padding-top:-2px;padding:0px;padding-right:5px;padding-left:3px;padding-bottom:-2px">
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
	
	<div class="modal fade" id="DataUpload"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">
					<form id="form_doc" action="" method="post" enctype="multipart/form-data">
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Upload</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. PR :</b></span>
								<input type="text"  id ="no_bc1" name="nama" value=""
								style="text-transform: uppercase;text-align: center;width:30%" readonly >	
								<input type="hidden"  id ="id_bc1" name="id_bc1" value=""   >
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Keterangan :</b></span>
								<input type="text-align" id="ket" name="ket"  style="text-align: left;width:80%;"   >
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>File Document :</b></span>
								<input type='file' name='dok' style="height:26px;padding:4px;width:80%;font-family:tahoma;font-size:11px" id='file' class='form-control' ><br>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="submit" class="btn btn-primary" >&nbsp;&nbsp;Save&nbsp;&nbsp;</button>
								<button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>								
							</div>
							<br>
						</div>
					</div>		
					</form>	
				</div>
			</div>
		</div>	
    </div>
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
