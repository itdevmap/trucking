<?php
	session_start();
	include "koneksi.php"; 
	include "session_log.php"; 
	//include "lib.php";

	$pq = mysqli_query($koneksi,"SELECT * FROM m_role_akses_tr WHERE id_role = '$id_role' AND id_menu ='3' ");
	$rq 	= mysqli_fetch_array($pq);	
	$m_edit = $rq['m_edit'];
	$m_add 	= $rq['m_add'];
	$m_del 	= $rq['m_del'];
	$m_view = $rq['m_view'];
	$m_exe  = $rq['m_exe'];

	if(!isset($_SESSION['id_user'])  ||  $m_view != '1'  ){
		header('location:logout.php'); 
	}

	if($_SERVER['REQUEST_METHOD'] == "POST"){	
		$hal		 = '1';	
		$field 		 = $_POST['field'];
		$search_name = $_POST['search_name'];
		$tgl1 		 = $_POST['tgl1'];
		$tgl2 		 = $_POST['tgl2'];
		$paging 	 = $_POST['paging'];
		$stat 		 = $_POST['stat'];
		$field1 	 = $_POST['field1'];
		$search_name1= $_POST['search_name1'];
	} else {	
		$tahun	= date("Y") ;
		$tgl1	= date("01-01-$tahunx");
		$tgl2	= date("31-12-$tahun");
		$paging	= '10';
		$hal	= '1';
		$stat 	= 'All';
		$field 	= 'No Order';
		$field1 = 'No Quo';
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

	<!-- ============== LEAFLET ============== -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
	
	<style>
		.datepicker{
			z-index:1151 !important;
		}
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



			let delayTimer;
			function debounceSearch(callback, delay) {
				clearTimeout(delayTimer);
				delayTimer = setTimeout(callback, delay);
			}

			$("#cari_cont, #cari_vendor").on("keyup", function() {
				clearTimeout(this.delay);
				this.delay = setTimeout(function() {
					ListPO();
				}.bind(this), 300);
			});

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
		function ReadData(hal) {
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
				stat:stat,
				paging:paging,
				cari:cari,
				field:field,
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
				$("#tampil_ujx").hide();
			} else {
				$("#tampil_ujx").show();
			}

			$("#id_jo").val(id);

			$.post("ajax/jo_crud.php", { id: id, type:"Detil_Data" }, function (res) {
				var data = JSON.parse(res);

				$("#no_jo").val(data.no_jo);
				$("#project_codex").val(data.project_code);
				$("#sap_projectx").val(data.kode_project);

				$("#no_do").val(data.no_do);
				$("#sj_custx").val(data.sj_cust);
				$("#no_sqx").val(data.quo_no);
				$("#no_po").val(data.code_po);

				$("#tanggal").val(changeDateFormat(data.tgl_jo));
				$("#jenis_mobil").val(data.jenis_mobil);
				$("#penerima").val(data.penerima);
				$("#id_asal").val(data.id_asal);
				$("#nama_asal").val(data.asal);
				$("#id_tujuan").val(data.id_tujuan);
				$("#nama_tujuan").val(data.tujuan);
				$("#biaya").val(Rupiah(data.harga));	
				$("#uj").val(Rupiah(data.uj));	
				$("#ritase").val(Rupiah(data.ritase));	
				$("#stapel").val(Rupiah(data.stapel));	
				$("#cont_edit").val(data.container);		
				$("#ket").val(data.ket);		
				$("#pph").val(data.pph);		
				$("#mode").val('Edit');		

				if(data.jenis_po == '1'){
					$("#no_do, #no_cont").prop("readonly", true);
				} else {
					$("#no_do, #no_cont").prop("readonly", false);
				}
			});

			$('#ModelEdit').modal('show');
		}

		function AddOrder() {
			var tanggal = $("#tanggal").val();
			var no_do = $("#no_do").val();

			if(tanggal == '' )
			{
				alert("Tanggal harus diisi !..");
			}
			else if(no_do === '')
			{
				alert("No. PO harus diisi !..");
			}
			else
			{
				var r = confirm("Are you sure ?...");
				if (r == true) {
					var id_jo = $("#id_jo").val();					
					var tanggal = $("#tanggal").val();
					var no_do = $("#no_do").val();
					var penerima = $("#penerima").val();
					var id_asal = $("#id_asal").val();
					var id_tujuan = $("#id_tujuan").val();
					var jenis_mobil = $("#jenis_mobil").val();
					var biaya = $("#biaya").val();
					var stapel = $("#stapel").val();
					var uj = $("#uj").val();
					var ritase = $("#ritase").val();
					var ket = $("#ket").val();
					var mode = $("#mode").val();
					var sap_project = $("#sap_project").val();
					var sj_custx = $("#sj_custx").val();
					var cont_edit = $("#cont_edit").val();

					$.post("ajax/jo_crud.php", {
						id_jo:id_jo,
						sj_custx:sj_custx,
						cont_edit:cont_edit,
						tanggal:tanggal,
						no_do:no_do,
						penerima:penerima,					
						id_asal:id_asal,
						id_tujuan:id_tujuan,
						jenis_mobil:jenis_mobil,
						biaya:biaya,
						stapel:stapel,
						uj:uj,
						ritase:ritase,
						ket:ket,
						mode:mode,
						type : "Update_Order"
						}, function (data, status) {
						alert(data);
						$("#ModelEdit").modal("hide");				
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
						id: id,
						type: "Executed"
					},
					function (data, status) {
						if (data.includes("GAGAL")) {
							alert(data);
						} else {
							alert("Berhasil");
							ReadData(hal);
						}
					}
				).fail(function(xhr, status, error) {
					alert("Request gagal: " + error);
				});
			}
		}

		function ListBiaya_Lain(id, stat) {
			$("#id_jo").val(id);
			$("#stat_biaya").val(stat);

			if(stat == '1' || stat == '3'){
				document.getElementById("btnBiaya").disabled = true;
			} else{
				document.getElementById("btnBiaya").disabled = false;
			}
			var mode = $("#mode").val();
			$.get("ajax/jo_crud.php", {mode:mode, stat:stat, id:id,  type:"List_Biaya_Lain" }, function (data, status) {
				$(".tampil_biaya_lain").html(data);
				});

			$("#DaftarBiayaLain").modal("show");
		}

		function TampilBiayaLain() {
			$("#biaya_lain").val('');
			$("#mode_biaya_lain").val('Add');
			$('#DataBiayaLain').modal('show');
			checkPPH();
			$("#id_cost_biaya").off('change').on('change', function() {
				checkPPH();
			});
		}

		function checkPPH() {
			var id_jo   = $("#id_jo").val();
			var id_cost = $("#id_cost_biaya").val();

			$.post("ajax/jo_crud.php", {
				id_cost: id_cost,
				id_jo: id_jo,
				type: "checkPPH"
			}, function (res) {
				console.log(res);
				if (res.pph_fix) {
					$("#wtax").val(res.pph_fix);
				} else {
					$("#wtax").val(0);
				}
			}, "json");
		}

		function AddBiayaLain() {
			var id_jo 		= $("#id_jo").val();
			var id 			= $("#id_biaya_lain").val();
			var id_cost 	= $("#id_cost_biaya").val();
			var biaya 		= $("#biaya_lain").val();
			var pph 		= $("#pph").val();
			var wtax 		= $("#wtax").val();
			var remark_cost	= $("#remark_cost").val();
			var mode 		= $("#mode_biaya_lain").val();

			$.post("ajax/jo_crud.php", {
				id_jo:id_jo,
				id:id,
				id_cost:id_cost,
				biaya:biaya,
				pph:pph,
				wtax:wtax,
				remark_cost:remark_cost,
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
			// isi ID biaya ke hidden field
			$("#id_biaya_lain").val(id);	

			// ambil data detail biaya lain lewat AJAX
			$.post("ajax/jo_crud.php", { id: id, type: "Detil_Biaya_Lain" }, function (data, status) {
				var data = JSON.parse(data);

				// set dropdown sesuai id_cost dari data
				$("#id_cost_biaya").val(data.id_cost).trigger("change");

				// isi field lain dengan nilai dari data
				$("#biaya_lain").val(Rupiah(data.harga));
				$("#pph").val(Rupiah(data.pph));
				$("#wtax").val(Rupiah(data.wtax));
				$("#remark_cost").val(data.remark);
				$("#mode_biaya_lain").val("Edit");
			});

			// tampilkan modal setelah data di-set
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

			if(stat == '1' || stat == '3'){
				document.getElementById("btnUJ").disabled = true;
			} else{
				document.getElementById("btnUJ").disabled = false;
			}
			var mode = $("#mode").val();
			$.get("ajax/jo_crud.php", {stat:stat, id:id,  type:"List_UJ" }, function (data, status) {
				$(".tampil_uj").html(data);
				});
			$("#DaftarUJ").modal("show");
		}

		function TampilUJ() {
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
		
		function TampilData() {
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

		$(document).on('hidden.bs.modal', '.modal', function () {
			if ($('.modal:visible').length) {
				$('body').addClass('modal-open');
			}
		});

		// ============== AJAX TAMPIL PO PTL ==============
			function TampilPO(){
				$("#cari_po").val('');
				ListPO();
				$('#DaftarPO').modal('show');
			}
			function ListPO() {
				let cari_cont = $("#cari_cont").val();
				let cari_vendor = $("#cari_vendor").val();

				$.get("ajax/jo_crud.php", {
					cari_cont: cari_cont,
					cari_vendor: cari_vendor,
					type: "ListPO"
				}, function(data) {
					$(".tampil_po").html(data);
				});
			}

			function PilihPO(id) {
				$.post("ajax/jo_crud.php", {
						id: id, type:"DetilPO"
					},
					function (data, status) {
						var data = JSON.parse(data);	
						$("#no_dox").val(data.no_tagihan);
						$("#id_contx").val(data.id_cont);
						$("#penerimax").val(data.nama_cust);
						$("#id_po").val(data.id_tagihan);
						$("#pphx").val(data.pph || 0);
						if (data.kode == "AA") {
							$("#id_cust").val('119');
							$("#nama_cust").val('ARTHA ADIPERSADA, PT');
						}
						else if (data.kode == "AMA"){
							$("#id_cust").val('115');
							$("#nama_cust").val('AGRO MANDIRI ABADI, PT');
						} 
						else if (data.kode == "PFW"){
							$("#id_cust").val('141');
							$("#nama_cust").val('PLANET FIREWORK, CV');
						} 
						else if (data.kode == "SIM"){
							$("#id_cust").val('146');
							$("#nama_cust").val('SARANA INTI MAJU, CV');
						} 
						else if (data.kode == "TAJI"){
							$("#id_cust").val('152');
							$("#nama_cust").val('TRITUNGGAL ABADI JAYA, PT');
						} 
						else {
							$("#id_cust").val('1');
							$("#nama_cust").val('PLANET TRANS LOGISTIK, PT');
						}

						CekRate();
					}
				);
				$("#DaftarPO").modal("hide");
			}

		// ============== AJAX TAMPIL SAP PROJECT ==============
			function TampilSAP(){
				$cari = $("#cari_SAP").val('');
				ListSAP();
				$('#DaftarSAP').modal('show');
			}
			function ListSAP() {
				var cari = $("#cari_SAP").val();
				$.get("ajax/jo_crud.php", {cari:cari,  type:"ListSAP" }, function (data, status) {
					$(".tampil_SAP").html(data);
					$("#hal").val(hal);
				});
			}
			function PilihSAP(id) {
				$.post("ajax/jo_crud.php", {
						id: id, type:"DetilSAP"
					},
					function (data, status) {
						var data = JSON.parse(data);	
						$("#sap_project").val(data.kode_project);
						$("#id_sap").val(data.rowid);
					}
				);
				$("#DaftarSAP").modal("hide");
			}

		// ============== AJAX TAMPIL SQ ==============
			function TampilSQ(){
				$cari = $("#cari_SQ").val('');
				ListSQ();
				$('#DaftarSQ').modal('show');
			}
			function ListSQ() {
				var cari = $("#cari_SQ").val();
				$.get("ajax/jo_crud.php", {cari:cari,  type:"ListSQ" }, function (data, status) {
					$(".tampil_SQ").html(data);
					$("#hal").val(hal);
				});
			}
			function PilihSQ(id_quo) {
				$.post("ajax/jo_crud.php", {
						id_quo: id_quo, type:"DetilSQ"
					},
					function (data, status) {
						var data = JSON.parse(data);	
						$("#no_sq").val(data.quo_no);
						$("#id_quo").val(data.id_quo);
						$("#jenisx").val(data.jenis_mobil).trigger("change");
						$("#biayax").val(Rupiah(data.harga));
						$("#ujx").val(0);
						$("#ritasex").val(0);
					}
				);
				$("#DaftarSQ").modal("hide");
			}
		// ============== AJAX TAMPIL PO TR ==============
			function TampilPOTR(){
				$cari = $("#cari_POTR").val('');
				ListPOTR();
				$('#DaftarPOTR').modal('show');
			}
			function ListPOTR() {
				var cari = $("#cari_POTR").val();
				$.get("ajax/jo_crud.php", {cari:cari,  type:"ListPOTR" }, function (data, status) {
					$(".tampil_POTR").html(data);
					$("#hal").val(hal);
				});
			}
			function PilihPOTR(id_po) {
				$.post("ajax/jo_crud.php", {
						id_po: id_po, type:"DetilPOTR"
					},
					function (data, status) {
						var data = JSON.parse(data);	
						$("#id_quo").val(data.id_quo);
						$("#id_po").val(data.id_po);
						$("#is_trucking").val(data.code_po);
						$("#id_sap").val(data.sap_project);
						$("#sap_project").val(data.kode_project);
						$("#no_sq").val(data.quo_no);
						$("#nama_cust").val(data.nama_cust);
						$("#id_cust").val(data.id_cust);
						$("#id_asalx").val(data.id_origin).trigger("change");
						$("#id_tujuanx").val(data.id_destination).trigger("change");
						$("#jenisx").val(data.jenis_mobil.toUpperCase()).trigger("change");
						$("#biayax").val(Rupiah(data.harga));
						$("#pphx").val(Rupiah(data.pph));
						
						$("#ujx").val(0);
						$("#ritasex").val(0);
					}
				);
				$("#DaftarPOTR").modal("hide");
			}

		// ============== UP SO TO SAP ==============
			function TampilUpSAP(id_jo){
				$cari = $("#cari_UpSAP").val('');
				ListUpSAP(id_jo);
				$('#DaftarUpSAP').modal('show');
			}
			function ListUpSAP(id_jo) {
				var cari = $("#cari_UpSAP").val();
				$.get("ajax/jo_crud.php", {cari:cari,id_jo:id_jo,  type:"ListUpSAP" }, function (data, status) {
					$(".tampil_UpSAP").html(data);
					$("#hal").val(hal);
				});
			}
			function SaveUpSAP() {
				let selected = [];
				$('input[name="sap_selected[]"]:checked').each(function () {
					selected.push($(this).val());
				});

				if (selected.length === 0) {
					alert("Pilih minimal 1 data!");
					return;
				}

				$("#btnSaveSAP").prop("disabled", true).text("Processing...");

				$.ajax({
					url: "ajax/jo_crud.php",
					type: "POST",
					data: { type: "SaveUpSAP", ids: selected },
					dataType: "json",
					success: function (res) {
						if (res.success === false) {
							alert("Gagal: " + res.message);
						} else {
							alert("Data berhasil dikirim ke SAP!");
							console.log(res);
							$('#DaftarUpSAP').modal('hide');
						}
					},
					error: function (xhr, status, err) {
						console.error(xhr.responseText);
						alert("Terjadi error: " + err);
					},
					complete: function () {
						$("#btnSaveSAP").prop("disabled", false).text("Save to SAP");
						ReadData();
					}
				});
			}
			$(document).on('click', '#btnSaveSAP', function () {
				SaveUpSAP();
			});

		// ============== UP AR TO SAP ==============
			function TampilUpAR(id_jo){
				$cari = $("#cari_UpAR").val('');
				ListUpAR(id_jo);
				$('#DaftarUpAR').modal('show');
			}
			function ListUpAR(id_jo) {
				var cari = $("#cari_UpAR").val();
				$.get("ajax/jo_crud.php", {cari:cari,id_jo:id_jo,  type:"ListUpAR" }, function (data, status) {
					$(".tampil_UpAR").html(data);
					$("#hal").val(hal);
				});
			}
			function SaveUpAR() {
				let selected = [];
				$('input[name="ar_selected[]"]:checked').each(function () {
					selected.push($(this).val());
				});

				if (selected.length === 0) {
					alert("Pilih minimal 1 data!");
					return;
				}

				$("#btnSaveAR").prop("disabled", true).text("Processing...");

				$.ajax({
					url: "ajax/jo_crud.php",
					type: "POST",
					data: { type: "SaveUpAR", ids: selected },
					dataType: "json",
					success: function (res) {
						let msg = res.message ?? "Berhasil UP AR ke SAP";

						if (res.success === false) {
							alert("Gagal: " + msg);
						} else {
							alert("Sukses: " + msg);
							console.log(res);
							$('#DaftarUpAR').modal('hide');
						}
					},
					error: function (xhr, status, err) {
						let errMsg = xhr.responseText || err || "Unknown error";
						console.error(errMsg);
						alert("Terjadi error AJAX: " + errMsg);
					},
					complete: function () {
						$("#btnSaveAR").prop("disabled", false).text("Save to AR");
						ReadData();
					}
				});
			}
			$(document).on('click', '#btnSaveAR', function () {
				SaveUpAR();
			});

		// ============== UP KB TO SAP ==============
			function TampilUpKB(id_jo){
				$cari = $("#cari_UpKB").val('');
				ListUpKB(id_jo);
				$('#DaftarUpKB').modal('show');
			}
			function ListUpKB(id_jo) {
				var cari = $("#cari_UpKB").val();
				$.get("ajax/jo_crud.php", {cari:cari,id_jo:id_jo,  type:"ListUpKB" }, function (data, status) {
					$(".tampil_UpKB").html(data);
					$("#hal").val(hal);
				});
			}
			$(document).on('click', '#btnSaveKB', function () {
				CreateKB();
			});
			function CreateKB() {
				let selected = [];
				$('input[name="kb_selected[]"]:checked').each(function () {
					selected.push($(this).val());
				});

				if (selected.length === 0) {
					alert("Pilih minimal 1 data!");
					return;
				}

				$("#btnSaveKB").prop("disabled", true).text("Processing...");

				$.ajax({
					url: "ajax/jo_crud.php",
					type: "POST",
					data: { type: "CreateKB", ids: selected },
					dataType: "json",
					success: function (res) {
						if (res.success === false) {
							alert("Gagal: " + res.message);
						} else {
							alert("Kontrabon Berhasil di buat");
							console.log(res);
							$('#DaftarUpKB').modal('hide');
						}
					},
					error: function (xhr, status, err) {
						console.error(xhr.responseText);
						alert("Terjadi error: " + err);
					},
					complete: function () {
						$("#btnSaveKB").prop("disabled", false).text("Create Kontrabon");
						ReadData();
					}
				});
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
			var id_cust  = $("#id_cust").val();
			var no_do    = $("#no_dox").val();
			var id_po    = $("#is_trucking").val();
			var id_quo   = $("#id_quo").val();
			var tanggal  = $("#tanggalx").val();
			var id_asal  = $("#id_asalx").val();
			var id_tujuan= $("#id_tujuanx").val();
			var uj       = $("#ujx").val();
			var ritase   = $("#ritasex").val();
			var pph   	 = $("#pphx").val();

			if (tanggal === '') {
				alert("Tanggal harus diisi !..");	
				return;			
			}			
			else if (no_do === '' && id_po === '') {
				alert("No. PO harus diisi !..");
				return;
			}
			else if (id_cust === '') {
				alert("Customer harus diisi !..");		
				return;		
			}
			else if (id_asal === '') {
				alert("Asal harus diisi !..");
				return;				
			}
			else if (id_tujuan === '') {
				alert("Tujuan harus diisi !..");
				return;				
			}

			// lanjutkan proses simpan
			var id_cont  = $("#id_contx").val();
			
			var penerima = $("#penerimax").val();
			var jenis    = $("#jenisx").val();
			var biaya    = $("#biayax").val();
			var ket      = $("#ketx").val();
			var id_sap   = $("#id_sap").val();
			var sj_cust  = $("#sj_cust").val();

			$.post("ajax/jo_crud.php", {
				id_po:id_po,
				id_quo:id_quo,
				tanggal:tanggal,
				id_cust:id_cust,
				no_do:no_do,
				sap_project:id_sap,
				sj_cust:sj_cust,
				penerima:penerima,
				id_asal:id_asal,
				id_tujuan:id_tujuan,
				jenis:jenis,
				biaya:biaya,
				uj:uj,
				pph:pph,
				ritase:ritase,
				ket:ket,
				type : "Add_Order"
			}, function (data, status) {
				alert(data);
				$("#DataBaru").modal("hide");				
				ReadData(1);
			});
		}

		function CekRate() {
			var id_asal = $("#id_asalx").val();
			var id_tujuan = $("#id_tujuanx").val();
			var jenis_mobil = $("#jenisx").val();
			var id_cust = $("#id_cust").val();
			$("#biaya_kirim").val('0');	
			$.post("ajax/quo_crud.php", {
				id_cust:id_cust, id_asal: id_asal, id_tujuan:id_tujuan, jenis_mobil:jenis_mobil, type:"Cek_Rate_Cust"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					if(data.status == 200){
						CekRate_Umum();
					}
					else{
						$("#ujx").val(Rupiah(data.uj));
						$("#ritasex").val(Rupiah(data.ritase));
					}
				}
			);
		}
		function CekRate_Umum() {
			//alert('ddd');
			var id_asal = $("#id_asalx").val();
			var id_tujuan = $("#id_tujuanx").val();
			var jenis_mobil = $("#jenisx").val();
			$("#biaya_kirimx").val('');	
			$.post("ajax/quo_crud.php", {
				id_asal: id_asal, id_tujuan:id_tujuan, jenis_mobil:jenis_mobil, type:"Cek_Rate"
				},
				function (data, status) {
					var data = JSON.parse(data);
					$("#ujx").val(Rupiah(data.uj));
					$("#ritasex").val(Rupiah(data.ritase));
				}
			);
		}
		// function CekPTL(cb) {
		// 	$("#id_cust").val('');
		// 	$("#nama_cust").val('');
		// 	var checkBox = document.getElementById("cek_ptl");

		// 	if (checkBox.checked == true){
		// 		$("#jenis_po").val('1');
		// 		document.getElementById("po").style.display = 'inline';
		// 		document.getElementById('no_dox').readOnly=true;
		// 		document.getElementById("cust").style.display = 'none';
			
		// 	} else {				
		// 		$("#jenis_po").val('0');
		// 		document.getElementById("po").style.display = 'none';
		// 		document.getElementById('no_dox').readOnly=false;
		// 		document.getElementById("cust").style.display = 'inline';
		// 	}
		// }	
		function Download() {
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

		function checkUJ() {

			var type_price = $("#price_type").val();
			var id_asal = $("#id_asalx").val();
			var id_tujuan = $("#id_tujuanx").val();
			var jenis_mobil = $("#jenisx").val();

			$.post("ajax/jo_crud.php", {
				type_price:type_price, 
				id_asal: id_asal, 
				id_tujuan:id_tujuan, 
				jenis_mobil:jenis_mobil, 
				type:"checkUJ"
				},
				function (data, status) {
					var data = JSON.parse(data);	
					
					$("#ujx").val(Rupiah(data.uj)?? 0);
					$("#ritasex").val(Rupiah(data.ritase) ?? 0);
				}
			);
		}

		// ============== FITUR CLAIM ==============
			function ListClaim(id, stat) {
				$("#id_jo").val(id);
				if(stat == '3'){
					document.getElementById("btnClaim").disabled = true;
				} else{
					document.getElementById("btnClaim").disabled = false;
				}
				$("#DaftarClaim").modal("show");
			}
			function AddClaim() {
				var id_jo = $("#id_jo").val();
				var status = $("#status").val();
				var biaya = $("#biaya_claim").val();

				if (biaya.trim() === "") {
					alert("Biaya tidak boleh kosong!");
					return;
				}

				$.post("ajax/jo_crud.php", {
					id_jo: id_jo,
					status: status,
					biaya: biaya,
					type: "Add_Claim"
				}, function (data, respStatus) {
					ReadData(1);
					$("#DaftarClaim").modal("hide");
				});
			}

		// ============== FUNCTION ADD ATTACHMENT ==============
			function AddAttc(id_jo) {
				$('#id_jo_attc').val(id_jo);
				$('.view_so').attr('href', '#').text('-');
				$('.view_sj').attr('href', '#').text('-');
				$('.view_mutasi').attr('href', '#').text('-');

				$('#DataAttc').modal('show');

				$.ajax({
					url: 'ajax/get_attachment_by_idjo.php',
					method: 'POST',
					data: { id_jo: id_jo },
					dataType: 'json',
					success: function (res) {
						if (res.status === 200) {
							res.data.forEach(function(file) {
								let fileUrl = 'show_file.php?file=' + encodeURIComponent(file);

								if (file.includes('foto_so_')) {
									$('.view_so').attr('href', fileUrl).text(file);
								} else if (file.includes('surat_jalan_')) {
									$('.view_sj').attr('href', fileUrl).text(file);
								} else if (file.includes('mutasi_rekening_')) {
									$('.view_mutasi').attr('href', fileUrl).text(file);
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

		// ============== PRINT AR ==============
			function TampilPrint(no_ar){
				$("#no_ar").val(no_ar);
				btnPrint(no_ar)
			}
			function btnPrint(no_ar) {
				// alert(no_ar);
				// exit;

				$.ajax({
					url: "ajax/jo_crud.php",
					method: "POST",
					data: {
						no_ar: no_ar,
						type: "printAR"
					},
					success: function (data) {
						try {
							var res = JSON.parse(data);

							if (res.status === "success") {
								// window.open(res.url, "_blank");

								// fetch(res.url)
								// 	.then(response => response.blob())
								// 	.then(blob => {
								// 		var url = URL.createObjectURL(blob);
								// 		var a = document.createElement("a");
								// 		a.style.display = "none";
								// 		a.href = url;
								// 		a.download = "cetak_AR.pdf";
								// 		document.body.appendChild(a);
								// 		a.click();
								// 		URL.revokeObjectURL(url);
								// 		document.body.removeChild(a);
								// 	})
								// 	.catch(err => {
								// 		alert("Gagal ambil file cetak: " + err);
								// 	});

								// var printWindow = window.open(res.url, "_blank");
								// printWindow.onload = function () {
								// 	printWindow.focus();
								// 	printWindow.print();
								// };

								var printWindow = window.open(res.url, "_blank");
								var timer = setInterval(function() {
									if (printWindow.document.readyState === "complete") {
										clearInterval(timer);

										printWindow.focus();
										printWindow.print();

										// setTimeout(function() {
										// 	printWindow.close();
										// }, 5000);
									}
								}, 500);

								$("#no_ar").val('');
								$("#username").val('')
								$("#password").val('')
								ReadData();
							} else {
								alert(res.msg);
							}
						} catch (e) {
							alert("Response server tidak valid.");
							console.log(data);
						}
					},
					error: function (xhr, status, error) {
						alert("AJAX Error: " + error);
					}
				});
			}
			function ApproveAR(no_ar, btnElement){
				$(btnElement).prop("disabled", true);
				$.ajax({
					url: "ajax/jo_crud.php",
					method: "POST",
					data: {
						no_ar: no_ar,
						type: "sendApprovalAR"
					},
					success: function (data) {
						try {
							var res = JSON.parse(data);
							if (res.status === "success") {
								alert("Berhasil kirim approval ke atasan.");
							} else {
								alert(res.msg || "Gagal mengirim email.");
							}
						} catch (e) {
							alert("Response tidak valid dari server.");
						}

						// Enable tombol lagi
						$(btnElement).prop("disabled", false);
					},
					error: function (xhr, status, error) {
						alert("AJAX Error: " + error);
						// Enable kembali kalau error
						$(btnElement).prop("disabled", false);
					}
				});
			}

		// ============== ADD SAP ==============
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

		// ============== PRINT KB ==============
			function TampilPrintKB(no_kb){
				$("#no_kb").val(no_kb);
				btnPrintKB()
			}
			function btnPrintKB(no_kb) {
				$("#no_kb").val(no_kb);

				// alert(no_kb);
				// return;

				$.ajax({
					url: "ajax/jo_crud.php",
					method: "POST",
					data: {
						no_kb: no_kb,
						type: "printKB"
					},
					success: function (data) {
						try {
							var res = JSON.parse(data);

							if (res.status === "success") {
								// var printWindow = window.open(res.url, "_blank");
								// printWindow.onload = function () {
								// 	printWindow.focus();
								// 	printWindow.print();
								// };

								var printWindow = window.open(res.url, "_blank");
								var timer = setInterval(function() {
									if (printWindow.document.readyState === "complete") {
										clearInterval(timer);

										printWindow.focus();
										printWindow.print();

										// setTimeout(function() {
										// 	printWindow.close();
										// }, 2000);
									}
								}, 500);

								$("#no_ar").val('');
								$("#username").val('')
								$("#password").val('')
								ReadData();
							} else {
								alert(res.msg);
							}
						} catch (e) {
							alert("Response server tidak valid.");
							console.log(data);
						}
					},
					error: function (xhr, status, error) {
						alert("AJAX Error: " + error);
					}
				});
			}
			function ApproveKB(no_kb, btnElement){
				$(btnElement).prop("disabled", true);
				$.ajax({
					url: "ajax/jo_crud.php",
					method: "POST",
					data: {
						no_kb: no_kb,
						type: "sendApprovalKB"
					},
					success: function (data) {
						try {
							var res = JSON.parse(data);
							if (res.status === "success") {
								alert("Berhasil kirim approval ke atasan.");
							} else {
								alert(res.msg || "Gagal mengirim email.");
							}
						} catch (e) {
							alert("Response tidak valid dari server.");
						}

						$(btnElement).prop("disabled", false);
					},
					error: function (xhr, status, error) {
						alert("AJAX Error: " + error);
						$(btnElement).prop("disabled", false);
					}
				});
			}

		// ============== CANCEL SO ==============
			function Cancel(id_jo){
				$('#id_jo_cancel').val(id_jo);
				$('#ModalCancel').modal('show');
			}
			function CancelSO(){
				var id_jo 		 = $('#id_jo_cancel').val();
				var alasanCancel = $('#alasanCancel').val();

				$.post("ajax/jo_crud.php", {
					id_jo:id_jo,
					alasanCancel:alasanCancel,
					type : "CancelSO"
				}, function (data, status) {
					alert(data);
					$("#ModalCancel").modal("hide");				
					ReadData(1);
				});
			}

		// ============== APPROVE PENDING ==============
			function ApprovePending(no_jo, btnElement){
				$(btnElement).prop("disabled", true);
				$.ajax({
					url: "ajax/jo_crud.php",
					method: "POST",
					data: {
						no_jo: no_jo,
						type: "sendApprovalPending"
					},
					success: function (data) {
						try {
							var res = JSON.parse(data);
							if (res.status === "success") {
								alert("Berhasil kirim approval ke atasan.");
							} else {
								alert(res.msg || "Gagal mengirim email.");
							}
						} catch (e) {
							alert("Response tidak valid dari server.");
						}
						$(btnElement).prop("disabled", false);
					},
					error: function (xhr, status, error) {
						alert("AJAX Error: " + error);
						$(btnElement).prop("disabled", false);
					}
				});
			}

		// ============== MODAL SO ==============
			let selectedIdJO = null;
			function ModalSO(id_jo) {
				selectedIdJO = id_jo;
				$('#ModalPrintSO').modal('show');
			}

			function PrintSO(jenis) {
				if (!selectedIdJO) {
					alert("ID JO tidak ditemukan!");
					return;
				}

				const encodedId = btoa(selectedIdJO); 
				let file = '';
				switch (jenis) {
					case 'full':
						file = 'cetak_so.php';
						break;
					case 'trucking':
						file = 'cetak_so_tr.php';
						break;
					case 'other':
						file = 'cetak_so_oc.php';
						break;
					default:
						alert('Jenis tidak dikenal');
						return;
				}

				window.open(file + '?id=' + encodedId, '_blank');
				$('#ModalPrintSO').modal('hide');
			}

		// ============== MODAL AR ==============
			let selectedNoAR = null;
			function ModalAR(no_ar) {
				// alert(no_ar);
				// return;
				selectedNoAR = no_ar;
				$('#ModalPrintAR').modal('show');
			}
			function PrintAR(jenis) {
				if (!selectedNoAR) {
					alert("ID JO tidak ditemukan!");
					return;
				}

				const encodedAR = btoa(selectedNoAR); 
				let file = '';
				switch (jenis) {
					case 'full':
						file = 'cetak_ar.php';
						break;
					case 'trucking':
						file = 'cetak_ar_tr.php';
						break;
					case 'other':
						file = 'cetak_ar_oc.php';
						break;
					case 'nopph':
						file = 'cetak_ar_nt.php';
						break;
					default:
						alert('Jenis tidak dikenal');
						return;
				}

				$.ajax({
					url: "ajax/jo_crud.php",
					method: "POST",
					data: {
						no_ar: selectedNoAR,
						jenis: jenis,
						file: file,
						type: "printAR"
					},
					success: function (data) {
						try {
							var res = JSON.parse(data);

							if (res.status === "success") {

								var printWindow = window.open(res.url, "_blank");
								var timer = setInterval(function() {
									if (printWindow.document.readyState === "complete") {
										clearInterval(timer);

										printWindow.focus();
										printWindow.print();

									}
								});

								// $("#no_ar").val('');
								// $("#username").val('')
								// $("#password").val('')
								$('#ModalPrintAR').modal('hide');
								ReadData();
							} else {
								alert(res.msg);
							}
						} catch (e) {
							alert("Response server tidak valid.");
							console.log(data);
						}
					},
					error: function (xhr, status, error) {
						alert("AJAX Error: " + error);
					}
				});
				
			}


		// SEND SO AR OTHER 
		
			function TampilOther(id_biaya) {
				
			}
			function SOother(id_biaya) {
				const link = $('a[onclick="SOother(\'' + id_biaya + '\')"]');

				link.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
				link.css({ "pointer-events": "none", "opacity": "0.6" });

				$.ajax({
					url: "ajax/jo_crud.php",
					type: "POST",
					data: { type: "SOother", ids: id_biaya },
					dataType: "json",
					success: function (res) {
						if (res.success === false) {
							alert("Gagal: " + res.message);
							link.html("Send SO");
							link.css({ "pointer-events": "auto", "opacity": "1" });
						} else {
							alert("Data berhasil dikirim ke SAP!");
							link.replaceWith('<span>SO ' + (res.so_sap ?? 'Terkirim') + '</span>');
						}
					},
					error: function (xhr, status, err) {
						console.error(xhr.responseText);
						alert("Terjadi error: " + err);
						link.html("Send SO");
						link.css({ "pointer-events": "auto", "opacity": "1" });
					},
					complete: function () {
						$('#DaftarBiayaLain').modal('hide');
						ReadData();
					}
				});
			}
			function ARother(id_biaya) {
				const link = $('a[onclick="ARother(\'' + id_biaya + '\')"]');

				link.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
				link.css({ "pointer-events": "none", "opacity": "0.6" });

				$.ajax({
					url: "ajax/jo_crud.php",
					type: "POST",
					data: { type: "ARother", ids: id_biaya },
					dataType: "json",
					success: function (res) {
						if (res.success === false) {
							alert("Gagal: " + res.message);
							link.html("Send SO");
							link.css({ "pointer-events": "auto", "opacity": "1" });
						} else {
							alert("Data berhasil dikirim ke SAP!");
							link.replaceWith('<span>AR ' + (res.so_sap ?? 'Terkirim') + '</span>');
						}
					},
					error: function (xhr, status, err) {
						console.error(xhr.responseText);
						alert("Terjadi error: " + err);
						link.html("Send AR");
						link.css({ "pointer-events": "auto", "opacity": "1" });
					},
					complete: function () {
						$('#DaftarBiayaLain').modal('hide');
						ReadData();
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
								<option>No SO SAP</option>
								<option>No AR SAP</option>
								<option>Customer</option>
								<option>Origin</option>
								<option>Destination</option>
								<option>No Cont</option>
								<option>No Police</option>
								<option>Driver</option>
								<option value="<?php echo $field; ?>" selected hidden><?php echo $field; ?></option>
							</select>
							<input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
							style="text-align: left;width:200px" onkeypress="ReadData(1)" >
						</div>
						<div style="width:100%" class="input-group">
							<span class="input-group-addon" style="text-align:right;"></span>
							<select size="1" id="field1"  onchange="ReadData(1)" name="field1" style="padding:4px;margin-right:2px;width: 85px">
								<option>No Order</option>
								<option>No Quo</option>
								<option>No SO SAP</option>
								<option>No AR SAP</option>
								<option>Customer</option>
								<option>Origin</option>
								<option>Destination</option>
								<option>No Cont</option>
								<option>No Police</option>
								<option>Driver</option>
								<option value="<?php echo $field1; ?>" selected hidden><?php echo $field1; ?></option>
							</select>
							<input type="text"  id ="search_name1" name="search_name1" value="<?php echo $search_name1; ?>" 

							style="text-align: left;width:200px" onkeypress="ReadData(1)" >
							<input type="hidden"  id ="hal" name="hal" value="<?php echo $hal; ?>" style="text-align: left;width:5%">

							<input type="hidden"  id ="jenis_role" name="jenis_role" value="<?php echo $id_role; ?>" style="text-align: left;width:5%"  >
							
							<button class="btn btn-block btn-primary" style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:2px;padding-top:6px;padding-bottom:6px"type="submit">
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
	
	<!-- ============== MODAL NEW SO ============== -->
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

								<input type="hidden" id="id_po" placeholder="id_po">  
								<input type="hidden" id="id_sap" placeholder="id_sap">	
								<input type="hidden" id="id_quo" placeholder="id_quo">  
								<input type="hidden" id="id_cust" placeholder="id_cust"/>

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Order :</b></span>
									<input type="text"  id ="no_sjx" style="text-align: center;width:22%" readonly  >
									<input type="hidden" id="id_sjx" value=""/>
									&nbsp;
									<input type="hidden" id="cek_ptl" value="1"  onclick='CekPTL(this);' ></b>
								</div>	
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Project Code :</b></span>
									<input type="text"  id ="project_code" style="text-align: center;width:22%" readonly placeholder="-- Auto --">
								</div>	
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Date :</b></span>
									<div style="display:flex; align-items:center; gap:20px;">
										<input type="text"  id ="tanggalx" style="text-align: center;width:22%" readonly>
										<div>
											<input type="radio" id="is_vendor" name="tipe_po" value="vendor">
											<label for="is_vendor">Vendor</label>
										</div>
										<div>
											<input type="radio" id="is_ptl" name="tipe_po" value="ptl">
											<label for="is_ptl">PTL</label>
										</div>
									</div>
								</div>

								<div style="width:100%; display:none;" class="input-group" id="ptl_row">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px">
										<b>No. PO PTL :</b>
									</span>
									<input type="text" id="no_dox" value="" style="text-transform: uppercase;text-align:left;width:58.9%;" readonly>  
									<button class="btn btn-primary" id="po"
										style="padding:6px 12px; border-radius:2px; margin-left:2px" type="button"
										onClick="javascript:TampilPO()">
										<span class="glyphicon glyphicon-search"></span>
									</button>  
								</div>

								<div style="width:100%; display:none;" class="input-group" id="trucking_row">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px">
										<b>No. PO TR :</b>
									</span>
									
									<input type="text" id="is_trucking" value="" style="text-transform: uppercase;text-align: left; width:58.9%;" readonly>  
									<button class="btn btn-primary" id="po"
										style="padding:4px 12px; border-radius:2px; margin-left:2px" type="button"
										onClick="javascript:TampilPOTR()">
										<span class="glyphicon glyphicon-search"></span>
									</button>  
								</div>

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Project :</b></span>
									<input type="text" id="sap_project" style="text-transform: uppercase;text-align: left;width:80%;" readonly>	
									
									<button class="btn btn-block btn-primary" id="po"
										style="padding:6px 12px;margin-top:-3px;border-radius:2px;margin-left:2px" type="button" 
										onClick="javascript:TampilSAP()">
										<span class="glyphicon glyphicon-search"></span>
									</button>
								</div>

								<div style="width:100%;" class="input-group" id="trucking_row">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px">
										<b>No. SQ :</b>
									</span>
									<input type="text" id="no_sq" value="" style="text-transform: uppercase;text-align: left; width:80%;" readonly>  
									<button class="btn btn-primary" id="po"
										style="padding:4px 12px; border-radius:2px; margin-left:2px" type="button"
										onClick="javascript:TampilSQ()">
										<span class="glyphicon glyphicon-search"></span>
									</button>  
								</div>
								
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>DO/SJ Cust :</b></span>
									<input type="text"  id ="sj_cust" style="text-align: left;width:80%">
								</div>

								<!-- <div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Container :</b></span>
									<input type="text" id ="cont_add" style="text-align: left;width:80%">	
								</div> -->
								
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Customer :</b></span>
									<input type="text"  id ="nama_cust" style="text-align: left;width:80%" readonly  >
									<button class="btn btn-block btn-primary" id="cust"
										style="padding:6px;margin-top:-3px;border-radius:2px;margin-left:-1px" type="button" 
										onClick="javascript:TampilCust()">
										<span class="glyphicon glyphicon-search"></span>
									</button>	
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Receiver :</b></span>
									<textarea id="penerimax"style="resize:none;width: 80%; height: 70px; font-size: 11px; line-height: 12px; border: 1px solid #444; padding: 5px;"></textarea>	
								</div>

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Origin :</b></span>
									<select id="id_asalx" onchange="CekRate()" <?php echo $dis;?> style="width: 80%;padding:4px">
										<?php
										$t1="SELECT * from m_kota_tr where `status` = '1' order by nama_kota";
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
										$t1="SELECT * from m_kota_tr where `status` = '1' order by nama_kota";
										$h1=mysqli_query($koneksi, $t1);       
										while ($d1=mysqli_fetch_array($h1)){?>
										<option value="<?php echo $d1['id_kota'];?>" ><?php echo $d1['nama_kota'];?></option>
										<?php }?>
									</select>
								</div>

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Type :</b></span>
									<select id="jenisx" name="jenisx" onchange="checkUJ()" <?php echo $dis;?> style="width: 80%;padding:4px">
										<?php
										$t1="SELECT * from m_jenis_mobil_tr where status = '1' order by nama   ";
										$h1=mysqli_query($koneksi, $t1);       
										while ($d1=mysqli_fetch_array($h1)){?>
										<option value="<?php echo $d1['nama'];?>" ><?php echo $d1['nama'];?></option>
										<?php }?>
									</select>	
								</div>	

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Price Type :</b></span>
									<select id="price_type" name="price_type" onchange="checkUJ()" style="width: 80%;padding:4px">
										<option value="high" >HIGH</option>
										<option value="middle" >MIDDLE</option>
										<option value="low" >LOW</option>
									</select>	
								</div>
								
								<div  id="tampil_uj" style="display:none;">
									<div style="width:100%;" class="input-group">
										<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Deliv. Cost :</b></span>								
										<input type="text" id="biayax" style="text-align: right;width:22%;" 
										onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly >	
									</div>
									<div style="width:100%;" class="input-group">
										<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Travel Expense :</b></span>								
										<input type="text" id="ujx" value="0" style="text-align: right;width:22%;" 
										onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly>	
									</div>
									<div style="width:100%;" class="input-group">
										<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Ritase :</b></span>								
										<input type="text" id="ritasex" value="0" style="text-align: right;width:22%;" 
										onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly>	
									</div>
								</div>

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>PPH :</b></span>								
									<input type="text" id="pphx" style="text-align: right;width:22%;" readonly>	
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

	<!-- ============== MODAL SEARCH POTR ============== -->
		<div class="modal fade" id="DaftarPOTR"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
									&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data POTR</b>
								</div>	
								<br>
								<div style="width:100%" class="input-group" style="background:none !important;">
									<span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Search :</b>&nbsp;&nbsp;
										<input type="text" id ="cari_POTR" style="text-align: left;width:200px">

										<button class="btn btn-block btn-primary" style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:2px;padding:5px" onClick="javascript:ListPOTR()">
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
									<div class="tampil_POTR"></div>
								</div>
								<br>
							</div>		
						</div>		
					</div>	
				</div>
			</div>	
		</div>

	<!-- ============== MODAL SEARCH SQ ============== -->
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
	
	<!-- ============== MODAL PO PTL ============== -->
	<div class="modal fade" id="DaftarPO" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">	
				<div class="modal-body">						
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data PO PTL</b>
								<button class="btn btn-block btn-danger" 
									style="margin:0px;margin-left:-2px;margin-bottom:3px;border-radius:2px;padding:5px"  
									data-dismiss="modal">
									<span class="glyphicon glyphicon-remove"></span>
								</button>
							</div>	
							<br>
							<div style="width:100%" class="input-group" style="background:none !important;">
								<span class="input-group-addon" style="width:100%;text-align:left;padding:0px">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>No Cont :</b>&nbsp;&nbsp;
									<input type="text" id="cari_cont"  
										style="text-align: left;width:200px">

									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Vendor :</b>&nbsp;&nbsp;
									<input type="text" id="cari_vendor"  
										style="text-align: left;width:200px">
								</span>
							</div>							
							<div class="table-responsive mailbox-messages">									
								<div class="tampil_po"></div>
							</div>
							<br>
						</div>		
					</div>		
				</div>	
			</div>
		</div>	
	</div>


	<!-- ============== MODAL SEARCH SAP PROJECT ============== -->
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

	<!-- ============== MODAL SEARCH SO UP TO SAP ============== -->
		<div class="modal fade" id="DaftarUpSAP"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="display:flex;align-items:center;justify-content:space-between; font-size:12px;font-family:'Arial';color:#fff;margin:0;background-color:#4783b7;padding:5px;margin-bottom:1px">
									<div style="text-align:left;">
										<b><i class="fa fa-list"></i>&nbsp;Data Up to SAP</b>
									</div>
									<button class="btn btn-danger btn-sm" style="border-radius:2px;padding:3px 6px;" data-dismiss="modal">
										<span class="glyphicon glyphicon-remove"></span>
									</button>
								</div>
								<br>
								<div style="width:100%" class="input-group" style="background:none !important;">
									<span class="input-group-addon" style="width:80%;text-align:right;padding:0px">									
									</span>
								</div>							
								<div class="table-responsive mailbox-messages">									
									<form id="formUpSAP">
										<div class="tampil_UpSAP"></div>
									</form>
								</div>

								<br>
								<div style="text-align:right;">
									<button type="button" id="btnSaveSAP" class="btn btn-success" style="margin:0;border-radius:2px;">
										<span class="fa fa-plus-square"></span>
										<b>Save to SAP</b>
									</button>	
								</div>

							</div>		
						</div>		
					</div>	
				</div>
			</div>	
		</div>

	<!-- ============== MODAL SEARCH AR UP TO AR ============== -->
		<div class="modal fade" id="DaftarUpAR"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="display:flex;align-items:center;justify-content:space-between; font-size:12px;font-family:'Arial';color:#fff;margin:0;background-color:#4783b7;padding:5px;margin-bottom:1px">
									<div style="text-align:left;">
										<b><i class="fa fa-list"></i>&nbsp;Data Up to AR</b>
									</div>
									<button class="btn btn-danger btn-sm" style="border-radius:2px;padding:3px 6px;" data-dismiss="modal">
										<span class="glyphicon glyphicon-remove"></span>
									</button>
								</div>
								<br>
								<div style="width:100%" class="input-group" style="background:none !important;">
									<span class="input-group-addon" style="width:80%;text-align:right;padding:0px">									
									</span>
								</div>							
								<div class="table-responsive mailbox-messages">									
									<form id="formUpAR">
										<div class="tampil_UpAR"></div>
									</form>
								</div>

								<br>
								<div style="text-align:right;">
									<button type="button" id="btnSaveAR" class="btn btn-success" style="margin:0;border-radius:2px;">
										<span class="fa fa-plus-square"></span>
										<b>Save to AR</b>
									</button>	
								</div>

							</div>		
						</div>		
					</div>	
				</div>
			</div>	
		</div>

	<!-- ============== MODAL SEARCH AR UP TO AR ============== -->
		<div class="modal fade" id="DaftarUpKB"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="display:flex;align-items:center;justify-content:space-between; font-size:12px;font-family:'Arial';color:#fff;margin:0;background-color:#4783b7;padding:5px;margin-bottom:1px">
									<div style="text-align:left;">
										<b><i class="fa fa-list"></i>&nbsp;Data Kontrabon</b>
									</div>
									<button class="btn btn-danger btn-sm" style="border-radius:2px;padding:3px 6px;" data-dismiss="modal">
										<span class="glyphicon glyphicon-remove"></span>
									</button>
								</div>
								<br>
								<div style="width:100%" class="input-group" style="background:none !important;">
									<span class="input-group-addon" style="width:80%;text-align:right;padding:0px">									
									</span>
								</div>							
								<div class="table-responsive mailbox-messages">									
									<form id="formUpAR">
										<div class="tampil_UpKB"></div>
									</form>
								</div>

								<br>
								<div style="text-align:right;">
									<button type="button" id="btnSaveKB" class="btn btn-success" style="margin:0;border-radius:2px;">
										<span class="fa fa-plus-square"></span>
										<b>Create Kontrabon</b>
									</button>	
								</div>

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
	
	<!-- ============== EDIT JO ============== -->
		<div class="modal fade" id="ModelEdit"  role="dialog" aria-labelledby="myModalLabel">
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
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Project Code :</b></span>
									<input type="text"  id ="project_codex" style="text-align: center;width:22%" readonly>
								</div>	
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Date :</b></span>
									<input type="text"  id ="tanggal" style="text-align: center;width:22%" readonly  >
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>NO PO :</b></span>
									<input type="text"  id ="no_po" style="text-align: left;width:80%" readonly>
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>SAP Project :</b></span>
									<input type="text"  id ="sap_projectx" style="text-align: left;width:80%" readonly>
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. SQ :</b></span>
									<input type="text" id ="no_sqx" style="text-align: left;width:80%" readonly>
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>DO/SJ Cust :</b></span>
									<input type="text" id ="sj_custx" style="text-align: left;width:80%">
								</div>
								
								<!-- <div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Container :</b></span>
									<input type="text" id ="cont_edit" style="text-align: left;width:80%">	
								</div> -->

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Receiver :</b></span>
									<textarea id="penerima" style="resize:none;width: 80%; height: 70px; font-size: 11px; line-height: 12px; border: 1px solid #444; padding: 5px;"  ></textarea>	
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

								<div  id="tampil_ujx" style="display:none;">
									<div style="width:100%;" class="input-group">
										<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Deliv. Cost :</b></span>								
										<input type="text" id="biaya" value="0" style="text-align: right;width:22%;" 
										onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly >	
									</div>
									<div style="width:100%;" class="input-group">
										<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Travel Expense:</b></span>								
										<input type="text" id="uj" value="0" style="text-align: right;width:22%;" 
										onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly>	
									</div>
									<div style="width:100%;" class="input-group">
										<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Ritase :</b></span>								
										<input type="text" id="ritase" value="0" style="text-align: right;width:22%;" 
										onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" readonly>	
									</div>
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>PPH :</b></span>								
									<input type="text" id="pph" style="text-align: right;width:22%;" readonly>	
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>stapel :</b></span>								
									<input type="text" id="stapel" style="text-align: right;width:22%;" >	
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
	
	<!-- ============== AR OTHER COST ============== -->
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
								<div class="table-responsive mailbox-messages">				
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
										$t1="SELECT * FROM m_cost_tr WHERE `status` = '1' AND id_cost <> '1' AND itemcode IS NOT NULL AND sap_ips LIKE '%S%' ORDER BY nama_cost  ";
										$h1	= mysqli_query($koneksi, $t1);       
										while ($d1=mysqli_fetch_array($h1)){?>
											<option value="<?php echo $d1['id_cost'];?>" ><?php echo $d1['nama_cost'];?></option>
										<?php }?>
									</select>	
									<input type="hidden" id="id_biaya_lain"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />	
									<input type="hidden" id="mode_biaya_lain"   value="" style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />
								</div>	

								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Remark :</b></span>
									<textarea id="remark_cost" style="resize:none;width: 80%; height: 70px; font-size: 11px; line-height: 12px; border: 1px solid #444; padding: 5px;"></textarea>
								</div>
								
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;min-width:150px"><b>PPN :</b></span>
									<input type="text" id="pph" style="text-align: right;width:20%;" 
									onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)" value="0">
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;min-width:150px"><b>WTAX :</b></span>
									<input type="text" id="wtax" style="text-align: right;width:20%;" 
									onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  value="0">
								</div>
								<div style="width:100%;" class="input-group">
									<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Cost :</b></span>
									<input type="text" id="biaya_lain" style="text-align: right;width:20%;" 
									onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  value="0">
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
							<div class="table-responsive mailbox-messages">									
								<div class="tampil_uj"></div>
							</div>
							
						</div>
					</div>		
				</div>	
			</div>
		</div>	
    </div>

	<div class="modal fade" id="DaftarClaim"  role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="background: none">
				<div class="modal-body">
					<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
						<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
							<div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
								&nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Other AP Data</b>
							</div>	
							<br>

							<input type="hidden" id="id_jo">
							<input type="hidden" id="status">

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;min-width:150px"><b>Biaya :</b></span>
								<input type="text" id="biaya_claim" style="text-align: right;width:50%;" 
								onBlur ="this.value=Rupiah(this.value);" onkeypress="return isNumber(event)"  >
							</div>

							<div style="width:100%;" class="input-group">
								<span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>

								<button type="button" id="btnClaim" class="btn btn-success" onclick="AddClaim()">
									<span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save</b>&nbsp;&nbsp;
								</button>


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
									$t1="SELECT * from m_cost_tr where status = '1' and id_cost <> '1' order by nama_cost  ";
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

	<!-- ============== MODAL ATTACHMENT ============== -->
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
										<p><b>Lihat File SO: </b><a href="#" class="view_so" target="_blank"></a></p>
									</div>
									<!-- <div class="form-group mt-3">
										<label for=""><b>File SJ:</b></label>
										<input type="file" class="form-control" id="file_sj" name="file_sj">
										<p><b>Lihat File SJ : </b><a href="#" class="view_sj" target="_blank"></a></p>
									</div> -->
									<div class="form-group mt-3">
										<label for=""><b>File Mutasi:</b></label>
										<input type="file" class="form-control" id="file_mutasi" name="file_mutasi">
										<p><b>Lihat File Mutasi : </b><a href="#" class="view_mutasi" target="_blank"></a></p>
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

	<!-- ============== MODAL CANCEL SO ============== -->
		<div class="modal fade" id="ModalCancel" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<div class="col-md-12" style="padding: 0;">
							<div class="box box-success box-solid" style="padding: 5px; border: 1px solid #ccc;">
								<div class="small-box bg" style="font-size:12px;font-family:'Arial';color:#fff;margin:0;background-color:#4783b7;padding:5px;">
									<b><i class="fa fa-list"></i>&nbsp;Cancel SO</b>
								</div>

								<input type="hidden" id="id_jo_cancel" name="id_jo_cancel">

								<div class="form-group" style="margin-top: 1rem;">
									<label for="alasanCancel"><b>Alasan Cancel SO</b></label>
									<textarea id="alasanCancel" name="alasanCancel" class="form-control" rows="3" placeholder="Tuliskan alasan pembatalan..."></textarea>
								</div>

				
								<div class="form-group mt-3 text-right">
									<button type="button" class="btn btn-success" onclick="CancelSO()">
										<span class="fa fa-save"></span>&nbsp;<b>Save</b>
									</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal">
										<span class="fa fa-close"></span>&nbsp;<b>Cancel</b>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<!-- ============== MODAL PRINT SO ============== -->
		<div class="modal fade" id="ModalPrintSO" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<div class="col-md-12" style="padding: 0;">
							<div class="box box-success box-solid" style="padding: 5px; border: 1px solid #ccc;">
								<div class="small-box bg" style="font-size:12px;font-family:'Arial';color:#fff;margin:0;background-color:#4783b7;padding:5px;">
									<b><i class="fa fa-list"></i>&nbsp;PRINT SO</b>
								</div>

								<!-- <input type="hidden" id="id_jo_cancel" name="id_jo_cancel"> -->

								<div class="form-group" style="margin-top: 1rem;">
									<button type="button" class="btn btn-primary" onclick="PrintSO('full')">
										<span class="fa fa-print"></span>&nbsp;<b>Print SO Full</b>
									</button>
									<button type="button" class="btn btn-success" onclick="PrintSO('trucking')">
										<span class="fa fa-print"></span>&nbsp;<b>Print SO Trucking</b>
									</button>
									<button type="button" class="btn btn-warning" onclick="PrintSO('other')">
										<span class="fa fa-print"></span>&nbsp;<b>Print SO Other Cost</b>
									</button>
								</div>


				
								<div class="form-group mt-3 text-right">
									<button type="button" class="btn btn-danger" data-dismiss="modal">
										<span class="fa fa-close"></span>&nbsp;<b>Close</b>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<!-- ============== MODAL PRINT AR ============== -->
		<div class="modal fade" id="ModalPrintAR" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<div class="col-md-12" style="padding: 0;">
							<div class="box box-success box-solid" style="padding: 5px; border: 1px solid #ccc;">
								<div class="small-box bg" style="font-size:12px;font-family:'Arial';color:#fff;margin:0;background-color:#4783b7;padding:5px;">
									<b><i class="fa fa-list"></i>&nbsp;PRINT AR</b>
								</div>

								<div class="form-group" style="margin-top: 1rem;">
									<button type="button" class="btn btn-primary" onclick="PrintAR('full')">
										<span class="fa fa-print"></span>&nbsp;<b>Print AR Full</b>
									</button>
									<button type="button" class="btn btn-info" onclick="PrintAR('nopph')">
										<span class="fa fa-print"></span>&nbsp;<b>Print AR No PPH</b>
									</button>
									<button type="button" class="btn btn-success" onclick="PrintAR('trucking')">
										<span class="fa fa-print"></span>&nbsp;<b>Print AR Trucking</b>
									</button>
									<button type="button" class="btn btn-warning" onclick="PrintAR('other')">
										<span class="fa fa-print"></span>&nbsp;<b>Print AR Other Cost</b>
									</button>
									
								</div>


				
								<div class="form-group mt-3 text-right">
									<button type="button" class="btn btn-danger" data-dismiss="modal">
										<span class="fa fa-close"></span>&nbsp;<b>Close</b>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const vendorCb   = document.getElementById("is_vendor");
			const ptlCb      = document.getElementById("is_ptl");
			const vendorRow  = document.getElementById("trucking_row");
			const ptlRow     = document.getElementById("ptl_row");

			function toggleRows() {
				if (vendorCb.checked) {
					vendorRow.style.display = "flex";
					ptlRow.style.display = "none";
					ptlCb.checked = false;
				} else if (ptlCb.checked) {
					ptlRow.style.display = "flex";
					vendorRow.style.display = "none";
					vendorCb.checked = false;
				} else {
					vendorRow.style.display = "none";
					ptlRow.style.display = "none";
				}
			}

			vendorCb.addEventListener("change", toggleRows);
			ptlCb.addEventListener("change", toggleRows);

			toggleRows();
		});
	</script>

  </body>
</html>
