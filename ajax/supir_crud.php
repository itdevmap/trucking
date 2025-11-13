<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "SELECT * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='11' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];


if ($_GET['type'] == "Read")
{
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	$search_name = $_GET['search_name'];
	$field = $_GET['field'];	
	$stat = $_GET['stat'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="20%" style="text-align: center;">DRIVER NAME</th>	
					<th rowspan="2" width="7%" style="text-align: center;">DATE OF <br>BIRTH</th>
					<th rowspan="2" width="7%" style="text-align: center;">GEBDER</th>
					<th rowspan="2" width="7%" style="text-align: center;">MARITIAL<br>STATUS</th>
					<th rowspan="2" width="7%" style="text-align: center;">RELIGION</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. TELP</th>	
					<th rowspan="2" width="10%" style="text-align: center;">SIM<br>TYPE</th>	
					<th rowspan="2" width="5%" style="text-align: center;">BACKUP</th>	
					<th rowspan="2" width="7%" style="text-align: center;">SIM<br>VALIDITY <br>PERIOD</th>	
					<th colspan="3" width="6%" style="text-align: center;">DOWNLOAD<br>DOCUMENT</th>	
					<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>
					<th colspan="3" width="6%" style="text-align: center;">ACTION</th>						
				</tr>
				<tr>	
					<th rwidth="2%" style="text-align: center;">KTP</th>
					<th rwidth="2%" style="text-align: center;">KK</th>
					<th rwidth="2%" style="text-align: center;">SIM</th>
					<th rwidth="2%" style="text-align: center;">EDIT</th>
					<th rwidth="2%" style="text-align: center;">PHOTO</th>
					<th rwidth="2%" style="text-align: center;">DOC</th>
				</tr>
			</thead>';		
				
	if(!isset($_GET['hal'])){ 
		$page = 1;       
		} else { 
		$page = $_GET['hal']; 
		$posisi=0;
	}
	$jmlperhalaman = $paging;
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman); 
	
	$SQL = "SELECT * from m_supir_tr where nama_supir LIKE '%$search_name%'  order by nama_supir 
			LIMIT $offset, $jmlperhalaman";
	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			$nama = str_replace("\'","'",$row['nama_supir']);
			$term = 0;	
			//STNK
			$tanggal_lahir = ConverTgl($row['tanggal_lahir']);
			$tanggal_masuk = ConverTgl($row['tanggal_masuk']);
			$masa_berlaku = ConverTgl($row['masa_berlaku']);
			$due = date('Y-m-d',strtotime($row['masa_berlaku']) + (24*3600*$term)); 
			$duex = ConverTgl($due);
			$duey = strtotime($due);
			$tgl_sekarang = date('Y-m-d');
			$tgl_sekarang = strtotime($tgl_sekarang);
			$aging = $tgl_sekarang - $duey; 
			$aging = ($aging/24/60/60);
			$aging = round($aging);
			if($aging > 0 )
			{
				$label_sim = 'danger';
			}else if($aging >= -7)
			{
				$label_sim = 'warning';
			}else{
				$label_sim = 'success';
			}
			if($row['photo'] == '')
			{
				$photo = "supir/no.jpg";
			}else{
				$photo = strtolower($row['photo']);
			}
			
			
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:left">
					<div class="hover_img">
						<a href="#" onclick="javascript:ViewData('.$row['id_supir'].')">'.$nama.'
						<span><img src="'.$photo.'" alt="image" width="120px" height="140px" /></span></a>
					</div>
				</td>
				<td style="text-align:center">'.$tanggal_lahir.'</td> 	
				<td style="text-align:center">'.$row['kelamin'].'</td>
				<td style="text-align:center">'.$row['perkawinan'].'</td>
				<td style="text-align:center">'.$row['agama'].'</td>	
				<td style="text-align:center">'.$row['telp'].'</td>
				<td style="text-align:center">'.$row['jenis_sim'].'</td>
				<td style="text-align:center">'.$row['cadangan'].'</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label_sim.'" style="width:100%;padding:1px;margin:-3px">'.$masa_berlaku.'</button>
				</td>';
				if($row['ktp'] == '')
				{
					$data .='<td></td>';
				}else{
					$photo = strtolower($row['ktp']);
					$link = "'$photo'";
					$data .= '<td>
								<button class="btn btn-block btn-default" 
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="window.open('.$link.') "   >
									<span class="fa fa-file-text " ></span>
								</button></td>';
				}
				if($row['kk'] == '')
				{
					$data .='<td></td>';
				}else{
					$photo = strtolower($row['kk']);
					$link = "'$photo'";
					$data .= '<td>
								<button class="btn btn-block btn-default" 
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="window.open('.$link.') "   >
									<span class="fa fa-file-text " ></span>
								</button></td>';
				}
				if($row['sim'] == '')
				{
					$data .='<td></td>';
				}else{
					$photo = strtolower($row['sim']);
					$link = "'$photo'";
					$data .= '<td>
								<button class="btn btn-block btn-default" 
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="window.open('.$link.') "   >
									<span class="fa fa-file-text " ></span>
								</button></td>';
				}
				
				if($row['status'] =='0' ){
					$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-danger" style="margin:-3px;width:100%;padding:1px;border-radius:1px">&nbsp;In Active &nbsp;</button>
					</td>';
				} else if($row['status'] =='1'){
					$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-success" style="margin:-3px;width:100%;padding:1px;border-radius:1px">Active</button>
					</td>';	
				}
				if($m_edit == '1'  ){
					$data .= '<td>
								<button class="btn btn-block btn-default" 
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_supir'].')"   >
									<span class="fa fa-edit " ></span>
								</button></td>';
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Upload Photo"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:GetImg('.$row['id_supir'].')"   >
									<span class="fa fa-image" ></span>
								</button></td>';
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Upload Document"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:GetDoc('.$row['id_supir'].')"   >
									<span class="fa fa-upload " ></span>
								</button></td>';	
				}
				else
				{
					$data .='<td></td>';
					$data .='<td></td>';
					$data .='<td></td>';
				}	
				$data .='</tr>';
    	}		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';	
	$data .= '<div class="paginate paginate-dark wrapper">
				<ul>';
				$pq = mysqli_query($koneksi, "select count(id_supir) as jml from m_supir_tr where nama_supir LIKE '%$search_name%' ");
				
				$rq=mysqli_fetch_array($pq);
				$total_record = $rq['jml'];										
				$total_halaman = ceil($total_record / $jmlperhalaman);
					
				if ($total_record > $jmlperhalaman){
					$perhal=4;
					if($hal > 1){ 
						$prev = ($page - 1); 
						$data .='<li><a href=# onclick="ReadData('.$prev.')">Prev</a></li> '; 
					}
					if($total_halaman<=$jmlperhalaman){
						$hal1=1;
						$hal2=$total_halaman;
						}else{
						$hal1=$hal-$perhal;
						$hal2=$hal+$perhal;
					}
					if($hal<=5){
						$hal1=1;
					} 
					if($hal<$total_halaman){
						$hal2=$hal+$perhal;
						}else{
						$hal2=$hal;
					}
					for($i = $hal1; $i <= $hal2; $i++){ 
						if(($hal) == $i){ 
							$data .='<li><a href="#" class="active">'.$i.'</a></li> '; 
							}else{ 
							if($i<=$total_halaman){
								$data .='<li><a href=# onclick="ReadData('.$i.')">'.$i.'</a></li> ';
							}
						} 
					}
					if($hal < $total_halaman){ 
						$next = ($page + 1); 
						$data .='<li><a href=# onclick="ReadData('.$next.')">Next</a></li> '; 
					} 
				}
				$data .= '</ul></div>';
				
    echo $data;

	
	
}else if ($_POST['type'] == "add"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$id_supir = $_POST['id_supir'];
		$nama = strtoupper($_POST['nama']);
		$sponsor = strtoupper($_POST['sponsor']);
		$no_ktp = strtoupper($_POST['no_ktp']);
		$tempat_lahir = $_POST['tempat_lahir'];
		$tanggal_lahir = $_POST['tanggal_lahir'];
		$tanggal_masuk = $_POST['tanggal_masuk'];
		$kelamin = $_POST['kelamin'];
		$agama = $_POST['agama'];
		$perkawinan = $_POST['perkawinan'];
		$alamat = $_POST['alamat'];
		$telp = $_POST['telp'];
		$stat = $_POST['stat'];
		$no_sim = strtoupper($_POST['no_sim']);
		$jenis_sim = $_POST['jenis_sim'];
		$masa_berlaku = $_POST['masa_berlaku'];
		$mode = $_POST['mode'];
		$status = $_POST['stat'];	
		$tanggal_lahir = ConverTglSql($tanggal_lahir);
		$masa_berlaku = ConverTglSql($masa_berlaku);
		$tanggal_masuk = ConverTglSql($tanggal_masuk);
		
		if($mode == 'Add')
		{	
			$tanggal = date('Y-m-d');
			$sql = "INSERT INTO m_supir_tr (nama_supir, no_ktp,tempat_lahir,tanggal_lahir, kelamin, agama, perkawinan, alamat, telp, no_sim, jenis_sim, masa_berlaku, 
			status, created, tanggal,tanggal_masuk)
					values
					('$nama','$no_ktp','$tempat_lahir','$tanggal_lahir','$kelamin','$agama','$perkawinan','$alamat','$telp','$no_sim','$jenis_sim',
					'$masa_berlaku','1','$id_user','$tanggal','$tanggal_masuk') ";
			$hasil=mysqli_query($koneksi, $sql);	
		}
		else
		{
			$sql = "update m_supir_tr set
			nama_supir = '$nama',
			no_ktp = '$no_ktp',
			tempat_lahir = '$tempat_lahir',
			tanggal_lahir = '$tanggal_lahir',
			kelamin = '$kelamin',
			agama = '$agama',
			perkawinan = '$perkawinan',
			alamat = '$alamat',
			telp = '$telp',
			no_sim = '$no_sim',
			jenis_sim = '$jenis_sim',
			status = '$stat',
			masa_berlaku = '$masa_berlaku',
			tanggal_masuk = '$tanggal_masuk'	
			where id_supir = '$id'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        			
			exit(mysqli_error($koneksi));
			echo "Nama Supir sudah terdaftar...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
	
	
}else if ($_POST['type'] == "Update_Status"){	
	$id = $_POST['id'];
	$tanggal_keluar = $_POST['tanggal_keluar'];
	$alasan = addslashes($_POST['alasan']);
	$tanggal_keluar = ConverTglSql($tanggal_keluar);
	
	$sql = "update m_supir_tr set
			tanggal_keluar = '$tanggal_keluar',
			alasan = '$alasan',
			status = '0'	
			where id_supir = '$id'	";
	$hasil=mysqli_query($koneksi, $sql);
			
	if (!$hasil) {	
		exit(mysqli_error($koneksi));
		echo "Nama Supir sudah terdaftar...!";
	}
	else
	{	
		echo "Data saved!";
	}	
	
}else if ($_POST['type'] == "detil"){
	$id = $_POST['id'];	
    $query = "select * from m_supir_tr where id_supir  = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }
    $response = array();
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response = $row;
        }
    }
    else
    {
        $response['status'] = 200;
        $response['message'] = "Data not found!";
    }
    echo json_encode($response);


}

?>