<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role' and id_menu ='65' ");
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
						<th rowspan="2" width="4%" style="text-align: center;">NO</th>
						<th rowspan="2" width="8%" style="text-align: center;">DATE</th>
						<th rowspan="2" width="8%" style="text-align: center;">REQ DATE</th>
						<th rowspan="2" width="10%" style="text-align: center;">SAP PROJECT</th>
						<th rowspan="2" width="10%" style="text-align: center;">CODE PR</th>
						<th rowspan="2" width="10%" style="text-align: center;">CODE SQ</th>
						<th rowspan="2" width="40%" style="text-align: center;">REMARK</th>
						<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>
						<th rowspan="2" width="3%" style="text-align: center;">EXEC</th>
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

		$q_pr = "SELECT 
					tr_pr.*,
					m_cust_tr.nama_cust,
					tr_quo.quo_no,
					sap_project.kode_project
				FROM tr_pr 
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_pr.user_req
				LEFT JOIN tr_quo ON tr_quo.id_quo = tr_pr.id_quo
				LEFT JOIN sap_project ON sap_project.rowid = tr_pr.sap_rowid
				WHERE tr_pr.code_pr LIKE '%$cari%'
				AND tr_pr.code_pr NOT LIKE '%PRWH%'
				ORDER BY tr_pr.code_pr DESC
				LIMIT $offset, $jmlperhalaman";	

		$query = mysqli_query($koneksi, $q_pr);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}
		if(mysqli_num_rows($result) > 0)
		{
			while($row = mysqli_fetch_assoc($result))
			{	
				$xy1	= "Edit|$row[id_pr]";
				$xy1	= base64_encode($xy1);
				$link 	= "pr_data.php?id=$xy1";

				if($row['status'] == '0')
				{
					$label = 'danger';
					$status = 'In Progress';
				}
				else if($row['status'] == '1')
				{
					$label = 'success';
					$status = 'Executed';
				}

				$posisi++;		
				$data .= '<tr>							
					<td style="text-align:center">'.$posisi.'.</td>	
					<td style="text-align:center">'.$row['tgl'].'</td>
					<td style="text-align:center">'.$row['tgl_pr'].'</td>
					<td style="text-align:center">'.$row['kode_project'].'</td>
					<td style="text-align:center"><a href="'.$link.'">'.$row['code_pr'].'</a></td>
					<td style="text-align:center">'.$row['quo_no'].'</td>
					<td style="text-align:center">'.$row['remark'].'</td>
					<td style="text-align:center">
						<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
					</td>';

				if($row['status'] == '0' ) {
					$data .= '<td>
							<button class="btn btn-block btn-default" title="Execute" style="margin:-3px;border-radius:0px" type="button" onClick="javascript:Confirm('.$row['id_pr'].')">
								<span class="fa fa-check-square-o"></span>
							</button></td>';
						
				}
				$data .='</tr>';
				$number++;
			}		
		}
		else
		{
			$data .= '<tr><td colspan="7" style="text-align:center">Records not found!</td></tr>';
		}
		$data .= '</table>';
		
		$data .= '<div class="paginate paginate-dark wrapper">
					<ul>';
					$pq = mysqli_query($koneksi, "SELECT count(*) AS jml FROM m_route_tr WHERE rute LIKE '%$cari%' ");					
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
			$id     	= $_POST['id'];
			$mode   	= $_POST['mode'];

			$tgl_pr 	= $_POST['tgl_pr'];
			$user_req 	= $_POST['user_req'];
			$barang   	= $_POST['barang'];
			$uom   		= $_POST['uom'];
			$remark   	= $_POST['remark'];
			$user   	= $_POST['user'];
			$raw_qty 	= $_POST['qty'];
			$qty   		= str_replace(",","", $raw_qty);
			$tanggal 	= date("Y-m-d");

			// ----------- BUILD CODE PR -----------
				$tahun = date("y");
				$q = "SELECT MAX(RIGHT(code_pr,5)) as last_num 
					FROM tr_pr 
					WHERE SUBSTRING(code_pr,5,2) = '$tahun'";
				$res = mysqli_query($koneksi, $q);
				$row = mysqli_fetch_assoc($res);
				$nextNum = ($row['last_num'] ?? 0) + 1;

				$urut = str_pad($nextNum, 5, "0", STR_PAD_LEFT);
				$code_pr = "PR-" . $tahun . $urut;

				$code_pr = $tahun . $urut;
				$code_pr = "PR-" . $code_pr;

			if ($mode == 'Add') {
				$sql = "INSERT INTO tr_pr (
							code_pr, 
							user_req, 
							tgl, 
							tgl_pr, 
							barang,
							uom,
							qty,
							qty_close,
							remark
						) 
						VALUES (
							'$code_pr', 
							'$user_req', 
							'$tanggal', 
							'$tgl_pr', 
							'$barang', 
							'$uom', 
							'$qty', 
							'$qty', 
							'$remark'
						)";

				$hasil = mysqli_query($koneksi, $sql);

				if ($hasil) {
					echo "INSERT _SUCCESS";
				} else {
					echo "INSERT_FAILED: " . mysqli_error($koneksi);
				}
				exit;
			}
		}
	}

