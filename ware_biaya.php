<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	include "lib.php";

	if(!isset($_SESSION['id_user'])  ){
	header('location:logout.php'); 
	}

	if($_SERVER['REQUEST_METHOD'] == "POST"){		
		$mode = $_POST['mode'];
		$id_quo = $_POST['id_quo'];	
		$quo_date = $_POST['quo_date'];	
		$id_cust = $_POST['id_cust'];
		$no_kontrak = trim(addslashes(strtoupper($_POST['no_kontrak'])));
		$ket = addslashes(trim($_POST['ket']));
		$quo_datex = ConverTglSql($quo_date);
		$sales = $_POST['sales'];
		$max_cbm = $_POST['max_cbm'];
		$aging_sewa = $_POST['aging_sewa'];
		$harga_sewa = $_POST['harga_sewa'];
		$harga_handling = $_POST['harga_handling'];
		$max_cbm = str_replace(",","", $max_cbm);
		$harga_sewa = str_replace(",","", $harga_sewa);
		$harga_handling = str_replace(",","", $harga_handling);
		
		if($mode == 'Add' )
		{}else{
			$sql = "UPDATE t_ware_quo set 
						id_cust = '$id_cust',
						ket = '$ket',
						sales = '$sales',
						max_cbm = '$max_cbm',
						aging_sewa = '$aging_sewa',
						harga_sewa = '$harga_sewa',
						harga_handling = '$harga_handling',
						no_kontrak = '$no_kontrak'
						where id_quo = '$id_quo'";
			$hasil=mysqli_query($koneksi,$sql);
		}
		
		$cat ="Data saved...";
		$xy1="Edit|$id_quo|$cat";
		$xy1=base64_encode($xy1);
		header("Location: ware_biaya.php?id=$xy1");
	}
	else{	
		$idx = $_GET['id'];	
		$x=base64_decode($idx);
		$pecah = explode("|", $x);
		$mode= $pecah[0];
		$id_quo = $pecah[1];
		$cat = $pecah[2];
	}

	if($mode == 'Add'){
		$quo_no = '-- Auto -- ';
		$quo_date = date('d-m-Y');
		
	}else{
		
		$pq = mysqli_query($koneksi, "select t_ware_quo.*, m_cust_tr.nama_cust
			from 
			t_ware_quo left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
			where t_ware_quo.id_quo = '$id_quo'  ");
		$rq=mysqli_fetch_array($pq);	
		$quo_no = $rq['quo_no'];
		$quo_date = ConverTgl($rq['quo_date']);
		$id_cust = $rq['id_cust'];
		$nama_cust = $rq['nama_cust'];
		$no_po = $rq['no_po'];
		$ket = $rq['ket'];
		$sales = $rq['sales'];
		$stat = $rq['status'];
		$aging_sewa = $rq['aging_sewa'];
		$no_kontrak = $rq['no_kontrak'];
		$max_cbm  = number_format($rq['max_cbm'],2);
		$harga_sewa  = number_format($rq['harga_sewa'],0);
		$harga_handling  = number_format($rq['harga_handling'],0);
		$disx = 'Disabled';
	}

	if($mode == 'View'){
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
			var date_input=$('input[name="quo_date"]'); 
			var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
			date_input.datepicker({
				format: 'dd-mm-yyyy',
				container: container,
				todayHighlight: true,
				autoclose: true,
			})
			$("#tgl_jo").datepicker({
				format:'dd-mm-yyyy',
				todayHighlight: true,
				autoclose: true,
			});
			ReadData();
		});	
		function ReadData() {	
			var id_quo = $("#id_quo").val();
			var mode = $("#mode").val();
			var stat = $("#stat").val();
			$.get("ajax/ware_crud.php", {stat:stat, mode:mode,id_quo:id_quo, type:"Read_Data_Quo_Biaya" }, function (data, status) {
				$(".tampil_data").html(data);
			});
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
		function checkvalue() {
			var id_cust = document.getElementById('id_cust').value; 
			if(id_cust == '') {
				alert ('Customer harus diisi..');				
				return false;
			}else{
				return true;
			}	
		}
		
		
		function TampilData() 
		{
			$("#modex").val('Add');
			//$("#id_biaya").val('');
			$("#harga").val('');
			$('#Data').modal('show');
		}
		function AddData() {
			var harga = $("#harga").val();
			if(harga <= 0){
				alert("Harga harus diisi !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {	
					var id = $("#id").val();
					var id_biaya = $("#id_biaya").val();
					var id_quo = $("#id_quo").val();
					var harga = $("#harga").val();
					var mode = $("#modex").val();
					//alert(mode);
					$.post("ajax/ware_crud.php", {
						id:id,
						id_biaya:id_biaya,
						id_quo:id_quo,
						harga:harga,
						mode:mode,
						type : "Add_Data_Quo_Biaya"
						}, function (data, status) {
						alert(data);
						$("#Data").modal("hide");				
						ReadData(1);
					});
				}
			}	
		}
		
		function GetData(id) {
			$("#id").val(id);	
			
			$.post("ajax/ware_crud.php", {
					id: id, type:"Detil_Data_Quo_Biaya"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					$("#id_biaya").val(data.id_biaya);
					$("#harga").val(Rupiah(data.harga));
					$("#modex").val('Edit');
				}
			);
			$("#Data").modal("show");
		}
		function DelData(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/ware_crud.php", {
						id: id, type:"Del_Data_Quo_Jasa"
					},
					function (data, status) {
						 ReadData();
					}
				);
			}
		}
		function HitungVol() 
		{
			var panjang = $("#panjang").val();
			var lebar = $("#lebar").val();
			var tinggi = $("#tinggi").val();
			var vol = (Number(panjang) * Number(lebar) * Number(tinggi))/1000000;
			$("#vol").val(Desimal(vol));
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
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Quotation</b></font></h1></li>					
			</ol>
			<br>
			<?php if($cat != '') {?>
			<div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
				<i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo "$cat"; ?></font>
			</div>
			<?php }?>
			
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;height:230px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
					text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Data Quotation</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>#Quo No :</b></span>
						<input type="text"  id ="quo_no" name="quo_no" value="<?php echo $quo_no; ?>" 
						style="text-align: center;width:20%" readonly <?php echo $dis;?> >						
						<input type="hidden"  id ="id_quo" name="id_quo" value="<?php echo $id_quo; ?>" >	
						<input type="hidden"  id ="mode" name="mode" value="<?php echo $mode; ?>" >
						<input type="hidden"  id ="stat" name="stat" value="<?php echo $stat; ?>" >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Date :</b></span>
						<input type="text"  id ="quo_date" name="quo_date" value="<?php echo $quo_date; ?>" 
						style="text-align: center;width:20%" readonly <?php echo $dis;?>  >
					</div>				
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Customer :</b></span>
						<input type="text"  id ="nama_cust" name="nama_cust" value="<?php echo $nama_cust;?>" style="text-align: left;width:74.3%" readonly  >
						<button class="btn btn-block btn-primary" id="btn_custx"
							style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-1px" type="button" 
							onClick="javascript:TampilCust()" <?php echo $disx;?> >
							<span class="glyphicon glyphicon-search"></span>
						</button>
						<input type="hidden" id="id_cust"  name="id_cust" value="<?php echo $id_cust;?>" 
						style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />		
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>No. Kontrak :</b></span>
						<input type="text" id="no_kontrak" name="no_kontrak" value="<?php echo $no_kontrak;?>" 
						style="text-transform: uppercase;text-align: left;width:79.5%;" <?php echo $dis;?> >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Sales :</b></span>
							<select id="sales" name="sales" style="width: 80%;padding:4px" <?php echo $dis;?>>
								<?php
								$t1="select * from m_sales_tr where status = '1' order by nama  ";
								$h1=mysqli_query($koneksi, $t1);       
								while ($d1=mysqli_fetch_array($h1)){?>
								<option value="<?php echo $d1['nama'];?>" ><?php echo $d1['nama'];?></option>
								<?php }?>
								<option value="<?php echo $sales;?>" selected><?php echo $sales;?></option>
							</select>
					</div>	
					
						
					<br>	
				</div>
            </div>
			<div class="col-md-6" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;height:230px">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
					text-align:left;padding:5px;margin-bottom:1px">							
						<b><i class="fa fa-list"></i>&nbsp;Remarks</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:170px"><b>Aging Sewa :</b></span>
						<input type="text"  id ="aging_sewa" name="aging_sewa" value="<?php echo $aging_sewa; ?>" 
						style="text-align: center;width:10%" onkeypress="return isNumber(event)" 
						 <?php echo $dis;?>  > Days
					</div>	
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:170px"><b>Harga Sewa Gudang :</b></span>
						<input type="text"  id ="harga_sewa" name="harga_sewa" value="<?php echo $harga_sewa; ?>" 
						style="text-align: right;width:18%" onkeypress="return isNumber(event)" onBlur ="this.value=Rupiah(this.value);" <?php echo $dis;?>  >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:170px"><b>Harga Handling :</b></span>
						<input type="text"  id ="harga_handling" name="harga_handling" value="<?php echo $harga_handling; ?>" 
						style="text-align: right;width:18%" onkeypress="return isNumber(event)" onBlur ="this.value=Rupiah(this.value);" <?php echo $dis;?>  >
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:170px"><b>Max CBM :</b></span>
						<input type="text"  id ="max_cbm" name="max_cbm" value="<?php echo $max_cbm; ?>" 
						style="text-align: right;width:18%" onkeypress="return isNumber(event)" onBlur ="this.value=Desimal(this.value);" <?php echo $dis;?>  >
					</div>	
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;background:none;min-width:170px"><b>Note :</b></span>
							<textarea name="ket" id="ket"
						style="resize:none;width: 80%; height: 45px; font-size: 11px; line-height: 12px; 
						border: 1px solid #4; padding: 5px;" <?php echo $dis;?> ><?php echo $ket; ?></textarea>
					</div>	
					
					<br>	
				</div>
            </div>
			<?php if($mode != 'Add'){?>	
				<div class="col-md-12" style="width:99%;border:0px solid #ddd;padding:5px">					
					<div style="width:99%;border-bottom:2px solid #83a939;background:none;margin-left:-5px;margin-top:-5px;margin-bottom:-9px" class="input-group">	
						<?php
							$xy1="$mode|$id_quo";
							$xy1=base64_encode($xy1);
							$link1 = "ware_data.php?id=$xy1";
							$link2 = "ware_biaya.php?id=$xy1";
						?>
						<div id="tabs5" >
							<ul> 
								<li ><?php echo "<a href=$link1>"; ?><span><b>DATA ITEM</b></span></a></li> 
								<li id="current"><?php echo "<a href=$link2>"; ?><span><b>DATA COST SERVICE</b></span></a></li>
							</ul>
						</div>	
					</div>
					
				</div>	
				<div class="col-md-12" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:95px">
						<?php if($mode == 'Edit'){?>
							<button class="btn btn-block btn-success" 
								style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:2px" type="button" 
								onClick="javascript:TampilData()"  <?php echo $dis;?> <?php echo $dis_copy;?> >
								<span class="fa  fa-plus-square"></span>
								<b>Add Cost</b>
							</button>
						<?php }?>
						<div class="table-responsive mailbox-messages" style="min-height:10px">									
							<div class="tampil_data"></div>
						</div>	
					</div>
				</div>
			<?php }?>	
			<?php
				$link = "ware.php?id=$xy1";
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
	
	<div class="modal fade" id="Data"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">	
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Barang</b>
							</div>	
							<br>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>COST NAME :</b></span>
								<select id="id_biaya" style="width: 80%;padding:4px">
									<?php
									$t1="SELECT * FROM m_cost_tr WHERE `status` = '1' AND id_cost <> '1' ORDER BY nama_cost";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_cost'];?>" ><?php echo $d1['nama_cost'];?></option>
									<?php }?>
								</select>	
								<input type="hidden" id="id"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="modex"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Harga :</b></span>								
								<input type="text" id="harga" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  >	
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
