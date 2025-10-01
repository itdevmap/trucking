<?php
session_start();
include "koneksi.php"; 
include "session_log.php";
$pq = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='63' ");

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
	$search_name = $_POST['search_name'];
	$paging = $_POST['paging'];
}
else
{	
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
                $.get("ajax/sj_crud.php", {paging:paging,cari:cari,hal:hal, type:"Read" }, function (data, status) {
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
            }
            function TampilSO(){
                $("#cari").val('');
                ListSO();
                $('#DaftarSJ').modal('show');
            }
            function ListSO() {
                var cari = $("#cari").val();
                $.get("ajax/sj_crud.php", {cari:cari,  type:"ListSO" }, function (data, status) {
                    $(".tampil_sj").html(data);
                    $("#hal").val(hal);
                });
            }
            function PilihSO(id) {
                $.post("ajax/sj_crud.php", {
                        id: id, type:"DetilData"
                    },
                    function (data, status) {
                        var data = JSON.parse(data);	
                        $("#no_jo").val(data.no_jo);
                        $("#project_code").val(data.project_code);
                        $("#no_do").val(data.no_do);
                        $("#nama_cust").val(data.nama_cust);
                        $("#penerima").val(data.penerima);
                        $("#container").val(data.container);
                        $("#route").val(data.rute);
                    }
                );
                $("#DaftarSJ").modal("hide");
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
                $.post("ajax/route_crud.php", {
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

        // ----------------- STORE/UPDATE DATA -----------------
            function addSJ() {
                var r = confirm("Are you sure ?...");
                if (r == true) {	
                    var id = $("#id").val();
                    var mode = $("#mode").val();
                    var hal = $("#hal").val();

                    var no_jo = $("#no_jo").val();
                    var project_code = $("#project_code").val();
                    var no_do = $("#no_do").val();
                    var container = $("#container").val();
                    var route = $("#route").val();
                    var seal = $("#seal").val();
                    var id_mobilx = $("#id_mobilx").val();
                    var id_supirx = $("#id_supirx").val();
                    var desc = $("#desc").val();

                    $.post("ajax/sj_crud.php", {
                        id:id,
                        mode:mode,
                        
                        no_jo:no_jo,
                        project_code:project_code,
                        no_do:no_do,

                        route:route,
                        container:container,
                        seal:seal,
                        id_mobil:id_mobilx,
                        id_supir:id_supirx,
                        desc:desc,
                        
                        type : "Add_Data"
                        }, function (data, status) {
                        alert(data);
                        $("#Data").modal("hide");				
                        ReadData(hal);
                    });
                }
            }

            function Delete(id) {
                var conf = confirm("Are you sure to Delete ?");
                if (conf == true) {
                    $.post("ajax/sj_crud.php", {
                            id: id, type:"Del_Order"
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

		<!-- ------------ CONTENT----------- -->
		<form method="post" name ="myform" action="sj.php" class="form-horizontal" > 
            <div class="content-wrapper" style="min-height:750px">
                <br>
                <ol class="breadcrumb">
                    <li><h1><i class="fa fa-list"></i><font size="4">&nbsp;&nbsp;<b>Data SJ</b></font></h1></li>					
                </ol>
                <br>
                <div class="col-md-12" >
                    <div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">					
                        <div class="small-box bg" style="font-size:11px;font-family: 'Tahoma';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
                                <b><i class="fa fa-search"></i>&nbsp;Filter Data</b>
                        </div>
                        <br>					
                        <div style="width:100%" class="input-group">
                            <span class="input-group-addon" style="text-align:right;"><b>Find JO :</b></span>
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
                                <?php if ($m_add == '1'){?>
                                <button class="btn btn-block btn-success" 
                                    style="margin:0px;margin-left:0px;margin-bottom:0px;border-radius:2px" type="button" onClick="javascript:TampilData()">
                                    <span class="fa  fa-plus-square"></span>
                                    <b>Create SJ</b>
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

    <!-- ------------ MODAL ADD SJ ------------ -->
        <div class="modal fade" id="Data"  role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="background: none">							
                    <div class="modal-body">	
                        <div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
                            <div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
                                <div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
                                    &nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data SJ</b>
                                </div>	
                                <br>
                                
                                <input type="hidden" id="id" value=""/>	
                                <input type="hidden" id="mode" value=""/>

                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No JO :</b></span>
                                    <input type="text"  id ="no_jo" name="no_jo" value="<?php echo $no_jo;?>" style="text-align: left;width:70%" readonly  >
                                    <button class="btn btn-block btn-primary" id="btn_custx"
                                        style="padding:6px 12px 6px 12px;margin-top:-3px;border-radius:2px;margin-left:5px" type="button" 
                                        onClick="javascript:TampilSO()" <?php echo $disx;?> >
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                    <input type="hidden" id="id_cust"  name="id_cust" value="<?php echo $id_cust;?>" 
                                    style="text-align: right;width:25%;border:1px solid rgb(169, 169, 169)" />		
                                </div>
                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;">
                                        <b>Project Code :</b>
                                    </span>
                                    <input type="text" id ="project_code" name="project_code" style="width:70%"  <?php echo $dis;?> readonly>						
                                </div>
                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;">
                                        <b>Cust Name :</b>
                                    </span>
                                    <input type="text" id ="nama_cust" name="nama_cust" style="width:70%"  <?php echo $dis;?> readonly>						
                                </div>
                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;">
                                        <b>Receiver :</b>
                                    </span>
                                    <input type="text" id ="penerima" name="penerima" style="width:70%"  <?php echo $dis;?> readonly>						
                                </div>
                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;">
                                        <b>Route :</b>
                                    </span>
                                    <input type="text" id ="route" name="route" style="width:70%"  <?php echo $dis;?> readonly>						
                                </div>
                                <!-- <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Weight :</b></span>
                                    <input type="text" id="berat" value="0" style="text-align: right;width:22%;" 
                                    onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  > &nbsp;<b>KG</b>	
							    </div>
                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Vol :</b></span>
                                    <input type="text" id="volx" value="0" style="text-align: right;width:22%;" 
                                    onBlur ="this.value=Desimal(this.value);" onkeypress="return isNumber(event)"  > &nbsp;<b>M3</b>	
                                </div> -->
                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;">
                                        <b>No. Container :</b>
                                    </span>
                                    <input type="text" id ="container" name="container" style="width:70%"  <?php echo $dis;?>>						
                                </div>
                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;">
                                        <b>No. Seal :</b>
                                    </span>
                                    <input type="text" id ="seal" name="seal" style="width:70%"  <?php echo $dis;?>>						
                                </div>
                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>No. Police :</b></span>
                                    <select id="id_mobilx"  style="width: 70%;padding:4px">
                                        <?php
                                        $t1="SELECT * FROM m_mobil_tr WHERE STATUS = '1' ORDER BY no_polisi  ";
                                        $h1=mysqli_query($koneksi, $t1);       
                                        while ($d1=mysqli_fetch_array($h1)){?>
                                        <option value="<?php echo $d1['id_mobil'];?>" ><?php echo $d1['no_polisi'];?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Driver :</b></span>
                                    <select id="id_supirx"  style="width: 70%;padding:4px">
                                        <?php
                                        $t1="SELECT * FROM m_supir_tr WHERE STATUS = '1' ORDER BY nama_supir  ";
                                        $h1=mysqli_query($koneksi, $t1);       
                                        while ($d1=mysqli_fetch_array($h1)){?>
                                        <option value="<?php echo $d1['id_supir'];?>" ><?php echo $d1['nama_supir'];?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <div style="width:100%;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"><b>Description :</b></span>
                                    <textarea id="desc" class="form-textarea" rows="3" style="width:70%" placeholder=""></textarea>
                                </div>

                                <div style="width:100%; margin-top:10px;" class="input-group">
                                    <span class="input-group-addon" style="text-align:right;background:none;min-width:150px"></span>
                                    <button type="button" class="btn btn-success"  onclick="addSJ()">
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

    <!-- ------------ MODAL DAFTAR SO ------------ -->
        <div class="modal fade" id="DaftarSJ"  role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="background: none">	
                    <div class="modal-body">						
                        <div class="col-md-12" style="min-height:40px;border:0px solid #ddd;padding:0px;border-radius:5px;">
                            <div class="box box-success box-solid" style="padding:5px;border:1px solid #ccc">	
                                <div class="small-box bg" style="font-size:12px;font-family: 'Arial';color :#fff;margin:0px;background-color:#4783b7;text-align:left;padding:5px;margin-bottom:1px">							
                                    &nbsp;&nbsp;<b><i class="fa fa-list"></i>&nbsp;Data SO</b>
                                </div>	
                                <br>
                                <div style="width:100%" class="input-group" style="background:none !important;">
                                    <span class="input-group-addon" style="width:80%;text-align:left;padding:0px">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Search :</b>&nbsp;&nbsp;
                                        <input type="text"  id ="cari" name="cari" value="<?php echo $cari; ?>" 
                                        style="text-align: left;width:200px" onkeypress="ListSO()" >
                                        <button class="btn btn-block btn-primary" 
                                        style="margin:0px;margin-left:-3px;margin-bottom:3px;border-radius:2px;padding:5px" 
                                        onClick="javascript:ListSO()">
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
                                    <div class="tampil_sj"></div>
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
