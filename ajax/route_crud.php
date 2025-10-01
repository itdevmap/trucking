<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role' and id_menu ='63' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

// -------------- READ DATA --------------
	if ($_GET['type'] == "Read"){
		$cari = trim($_GET['cari']);
		$hal = $_GET['hal'];
		$paging = $_GET['paging'];
		
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>	
						<th rowspan="2" width="3%" style="text-align: center;">NO</th>
						<th rowspan="2" width="30%" style="text-align: center;">VENDOR NAME</th>
						<th rowspan="2" width="20%" style="text-align: center;">ORIGIN</th>				
						<th rowspan="2" width="20%" style="text-align: center;">DESTINATION</th>				
						<th rowspan="2" width="10%" style="text-align: center;">COST</th>
						<th rowspan="2" width="3%" style="text-align: center;">EDIT</th>						
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
		$SQL = "SELECT 
					m_route_tr.*, 
					m_cust_tr.nama_cust,
					m_asal.nama_kota AS asal,
					m_tujuan.nama_kota AS tujuan
				FROM m_route_tr 
				INNER JOIN m_cust_tr on m_cust_tr.id_cust = m_route_tr.vendor 
				LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = m_route_tr.id_asal
				LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = m_route_tr.id_tujuan
				WHERE m_cust_tr.nama_cust LIKE '%$cari%'
				ORDER BY m_route_tr.vendor
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
					$data .= '<tr>							
					<td style="text-align:center">'.$posisi.'.</td>	
					<td style="text-align:left">'.$row['nama_cust'].'</td>
					<td style="text-align:center">'.$row['asal'].'</td>
					<td style="text-align:center">'.$row['tujuan'].'</td>
					<td style="text-align:center">'.number_format($row['cost'], 0, ',', '.').'</td>';
					if($m_edit == '1'  && $row['id_user'] != 'admin'){
						$data .= '
						<td>
							<button class="btn btn-block btn-default" title="Edit"
								style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
								onClick="javascript:GetData('.$row['id_pr'].')"  >
								<span class="fa fa-edit " ></span>
							</button>
						</td>';
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
					$pq = mysqli_query($koneksi, "select count(*) as jml from m_route_tr where rute LIKE '%$cari%' ");					
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

// -------------- STORE DATA --------------
	else if ($_POST['type'] == "Add_Data"){
		if(!empty($_POST['mode'])) {	
			$id      = $_POST['id'] ?? 0;
			$mode    = $_POST['mode'] ?? '';
			$vendor  = $_POST['vendor'] ?? 0;
			$origin  = $_POST['origin'] ?? 0;
			$destination = $_POST['destination'] ?? 0;
			$raw_cost = $_POST['cost'] ?? 0;
			$cost    = str_replace(",","", $raw_cost);
			$id_user = $_SESSION['id'] ?? 0;

			// definisikan rute biar tidak null
			$rute = $origin . "-" . $destination;

			if ($mode == 'Add') {
				$cek_sql = "SELECT id_route 
							FROM m_route_tr 
							WHERE vendor = '$vendor' 
							AND id_asal = '$origin' 
							AND id_tujuan = '$destination'
							LIMIT 1";
				$cek = mysqli_query($koneksi, $cek_sql);

				if (!$cek) {
					echo "QUERY_FAILED: " . mysqli_error($koneksi);
					exit;
				}

				if (mysqli_num_rows($cek) > 0) {
					echo "DATA SUDAH ADA";
					exit;
				}

				$sql = "INSERT INTO m_route_tr (
							vendor, 
							id_asal, 
							id_tujuan, 
							cost, 
							created_by, 
							created_at, 
							updated_by
						) 
						VALUES (
							'$vendor', 
							'$origin', 
							'$destination', 
							'$cost', 
							'$id_user', 
							NOW(), 
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
			else if ($mode == 'Edit') {
				$sql = "UPDATE m_route_tr SET 
							vendor     	= '$vendor',
							origin      = '$origin',
							destination	= '$destination',
							cost       	= '$cost',
							updated_by 	= '$id_user',
							updated_at 	= NOW()
						WHERE id_route 	= '$id'";
				$hasil = mysqli_query($koneksi, $sql);

				if ($hasil) {
					echo "UPDATE_SUCCESS";
				} else {
					echo "UPDATE_FAILED: " . mysqli_error($koneksi);
				}
				exit;
			}
		}
	}

// -------------- EDIT --------------
	else if ($_POST['type'] == "Detil_Data"){
		$id = $_POST['id'];	

		$query = "SELECT * FROM m_route_tr WHERE id_route = '$id'";
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