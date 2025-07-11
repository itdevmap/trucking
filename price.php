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
	
	<!-- ---------------------- LEAFLET ---------------------- -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

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
					// alert(data.origin_address);
					$("#id_asal").val(data.id_asal);
					$("#id_tujuan").val(data.id_tujuan);
					$("#origin_address").val(data.origin_address);
					$("#origin_lon").val(data.origin_lon);
					$("#origin_lat").val(data.origin_lat);
					$("#destination_address").val(data.destination_address);
					$("#destination_lon").val(data.destination_lon);
					$("#destination_lat").val(data.destination_lat);
					$("#distance_result").val(data.km);
					$("#price_type").val(data.price_type && data.price_type !== "" ? data.price_type : "high");

					$("#jenis_mobil").val(data.jenis_mobil);

					
					$("#km").val(data.km);
					$("#rate").val(Desimal(data.rate));
					$("#uj").val(Desimal(data.uj));
					$("#ritase").val(Desimal(data.ritase));
					$("#stat").val(data.status);
					$("#mode").val('Edit');
					origin_address();
					destination_address();
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
				var origin_address = $("#origin_address").val();
				var origin_lat = $("#origin_lat").val();
				var origin_lon = $("#origin_lon").val();

				var destination_address = $("#destination_address").val();
				var destination_lat = $("#destination_lat").val();
				var destination_lon = $("#destination_lon").val();

				var rate = $("#rate").val();
				var jenis_mobil = $("#jenis_mobil").val();
				var km = $("#distance_result").val();
				var uj = $("#uj").val();
				var ritase = $("#ritase").val();
				var stat = $("#stat").val();
				var mode = $("#mode").val();
				var hal = $("#hal").val();
				var price_type = $("#price_type").val();

				$.post("ajax/price_crud.php", {
					id:id,
					id_asal:id_asal,
					id_tujuan:id_tujuan,

					origin_address:origin_address,
					origin_lat:origin_lat,
					origin_lon:origin_lon,

					destination_address:destination_address,
					destination_lat:destination_lat,
					destination_lon:destination_lon,

					jenis_mobil:jenis_mobil,
					km:km,
					rate:rate,
					ritase:ritase,
					uj:uj,
					stat:stat,
					mode:mode,
					stat:stat,
					price_type:price_type,
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

		// ------------------- FUNCTION CHECK LOCATION -------------------
		let origin_lat = null;
		let origin_lon = null;
		let dest_lat = null;
		let dest_lon = null;

		$(document).ready(function () {
			let lastOriginValue = '';
			let lastDestinationValue = '';

			// =========================
			// ORIGIN ADDRESS HANDLER
			// =========================
			$('#origin_address').on('keydown', function (event) {
				if (event.key === "Enter") {
					event.preventDefault();
					const val = $(this).val().trim();
					if (val.length > 5 && val !== lastOriginValue) {
						lastOriginValue = val;
						console.log("Menjalankan origin_address() via ENTER:", val);
						origin_address();
					}
				}
			});

			$('#origin_address').on('blur', function () {
				const val = $(this).val().trim();
				if (val.length > 5 && val !== lastOriginValue) {
					lastOriginValue = val;
					console.log("Menjalankan origin_address() via BLUR:", val);
					origin_address();
				}
			});

			// =============================
			// DESTINATION ADDRESS HANDLER
			// =============================
			$('#destination_address').on('keydown', function (event) {
				if (event.key === "Enter") {
					event.preventDefault();
					const val = $(this).val().trim();
					if (val.length > 5 && val !== lastDestinationValue) {
						lastDestinationValue = val;
						console.log("Menjalankan destination_address() via ENTER:", val);
						destination_address();
					}
				}
			});

			$('#destination_address').on('blur', function () {
				const val = $(this).val().trim();
				if (val.length > 5 && val !== lastDestinationValue) {
					lastDestinationValue = val;
					console.log("Menjalankan destination_address() via BLUR:", val);
					destination_address();
				}
			});
		});

		function origin_address() {
			const origin_address = $("#origin_address").val();

			$.post("ajax/geoapify.php", {
				address: origin_address,
				type: 'origin'
			}, function (data) {
				console.log("Data dari server (origin):", data);

				if (data.status !== "success") {
					alert("Gagal mendapatkan lokasi: " + data.message);
					console.warn("Error response dari geoapify:", data);

					origin_lat = null;
					origin_lon = null;
					$("#origin_lat").val('');
					$("#origin_lon").val('');
					$("#origin_map").hide();

					if (window.originMap) {
						window.originMap.remove();
						window.originMap = null;
					}
					return;
				}

				origin_lat = data.lat;
				origin_lon = data.lon;

				// ✅ Tampilkan ke input lat/lon
				$("#origin_lat").val(origin_lat.toFixed(6));
				$("#origin_lon").val(origin_lon.toFixed(6));

				$('#origin_map').css('display', 'block');

				if (window.originMap) {
					window.originMap.remove();
				}

				window.originMap = L.map('origin_map').setView([origin_lat, origin_lon], 15);
				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: '© OpenStreetMap contributors'
				}).addTo(window.originMap);

				let marker = L.marker([origin_lat, origin_lon], { draggable: true }).addTo(window.originMap)
					.bindPopup("Klik di peta untuk pindahkan marker").openPopup();

				marker.on('dragend', function (e) {
					let pos = e.target.getLatLng();
					updateLocation(pos.lat, pos.lng, marker);
				});

				window.originMap.on('click', function (e) {
					let lat = e.latlng.lat;
					let lon = e.latlng.lng;
					marker.setLatLng([lat, lon]);
					updateLocation(lat, lon, marker);
				});

				function updateLocation(lat, lon, markerRef) {
					console.log("updateLocation fired", lat, lon);
					origin_lat = lat;
					origin_lon = lon;

					$("#origin_lat").val(lat.toFixed(6));
					$("#origin_lon").val(lon.toFixed(6));

					$.post("ajax/geoapify.php", {
						lat: lat,
						lon: lon
					}, function (res) {
						if (res.status === "success") {
							let newAddress = res.formatted;
							$("#origin_address").val(newAddress);
							markerRef.bindPopup(`Lokasi baru:<br>${newAddress}<br>Lat: ${lat.toFixed(6)}<br>Lon: ${lon.toFixed(6)}`).openPopup();
						} else {
							markerRef.bindPopup(`Lat: ${lat}<br>Lon: ${lon}<br>${res.message}`).openPopup();
						}
					}, 'json');
				}

				if (origin_lat && origin_lon && typeof dest_lat !== 'undefined' && typeof dest_lon !== 'undefined') {
					hitungJarakDanTampilkan();
				}
			}, 'json');
		}

		function destination_address() {
			const destination_address = $("#destination_address").val();

			$.post("ajax/geoapify.php", {
				address: destination_address,
				type: 'destination'
			}, function (data) {
				console.log("Data dari server (destination):", data);

				if (data.status !== "success") {
					alert("Gagal mendapatkan lokasi tujuan: " + data.message);
					console.warn("Response error:", data);

					dest_lat = null;
					dest_lon = null;
					$("#destination_lat").val('');
					$("#destination_lon").val('');
					$("#destination_map").hide();

					if (window.destinationMap) {
						window.destinationMap.remove();
						window.destinationMap = null;
					}
					return;
				}

				dest_lat = data.lat;
				dest_lon = data.lon;

				$("#destination_lat").val(dest_lat.toFixed(6));
				$("#destination_lon").val(dest_lon.toFixed(6));

				$('#destination_map').css('display', 'block');

				if (window.destinationMap) {
					window.destinationMap.remove();
				}

				window.destinationMap = L.map('destination_map').setView([dest_lat, dest_lon], 15);
				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: '© OpenStreetMap contributors'
				}).addTo(window.destinationMap);

				let marker = L.marker([dest_lat, dest_lon], { draggable: true }).addTo(window.destinationMap)
					.bindPopup("Klik di peta untuk ubah titik tujuan").openPopup();

				marker.on('dragend', function (e) {
					let pos = e.target.getLatLng();
					updateDestinationLocation(pos.lat, pos.lng, marker);
				});

				window.destinationMap.on('click', function (e) {
					let lat = e.latlng.lat;
					let lon = e.latlng.lng;
					marker.setLatLng([lat, lon]);
					updateDestinationLocation(lat, lon, marker);
				});

				function updateDestinationLocation(lat, lon, markerRef) {
					console.log("updateDestinationLocation fired", lat, lon);
					dest_lat = lat;
					dest_lon = lon;

					$("#destination_lat").val(lat.toFixed(6));
					$("#destination_lon").val(lon.toFixed(6));

					$.post("ajax/geoapify.php", {
						lat: lat,
						lon: lon
					}, function (res) {
						if (res.status === "success") {
							let newAddress = res.formatted;
							$("#destination_address").val(newAddress);
							markerRef.bindPopup(`Tujuan diperbarui:<br>${newAddress}<br>Lat: ${lat.toFixed(6)}<br>Lon: ${lon.toFixed(6)}`).openPopup();
						} else {
							markerRef.bindPopup(`Lat: ${lat}<br>Lon: ${lon}<br>${res.message}`).openPopup();
						}
					}, 'json');
				}

				if (origin_lat && origin_lon && dest_lat && dest_lon) {
					hitungJarakDanTampilkan();
				}
			}, 'json');
		}

		function hitungJarakDanTampilkan() {
			const jarak = hitungJarak(origin_lat, origin_lon, dest_lat, dest_lon);
			$('#distance_result').val(jarak.toFixed(2));
		}

		function hitungJarak(lat1, lon1, lat2, lon2) {
			const R = 6371; // radius bumi dalam KM
			const dLat = toRad(lat2 - lat1);
			const dLon = toRad(lon2 - lon1);

			const rLat1 = toRad(lat1);
			const rLat2 = toRad(lat2);

			const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
				Math.cos(rLat1) * Math.cos(rLat2) *
				Math.sin(dLon / 2) * Math.sin(dLon / 2);

			const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
			const d = R * c;
			return d;
		}

		function toRad(value) {
			return value * Math.PI / 180;
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
			<div class="content-wrapper" style="min-height:800px">
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
								<option>Origin</option>
								<option>Destination</option>
								<option>Type</option>
								<option value="<?php echo $field1; ?>" selected><?php echo $field1; ?></option>
							</select>
							<input type="text"  id ="search_name1" name="search_name1" value="<?php echo $search_name1; ?>" 
							style="text-align: left;width:200px" onkeypress="ReadData(1)" >
						</div>	
						<div style="width:100%" class="input-group">
							<span class="input-group-addon" style="text-align:right;"></span>
							<select size="1" id="field2"  name="field2" style="padding:4px;margin-right:2px;width: 85px">
								<option>Origin</option>
								<option>Destination</option>
								<option>Type</option>
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
		
	<!-- --------------------- MODAL ADD DATA --------------------- -->
	<div class="modal fade" id="Data"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">	
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="" style="display: flex; justify-content: space-between; width: auto;font-size:12px; font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7; padding:5px;align-items: center;">
								<div class="small-box bg" style=" text-align:left;margin-bottom:1px;">							
									&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Rate</b>
								</div>	
								<button type="button" class="btn btn-danger" data-dismiss="modal">
								<span class="fa fa-close"></span></button>	
							</div>
							<br>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Origin :</b></span>
								<select size="1" id="id_asal"  style="padding:4px;margin-right:2px;width:80%">
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
							<!-- -------------- ORIGIN CHECK LOCATION -------------- -->
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b></b></span>
								<textarea id="origin_address" class="form-textarea" rows="3" style="width:80%" placeholder="Push Enter To Search Origin"></textarea>
								<div id="origin_map" style="height: 200px; width: 80%;display: none;"></div>
								<input type="text" id="origin_lat" style="width:40%" readonly placeholder="Latitude" />
								<input type="text" id="origin_lon" style="width:40%" readonly placeholder="Longitude" />
								<br>
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Destination :</b></span>
								<select size="1" id="id_tujuan"  style="padding:4px;margin-right:2px;width:80%">
									<?php 
									$t1="select * from m_kota_tr where status = '1'  order by nama_kota";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){  
									?>
									<option value="<?php echo $d1['id_kota'];?>"><?php echo $d1['nama_kota'];?></option>
									<?php }?>
								</select>		
							</div>	
							<!-- -------------- DESTINATION CHECK LOCATION -------------- -->
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b></b></span>
								<textarea id="destination_address" class="form-textarea" rows="3" style="width:80%" placeholder="Push Enter To Search Destination"></textarea>
								<div id="destination_map" style="height: 200px; width: 80%;display: none;"></div>
								<input type="text" id="destination_lat" style="width:40%" readonly placeholder="Latitude" />
								<input type="text" id="destination_lon" style="width:40%" readonly placeholder="Longitude" />
								<br>
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Container Type :</b></span>
								<select size="1" id="jenis_mobil"  style="padding:4px;margin-right:2px;width:80%">
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
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Distance/KM :</b></span>
								<input type="text" id="distance_result" value="" style="text-transform: uppercase;text-align: left;width:80%;" readonly>
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;;min-width:150px"><b>Price :</b></span>
								<input type="text" id="rate" value="" style="text-align: right;width:40%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Road Fee :</b></span>
								<input type="text" id="uj" value="" style="text-align: right;width:40%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Ritase :</b></span>
								<input type="text" id="ritase" value="" style="text-align: right;width:40%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group mb-3">
								<span class="input-group-addon" style="text-align:right; background:none; min-width:150px;">
									<b>Price Type :</b>
								</span>
								<select id="price_type" class="form-select" style="width:40%;">
									<option value="low">Low</option>
									<option value="middle">Middle</option>
									<option value="high" selected>High</option>
								</select>
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Status :</b></span>
								<select id="stat"  style="width: 40%;">
									<option value="1" >Active</option>
									<option value="0" >In Active</option>
								</select>						
							</div>
							<div style="width:100%; margin-top: 1rem;" class="input-group">
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
