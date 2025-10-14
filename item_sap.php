<?php
    session_start();
    include "koneksi.php"; 
    include "session_log.php";

    $pq = mysqli_query($koneksi,"SELECT * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='70' ");

    $rq=mysqli_fetch_array($pq);	
    $m_edit = $rq['m_edit'];
    $m_add = $rq['m_add'];
    $m_del = $rq['m_del'];
    $m_view = $rq['m_view'];
    $m_exe = $rq['m_exe'];

    if(!isset($_SESSION['id_user'])  ||  $m_view != '1'  ){
        header('location:logout.php'); 
    }

    if($_SERVER['REQUEST_METHOD'] == "POST"){	
        $hal = $_POST['hal'];
        $search_name = $_POST['search_name'];
        $paging = $_POST['paging'];
    } else {	
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
                $.get("ajax/item_crud.php", {paging:paging,cari:cari,hal:hal, type:"Read" }, function (data, status) {
                    $(".tampil_data").html(data);
                    $("#hal").val(hal);
                });
            }

        // ============ SHOW MODAL ============
            function TampilData() {
                $("#mode").val('Add');
                $('#ModalAddEdit').modal('show');
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
                $.post("ajax/item_crud.php", {
                        id: id,
                        type: "Detil_Data"
                    },
                    function (data, status) {
                        try {
                            var res = JSON.parse(data);

                            $("#itemcode").val(res.sapitemcode);
                            $("#itemname").val(res.sapitemname);
                            $("#uom").val(res.uom);
                            $("#sap_coa").val(res.sap_coa);
                            $("#sap_corporate").val(res.sap_corporate);
                            $("#sap_divisi").val(res.sap_divisi);
                            $("#sap_dept").val(res.sap_dept);
                            $("#sap_activity").val(res.sap_activity);
                            $("#sap_location").val(res.sap_location);

                            $("#mode").val("Edit");
                        } catch (e) {
                            console.error("JSON Parse Error:", e, data);
                            alert("Data tidak valid dari server!");
                        }
                    }
                );
                $("#ModalAddEdit").modal("show");
            }

        // ============ STORE/UPDATE DATA ============
            function add() {
                var r = confirm("Are you sure ?...");
                if (r == true) {	
                    var id      = $("#id").val();
                    var mode    = $("#mode").val();
                    var hal     = $("#hal").val();

                    var itemcode      = $("#itemcode").val();
                    var itemname      = $("#itemname").val();
                    var uom           = $("#uom").val();
                    var sap_coa       = $("#sap_coa").val();
                    var sap_corporate = $("#sap_corporate").val();
                    var sap_divisi    = $("#sap_divisi").val();
                    var sap_dept      = $("#sap_dept").val();
                    var sap_activity  = $("#sap_activity").val();
                    var sap_location  = $("#sap_location").val();

                    $.post("ajax/item_crud.php", {
                        id:id,
                        mode:mode,
                        
                        itemcode:itemcode,
                        itemname:itemname,
                        uom:uom,
                        sap_coa:sap_coa,
                        sap_corporate:sap_corporate,
                        sap_divisi:sap_divisi,
                        sap_dept:sap_dept,
                        sap_activity:sap_activity,
                        sap_location:sap_location,
                        
                        type : "Add_Data"
                        }, function (data, status) {
                        alert(data);
                        $("#ModalAddEdit").modal("hide");				
                        ReadData(hal);

                        $("#itemcode").val('');
                        $("#itemname").val('');
                        $("#uom").val('');
                        $("#sap_coa").val('');
                        $("#sap_corporate").val('');
                        $("#sap_divisi").val('');
                        $("#sap_dept").val('');
                        $("#sap_activity").val('');
                        $("#sap_location").val('');
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

		<!-- ============ CONTENT ============ -->
		<form method="post" name ="myform" action="route.php" class="form-horizontal" > 
            <div class="content-wrapper" style="min-height:750px">
                <br>
                <ol class="breadcrumb">
                    <li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data Item SAP</b></font></h1></li>					
                </ol>
                <br>
                <div class="col-md-12" >
                    <div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
                        <div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
                                <b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
                        </div>
                        <br>					
                        <div style="width:100%" class="input-group">
                            <span class="input-group-addon" style="text-align:right;"><b>Find Item :</b></span>

                            <input type="text"  id ="search_name" name="search_name" value="<?php echo $search_name; ?>" style="text-align: left;width:200px" onkeypress="ReadData(1)" >

                            <input type="hidden" id ="hal" name="hal" value="<?php echo $hal; ?>" style="text-align: left;width:5%"  >
                            
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
                            <select size="1" id="paging"  name="paging" onchange="Tampil()" style="padding:4px;margin-right:2px">
                                <?php 
                                $tampil1="SELECT * from m_paging  order by baris";
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

    <!-- ============ MODAL ============ -->
    <div class="modal fade" id="ModalAddEdit"  role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="background: none">							
                <div class="modal-body">	
                    <div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
                        <div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
                            <div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
                                &nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data Item SAP</b>
                            </div>	
                            <br>
                            
                            <input type="hidden" id="id" value=""/>	
                            <input type="hidden" id="mode" value=""/>
                            
                            <div style="width:100%;" class="input-group">
                                <span class="input-group-addon" style="text-align:right;min-width:150px"><b>Itemcode :</b></span>
                                <input type="text" id="itemcode" value="" style="text-align: left;width:80%;" >
                            </div>
                            <div style="width:100%;" class="input-group">
                                <span class="input-group-addon" style="text-align:right;min-width:150px"><b>Itemname :</b></span>
                                <input type="text" id="itemname" value="" style="text-align: left;width:80%;" >
                            </div>

                            <div style="width:100%;" class="input-group">
                                <span class="input-group-addon" style="text-align:right;min-width:150px"><b>UoM :</b></span>
                                <input type="text" id="uom" value="" style="text-align: left;width:80%;" >
                            </div>
                            <div style="width:100%;" class="input-group">
                                <span class="input-group-addon" style="text-align:right;min-width:150px"><b>SAP COA :</b></span>
                                <input type="text" id="sap_coa" value="" style="text-align: left;width:80%;" >
                            </div>
                            <div style="width:100%;" class="input-group">
                                <span class="input-group-addon" style="text-align:right;min-width:150px"><b>SAP Corporate :</b></span>
                                <input type="text" id="sap_corporate" value="" style="text-align: left;width:80%;" >
                            </div>
                            <div style="width:100%;" class="input-group">
                                <span class="input-group-addon" style="text-align:right;min-width:150px"><b>SAP Divisi :</b></span>
                                <input type="text" id="sap_divisi" value="" style="text-align: left;width:80%;" >
                            </div>
                            <div style="width:100%;" class="input-group">
                                <span class="input-group-addon" style="text-align:right;min-width:150px"><b>SAP Department :</b></span>
                                <input type="text" id="sap_dept" value="" style="text-align: left;width:80%;" >
                            </div>
                            <div style="width:100%;" class="input-group">
                                <span class="input-group-addon" style="text-align:right;min-width:150px"><b>SAP Activity :</b></span>
                                <input type="text" id="sap_activity" value="" style="text-align: left;width:80%;" >
                            </div>
                            <div style="width:100%;" class="input-group">
                                <span class="input-group-addon" style="text-align:right;min-width:150px"><b>SAP Location :</b></span>
                                <input type="text" id="sap_location" value="" style="text-align: left;width:80%;" >
                            </div>

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
	
	<?php include "footer.php"; ?>
	<?php include "js.php"; ?>
	
  </body>
</html>
