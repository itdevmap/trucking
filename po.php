<?php
    session_start();
    include "koneksi.php"; 
    include "session_log.php";
    $pq = mysqli_query($koneksi,"SELECT * FROM m_role_akses_tr WHERE id_role = '$id_role' AND id_menu ='66'");

    $rq=mysqli_fetch_array($pq);	
    $m_edit = $rq['m_edit'];
    $m_add = $rq['m_add'];
    $m_del = $rq['m_del'];
    $m_view = $rq['m_view'];
    $m_exe = $rq['m_exe'];

    if(!isset($_SESSION['id_user'])  ||  $m_view != '1'  ) {
        header('location:logout.php'); 
    }

    if($_SERVER['REQUEST_METHOD'] == "POST") {	
        $hal = $_POST['hal'];
        $search_name = $_POST['search_name'];
        $paging = $_POST['paging'];
    }
    else {	
        $paging='15';
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
        // ============ READ DATA ============
            $(document).ready(function () {
                var hal = $("#hal").val();
                ReadData(hal);
            });
            function ReadData(hal) {
                var cari = $("#search_name").val();
                var paging = $("#paging").val();	
                $.get("ajax/po_crud.php", {paging:paging,cari:cari,hal:hal, type:"Read" }, function (data, status) {
                    $(".tampil_data").html(data);
                    $("#hal").val(hal);
                });
            }

        // ============ SHOW MODAL ============
            function TampilData() 
            {
                $("#mode").val('Add');
                $('#Data').modal('show');
            }

        // ============ FORMAT RUPIAH ============
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

        // ============ EDIT DATA ============
            function GetData(id) {
                $("#id").val(id);	
                $.post("ajax/po_crud.php", {
                        id: id,
                        type: "Detil_Data"
                    },
                    function (data, status) {
                        try {
                            var res = JSON.parse(data);

                            $("#vendor").val(res.vendor);
                            $("#rute").val(res.rute);
                            $("#cost").val(res.cost);

                            $("#mode").val("Edit");
                        } catch (e) {
                            console.error("JSON Parse Error:", e, data);
                            alert("Data tidak valid dari server!");
                        }
                    }
                );
                $("#Data").modal("show");
            }

        // ============ STORE/UPDATE DATA ============
            function add() {
                var r = confirm("Are you sure ?...");
                if (r == true) {	
                    var id = $("#id").val();
                    var mode = $("#mode").val();
                    var hal = $("#hal").val();

                    var vendor = $("#vendor").val();
                    var rute = $("#rute").val();
                    var cost = $("#cost").val();

                    $.post("ajax/po_crud.php", {
                        id:id,
                        mode:mode,
                        
                        vendor:vendor,
                        rute:rute,
                        cost:cost,
                        
                        type : "Add_Data"
                        }, function (data, status) {
                        alert(data);
                        $("#Data").modal("hide");				
                        ReadData(hal);
                    });
                }
            }
        // ============ AJAX UP PO TO SAP ============
            function TampilUpSAP(id_po){
				$cari = $("#cari_UpSAP").val('');
				ListUpSAP(id_po);
				$('#DaftarUpSAP').modal('show');
			}
			function ListUpSAP(id_po) {
				var cari = $("#cari_UpSAP").val();
				$.get("ajax/po_crud.php", {cari:cari,id_po:id_po,  type:"ListUpSAP" }, function (data, status) {
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
					url: "ajax/po_crud.php",
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

        // ============ AJAX UP AP TO SAP ============
            function TampilUpAP(id_po){
				$cari = $("#cari_AP").val('');
				ListAP(id_po);
				$('#DaftarAP').modal('show');
			}
            function ListAP(id_po) {
				var cari = $("#cari_AP").val();
				$.get("ajax/po_crud.php", {cari:cari,id_po:id_po,  type:"ListAP" }, function (data, status) {
					$(".tampil_AP").html(data);
					$("#hal").val(hal);
				});
			}
            function SaveAP() {
				let selected = [];
				$('input[name="ap_selected[]"]:checked').each(function () {
					selected.push($(this).val());
				});

				if (selected.length === 0) {
					alert("Pilih minimal 1 data!");
					return;
				}

				$("#btnSaveAP").prop("disabled", true).text("Processing...");

				$.ajax({
					url: "ajax/po_crud.php",
					type: "POST",
					data: { type: "SaveAP", ids: selected },
					dataType: "json",
					success: function (res) {
						if (res.success === false) {
							alert("Gagal: " + res.message);
						} else {
							alert("Data berhasil dikirim ke SAP!");
							console.log(res);
							$('#DaftarAP').modal('hide');
						}
					},
					error: function (xhr, status, err) {
						console.error(xhr.responseText);
						alert("Terjadi error: " + err);
					},
					complete: function () {
						$("#btnSaveAP").prop("disabled", false).text("Create AP");
						ReadData();
					}
				});
			}
            $(document).on('click', '#btnSaveAP', function () {
				SaveAP();
			});
        // ============ EXECUTE DATA ============
            function Confirm(id) {
                const hal = $("#hal").val();
                const conf = confirm("Are you sure to Close ?");
                
                if (!conf) return;

                $.post("ajax/po_crud.php", {
                    id: id,
                    type: "Executed"
                }, function (response) {
                    // Coba parse JSON
                    let res;
                    try {
                        res = typeof response === "object" ? response : JSON.parse(response);
                    } catch (e) {
                        alert("Response tidak valid: " + response);
                        return;
                    }

                    if (res.success) {
                        alert(res.message || "Berhasil");
                        ReadData(hal);
                    } else {
                        alert("Gagal: " + (res.message || "Terjadi kesalahan"));
                    }

                }).fail(function (xhr, status, error) {
                    alert("Request gagal: " + error);
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

		<!-- ============ CONTENT ============ -->
		<form method="post" name ="myform" action="po.php" class="form-horizontal" > 
            <div class="content-wrapper" style="min-height:750px">
                <br>
                <ol class="breadcrumb">
                    <li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data PO</b></font></h1></li>					
                </ol>
                <br>
                <div class="col-md-12" >
                    <div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
                        <div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
                                <b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
                        </div>
                        <br>					
                        <div style="width:100%" class="input-group">
                            <span class="input-group-addon" style="text-align:right;"><b>Find Code PO :</b></span>
                            <input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" 
                            style="text-align: left;width:200px" onkeypress="ReadData(1)" >
                            <input type="hidden"  id ="hal" name="hal" value="<?php echo $hal; ?>" style="text-align: left;width:5%"  >
                            
                            <button class="btn btn-block btn-primary" style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:2px;padding-top:6px;padding-bottom:6px" type="submit" >
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
                                    style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:2px" type="button" title = "Created Order"
                                    onClick="window.location.href = 'po_data.php?id=<?php echo $xy1; ?>' ">
                                    <span class="fa  fa-plus-square"></span>
                                    <b>Create PO</b>
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

    <!-- ============ MODAL PO TO SAP ============ -->
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

    <!-- ============ MODAL AP TO SAP ============ -->
        <div class="modal fade" id="DaftarAP"  role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="background: none">	
					<div class="modal-body">						
						<div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
							<div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
								<div class="small-box bg" style="display:flex;align-items:center;justify-content:space-between; font-size:12px;font-family:'Arial';color:#fff;margin:0;background-color:#4783b7;padding:5px;margin-bottom:1px">
									<div style="text-align:left;">
										<b><i class="fa fa-list"></i>&nbsp;Data AP to SAP</b>
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
										<div class="tampil_AP"></div>
									</form>
								</div>

								<br>
								<div style="text-align:right;">
									<button type="button" id="btnSaveAP" class="btn btn-success" style="margin:0;border-radius:2px;">
										<span class="fa fa-plus-square"></span>
										<b>Create AP</b>
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
	
  </body>
</html>
