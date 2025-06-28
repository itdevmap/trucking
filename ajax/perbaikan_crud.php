<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='24' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

if ($_GET['type'] == "Read")
{
	$cari = trim($_GET['cari']);
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	$tgl1 = $_GET['tgl1'];
	$tgl2 = $_GET['tgl2'];
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);	
	$field = $_GET['field'];
	$field1 = $_GET['field1'];
	$cari1 = trim($_GET['cari1']);
	
	if($field == 'No Polisi')
	{
		$f = 'm_mobil_tr.no_polisi';	
	}else if($field == 'No SPK'){
		$f = 't_spk.no_spk';	
	}else if($field == 'Jenis Pekerjaan'){
		$f = 't_spk.jenis';
	}else{
		$f = 't_spk.ket';	
	}
	
	if($field1 == 'No Polisi')
	{
		$f1 = 'm_mobil_tr.no_polisi';	
	}else if($field1 == 'No SPK'){
		$f1 = 't_spk.no_spk';	
	}else if($field1 == 'Jenis Pekerjaan'){
		$f1 = 't_spk.jenis';
	}else{
		$f1 = 't_spk.ket';	
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL</th>
					<th rowspan="2" width="10%" style="text-align: center;">NO. SPK</th>
					<th rowspan="2" width="6%" style="text-align: center;">START</th>
					<th rowspan="2" width="6%" style="text-align: center;">FINISH</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. POLISI</th>
					<th rowspan="2" width="6%" style="text-align: center;">KM</th>
					<th rowspan="2" width="39%" style="text-align: center;">JENIS PEKERJAAN</th>
					<th rowspan="2" width="3%" style="text-align: center;">FOTO</th>
					<th rowspan="2" width="6%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>	
					<th rowspan="2" width="2%" style="text-align: center;">ADD<br>FOTO</th>	
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>	
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
	
	$SQL = "select t_spk.*, m_mobil_tr.no_polisi
			from 
			t_spk left join m_mobil_tr on t_spk.id_mobil = m_mobil_tr.id_mobil
			where t_spk.tanggal between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' 	
			order by t_spk.tanggal desc
			LIMIT $offset, $jmlperhalaman";
			
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			$tanggal = ConverTgl($row['tanggal']);
			$km = number_format($row['km'],0);	
			$ket = nl2br($row['ket']); 			
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.	'</td>
				<td style="text-align:center">'.$row['no_spk'].'</td>
				<td style="text-align:center">'.$row['jam_mulai'].' : '.$row['menit_mulai'].'</td>
				<td style="text-align:center">'.$row['jam_selesai'].' : '.$row['menit_selesai'].'</td>
				<td style="text-align:center">'.$row['no_polisi'].'</td>
				<td style="text-align:center">'.$km.'</td>
				<td style="text-align:center">'.$row['jenis'].'</td>';
				if($row['photo'] == '')
				{
					$data .='<td></td>';
				}else{
					$photo = strtolower($row['photo']);
					$link = "'$photo'";
					$data .= '<td>
								<button class="btn btn-block btn-default" 
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="window.open('.$link.') "   >
									<span class="fa fa-image " ></span>
								</button></td>';
				}	
				
			$data .='<td style="text-align:center">'.$row['created'].'</td>';					
				
				if($m_edit == '1'  ){
					$xy1="Edit|$row[id_spk]";
					$xy1=base64_encode($xy1);
					$link = "'perbaikan_data.php?id=$xy1'";
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="window.location.href = '.$link.' "  >
									<span class="fa fa-edit " ></span>
								</button></td>';
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Upload Photo"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:GetImg('.$row['id_spk'].')"   >
									<span class="fa fa-cloud-upload" ></span>
								</button></td>';			
				}
				else
				{
					$data .='<td></td>';
					$data .='<td></td>';
				}
				if($m_del == '1') 	
				{
					$data .= '<td>
							<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:Delete('.$row['id_spk'].')"  >
								<span class="fa fa-close " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				$data .='</tr>';
    		$number++;
    	}		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
	
	$data .= '<div class="paginate paginate-dark wrapper">
				<ul>';
				$pq = mysqli_query($koneksi, "select count(t_spk.id_spk) as jml
					from 
					t_spk left join m_mobil_tr on t_spk.id_mobil = m_mobil_tr.id_mobil
					where t_spk.tanggal between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%'  ");					
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

}else if ($_POST['type'] == "Del_SPK"){
	$id = $_POST['id']; 	
	
	$pq = mysqli_query($koneksi, "select * from t_spk where id_spk = '$id' ");
	$rq=mysqli_fetch_array($pq);	
	$photo = strtolower($rq['photo']);
	unlink("../$photo");
	
	$t1="select * from  t_spk_part where id_spk = '$id' ";
	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{  
		$sql = "update m_part set keluar = keluar - '$d1[qty]' where id_part = '$d1[id_part]' ";
		$hasil=mysqli_query($koneksi, $sql);
	}
		
	$del = mysqli_query($koneksi, "DELETE FROM t_spk_part WHERE id_spk = '$id'");	
	
    $query = "DELETE FROM t_spk WHERE id_spk = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }

}
else if($_GET['type'] == "Read_Data")
{
	$id_spk = $_GET['id_spk'];
	$mode = $_GET['mode'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="10%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="74%" style="text-align: center;">ITEM DESCRIPTION</th>
					<th rowspan="2" width="5%" style="text-align: center;">QTY</th>
					<th rowspan="2" width="6%" style="text-align: center;">UNIT</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>						
				</tr>
			</thead>';	
	$total = 0;		
	$SQL = "select t_spk_part.*, m_part.nama, m_part.kode, m_part.unit	
			from t_spk_part inner join m_part on t_spk_part.id_part = m_part.id_part
			where t_spk_part.id_spk = '$id_spk'  order by  t_spk_part.id_detil";
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;		
			$data .= '<tr>						
				<td style="text-align:center">'.$posisi.'.</td>
				<td style="text-align:center">'.$row['kode'].'</td>	
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['qty'].'</td>
				<td style="text-align:center">'.$row['unit'].'</td>';
				
				if($mode == 'Edit'){
				
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:DelData('.$row['id_detil'].')"  >
								<span class="fa fa-close " ></span>
							</button></td>';
				}else{
					$data .='<td></td>';
				}		
						
				$data .='</tr>';
    		$number++;
    	}
		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
	
			
    echo $data;		
	
	
}else if ($_POST['type'] == "Add_Data"){		
	if($_POST['id_spk'] != '' )
	{	
		$id_spk = $_POST['id_spk'];
		$id_part = $_POST['id_part'];
		$qty = $_POST['qty'];
		
		$sql = "INSERT INTO  t_spk_part (id_spk, id_part, qty)	 
					values
					('$id_spk', '$id_part', '$qty')";
		$hasil=mysqli_query($koneksi, $sql);
			
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Data Error...!";
	    }
		else
		{	
			$sql = "update m_part set keluar = keluar + '$qty' where id_part = '$id_part' ";
			$hasil=mysqli_query($koneksi, $sql);
			echo "Data saved!";
		}
	}	
	
}else if ($_POST['type'] == "Detil_Data"){
	$id = $_POST['id'];	
    $query = "select * from t_spk where id_spk  = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
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

}else if ($_POST['type'] == "Del_Data"){
	$id = $_POST['id']; 	
	
	$pq = mysqli_query($koneksi, "select * from t_spk_part where id_detil = '$id' ");
	$rq=mysqli_fetch_array($pq);	
	$id_part = $rq['id_part'];
	$qty = $rq['qty'];
	
	$sql = "update m_part set keluar = keluar - '$qty' where id_part = '$id_part' ";
	$hasil=mysqli_query($koneksi, $sql);
			
			
    $query = "DELETE FROM t_spk_part WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }

	
}

?>