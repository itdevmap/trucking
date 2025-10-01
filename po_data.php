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

		$rowid 		= $_POST['rowid'];
		$mode 		= $_POST['mode'];
		$user_req 	= $_POST['id_vendor'];	
		$code_pr 	= $_POST['no_pr'];	
		$id_quo 	= $_POST['id_quo'];	
		$deliv_date = $_POST['deliv_date'];
		$buyer 		= $_POST['buyer'];
		$payment 	= $_POST['payment'];
		$remark 	= addslashes(trim($_POST['remark']));

		$errors = [];
		if (empty($rowid))      $errors[] = "SAP Project wajib diisi";
		if (empty($user_req))   $errors[] = "Vendor wajib diisi";
		if (empty($code_pr))    $errors[] = "No PR wajib diisi";
		if (empty($id_quo))     $errors[] = "ID Quotation wajib diisi";
		if (empty($deliv_date)) $errors[] = "Delivery Date wajib diisi";
		if (empty($buyer))      $errors[] = "Buyer wajib diisi";
		if (empty($payment))    $errors[] = "Payment wajib diisi";
		if (empty($remark))     $errors[] = "Remark wajib diisi";

		if (!empty($errors)) {
			// tampilkan error dan stop proses
			echo "<script>alert('".implode("\\n", $errors)."');history.back();</script>";
			exit;
		}

		if($mode == 'Add' ){
			// ----------- BUILD CODE PR -----------
				$tahun = date("y");
				$q = "SELECT MAX(RIGHT(code_po,4)) as last_num 
					FROM tr_po 
					WHERE SUBSTRING(code_po,4,2) = '$tahun'";
				$res = mysqli_query($koneksi, $q);
				$row = mysqli_fetch_assoc($res);
				$nextNum = ($row['last_num'] ?? 0) + 1;
				$urut = str_pad($nextNum, 4, "0", STR_PAD_LEFT);
				$code_po = "PO-" . $tahun . $urut;
				$code_po = $tahun . $urut;
				$code_po = "PO-" . $code_po;
			
			$sql = "INSERT INTO  tr_po (
						sap_project, 
						id_quo, 
						code_pr, 
						code_po, 
						delivery_date, 
						buyer, 
						payment, 
						user_req, 
						remark ) 
					VALUES (
					'$rowid', 
					'$id_quo', 
					'$code_pr', 
					'$code_po', 
					'$deliv_date',
					'$buyer',
					'$payment',
					'$user_req', 
					'$remark')";

			$hasil= mysqli_query($koneksi, $sql);
			$sql = mysqli_query($koneksi, "SELECT max(id_po) AS id_po FROM tr_po");			
			$row = mysqli_fetch_array($sql);
			$id_po = $row['id_po'];

		}else{
			$sql = "UPDATE tr_po SET 
						user_req = '$user_req',
						remark = '$remark',
					WHERE id_po = '$id_po'	";
			$hasil=mysqli_query($koneksi,$sql);
		}
		
		$cat ="Data saved...";
		$xy1="Edit|$id_po|$cat";
		$xy1=base64_encode($xy1);
		header("Location: po_data.php?id=$xy1");
	}else{

		$idx 	= $_GET['id'];	
		$x		= base64_decode($idx);
		$pecah 	= explode("|", $x);
		$mode	= $pecah[0];
		$id_po 	= $pecah[1];
	}

	if($mode == 'Add') {
		$quo_date = date('d-m-Y');
		
	}else{

		$pq 		= mysqli_query($koneksi, 
						"SELECT 
							tr_po.*,
							sap_project.kode_project,
							m_cust_tr.nama_cust
						FROM tr_po 
						LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_po.user_req
						LEFT JOIN sap_project ON sap_project.rowid = tr_po.sap_project
						WHERE tr_po.id_po = '$id_po' ");
						
		$rq				= mysqli_fetch_array($pq);	
		$quo_date 		= date('d-m-Y');
		$rowid 		    = $rq['sap_project'];
		$id_quo 		= $rq['id_quo'];
		$kode_project   = $rq['kode_project'];
		$no_pr 			= $rq['code_pr'];
		$code_po 		= $rq['code_po'];
		$user_req 		= $rq['user_req'];
		$deliv_date 	= $rq['delivery_date'];
		$nama_cust 		= $rq['nama_cust'];
		$buyer 			= $rq['buyer'];
		$payment 		= $rq['payment'];
		$remark 		= $rq['remark'];
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
				var id_vendor = $("#id_vendor").val();
				if (id_vendor) {
					checkPayment(id_vendor);
				}
				ReadData('route');

				$(document).on("change", "#id_vendor", function () {
					var id_vendor = $(this).val();
					if (id_vendor) {
						checkPayment(id_vendor);
					} else {
						$("#payment").val("");
						$("#buyer").val("");
					}
				});
			});
			function ReadData(jenis) {
				var code_po = $("#code_po").val();
				var id_pr   = $("#id_pr").val();
				var mode    = $("#mode").val();

				$("button[data-jenis]").removeClass("btn-warning").addClass("btn-primary");

				$("button[data-jenis='" + jenis + "']")
					.removeClass("btn-primary")
					.addClass("btn-warning");

				$.get("ajax/po_crud.php", {
					mode: mode,
					code_po: code_po,
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
			function hitungTotal() {
				let qty   	= parseFloat(document.getElementById("qty").value.replace(/,/g,''))   || 0;
				let harga 	= parseFloat(document.getElementById("harga").value.replace(/,/g,'')) || 0;
				let disc  	= parseFloat(document.getElementById("disc").value.replace(/,/g,''))  || 0;
				let ppn  	= parseFloat(document.getElementById("ppn").value.replace(/,/g,''))  || 0;

				let subtotal 		= qty * harga;
				let nominal_disc	= (subtotal * disc) /100;
				let total    		= subtotal - nominal_disc;
				let nominal_ppn 	= (total * ppn) /100;
				let last_total    	= total - nominal_ppn;

				document.getElementById("qty").value   			= Desimal(qty);
				document.getElementById("harga").value 			= Desimal(harga);
				document.getElementById("disc").value  			= Desimal(disc);
				document.getElementById("ppn").value  			= Desimal(ppn);
				document.getElementById("nominal_ppn").value	= Desimal(nominal_ppn);
				document.getElementById("total").value 			= Desimal(last_total < 0 ? 0 : last_total);
			}

		// --------- SHOW MILIH PR ---------
			function TampilPR() {
				$("#cari").val('');
				ListPR();
				$('#DaftarPR').modal('show');
			}
			function ListPR() {
				var cari = $("#cari").val();
				$.get("ajax/po_crud.php", {cari:cari,  type:"ListPR" }, function (data, status) {
					$(".tampil_po").html(data);
					$("#hal").val(hal);
				});
			}
			function PilihPR(id) {
				$.post("ajax/po_crud.php", {
						id: id, type:"DetilData"
					},
					function (data, status) {
						var data = JSON.parse(data);	
						$("#no_pr").val(data.code_pr);
						$("#user_req").val(data.nama_cust);
						$("#deliv_date").val(data.tgl_pr);
						$("#id_quo").val(data.id_quo);
					}
				);
				$("#DaftarPR").modal("hide");
			}

		// ------------------- AJAX TAMPIL SAP PROJECT -------------------
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

		// --------- SHOW MILIH ITEM DARI PR ---------
			function TampilItemPR(jenis) {
				$('#DaftarItemPR').modal('show');
				ListItemPR(jenis);
				$('#DaftarItemPR').find('input, textarea, select').val('');
				$('#DaftarItemPR').find('input[type=checkbox], input[type=radio]').prop('checked', false);
				$("#cari").val('');
				$("#item").val('');
				$("#description").val('');
			}

			function ListItemPR(jenis) {
				var cari 	= $("#cari_Item").val();
				var code_pr	= $("#no_pr").val();
				jenis 		= jenis ?? $("#jenisx").val();

				$.get("ajax/po_crud.php", {cari:cari,code_pr:code_pr, jenis:jenis,type:"ListItemPR" }, function (data, status) {
					$(".tampil_item_po").html(data);
				});
			}

			function PilihItemPR(id) {
				var jenis     = $("#jenisx").val();
				var id_vendor = $("#id_vendor").val();

				$.post("ajax/po_crud.php", {
					id: id,
					jenis: jenis,
					id_vendor: id_vendor,
					type: "DetilDataItem"
				}, function (res) {
					let data;
					try {
						data = JSON.parse(res);
					} catch (e) {
						alert("Response tidak valid: " + res);
						return;
					}

					if (data.status && data.status !== 200) {
						alert(data.message || "Terjadi kesalahan!");
						return;
					}

					$("#idx").val(data.id_detail);
					$("#id_item").val(data.item);
					$("#item").val(data.jenis);
					$("#origin").val(data.origin);
					$("#destination").val(data.destination);
					$("#itemcode").val(data.itemcode);
					$("#description").val(data.description);
					$("#uom").val(data.uom);
					$("#harga").val(data.harga);
					$("#qty").val(data.qty_close);
					$("#disc").val(0);
					$("#total").val(data.harga * data.qty_close);

					if (jenis === "route") {
						$("#harga").prop("readonly", true);
					} else {
						$("#harga").val(0).prop("readonly", false);
						$("#total").val(0);
					}

					$("#DaftarItemPR").modal("hide");
				});
			}
		
		function TampilData(jenis){
			let jenisCap = jenis.charAt(0).toUpperCase() + jenis.slice(1).toLowerCase();
			var id_vendor = $("#id_vendor").val();


			$.post("ajax/po_crud.php", {
				id_vendor: id_vendor,
				type: "checkPPN"
			}, function (data) {
				try {
					var res = JSON.parse(data);
					$("#ppn").val(res.ppn);
				} catch (e) {
					console.error("Invalid JSON:", data);
					alert("Response server tidak valid.");
				}
			})

			$("#modex").val('Add');
			$('#modalAddDetail').modal('show');
			$("#jenisx").val(jenis);
    		$("#nama_jenis").text(jenisCap);
			$("#itemcode").val('');
			$("#itemname").val('');
			$("#container").val('');
			$("#uom").val('');
			$("#qty").val('');
			$("#cur").val('IDR');
			$("#harga").val('');
			$("#disc").val('');
			$("#total").val('');
			$(".form-group-jenis").hide();

		}

		function AddData() {
			var mode 			= $("#modex").val().trim();
			var idx 			= $("#idx").val().trim();
			var jenisx 			= $("#jenisx").val().trim();
			var code_po 		= $("#code_po").val().trim();
			var code_pr 		= $("#no_pr").val().trim();
			var id_item 		= $("#id_item").val().trim();
			var itemcode 		= $("#itemcode").val().trim();
			var item 			= $("#item").val().trim();
			var origin 			= $("#origin").val().trim();
			var destination 	= $("#destination").val().trim();
			var description 	= $("#description").val().trim();
			var container 		= $("#container").val().trim();
			var uom 			= $("#uom").val().trim();
			var cur 			= $("#cur").val().trim();

			var qty_raw 		= $("#qty").val().replace(/,/g, '');
			var qty 			= parseFloat(qty_raw);
			var harga_raw 		= $("#harga").val().replace(/,/g, '');
			var harga 			= parseFloat(harga_raw);
			var disc_raw 		= $("#disc").val().replace(/,/g, '');
			var disc 			= parseFloat(disc_raw);
			var ppn_raw 		= $("#ppn").val().replace(/,/g, '');
			var ppn 			= parseFloat(ppn_raw);
			var nominal_ppn_raw	= $("#nominal_ppn").val().replace(/,/g, '');
			var nominal_ppn 	= parseFloat(nominal_ppn_raw);
			var total_raw 		= $("#total").val().replace(/,/g, '');
			var total 			= parseFloat(total_raw);

			if (container === "") {
				alert("Container wajib di isi !");
				return;
			}
			else if (isNaN(harga) || harga <= 0) {
				alert("Masukan nilai harga yang valid !");
				return;
			}
			else if (isNaN(total) || total <= 0) {
				alert("Masukan nilai total yang valid !");
				return;
			}
			
			$.post("ajax/po_crud.php", {
				idx: idx,
				jenisx: jenisx,
				code_po: code_po,
				code_pr: code_pr,
				id_item: id_item,
				itemcode: itemcode,
				item: item,
				origin: origin,
				destination: destination,
				description: description,
				container: container,
				uom: uom,
				cur: cur,
				qty: qty,
				harga: harga,
				disc: disc,
				ppn: ppn,
				nominal_ppn: nominal_ppn,
				total: total,

				mode: mode,
				type: "Add_Detil"
			}, function (data, status) {
				alert(data);

				$("#modalAddDetail input:not(#code_po, #id_pr, #mode)").val("");
				$("#modalAddDetail textarea").val("");
				$("#modalAddDetail select").val("").trigger("change");

				$("#modalAddDetail").modal("hide");
				setTimeout(function() {
					ReadData(jenisx);
				}, 200);
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

		function EditDetail(id, jenis) {
			// alert(jenis);
			// die();
			$.post("ajax/po_crud.php", {
					id: id,
					jenis: jenis,
					type: "EditData"
				}, function (data) {
				
					$("#modex").val('Edit');
					$("#jenisx").val(data.jenis);

					$("#id_asal").val('');
					$("#id_tujuan").val('');
					$("#form-origin, #form-destination, #form-item, #form-service, #form-itemcode").hide();
					$("#name-origin, #name-destination, #name-item, #name-service, #name-itemcode, #uom").val('');

					if (data.jenis === "route") {
						$("#idx").val(data.id);
						$("#nama_jenis").text(data.jenis);
						$("#item").val(data.item);
						$("#id_item").val(data.id_item);
						$("#itemcode").val(data.itemcode);
						$("#description").val(data.description);
						$("#container").val(data.container);
						$("#uom").val(data.uom).prop("readonly", true);
						$("#qty").val(data.qty);
						$("#cur").val(data.cur.toUpperCase()).prop("readonly", true);
						$("#harga").val(data.harga).prop("readonly", true);
						$("#disc").val(data.disc);
						$("#ppn").val(data.ppn);
						$("#nominal_ppn").val(data.nominal_ppn);
						$("#total").val(data.total);
						$("#origin").val(data.id_asal);
						$("#destination").val(data.id_tujuan);
					} 
					else if (jenis === "item") {
						$("#idx").val(data.id);
						$("#nama_jenis").text(data.jenis);
						$("#item").val(data.item);
						$("#id_item").val(data.id_item);
						$("#itemcode").val(data.itemcode);
						$("#description").val(data.description);
						$("#container").val(data.container);
						$("#uom").val(data.uom).prop("readonly", true);
						$("#qty").val(data.qty);
						$("#cur").val(data.cur.toUpperCase()).prop("readonly", true);
						$("#harga").val(data.harga).prop("readonly", true);
						$("#disc").val(data.disc);
						$("#ppn").val(data.ppn);
						$("#nominal_ppn").val(data.nominal_ppn);
						$("#total").val(data.total);
						$("#origin").val(data.id_asal);
						$("#destination").val(data.id_tujuan);
					} 
					else if (jenis === "service") {
						$("#idx").val(data.id);
						$("#nama_jenis").text(data.jenis);
						$("#item").val(data.item);
						$("#id_item").val(data.id_item);
						$("#itemcode").val(data.itemcode);
						$("#description").val(data.description);
						$("#container").val(data.container);
						$("#uom").val(data.uom.toUpperCase()).prop("readonly", true);
						$("#qty").val(data.qty);
						$("#cur").val(data.cur.toUpperCase()).prop("readonly", true);
						$("#harga").val(data.harga).prop("readonly", true);
						$("#disc").val(data.disc);
						$("#ppn").val(data.ppn);
						$("#nominal_ppn").val(data.nominal_ppn);
						$("#total").val(data.total);
						$("#origin").val(data.id_asal);
						$("#destination").val(data.id_tujuan);
					}
				}, "json");
				$('#Data').modal('show');
		}

		// --------- ADD SAP ---------
			function AddSAP() {
				$.get("ajax/po_crud.php", { type: "AddProject" }, function (res) {
					$("#sap_project").val(res.newKode);
					$("#rowid").val(res.rowid);

					$("#DaftarSAP").modal("hide");
				}, "json");
			}
			function checkPayment(id_vendor) {
				$.post("ajax/po_crud.php", {
					id_vendor: id_vendor,
					type: "checkPayment"
				}, function (data) {
					try {
						var res = JSON.parse(data);
						if (res.status === "success") {
							$("#payment").val(res.payment);
							$("#buyer").val(res.nama_rek);
						} else {
							$("#payment").val("");
							$("#buyer").val("");
							alert("Data bank vendor tidak ditemukan.");
						}
					} catch (e) {
						console.error("Invalid JSON:", data);
						alert("Response server tidak valid.");
					}
				}).fail(function (xhr, status, error) {
					alert("AJAX Error: " + error);
				});
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
					<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Purchase Order</b></font></h1></li>					
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
							<b><i class="fa fa-list"></i>&nbsp;Data Purchase Order</b>
						</div>
						<br>
						<input type="hidden" id ="id_po" name="id_po" value="<?php echo $id_po; ?>" >	
						<input type="hidden" id ="mode" name="mode" value="<?php echo $mode; ?>" >
						<input type="hidden" id ="id_quo" name="id_quo" value="<?php echo $id_quo; ?>" >
						<input type="hidden" name="rowid" id="rowid" value="<?php echo $rowid; ?>" >

						<div style="width:100%; display: none;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Date :</b></span>
							<input type="text"  id ="quo_date" name="quo_date" value="<?php echo $quo_date; ?>" style="text-align: center;width:20%" readonly>
						</div>		

						<div style="width:100%;" class="input-group">
                            <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Project :</b></span>
                            <input type="text" name="sap_project" id="sap_project" style="text-transform: uppercase;text-align: left;width:70%;" value="<?php echo $kode_project; ?>"  readonly>
                            	
                            <button class="btn btn-block btn-primary" id="po" style="padding:6px 12px 6px 12px; ;margin-top:-3px;border-radius:2px;margin-left:5px" type="button" onClick="javascript:TampilSAP()">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No PR :</b></span>
							<input type="text"  id ="no_pr" name="no_pr" value="<?php echo $no_pr;?>" style="text-align: left;width:70%" readonly >
							<button class="btn btn-block btn-primary" id="btn_custx" style="padding:6px 12px 6px 12px; ;margin-top:-3px;border-radius:2px;margin-left:5px" type="button" onClick="javascript:TampilPR()">
								<span class="glyphicon glyphicon-search"></span>
							</button>	
						</div>
						
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;background:none;min-width:150px">
								<b>Vendor :</b>
							</span>
							<select id="id_vendor" name="id_vendor" style="width: 70%;padding:4px" onchange="checkPayment(this.value)">
								<?php 
								$t1 = "SELECT * FROM m_vendor_tr 
										WHERE status = '1' 
										AND caption IS NOT NULL 
										ORDER BY nama_vendor";
								$h1 = mysqli_query($koneksi, $t1);       
								while ($d1 = mysqli_fetch_array($h1)) {
									$selected = ($d1['id_vendor'] == $user_req) ? 'selected' : '';
									?>
									<option value="<?= $d1['id_vendor']; ?>" <?= $selected; ?>>
										<?= strtoupper($d1['nama_vendor']); ?>
									</option>
								<?php } ?>
							</select>
						</div>

						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Delivery Date :</b></span>
							<input type="text"  id ="deliv_date" name="deliv_date" value="<?php echo $deliv_date; ?>" style="text-align: left;width:70%" readonly>
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
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Account Name :</b></span>
							<input type="text"  id ="buyer" name="buyer" value="<?php echo $buyer; ?>" style="text-align: left;width:70%">
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Payment :</b></span>
							<input type="text"  id ="payment" name="payment" value="<?php echo $payment; ?>" style="text-align: left;width:70%">
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Remark :</b></span>
							<textarea name="remark" id="remark"
							style="height:60px;width: 70%; font-size: 11px; line-height: 12px; padding: 5px;" ><?php echo $remark; ?></textarea>
						</div>
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
									<b>PO Route</b>
								</button>
								<button class="btn btn-primary" 
										style="border-radius:2px" 
										type="button" 
										data-jenis="item"
										onClick="ReadData('item')">
									<b>PO Item</b>
								</button>
								<!-- <button class="btn btn-primary" 
										style="border-radius:2px" 
										type="button" 
										data-jenis="service"
										onClick="ReadData('service')">
									<b>PO Service</b>
								</button> -->
							</div>

							<div class="table-responsive mailbox-messages" style="min-height:10px">									
								<div class="tampil_data"></div>
							</div>	
						</div>
					</div>
				<?php }?>	

				<?php
					$link = "po.php?id=$xy1";
					$xy1="$id_po";
					$idx=base64_encode($xy1);
				?>

				<div class="col-md-12" >
					<div style="width:98%;background:none;margin-left:0;margin-top:0px;border-top:0px;border-bottom:0px" class="input-group">
						<?php if($mode != 'Edit'){?>
							<button type="submit" class="btn btn-success"><span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save Order</b>&nbsp;&nbsp;</button>	
						<?php }?>
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
	

	<!-- ---------- MODAL NAMBAH ITEM DI PO  -->
		<div class="modal fade" id="modalAddDetail"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">
					<div class="modal-body">
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							 
									&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Order</b>
								</div>	
								<br>

								<input type="hidden" id="idx" value=""/>
								<input type="hidden" id="modex" value=""/>
								<input type="hidden" id="code_po" value="<?php echo $code_po;?>"/>
								<input type="hidden" id="jenisx" value=""/>
								<input type="hidden" id="id_item" value=""/>
								<input type="hidden" id ="origin" name="origin">
								<input type="hidden" id ="destination" name="destination">
								<input type="hidden" id="nominal_ppn" name="nominal_ppn">

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b id="nama_jenis"></b> : </span>
									<input type="text" id ="item" name="item" style="text-transform:uppercase; text-align: left; width:70%" readonly>
									<button class="btn btn-block btn-primary" id="btn_item"
										style="padding:6px 12px 6px 12px; margin-top:-3px;border-radius:2px; margin-left:5px"
										type="button" onClick="TampilItemPR($('#jenisx').val())">
										<span class="glyphicon glyphicon-search"></span>
									</button>
								</div>

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;"><b>Itemcode :</b></span>
									<input type="text" id ="itemcode" name="itemcode" style="text-transform:uppercase; text-align: left;width:80%" readonly>
								</div>	

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;"><b>Description:</b></span>
									<textarea name="description" id="description" style="text-transform:uppercase; width: 80%; height: 50px; font-size: 11px; line-height: 12px;"></textarea>
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;"><b>Container :</b></span>
									<input type="text" id ="container" name="container" style="text-transform:uppercase; text-align: left;width:80%">
								</div>	
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;"><b>UoM :</b></span>
									<input type="text" id ="uom" name="uom" style="text-align: right;width:18%" readonly>
								</div>	
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;"><b>Quantity :</b></span>
									<input type="text" id="qty" name="qty" onblur="hitungTotal()" style="text-align: right;width:18%">
								</div>  
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;"><b>Currency :</b></span>
									<input type="text" id ="cur" name="cur" value="IDR" style="text-align: right;width:18%">
								</div>	

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;"><b>Harga :</b></span>
									<input type="text" id="harga" name="harga" onblur="hitungTotal()" style="text-align: right;width:18%">
								</div>  
								
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;"><b>Disc :</b></span>
									<input type="text" id="disc" name="disc" onblur="hitungTotal()" style="text-align: right;width:14%"> %
								</div>  
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;"><b>PPN :</b></span>
									<input type="text" id="ppn" name="ppn" onblur="hitungTotal()" style="text-align: right;width:14%"> %
								</div>  
								
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;"><b>Total :</b></span>
									<input type="text" id="total" name="total" readonly style="text-align: right;width:18%">
								</div>  
								
								<div style="width:100%; margin-top: 6px;" class="input-group">
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
	
	<!-- --------- MODAL PR --------- -->
		<div class="modal fade" id="DaftarPR"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
									&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data PR</b>
								</div>	
								<br>
								<div style="width:100%" class="input-group" style="background:none !important;">
									<span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Search :</b>&nbsp;&nbsp;
										<input type="text"  id ="cari" name="cari" value="<?php echo $cari; ?>" 
										style="text-align: left;width:200px" onkeypress="ListPR()" >
										<button class="btn btn-block btn-primary" 
										style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:2px;padding:5px" 
										onClick="javascript:ListPR()">
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
									<div class="tampil_po"></div>
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
								<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
									&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data SAP Project</b>
								</div>	
								<br>
								<div style="width:100%" class="input-group" style="background:none !important;">
									<span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Search :</b>&nbsp;&nbsp;
										<input type="text"  id ="cari_SAP" name="cari_SAP" value="<?php echo $cari; ?>" 
										style="text-align: left;width:200px" onkeypress="ListSAP()" >
										<button class="btn btn-block btn-primary" style="margin:0px; margin-bottom:3px;border-radius:2px;padding:5px" onClick="javascript:ListSAP()">
											<span class="glyphicon glyphicon-search"></span> Search
										</button>

										<button class="btn btn-block btn-danger" style="margin:0px; margin-bottom:3px;border-radius:2px;padding:5px" data-dismiss="modal">
											<span class="glyphicon glyphicon-remove"></span> Close
										</button>

										<button class="btn btn-block btn-success" style="margin:0px; margin-bottom:3px;border-radius:2px;padding:5px" onClick="javascript:AddSAP()">
											<span class="glyphicon glyphicon-plus"></span> Project
										</button>
									</span>
									<span class="input-group-addon" style="width:80%;text-align:right;padding:0px">									
									</span>
								</div>							
								<div class="table-responsive mailbox-messages" >									
									<div class="tampil_SAP"></div>
								</div>
								<br>
							</div>		
						</div>		
					</div>	
				</div>
			</div>	
		</div>

	<!-- --------- MODAL ITEM PR --------- -->
		<div class="modal fade" id="DaftarItemPR"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
									&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Item PR</b>
								</div>	
								<br>
								<div style="width:100%" class="input-group" style="background:none !important;">
									<span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Search :</b>&nbsp;&nbsp;
										<input type="text"  id ="cari_Item" name="cari_Item" value="<?php echo $cari; ?>" style="text-align: left;width:200px" onkeypress="ListItemPR()" >
										
										<button class="btn btn-block btn-primary" 
										style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:2px;padding:5px" 
										onClick="javascript:ListItemPR()">

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
								<div class="table-responsive mailbox-messages">									
									<div class="tampil_item_po"></div>
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
