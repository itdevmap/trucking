<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='23' ");
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
	
	if($field == 'No Polisi')
	{
		$f = 'm_mobil_tr.no_polisi';	
	}else if($field == 'No Seri'){
		$f = 't_ban.no_seri';		
	}else if($field == 'Jenis'){
		$f = 't_ban.jenis_ban';	
	}else if($field == 'Merk'){
		$f = 't_ban.merk_ban';		
	}else{
		$f = 't_ban.no_seri';	
	}
	
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL</th>
					<th rowspan="2" width="9%" style="text-align: center;">POSISI</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. POLISI</th>
					<th rowspan="2" width="7%" style="text-align: center;">NO. SERI</th>
					<th rowspan="2" width="7%" style="text-align: center;">JENIS</th>
					<th rowspan="2" width="14%" style="text-align: center;">MERK</th>
					<th rowspan="2" width="6%" style="text-align: center;">KETEBALAN</th>
					<th rowspan="2" width="6%" style="text-align: center;">KM</th>
					<th colspan="4" width="25%" style="text-align: center;">POSISI SAAT INI</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>
					<th rowspan="2" width="2%" style="text-align: center;">ADD ROTASI</th>
					<th rowspan="2" width="2%" style="text-align: center;">VIEW ROTASI</th>
				</tr>
				<tr>
					<th  width="6%" style="text-align: center;">POSISI</th>
					<th  width="7%" style="text-align: center;">NO. POLISI</th>
					<th  width="6%" style="text-align: center;">KETEBALAN</th>
					<th  width="6%" style="text-align: center;">KM</th>
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
	
	//$SQL = "select t_ban.*, m_mobil_tr.no_polisi
	//		from 
	//		t_ban left join m_mobil_tr on t_ban.id_mobil = m_mobil_tr.id_mobil
	//		where t_ban.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' 	
	//		order by t_ban.tanggal desc, m_mobil_tr.no_polisi	LIMIT $offset, $jmlperhalaman";
	
	$SQL = "select t_ban.*, m_mobil_tr.no_polisi
			from 
			t_ban left join m_mobil_tr on t_ban.id_mobil = m_mobil_tr.id_mobil
			where t_ban.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' 	
			order by t_ban.posisi	LIMIT $offset, $jmlperhalaman";
			
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
			$ketebalan = number_format($row['ketebalan'],2);
			$km = number_format($row['km'],0);
			
			$pq = mysqli_query($koneksi,"select t_ban_detil.*, m_mobil_tr.no_polisi
				from  t_ban_detil left join m_mobil_tr on t_ban_detil.id_mobil = m_mobil_tr.id_mobil 
				where t_ban_detil.id_ban = '$row[id_ban]' order  by t_ban_detil.tanggal desc, t_ban_detil.id_detil desc ");
			$rq=mysqli_fetch_array($pq);	
			$no_polisi = $rq['no_polisi'];
			$posisi_rotasi = $rq['posisi'];
			$ketebalan_rotasi = number_format($rq['ketebalan'],2);
			$km_rotasi = number_format($rq['km'],0);
			
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>
				<td style="text-align:center">'.$row['posisi'].'</td>
				<td style="text-align:center">'.$row['no_polisi'].'</td>
				<td style="text-align:center">'.$row['no_seri'].'</td>
				<td style="text-align:center">'.$row['jenis_ban'].'</td>
				<td style="text-align:center">'.$row['merk_ban'].'</td>
				<td style="text-align:center">'.$ketebalan.'</td>
				<td style="text-align:center">'.$km.'</td>
				<td style="text-align:center">'.$posisi_rotasi.'</td>
				<td style="text-align:center">'.$no_polisi.'</td>';

			if($rq['ketebalan'] <= 2 && $no_polisi != '')
			{
				$data .= '<td style="text-align:center;background:#e40f0f;color:#fff">'.$ketebalan_rotasi.'</td>';
			}else{
				$data .= '<td style="text-align:center">'.$ketebalan_rotasi.'</td>';
			}				
				
			$data .= '<td style="text-align:center">'.$km_rotasi.'</td>';					
				
				
				if($m_del == '1' && $no_polisi == '') 	
				{
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetBan('.$row['id_ban'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';
					$data .= '<td>
							<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:Delete('.$row['id_ban'].')"  >
								<span class="fa fa-close " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
					$data .='<td></td>';
				}
				
				
				$data .= '<td>
						<button class="btn btn-block btn-default" title="Add Payment"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetRotasi('.$row['id_ban'].')">
									<span class="fa  fa-plus-square" ></span>
						</button></td>';
						
				$data .='<td   style="text-align:center">
					<button class="btn btn-block btn-default" title="View Data"
						style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
						onClick="javascript:ListRotasi('.$row['id_ban'].')"  >
						<span class="glyphicon glyphicon-search" ></span>
					</button>	
				</td>';
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
				$pq = mysqli_query($koneksi, "select count(t_ban.id_ban) as jml
				from 
				t_ban left join m_mobil_tr on t_ban.id_mobil = m_mobil_tr.id_mobil
				where t_ban.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  ");					
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


	
}else if ($_POST['type'] == "Del_Ban"){
	$id = $_POST['id']; 	
	
    $query = "DELETE FROM t_ban WHERE id_ban = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }
	
}else if ($_POST['type'] == "Add_Ban"){		
	if($_POST['mode'] != '' )
	{	

		$id = $_POST['id'];
		$tanggal = ConverTglSql($_POST['tanggal']);
		$id_mobil = $_POST['id_mobil'];
		$no_seri = addslashes(trim(strtoupper($_POST['no_seri'])));
		$jenis_ban = $_POST['jenis_ban'];
		$merk_ban = addslashes(trim($_POST['merk_ban']));	
		$posisi_ban = addslashes(trim($_POST['posisi_ban']));
		$ketebalan_ban = $_POST['ketebalan_ban'];
		$ketebalan_ban = str_replace(",","", $ketebalan_ban);
		$km_ban = $_POST['km_ban'];
		$km_ban = str_replace(",","", $km_ban);
		$mode = $_POST['mode'];
		
		$tgl = date('Y-m-d H:i:s');
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO t_ban (tanggal, id_mobil, no_seri , jenis_ban, merk_ban, ketebalan, posisi,created, km) values
					('$tanggal', '$id_mobil', '$no_seri' , '$jenis_ban', '$merk_ban', '$ketebalan_ban', '$posisi_ban', '$id_user', '$km_ban')";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update t_ban set 
					tanggal = '$tanggal',
					id_mobil = '$id_mobil',
					no_seri = '$no_seri',
					jenis_ban = '$jenis_ban',
					merk_ban = '$merk_ban',
					ketebalan = '$ketebalan_ban',
					km = '$km_ban',
					posisi = '$posisi_ban'
					where 	id_ban = '$id'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Data Error...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
	
	
}else if ($_POST['type'] == "Detil_Ban"){
	$id = $_POST['id'];	
    $query = "select * from t_ban where id_ban  = '$id'";
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


}else if ($_POST['type'] == "Add_Rotasi"){		
	if($_POST['id_ban'] != '' )
	{	

		$id_ban = $_POST['id_ban'];
		$tanggal = ConverTglSql($_POST['tanggal']);
		$id_mobil = $_POST['id_mobil'];
		$jenis_pekerjaan = addslashes(trim(strtoupper($_POST['jenis_pekerjaan'])));
		$posisi = addslashes(trim(strtoupper($_POST['posisi'])));
		$ketebalan = $_POST['ketebalan'];
		$ketebalan = str_replace(",","", $ketebalan);
		$km = $_POST['km'];
		$km = str_replace(",","", $km);
		$ket = addslashes(trim(strtoupper($_POST['ket'])));
		
		$sql = "INSERT INTO t_ban_detil (id_ban, tanggal, id_mobil, jenis_pekerjaan, posisi, ketebalan, ket, created, km) values
					('$id_ban', '$tanggal', '$id_mobil', '$jenis_pekerjaan', '$posisi', '$ketebalan', '$ket', '$id_user', '$km')";
			$hasil=mysqli_query($koneksi, $sql);
			
			
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Data Error...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
	
}else if($_GET['type'] == "ListRotasi")
{
	$id = $_GET['id'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="9%" style="text-align: center;">TANGGAL</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. POLISI</th>
					<th rowspan="2" width="18%" style="text-align: center;">JENIS PEKERJAAN</th>		
					<th rowspan="2" width="12%" style="text-align: center;">POSISI</th>	
					<th rowspan="2" width="8%" style="text-align: center;">KETEBALAN</th>
					<th rowspan="2" width="8%" style="text-align: center;">KM</th>	
					<th rowspan="2" width="19%" style="text-align: center;">KETERANGAN</th>	
					<th rowspan="2" width="10%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="5%" style="text-align: center;">DEL</th>				
				</tr>
			</thead>';
	$t1="select t_ban_detil.*, m_mobil_tr.no_polisi
				from  t_ban_detil left join m_mobil_tr on t_ban_detil.id_mobil = m_mobil_tr.id_mobil 
				where t_ban_detil.id_ban = '$id' order  by t_ban_detil.tanggal asc, t_ban_detil.id_detil asc";
	$h1=mysqli_query($koneksi, $t1);   
	while ($d1=mysqli_fetch_array($h1))		
	{
		$tanggal = ConverTgl($d1['tanggal']);
		$ketebalan = number_format($d1['ketebalan'],2);
		$km = number_format($d1['km'],0);
		$total = $total + $amount;
		$n++;
		$data .= '<tr>							
			<td style="text-align:center">'.$n.'.</td>
			<td style="text-align:center">'.$tanggal.'</td>	
			<td style="text-align:center">'.$d1['no_polisi'].'</td>	
			<td style="text-align:left">'.$d1['jenis_pekerjaan'].'</td>	
			<td style="text-align:center">'.$d1['posisi'].'</td>	
			<td style="text-align:center">'.$ketebalan.'</td>
			<td style="text-align:center">'.$km.'</td>
			<td style="text-align:center">'.$d1['ket'].'</td>
			<td style="text-align:center">'.$d1['created'].'</td>	';	
		
			if($m_del == '1' ){
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Delete"
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:DelRotasi('.$d1['id_detil'].')"  >
		     		<span class="fa fa-close " ></span>
					</button></td>';
			}
			else
			{
				$data .='<td></td>';
			}				
		$data .='</tr>';
	}

    $data .= '</table>';
    echo $data;		
	
}else if ($_POST['type'] == "Del_Rotasi"){
	$id = $_POST['id']; 	
	
    $query = "DELETE FROM t_ban_detil WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }
	
}

?>