// -------------- ADD DETAIL --------------
	else if ($_POST['type'] == "Add_Detil"){

		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		if (!empty($_POST['mode'])) {	
			$id          = $_POST['id'] ?? null;
			$mode        = $_POST['mode'];
			$jenisx      = $_POST['jenisx'];
			$code_pr     = mysqli_real_escape_string($koneksi, $_POST['code_pr']);
			$item        = strtoupper(mysqli_real_escape_string($koneksi, $_POST['name']));
			$origin      = mysqli_real_escape_string($koneksi, $_POST['origin'] ?? '');
			$destination = mysqli_real_escape_string($koneksi, $_POST['destination'] ?? '');
			$itemcode    = mysqli_real_escape_string($koneksi, $_POST['itemcode'] ?? '');
			$desc        = strtoupper(mysqli_real_escape_string($koneksi, $_POST['desc']));
			$remark      = mysqli_real_escape_string($koneksi, $_POST['remark']);
			$uom         = mysqli_real_escape_string($koneksi, $_POST['uom']);
			$qty         = (float) $_POST['qty'];

			if ($mode === 'Add') {

				if ($jenisx === 'item') {
					$check_item = "SELECT id_detail
						FROM tr_pr_detail
						WHERE itemcode = '$itemcode' 
						AND code_pr = '$code_pr'
					";
					// $r_check = mysqli_query($koneksi, $check_item);

					// if (!$r_check) {
					// 	die("Error check item: " . mysqli_error($koneksi));
					// }

					// if (mysqli_num_rows($r_check) > 0) {
					// 	$sql_insert = "UPDATE tr_pr_detail 
					// 		SET qty = qty + $qty,
					// 			qty_close = qty_close + $qty,
					// 		WHERE itemcode = '$itemcode'
					// 		AND code_pr = '$code_pr'
					// 	";
					// } else {
						$sql_insert = "INSERT INTO tr_pr_detail
							(
								code_pr, itemcode, item, origin, destination, `description`,remark, uom, jenis, qty, qty_close
							) VALUES (
								'$code_pr', '$itemcode', '$item', '$origin', '$destination', '$desc', '$remark', '$uom', '$jenisx', $qty, $qty
							)
						";
					// }
				} else {
					$sql_insert = "INSERT INTO tr_pr_detail 
							(
								code_pr, itemcode, item, origin, destination, `description`,remark, uom, jenis, qty, qty_close
							) VALUES (
								'$code_pr', '$itemcode', '$item', '$origin', '$destination', '$desc', '$remark', '$uom', '$jenisx', $qty, $qty
							)
						";
				}

				$hasil = mysqli_query($koneksi, $sql_insert);

				if (!$hasil) {
					echo "Insert data error: " . mysqli_error($koneksi);
				} else {
					echo "Data inserted!";
				}

			} elseif ($mode === 'Edit') {

				if ($jenisx === 'route') {
					$sql_update = "UPDATE tr_pr_detail 
						SET description = '$desc',
							qty = $qty,
							qty_close = $qty
						WHERE id_detail = '$id'
					";
				} elseif ($jenisx === 'item') {
					$sql_update = "UPDATE tr_pr_detail 
						SET itemcode = '$itemcode',
							item = '$item',
							`description` = '$desc',
							remark = '$remark',
							qty = $qty,
							qty_close = $qty
						WHERE id_detail = '$id'
					";
				}

				$hasil = mysqli_query($koneksi, $sql_update);

				if (!$hasil) {
					echo "Update data error: " . mysqli_error($koneksi);
				} else {
					echo "Data updated!";
				}
			}
		}

	}

