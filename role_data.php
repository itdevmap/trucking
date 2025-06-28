<?php
session_start();
include "koneksi.php"; 
include "session_log.php"; 
include "lib.php";




if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$mode=$_POST['mode'];
	$id=$_POST['id'];
	$nama=$_POST['nama'];
	$page=$_POST['page'];
	$stat=$_POST['stat'];
	$jml = $_POST['jml'];	
	if($stat == 'Aktif')
	{
		$statx = '1';		
	}else{
		$statx='0';
	}
	
	if($mode == 'Add')
	{	
		$sql="INSERT INTO m_role_tr (nama_role,status) values ('$nama','$statx')"; 
	}
	else
	{
		$sql="UPDATE m_role_tr set 
			  nama_role = '$nama',
			  status = '$statx'
			  where id_role = '$id' ";
	}	
	$hasil=mysqli_query($koneksi, $sql);
	if($hasil)
	{
		$cat ="Data berhasil disimpan..";
		$penghapusan = MYSQLI_QUERY($koneksi, "DELETE FROM m_role_akses_tr WHERE id_role ='$id'   ");
		for($i = 0; $i <= $jml; $i++)
		{ 
			$view = $_POST['view'.$i];  
			$add = $_POST['add'.$i];  
			$edit = $_POST['edit'.$i];  
			$del = $_POST['del'.$i];  
			$exe = $_POST['exe'.$i];  
			$idy = $_POST['idy'.$i]; 
			
			$perintah = "INSERT INTO m_role_akses_tr
			(id_role,id_menu,m_view,m_add,m_edit,m_del,m_exe) values ('$id','$idy','$view','$add','$edit','$del','$exe')"; 
			mysqli_query($koneksi, $perintah); 
		}
	}
	else
	{
		$cat = "Data gagal disimpan...";
	}
	
}
else
{
	$idx = $_GET['id'];	
	$x=base64_decode($idx);
	$pecah = explode("|", $x);
	$mode= $pecah[0];
	$id = $pecah[1];
	$page = $pecah[2];
}

if($mode == 'Edit')
{
	$sql = mysqli_query($koneksi, "select * from m_role_tr where id_role = '$id' ");
	$row = mysqli_fetch_array($sql);
	$nama = $row['nama_role'];
	if($row['status']=='1')
	{
		$stat = 'Aktif';
	}else{
		$stat = 'Tidak Aktif';
	}
}


?>


