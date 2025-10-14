<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "SELECT * from m_role_akses_tr where id_role = '$id_role' and id_menu ='70' ");
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
						<th rowspan="2" width="10%" style="text-align: center;">ITEMCODE</th>				
						<th rowspan="2" width="30%" style="text-align: center;">ITEMNAME</th>
						<th rowspan="2" width="5%" style="text-align: center;">UoM</th>
						<th rowspan="2" width="10%" style="text-align: center;">COA</th>
						<th rowspan="2" width="10%" style="text-align: center;">CORPORATE</th>
						<th rowspan="2" width="10%" style="text-align: center;">DIVISI</th>
						<th rowspan="2" width="10%" style="text-align: center;">DEPARTMENT</th>
						<th rowspan="2" width="10%" style="text-align: center;">ACTIVITY</th>
						<th rowspan="2" width="10%" style="text-align: center;">LOCATION</th>
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
					*
				FROM sap_item_tr 
				WHERE sapitemname LIKE '%$cari%'
				ORDER BY sapitemname ASC
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
					<td style="text-align:center">'.$row['sapitemcode'].'</td>
					<td style="text-align:left">'.$row['sapitemname'].'</td>
					<td style="text-align:center">'.$row['uom'].'</td>

					<td style="text-align:center">'.$row['sap_coa'].'</td>
					<td style="text-align:center">'.$row['sap_corporate'].'</td>
					<td style="text-align:center">'.$row['sap_divisi'].'</td>
					<td style="text-align:center">'.$row['sap_dept'].'</td>
					<td style="text-align:center">'.$row['sap_activity'].'</td>
					<td style="text-align:center">'.$row['sap_location'].'</td>';
					if($m_edit == '1'  && $row['id_user'] != 'admin'){
						$data .= '
						<td>
							<button class="btn btn-block btn-default" title="Edit"
								style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
								onClick="javascript:GetData('.$row['rowid'].')"  >
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
		} else {
			$data .= '<tr><td colspan="7">Records not found!</td></tr>';
		}
		$data .= '</table>';
		
		$data .= '<div class="paginate paginate-dark wrapper">
					<ul>';
					$pq = mysqli_query($koneksi, "SELECT count(*) as jml from m_route_tr where rute LIKE '%$cari%' ");					
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

			// echo "<pre>";
			// print_r($_POST);
			// echo "</pre>";
			// die();


			$id      = $_POST['id'] ?? 0;
			$mode    = $_POST['mode'] ?? '';

			$itemcode  		= $_POST['itemcode'] ?? '';
			$itemname  		= $_POST['itemname'] ?? '';
			$uom 			= $_POST['uom'] ?? '';
			$sap_coa 		= $_POST['sap_coa'] ?? '';
			$sap_corporate 	= $_POST['sap_corporate'] ?? '';
			$sap_divisi 	= $_POST['sap_divisi'] ?? '';
			$sap_dept 		= $_POST['sap_dept'] ?? '';
			$sap_activity 	= $_POST['sap_activity'] ?? '';
			$sap_location 	= $_POST['sap_location'] ?? '';


			if ($mode == 'Add') {
				$cek_sql = "SELECT 
								sapitemcode 
							FROM sap_item_tr 
							WHERE sapitemcode = '$itemcode'
							LIMIT 1";
				$cek = mysqli_query($koneksi, $cek_sql);

				if (!$cek) {
					echo "QUERY_FAILED: " . mysqli_error($koneksi);
					exit;
				}

				if (mysqli_num_rows($cek) > 0) {
					echo "DATA ITEMCODE SUDAH ADA";
					exit;
				}

				$sql = "INSERT INTO sap_item_tr (
							sapitemcode, 
							sapitemname, 
							uom, 
							sap_coa, 
							sap_corporate, 
							sap_divisi, 
							sap_dept,
							sap_activity,
							sap_location
						) 
						VALUES (
							'$itemcode', 
							'$itemname', 
							'$uom', 
							'$sap_coa', 
							'$sap_corporate', 
							'$sap_divisi', 
							'$sap_dept', 
							'$sap_activity', 
							'$sap_location'
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
							sapitemcode     = '$sapitemcode',
							sapitemname     = '$sapitemname',
							uom				= '$uom',
							sap_coa       	= '$sap_coa',
							sap_corporate	= '$sap_corporate',
							sap_divisi 		= '$sap_divisi',
							sap_dept 		= '$sap_dept',
							sap_activity 	= '$sap_activity',
							sap_location 	= '$sap_location'
						WHERE id_route 		= '$id'";
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
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		$id = $_POST['id'];	

		$query = "SELECT * FROM sap_item_tr WHERE rowid = '$id'";
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