// -------------- DETAIL DATA PO--------------
	else if($_GET['type'] == "Read_Detil") {

		// echo "<pre>";
		// print_r($_GET);
		// echo "</pre>";
		// die();


		$code_pr  = $_GET['code_pr'];
		$jenis    = $_GET['jenis'];
		$data = '';

		$xy1    = base64_encode($code_pr);
		$link   = "pr_cetak.php?jenis=$jenis&id=$xy1";
		$jenisx = ucwords(strtolower($jenis));

		
		$query_status = "SELECT `status` FROM tr_pr WHERE code_pr = '$code_pr' LIMIT 1";
		$result_status = mysqli_query($koneksi, $query_status);

		if ($result_status && mysqli_num_rows($result_status) > 0) {
			$row_status = mysqli_fetch_assoc($result_status);

			if ($row_status['status'] === '0') {
				$data .= '
					<div style="margin-bottom:10px; display:flex; align-items:center; gap:6px;">
						<button class="btn btn-success" 
								style="border-radius:2px" 
								type="button" 
								onClick="TampilData(\''.$jenis.'\')">
							<span class="fa fa-plus-square"></span> <b>Add '.$jenisx.'</b>
						</button>';
			}
		}

		switch ($jenis) {
			case 'route':
				$q_detail = "SELECT 
							tr_pr_detail.*,
							CONCAT(m_asal.nama_kota, ' - ', m_tujuan.nama_kota) AS item,
							tr_pr.status
						FROM tr_pr_detail 
						LEFT JOIN tr_pr ON tr_pr.code_pr = tr_pr_detail.code_pr
						LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_pr_detail.origin
						LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_pr_detail.destination
						WHERE tr_pr_detail.code_pr = '$code_pr' AND tr_pr_detail.jenis = '$jenis'
						ORDER BY tr_pr_detail.id_detail";
				$ket = 'FEET';
				break;

			case 'item':
				$q_detail = "SELECT 
							tr_pr_detail.*,
							m_cost_tr.itemcode AS item,
							tr_pr.status
						FROM tr_pr_detail 
						LEFT JOIN tr_pr ON tr_pr.code_pr = tr_pr_detail.code_pr
						LEFT JOIN m_cost_tr  ON m_cost_tr.id_cost = tr_pr_detail.itemcode
						WHERE tr_pr_detail.code_pr = '$code_pr' AND tr_pr_detail.jenis = '$jenis'
						ORDER BY tr_pr_detail.id_detail";
				$ket = 'UOM';
				break;

			default:
				$q_detail = "SELECT 
							tr_pr_detail.*,
							tr_pr.status
						FROM tr_pr_detail 
						LEFT JOIN tr_pr ON tr_pr.code_pr = tr_pr_detail.code_pr
						WHERE tr_pr_detail.code_pr = '$code_pr' AND tr_pr_detail.jenis = '$jenis'
						ORDER BY tr_pr_detail.id_detail";
				$ket = 'UOM';
				break;
		}

		// echo $query;
		// exit;

		$query = mysqli_query($koneksi, $q_detail);

		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}

		if(mysqli_num_rows($result) > 0) {
			$data .= '
				<button class="btn btn-success" 
						style="border-radius:2px" 
						type="button" 
						onClick="window.open(\''.$link.'\')">
					<span class="fa fa-print"></span> <b>Print PR</b>
				</button>';

			$data .= '</div>';

			$data .= "<table class='table table-hover table-striped' style='width:100%; margin-top: 6px;'>
						<thead>
							<tr>    
								<th width='5%' style='text-align: center;'>NO</th>                    
								<th width='15%' style='text-align: center;text-transform:uppercase;'>$jenis</th>
								<th width='55%' style='text-align: center;'>DESC</th>
								<th width='10%' style='text-align: center;'>QTY</th>                    
								<th width='10%' style='text-align: center;'>$ket</th>                      
								<th width='10%' style='text-align: center;'>Edit</th>                
							</tr>    
						</thead>";

			$posisi = 0;
			while($row = mysqli_fetch_assoc($result)) {
				$posisi++;        
				$data .= '<tr>                        
					<td style="text-align:center">'.$posisi.'.</td>
					<td style="text-align:center">'.$row['item'].'</td>    
					<td style="text-align:center">'.$row['description'].'</td>
					<td style="text-align:center">'.$row['qty_close'].'</td>
					<td style="text-align:center">'.$row['uom'].'</td>
					';

				if($row['status'] == '0' ) {
					$data .= '<td>
								<button class="btn btn-block btn-default" 
										title="Execute" 
										style="margin:-3px;border-radius:0px" 
										type="button" 
										onClick="javascript:EditDetail('.$row['id_detail'].', \''.$jenis.'\')">
									<span class="fa fa-edit"></span>
								</button>
							</td>';
				}
				$data .='</tr>';
			}

			$data .= '</table>';
		} else {
			$data .= '</div>';
			$data .= '<div style="margin:10px 0px;text-align:center"><b>Records not found!</b></div>';
		}

		echo $data;    
	}

	else if($_GET['type'] == "Read_Detil_WH") {

		// echo "<pre>";
		// print_r($_GET);
		// echo "</pre>";
		// die();


		$code_pr  = $_GET['code_pr'];
		$jenis    = $_GET['jenis'];
		$data = '';

		$xy1    = base64_encode($code_pr);
		$link   = "pr_cetak.php?jenis=$jenis&id=$xy1";
		$jenisx = ucwords(strtolower($jenis));

		
		$query_status = "SELECT `status` FROM tr_pr WHERE code_pr = '$code_pr' LIMIT 1";
		$result_status = mysqli_query($koneksi, $query_status);

		if ($result_status && mysqli_num_rows($result_status) > 0) {
			$row_status = mysqli_fetch_assoc($result_status);

			if ($row_status['status'] === '0') {
				$data .= '
					<div style="margin-bottom:10px; display:flex; align-items:center; gap:6px;">
						<button class="btn btn-success" 
								style="border-radius:2px" 
								type="button" 
								onClick="TampilData(\''.$jenis.'\')">
							<span class="fa fa-plus-square"></span> <b>Add '.$jenisx.'</b>
						</button>';
			}
		}

		$q_detail = "SELECT 
					tr_pr_detail.*,
					m_cost_tr.itemcode AS item,
					tr_pr.status
				FROM tr_pr_detail 
				LEFT JOIN tr_pr ON tr_pr.code_pr = tr_pr_detail.code_pr
				LEFT JOIN m_cost_tr  ON m_cost_tr.id_cost = tr_pr_detail.itemcode
				WHERE tr_pr_detail.code_pr = '$code_pr' AND tr_pr_detail.jenis = '$jenis'
				ORDER BY tr_pr_detail.id_detail";
		$ket = 'UOM';


		$query = mysqli_query($koneksi, $q_detail);

		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}

		if(mysqli_num_rows($result) > 0) {
			$data .= '
				<button class="btn btn-success" 
						style="border-radius:2px" 
						type="button" 
						onClick="window.open(\''.$link.'\')">
					<span class="fa fa-print"></span> <b>Print PR</b>
				</button>';

			$data .= '</div>';

			$data .= "<table class='table table-hover table-striped' style='width:100%; margin-top: 6px;'>
						<thead>
							<tr>    
								<th width='5%' style='text-align: center;'>NO</th>                    
								<th width='15%' style='text-align: center;text-transform:uppercase;'>$jenis</th>
								<th width='20%' style='text-align: center;'>DESC</th>
								<th width='35%' style='text-align: center;'>REMARK</th>   
								<th width='10%' style='text-align: center;'>QTY</th>
								<th width='10%' style='text-align: center;'>$ket</th>
								<th width='10%' style='text-align: center;'>Edit</th>                
							</tr>    
						</thead>";

			$posisi = 0;
			
			while($row = mysqli_fetch_assoc($result)) {
				$posisi++;        
				$data .= '<tr>                        
					<td style="text-align:center">'.$posisi.'.</td>
					<td style="text-align:center">'.$row['item'].'</td>    
					<td style="text-align:center">'.$row['description'].'</td>
					<td style="text-align:center">'.$row['remark'].'</td>
					<td style="text-align:center">'.$row['qty_close'].'</td>
					<td style="text-align:center">'.$row['uom'].'</td>
					';

				if($row['status'] == '0' ) {
					$data .= '<td>
								<button class="btn btn-block btn-default" 
										title="Execute" 
										style="margin:-3px;border-radius:0px" 
										type="button" 
										onClick="javascript:EditDetail('.$row['id_detail'].', \''.$jenis.'\')">
									<span class="fa fa-edit"></span>
								</button>
							</td>';
				}
				$data .='</tr>';
			}

			$data .= '</table>';
		} else {
			$data .= '</div>';
			$data .= '<div style="margin:10px 0px;text-align:center"><b>Records not found!</b></div>';
		}

		echo $data;    
	}