<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><? echo $aplikasi; ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	
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
		function checkvalue() {
			var nama = document.getElementById('nama').value; 
			var stat = document.getElementById('stat').value; 
			
			if(!nama.match(/\S/)) {
				alert ('Nama Role harus diisi..');				
				return false;	
			}else if(!stat.match(/\S/)) {
				alert ('Status harus diisi..');				
				return false;	
			} else {
				return true;
			}	
		}
		
    </script>
	
  </head>
  <body class="hold-transition skin-blue sidebar-mini sidebar-collapse" onload="Disabled()">
	
	<div class="wrapper">
		<header class="main-header">
			<?php include "header.php"; ?>	 
		</header>
		<aside class="main-sidebar">
			<?php include "menu.php" ; ?>	
		</aside>	
		
		
		<form method="post" name ="myform" action="role_data.php?action=save" class="form-horizontal" onsubmit="return checkvalue(this)" > 
		<div class="content-wrapper">   
			<section class="content">
				<br>
				<ol class="breadcrumb">
					<li><h1><i class="fa fa-gear"></i><b>&nbsp;&nbsp;<font size="4"><b>Data Role</b></font></h1></li>					
				</ol>
				
				<?php if ($cat != ''){?>
					<div class="callout callout-danger" style="margin-bottom: 0!important;width:98%">
						<i class="icon 	fa fa-info-circle" style="color:#cbe20d;font-size:16px"></i> <font color="#fff"><?php echo $cat; ?></font>
					</div>
					<br>
				<?php }?>
				
				<div class="col-md-6" style="width:50%;border:1px solid #ddd;padding:5px">
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;">NAMA ROLE :</span>
						<input type="text"  id ="nama" name="nama" value="<?php echo $nama; ?>" 						
						style="text-align: left;width:90%;"  >
						<input type="hidden"  id ="id" name="id" value="<?php echo $id; ?>"   >	
						<input type="hidden"  id ="mode" name="mode" value="<?php echo $mode; ?>"   >	
						<input type="hidden"  id ="page" name="page" value="<?php echo $page; ?>"   >	
					</div>
					<div style="width:100%;" class="input-group">
						<span class="input-group-addon" style="text-align:right;">STATUS</span>
						<select size="1" id="stat" name="stat" style="padding:4px;margin-right:2px;width:100px">
							<option >Aktif</option>
							<option >Tidak Aktif</option>
							<option value="<?php echo $stat; ?>" selected><?php echo $stat; ?></option>
						</select>
					</div>
				</div>	
				<div class="col-md-6" style="width:50%;border:1px solid #ddd;padding:5px">
					<table class="table table-hover table-striped" style="width:100%">
						<thead style="font-weight:500px !important">
						<tr>	
							<th  width="88%" style="text-align: center;">NAMA MENU</th>
							<th  width="3%" style="text-align: center;">VIEW</th>
							<th  width="3%" style="text-align: center;">ADD</th>
							<th  width="3%" style="text-align: center;">EDIT</th>	
							<th  width="3%" style="text-align: center;">DEL</th>	
							<th  width="3%" style="text-align: center;">EXE</th>							
						</tr>
						</thead>
						<?php
						$tampil1="select * from m_menu_tr where id_parent = '0'  order by id_menu  ";
						$hasil1=mysqli_query($koneksi, $tampil1);       
						while ($data1=mysqli_fetch_array($hasil1))
						{ 
					
						$n++; 
						$view_c='';
						$add_c='';
						$edit_c='';
						$del_c='';
						$exe_c='';
						$s1 = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id' and id_menu ='$data1[id_menu]'  ");
						$r1=mysqli_fetch_array($s1);	
						if($r1['m_view']=='1'){$view_c ='checked';}
						if($r1['m_add']=='1'){$add_c ='checked';}
						if($r1['m_edit']=='1'){$edit_c ='checked';}
						if($r1['m_del']=='1'){$del_c ='checked';}
						if($r1['m_exe']=='1'){$exe_c ='checked';}
						?>
							<tr>
							<td style="text-align: left;"><?php echo $data1['nama_menu'];?></td>
							<td style="text-align: center;">
								<input type="hidden"  <?php echo "name='idy".$n."' "; ?> value="<?php echo $data1['id_menu']; ?>" >
								<input type="checkbox"  <?php echo "name='view".$n."' "; ?> style="margin-bottom:0px;" value="1" <?php echo $view_c;?> >
							</td>
							<td style="text-align: center;">
								<input type="checkbox"  <?php echo "name='add".$n."' "; ?> style="margin-bottom:0px;" value="1" <?php echo $add_c;?> >
							</td>
							<td style="text-align: center;">
								<input type="checkbox"  <?php echo "name='edit".$n."' "; ?> style="margin-bottom:0px;" value="1" <?php echo $edit_c;?> >
							</td>	
							<td style="text-align: center;">
								<input type="checkbox"  <?php echo "name='del".$n."' "; ?> style="margin-bottom:0px;" value="1" <?php echo $del_c;?> >
							</td>	
							<td style="text-align: center;">
								<input type="checkbox"  <?php echo "name='exe".$n."' "; ?> style="margin-bottom:0px;" value="1" <?php echo $exe_c;?> >
							</td>	
							</tr>
							
							<?php
							$tampil2="select * from m_menu_tr where id_parent = '$data1[id_menu]'  order by id_menu  ";
							$hasil2=mysqli_query($koneksi, $tampil2);       
							while ($data2=mysqli_fetch_array($hasil2))
							{ 
							$n++; 
							$view_c='';
							$add_c='';
							$edit_c='';
							$del_c='';
							$exe_c='';
							$s1 = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id' and id_menu ='$data2[id_menu]'  ");
							$r1=mysqli_fetch_array($s1);	
							if($r1['m_view']=='1'){$view_c ='checked';}
							if($r1['m_add']=='1'){$add_c ='checked';}
							if($r1['m_edit']=='1'){$edit_c ='checked';}
							if($r1['m_del']=='1'){$del_c ='checked';}
							if($r1['m_exe']=='1'){$exe_c ='checked';}
							?>
						
								<tr>
								<td style="text-align: left;">----<?php echo $data2['nama_menu'];?></td>
								<td style="text-align: center;">
								<input type="hidden"  <?php echo "name='idy".$n."' "; ?> value="<?php echo $data2['id_menu']; ?>" >
								<input type="checkbox"  <?php echo "name='view".$n."' "; ?> style="margin-bottom:0px;" value="1" <?php echo $view_c;?> >
								</td>
								<td style="text-align: center;">
								<input type="checkbox"  <?php echo "name='add".$n."' "; ?> style="margin-bottom:0px;" value="1" <?php echo $add_c;?> >
								</td>
								<td style="text-align: center;">
								<input type="checkbox"  <?php echo "name='edit".$n."' "; ?> style="margin-bottom:0px;" value="1" <?php echo $edit_c;?> >
								</td>	
								<td style="text-align: center;">
								<input type="checkbox"  <?php echo "name='del".$n."' "; ?> style="margin-bottom:0px;" value="1" <?php echo $del_c;?> >
								</td>
								<td style="text-align: center;">
								<input type="checkbox"  <?php echo "name='exe".$n."' "; ?> style="margin-bottom:0px;" value="1" <?php echo $exe_c;?> >
								</td>									
								</tr>
							
						<?php }}?>
					</table>
				</div>
				<?php
					$link = "role.php?id=$xy1";
				?>
				<input type="hidden" name="jml" size="17" style="font-family: Tahoma; font-size: 8pt" value="<?php echo $n; ?>">
				<div style="width:98%;background:none;margin-left:0;margin-top:0px" class="input-group">
					<input type="submit" id="btn-login" value="SIMPAN" name="B1" class="btn btn-success" >
					<input type="button" id="btn-login" value="KEMBALI" name="B1" class="btn btn-success"  
					onclick="window.location.href='<?php echo $link; ?>'">										
				</div>
				
				<div style="width:100%;border:none;background:none" class="input-group">
					<span class="input-group-addon" style="text-align:right;background:none">.</span>						
				</div>
				<div style="width:100%;border:none;background:none" class="input-group">
					<span class="input-group-addon" style="text-align:right;background:none">.</span>						
				</div>
				<div style="width:100%;border:none;background:none" class="input-group">
					<span class="input-group-addon" style="text-align:right;background:none"></span>						
				</div>
			</section>	
		</div>    
		</form>	
	</div>		
	
	<script type="text/javascript" src="ajax/daftar.js"></script> 
	<? include "footer.php"; ?>
	<? include "js.php"; ?>
	<script>
      $(function () {
        $(".select2").select2();

      });
    </script>
  </body>
</html>
