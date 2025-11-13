<?php
    session_start();
    include "koneksi.php"; 
    include "session_log.php";
    $pq = mysqli_query($koneksi,"SELECT * FROM m_role_akses_tr WHERE id_role = '$id_role' AND id_menu ='68'");

    $rq=mysqli_fetch_array($pq);	
    $m_edit = $rq['m_edit'];
    $m_add  = $rq['m_add'];
    $m_del  = $rq['m_del'];
    $m_view = $rq['m_view'];
    $m_exe  = $rq['m_exe'];

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
        // ----------------- READ DATA -----------------
            $(document).ready(function () {
                var hal = $("#hal").val();
                ReadData(hal);
            });
            function ReadData(hal) {
                
                var cari = $("#search_name").val();
                var paging = $("#paging").val();	
                $.get("ajax/pr_crud.php", {paging:paging,cari:cari,hal:hal, type:"ReadWH" }, function (data, status) {
                    $(".tampil_data").html(data);
                    $("#hal").val(hal);
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
                            
            }

        // ----------------- SHOW MODAL -----------------
            function TampilData() {
                $("#mode").val('Add');
                $('#Data').modal('show');
                $("#btnSave").show();
                $("#qty_close").hide();

                $("#Data").find("input[type=text], input[type=hidden], input[type=date], textarea").val("");
                $("#code_pr").val('auto');
            }

        // ----------------- FORMAT RUPIAH -----------------
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

        // ----------------- EDIT DATA -----------------
            function GetData(id) {
                $("#id").val(id);
                $.post("ajax/pr_crud.php", { id: id, type: "Detil_Data" }, function (data, status) {
                    try {
                        var res = JSON.parse(data);

                        $("#code_pr").val(res.code_pr);
                        $("#tgl_pr").val(res.tgl_pr);
                        $("#user_req").val(res.user_req);
                        $("#barang").val(res.barang);
                        $("#uom").val(res.uom);
                        $("#qty").val(res.qty);
                        $("#show_qty_close").val(res.qty_close);
                        $("#remark").val(res.remark);

                        $("#mode").val("Edit");

                        $("#btnSave").hide();
                        $("#qty_close").show();

                    } catch (e) {
                        console.error("JSON Parse Error:", e, data);
                        alert("Data tidak valid dari server!");
                    }
                });

                $("#Data").modal("show");
            }


        // ----------------- STORE DATA -----------------
            function add() {
                var r = confirm("Are you sure ?...");
                if (r == true) {	
                    var id = $("#id").val();
                    var mode = $("#mode").val();
                    var hal = $("#hal").val();

                    var tgl_pr  = $("#tgl_pr").val();
                    var user_req  = $("#user_req").val();
                    var barang  = $("#barang").val();
                    var uom     = $("#uom").val();
                    var qty     = $("#qty").val();
                    var remark  = $("#remark").val();

                    $.post("ajax/pr_crud.php", {
                        id:id,
                        mode:mode,
                        tgl_pr:tgl_pr,
                        user_req:user_req,
                        barang:barang,
                        uom:uom,
                        qty:qty,
                        remark:remark,
                        type : "Add_Data"
                    }, function (data, status) {
                        alert(data);

                        $("#Data").modal("hide");
                        $("#Data").find("input[type=text], input[type=hidden], input[type=date], textarea").val("");

                        ReadData(hal);
                    });

                }
            }

        // ----------------- EXECUTE DATA -----------------
            function Confirm(id) {
                const hal = $("#hal").val();
                const conf = confirm("Are you sure to Close ?");
                
                if (!conf) return;

                $.post("ajax/pr_crud.php", {
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

		<!-- ------------ CONTENT----------- -->
		<form method="post" name ="myform" action="pr_wh.php" class="form-horizontal" > 
            <div class="content-wrapper" style="min-height:750px">
                <br>
                <ol class="breadcrumb">
                    <li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data PR WH</b></font></h1></li>					
                </ol>
                <br>
                <div class="col-md-12" >
                    <div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
                        <div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
                                <b><i class="fa fa-search"></i>&nbsp;Filter PR WH</b>
                        </div>
                        <br>					
                        <div style="width:100%" class="input-group">
                            <span class="input-group-addon" style="text-align:right;"><b>Find PR WH :</b></span>
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
                                    $xy1    = "Add|";
                                    $xy1    = base64_encode($xy1);?>

                                    <button class="btn btn-block btn-success" 
                                    style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:2px" type="button" title = "Created Purchase Request"
                                    onClick="window.location.href = 'pr_wh_data.php?id=<?php echo $xy1; ?>' ">
                                    <span class="fa  fa-plus-square"></span>
                                    <b>Create PR WH</b>
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
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