// -------------- SHOW AND CHOISE SQ IN PR --------------
	else if ($_GET['type'] == "ListSQ"){
		$cari = $_GET['cari'];
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>
						<th width="5%" style="text-align: center;">NO</th>
						<th width="15%" style="text-align: center;">DATE</th>
						<th width="15%" style="text-align: center;">NO SQ</th>
						<th width="55%" style="text-align: center;">CUSTOMER</th>
						<th width="10%" style="text-align: center;">ADD</th>
					</tr>
				</thead>';	
		$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
		$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
		
		$SQL = "SELECT 
					tr_quo.*,
					m_cust_tr.nama_cust
				FROM tr_quo 
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_quo.id_cust
				WHERE quo_no LIKE '%$cari%' AND tr_quo.status = '1'
				ORDER BY id_quo DESC LIMIT 0, 10";

		$query = mysqli_query($koneksi, $SQL);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}
		if(mysqli_num_rows($result) > 0)
		{
			while($row = mysqli_fetch_assoc($result))
			{	
				$posisi++;
				$data .= '<tr>';		
				$data .= '<td style="text-align:center">'.$posisi.'.</td>';

				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['id_quo'].')" >'.$row['quo_date'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['id_quo'].')" >'.$row['quo_no'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['id_quo'].')" >'.$row['nama_cust'].'</a></td>';

				$data .= '<td style="text-align:center">
						<button type="button" class="btn btn-default" onClick="javascript:PilihSQ('.$row['id_quo'].')" 
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
		
	}
	else if ($_POST['type'] == "DetilSQ"){
		$id = $_POST['id'];	
		
		$query = "SELECT 
					tr_quo.*
				FROM tr_quo 
				LEFT JOIN tr_quo_data ON tr_quo_data.id_quo = tr_quo.id_quo
				WHERE tr_quo.id_quo = '$id'";
		
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
	elseif ($_POST['type'] == "checkSQRoute") {

		$id = $_POST['id_quo'];
		if ($_POST['jenis'] === "route") {
			$query = "SELECT 
					tr_quo_data.*,
					kota_asal.id_kota AS id_asal,
					kota_asal.nama_kota AS asal,
					kota_tujuan.id_kota AS id_tujuan,
					kota_tujuan.nama_kota AS tujuan
				FROM tr_quo_data 
				LEFT JOIN m_kota_tr AS kota_asal ON kota_asal.id_kota = tr_quo_data.id_asal
				LEFT JOIN m_kota_tr AS kota_tujuan ON kota_tujuan.id_kota = tr_quo_data.id_tujuan
				WHERE tr_quo_data.id_quo = '$id'";
				$query_route = mysqli_query($koneksi, $query);
				$row_route   = mysqli_fetch_assoc($query_route);
		}

		$id_asal     = strtolower($row_route['id_asal'] ?? '');
		$asal        = strtolower($row_route['asal'] ?? '');
		$id_tujuan   = strtolower($row_route['id_tujuan'] ?? '');
		$tujuan   	 = strtolower($row_route['tujuan'] ?? '');

		$jenis_mobil = strtolower($row_route['jenis_mobil'] ?? '');

		echo json_encode([
			"id_asal" 		=> $id_asal,
			"asal" 			=> $asal,
			"id_tujuan" 	=> $id_tujuan,
			"tujuan" 		=> $tujuan,
			"jenis_mobil" 	=> $jenis_mobil,
		]);
		exit;
	}
	elseif ($_POST['type'] == "EditData") {

		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// exit;

		$id = $_POST['id_detail'];

		if ($_POST['jenis'] === "route") {
			$query = "SELECT 
						tr_pr_detail.id_detail,
						tr_pr_detail.itemcode,
						tr_pr_detail.uom,
						tr_pr_detail.qty,
						tr_pr_detail.description,
						tr_pr_detail.jenis,
						kota_asal.nama_kota AS asal,
						kota_tujuan.nama_kota AS tujuan
					FROM tr_pr_detail
					LEFT JOIN m_kota_tr AS kota_asal ON kota_asal.id_kota = tr_pr_detail.origin
					LEFT JOIN m_kota_tr AS kota_tujuan ON kota_tujuan.id_kota = tr_pr_detail.destination
					WHERE tr_pr_detail.id_detail = '$id'";
				
		} elseif ($_POST['jenis'] === "item") {
			$query = "SELECT 
				tr_pr_detail.id_detail,
				m_cost_tr.itemcode,
				m_cost_tr.id_cost AS rowid,
				tr_pr_detail.uom,
				tr_pr_detail.qty,
				tr_pr_detail.description,
				tr_pr_detail.jenis,
				tr_pr_detail.remark
			FROM tr_pr_detail
			LEFT JOIN m_cost_tr ON m_cost_tr.id_cost = tr_pr_detail.item
			WHERE tr_pr_detail.id_detail = '$id'";

			// echo $query;
			// exit;
		} else {
			$query = "SELECT 
						tr_pr_detail.id_detail,
						tr_pr_detail.uom,
						tr_pr_detail.qty,
						tr_pr_detail.description,
						tr_pr_detail.jenis
					FROM tr_pr_detail
					WHERE tr_pr_detail.id_detail = '$id'";
		}

		$dataQuery 	 = mysqli_query($koneksi, $query);
		$data   	 = mysqli_fetch_assoc($dataQuery);

		$id_detail   = strtolower($data['id_detail'] ?? '');
		$id_asal     = strtolower($data['id_asal'] ?? '');
		$asal        = strtolower($data['asal'] ?? '');
		$id_tujuan   = strtolower($data['id_tujuan'] ?? '');
		$tujuan   	 = strtolower($data['tujuan'] ?? '');
		$uom 		 = strtolower($data['uom'] ?? '');
		$description = strtolower($data['description'] ?? '');
		$qty 		 = strtolower($data['qty'] ?? '');
		$jenis 		 = strtolower($data['jenis'] ?? '');
		$itemcode 	 = strtolower($data['itemcode'] ?? '');
		$rowid 	 	 = strtolower($data['rowid'] ?? '');
		$remark 	 = strtolower($data['remark'] ?? '');

		// echo $itemcode;
		// exit;

		echo json_encode([
			"id_detail" 	=> $id_detail,
			"id_asal" 		=> $id_asal,
			"asal" 			=> $asal,
			"id_tujuan" 	=> $id_tujuan,
			"tujuan" 		=> $tujuan,
			"uom" 			=> $uom,
			"qty" 			=> $qty,
			"description" 	=> $description,
			"jenis" 		=> $jenis,
			"itemcode" 		=> $itemcode,
			"rowid" 		=> $rowid,
			"remark" 		=> $remark,
		]);
		exit;
	}
	else if ($_POST['type'] == "Executed") {
		header('Content-Type: application/json');

		if (!empty($_POST['id'])) {
			$id = mysqli_real_escape_string($koneksi, $_POST['id']);

			$q_check = "SELECT tr_pr_detail.id_detail
						FROM tr_pr_detail
						LEFT JOIN tr_pr ON tr_pr.code_pr = tr_pr_detail.code_pr
						WHERE tr_pr.id_pr = '$id'";
			$r_check = mysqli_query($koneksi, $q_check);

			if (!$r_check) {
				echo json_encode([
					"success" => false,
					"message" => "Query check gagal: " . mysqli_error($koneksi)
				]);
				exit;
			}

			if (mysqli_num_rows($r_check) == 0) {
				echo json_encode([
					"success" => false,
					"message" => "Masih belum ada detail PR."
				]);
				exit;
			}

			$sql = "UPDATE tr_pr 
					SET status = '1'
					WHERE id_pr = '$id'";
			$hasil = mysqli_query($koneksi, $sql);

			if (!$hasil) {
				echo json_encode([
					"success" => false,
					"message" => "Gagal update data: " . mysqli_error($koneksi)
				]);
			} else {
				echo json_encode([
					"success" => true,
					"message" => "Data Executed!"
				]);
			}
		} else {
			echo json_encode([
				"success" => false,
				"message" => "ID tidak ditemukan."
			]);
		}
	}

// ============= SHOW AND CHOISE ITEM IN PR =============
	else if ($_GET['type'] == "ListItem"){
		$cari = trim($_GET['cari']);

		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>
						<th width="5%" style="text-align: center;">NO</th>
						<th width="15%" style="text-align: center;">ITEMCODE</th>
						<th width="75%" style="text-align: center;">DESC</th>
						<th width="5%" style="text-align: center;">ADD</th>
					</tr>
				</thead>';	
		$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
		$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
		
		$SQL = "SELECT * 
				FROM m_cost_tr 
				WHERE nama_cost LIKE '%$cari%'
					AND sap_ips LIKE '%P%'
					AND itemcode IS NOT NULL
				ORDER BY itemcode DESC LIMIT 0, 10";

		$query = mysqli_query($koneksi, $SQL);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}
		if(mysqli_num_rows($result) > 0)
		{
			while($row = mysqli_fetch_assoc($result))
			{	
				$posisi++;
				$data .= '<tr>';		
				$data .= '<td style="text-align:center">'.$posisi.'.</td>';

				$data .= '<td style="text-align:center"><a href="#" onclick="PilihItem('.$row['id_cost'].')" >'.$row['itemcode'].'</a></td>';
				$data .= '<td style="text-align:left"><a href="#" onclick="PilihItem('.$row['id_cost'].')" >'.$row['nama_cost'].'</a></td>';

				$data .= '<td style="text-align:center">
						<button type="button" class="btn btn-default" onClick="javascript:PilihItem('.$row['id_cost'].')" 
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
		
	}
	else if ($_POST['type'] == "DetilItem"){
		$id = $_POST['id'];	
		
		$query = "SELECT * FROM m_cost_tr WHERE id_cost = '$id'";

		// echo $query;
		// exit;
		
		if (!$result = mysqli_query($koneksi, $query)) {
			exit(mysqli_error($koneksi));
		}
		$response = array();
		if(mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$response = $row;
			}
		}
		else {
			$response['status'] = 200;
			$response['message'] = "Data not found!";
		}
		echo json_encode($response);	
		
	}


// ============== PR WAREHOUSE ==============
	if ($_GET['type'] == "ReadWH"){
		$cari = trim($_GET['cari']);
		$hal = $_GET['hal'];
		$paging = $_GET['paging'];
		
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>	
						<th rowspan="2" width="3%" style="text-align: center;">NO</th>
						<th rowspan="2" width="5%" style="text-align: center;">DATE</th>
						<th rowspan="2" width="5%" style="text-align: center;">REQ DATE</th>				
						<th rowspan="2" width="7%" style="text-align: center;">CODE PR</th>
						<th rowspan="2" width="7%" style="text-align: center;">CODE QUO</th>
						<th rowspan="2" width="40%" style="text-align: center;">REMARK</th>
						<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>
						<th rowspan="2" width="3%" style="text-align: center;">EXEC</th>
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
					tr_pr.*,
					m_cust_tr.nama_cust,
					t_ware_quo.quo_no
				FROM tr_pr 
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_pr.user_req
				LEFT JOIN t_ware_quo ON t_ware_quo.id_quo = tr_pr.id_quo
				WHERE tr_pr.code_pr LIKE '%$cari%'
				AND tr_pr.code_pr LIKE '%PRWH%'
				ORDER BY tr_pr.code_pr DESC
				LIMIT $offset, $jmlperhalaman";	

		$query = mysqli_query($koneksi, $SQL);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}
		if(mysqli_num_rows($result) > 0)
		{
			while($row = mysqli_fetch_assoc($result))
			{	
				$xy1	= "Edit|$row[id_pr]";
				$xy1	= base64_encode($xy1);
				$link 	= "pr_wh_data.php?id=$xy1";

				if($row['status'] == '0')
				{
					$label = 'danger';
					$status = 'In Progress';
				}
				else if($row['status'] == '1')
				{
					$label = 'success';
					$status = 'Executed';
				}

				$posisi++;		
				$data .= '<tr>							
					<td style="text-align:center">'.$posisi.'.</td>	
					<td style="text-align:center">'.$row['tgl'].'</td>
					<td style="text-align:center">'.$row['tgl_pr'].'</td>
					<td style="text-align:center"><a href="'.$link.'">'.$row['code_pr'].'</a></td>
					<td style="text-align:center">'.$row['quo_no'].'</td>
					<td style="text-align:center">'.$row['remark'].'</td>
					<td style="text-align:center">
						<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
					</td>';

				if($row['status'] == '0' ) {
					$data .= '<td>
							<button class="btn btn-block btn-default" title="Execute" style="margin:-3px;border-radius:0px" type="button" onClick="javascript:Confirm('.$row['id_pr'].')">
								<span class="fa fa-check-square-o"></span>
							</button></td>';
						
				}
				$data .='</tr>';
				$number++;
			}		
		}
		else
		{
			$data .= '<tr><td colspan="7" style="text-align:center">Records not found!</td></tr>';
		}
		$data .= '</table>';
		
		$data .= '<div class="paginate paginate-dark wrapper">
					<ul>';
					$pq = mysqli_query($koneksi, "SELECT count(*) AS jml FROM m_route_tr WHERE rute LIKE '%$cari%' ");					
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

	else if ($_GET['type'] == "ListSQWH"){
		$cari = $_GET['cari'];
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>
						<th width="5%" style="text-align: center;">NO</th>
						<th width="15%" style="text-align: center;">DATE</th>
						<th width="15%" style="text-align: center;">NO Quo</th>
						<th width="55%" style="text-align: center;">CUSTOMER</th>
						<th width="10%" style="text-align: center;">ADD</th>
					</tr>
				</thead>';	
		$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
		$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
		
		$SQL = "SELECT 
					t_ware_quo.id_quo,
					t_ware_quo.quo_date,
					t_ware_quo.quo_no,
					m_cust_tr.nama_cust
				FROM t_ware_quo 
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = t_ware_quo.id_cust
				WHERE t_ware_quo.quo_no LIKE '%$cari%' AND t_ware_quo.status = '1'
				ORDER BY id_quo DESC LIMIT 0, 10";

		$query = mysqli_query($koneksi, $SQL);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}
		if(mysqli_num_rows($result) > 0)
		{
			while($row = mysqli_fetch_assoc($result))
			{	
				$posisi++;
				$data .= '<tr>';		
				$data .= '<td style="text-align:center">'.$posisi.'.</td>';

				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['id_quo'].')" >'.$row['quo_date'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['id_quo'].')" >'.$row['quo_no'].'</a></td>';
				$data .= '<td style="text-align:left"><a href="#" onclick="PilihSQ('.$row['id_quo'].')" >'.$row['nama_cust'].'</a></td>';

				$data .= '<td style="text-align:center">
						<button type="button" class="btn btn-default" onClick="javascript:PilihSQ('.$row['id_quo'].')" 
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
		
	}
	else if ($_POST['type'] == "DetilSQWH"){
		$id = $_POST['id'];	
		
		$query = "SELECT 
					t_ware_quo.*
				FROM t_ware_quo 
				WHERE t_ware_quo.id_quo = '$id'";
		
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