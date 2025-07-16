<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='25' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

if ($_GET['type'] == "Read")
{
	$cari1 = trim($_GET['cari1']);
	$cari2 = trim($_GET['cari2']);
	$field1 = trim($_GET['field1']);
	$field2 = trim($_GET['field2']);
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	
	if($field1 == 'Origin')
	{
		$f1 = 'm_kota_tr.nama_kota';
	}else if($field1 == 'Destination'){
		$f1 = 'm_kota_tr1.nama_kota';		
	}else if($field1 == 'Type'){
		$f1 = 'm_rate_tr.jenis_mobil';	
	}else{
		$f1 = 'm_rate_tr.jenis_mobil';	
	}
	
	if($field2 == 'Origin')
	{
		$f2 = 'm_kota_tr.nama_kota';
	}else if($field2 == 'Destination'){
		$f2 = 'm_kota_tr1.nama_kota';		
	}else if($field2 == 'Type'){
		$f2 = 'm_rate_tr.jenis_mobil';	
	}else{
		$f2 = 'm_rate_tr.jenis_mobil';	
	}
	
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="10%" style="text-align: center;">ORIGIN</th>
					<th rowspan="2" width="18%" style="text-align: center;">ORIGIN ADDRESS</th>
					<th rowspan="2" width="10%" style="text-align: center;">DESTINATION</th>
					<th rowspan="2" width="18%" style="text-align: center;">DESTINATION ADDRESS</th>
					<th rowspan="2" width="10%" style="text-align: center;">TYPE</th>
					<th rowspan="2" width="13%" style="text-align: center;">KM</th>
					<th rowspan="2" width="7%" style="text-align: center;">MAX PRICE</th>
					<th rowspan="2" width="7%" style="text-align: center;">MIN PRICE</th>
					<th rowspan="2" width="7%" style="text-align: center;">ROAD FEE</th>
					<th rowspan="2" width="7%" style="text-align: center;">RITASE</th>
					<th rowspan="2" width="7%" style="text-align: center;">PRICE TYPE</th>
					<th rowspan="2" width="10%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="7%" style="text-align: center;">STATUS</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>	
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

	$SQL = "select 
				m_rate_tr.*, 
				m_kota_tr.nama_kota as asal, 
				m_kota_tr1.nama_kota as tujuan 
         	from m_rate_tr 
			left join m_kota_tr on m_rate_tr.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota_tr1 on m_rate_tr.id_tujuan = m_kota_tr1.id_kota	
			where $f1 LIKE '%$cari1%' and $f2 LIKE '%$cari2%' 
			order by m_kota_tr.nama_kota asc, m_kota_tr1.nama_kota, m_rate_tr.jenis_mobil LIMIT $offset, $jmlperhalaman";	
	$query = mysqli_query($koneksi, $SQL);	

	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$max_price = number_format($row['max_price'],0);
			$min_price = number_format($row['min_price'],0);
			$ritase = number_format($row['ritase'],0);
			$uj = number_format($row['uj'],0);
			$posisi++;		
				$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$row['asal'].'</td>
				<td style="text-align:center">'.$row['origin_address'].'</td>
				<td style="text-align:center">'.$row['tujuan'].'</td>
				<td style="text-align:center">'.$row['destination_address'].'</td>
				<td style="text-align:center">'.$row['jenis_mobil'].'</td>
				<td style="text-align:center">'.($row['km'] ?? '-').'</td>
				<td style="text-align:right">'.$max_price.'</td>
				<td style="text-align:right">'.$min_price.'</td>
				<td style="text-align:right">'.$uj.'</td>
				<td style="text-align:right">'.$ritase.'</td>
				<td style="text-align:center">'.$row['price_type'].'</td>
				<td style="text-align:center">'.$row['created'].'</td>';					
				if($row['status'] =='0'){
				$data .= '<td style="text-align:center">
					<span class="label label-danger" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
						&nbsp;&nbsp;In Active&nbsp;&nbsp;
					</span>
					</td>';
				} else if($row['status'] =='1'){
				$data .= '<td style="text-align:center"> 
					<span class="label label-success" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
					&nbsp;&nbsp;&nbsp;&nbsp; Active &nbsp;&nbsp;&nbsp;
					</span>
					</td>';
				}
				if($m_edit == '1'  ){
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_rate'].')"  >
									<span class="fa fa-edit " ></span>
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
				$pq = mysqli_query($koneksi, "select count(m_rate_tr.id_rate) as jml 
				from 
				m_rate_tr left join m_kota_tr on m_rate_tr.id_asal = m_kota_tr.id_kota
				left join m_kota_tr as m_kota_tr1 on m_rate_tr.id_tujuan = m_kota_tr1.id_kota	
				where $f1 LIKE '%$cari1%' and $f2 LIKE '%$cari2%' ");					
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

}
// --------------------- ADD DATA  ---------------------
else if ($_POST['type'] == "Add_Data"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$id_asal = $_POST['id_asal'];
		$id_tujuan = $_POST['id_tujuan'];
		$jenis_mobil = $_POST['jenis_mobil'];
		$km = $_POST['km'];
		
		// $rate = $_POST['rate'];
		$max_price = $_POST['max_price'];
		$min_price = $_POST['min_price'];

		$uj = $_POST['uj'];
		$ritase = $_POST['ritase'];
		$stat = $_POST['stat'];
		$mode = $_POST['mode'];
		$price_type = $_POST['price_type'];

		$origin_address = $_POST['origin_address'];
		$origin_lat = $_POST['origin_lat'];
		$origin_lon = $_POST['origin_lon'];

		$destination_address = $_POST['destination_address'];
		$destination_lat = $_POST['destination_lat'];
		$destination_lon = $_POST['destination_lon'];
		
		// $rate = str_replace(",","", $rate);
		$max_price = str_replace(",","", $max_price);
		$min_price = str_replace(",","", $min_price);

		$uj = str_replace(",","", $uj);
		$ritase = str_replace(",","", $ritase);
		
		if ($mode == 'Add') {

			$cek_sql = "
				SELECT id_rate FROM m_rate_tr 
				WHERE id_asal = '$id_asal' 
				AND id_tujuan = '$id_tujuan' 
				AND jenis_mobil = '$jenis_mobil' 
				AND price_type = '$price_type'
				LIMIT 1
			";

			$cek = mysqli_query($koneksi, $cek_sql);

			if (!$cek) {
				echo "QUERY_FAILED: " . mysqli_error($koneksi);
				exit;
			}
			if (mysqli_num_rows($cek) > 0) {
				echo "DATA_FOUND";
				exit;
			}

			$sql = "INSERT INTO m_rate_tr (
				id_asal, 
				id_tujuan, 
				jenis_mobil, 
				origin_address, 
				origin_lon, 
				origin_lat, 
				destination_address, 
				destination_lon, 
				destination_lat, 
				km, 
				max_price,	 
				min_price,	 
				uj, 
				ritase, 
				price_type, 
				status, 
				created
			) 
			VALUES (
				'$id_asal', 
				'$id_tujuan', 
				'$jenis_mobil', 
				'$origin_address', 
				'$origin_lon', 
				'$origin_lat',
				'$destination_address', 
				'$destination_lon', 
				'$destination_lat', 
				'$km', 
				'$max_price', 
				'$min_price', 
				'$uj',
				'$ritase', 
				'$price_type', 
				'1', 
				'$id_user'
			)";

			$hasil = mysqli_query($koneksi, $sql);

			if ($hasil) {
				echo "INSERT_SUCCESS";
			} else {
				echo "INSERT_FAILED: " . mysqli_error($koneksi);
			}
			exit;
		}
		else
		{
			$sql = "update m_rate_tr set 
				id_asal = '$id_asal',
				id_tujuan = '$id_tujuan',
				jenis_mobil = '$jenis_mobil',
				origin_address = '$origin_address',
				origin_lon = '$origin_lon',
				origin_lat = '$origin_lat',
				destination_address = '$destination_address',
				destination_lon = '$destination_lon',
				destination_lat = '$destination_lat',
				km = '$km',
				max_price = '$max_price',
				min_price = '$min_price',
				uj = '$uj',
				ritase = '$ritase',
				price_type = '$price_type',
				status = '$stat',
				created = '$id_user'
				where id_rate = '$id'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {	
			echo "Price has found...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
}else if ($_POST['type'] == "Detil_Data"){
	$id = $_POST['id'];	
    $query = "select * from m_rate_tr where id_rate  = '$id'";
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


}else if ($_GET['type'] == "ListCost")
{	
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="6%" style="text-align: center;">NO</th>
					<th width="87%" style="text-align: center;">COST NAME</th>
					<th width="7%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "select * from m_kota_tr where nama_kota LIKE '%$cari%' and status = '1'  order by nama_kota LIMIT 0, 25";
	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$n++;
			$data .= '<tr>';
			$data .= '<td style="text-align:center">'.$n.'.</td>';	
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihCost('.$row['id_kota'].')" >'.$row['nama_kota'].'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihCost('.$row['id_kota'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px">Add</button>
					</td>';		
			$data .='</tr>';
    	}		
    }
    else
    {
    	$data .= '<tr><td colspan="7"></td></tr>';
    }
    $data .= '</table>';
    echo $data;		
	
	
	
}else if ($_GET['type'] == "Read_LCL")
{
	$cari1 = trim($_GET['cari1']);
	$cari2 = trim($_GET['cari2']);
	$field1 = trim($_GET['field1']);
	$field2 = trim($_GET['field2']);
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	
	if($field1 == 'Asal')
	{
		$f1 = 'm_kota_tr.nama_kota';
	}else if($field1 == 'Tujuan'){
		$f1 = 'm_kota_tr1.nama_kota';		
	}else if($field1 == 'Jenis'){
		$f1 = 'm_rate_tr.jenis_mobil';	
	}else{
		$f1 = 'm_rate_tr.jenis_mobil';	
	}
	
	if($field2 == 'Asal')
	{
		$f2 = 'm_kota_tr.nama_kota';
	}else if($field2 == 'Tujuan'){
		$f2 = 'm_kota_tr1.nama_kota';		
	}else if($field2 == 'Jenis'){
		$f2 = 'm_rate_tr.jenis_mobil';	
	}else{
		$f2 = 'm_rate_tr.jenis_mobil';	
	}
	
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="29%" style="text-align: center;">ASAL</th>
					<th rowspan="2" width="29%" style="text-align: center;">TUJUAN</th>
					<th colspan="2" width="20%" style="text-align: center;">HARGA</th>
					<th rowspan="2" width="8%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="8%" style="text-align: center;">STATUS</th>
					<th rowspan="2" width="3%" style="text-align: center;">EDIT</th>	
				</tr>
				<tr>					
					<th  width="10%" style="text-align: center;">KG</th>
					<th  width="10%" style="text-align: center;">VOL</th>
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
	$SQL = "select m_rate_tr_lcl.*, m_kota_tr.nama_kota as asal, m_kota_tr1.nama_kota as tujuan 
         	from 
			m_rate_tr_lcl left join m_kota_tr on m_rate_tr_lcl.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota_tr1 on m_rate_tr_lcl.id_tujuan = m_kota_tr1.id_kota	
			where $f1 LIKE '%$cari1%' and $f2 LIKE '%$cari2%' 
			order by m_kota_tr.nama_kota asc, m_kota_tr1.nama_kota LIMIT $offset, $jmlperhalaman";	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$kg = number_format($row['kg'],0);
			$vol = number_format($row['vol'],0);
			$posisi++;		
				$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$row['asal'].'</td>
				<td style="text-align:center">'.$row['tujuan'].'</td>		
				<td style="text-align:right">'.$kg.'</td>
				<td style="text-align:right">'.$vol.'</td>
				<td style="text-align:center">'.$row['created'].'</td>';					
				if($row['status'] =='0'){
				$data .= '<td style="text-align:center">
					<span class="label label-danger" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
						&nbsp;&nbsp;In Active&nbsp;&nbsp;
					</span>
					</td>';
				} else if($row['status'] =='1'){
				$data .= '<td style="text-align:center"> 
					<span class="label label-success" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
					&nbsp;&nbsp;&nbsp;&nbsp; Active &nbsp;&nbsp;&nbsp;
					</span>
					</td>';
				}
				if($m_edit == '1'  ){
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_rate'].')"  >
									<span class="fa fa-edit " ></span>
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
				$pq = mysqli_query($koneksi, "select count(m_rate_tr.id_rate) as jml 
				from 
				m_rate_tr left join m_kota_tr on m_rate_tr.id_asal = m_kota_tr.id_kota
				left join m_kota_tr as m_kota_tr1 on m_rate_tr.id_tujuan = m_kota_tr1.id_kota	
				where $f1 LIKE '%$cari1%' and $f2 LIKE '%$cari2%' ");					
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

}else if ($_POST['type'] == "Add_Data_LCL"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$id_asal = $_POST['id_asal'];
		$id_tujuan = $_POST['id_tujuan'];
		$kg = $_POST['kg'];
		$vol = $_POST['vol'];
		$stat = $_POST['stat'];
		$mode = $_POST['mode'];
		
		$kg = str_replace(",","", $kg);
		$vol = str_replace(",","", $vol);
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO  m_rate_tr_lcl (id_asal, id_tujuan, kg, vol, status, created) values
					('$id_asal', '$id_tujuan' , '$kg' , '$vol' , '1', '$id_user')";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update m_rate_tr_lcl set 
					id_asal = '$id_asal',
					id_tujuan = '$id_tujuan',
					kg = '$kg',
					vol = '$vol',
					status = '$stat',
					created = '$id_user'
					where id_rate = '$id'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        			
			//exit(mysqli_error($koneksi));
			echo "Rate has found...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
	
}else if ($_POST['type'] == "Detil_Data_LCL"){
	$id = $_POST['id'];	
    $query = "select * from m_rate_tr_lcl where id_rate  = '$id'";
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