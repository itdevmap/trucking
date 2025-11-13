<?php
	session_start(); 
	include "../session_log.php"; 
	include("../koneksi.php");
	include "../lib.php";

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	include("../PHPMailer/src/Exception.php"); 
	include("../PHPMailer/src/PHPMailer.php"); 
	include("../PHPMailer/src/SMTP.php");

	$pq = mysqli_query($koneksi, "SELECT * FROM m_role_akses_tr WHERE id_role = '$id_role' AND id_menu = '7' ");
	$rq=mysqli_fetch_array($pq);	
	$m_edit = $rq['m_edit'];
	$m_add = $rq['m_add'];
	$m_del = $rq['m_del'];
	$m_view = $rq['m_view'];
	$m_exe = $rq['m_exe'];

	if ($_GET['type'] == "Read") {
		$cari = trim($_GET['cari']);
		$hal = $_GET['hal'];
		$paging = $_GET['paging'];
		$tgl1 = $_GET['tgl1'];
		$tgl2 = $_GET['tgl2'];
		$tgl1x = ConverTglSql($tgl1);
		$tgl2x = ConverTglSql($tgl2);	
		$field = $_GET['field'];
		$stat = $_GET['stat'];
		$field1 = $_GET['field1'];
		$cari1 = trim($_GET['cari1']);

		if($field == 'Quo No')
		{
			$f = 'tr_quo.quo_no';	
		}else if($field == 'Customer'){
			$f = 'm_cust_tr.nama_cust';	
		}else if($field == 'Origin'){
			$f = 'm_kota_tr.nama_kota';
		}else if($field == 'Destination'){
			$f = 'm_kota1.nama_kota';	
		}else if($field == 'Type'){
			$f = 'tr_quo_data.jenis_mobil';	
		}else{
			$f = 't_jo_tr.no_jo';
		}
		
		if($field1 == 'Quo No')
		{
			$f1 = 'tr_quo.quo_no';	
		}else if($field1 == 'Customer'){
			$f1 = 'm_cust_tr.nama_cust';	
		}else if($field1 == 'Origin'){
			$f1 = 'm_kota_tr.nama_kota';
		}else if($field1 == 'Destination'){
			$f1 = 'm_kota1.nama_kota';	
		}else if($field1 == 'Type'){
			$f1 = 'tr_quo_data.jenis_mobil';	
		}else{
			$f1 = 't_jo_tr.no_jo';
		}
		
		if($stat == 'In Progress')
		{
			$stat = '0';
		}
		else if($stat == 'Executed')
		{
			$stat = '1';
		}
		
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>					
						<th rowspan="2" width="3%" style="text-align: center;">NO</th>
						<th rowspan="2" width="7%" style="text-align: center;">DATE</th>	
						<th rowspan="2" width="8%" style="text-align: center;">#QUO NO</th>
						<th rowspan="2" width="22%" style="text-align: center;">CUSTOMER</th>					
						<th rowspan="2" width="11%" style="text-align: center;">ORIGIN</th>
						<th rowspan="2" width="11%" style="text-align: center;">DESTINATION</th>
						<th rowspan="2" width="6%" style="text-align: center;">TYPE</th>
						<th rowspan="2" width="6%" style="text-align: center;">COST</th>
						<th rowspan="2" width="6%" style="text-align: center;">SALES</th>
						<th rowspan="2" width="6%" style="text-align: center;">CREATED</th>
						<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>
						<th colspan="4" width="8%" style="text-align: center;">ACTION</th>	
					</tr>
					<tr>
						<th width="2%" style="text-align: center;">EDIT</th>
						<th width="2%" style="text-align: center;">DEL</th>	
						<th width="2%" style="text-align: center;">EXEC</th>						
						<th width="2%" style="text-align: center;">ADD ORDER</th>						
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
		
		if($stat == 'All')
		{
			$SQL = "SELECT 
					tr_quo.*, 
					tr_quo_data.harga, 
					tr_quo_data.jenis_mobil, 
					tr_quo_data.id_detil, 
					m_kota_tr.nama_kota AS asal, 
					m_kota1.nama_kota AS tujuan,
					 m_cust_tr.nama_cust
				FROM tr_quo 
				LEFT JOIN tr_quo_data ON tr_quo.id_quo = tr_quo_data.id_quo
				LEFT JOIN m_cust_tr ON tr_quo.id_cust = m_cust_tr.id_cust
				LEFT JOIN m_kota_tr ON tr_quo_data.id_asal = m_kota_tr.id_kota
				LEFT JOIN m_kota_tr AS m_kota1 ON tr_quo_data.id_tujuan = m_kota1.id_kota
				WHERE tr_quo.quo_date BETWEEN '$tgl1x' AND '$tgl2x'
					AND $f LIKE '%$cari%' 
					AND $f1 LIKE '%$cari1%'
				ORDER BY tr_quo.quo_date DESC, tr_quo.quo_no DESC
				LIMIT $offset, $jmlperhalaman";
		}else{
			
			$SQL = "SELECT 
					tr_quo.*, 
					tr_quo_data.harga, 
					tr_quo_data.jenis_mobil, 
					tr_quo_data.id_detil,
					m_kota_tr.nama_kota AS asal, 
					m_kota1.nama_kota AS tujuan, 
					m_cust_tr.nama_cust
				FROM tr_quo 
				LEFT JOIN tr_quo_data ON tr_quo.id_quo = tr_quo_data.id_quo
				LEFT JOIN m_cust_tr ON tr_quo.id_cust = m_cust_tr.id_cust
				LEFT JOIN m_kota_tr ON tr_quo_data.id_asal = m_kota_tr.id_kota
				LEFT JOIN m_kota_tr AS m_kota1 ON tr_quo_data.id_tujuan = m_kota1.id_kota
				WHERE tr_quo.quo_date BETWEEN '$tgl1x' AND '$tgl2x'
					AND $f LIKE '%$cari%' 
					AND $f1 LIKE '%$cari1%' 
					AND tr_quo.status = '$stat'
				ORDER BY tr_quo.quo_date DESC, tr_quo.quo_no DESC
				LIMIT $offset, $jmlperhalaman";
		}
				
		$query = mysqli_query($koneksi, $SQL);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}
		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result))
			{	
				$tanggal = ConverTgl($row['quo_date']);
				$biaya = number_format($row['harga'],0);
				$posisi++;
				$xy1="View|$row[id_quo]";
				$xy1=base64_encode($xy1);
				$link = "quo_data.php?id=$xy1";

				if($row['status'] == '0'){
					$label = 'danger';
					$status = 'In Progress';
				}
				else if($row['status'] == '1'){
					$label = 'success';
					$status = 'Executed';
				} 
				else if($row['status'] == '2'){
					$label = 'warning';
					$status = 'Draft';
				} 

				$xy1="$row[id_sj]";
				$xy1=base64_encode($xy1);
				$link_sj = "cetak_sj_fcl.php?id=$xy1";
				$data .= '<tr>							
					<td style="text-align:center">'.$posisi.'.</td>	
					<td style="text-align:center">'.$tanggal.'</td>
					<td style="text-align:center"><a href="'.$link.'"  title="View">'.$row['quo_no'].'</a></td>
					<td style="text-align:left">'.$row['nama_cust'].'</b></td>	
					<td style="text-align:center">'.$row['asal'].'</td>
					<td style="text-align:center">'.$row['tujuan'].'</td>
					<td style="text-align:center">'.$row['jenis_mobil'].'</td>
					<td style="text-align:right">'.$biaya.'</td>
					<td style="text-align:center">'.$row['sales'].'</td>
					<td style="text-align:center">'.$row['created'].'</td>
					<td style="text-align:center">
						<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
					</td>';
				
					if($m_edit == '1' && $row['status'] != '1' ) {
						$xy1="Edit|$row[id_quo]";
						$xy1=base64_encode($xy1);
						$link = "'quo_data.php?id=$xy1'";
						$data .= '<td>
									<button class="btn btn-block btn-default" title="Edit"
										style="margin:-3px;border-radius:0px" type="button" 
										onClick="window.location.href = '.$link.' "  >
										<span class="fa fa-edit " ></span>
									</button></td>';
					} else {					
						$data .='<td></td>';
					}
					
					if($m_del == '1' && $row['status'] == '0') 	
					{
						if(empty($row['id_detil']))
						{
							$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:DelQuo('.$row['id_quo'].')"  >
									<span class="fa fa-close " ></span>
									</button></td>';
						}else{
							$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:DelData('.$row['id_detil'].')"  >
									<span class="fa fa-close " ></span>
									</button></td>';
						}
					}
					else
					{
						if(empty($row['id_detil']))
						{
							$data .= '<td>
									<button class="btn btn-block btn-default"  title="Delete"
										style="margin:-3px;border-radius:0px" type="button" 
										onClick="javascript:DelOrder('.$row['id_jo'].')"  >
										<span class="fa fa-close " ></span>
									</button></td>';
						}else{
							$data .='<td></td>';
						}
					}
					
					if($row['status'] == '0' && !empty($row['id_detil']) ) {
						$data .= '<td>
								<button class="btn btn-block btn-default"  title="Execute"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:Confirm('.$row['id_quo'].')"  >
									<span class="fa fa-check-square-o " ></span>
								</button></td>';
							
					}
					else
					{
						$data .='<td></td>';
					}
					
					if($row['status'] == '1' )
					{
						$xy1	= "Add|$row[id_quo]";
						$xy1	=base64_encode($xy1);
						$link 	= "'so_data.php?id=$xy1'";
			
						$data  .= '<td>
									<button class="btn btn-block btn-default"
										style="margin:-3px;border-radius:0px" type="button" 									
										onClick="window.open('.$link.') ">
										<span class="fa fa-plus-square"></span>
										</button>
								</td>';
					}else{
						$data .='<td></td>';
					}	
					
					$data .='</tr>';
				$number++;
			}		
		}
		else {
			$data .= '<tr><td colspan="7">Records not found!</td></tr>';
		}
		$data .= '</table>';
		
		$data .= '<div class="paginate paginate-dark wrapper">
					<ul>';
					
					if($stat == 'All')
					{
						$pq = mysqli_query($koneksi, "select count(tr_quo_data.id_detil) as jml
						from 
						tr_quo left join tr_quo_data on tr_quo.id_quo = tr_quo_data.id_quo
						left join m_cust_tr on tr_quo.id_cust = m_cust_tr.id_cust
						left join m_kota_tr on tr_quo_data.id_asal = m_kota_tr.id_kota
						left join m_kota_tr as m_kota1 on tr_quo_data.id_tujuan = m_kota1.id_kota
						where tr_quo.quo_date between '$tgl1x' and '$tgl2x'   and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' ");
					}else{
						$pq = mysqli_query($koneksi, "select count(tr_quo_data.id_detil) as jml
						from 
						tr_quo left join tr_quo_data on tr_quo.id_quo = tr_quo_data.id_quo
						left join m_cust_tr on tr_quo.id_cust = m_cust_tr.id_cust
						left join m_kota_tr on tr_quo_data.id_asal = m_kota_tr.id_kota
						left join m_kota_tr as m_kota1 on tr_quo_data.id_tujuan = m_kota1.id_kota
						where tr_quo.quo_date between '$tgl1x' and '$tgl2x'   and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and tr_quo.status = '$stat'");
					}
										
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
	else if ($_POST['type'] == "Executed") {
		if($_POST['id'] != '' )
		{	
			$id = $_POST['id'];
			$sql = "update tr_quo set 
					status = '1'
					where id_quo = '$id'	";
				$hasil=mysqli_query($koneksi, $sql);
			if (!$hasil) {
							
				exit(mysqli_error($koneksi));
				echo "Data error...!";
			}
			else
			{	
		
				echo "Data Executed!";
			}
		}	
		
	}
	else if ($_POST['type'] == "Del_Quo") {
		$id = $_POST['id']; 	
		
		$query = "DELETE FROM tr_quo WHERE id_quo = '$id' ";
		if (!$result = mysqli_query($koneksi, $query)) {
			exit(mysqli_error($koneksi));
		}	

	}
	else if($_GET['type'] == "Read_Detil") {
		$id_quo = $_GET['id_quo'];
		$mode = $_GET['mode'];
		
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>	
						<th rowspan="2" width="3%" style="text-align: center;">NO</th>					
						<th rowspan="2" width="31%" style="text-align: center;">ORIGIN</th>
						<th rowspan="2" width="31%" style="text-align: center;">DESTINATION</th>
						<th rowspan="2" width="20%" style="text-align: center;">TYPE</th>
						<th rowspan="2" width="9%" style="text-align: center;">DELIV. COST</th>
						<th colspan="2" width="6%" style="text-align: center;">ACTION</th>						
					</tr>
					<tr>
						<th width="3%" style="text-align: center;">EDIT</th>
						<th width="3%" style="text-align: center;">DEL</th>
					</tr>	
				</thead>';	
		$SQL = "select tr_quo_data.*, m_kota_tr.nama_kota as asal, m_kota1.nama_kota as tujuan
			from 
			tr_quo_data left join m_kota_tr on tr_quo_data.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota1 on tr_quo_data.id_tujuan = m_kota1.id_kota
			where tr_quo_data.id_quo = '$id_quo' order by  tr_quo_data.id_detil";
		$query = mysqli_query($koneksi, $SQL);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}
		if(mysqli_num_rows($result) > 0)
		{
			$idr = 0;
			$usd =0;
			while($row = mysqli_fetch_assoc($result))
			{	
				$posisi++;	
				
				$harga = number_format($row['harga'],0);	
				$data .= '<tr>						
					<td style="text-align:center">'.$posisi.'.</td>
					
					<td style="text-align:center">'.$row['asal'].'</td>	
					<td style="text-align:center">'.$row['tujuan'].'</td>
					<td style="text-align:center">'.$row['jenis_mobil'].'</td>
					<td style="text-align:right">'.$harga.'</td>';
					
					if($mode == 'Edit' ){
						$data .= '<td>
									<button class="btn btn-block btn-default"  title="Edit"
										style="margin:-3px;border-radius:0px" type="button" 
										onClick="javascript:GetData('.$row['id_detil'].')"  >
										<span class="fa fa-edit " ></span>
									</button></td>';
						$data .= '<td>
									<button class="btn btn-block btn-default"  title="Delete"
										style="margin:-3px;border-radius:0px" type="button" 
										onClick="javascript:DelDetil('.$row['id_detil'].')"  >
										<span class="fa fa-close " ></span>
									</button></td>';			
					}
					else
					{
						$data .='<td></td>';
						$data .='<td></td>';
					}
					
					$data .='</tr>';
				$number++;
			}		
			/*
			$totalx = number_format($total,0);
				$data .= '<td colspan="7"></td>';
				$data .= '<td colspan="2" style="text-align:right;background:#eee;color:#000"><b>Total  :</b></td>	
							<td style="text-align:right;background:#008d4c;color:#fff"><b>'.$totalx.'</b></td>';
				$data .= '<td colspan="2"></td>';			
				$data .= '</tr>';
			*/	
			
		}
		else
		{
			$data .= '<tr><td colspan="7">Records not found!</td></tr>';
		}
		
		
		$data .= '</table>';
		
		echo $data;	
		
	}
	else if ($_POST['type'] == "Del_Detil") {
		$id = $_POST['id']; 

		$query = "DELETE FROM tr_quo_data WHERE id_detil = '$id' ";
		if (!$result = mysqli_query($koneksi, $query)) {
			exit(mysqli_error($koneksi));
		}	
		
	}
	else if ($_POST['type'] == "Detil_Data") {
		$id = $_POST['id'];	
		$query = "select tr_quo_data.*, 
				m_kota_tr.nama_kota as asal, 
				m_kota1.nama_kota as tujuan, 
				tr_quo.id_cust
			FROM tr_quo_data 
				left join m_kota_tr on tr_quo_data.id_asal = m_kota_tr.id_kota
				left join m_kota_tr as m_kota1 on tr_quo_data.id_tujuan = m_kota1.id_kota
				left join tr_quo on tr_quo_data.id_quo = tr_quo.id_quo
			where tr_quo_data.id_detil  = '$id'";

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

	if ($_POST['type'] == "Add_Detil") {
		if ($_POST['mode'] != '' ) {	
			$id 		 = $_POST['id'];
			$id_quo 	 = $_POST['id_quo'];
			$mode 		 = $_POST['mode'];
			$id_asal 	 = $_POST['id_asal'];
			$id_tujuan 	 = $_POST['id_tujuan'];
			$jenis 		 = $_POST['jenis'];
			$biaya_kirim = str_replace(",","", $_POST['biaya_kirim']);
			$penerima 	 = addslashes(trim(strtoupper($_POST['penerima'])));
			$no_cont 	 = addslashes(trim(strtoupper($_POST['no_cont'])));
			$sts 		 = $_POST['sts'];

			$origin_address = $_POST['origin_address'];
			$origin_lat 	= $_POST['origin_lat'];
			$origin_lon 	= $_POST['origin_lon'];

			$destination_address = $_POST['destination_address'];
			$destination_lat 	  = $_POST['destination_lat'];
			$destination_lon 	  = $_POST['destination_lon'];

			$distance 	 = $_POST['distance'];
			$km 		 = $_POST['km'];
			$price_type  = $_POST['price_type'];

			if ($mode == 'Add') {
				$sql_insert = "INSERT INTO tr_quo_data 
						(id_quo, id_asal, id_tujuan, jenis_mobil, harga, origin_address, origin_lon, origin_lat, destination_address, destination_lon, destination_lat, distance, price_type) 
					VALUES
						('$id_quo', '$id_asal', '$id_tujuan', '$jenis', '$biaya_kirim', '$origin_address','$origin_lon','$origin_lat','$destination_address','$destination_lon','$destination_lat', '$distance', '$price_type')";
				
				$result_insert 	= mysqli_query($koneksi, $sql_insert);
				$last_id 		= mysqli_insert_id($koneksi);

				$sql_update = "UPDATE tr_quo SET `status` = '$sts' WHERE id_quo = '$id_quo'";
				mysqli_query($koneksi, $sql_update);

				

				$sql_quo = "SELECT 
						tr_quo.quo_no,
						tr_quo.quo_date,
						tr_quo.created,
						tr_quo_data.harga,
						m_rate_tr.max_price,
						m_rate_tr.min_price,
						CONCAT(rute_asal.nama_kota, ' - ', rute_tujuan.nama_kota) AS rute,
						CONCAT(m_rate_tr.origin_address, ' - ', m_rate_tr.destination_address) AS alamat,
						m_rate_tr.origin_address,
						m_rate_tr.destination_address,
						tr_quo_data.jenis_mobil,
						tr_quo_data.price_type,
						m_cust_tr.nama_cust
					FROM tr_quo 
					LEFT JOIN tr_quo_data 
						ON tr_quo_data.id_quo = tr_quo.id_quo
					LEFT JOIN m_rate_tr 
						ON m_rate_tr.id_asal = tr_quo_data.id_asal 
						AND m_rate_tr.id_tujuan = tr_quo_data.id_tujuan
						AND m_rate_tr.jenis_mobil = tr_quo_data.jenis_mobil
						AND m_rate_tr.price_type = tr_quo_data.price_type
					LEFT JOIN m_kota_tr AS rute_asal 
						ON rute_asal.id_kota = tr_quo_data.id_asal 
					LEFT JOIN m_kota_tr AS rute_tujuan 
						ON rute_tujuan.id_kota = tr_quo_data.id_tujuan 
					LEFT JOIN m_cust_tr 
						ON m_cust_tr.id_cust = tr_quo.id_cust
					WHERE tr_quo.id_quo = '$id_quo'
				";

				$query_data = mysqli_query($koneksi, $sql_quo);
				$data_quo = mysqli_fetch_assoc($query_data);

				// Ambil semua rate terkait
				$sql_rate = "SELECT 
						m_rate_tr.max_price,
						m_rate_tr.min_price,
						m_rate_tr.price_type
					FROM tr_quo_data
					LEFT JOIN tr_quo 
						ON tr_quo.id_quo = tr_quo_data.id_quo
					LEFT JOIN m_rate_tr 
						ON m_rate_tr.id_tujuan = tr_quo_data.id_tujuan 
						AND m_rate_tr.id_asal = tr_quo_data.id_asal
					WHERE tr_quo.id_quo = '$id_quo'
				";
				$query_rate = mysqli_query($koneksi, $sql_rate);

				// Ambil data utama
				$quo_code   = $data_quo['quo_no'] ?? '';
				$quo_date   = $data_quo['quo_date'] ?? '';
				$harga      = $data_quo['harga'] ?? 0;
				$max_price  = $data_quo['max_price'] ?? 0;
				$min_price  = $data_quo['min_price'] ?? 0;
				$jenis_mobil= $data_quo['jenis_mobil'] ?? '';
				$rute       = $data_quo['rute'] ?? '';
				$nama_cust  = $data_quo['nama_cust'] ?? '';
				$created  	= $data_quo['created'] ?? '';
				$alamat  	= $data_quo['alamat'] ?? '';
				$price_type = $data_quo['price_type'] ?? '';

				if ($sts === 2 || $sts === "2") {
					$mail = new PHPMailer(true);
					try {
						$mail->isSMTP();
						$mail->Host       = 'smtp.gmail.com';
						$mail->SMTPAuth   = true;
						$mail->Username   = 'itdivision.map@gmail.com';
						$mail->Password   = 'glpykeqqsaulnhxd'; 
						$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
						$mail->Port       = 587;

						$mail->setFrom('itdivision.map@gmail.com', 'noreply@planetexpress.co.id');
						// $mail->addAddress('itdev2.staff.map@gmail.com');
						$mail->addAddress('itinfra.coord.map@gmail.com');

						$mail->isHTML(true);
						$mail->Subject = "Approval harga di bawah Price List trucking PETJ";

						// Siapkan baris harga rate
						$rate_rows = '';
						while ($rate = mysqli_fetch_assoc($query_rate)) {
							$rate_rows .= '
								<tr>
									<td><b>Range Harga (' . htmlspecialchars($rate['price_type']) . ')</b></td>
									<td>: Rp ' . number_format($rate['min_price'], 0, ",", ".") . ' - Rp ' . number_format($rate['max_price'], 0, ",", ".") . '</td>
								</tr>
							';
						}

						$mail->Body = '
							<table cellspacing="0" cellpadding="4" style="font-family:Arial, sans-serif; font-size:13px;">
								<tr>
									<td><b>No Quotation</b></td>
									<td>: ' . htmlspecialchars($quo_code) . '</td>
								</tr>
								<tr>
									<td><b>Tanggal Kebutuhan</b></td>
									<td>: ' . htmlspecialchars($quo_date) . '</td>
								</tr>
								<tr>
									<td><b>Yang Mengajukan</b></td>
									<td>: ' . htmlspecialchars($created) . '</td>
								</tr>
								<tr>
									<td><b>Tujuan Approval</b></td>
									<td>: Approval harga di bawah  Price List trucking PETJ</td>
								</tr>
								<tr>
									<td><b>Nama Customer</b></td>
									<td>: ' . htmlspecialchars($nama_cust) . '</td>
								</tr>
								<tr>
									<td><b>Jenis Container</b></td>
									<td>: ' . htmlspecialchars($jenis_mobil) . '</td>
								</tr>
								<tr>
									<td><b>Rute Pengiriman</b></td>
									<td>: ' . htmlspecialchars($rute) . '</td>
								</tr>
								<tr>
									<td><b>Alamat Pengiriman</b></td>
									<td>: ' . htmlspecialchars($alamat) . '</td>
								</tr>
								' . $rate_rows . '
								<tr>
									<td style="color:red;"><b>Harga Pengajuan ('. $price_type .')</b></td>
									<td style="color:red;"><b>: Rp ' . number_format($harga, 0, ",", ".") . '</b></td>
								</tr>
							</table>
							<br><br>
							<a href="http://http://192.168.1.210:8089/tr-dummy/quo_approve.php?no_doc=' . $quo_code . '" 
								style="display:inline-block; padding:10px 16px; background-color:#28a745; color:#fff; text-decoration:none; border-radius:4px; font-weight:bold;">
								Approve Quo
							</a>
							&nbsp;&nbsp;
							<a href="http://http://192.168.1.210:8089/tr-dummy/quo_reject.php" 
								style="display:inline-block; padding:10px 16px; background-color:#dc3545; color:#fff; text-decoration:none; border-radius:4px; font-weight:bold;">
								Reject Quo
							</a>
						';

						if ($mail->send()) {
							echo "✅ Email terkirim<br>";
						} else {
							echo "❌ Email gagal: " . $mail->ErrorInfo . "<br>";
						}

					} catch (Exception $e) {
						echo "❌ Gagal mengirim email: {$mail->ErrorInfo}";
					}
				}

			} else {
				$sql_update_detail = "UPDATE tr_quo_data SET 
						id_asal = '$id_asal',
						id_tujuan = '$id_tujuan',
						jenis_mobil = '$jenis',
						origin_address = '$origin_address',
						origin_lon = '$origin_lon',
						origin_lat = '$origin_lat',
						destination_address = '$destination_address',
						destination_lon = '$destination_lon',
						destination_lat = '$destination_lat',
						distance = '$distance',
						harga = '$biaya_kirim'
					WHERE id_detil = '$id'";

				$hasil = mysqli_query($koneksi, $sql_update_detail);

				if (!$hasil) {
					echo "Data Error...!";
				} else {	
					echo "Data saved!";
				}
			}
		}
	}
	
	else if ($_POST['type'] == "Cek_Rate_Cust") {
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		$id_asal = $_POST['id_asal'];	
		$id_cust = $_POST['id_cust'];	
		$id_tujuan = $_POST['id_tujuan'];	
		$jenis_mobil = $_POST['jenis_mobil'];	

		$query = "SELECT 
					m_rate_tr.*
				FROM m_rate_tr
				WHERE id_asal = '$id_asal' 
					AND id_tujuan = '$id_tujuan' 
					AND jenis_mobil = '$jenis_mobil'";

		// echo $query;
		// return;

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
		}
		echo json_encode($response);	
		
	}
	else if ($_POST['type'] == "Cek_Rate") {

		$id_asal = $_POST['id_asal'];	
		$id_tujuan = $_POST['id_tujuan'];	
		$jenis_mobil = $_POST['jenis_mobil'];	
		$price_type = !empty($_POST['price_type']) ? $_POST['price_type'] : 'high';
		
		$query = "SELECT * 
				FROM m_rate_tr 
				WHERE id_asal  = '$id_asal' 
					AND id_tujuan = '$id_tujuan' 
					AND jenis_mobil = '$jenis_mobil' 
					AND price_type = '$price_type'";

		if (!$result = mysqli_query($koneksi, $query)) {
			exit(mysqli_error($koneksi));
		}

		if(mysqli_num_rows($result) > 0) {
			$response = mysqli_fetch_assoc($result);
			$response['status'] = 200;
		} else {
			$response = [
				"status" => 404,
				"km" => 0,
				"max_price" => 0,
				"min_price" => 0
			];
		}

		echo json_encode($response);
		exit;
	}

	else if ($_POST['type'] == "AddOrder") {
		if($_POST['id_detil'] != '' )
		{	
			$id_detil 		= $_POST['id_detil'];
			$id_cust 		= $_POST['id_cust'];
			$tanggal 		= $_POST['tanggal'];
			$no_do 			= addslashes(trim(strtoupper($_POST['no_do'])));
			$penerima 		= addslashes(trim($_POST['penerima']));
			$id_asal 		= $_POST['id_asal'];
			$id_tujuan 		= $_POST['id_tujuan'];
			$jenis_mobil	= $_POST['jenis_mobil'];	
			$biaya 			= $_POST['biaya'];
			$uj 			= $_POST['uj'];
			$ritase 		= $_POST['ritase'];
			$sap_project 	= $_POST['sap_project'];
			$ket 			= trim(addslashes($_POST['ket']));
			$biaya 			= str_replace(",","", $biaya);
			$uj 			= str_replace(",","", $uj);
			$ritase 		= str_replace(",","", $ritase);
			$tanggalx 		= ConverTglSql($tanggal);
			
			// ------------ BUILD JO ------------
				$ptgl 		= explode("-", $tanggal);
				$tg 		= $ptgl[0];
				$bl 		= $ptgl[1];
				$th 		= $ptgl[2];	
				$query 		= "SELECT max(right(no_jo,5)) AS maxID FROM tr_jo WHERE year(tgl_jo) = '$th'";
				$hasil 		= mysqli_query($koneksi, $query);    
				$data  		= mysqli_fetch_array($hasil);
				$idMax 		= $data['maxID'];
				if ($idMax == '99999'){
					$idMax='00000';
				}
				$noUrut 	= (int) $idMax;   
				$noUrut++;  
				if(strlen($noUrut)=='1'){
					$noUrut="0000$noUrut";
					}elseif(strlen($noUrut)=='2'){
					$noUrut="000$noUrut";
					}elseif(strlen($noUrut)=='3'){
					$noUrut="00$noUrut";
					}elseif(strlen($noUrut)=='4'){
					$noUrut="0$noUrut";
				}   
				$year 		= substr($th,2,2);
				$no_sj 		= "SO-$year$noUrut";
				

			// ------------ PEMBUATAN PROJECT CODE ------------
				$year_PC = date('y');
				$sql = "SELECT project_code FROM tr_jo ORDER BY id_jo DESC LIMIT 1";
				$result = mysqli_query($koneksi, $sql);

				if (!$result) {
					die("Query error: " . mysqli_error($koneksi));
				}

				if (mysqli_num_rows($result) == 0) {
					$project_code = "TRC/$year_PC" . "0001";
				} else {
					$row = mysqli_fetch_assoc($result);
					$lastProjectCode = $row['project_code'];
					$lastYear = substr($lastProjectCode, 4, 2);

					if ($lastYear !== $year) {
						$project_code = "TRC/$year" . "0001";
					} else {
						$lastNum = (int)substr($lastProjectCode, -4);
						$newNum = str_pad($lastNum + 1, 4, "0", STR_PAD_LEFT);
						$project_code = "TRC/$year$newNum";
					}
				}

			$sql = "INSERT INTO  tr_jo (
				sap_project, 
				project_code, 
				id_cust, 
				id_detil_quo, 
				no_jo, 
				tgl_jo, 
				no_do, 
				penerima, 
				id_asal, 
				id_tujuan, 
				jenis_mobil, 
				biaya_kirim, 
				uj, 
				ritase, 
				ket, 
				created) 
			VALUES (
					'$sap_project',
					'$project_code',
					'$id_cust',
					'$id_detil',
					'$no_sj', 
					'$tanggalx',
					'$no_do',
					'$penerima',
					'$id_asal', 
					'$id_tujuan', 
					'$jenis_mobil', 
					'$biaya', 
					'$uj', 
					'$ritase', 
					'$ket', 
					'$id_user'
				)";

			$hasil= mysqli_query($koneksi, $sql);
				
			if (!$hasil) {
				echo "Data Error...!";
			}
			else
			{	
				echo "Data saved!";
			}
		}			
	}
?>