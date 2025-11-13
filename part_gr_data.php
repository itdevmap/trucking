<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	include "lib.php";

	if(!isset($_SESSION['id_user'])  ){
		header('location:logout.php'); 
	}

	if($_SERVER['REQUEST_METHOD'] == "POST") {

		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// exit;

		$mode 			= $_POST['mode'];
		$id_part_gr 	= $_POST['id_part_gr'];	
		$date_input 	= $_POST['date'];
		$date 			= date('Y-m-d', strtotime(str_replace('/', '-', $date_input)));
		$no_grwh    	= $_POST['no_grwh'];	
		$remark 		= addslashes(trim($_POST['remark']));
		$jurnal_remark 	= addslashes(trim($_POST['jurnal_remark']));
		$type_receipt 	= $_POST['type_receipt'];

		if($mode == 'Add' ){

			// ========= BUILD CODE SO =========
			$tahun = date("y");
			$q = "SELECT MAX(RIGHT(no_grwh,4)) as last_num 
				FROM m_part_gr 
				WHERE SUBSTRING(no_grwh,6,2) = '$tahun'";

			// echo $q;
			// exit;

			$res = mysqli_query($koneksi, $q);
			$row = mysqli_fetch_assoc($res);
			$nextNum = ($row['last_num'] ?? 0) + 1;

			$urut = str_pad($nextNum, 5, "0", STR_PAD_LEFT);
			$no_grwh = "GRWH-" . $tahun . $urut;

			$no_grwh = $tahun . $urut;
			$no_grwh = "GRWH-" . $no_grwh;

			// echo $no_grwh;
			// exit;

    		$id_user	= $_SESSION['id_user'] ?? 0;
			
			$sql = "INSERT INTO  m_part_gr (
						no_grwh, 
						tanggal, 
						remark,
						jurnal_remark,
						type_receipt
						) 
					VALUES (
						'$no_grwh', 
						'$date',
						'$remark',
						'$jurnal_remark',
						'$type_receipt'
						)";

			$hasil = mysqli_query($koneksi, $sql);
			$sql = mysqli_query($koneksi, "SELECT max(id_part_gr) AS id_part_gr FROM m_part_gr");	

			$row = mysqli_fetch_array($sql);
			$id_gr = $row['id_part_gr'];

		}else{
			$sql 	= "UPDATE m_part_gr SET 
						remark	= '$remark',
						jurnal_remark	= '$jurnal_remark',
						type_receipt	= '$type_receipt'
					WHERE id_part_gr = '$id_part_gr'";
			$hasil 	= mysqli_query($koneksi,$sql);
			$id_gr = $id_part_gr;
		}

		$mode = 'Edit';
		$xy1  = base64_encode($id_gr);
		header("Location: part_gr_data.php?id=$xy1&mode=$mode");
		exit;

	}else{
        $mode	= $_GET['mode'];
		$idx 	= $_GET['id'];	
        $x_part	= base64_decode($idx);

		// echo  $id_part_gr;
		// exit;
	}

	if($mode == 'Add') {
		$tanggal = date('d-m-Y');
	}else{

        $q_part_gr = "SELECT 
                m_part_gr.*
            FROM m_part_gr 
            WHERE id_part_gr = '$x_part'";

        $p_part_gr	= mysqli_query($koneksi, $q_part_gr);
		$r_part_gr 	= mysqli_fetch_array($p_part_gr);	

		$id_part_gr	= $r_part_gr['id_part_gr'];
		$tanggal	= $r_part_gr['tanggal'];
		$no_grwh	= $r_part_gr['no_grwh'];
		$remark		= $r_part_gr['remark'];
		$jurnal_remark 	= $r_part_gr['jurnal_remark'];
		$status 	 	= $r_part_gr['status'];
		$type_receipt 	= $r_part_gr['type_receipt'];
	}

	if($mode == 'View') {
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
			var date_input=$('input[name="date"]'); 
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
			var id_part_gr = $("#id_part_gr").val();
			var mode = $("#mode").val();
			$.get("ajax/part_crud.php", {mode:mode,id_part_gr:id_part_gr, type:"Read_DetilGR" }, function (data, status) {
				$(".tampil_data").html(data);
			});
		}

		function TampilData() {
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
			$("#modex").val('AddDetail');
			$('#ModalAdd').modal('show');
		}
		function TampilPart(){
			var cari	= $("#cari_part").val();
			cari='';
			$.get("ajax/part_crud.php", {cari:cari, type:"ListPart_GR" }, function (data, status) {
				$(".tampil_part").html(data);
			});
			$('#DataPart').modal('show');
		}
		function ListPart() {
			var cari = $("#cari_part").val();
			$.get("ajax/part_crud.php", {cari:cari, type:"ListPart_GR" }, function (data, status) {
				$(".tampil_part").html(data);
			});
		}
		function PilihPart(id_part) {
			$.post("ajax/part_crud.php", {
					id_part: id_part, type:"Pilih_itemGR"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_part").val(data.id_part);
					$("#nama_part").val(data.nama);
					$("#unit").val(data.uom);
				}
			);
			$("#DataPart").modal("hide");
		}
		function AddData() {
			var qty = $("#qty").val();
			if (!$("#id_part").val() || !$("#nama_part").val()) {
				alert("Pilih Part Dahulu!");
				return;
			}

			if (qty <= 0) {
				alert("Qty harus diisi!");
				return;
			}

			if (confirm("Are you sure ?...")) {	
				$.post("ajax/part_crud.php", {
					id_part_gr: $("#id_part_gr").val(),
					id_detail_gr: $("#id_detail_gr").val(),
					id_part: $("#id_part").val(),
					nama_part: $("#nama_part").val(),
					qty: $("#qty").val(),
					gr_coa: $("#gr_coa").val(),
					mode: $("#mode").val(),
					type: "Add_GR"
				}, function (data) {
					alert(data);
					$("#ModalAdd").modal("hide");
					ReadData(1);
				});
			}
		}

		function EditDetail(id_detail_gr) {
			$.post("ajax/part_crud.php", {
				id_detail_gr: id_detail_gr,
				type: "EditDetailGR"
			}, function (data) {
				if (data.error) {
					alert("Error: " + data.error);
					return;
				}

				$("#modex").val('EditDetail');
				$("#id_detail_gr").val(id_detail_gr);
				$("#id_part").val(data.id_part);
				$("#nama_part").val(data.nama_cost);
				$("#qty").val(data.qty);
				$("#unit").val(data.uom);
				$("#gr_coa").val(data.gr_coa).trigger("change");

				// 🔒 Jika mode Edit, nonaktifkan tombol cari part
				if ($("#modex").val() === "EditDetail") {
					$("#cost").prop("disabled", true);
				} else {
					$("#cost").prop("disabled", false);
				}

				$("#ModalAdd").modal("show");
			}, "json");
		}

		function DelDetail(id_detail_gr) {
			if (confirm("Are you sure to Delete ?...")) {	
				$.post("ajax/part_crud.php", {
					id_detail_gr: id_detail_gr,
					type: "DelDetailGR"
				}, function (data) {
					alert(data);
					$("#ModalAdd").modal("hide");
					ReadData(1);
				});
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
		
		<div class="wrapper">
		<header class="main-header">
			<?php include "header.php"; ?>	 
		</header>
		<aside class="main-sidebar">
			<?php include "menu.php" ; ?>	
		</aside>	
		
		<form method="post" name ="myform"  class="form-horizontal"> 
			<div class="content-wrapper" style="min-height:750px">
				<br>
				<ol class="breadcrumb">
					<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Add Item GR</b></font></h1></li>					
				</ol>
				<br>

				<?php if($cat != '') {?>
                    <div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
                        <i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo "$cat"; ?></font>
                    </div>
				<?php }?>
				
				<div class="col-md-6" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc; min-height:180px">					
						<div class="small-box bg" style="font-size:11px; font-family: 'Tahoma'; color :#fff; margin:0px; background-color:#4783b7; text-align:left;padding:5px; margin-bottom:1px">							
							<b><i class="fa fa-list"></i>&nbsp;Data Item GR</b>
						</div>
						<br>

						<input type="hidden" id ="id_part_gr" name="id_part_gr" value="<?php echo $id_part_gr; ?>" >
						<input type="hidden" id ="mode" name="mode" value="<?php echo $mode; ?>" >

						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Date :</b></span>
							<input type="text"  id ="date" name="date" value="<?php echo $tanggal; ?>" style="text-align: left;width:40%" readonly>
						</div>		

						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>No GRWH :</b></span>
							<input type="text"  id ="no_grwh" name="no_grwh" value="<?php echo $no_grwh; ?>" style="text-align: left;width:70%" readonly>
						</div>	
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Remark :</b></span>
							<textarea name="remark" id="remark" style="height:40px;width: 70%; font-size: 11px; line-height: 12px; padding: 5px;" ><?php echo $remark; ?></textarea>
						</div>	
						<br>	
					</div>
				</div>
				<div class="col-md-6" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc; min-height:180px">					
						<div class="small-box bg" style="font-size:11px; font-family: 'Tahoma'; color :#fff; margin:0px; background-color:#4783b7; text-align:left;padding:5px; margin-bottom:1px">							
							<b><i class="fa fa-list"></i>&nbsp;</b>
						</div>
						<br>

						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Type Receipt :</b></span>
							<select id="type_receipt" name="type_receipt" style="text-align: left; width:70%">
								<option value="3" <?= ($type_receipt == '3') ? 'selected' : '' ?>>Consumable</option>
								<option value="5" <?= ($type_receipt == '5') ? 'selected' : '' ?>>Stock</option>
							</select>
						</div>

						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Jurnal Remark :</b></span>
							<textarea name="jurnal_remark" id="jurnal_remark" style="height:40px;width: 70%; font-size: 11px; line-height: 12px; padding: 5px;" ><?php echo $jurnal_remark; ?></textarea>
						</div>	
						<br>	
					</div>
				</div>

				<?php if($mode != 'Add'){?>	
					<div class="col-md-12" >
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:95px">
							<?php if($mode == 'Edit' && $status != '1'){?>
								<button class="btn btn-block btn-success" 
									style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:2px" type="button" 
									onClick="javascript:TampilData()"  <?php echo $dis_copy;?> >
									<span class="fa  fa-plus-square"></span>
									<b>Add Data</b>
								</button>
							<?php }?>
							<div class="table-responsive mailbox-messages" style="min-height:10px">									
								<div class="tampil_data"></div>
							</div>	
						</div>
					</div>
				<?php }?>	

				<?php
					$link = "part_gr.php";
				?>

				<div class="col-md-12" >
					<div style="width:100%;background:none;margin-left:0;margin-top:0px;border-top:0px;border-bottom:0px" class="input-group">
						<button type="submit" class="btn btn-success"><span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save Order</b>&nbsp;&nbsp;</button>
						<button type="button" class="btn btn-danger" onclick="window.location.href='<?php echo $link; ?>'">
							<span class="fa fa-backward"></span>&nbsp;&nbsp;<b>Back</b>
						</button>	
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
	
	<!-- ====== ADD PART GR ====== -->
	<div class="modal fade" id="ModalAdd"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">	
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Spare Part In</b>
							</div>	
							<br>

							<input type="hidden" id="id_part"/>
							<input type="hidden" id="id_detail_gr"/>
							<input type="hidden" id="modex"/>
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Item Description :</b></span>
								<input type="text" id ="nama_part" name="nama" style="text-align: left;width:70%" readonly>

								<button class="btn btn-block btn-primary" id="cost"
									style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:4px" type="button" 
									onClick="javascript:TampilPart()" <?php echo $dis;?>>
									<span class="glyphicon glyphicon-search"></span>
								</button>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>COA :</b></span>

							<select id="gr_coa" name="gr_coa" style="text-align: left; width:70%">
								<?php 
								$tampil1 = "SELECT * FROM m_part_gr_coa WHERE type_receipt LIKE '%$type_receipt%' ORDER BY keterangan";
								$hasil1  = mysqli_query($koneksi, $tampil1);       

								while ($data1 = mysqli_fetch_assoc($hasil1)) {
									$selected = ($data1['id_gr_coa'] == $id_gr_coa) ? 'selected' : '';
									echo '<option value="' . htmlspecialchars($data1['id_gr_coa']) . '" ' . $selected . '>'
										. htmlspecialchars($data1['keterangan']) .
										'</option>';
								}
								?>
							</select>

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
	
	<!-- ====== LIST PART GR ====== -->
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
									onClick="javascript:ListPart()">
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
