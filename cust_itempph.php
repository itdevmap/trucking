<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
include "lib.php";

$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='8' ");
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
	$field = $_POST['field'];
	$search_name = $_POST['search_name'];
	$paging = $_POST['paging'];
}
else
{	
	$paging='25';
	$hal='1';
}

$customer_name = "";
if (isset($_GET['id']) && $_GET['id'] != "") {
    $id_cust = $_GET['id'];
    $res = mysqli_query($koneksi, "SELECT nama_cust FROM m_cust_tr WHERE id_cust = '$id_cust' LIMIT 1");
    if ($row = mysqli_fetch_assoc($res)) {
        $customer_name = $row['nama_cust'];
    }
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
            // ----------------- SHOW MODAL -----------------
                $(document).ready(function () {
                    $("#tanggal").datepicker({
                        format:'dd-mm-yyyy',
                        todayHighlight: true,
                        autoclose: true,
                    });

                    var hal = $("#hal").val();
                    var id  = getUrlParam("id");
                    console.log("ID dari URL:", id);

                    ReadData(hal, id);
                });
                function getUrlParam(name) {
                    let url = new URL(window.location.href);
                    return url.searchParams.get(name);
                }
                function ReadData(hal, id) {
                    var search_name = $("#search_name").val();
                    var field = $("#field").val();
                    var paging = $("#paging").val();

                    $.get("ajax/itempph_crud.php", {
                        field: field,
                        paging: paging,
                        search_name: search_name,
                        hal: hal,
                        id: id,
                        type: "read"
                    }, function (data, status) {
                        $(".tampil_data").html(data);
                        $("#hal").val(hal);
                    });
                }

            // ----------------- SHOW MODAL -----------------
                function TampilData() 
                {
                    $("#mode").val('Add');
                    $('#Data').modal('show');
                }

            // ----------------- EDIT DATA -----------------
                function GetData(id) {
                    $("#id").val(id);	
                    $.post("ajax/itempph_crud.php", {
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
			
            <!-- ------------ CONTENT----------- -->
			<form method="post" name="myform" action="cust_itempph.php?id=<?= $id_cust ?>&action=cari" class="form-horizontal">
                <div class="content-wrapper" style="min-height:750px">
                    <br>
                    <ol class="breadcrumb">
                        <li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Customer Item PPH</b></font></h1></li>					
                    </ol>
                    <br>
                    <div class="col-md-12">
                        <div class="box box-success box-solid" style="padding:5px; border:1px solid #ccc;">					
                            
                            <div class="small-box" 
                                style="font-size:11px; font-family:'Tahoma'; color:#fff; margin:0; background-color:#4783b7; text-align:left; padding:5px; margin-bottom:5px;">
                                <b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
                            </div>
                            
                            <div class="input-group" style="margin-bottom:5px;"> 
                                <span class="input-group-addon" style="text-align:right; min-width:150px;">
                                    <b>Customer :</b>
                                </span>
                                <input type="text" id="cus_name" name="cus_name" value="<?php echo $customer_name; ?>" style="text-align:left;margin-left:-5px;width:200px" readonly>
                            </div>

                            <div class="input-group" style="margin-bottom:5px;">
                                <span class="input-group-addon" style="text-align:right; min-width:150px;">
                                    <b>Find Item :</b>
                                </span>
                                <input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" style="text-align: left;margin-left:-5px;width:200px" onkeypress="ReadData(1)" >
                                <input type="hidden" id="hal" name="hal" value="<?php echo $hal; ?>">

                                <button class="btn btn-block btn-primary" style="margin:0px;margin-left:0px;margin-bottom:3px;border-radius:2px;padding:6px" type="submit" onClick="window.location.href = 'paket_data.php?id=<?php echo $xy1; ?>' ">
                                    <span class="glyphicon glyphicon-search"></span>
                                </button>
                            </div>

                        </div>
                    </div>

                    
                    <div class="col-md-12" >
                        <div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc;background:#fff !important;">	
                            <div style="width:100%;background: #fff;" class="input-group" >
                                <span class="input-group-addon" style="width:50%;text-align:left;padding:0px;background:#fff;">
                                    <?php if ($m_add == '1'){?>
                                    <button class="btn btn-block btn-success" 
                                        style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:3px" type="button" 
                                        onClick="javascript:TampilData()">
                                        <span class="fa  fa-plus-square"></span>
                                        <b>Add New</b>
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

            <!-- ------------ MODAL ------------ -->
            <div class="modal fade" id="Data"  role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="background: none">							
                        <div class="modal-body">	
                            <div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
                                <div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
                                    <div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
                                        &nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Route</b>
                                    </div>	
                                    <br>
                                    
                                    <input type="hidden" id="id" value=""/>	
                                    <input type="hidden" id="mode" value=""/>

                                    <div style="width:100%;" class="input-group">
                                        <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
                                        <button type="button" class="btn btn-success"  onclick="add()">
                                        <span class="fa fa-save"></span>&nbsp;&nbsp;<b>Save&nbsp;&nbsp;</button>	
                                        <button type="button" class="btn btn-danger" style="margin-left:-2px" data-dismiss="modal">
                                        <span class="fa fa-close"></span>&nbsp;&nbsp;<b>Cancel</button>	
                                    </div>
                                    <br>
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
