<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	//include "lib.php";

	$pq = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='3' ");
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
		$field1 = $_POST['field1'];
		$search_name1 = $_POST['search_name1'];
	}
	else
	{	
		$tahun= date("Y") ;
		$tgl1= date("01-01-$tahunx");
		$tgl2= date("31-12-$tahun");
		$paging='10';
		$hal='1';
		$stat = 'All';
		$field = 'No Order';
		$field1 = 'No DO';
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
		.modal-backdrop.in {
			z-index: 1040 !important;
		}
		.modal.in {
			z-index: 1050 !important;
			overflow-y: auto;
		}

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
			$("#tanggalx").datepicker({
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
			var stat = $("#stat").val();
			var field = $("#field").val();
			var field1 = $("#field1").val();
			var cari1 = $("#search_name1").val();
			$.get("ajax/jo_crud.php", {
				tgl1:tgl1, 
				tgl2:tgl2, 
				field:field,
				stat:stat,
				paging:paging,
				cari:cari,
				field1:field1,
				cari1:cari1,
				hal:hal,
				type:"Read" }, function (data, status) {
				$(".tampil_data").html(data);
				$("#hal").val(hal);
			});
		}		
		
		function GetData(id){	
			var jenis_role = $("#jenis_role").val();
			if (jenis_role == '2'){
				document.getElementById("tampil_ujx").style.display = 'none';
			} else {
				document.getElementById("tampil_ujx").style.display = 'inline';
			}
			$("#id_jo").val(id);
			$.post("ajax/jo_crud.php", {
					id: id, type:"Detil_Data"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#no_jo").val(data.no_jo);
					$("#no_do").val(data.no_do);
					$("#tanggal").val(changeDateFormat(data.tgl_jo));
					$("#no_cont").val(data.no_cont);
					$("#no_seal").val(data.no_seal);
					$("#barang").val(data.barang);
					$("#berat").val(Desimal(data.berat));
					$("#vol").val(Desimal(data.vol));
					$("#jenis_mobil").val(data.jenis_mobil);
					$("#penerima").val(data.penerima);
					$("#id_asal").val(data.id_asal);
					$("#nama_asal").val(data.asal);
					$("#id_tujuan").val(data.id_tujuan);
					$("#nama_tujuan").val(data.tujuan);
					$("#id_mobil").val(data.id_mobil);
					$("#id_supir").val(data.id_supir);	
					$("#biaya").val(Rupiah(data.biaya_kirim));	
					$("#uj").val(Rupiah(data.uj));	
					$("#ritase").val(Rupiah(data.ritase));	
					$("#ket").val(data.ket);		
					$("#mode").val('Edit');		
					if(data.jenis_po == '1')
					{
						document.getElementById('no_do').readOnly=true;
						document.getElementById('no_cont').readOnly=true;
					}else{
						document.getElementById('no_do').readOnly=false;
						document.getElementById('no_cont').readOnly=false;
					}
				}
			);	
			$('#Data').modal('show');
		}
		function AddOrder() {	
			var tanggal = $("#tanggal").val();
			var no_do = $("#no_do").val();
			if(tanggal == '' )
			{
				alert("Tanggal harus diisi !..");
			}
			else if(no_do == '')
			{
				alert("No. DO/PO harus diisi !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {
					var id_jo = $("#id_jo").val();					
					var tanggal = $("#tanggal").val();
					var no_do = $("#no_do").val();
					var penerima = $("#penerima").val();
					var barang = $("#barang").val();
					var berat = $("#berat").val();
					var vol = $("#vol").val();
					var no_cont = $("#no_cont").val();
					var no_seal = $("#no_seal").val();
					var id_asal = $("#id_asal").val();
					var id_tujuan = $("#id_tujuan").val();
					var jenis_mobil = $("#jenis_mobil").val();
					var id_mobil = $("#id_mobil").val();
					var id_supir = $("#id_supir").val();
					var biaya = $("#biaya").val();
					var uj = $("#uj").val();
					var ritase = $("#ritase").val();
					var ket = $("#ket").val();
					var mode = $("#mode").val();
					//alert(id_jo);
					$.post("ajax/jo_crud.php", {
						id_jo:id_jo,
						tanggal:tanggal,
						no_do:no_do,
						penerima:penerima,
						barang:barang,
						berat:berat,
						vol:vol,
						no_cont:no_cont,						
						no_seal:no_seal,						
						id_asal:id_asal,
						id_tujuan:id_tujuan,
						jenis_mobil:jenis_mobil,						
						id_mobil:id_mobil,						
						id_supir:id_supir,
						biaya:biaya,
						uj:uj,
						ritase:ritase,
						ket:ket,
						mode:mode,
						type : "Update_Order"
						}, function (data, status) {
						alert(data);
						$("#Data").modal("hide");				
						ReadData(1);
					});
				}
			}	
		}
		function GetPPN(id) {
			$("#id_ppn").val(id);	
			$.post("ajax/jo_crud.php", {
					id: id, type:"Detil_Data"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#ppn").val(data.ppn);	
					$("#pph").val(data.pph);					
				}
			);
			$("#DataPPN").modal("show");
		}
		function AddPPN() {	
			var r = confirm("Are you sure ?...");
			if (r == true) {
				var id_jo = $("#id_ppn").val();					
				var ppn = $("#ppn").val();
				var pph = $("#pph").val();
				//alert(id_jo);
				$.post("ajax/jo_crud.php", {
					id_jo:id_jo,
					ppn:ppn,
					pph:pph,
					type : "Update_PPN"
					}, function (data, status) {
					alert(data);
					$("#DataPPN").modal("hide");				
					ReadData(1);
				});
			}
			
		}
		function Delete(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/jo_crud.php", {
						id: id, type:"Del_Order"
					},
					function (data, status) {
						 ReadData();
					}
				);
			}
		}
		function Confirm(id) {
			var hal = $("#hal").val();
			var conf = confirm("Are you sure to Closed ?");
			if (conf == true) {
				$.post("ajax/jo_crud.php", {
						id: id,type:"Executed"
					},
					function (data, status) {
						ReadData(hal);
					}
				);
			}
		}
		
		function ListBiaya_Lain(id, stat) {
			$("#id_jo").val(id);
			$("#stat_biaya").val(stat);
			if(stat == '1')
			{
				document.getElementById("btnBiaya").disabled = true;
			}else{
				document.getElementById("btnBiaya").disabled = false;
			}
			var mode = $("#mode").val();
			$.get("ajax/jo_crud.php", {mode:mode, stat:stat, id:id,  type:"List_Biaya_Lain" }, function (data, status) {
				$(".tampil_biaya_lain").html(data);
				});
			$("#DaftarBiayaLain").modal("show");
		}
		function TampilBiayaLain() 
		{			
			$("#biaya_lain").val('');
			$("#mode_biaya_lain").val('Add');
			$('#DataBiayaLain').modal('show');
		}
		function AddBiayaLain() {
			var id_jo = $("#id_jo").val();
			var id = $("#id_biaya_lain").val();
			var id_cost = $("#id_cost_biaya").val();
			var biaya = $("#biaya_lain").val();
			var pph = $("#pph").val();
			var wtax = $("#wtax").val();
			var mode = $("#mode_biaya_lain").val();
			$.post("ajax/jo_crud.php", {
				id_jo:id_jo,
				id:id,
				id_cost:id_cost,
				biaya:biaya,
				pph:pph,
				wtax:wtax,
				mode:mode,
				type : "Add_Biaya_Lain"
				}, function (data, status) {
				alert(data);
				
				var id = $("#id_jo").val();
				var stat = $("#stat_biaya").val();

				$.get("ajax/jo_crud.php", {mode:mode, stat:stat, id:id,  type:"List_Biaya_Lain" }, function (data, status) {
					$(".tampil_biaya_lain").html(data);
				});

				ReadData(1);
				$("#DataBiayaLain").modal("hide");				
				
			});
		}	
		function GetBiayaLain(id) {
			$("#id_biaya_lain").val(id);	
			$.post("ajax/jo_crud.php", {
					id: id, type:"Detil_Biaya_Lain"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_cost").val(data.id_cost);
					$("#biaya_lain").val(Rupiah(data.harga));
					$("#pph").val(Rupiah(data.pph));
					$("#wtax").val(Rupiah(data.wtax));
					$("#mode_biaya_lain").val('Edit');							
				}
			);
			$("#DataBiayaLain").modal("show");
		}
		function DelBiayaLain(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/jo_crud.php", {
						id: id, type:"Del_Biaya_Lain"
					},
					function (data, status) {
						var id = $("#id_jo").val();
						var stat = $("#stat_biaya").val();
						$.get("ajax/jo_crud.php", {stat:stat, id:id,  type:"List_Biaya_Lain" }, function (data, status) {
							$(".tampil_biaya_lain").html(data);
						});
				
						 ReadData(1);
					}
				);
			}
		}
		
		function ListUJ(id, stat) {
			$("#id_jo").val(id);
			$("#stat_uj").val(stat);
			if(stat == '1')
			{
				document.getElementById("btnUJ").disabled = true;
			}else{
				document.getElementById("btnUJ").disabled = false;
			}
			var mode = $("#mode").val();
			$.get("ajax/jo_crud.php", {stat:stat, id:id,  type:"List_UJ" }, function (data, status) {
				$(".tampil_uj").html(data);
				});
			$("#DaftarUJ").modal("show");
		}
		function TampilUJ() 
		{			
			$("#biaya_uj").val('');
			$("#mode_uj").val('Add');
			$('#DataUJ').modal('show');
		}
		function AddUJ() {
			var id_jo = $("#id_jo").val();
			var id = $("#id_uj").val();
			var id_cost = $("#id_cost_uj").val();
			var biaya = $("#biaya_uj").val();
			var mode = $("#mode_uj").val();
			$.post("ajax/jo_crud.php", {
				id_jo:id_jo,
				id:id,
				id_cost:id_cost,
				biaya:biaya,
				mode:mode,
				type : "Add_UJ"
				}, function (data, status) {
				alert(data);
				
				var id = $("#id_jo").val();
				var stat = $("#stat_uj").val();
				$.get("ajax/jo_crud.php", {stat:stat, id:id,  type:"List_UJ" }, function (data, status) {
					$(".tampil_uj").html(data);
				});
				ReadData(1);
				$("#DataUJ").modal("hide");				
				
			});
		}	
		function GetUJ(id) {
			$("#id_uj").val(id);	
			$.post("ajax/jo_crud.php", {
					id: id, type:"Detil_UJ"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#id_cost_uj").val(data.id_cost);
					$("#biaya_uj").val(Rupiah(data.harga));
					$("#mode_uj").val('Edit');							
				}
			);
			$("#DataUJ").modal("show");
		}
		function DelUJ(id) {
			var conf = confirm("Are you sure to Delete ?");
			if (conf == true) {
				$.post("ajax/jo_crud.php", {
						id: id, type:"Del_UJ"
					},
					function (data, status) {
						var id = $("#id_jo").val();
						var stat = $("#stat_uj").val();
						$.get("ajax/jo_crud.php", {stat:stat, id:id,  type:"List_UJ" }, function (data, status) {
							$(".tampil_uj").html(data);
						});
				
						 ReadData(1);
					}
				);
			}
		}
		
		function TampilData() 
		{
			var jenis_role = $("#jenis_role").val();
			if (jenis_role == '2'){
				document.getElementById("tampil_uj").style.display = 'none';
			} else {
				document.getElementById("tampil_uj").style.display = 'inline';
			}
			$("#jenis_po").val('1');
			document.getElementById("po").style.display = 'none';
			document.getElementById("cek_ptl").checked = true;
			document.getElementById("po").style.display = 'inline';
				document.getElementById('no_dox').readOnly=true;
				document.getElementById("cust").style.display = 'none';
			$("#no_sjx").val('-- Auto --');
			$("#id_asalx").val('141');
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
			$("#tanggalx").val(today);	
			$("#no_dox").val('');
			$("#id_tujuanx").val('');
			$("#id_contx").val('');
			$("#jenisx").val('');
			$("#id_mobilx").val('');
			$("#id_supirx").val('');
			$("#id_detil_bc").val('');
			$("#penerimax").val('');
			$("#barangx").val('');
			$("#beratx").val('');
			$("#volx").val('');
			$("#no_contx").val('');
			$("#no_sealx").val('');
			$("#biayax").val('');
			$("#ritasex").val('');
			$("#ketx").val('');
			$("#id_cust").val('');
			$("#nama_cust").val('');
			$("#penerimax").val('');
			CekRate();
			$('#DataBaru').modal('show');
		}
		function TampilPO(){	
			$("#cari_po").val('');
			ListPO();
			$('#DaftarPO').modal('show');
		}
		$(document).on('hidden.bs.modal', '.modal', function () {
			if ($('.modal:visible').length) {
				$('body').addClass('modal-open');
			}
		});
		function ListPO() {	
			var cari = $("#cari_po").val();
			$.get("ajax/jo_crud.php", {cari:cari,  type:"ListPO" }, function (data, status) {
				$(".tampil_po").html(data);
				$("#hal").val(hal);
			});
		}
		function PilihPO(id) {	
		
			$.post("ajax/jo_crud.php", {
					id: id, type:"DetilPO"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					$("#no_dox").val(data.no_tagihan);
					$("#id_detil_bc").val(id);
					$("#id_contx").val(data.id_cont);
					$("#id_cust").val('1');
					$("#nama_cust").val('PLANET TRANS LOGISTIC, PT');
					$("#penerimax").val(data.nama_cust+'\n'+data.alamat_ambil);
					$("#barangx").val(data.ket);
					$("#beratx").val(Desimal(data.berat));
					$("#volx").val(Desimal(data.vol));
					$("#no_contx").val(data.no_cont);
					$("#id_asalx").val(data.id_asal);
					$("#id_tujuanx").val(data.id_kota);
					$("#jenisx").val(data.feet);
					CekRate();
				}
			);
			$("#DaftarPO").modal("hide");
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
		function AddBaru() {
			var id_cust = $("#id_cust").val();
			var no_do = $("#no_dox").val();
			var tanggal = $("#tanggalx").val();
			var id_asal = $("#id_asalx").val();
			var id_tujuan = $("#id_tujuanx").val();

			if(tanggal == '')
			{
				alert ("Tanggal harus diisi !..");				
			}			
			else if(no_do == '')
			{
				alert ("No DO/PO harus diisi !..");				
			}
			else if(id_cust == '')
			{
				alert ("Customer harus diisi !..");				
			}
			else if(id_asal == '')
			{
				alert ("Asal harus diisi !..");				
			}
			else if(id_tujuan == '')
			{
				alert ("Tujuan harus diisi !..");				
			}
			else
			{
				var tanggal = $("#tanggalx").val();
				var id_cust = $("#id_cust").val();
				var id_detil_bc = $("#id_detil_bc").val();
				var id_cont = $("#id_contx").val();
				var jenis_po = $("#jenis_po").val();
				var no_do = $("#no_dox").val();
				var penerima = $("#penerimax").val();
				var barang = $("#barangx").val();
				var berat = $("#beratx").val();
				var vol = $("#volx").val();
				var no_cont = $("#no_contx").val();
				var no_seal = $("#no_sealx").val();
				var id_asal = $("#id_asalx").val();
				var id_tujuan = $("#id_tujuanx").val();
				var jenis = $("#jenisx").val();
				var id_mobil = $("#id_mobilx").val();
				var id_supir = $("#id_supirx").val();
				var biaya = $("#biayax").val();
				var uj = $("#ujx").val();
				var ritase = $("#ritasex").val();
				var ket = $("#ketx").val();
				$.post("ajax/jo_crud.php", {
					tanggal:tanggal,
					id_detil_bc:id_detil_bc,
					jenis_po:jenis_po,
					id_cont:id_cont,
					id_cust:id_cust,
					no_do:no_do,
					penerima:penerima,
					barang:barang,
					berat:berat,
					vol:vol,
					no_cont:no_cont,
					no_seal:no_seal,
					id_asal:id_asal,
					id_tujuan:id_tujuan,
					jenis:jenis,
					id_mobil:id_mobil,
					id_supir:id_supir,
					biaya:biaya,
					uj:uj,
					ritase:ritase,
					ket:ket,
					type : "Add_Order"
				}, function (data, status) {
					alert(data);
					$("#DataBaru").modal("hide");				
					ReadData(1);
				});
			}
			
		}	
		function CekRate()
		{
			var id_asal = $("#id_asalx").val();
			var id_tujuan = $("#id_tujuanx").val();
			var jenis_mobil = $("#jenisx").val();
			var id_cust = $("#id_cust").val();
			//alert(id_cust);
			$("#biaya_kirim").val('0');	
			$.post("ajax/quo_crud.php", {
				id_cust:id_cust, id_asal: id_asal, id_tujuan:id_tujuan, jenis_mobil:jenis_mobil, type:"Cek_Rate_Cust"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					//alert(data.status);
					if(data.status == 200)
					{
						CekRate_Umum();
					}
					else
					{
						$("#biayax").val(Rupiah(data.rate));
						$("#ujx").val(Rupiah(data.uj));
						$("#ritasex").val(Rupiah(data.ritase));
					}
					
					
				}
			);
		}
		function CekRate_Umum()
		{
			//alert('ddd');
			var id_asal = $("#id_asalx").val();
			var id_tujuan = $("#id_tujuanx").val();
			var jenis_mobil = $("#jenisx").val();
			//alert(id_asal+'-'+id_tujuan+'-'+jenis_mobil);
			$("#biaya_kirimx").val('');	
			$.post("ajax/quo_crud.php", {
				id_asal: id_asal, id_tujuan:id_tujuan, jenis_mobil:jenis_mobil, type:"Cek_Rate"
				},
				function (data, status) {
					var data = JSON.parse(data);					
					$("#biayax").val(Rupiah(data.rate));
					$("#ujx").val(Rupiah(data.uj));
					$("#ritasex").val(Rupiah(data.ritase));
				}
			);
		}
		function CekPTL(cb) {
			$("#id_cust").val('');
			$("#nama_cust").val('');
			var checkBox = document.getElementById("cek_ptl");
			if (checkBox.checked == true){
				$("#jenis_po").val('1');
				document.getElementById("po").style.display = 'inline';
				document.getElementById('no_dox').readOnly=true;
				document.getElementById("cust").style.display = 'none';
			
			} else {				
				$("#jenis_po").val('0');
				document.getElementById("po").style.display = 'none';
				document.getElementById('no_dox').readOnly=false;
				document.getElementById("cust").style.display = 'inline';
			}
		}	
		function Download() 
		{
			var tgl1 = $("#tgl1").val();
			var tgl2 = $("#tgl2").val();	
			var cari = $("#search_name").val();
			var stat = $("#stat").val();
			var field = $("#field").val();
			var field1 = $("#field1").val();
			var cari1 = $("#search_name1").val();
			var id = tgl1+'|'+tgl2+'|'+stat+'|'+field+'|'+cari+'|'+field1+'|'+cari1;
			var idx = btoa(id);
			var win = window.open('jo_excel.php?id='+idx);
		}	

		// ------------------- FUNCTION ADD ATTACHMENT -------------------
		// function AddAttc(id_jo) {
		// 	document.getElementById('id_jo_attc').value = id_jo;
		// 	$('#DataAttc').modal('show');
		// }
		function AddAttc(id_jo) {
			$('#id_jo_attc').val(id_jo);
			$('.view_so').text('-'); 
			$('.view_sj').text('-'); 
			$('.view_mutasi').text('-');

			$('#DataAttc').modal('show');

			$.ajax({
				url: 'ajax/get_attachment_by_idjo.php',
				method: 'POST',
				data: { id_jo: id_jo },
				dataType: 'json',
				success: function (res) {
					if (res.status === 200) {
						res.data.forEach(function(file) {
							if (file.includes('foto_so_')) {
								$('.view_so').text(file);
							} else if (file.includes('surat_jalan_')) {
								$('.view_sj').text(file);
							} else if (file.includes('mutasi_rekening_')) {
								$('.view_mutasi').text(file);
							}
						});
					} else {
						console.warn(res.msg || 'No attachment found');
					}
				},
				error: function (xhr, err) {
					console.error("AJAX error:", err);
				}
			});
		}


		// function SaveAttc() {
		// 	const fileSo     = document.getElementById('file_so').files[0];
		// 	const fileSj     = document.getElementById('file_sj').files[0];
		// 	const fileMutasi = document.getElementById('file_mutasi').files[0];
		// 	const idJo       = document.getElementById('id_jo_attc').value;

		// 	if (!fileSo && !fileSj && !fileMutasi) {
		// 		alert("Minimal satu file harus dipilih.");
		// 		return;
		// 	}
		// 	if (!idJo) {
		// 		alert("ID JO wajib diisi.");
		// 		return;
		// 	}

		// 	const formData = new FormData();
		// 	formData.append("id_jo", idJo);
		// 	if (fileSo)     formData.append("file_so", fileSo);
		// 	if (fileSj)     formData.append("file_sj", fileSj);
		// 	if (fileMutasi) formData.append("file_mutasi", fileMutasi);

		// 	$.ajax({
		// 		url: "upload_attachment.php",
		// 		type: "POST",
		// 		data: formData,
		// 		processData: false,
		// 		contentType: false,
		// 		success: function (response) {
		// 			$('#DataAttc').modal('hide');
		// 			// Reset input
		// 			document.getElementById('file_so').value = '';
		// 			document.getElementById('file_sj').value = '';
		// 			document.getElementById('file_mutasi').value = '';
		// 		},
		// 		error: function (xhr, status, error) {
		// 			alert("Gagal upload:\n" + error);
		// 		}
		// 	});
		// }
		// function SaveAttc() {
		// 	var formData = new FormData(document.getElementById('form_attachment'));

		// 	$.ajax({
		// 		url: 'upload_attachment.php',
		// 		type: 'POST',
		// 		data: formData,
		// 		contentType: false,
		// 		processData: false,
		// 		success: function (res) {
		// 			try {
		// 				const result = JSON.parse(res);

		// 				if (result.status === 1) {
		// 					if (result.data.file_so) {
		// 						$('.view_so').text(result.data.file_so);
		// 					}
		// 					if (result.data.file_sj) {
		// 						$('.view_sj').text(result.data.file_sj);
		// 					}
		// 					if (result.data.file_mutasi) {
		// 						$('.view_mutasi').text(result.data.file_mutasi);
		// 					}

		// 					alert(result.message);
		// 					$('#DataAttc').modal('hide');
		// 				} else {
		// 					alert(result.message || "Upload gagal.");
		// 				}
		// 			} catch (e) {
		// 				console.error("Gagal parsing JSON:", res);
		// 				alert("Respon server tidak valid.");
		// 			}
		// 		},
		// 		error: function () {
		// 			alert("Gagal menghubungi server.");
		// 		}
		// 	});
		// }
		function SaveAttc() {
			var formData = new FormData(document.getElementById('form_attachment'));

			$.ajax({
				url: 'upload_attachment.php',
				type: 'POST',
				data: formData,
				contentType: false,
				processData: false,
				success: function (res) {
					if (res.includes("✅")) {
						alert("Upload berhasil!");
						$('#DataAttc').modal('hide');
					} else {
						alert("Gagal upload: " + res);
					}
				},
				error: function (xhr, status, error) {
					console.error("AJAX Error:", status, error);
					alert("Gagal menghubungi server.");
				}
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
		
		<form method="post" name ="myform"  class="form-horizontal" > 
		<div class="content-wrapper" style="min-height:750px">
			<br>
			<ol class="breadcrumb">
				<li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Order</b></font></h1></li>					
			</ol>
			<br>
			
			
			<div class="col-md-12" >
				<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
					<div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
							<b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
					</div>
					<br>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Date :</b></span>
						<input type="text"  id ="tgl1" name="tgl1" value="<?php echo $tgl1; ?>" 
						style="text-align: center;width:85px" onchange="ReadData(1)" readonly >
						&nbsp;&nbsp;<b>s.d</b>&nbsp;&nbsp;
						<input type="text"  id ="tgl2" name="tgl2" value="<?php echo $tgl2; ?>" 
						style="text-align: center;width:85px" onchange="ReadData(1)" readonly >	
					</div>	
					
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Status :</b></span>
						<select id="stat" name ="stat"  style="width: 85px;padding:5px" onchange="ReadData(1)" >
							<option >Open</option>
							<option >Close</option>
							<option >All</option>
							<option value="<?php echo $stat;?>" selected ><?php echo $stat;?></option>
						</select>	
					</div>	
				
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"><b>Filter By :</b></span>
						<select size="1" id="field"  onchange="ReadData(1)" name="field" style="padding:4px;margin-right:2px;width: 85px">
							<option>No Order</option>
							<option>No Quo</option>
							<option>No DO</option>
							<option>Customer</option>
							<option>Origin</option>
							<option>Destination</option>
							<option>No Cont</option>
							<option>No Police</option>
							<option>Driver</option>
							<option value="<?php echo $field; ?>" selected><?php echo $field; ?></option>
						</select>
						<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
					</div>
					<div style="width:100%" class="input-group">
						<span class="input-group-addon" style="text-align:right;"></span>
						<select size="1" id="field1"  onchange="ReadData(1)" name="field1" style="padding:4px;margin-right:2px;width: 85px">
							<option>No Order</option>
							<option>No Quo</option>
							<option>No DO</option>
							<option>Customer</option>
							<option>Origin</option>
							<option>Destination</option>
							<option>No Cont</option>
							<option>No Police</option>
							<option>Driver</option>
							<option value="<?php echo $field1; ?>" selected><?php echo $field1; ?></option>
						</select>
						<input type="text"  id ="search_name1" name="search_name1" value="<?php echo $search_name1; ?>" 
						style="text-align: left;width:200px" onkeypress="ReadData(1)" >
						<input type="hidden"  id ="hal" name="hal" value="<?php echo $hal; ?>" style="text-align: left;width:5%"  >
						<input type="hidden"  id ="jenis_role" name="jenis_role" value="<?php echo $id_role; ?>" style="text-align: left;width:5%"  >
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
								<b>Add Order</b>
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
	
	<div class="modal fade" id="DataBaru"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">	
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">							
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Order</b>
							</div>	
							<br>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Order :</b></span>
								<input type="text"  id ="no_sjx" style="text-align: center;width:22%" readonly  >
								<input type="hidden" id="id_sjx"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								&nbsp;
								<!-- <input type="checkbox" id="cek_ptl" style="margin-bottom:0px;" value="1"  onclick='CekPTL(this);'  > &nbsp;<b>PTL</b> -->
								<input type="hidden" id="cek_ptl" style="margin-bottom:0px;" value="1"  onclick='CekPTL(this);'  ></b>
								<input type="hidden" id="jenis_po"   style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>	

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Project Code :</b></span>
								<input type="text"  id ="project_code" style="text-align: center;width:22%" readonly placeholder="-- Auto --">
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Date :</b></span>
								<input type="text"  id ="tanggalx" style="text-align: center;width:22%" readonly  >
							</div>
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. DO/PO :</b></span>
								<input type="text" id="no_dox" value="" style="text-transform: uppercase;text-align: left;width:80%;"   >	
								<button class="btn btn-block btn-primary" id="po"
									style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-1px" type="button" 
									onClick="javascript:TampilPO()">
									<span class="glyphicon glyphicon-search"></span>
								</button>	
								<input type="hidden" id="id_detil_bc"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id_contx"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Customer :</b></span>
								<input type="text"  id ="nama_cust" style="text-align: left;width:80%" readonly  >
								<button class="btn btn-block btn-primary" id="cust"
									style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-1px" type="button" 
									onClick="javascript:TampilCust()">
									<span class="glyphicon glyphicon-search"></span>
								</button>	
								<input type="hidden" id="id_cust"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Receiver :</b></span>
								<textarea id="penerimax"
								style="resize:none;width: 80%; height: 70px; font-size: 11px; line-height: 12px; 
								border: 1px solid #444; padding: 5px;"  ></textarea>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Item Name :</b></span>
								<input type="text" id="barangx" value="" style="text-transform: uppercase;text-align: left;width:80%;"   >	
							</div>
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Weight :</b></span>
								<input type="text" id="beratx" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  > &nbsp;<b>KG</b>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Vol :</b></span>
								<input type="text" id="volx" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  > &nbsp;<b>M3</b>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Container :</b></span>
								<input type="text"  id ="no_contx" style="text-transform: uppercase;text-align: center;width:22%"  >														
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Seal :</b></span>
								<input type="text"  id ="no_sealx" style="text-transform: uppercase;text-align: center;width:22%"  >														
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Origin :</b></span>
								<select id="id_asalx"  onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_kota_tr where status = '1' order by nama_kota  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_kota'];?>" ><?php echo $d1['nama_kota'];?></option>
									<?php }?>
								</select>
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Destination :</b></span>
								<select id="id_tujuanx" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_kota_tr where status = '1' order by nama_kota  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_kota'];?>" ><?php echo $d1['nama_kota'];?></option>
									<?php }?>
								</select>
							</div>


							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Type :</b></span>
								<select id="jenisx" name="jenisx" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_jenis_mobil_tr where status = '1' order by nama   ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['nama'];?>" ><?php echo $d1['nama'];?></option>
									<?php }?>
								</select>	
							</div>							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Police :</b></span>
								<select id="id_mobilx"  style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_mobil_tr where status = '1' order by no_polisi  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_mobil'];?>" ><?php echo $d1['no_polisi'];?></option>
									<?php }?>
								</select>
							</div>
						
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Driver :</b></span>
								<select id="id_supirx"  style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_supir_tr where status = '1' order by nama_supir  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_supir'];?>" ><?php echo $d1['nama_supir'];?></option>
									<?php }?>
								</select>
							</div>
							
							<div  id="tampil_uj" style="display:none;">
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Deliv. Cost :</b></span>								
								<input type="text" id="biayax" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Road Fee :</b></span>								
								<input type="text" id="ujx" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Ritase :</b></span>								
								<input type="text" id="ritasex" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  readonly>	
							</div>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Remarks :</b></span>
								<input type="text" id="ketx" value="" style="text-align: left;width:80%;"   >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddBaru()">
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
	
	<div class="modal fade" id="DaftarPO"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">	
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data PO</b>
							</div>	
							<br>
							<div style="width:100%" class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Search :</b>&nbsp;&nbsp;
									<input type="text"  id ="cari_po"  
									style="text-align: left;width:200px" onkeypress="ListCust()" >
									<button class="btn btn-block btn-primary" 
									style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:2px;padding:5px" 
									onClick="javascript:ListPO()">
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
									onClick="javascript:ListCust()">
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
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Order :</b></span>
								<input type="text"  id ="no_jo" style="text-align: center;width:22%" readonly  >
								<input type="hidden" id="id_jo"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="mode"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								<input type="hidden" id="id_detil"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>						
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Date :</b></span>
								<input type="text"  id ="tanggal" style="text-align: center;width:22%" readonly  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. DO/PO :</b></span>
								<input type="text" id="no_do" value="" style="text-transform: uppercase;text-align: left;width:80%;">	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Receiver :</b></span>
								<textarea id="penerima"
								style="resize:none;width: 80%; height: 70px; font-size: 11px; line-height: 12px; 
								border: 1px solid #444; padding: 5px;"  ></textarea>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Item Name :</b></span>								
								<input type="text" id="barang" value="" style="text-transform: uppercase;text-align: left;width:80%;"   >	
							</div>
							
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Weight :</b></span>
								<input type="text" id="berat" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  > &nbsp;<b>KG</b>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Vol :</b></span>
								<input type="text" id="vol" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  > &nbsp;<b>M3</b>	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Container :</b></span>
								<input type="text"  id ="no_cont" style="text-transform: uppercase;text-align: center;width:22%"  >														
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Seal :</b></span>
								<input type="text"  id ="no_seal" style="text-transform: uppercase;text-align: center;width:22%"  >														
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Origin :</b></span>
								<input type="hidden" id="id_asal"   value=""  />
								<input type="text" id="nama_asal" value="" style="text-transform: uppercase;text-align: left;width:80%;"  readonly >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Destination :</b></span>
								<input type="hidden" id="id_tujuan"   value=""  />
								<input type="text" id="nama_tujuan" value="" style="text-transform: uppercase;text-align: left;width:80%;"  readonly >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Type :</b></span>
								<input type="text" id="jenis_mobil" value="" style="text-transform: uppercase;text-align: left;width:80%;"  readonly >
							</div>							
							<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Police :</b></span>
									<select id="id_mobil"  style="width: 80%;padding:4px">
										<?php
										$t1="select * from m_mobil_tr where status = '1' order by no_polisi  ";
										$h1=mysqli_query($koneksi, $t1);       
										while ($d1=mysqli_fetch_array($h1)){?>
										<option value="<?php echo $d1['id_mobil'];?>" ><?php echo $d1['no_polisi'];?></option>
										<?php }?>
									</select>
							</div>
						
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Driver :</b></span>
								<select id="id_supir"  style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_supir_tr where status = '1' order by nama_supir  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_supir'];?>" ><?php echo $d1['nama_supir'];?></option>
									<?php }?>
								</select>
							</div>
							
							<div  id="tampil_ujx" style="display:none;">
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Deliv. Cost :</b></span>								
								<input type="text" id="biaya" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Road Fee:</b></span>								
								<input type="text" id="uj" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Ritase :</b></span>								
								<input type="text" id="ritase" value="0" style="text-align: right;width:22%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  readonly>	
							</div>
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Remarks :</b></span>
								<input type="text" id="ket" value="" style="text-align: left;width:80%;"   >	
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddOrder()">
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
	
	<div class="modal fade" id="DaftarBiayaLain"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document" style="width:750px;">
			<div class="modal-content" style="background: none">
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Other Cost</b>
							</div>	
							<div  class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:50%;text-align:left;padding:0px;background: none;">									
									<input type="hidden"  id ="id_jo" name="id" value=""   >
									<input type="hidden"  id ="stat_biaya" name="id" value=""   >
								</span>								
							</div>	
							<?php if($m_add == '1'){?>
							<button class="btn btn-block btn-success" id="btnBiaya"
								style="margin-left:1px; margin-bottom:2px;padding:2px;padding-left:5px;padding-right:6px" type="button" 
								onClick="javascript:TampilBiayaLain()"   >
								<span class="fa  fa-plus-square"></span>
								<b>Add Data</b>
							</button>
							<?php }?>
							<button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-left:-1px; margin-bottom:2px;padding:2px;padding-left:5px;padding-right:6px">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Close</b></button>	
							<div class="table-responsive mailbox-messages" >									
								<div class="tampil_biaya_lain"></div>
							</div>
							
						</div>
					</div>		
				</div>	
			</div>
		</div>	
    </div>
	
	<div class="modal fade" id="DataBiayaLain"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Other Cost Data</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Cost Name :</b></span>
								<select id="id_cost_biaya" style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_cost_tr where status = '1' and id_cost <> '1' order by nama_cost  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_cost'];?>" ><?php echo $d1['nama_cost'];?></option>
									<?php }?>
								</select>	
								<input type="hidden" id="id_biaya_lain"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="mode_biaya_lain"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Cost :</b></span>
								<input type="text" id="biaya_lain" style="text-align: right;width:20%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>PPN :</b></span>
								<input type="text" id="pph" style="text-align: right;width:20%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>WTAX :</b></span>
								<input type="text" id="wtax" style="text-align: right;width:20%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddBiayaLain()">
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
	
	<div class="modal fade" id="DaftarUJ"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document" style="width:750px;">
			<div class="modal-content" style="background: none">
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Other AP</b>
							</div>	
							<div  class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:50%;text-align:left;padding:0px;background: none;">	
								<input type="hidden"  id ="stat_uj" name="id" value=""   >
								</span>								
							</div>	
							<?php if($m_add == '1'){?>
							<button class="btn btn-block btn-success" id="btnUJ"
								style="margin-left:1px; margin-bottom:2px;padding:2px;padding-left:5px;padding-right:6px" type="button" 
								onClick="javascript:TampilUJ()"   >
								<span class="fa  fa-plus-square"></span>
								<b>Add Data</b>
							</button>
							<?php }?>
							<button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-left:-1px; margin-bottom:2px;padding:2px;padding-left:5px;padding-right:6px">
								<span class="fa fa-close"></span>&nbsp;&nbsp;<b>Close</b></button>	
							<div class="table-responsive mailbox-messages" >									
								<div class="tampil_uj"></div>
							</div>
							
						</div>
					</div>		
				</div>	
			</div>
		</div>	
    </div>
	
	<div class="modal fade" id="DataUJ"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Other AP Data</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Cost Name :</b></span>
								<select id="id_cost_uj" style="width: 80%;padding:4px">
									<?php
									$t1="select * from m_cost_tr where status = '1' and id_cost <> '1' order by nama_cost  ";
									$h1=mysqli_query($koneksi, $t1);       
									while ($d1=mysqli_fetch_array($h1)){?>
									<option value="<?php echo $d1['id_cost'];?>" ><?php echo $d1['nama_cost'];?></option>
									<?php }?>
								</select>	
								<input type="hidden" id="id_uj"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
								<input type="hidden" id="mode_uj"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
							</div>	
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Biaya :</b></span>
								<input type="text" id="biaya_uj" style="text-align: right;width:20%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  >
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddUJ()">
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
	
	
	<div class="modal fade" id="DataPPN"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Tax Data</b>
							</div>	
							<br>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>PPN :</b></span>
								<input type="text" id="ppn" style="text-align: center;width:11%;" 
								onkeypress="return isNumber(event)"  > %
								<input type="hidden" id="id_ppn"   value="" style="text-align: right;width:20%;border:1px solid rgb(169, 169, 169)" /> 
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>WTax :</b></span>
								<input type="text" id="pph" style="text-align: center;width:11%;" 
								onkeypress="return isNumber(event)"  > %
							</div>
							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
								<button type="button" class="btn btn-success"  onclick="AddPPN()">
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

	<!-- ----------------- MODAL ATTACHMENT ----------------- -->
	<div class="modal fade" id="DataAttc" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="col-md-12" style="padding: 0;">
						<div class="box box-success box-solid" style="padding: 5px; border: 1px solid #ccc;">
							<div class="small-box bg" style="font-size:12px;font-family:'Arial';color:#fff;margin:0;background-color:#4783b7;padding:5px;">
								<b><i class="fa fa-list"></i>&nbsp;Add Attachment</b>
							</div>
							<form id="form_attachment" enctype="multipart/form-data" style="margin-top: 2rem;">
								<input type="hidden" id="id_jo_attc" name="id_jo">
								<div class="form-group mt-3">
									<label for=""><b>File SO:</b></label>
									<input type="file" class="form-control" id="file_so" name="file_so">
									<p class=""><b>Berhasil Upload Berkas : </b><span class="view_so"></span></p>
								</div>
								<div class="form-group mt-3">
									<label for=""><b>File SJ:</b></label>
									<input type="file" class="form-control" id="file_sj" name="file_sj">
									<p class=""><b>Berhasil Upload Berkas : </b><span class="view_sj"></span></p>
								</div>
								<div class="form-group mt-3">
									<label for=""><b>File Mutasi:</b></label>
									<input type="file" class="form-control" id="file_mutasi" name="file_mutasi">
									<p class=""><b>Berhasil Upload Berkas : </b><span class="view_mutasi"></span></p>
								</div>

								<div class="form-group mt-3 text-right">
									<button type="button" class="btn btn-success" onclick="SaveAttc()">
										<span class="fa fa-save"></span>&nbsp;<b>Save</b>
									</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal">
										<span class="fa fa-close"></span>&nbsp;<b>Cancel</b>
									</button>
								</div>
							</form>
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
