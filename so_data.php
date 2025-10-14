<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	include "lib.php";

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	include("../PHPMailer/src/Exception.php"); 
	include("../PHPMailer/src/PHPMailer.php"); 
	include("../PHPMailer/src/SMTP.php");

	if(!isset($_SESSION['id_user'])  ){
	header('location:logout.php'); 
	}

	if($_SERVER['REQUEST_METHOD'] == "POST") {
		$mode 		= $_POST['mode'];
		$id_quo 	= $_POST['id_quo'];	
		$id_cust 	= $_POST['id_cust'];	
		$rowid      = $_POST['rowid'];	
		$stat      	= '0';	
		$receiver 	= addslashes(trim($_POST['receiver']));
		$remark 	= addslashes(trim($_POST['remark']));

		if($mode == 'Add' ){

			// ========= CHECK LIMIT =========
				$q_limit = "SELECT overlimit FROM m_cust_tr WHERE id_cust = '$id_cust' LIMIT 1";
				$r_query = mysqli_query($koneksi, $q_limit);
				$r_limit = mysqli_fetch_assoc($r_query);
				$limit_cust = $r_limit['overlimit'];
			// ========= END CHECK LIMIT =========

			// ========= CHECK TOTAL SO =========
				$q_total_item = "SELECT 
						COALESCE(SUM(tr_jo_detail.harga - (tr_jo_detail.pph * tr_jo_detail.harga / 100)), 0) AS total_item
					FROM tr_jo
					INNER JOIN tr_sj ON tr_sj.no_jo = tr_jo.no_jo
					LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
					WHERE tr_jo.id_cust = '$id_cust'
				";
				$r_total_item = mysqli_query($koneksi, $q_total_item);
				$row_item = mysqli_fetch_assoc($r_total_item);
				$total_item = $row_item['total_item'] ?? 0;

				$q_total_biaya = "SELECT 
						COALESCE(SUM(tr_jo_biaya.harga - (tr_jo_biaya.pph * tr_jo_biaya.harga / 100)), 0) AS total_biaya
					FROM tr_jo_biaya
					LEFT JOIN tr_jo ON tr_jo.id_jo = tr_jo_biaya.id_jo
					WHERE tr_jo.id_cust = '$id_cust'
				";
				$r_total_biaya = mysqli_query($koneksi, $q_total_biaya);
				$row_biaya = mysqli_fetch_assoc($r_total_biaya);
				$total_biaya = $row_biaya['total_biaya'] ?? 0;

				$total_so = $total_item + $total_biaya;
			// ========= END CHECK TOTAL SO =========
			
			// ========= CHECK AR PAID =========
				$q_total_paid = "SELECT 
						COALESCE(SUM(total_so), 0) as total_so
					FROM tr_jo 
					WHERE ar_paid = '1' 
						AND total_so > 0 
						AND id_cust = '$id_cust'
				";

				$r_total_paid = mysqli_query($koneksi, $q_total_paid);
				$row_paid = mysqli_fetch_assoc($r_total_paid);
				$total_paid = $row_paid['total_so'] ?? 0;
			// ========= END CHECK AR PAID =========
			
			// echo "Total SO " . number_format($total_so, 0, ',', '.') . '</br>';
			// echo "Total IP " .  number_format($total_paid, 0, ',', '.') . '</br>';
			// echo "Total " .  number_format($last_total, 0, ',', '.') . '</br>';
			// echo "LIMIT " .  number_format($limit_cust, 0, ',', '.') . '</br>';
			// die();

			// ========= BUILD CODE SO =========
				$tahun = date("y");
				$q = "SELECT MAX(RIGHT(no_jo,4)) as last_num 
					FROM tr_jo 
					WHERE SUBSTRING(no_jo,4,2) = '$tahun'";

				$res = mysqli_query($koneksi, $q);
				$row = mysqli_fetch_assoc($res);
				$nextNum = ($row['last_num'] ?? 0) + 1;

				$urut = str_pad($nextNum, 5, "0", STR_PAD_LEFT);
				$no_so = "SO-" . $tahun . $urut;

				$no_so = $tahun . $urut;
				$no_so = "SO-" . $no_so;

            // ============ PEMBUATAN PROJECT CODE ============
				$year = date('y');
				$sql = "SELECT project_code FROM tr_jo ORDER BY id_jo DESC LIMIT 1";
				$result = mysqli_query($koneksi, $sql);

				if (!$result) {
					die("Query error: " . mysqli_error($koneksi));
				}

				if (mysqli_num_rows($result) == 0) {
					$project_code = "TRC/$year" . "0001";
				} else {
					$row = mysqli_fetch_assoc($result);
					$lastProjectCode = $row['project_code'];
					$lastYear = substr($lastProjectCode, 4, 2);

					if ($lastYear !== $year) {
						$project_code = "TRC/$year" . "0001";
					} else {
						$lastNum = (int)substr($lastProjectCode, -4);
						$newNum = str_pad($lastNum + 1, 4, "0", STR_PAD_LEFT);
						$project_code = "TRC/$year$newNum";
					}
				}

            $tgl_jo 	= date('Y-m-d');
    		$id_user	= $_SESSION['id_user'] ?? 0;

			// ============ CHECK IS PENDING OR NOT ============
			$last_total = $total_so - $total_paid;
			if (($last_total) > $limit_cust) {
				echo "Total SO " . number_format($last_total, 0, ',', '.') ." melebihi " . number_format($limit_cust, 0, ',', '.') ;
				$stat = '2';

				// ============ NAMA CUST ============
					$q_cust = "SELECT nama_cust FROM m_cust_tr WHERE id_cust = '$id_cust'";
					$r_cust = mysqli_query($koneksi, $q_cust);
					$d_cust = mysqli_fetch_assoc($r_cust);

					$nama_cust = $d_cust['nama_cust'] ?? null;

				// ============ SEND APPROVAL ============
					$mail	= new PHPMailer(true);

					$mail->isSMTP();
					$mail->Host       = 'smtp.gmail.com';
					$mail->SMTPAuth   = true;
					$mail->Username   = 'itdivision.map@gmail.com';
					$mail->Password   = 'glpykeqqsaulnhxd'; 
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
					$mail->Port       = 587;

					$mail->setFrom('itdivision.map@gmail.com', 'Approval Sales Order PETJ');
					// $mail->addAddress('director.petj@gmail.com');
					$mail->addAddress('kuroboy051@@gmail.com');

					$mail->isHTML(true);
					$mail->Subject = "Approval Sales Order Trucking" . $no_so;

					$mail->Body = '
						<table cellspacing="0" cellpadding="4">
							<tr>
								<td><b>No Sales Order</b></td>
								<td>: '. $no_so . '</td>
							</tr>
							<tr>
								<td><b>Tanggal SO</b></td>
								<td>: ' . $tgl_jo . '</td>
							</tr>
							<tr>
								<td><b>Tujuan Approval</b></td>
								<td>: Approval Status Sales Order Overlimit, Limit '. $limit_cust .' Pengajuan '.$last_total.'</td>
							</tr>
							<tr>
								<td><b>Customer</b></td>
								<td>: '.$nama_cust.'</td>
							</tr>
						</table>
						<br><br>
						<a href="http://127.0.0.1/trucking-local/so_approve.php/' . $no_so . '" 
							style="display:inline-block;
								padding:10px 16px;
								background-color:#28a745;
								color:#fff;
								text-decoration:none;
								border-radius:4px;
								font-weight:bold;">
							Approve Sales Order
						</a>
						&nbsp;&nbsp;
						<a href="http://127.0.0.1/trucking-local/so_reject.php/' . $no_so . '" 
							style="display:inline-block;
								padding:10px 16px;
								background-color:#dc3545;
								color:#fff;
								text-decoration:none;
								border-radius:4px;
								font-weight:bold;">
							Reject Sales Order
						</a>
					';
				// ============ END SEND APPROVAL ============
			}
			
			$sql = "INSERT INTO  tr_jo (
						sap_project, 
						project_code, 
						no_jo, 
						tgl_jo, 
						id_quo, 
						id_cust, 
						penerima, 
						created, 
						`status`, 
						ket ) 
					VALUES (
						'$rowid', 
						'$project_code', 
						'$no_so',
						'$tgl_jo',
						'$id_quo',
						'$id_cust',
						'$receiver',
						'$id_user',
						'$stat',
						'$remark')";

			$hasil= mysqli_query($koneksi, $sql);
			$sql = mysqli_query($koneksi, "SELECT max(id_jo) AS id_jo FROM tr_jo");			
			$row = mysqli_fetch_array($sql);
			$id_jo = $row['id_jo'];

		}else{
			$sql = "UPDATE tr_jo SET 
						sap_project = '$rowid',
						penerima    = '$receiver',
						ket         = '$remark'
					WHERE id_jo = '$id_jo'	";
			$hasil=mysqli_query($koneksi,$sql);
		}
		
		$cat ="Data saved...";
		$xy1="Edit|$id_jo|$cat";
		$xy1=base64_encode($xy1);

		header("Location: so_data.php?id=$xy1");
	}else{

        $idx = $_GET['id'];	
        $x=base64_decode($idx);
        $pecah = explode("|", $x);
        $mode= $pecah[0];
        $cat = $pecah[2];

        if ($mode === "Edit") {
            $id_jo = $pecah[1];
        } else{
            $id_quo = $pecah[1];
        }
	}

	if($mode == 'Add') {
		$date = date('d-m-Y');
        
        // ---------- SHOW DATA FROM QUOTATION ----------
        $query      = "SELECT 
                        tr_quo.*,
                        m_cust_tr.nama_cust 
                    FROM tr_quo 
                    LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_quo.id_cust 
                    WHERE tr_quo.id_quo = '$id_quo'";

        $sql        = mysqli_query($koneksi, $query);
        $data       = mysqli_fetch_assoc($sql);

        $nama_cust  = $data['nama_cust'];
        $id_cust    = $data['id_cust'];
        $no_sq      = $data['quo_no'];
        $id_quo     = $data['id_quo'];

	}else{

        $q = "SELECT 
                tr_jo.*,
                sap_project.kode_project,
                m_cust_tr.nama_cust
            FROM tr_jo 
            LEFT JOIN sap_project ON sap_project.rowid = tr_jo.sap_project
            LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_jo.id_cust
            WHERE id_jo = '$id_jo'";

        $pq             = mysqli_query($koneksi, $q);
		$rq			    = mysqli_fetch_array($pq);	

		$date 	        = date('d-m-Y');
		$id_jo 		    = $rq['id_jo'];
		$no_sq 		    = $rq['no_jo'];
		$rowid 		    = $rq['sap_project'];
		$kode_project   = $rq['kode_project'];
		$nama_cust      = $rq['nama_cust'];
		$receiver 	    = $rq['penerima'];
		$remark 	    = $rq['ket'];
        $id_quo         = $rq['id_quo'];
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
				var id_jo = $("#id_jo").val();
				var mode = $("#mode").val();
				$.get("ajax/jo_crud.php", {mode:mode,id_jo:id_jo, type:"Read_Detil" }, function (data, status) {
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
				let qty   = parseFloat(document.getElementById("qty").value.replace(/,/g,''))   || 0;
				let harga = parseFloat(document.getElementById("harga").value.replace(/,/g,'')) || 0;
				let disc  = parseFloat(document.getElementById("disc").value.replace(/,/g,''))  || 0;

				let subtotal = qty * harga;
				let nominal_disc = (subtotal * disc) /100;
				let total    = subtotal - nominal_disc;

				document.getElementById("qty").value   = Desimal(qty);
				document.getElementById("harga").value = Desimal(harga);
				document.getElementById("disc").value  = Desimal(disc);
				document.getElementById("total").value = Desimal(total < 0 ? 0 : total);
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
			function AddSAP() {
				$.get("ajax/po_crud.php", { type: "AddProject" }, function (res) {
					$("#sap_project").val(res.newKode);
					$("#rowid").val(res.rowid);
					$("#DaftarSAP").modal("hide");
				}, "json");
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

		// ------------------- SHOW MILIH SQ -------------------
			function TampilSQ() {
				$("#cari").val('');
				ListItemSQ();
				$('#DaftarItemPR').modal('show');
			}
			function ListItemSQ() {
				var cari = $("#cari").val();
				var id_quo = $("#id_quo").val();

				$.get("ajax/jo_crud.php", {cari:cari,id_quo:id_quo,  type:"ListItemSQ" }, function (data, status) {
					$(".tampil_item_sq").html(data);
				});
			}
			function PilihSQ(id) {
				$.post("ajax/jo_crud.php", {
						id: id, type:"DetilDataItem"
					},
					function (data, status) {
						var data = JSON.parse(data);	

						$("#id_asal").val(data.id_asal);
						$("#id_tujuan").val(data.id_tujuan);
						$("#origin").val(data.asal);
						$("#destination").val(data.tujuan);
						$("#jenis_mobil").val(data.jenis_mobil);
						$("#harga").val(data.harga);
						$("#uj").val(data.uj);
						$("#ritase").val(data.ritase);
						$("#pph").val(data.pph);
					}
				);
				$("#DaftarItemPR").modal("hide");
			}
		
		function TampilData(){
			$("#modex").val('Add');
			$('#Data').modal('show');

			$("#itemcode").val('');
			$("#itemname").val('');
			$("#container").val('');
			$("#uom").val('');
			$("#qty").val('');
			$("#cur").val('IDR');
			$("#harga").val('');
			$("#disc").val('');
			$("#total").val('');
		}

		// function AddData() {
		// 	var code_po = $("#code_po").val().trim();
		// 	var code_pr = $("#no_pr").val().trim();
		// 	var itemcode = $("#itemcode").val().trim();
		// 	var itemname = $("#itemname").val().trim();
		// 	var container = $("#container").val().trim();
		// 	var uom = $("#uom").val().trim();
		// 	var cur = $("#cur").val().trim();

		// 	var qty_raw = $("#qty").val().replace(/,/g, '');
		// 	var qty = parseFloat(qty_raw);
		// 	var harga_raw = $("#harga").val().replace(/,/g, '');
		// 	var harga = parseFloat(harga_raw);
		// 	var disc_raw = $("#disc").val().replace(/,/g, '');
		// 	var disc = parseFloat(disc_raw);
		// 	var total_raw = $("#total").val().replace(/,/g, '');
		// 	var total = parseFloat(total_raw);

		// 	if (itemcode === "") {
		// 		alert("Itemcode masih belum dipilih !");
		// 		return;
		// 	}
		// 	else if (container === "") {
		// 		alert("Container wajib di isi !");
		// 		return;
		// 	}
		// 	else if (isNaN(harga) || harga <= 0) {
		// 		alert("Masukan nilai harga yang valid !");
		// 		return;
		// 	}
		// 	else if (isNaN(total) || total <= 0) {
		// 		alert("Masukan nilai total yang valid !");
		// 		return;
		// 	}
			
		// 	$.post("ajax/po_crud.php", {
		// 		code_po: code_po,
		// 		code_pr: code_pr,
		// 		itemcode: itemcode,
		// 		itemname: itemname,
		// 		container: container,
		// 		uom: uom,
		// 		cur: cur,
		// 		qty: qty,
		// 		harga: harga,
		// 		disc: disc,
		// 		total: total,

		// 		mode: 'Add',
		// 		type: "Add_Detil"
		// 	}, function (data, status) {
		// 		alert(data);
		// 		$("#Data").modal("hide");
		// 		ReadData();
		// 	});
		// }

		// ------------------- ADD TO SO DETAILS -------------------
			function AddDataSO() {
				var id_jo 		= $("#id_jo").val().trim();
				var id_asal 	= $("#id_asal").val().trim();
				var id_tujuan 	= $("#id_tujuan").val().trim();
				var jenis_mobil	= $("#jenis_mobil").val().trim();
				var harga 		= $("#harga").val();
				var uj 			= $("#uj").val();
				var pph 		= $("#pph").val();
				var ritase 		= $("#ritase").val();
				var id_tujuan 	= $("#id_tujuan").val().trim();
				var container 	= $("#container").val().trim();
				var remark 		= $("#remark").val().trim();

				if (container === "" && id_asal === "" && id_tujuan === "") {
					alert("Lengkapi data dahulu!");
					return;
				}

				$.post("ajax/jo_crud.php", {
					id_jo: id_jo,
					id_asal: id_asal,
					id_tujuan: id_tujuan,
					jenis_mobil: jenis_mobil,
					harga: harga,
					uj: uj,
					pph: pph,
					ritase: ritase,
					container: container,
					remark: remark,
					mode: 'Add',
					type: "Add_DetailSO"
				}, function (data, status) {
					alert(data);

					$("#id_asal, #id_tujuan, #origin, #destination, #jenis_mobil, #harga, #uj, #ritase, #container, #remark").val('');

					$("#Data").modal("hide");
					ReadData();
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
		
		<form method="post" name ="myform"  class="form-horizontal"> 
			<div class="content-wrapper" style="min-height:750px">
				<br>
				<ol class="breadcrumb">
					<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Sales Order</b></font></h1></li>					
				</ol>
				<br>

				<?php if($cat != '') {?>
                    <div class="callout callout-Danger" style="margin-bottom: 0!important;width:98%;color:#fff">
                        <i class="icon 	fa fa-info-circle" style="color:#000;font-size:16px"></i>&nbsp;&nbsp;<font color="#000"><?php echo "$cat"; ?></font>
                    </div>
				<?php }?>
				
				<div class="col-md-6" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc; min-height:200px">					
						<div class="small-box bg" style="font-size:11px; font-family: 'Tahoma'; color :#fff; margin:0px; background-color:#4783b7; text-align:left;padding:5px; margin-bottom:1px">							
							<b><i class="fa fa-list"></i>&nbsp;Data Sales Order</b>
						</div>
						<br>
						<input type="hidden" id ="id_quo" name="id_quo" value="<?php echo $id_quo; ?>" >	
						<input type="hidden" id ="id_jo" name="id_jo" value="<?php echo $id_jo; ?>" >
						<input type="hidden" id ="mode" name="mode" value="<?php echo $mode; ?>" >

						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Date :</b></span>
							<input type="text"  id ="date" name="date" value="<?php echo $date; ?>" style="text-align: center;width:20%" readonly>
						</div>		

						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>No SQ :</b></span>
							<input type="text"  id ="no_sq" name="no_sq" value="<?php echo $no_sq; ?>" style="text-align: left;width:70%" readonly>
						</div>	
						
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Customer Name :</b></span>
							<input type="text"  id ="nama_cust" name="nama_cust" value="<?php echo $nama_cust; ?>" style="text-align: left;width:70%" readonly>
                            <input type="hidden"  id ="id_cust" name="id_cust" value="<?php echo $id_cust; ?>" style="text-align: left;width:70%" readonly>
						</div>	
								
						<br>	
					</div>
				</div>
				
				<div class="col-md-6" >
					<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:200px">					
						<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;
						text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-list"></i>&nbsp;</b>
						</div>
						<br>
                        <div style="width:100%;" class="input-group">
                            <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Project :</b></span>
                            <input type="text" name="sap_project" id="sap_project" style="text-transform: uppercase;text-align: left;width:70%;" value="<?php echo $kode_project; ?>"  readonly>
                            	
                            <button class="btn btn-block btn-primary" id="po" style="padding:6px 12px;margin-top:-3px;border-radius:2px;margin-left:2px" type="button" onClick="javascript:TampilSAP()">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </div>
                        <input type="hidden" name="rowid" id="rowid" value="<?php echo $rowid; ?>" >	
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Receiver :</b></span>
							<textarea name="receiver" id="receiver"
							style="height:40px;width: 70%; font-size: 11px; line-height: 12px; padding: 5px;" ><?php echo $receiver; ?></textarea>
						</div>
						<div style="width:100%;" class="input-group">
							<span class="input-group-addon" style="text-align:right;"><b>Remark :</b></span>
							<textarea name="remark" id="remark" style="height:40px;width: 70%; font-size: 11px; line-height: 12px; padding: 5px;" ><?php echo $remark; ?></textarea>
						</div>
						<br>	
					</div>
				</div>

				<?php if($mode != 'Add'){?>	
					<div class="col-md-12" >
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;min-height:95px">
							<?php if($mode == 'Edit'){?>
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
					$link = "jo.php";
				?>

				<div class="col-md-12" >
					<div style="width:100%;background:none;margin-left:0;margin-top:0px;border-top:0px;border-bottom:0px" class="input-group">
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
	

	<!-- ---------- MODAL NAMBAH DETAIL SO -->
	<div class="modal fade" id="Data"  role="dialog" aria-labelledby="myModalLabel">
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
							<input type="hidden" id="id_jo" value="<?php echo $id_jo;?>"/>
							<input type="hidden" id="id_quo" value="<?php echo $id_quo;?>"/>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SQ Route :</b></span>
								<input type="hidden"  id ="id_asal" name="id_asal" style="text-transform:uppercase; text-align: left; width:70%" readonly >
								<input type="text"  id ="origin" name="origin" style="text-transform:uppercase; text-align: left; width:70%" readonly >
								<button class="btn btn-block btn-primary" id="btn_item"
									style="padding:6px 12px 6px 12px; ; margin-top:-3px;border-radius:2px; margin-left:5px" type="button" onClick="javascript:TampilSQ()">
									<span class="glyphicon glyphicon-search"></span>
								</button>	
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Destination :</b></span>
								<input type="hidden"  id ="id_tujuan" name="id_tujuan" style="text-transform:uppercase; text-align: left; width:70%" readonly >
								<input type="text" id ="destination" name="destination" style="text-transform:uppercase; text-align: left;width:80%" readonly>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Type :</b></span>
								<input type="text" id ="jenis_mobil" name="jenis_mobil" style="text-transform:uppercase; text-align: left;width:80%" readonly>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Price :</b></span>
								<input type="text" id ="harga" name="harga" style="text-transform:uppercase; text-align: left;width:80%" readonly>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Travel Expense :</b></span>
								<input type="text" id ="uj" name="uj" style="text-transform:uppercase; text-align: left;width:80%" readonly>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Ritase :</b></span>
								<input type="text" id ="ritase" name="ritase" style="text-transform:uppercase; text-align: left;width:80%" readonly>
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>PPH :</b></span>
								<input type="text" id ="pph" name="container" style="text-transform:uppercase; text-align: left;width:80%">
							</div>	

							<div style="width:100%; display:none" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Container :</b></span>
								<input type="text" id ="container" name="container" style="text-transform:uppercase; text-align: left;width:80%">
							</div>	

							<div style="width:100%;display:none" class="input-group">
								<span class="input-group-addon" style="text-align:right;"><b>Remark:</b></span>
								<textarea name="remark" id="remark" style="text-transform:uppercase; width: 80%; height: 50px; font-size: 11px; line-height: 12px;"></textarea>
							</div>
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddDataSO()">
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

    <!-- --------- MODAL ITEM PR --------- -->
	<div class="modal fade" id="DaftarItemPR"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">	
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Item SQ</b>
							</div>	
							<br>
							<div style="width:100%" class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Search :</b>&nbsp;&nbsp;
									<input type="text"  id ="cari" name="cari" value="<?php echo $cari; ?>" 
									style="text-align: left;width:200px" onkeypress="ListItemPR()" >
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
								<div class="tampil_item_sq"></div>
							</div>
							<br>
						</div>		
					</div>		
				</div>	
			</div>
		</div>	
    </div>
	
    <!-- ---------- MODAL SEARCH SAP PROJECT ---------- -->
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
									<input type="text" id ="cari_SAP" style="text-align: left;width:200px">

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
	
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
