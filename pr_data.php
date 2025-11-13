<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	include "lib.php";

	if(!isset($_SESSION['id_user'])  ){
	header('location:logout.php'); 
	}

	if($_SERVER['REQUEST_METHOD'] == "POST") {
		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// die();

		$mode 		= $_POST['mode'];
		$id_pr 		= $_POST['id_pr'];

		$tgl 		= date('Y-m-d');
		$tgl_pr 	= $_POST['tgl_pr'];
		$remark 	= addslashes(trim($_POST['remark']));
		$id_quo 	= $_POST['id_quo'];
		$sap_rowid 	= $_POST['rowid'];

		if($mode == 'Add' ){
			// ----------- BUILD CODE PR -----------
				$tahun 		= date("y");
				$bulan 		= date("m");
				$q 			= "SELECT MAX(RIGHT(code_pr,4)) AS last_num 
							FROM tr_pr 
							WHERE SUBSTRING(code_pr,6,2) = '$tahun' 
								AND code_pr LIKE '%PRTR%'";

				$res 		= mysqli_query($koneksi, $q);
				$row 		= mysqli_fetch_assoc($res);
				$nextNum 	= ($row['last_num'] ?? 0) + 1;
				$urut 		= str_pad($nextNum, 4, "0", STR_PAD_LEFT);
				$build_code = $tahun . $bulan . $urut;
				$code_pr 	= "PRTR-" . $build_code;

				// echo $code_pr;
				// die();
			
			$sql = "INSERT INTO  tr_pr (
						code_pr,
						sap_rowid,
						id_quo,
						tgl, 
						tgl_pr,  
						remark ) 
					VALUES (
					'$code_pr',
					'$sap_rowid',
					'$id_quo',
					'$tgl',
					'$tgl_pr',
					'$remark')";

			$hasil= mysqli_query($koneksi, $sql);
			$sql = mysqli_query($koneksi, "SELECT max(id_pr) AS id_pr FROM tr_pr");			
			$row = mysqli_fetch_array($sql);
			$id_pr = $row['id_pr'];

		}else{
			$sql	= "UPDATE tr_pr SET
						tgl_pr = '$tgl_pr',
						remark = '$remark'
					WHERE id_pr = '$id_pr'	";
			// echo $sql;
			// die();
			$hasil 	= mysqli_query($koneksi,$sql);
		}
		
		$cat ="Data saved...";
		$xy1 = "Edit|$id_pr|$cat";
		$xy1 = base64_encode($xy1);
		header("Location: pr_data.php?id=$xy1");
	}else{

		$idx 	= $_GET['id'];	
		$x		= base64_decode($idx);
		$pecah 	= explode("|", $x);
		$mode	= $pecah[0];
		$id_pr 	= $pecah[1];
	}

	if($mode == 'Add') {
		$tgl = date('d-m-Y');
	}else{
		$pq 		= mysqli_query($koneksi, 
						"SELECT 
							tr_pr.*,
							tr_quo.quo_no,
							m_cust_tr.id_cust,
							m_cust_tr.nama_cust,
							sap_project.rowid,
							sap_project.kode_project
						FROM tr_pr 
						LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_pr.user_req
						LEFT JOIN tr_quo ON tr_quo.id_quo = tr_pr.id_quo
						LEFT JOIN sap_project ON sap_project.rowid = tr_pr.sap_rowid
						WHERE tr_pr.id_pr = '$id_pr'");
		$rq			= mysqli_fetch_array($pq);
		$id_pr 		= $rq['id_pr'];
		$quo_no 	= $rq['quo_no'];
		$id_quo 	= $rq['id_quo'];
		$code_pr 	= $rq['code_pr'];
		$id_user 	= $rq['id_cust'];
		$user_req 	= $rq['nama_cust'];
		$tgl 		= $rq['tgl'];
		$tgl_pr 	= $rq['tgl_pr'];
		$remark 	= $rq['remark'];
		$rowid 		  = $rq['rowid'];
		$kode_project  = $rq['kode_project'];
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
		// --------- SHOW DATA ---------
			$(document).ready(function () {
				ReadData('route');

				$("#name-item").on("change", function() {
					let selectedName = $(this).find(":selected").data("name") || "";
					console.log("DEBUG:", selectedName); // cek di console
					$("#desc").val(selectedName.toUpperCase());
				});
			});	
			function ReadData(jenis) {
				var code_pr = $("#code_pr").val();
				var id_pr   = $("#id_pr").val();
				var mode    = $("#mode").val();

				$("button[data-jenis]").removeClass("btn-warning").addClass("btn-primary");

				$("button[data-jenis='" + jenis + "']")
					.removeClass("btn-primary")
					.addClass("btn-warning");

				$.get("ajax/pr_crud.php", {
					mode: mode,
					code_pr: code_pr,
					id_pr: id_pr,
					jenis: jenis,
					type: "Read_Detil"
				}, function (data, status) {
					$(".tampil_data").html(data);
				});
			}

			function Desimal(num) {
				num = num.toString().replace(/\$|\,/g,'');
				if(isNaN(num)) num = "0";
				let sign = (num == (num = Math.abs(num)));
				num = Math.floor(num).toString();

				for (let i = 0; i < Math.floor((num.length-(1+i))/3); i++) {
					num = num.substring(0,num.length-(4*i+3))+','+
						num.substring(num.length-(4*i+3));
				}
				return (sign ? '' : '-') + num;
			}

		function TampilData(jenis) {
			$("#modex").val('Add');
			$("#jenisx").val(jenis);

			$("#desc").val('');
			$("#uom").val('');
			$("#qty").val('');
			$(".form-group-jenis").hide();

			var id_quo = $("#id_quo").val();
			$.post("ajax/pr_crud.php", {
				id_quo: id_quo,
				jenis: jenis,
				type: "checkSQRoute"
			}, function (data) {
			
				$("#id_asal").val('');
				$("#id_tujuan").val('');
				$("#qty").val(0);
				$("#form-origin, #form-destination, #form-item, #form-service, #form-itemcode").hide();
				$("#name-origin, #name-destination, #name-item, #name-service, #name-itemcode, #uom").val('');

				if (jenis === "route") {
					$("#form-origin").show();
					$("#form-destination").show();
					$("#form-itemcode").show();
					
					$("#id_asal").val(data.id_asal);
					$("#id_tujuan").val(data.id_tujuan);
					$("#name-origin").val(data.asal);
					$("#name-destination").val(data.tujuan);
					$("#name-itemcode").val('LJLOTO.0000BROTRUC');
					$("#uom").val(data.jenis_mobil).prop("readonly", true);
					$("#ket").html('Feet :');
				} 
				else if (jenis === "item") {
					$("#form-item").show();
					$("#uom").val('').prop("readonly", true);
					$("#ket").html('UoM :');
				} 
				else if (jenis === "service") {
					$("#form-service").show();
					$("#uom").val('').prop("readonly", false);
					$("#ket").html('UoM :');
				}
			}, "json");
			$('#Data').modal('show');
		}

		function AddData() {
			var code_pr		= $("#code_pr").val().trim();
			var desc 		= $("#desc").val().trim();
			
			var uom 		= $("#uom").val().trim();
			var id 			= $("#idx").val();
			var mode 		= $("#modex").val();
			var jenisx 		= $("#jenisx").val();

			var itemcode 	= "";
			var name 		= "";
			var origin 		= "";
			var destination = "";

			if (jenisx === "route") {
				origin 		= $("#id_asal").val();
				destination	= $("#id_tujuan").val();
				itemcode 	= $("#name-itemcode").val().trim();
			} else if (jenisx === "item") {
				itemcode 	= $("#item_rowid").val().trim();
				name 		= $("#item_rowid").val().trim();
			}

			var qty_raw 	= $("#qty").val().replace(/,/g, '');
			var qty 		= parseFloat(qty_raw);

			if (jenisx === "route" && mode === 'Add') {
				if (origin === "" && destination === "") {
					alert("Origin dan Destination wajib di isi !");
					return;
				}
			}else if (jenisx === "item"){
				if (itemcode === "") {
					alert("Itemcode wajib di isi !");
					return;
				}
			}

			if (desc === "") {
				alert("Desc wajib di isi !");
				return;
			}
			else if (isNaN(qty) || qty <= 0) {
				alert("Masukan nilai quantity yang valid !");
				return;
			}
			
			$.post("ajax/pr_crud.php", {
				code_pr: code_pr,
				origin: origin,
				destination: destination,
				name: name,
				itemcode: itemcode,
				desc: desc,
				uom: uom,
				qty: qty,
				jenisx: jenisx,

				id: id,
				mode: mode,
				type: "Add_Detil"
			}, function (data, status) {
				alert(data);
				$("#Data").modal("hide");
				ReadData(jenisx);
			});
		}

		function DelDetil(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/quo_crud.php", {
						id: id, type:"Del_Detil"
					},
					function (data, status) {
						 ReadData();
					}
				);
			}
		}

		function checkvalue(form) {
			let tgl_pr 	= form.tgl_pr.value.trim();
			let remark 	= form.remark.value.trim();
			let no_sq	= form.no_sq.value.trim();

			if (tgl_pr === "") {
				alert("Delivery Date harus diisi!");
				form.tgl_pr.focus();
				return false;
			}

			if (remark === "") {
				alert("Remark harus diisi!");
				form.remark.focus();
				return false;
			}

			// if (no_sq === "") {
			// 	alert("No SQ harus diisi!");
			// 	form.no_sq.focus();
			// 	return false;
			// }

			return true;
		}

		// ------------------- AJAX TAMPIL SQ -------------------
			function TampilSQ() {
				$cari = $("#cari_SQ").val('');
				ListSQ();
				$('#DaftarSQ').modal('show');
			}
			function ListSQ() {
				var cari = $("#cari_SQ").val();
				$.get("ajax/pr_crud.php", {cari:cari,  type:"ListSQ" }, function (data, status) {
					$(".tampil_SQ").html(data);
				});
			}
			function PilihSQ(id) {
				$.post("ajax/pr_crud.php", {
						id: id, type:"DetilSQ"
					},
					function (data, status) {
						var data = JSON.parse(data);	
						$("#no_sq").val(data.quo_no);
						$("#id_quo").val(data.id_quo);
					}
				);
				$("#DaftarSQ").modal("hide");
			}

		// ------------------- AJAX TAMPIL ITEM -------------------
			function TampilItem() {
				$cari = $("#cari_Item").val('');
				ListItem();
				$('#DaftarItem').modal('show');
			}
			function ListItem() {
				var cari = $("#cari_Item").val();
				$.get("ajax/pr_crud.php", {cari:cari,  type:"ListItem" }, function (data, status){
					$(".tampil_Item").html(data);
				});
			}
			function PilihItem(rowid) {
				$.post("ajax/pr_crud.php", {
						id: rowid, type:"DetilItem"
					},
					function (data, status) {
						var data = JSON.parse(data);	
						
						$("#item_rowid").val(data.id_cost);
						$("#name-item").val(data.itemcode);
						$("#desc").val(data.nama_cost);
						$("#uom").val(data.uom);
					}
				);
				$("#DaftarItem").modal("hide");
			}

		// ----------------- Edit DATA -----------------
            function EditDetail(id_detail, jenis) {

				$.post("ajax/pr_crud.php", {
					id_detail: id_detail,
					jenis: jenis,
					type: "EditData"
				}, function (data) {
				
					$("#modex").val('Edit');
					$("#jenisx").val(data.jenis);

					$("#id_asal").val('');
					$("#id_tujuan").val('');
					$("#form-origin, #form-destination, #form-item, #form-service, #form-itemcode").hide();
					$("#name-origin, #name-destination, #name-item, #name-service, #name-itemcode, #uom").val('');

					if (jenis === "route") {
						$("#form-origin").show();
						$("#form-destination").show();
						$("#form-itemcode").show();

						$("#idx").val(data.id_detail);
						$("#id_asal").val(data.id_asal);
						$("#id_tujuan").val(data.id_tujuan);
						$("#name-origin").val(data.asal);
						$("#name-destination").val(data.tujuan);
						$("#name-itemcode").val('LJLOTO.0000BROTRUC');
						$("#uom").val(data.uom).prop("readonly", true);
						$("#desc").val(data.description);
						$("#qty").val(data.qty);
					} 
					else if (jenis === "item") {
						$("#form-item").show();

						$("#idx").val(data.id_detail);
						$("#name-item").val(data.rowid).trigger("change");
						$("#uom").val(data.uom).prop("readonly", true);
						$("#qty").val(data.qty);
					} 
					else if (jenis === "service") {
						$("#form-service").show();

						$("#idx").val(data.id_detail);
						$("#desc").val(data.description);
						$("#uom").val(data.uom);
						$("#qty").val(data.qty);
					}
				}, "json");
				$('#Data').modal('show');
            }

		// ========= SAP PROJECT =========
			function TampilSAP(){
				$cari = $("#cari_SAP").val('');
				ListSAP();
				$('#DaftarSAP').modal('show');
			}
			function ListSAP() {
				var cari = $("#cari_SAP").val();
				$.get("ajax/jo_crud.php", {cari:cari,  type:"ListSAP" }, function (data, status) {
					$(".tampil_SAP").html(data);
				});
			}
			function PilihSAP(id) {
				$.post("ajax/jo_crud.php", {
						id: id, type:"DetilSAP"
					},
					function (data, status) {
						var data = JSON.parse(data);	
						$("#sap_project").val(data.kode_project);
						$("#rowid").val(data.rowid);
					}
				);
				$("#DaftarSAP").modal("hide");
			}
			function AddSAP() {
				$.get("ajax/po_crud.php", { type: "AddProject" }, function (res) {
					$("#sap_project").val(res.newKode);
					$("#rowid").val(res.rowid);

					$("#DaftarSAP").modal("hide");
				}, "json");
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
					<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Purchase Request</b></font></h1></li>					
				</ol>
				<br>
				<?php if($cat != '') {?>
				<div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
					<i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo "$cat"; ?></font>
				</div>
				<?php }?>
				
				<div class="col-md-6" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc; min-height:190px">					
						<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
						text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-list"></i>&nbsp;Data Purchase Request</b>
						</div>
						<br>
						<input type="hidden" id ="id_pr" name="id_pr" value="<?php echo $id_pr; ?>" >	
						<input type="hidden" id ="code_pr" name="code_pr" value="<?php echo $code_pr; ?>" >	
						<input type="hidden" id ="mode" name="mode" value="<?php echo $mode; ?>" >
						<input type="hidden" id ="id_quo" name="id_quo" value="<?php echo $id_quo; ?>" >
						<input type="hidden" id ="rowid" name="rowid" value="<?php echo $rowid; ?>" >

						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Date :</b></span>
							<input type="text"  id ="tgl" name="tgl" value="<?php echo $tgl; ?>" style="text-align: center;width:20%" readonly>
						</div>

						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Delivery Date :</b></span>
							<input type="date"  id ="tgl_pr" name="tgl_pr" value="<?php echo $tgl_pr; ?>" style="text-align: left;width:70%">
						</div>
						<div style="width:100%;" class="input-group">
                            <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No SQ :</b></span>
                            <input type="text" name="no_sq" id="no_sq" style="text-transform: uppercase;text-align: left;width:70%;" value="<?php echo $quo_no; ?>" readonly>
                            	
                            <button class="btn btn-block btn-primary" id="po" style="padding:6px 12px;margin-top:-3px;border-radius:2px;margin-left:5px" type="button" onClick="javascript:TampilSQ()">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </div>
						<div style="width:100%;" class="input-group">
                            <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Project :</b></span>
                            <input type="text" name="sap_project" id="sap_project" style="text-transform: uppercase;text-align: left;width:70%;" value="<?php echo $kode_project; ?>"  readonly>
                            	
                            <button class="btn btn-block btn-primary" id="po" style="padding:6px 12px 6px 12px; ;margin-top:-3px;border-radius:2px;margin-left:5px" type="button" onClick="javascript:TampilSAP()">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </div>
						<br>	
					</div>
				</div>
				
				<div class="col-md-6" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:190px">					
						<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
						text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-list"></i>&nbsp;Remarks</b>
						</div>
						<br>
						<textarea name="remark" id="remark" style="margin-left:12px;resize:none;width: 95%; height: 105px; font-size: 11px; line-height: 12px;" ><?php echo $remark; ?></textarea>
						<br>	
					</div>
				</div>

				<?php if($mode != 'Add'){?>	
					<div class="col-md-12" >
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:50px">
							<div style="margin: 6px 0px; display: flex; gap: 6px;">
								<button class="btn btn-primary" 
										style="border-radius:2px" 
										type="button" 
										data-jenis="route"
										onClick="ReadData('route')">
									<b>PR Route</b>
								</button>
								<button class="btn btn-primary" 
										style="border-radius:2px" 
										type="button" 
										data-jenis="item"
										onClick="ReadData('item')">
									<b>PR Item</b>
								</button>
								<!-- <button class="btn btn-primary" 
										style="border-radius:2px" 
										type="button" 
										data-jenis="service"
										onClick="ReadData('service')">
									<b>PR Service</b>
								</button> -->
							</div>

							<div class="table-responsive mailbox-messages" style="min-height:10px">									
								<div class="tampil_data"></div>
							</div>	
						</div>
					</div>
				<?php }?>	

				<?php
					$link = "pr.php?id=$xy1";
					$xy1="$id_pr";
					$idx=base64_encode($xy1);
				?>

				<div class="col-md-12" >
					<div style="width:98%;background:none;margin-left:0;margin-top:0px;border-top:0px;border-bottom:0px" class="input-group">
						
						<button type="submit" class="btn btn-success"><span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save</b>&nbsp;&nbsp;</button>	
						
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
	
	<!-- ---------- MODAL NAMBAH ITEM DI PR  -->
		<div class="modal fade" id="Data" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">
					<div class="modal-body">
						<div class="col-md-12" style="min-height:40px;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color:#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
									&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Order</b>
								</div>	
								<br>
								<input type="hidden" id="idx" value=""/>
								<input type="hidden" id="modex" value=""/>
								<input type="hidden" id="jenisx" value=""/>
								<input type="hidden" id="id_asal"/>
								<input type="hidden" id="id_tujuan"/>
								<input type="hidden" id="item_rowid"/>

								<!-- ------------ Route ------------ -->
									<div id="form-origin" class="input-group form-group-jenis" style="display:none;width:100%;">
										<span class="input-group-addon" style="text-align: right;"><b>Origin :</b></span>
										<input type="text" id="name-origin" style="text-transform:uppercase;width:80%" readonly>
									</div>
									<div id="form-destination" class="input-group form-group-jenis" style="display:none;width:100%;">
										<span class="input-group-addon" style="text-align: right;"><b>Destination :</b></span>
										<input type="text" id="name-destination" style="text-transform:uppercase;width:80%" readonly>
									</div>

									<div id="form-itemcode" class="input-group form-group-jenis" style="display:none;width:100%;">
										<span class="input-group-addon" style="text-align: right;"><b>Itemcode :</b></span>
										<input type="text" id="name-itemcode" style="text-transform:uppercase;width:80%" readonly>
									</div>

								<!-- ------------ Item ------------ -->
									<div id="form-item" class="input-group form-group-jenis" style="display:none;width:100%;">
										<span class="input-group-addon" style="text-align: right;"><b>Itemcode :</b></span>
										<input type="text" name="name-item" id="name-item" style="text-transform: uppercase;text-align: left;width:70%;" readonly>
										<button class="btn btn-block btn-primary" id="po" style="padding:6px 12px;margin-top:-3px;border-radius:2px;margin-left:2px" type="button" onClick="javascript:TampilItem()">
											<span class="glyphicon glyphicon-search"></span>
										</button>
									</div>

								<!-- ------------ Service ------------ -->
									<!-- <div id="form-service" class="input-group form-group-jenis" style="display:none;width:100%;">
										<span class="input-group-addon" style="text-align: right;"><b>Service :</b></span>
										<input type="text" id="name-service" style="text-transform:uppercase;width:80%">
									</div> -->

								<!-- ------------ INPUT BIASA ------------ -->
								<div class="input-group" style="width:100%;">
									<span class="input-group-addon" style="text-align: right;"><b>Description :</b></span>
									<textarea id="desc" style="text-transform:uppercase;width:80%;height:50px;font-size:11px;line-height:12px;"></textarea>
								</div>

								<div class="input-group" style="width:100%;">
									<span class="input-group-addon" style="text-align: right;"><b>
										<div id="ket"></div></b></span>
									<input type="text" id="uom" style="text-transform:uppercase;text-align:right;width:18%">
								</div>	

								<div class="input-group" style="width:100%;">
									<span class="input-group-addon" style="text-align: right;"><b>Quantity :</b></span>
									<input type="text" id="qty" onBlur="this.value=Desimal(this.value);" style="text-align:right;width:18%">
								</div>  

								<div class="input-group" style="width:100%;">
									<span class="input-group-addon" style="min-width:150px"></span>
									<button type="button" class="btn btn-success" onclick="AddData()">
										<span class="fa fa-save"></span>&nbsp;<b>Save</b>
									</button>	
									<button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-left:5px">
										<span class="fa fa-close"></span>&nbsp;<b>Cancel</b>
									</button>	
								</div>
								<br>
							</div>
						</div>			
					</div>
				</div>
			</div>	
		</div>

	<!-- ---------- MODAL SEARCH SQ PROJECT ---------- -->
		<div class="modal fade" id="DaftarSQ"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
									&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data SQ</b>
								</div>	
								<br>
								<div style="width:100%" class="input-group" style="background:none !important;">
									<span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Search :</b>&nbsp;&nbsp;
										<input type="text" id ="cari_SQ" style="text-align: left;width:200px">

										<button class="btn btn-block btn-primary" style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:2px;padding:5px" onClick="javascript:ListSQ()">
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
									<div class="tampil_SQ"></div>
								</div>
								<br>
							</div>		
						</div>		
					</div>	
				</div>
			</div>	
		</div>

	<!-- ---------- MODAL SEARCH ITEM ---------- -->
		<div class="modal fade" id="DaftarItem"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
									&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Item</b>
								</div>	
								<br>
								<div style="width:100%" class="input-group" style="background:none !important;">
									<span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Search :</b>&nbsp;&nbsp;
										<input type="text" id="cari_Item" style="text-align:left;width:200px" oninput="ListItem()">

										<button class="btn btn-block btn-primary" style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:2px;padding:5px" onClick="javascript:ListItem()">
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
									<div class="tampil_Item"></div>
								</div>
								<br>
							</div>		
						</div>		
					</div>	
				</div>
			</div>	
		</div>

	<!-- --------- MODAL SAP --------- -->
		<div class="modal fade" id="DaftarSAP"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">

								<div class="small-box bg" style="font-size:12px;font-family:'Arial';color:#fff;margin:0;background-color:#4783b7;text-align:left;padding:5px;">
									<b><i class="fa fa-list"></i>&nbsp;Data SAP Project</b>
								</div>

								<div style="display:flex;align-items:center;gap:10px;margin-top:10px;">
									<label style="margin:0;width: 100px;"><b>Search :</b></label>
									<input type="text" id="cari_SAP" name="cari_SAP" value="<?php echo $cari; ?>" style="width:40%" onkeypress="ListSAP()">

									<button class="btn btn-primary" style="padding:6px 10px;" onClick="ListSAP()">
										<span class="glyphicon glyphicon-search"></span> Search
									</button>
									<button class="btn btn-success" style="padding:6px 10px;" onClick="AddSAP()">
										<span class="glyphicon glyphicon-plus"></span> Project
									</button>
									<button class="btn btn-danger" style="padding:6px 10px;" data-dismiss="modal">
										<span class="glyphicon glyphicon-remove"></span> Close
									</button>
								</div>

								<input type="hidden" id="jenis_project" value="">

								<div class="table-responsive mailbox-messages" style="margin-top:15px;">
									<div class="tampil_SAP"></div>
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
