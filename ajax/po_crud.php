<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";

$pq = mysqli_query($koneksi, "SELECT * FROM m_role_akses_tr WHERE id_role = '$id_role' AND id_menu ='66'");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

// ================= READ DATA =================
	if ($_GET['type'] == "Read"){
		$cari = trim($_GET['cari']);
		$hal = $_GET['hal'];
		$paging = $_GET['paging'];
		
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>	
						<th width="3%" style="text-align: center;">NO</th>
						<th width="8%" style="text-align: center;">SAP PROJECT</th>
						<th width="8%" style="text-align: center;">CODE PR</th>
						<th width="10%" style="text-align: center;">CODE PO</th>
						<th width="7%" style="text-align: center;">REQ DATE</th>
						<th width="15%" style="text-align: center;">USER REQ</th>
						<th width="15%" style="text-align: center;">ACCOUNT</th>
						<th width="7%" style="text-align: center;">PAYMENT</th>					
						<th width="30%" style="text-align: center;">REMARK</th>			
						<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>	
						<th width="5%" style="text-align: center;">EXEC</th>				
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
					tr_po.*,
					sap_project.kode_project,
					m_cust_tr.nama_cust
				FROM tr_po 
				LEFT JOIN sap_project ON sap_project.rowid = tr_po.sap_project
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_po.user_req
				WHERE tr_po.code_po LIKE '%$cari%'
				AND tr_po.code_po NOT LIKE '%POWH%'
				ORDER BY tr_po.code_po DESC
				LIMIT $offset, $jmlperhalaman";	

		$query = mysqli_query($koneksi, $SQL);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}

		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result))
			{	
				$xy1	= "Edit|$row[id_po]";
				$xy1	= base64_encode($xy1);
				$link 	= "po_data.php?id=$xy1";

				if($row['status'] == '0'){
					$label = 'danger';
					$status = 'In Progress';
				} else if($row['status'] == '1'){
					$label = 'success';
					$status = 'Executed';
				}

				$posisi++;		
				$data .= '<tr><td style="text-align:center">'.$posisi.'.</td>	';

				if ($row['no_sap']!= null && $row['status'] === '1' && $row['no_ap']!= null) {
					$data .= '<td style="text-align:center">
						'.$row['kode_project'].'<br>
						PO '.$row['no_sap'].'<br>
						AP '.$row['no_ap'].'
					</td>';
				}elseif ($row['no_sap'] === null && $row['status'] === '1'){
					$data .= '<td style="text-align:center">
						'.$row['kode_project'].'<br>
						<a href="javascript:void(0);" onclick="TampilUpSAP(\''.$row['id_po'].'\')">Send PO <br>
						</a>
						'.$row['no_sap'].'
					</td>';
				} elseif ($row['no_sap'] != null && $row['status'] === '1' && $row['no_ap'] === null) {
					$data .= '<td style="text-align:center">
						'.$row['kode_project'].'<br>
						PO '.$row['no_sap'].'<br>
						<a href="javascript:void(0);" onclick="TampilUpAP(\''.$row['id_po'].'\')">Send AP<br>
						</a>
					</td>';
				} else{
					$data .= '<td style="text-align:center">
						'.$row['kode_project'].'
					</td>';
				}

				$data .= '
					<td style="text-align:center">'.$row['code_pr'].'</td>
					<td style="text-align:center"><a href="'.$link.'">'.$row['code_po'].'</a></td>
					<td style="text-align:center">'.$row['delivery_date'].'</td>
					<td style="text-align:center">'.$row['nama_cust'].'</td>
					<td style="text-align:center">'.$row['buyer'].'</td>
					<td style="text-align:center">'.$row['payment'].'</td>
					<td style="text-align:center">'.$row['remark'].'</td>
					<td style="text-align:center">
						<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
					</td>';

				if($row['status'] == '0' ) {
					$data .= '<td>
							<button class="btn btn-block btn-default" title="Execute" style="margin:-3px;border-radius:0px" type="button" onClick="javascript:Confirm('.$row['id_po'].')">
								<span class="fa fa-check-square-o"></span>
							</button></td>';
						
				}
				$data .='</tr>';

				$number++;
			}		
		}
		else {
			$data .= '<tr><td colspan="9" style="text-align:center">Records not found!</td></tr>';
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

// ================= STORE DATA =================
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
				$q = "SELECT MAX(RIGHT(code_pr,4)) as last_num 
					FROM tr_pr 
					WHERE SUBSTRING(code_pr,4,2) = '$tahun'";
				$res = mysqli_query($koneksi, $q);
				$row = mysqli_fetch_assoc($res);
				$nextNum = ($row['last_num'] ?? 0) + 1;

				$urut = str_pad($nextNum, 4, "0", STR_PAD_LEFT);
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

// ================= EDIT =================
	else if ($_POST['type'] == "Detil_Data"){
		$id = $_POST['id'];	

		$query = "SELECT * FROM tr_pr WHERE id_pr = '$id'";
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

// ================= SEARCH PR =================
	else if ($_GET['type'] == "ListPR"){
		$cari = $_GET['cari'];
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>
						<th width="5%" style="text-align: center;">NO</th>
						<th width="25%" style="text-align: center;">Purchase Request</th>
						<th width="60%" style="text-align: center;">SQ Code</th>
						<th width="10%" style="text-align: center;">ADD</th>
					</tr>
				</thead>';	
		$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
		$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
		
		$SQL = "SELECT 
					tr_pr.*, 
					SUM(tr_pr_detail.qty_close) AS total_qty_close,
					tr_quo.quo_no
				FROM tr_pr 
				LEFT JOIN tr_pr_detail ON tr_pr_detail.code_pr = tr_pr.code_pr
				LEFT JOIN tr_quo ON tr_quo.id_quo = tr_pr.id_quo
				WHERE tr_pr.code_pr LIKE '%$cari%'
					AND tr_pr.status = '1'
					AND tr_pr.code_pr NOT LIKE '%PRWH%'
				GROUP BY tr_pr.code_pr
				HAVING total_qty_close > 0
				ORDER BY tr_pr.code_pr";
		
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
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihPR('.$row['id_pr'].')" >'.$row['code_pr'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihPR('.$row['id_pr'].')" >'.$row['quo_no'].'</a></td>';
				$data .= '<td style="text-align:center">
						<button type="button" class="btn btn-default" onClick="javascript:PilihPR('.$row['id_pr'].')" 
						style="margin:-3px;width:100%;padding:1px;border-radius:1px"><span class="fa  fa-plus-square"></span></button>
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
	else if ($_GET['type'] == "ListPRWH"){
		$cari = $_GET['cari'];
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>
						<th width="5%" style="text-align: center;">NO</th>
						<th width="25%" style="text-align: center;">Purchase Request</th>
						<th width="60%" style="text-align: center;">SQ Code</th>
						<th width="10%" style="text-align: center;">ADD</th>
					</tr>
				</thead>';	
		$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
		$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
		
		$SQL = "SELECT 
					tr_pr.*, 
					SUM(tr_pr_detail.qty_close) AS total_qty_close,
					tr_quo.quo_no
				FROM tr_pr 
				LEFT JOIN tr_pr_detail ON tr_pr_detail.code_pr = tr_pr.code_pr
				LEFT JOIN tr_quo ON tr_quo.id_quo = tr_pr.id_quo
				WHERE tr_pr.code_pr LIKE '%$cari%'
					AND tr_pr.status = '1'
					AND tr_pr.code_pr LIKE '%PRWH%'
				GROUP BY tr_pr.code_pr
				HAVING total_qty_close > 0
				ORDER BY tr_pr.code_pr";
		
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
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihPR('.$row['id_pr'].')" >'.$row['code_pr'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihPR('.$row['id_pr'].')" >'.$row['quo_no'].'</a></td>';
				$data .= '<td style="text-align:center">
						<button type="button" class="btn btn-default" onClick="javascript:PilihPR('.$row['id_pr'].')" 
						style="margin:-3px;width:100%;padding:1px;border-radius:1px"><span class="fa  fa-plus-square"></span></button>
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
	else if ($_POST['type'] == "DetilData"){

		// echo "<pre>";
		// print_r($_GET);
		// echo "</pre>";
		// die();

		$id = $_POST['id'];

		$query = "SELECT 
					tr_pr.*,
					m_cust_tr.nama_cust,
					sap_project.rowid,
					sap_project.kode_project
				FROM tr_pr 
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_pr.user_req
				LEFT JOIN sap_project ON sap_project.rowid = tr_pr.sap_rowid
				WHERE id_pr = '$id'";

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

// ================= SEARCH ITEM PR =================
	else if ($_GET['type'] == "ListItemPR"){

		// echo "<pre>";
		// print_r($_GET);
		// echo "</pre>";
		// die();

		$cari 		= $_GET['cari'];
		$code_pr	= $_GET['code_pr'];
		$jenis 		= $_GET['jenis'];
		$jenisx = ucwords(strtolower($jenis));

		$data = "<table class='table table-hover table-striped' style='width:100%'>
			<thead style='font-weight:500 !important'>
				<tr>
					<th width='5%' style='text-align: center;'>NO</th>
					<th width='30%' style='text-align: center;'>$jenisx</th>
					<th width='40%' style='text-align: center;'>Description</th>
					<th width='15%' style='text-align: center;'>Type</th>
					<th width='10%' style='text-align: center;'>ADD</th>
				</tr>
			</thead>";

		$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
		$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);

		if ($jenis === 'route') {
			$q_item = "SELECT 
						tr_pr_detail.*,
						CONCAT(m_asal.nama_kota, ' - ', m_tujuan.nama_kota) AS item
					FROM tr_pr_detail 
					LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_pr_detail.origin
					LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_pr_detail.destination
					WHERE tr_pr_detail.item LIKE '%$cari%' 
						AND tr_pr_detail.code_pr = '$code_pr' 
						AND tr_pr_detail.qty_close > 0 
						AND tr_pr_detail.jenis = '$jenis'
					ORDER BY item DESC";
		} else if ($jenis === 'item') {
			$q_item = "SELECT 
						tr_pr_detail.*, 
						m_cost_tr.itemcode AS item 
					FROM tr_pr_detail 
					INNER JOIN m_cost_tr 
						ON m_cost_tr.id_cost = tr_pr_detail.item 
					WHERE m_cost_tr.nama_cost LIKE '%$cari%' 
					AND tr_pr_detail.code_pr = '$code_pr' 
					AND tr_pr_detail.qty_close > 0 
					AND tr_pr_detail.jenis = 'item' 
					ORDER BY tr_pr_detail.item DESC";

			// echo $q_item;
			// exit;
		}else {
			$q_item = "SELECT tr_pr_detail.* 
					FROM tr_pr_detail 
					WHERE tr_pr_detail.item LIKE '%$cari%' 
						AND tr_pr_detail.code_pr = '$code_pr' 
						AND tr_pr_detail.qty_close > 0 
						AND tr_pr_detail.jenis = '$jenis'
					ORDER BY item DESC";
		}
		

		$query = mysqli_query($koneksi, $q_item);	
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
				$data .= '
				<td style="text-align:left">
					<a href="#" style="text-transform:uppercase;" onclick="PilihItemPR('.$row['id_detail'].')" >'.$row['item'].'</a>
				</td>
				<td style="text-align:left;text-transform:uppercase;">
					'.$row['description'].'
				</td>
				<td style="text-align:center;text-transform:uppercase;">
					'.$row['jenis'].'
				</td>
				';
				$data .= '<td style="text-align:center">
						<button type="button" class="btn btn-default" onClick="javascript:PilihItemPR('.$row['id_detail'].')" 
						style="margin:-3px;width:100%;padding:1px;border-radius:1px"><span class="fa  fa-plus-square"></span></button>
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
	else if ($_POST['type'] == "DetilDataItem") {

		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		$id        = $_POST['id'];    
		$jenis     = $_POST['jenis'];
		$id_vendor = $_POST['id_vendor'];

		if ($jenis === 'route') {
			$query = "SELECT 
							d.*,
							r.cost AS harga,
							r.id_route AS item,
							CONCAT(a.nama_kota, ' - ', t.nama_kota) AS jenis,
							a.id_kota AS origin,
							t.id_kota AS destination
					FROM tr_pr_detail AS d
					LEFT JOIN m_route_tr AS r 
							ON r.id_asal = d.origin 
							AND r.id_tujuan = d.destination
							AND r.vendor = '$id_vendor'
					LEFT JOIN m_kota_tr AS a ON a.id_kota = d.origin
					LEFT JOIN m_kota_tr AS t ON t.id_kota = d.destination
					WHERE d.id_detail = '$id'";

			// echo $query;
			// exit;

		} else if ($jenis === 'item') {
			$query = "SELECT 
							tr_pr_detail.*,
							m_cost_tr.itemcode AS jenis,
							m_cost_tr.itemcode,
							m_cost_tr.uom
					FROM tr_pr_detail 
					LEFT JOIN m_cost_tr  
							ON m_cost_tr.id_cost = tr_pr_detail.itemcode
					WHERE tr_pr_detail.id_detail = '$id'
					ORDER BY tr_pr_detail.id_detail";    
		} else {
			$query = "SELECT 
							tr_pr_detail.*,
							tr_pr_detail.description AS jenis
					FROM tr_pr_detail 
					WHERE tr_pr_detail.id_detail = '$id'
					ORDER BY tr_pr_detail.id_detail";    
		}

		$result = mysqli_query($koneksi, $query);

		if (!$result) {
			exit(json_encode([
				'status'  => 500,
				'message' => mysqli_error($koneksi)
			]));
		}

		if (mysqli_num_rows($result) === 0) {
			echo json_encode([
				'status'  => 404,
				'message' => "Data tidak ditemukan!"
			]);
			exit;
		}

		$row = mysqli_fetch_assoc($result);

		if ($jenis === 'route' && (empty($row['harga']) || $row['harga'] === null)) {
			echo json_encode([
				'status'  => 404,
				'message' => "Data harga tidak ditemukan. Check Master Route"
			]);
			exit;
		}

		echo json_encode($row);
	}
	else if ($_POST['type'] == "Add_Detil"){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		if($_POST['mode'] != '' ) {	
			$id 		= $_POST['idx'];
			$mode 		= $_POST['mode'];
			$jenisx 	= $_POST['jenisx'];
			$code_po 	= $_POST['code_po'];
			$code_pr 	= $_POST['code_pr'];
			$id_item 	= $_POST['id_item'];
			$item 		= strtoupper($_POST['item']);
			$origin 	= $_POST['origin'];
			$destination= $_POST['destination'];
			$item 		= strtoupper($_POST['item']);
			$itemcode 	= strtoupper($_POST['itemcode']);
			$description= strtoupper($_POST['description']);
			$container 	= strtoupper($_POST['container']);
			$uom 		= $_POST['uom'];
			$cur 		= $_POST['cur'];
			$qty 		= $_POST['qty'];
			$harga 		= $_POST['harga'];
			$disc 		= $_POST['disc'] ?? 0;
			$ppn 		= $_POST['ppn'] ?? 0;
			$nominal_ppn= $_POST['nominal_ppn'] ?? 0;
			$total 		= $_POST['total'];

			if($mode == 'Add') {
				$sql_action = "INSERT INTO tr_po_detail (
						code_po, itemcode, item, origin, destination, `description`, container, qty, uom, cur, harga, total, disc, ppn, nominal_ppn, jenis
					) VALUES (
						'$code_po', '$itemcode', '$id_item', '$origin', '$destination', '$description', '$container', '$qty', '$uom', '$cur', '$harga', '$total', '$disc', '$ppn', '$nominal_ppn', '$jenisx'
					)";

				if ($jenisx == 'route') {
					$sql_qty_close = "SELECT 
										tr_pr_detail.qty_close AS qty_old
									FROM tr_pr_detail 
									LEFT JOIN m_route_tr ON m_route_tr.id_asal = tr_pr_detail.origin 
										AND m_route_tr.id_tujuan = tr_pr_detail.destination
									WHERE tr_pr_detail.code_pr = '$code_pr' 
									AND m_route_tr.id_route = '$id_item'";
				} elseif ($jenisx == 'item') {
					$sql_qty_close = "SELECT 
										tr_pr_detail.qty_close AS qty_old
									FROM tr_pr_detail WHERE tr_pr_detail.code_pr = '$code_pr' 
									AND tr_pr_detail.item = '$id_item'";
				} else {
					$sql_qty_close = "SELECT 
										tr_pr_detail.qty_close AS qty_old
									FROM tr_pr_detail WHERE tr_pr_detail.code_pr = '$code_pr' 
									AND tr_pr_detail.description = '$description'";
				}

				$result = mysqli_query($koneksi, $sql_qty_close);
				if ($result && mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_assoc($result);

					$new_qty_close = $row['qty_old'] - $qty;
					if ($new_qty_close < 0) {
						echo "QTY melebihi stok yang di ajukan di PR !";
						exit;
					}
					$sql_update_po = "UPDATE tr_pr_detail 
									LEFT JOIN m_route_tr ON m_route_tr.id_asal = tr_pr_detail.origin 
										AND m_route_tr.id_tujuan = tr_pr_detail.destination
										AND m_route_tr.id_route = '$id_item'
									SET tr_pr_detail.qty_close = '$new_qty_close'
									WHERE tr_pr_detail.code_pr = '$code_pr'
										AND tr_pr_detail.id_detail = '$id'";

					$hasil_update = mysqli_query($koneksi, $sql_update_po);
					$hasil_action = mysqli_query($koneksi, $sql_action);
				} else {
					echo "Data PO tidak ditemukan untuk code_po: $code_po";
				}
			}else {
				$sql_action = "UPDATE tr_po_detail SET
					`description` = '$description',
					`container`   = '$container',
					`qty`         = '$qty',
					`disc`        = '$disc',
					`ppn`         = '$ppn',
					`nominal_ppn` = '$nominal_ppn',
					`harga`       = '$harga',
					`total`       = '$total'
					WHERE id = '$id'";

				if ($jenisx == 'route') {
					$sql_qty_close = "SELECT 
										tr_pr_detail.qty AS qty_old,
										tr_pr_detail.id_detail
									FROM tr_pr_detail 
									LEFT JOIN m_route_tr ON m_route_tr.id_asal = tr_pr_detail.origin 
										AND m_route_tr.id_tujuan = tr_pr_detail.destination
									WHERE tr_pr_detail.code_pr = '$code_pr' 
									AND m_route_tr.id_route = '$id_item'";

					$result = mysqli_query($koneksi, $sql_qty_close);
					if ($result && mysqli_num_rows($result) > 0) {
						$row = mysqli_fetch_assoc($result);

						$new_qty_close = $row['qty_old'] - $qty;
						if ($new_qty_close < 0) {
							echo "QTY melebihi stok yang di ajukan di PR !";
							exit;
						}
						$id_detail = $row['id_detail'];
						$sql_update_po = "UPDATE tr_pr_detail 
										LEFT JOIN m_route_tr ON m_route_tr.id_asal = tr_pr_detail.origin 
											AND m_route_tr.id_tujuan = tr_pr_detail.destination
											AND m_route_tr.id_route = '$id_item'
										SET tr_pr_detail.qty_close = '$new_qty_close'
										WHERE tr_pr_detail.code_pr = '$code_pr'
											AND tr_pr_detail.id_detail = '$id_detail'";

						$hasil_update = mysqli_query($koneksi, $sql_update_po);
						$hasil_action = mysqli_query($koneksi, $sql_action);
					} else {
						echo "Data PO tidak ditemukan untuk code_po: $code_po";
					}

				} elseif ($jenisx == 'item') {
					$sql_qty_close = "SELECT 
										tr_pr_detail.qty AS qty_old,
										tr_pr_detail.id_detail
									FROM tr_pr_detail WHERE tr_pr_detail.code_pr = '$code_pr' 
									AND tr_pr_detail.item = '$id_item'";
									
					$result = mysqli_query($koneksi, $sql_qty_close);
					if ($result && mysqli_num_rows($result) > 0) {
						$row = mysqli_fetch_assoc($result);

						$new_qty_close = $row['qty_old'] - $qty;
						if ($new_qty_close < 0) {
							echo "QTY melebihi stok yang di ajukan di PR !";
							exit;
						}
						$id_detail = $row['id_detail'];
						$sql_update_po = "UPDATE tr_pr_detail 
										SET tr_pr_detail.qty_close = '$new_qty_close'
										WHERE tr_pr_detail.code_pr = '$code_pr'
											AND tr_pr_detail.id_detail = '$id_detail'";

						$hasil_update = mysqli_query($koneksi, $sql_update_po);
						$hasil_action = mysqli_query($koneksi, $sql_action);
					} else {
						echo "Data PO tidak ditemukan untuk code_po: $code_po";
					}
				} else {
					$sql_qty_close = "SELECT 
										tr_pr_detail.qty AS qty_old,
										tr_pr_detail.id_detail
									FROM tr_pr_detail WHERE tr_pr_detail.code_pr = '$code_pr' 
									AND tr_pr_detail.jenis = '$jenisx'
									AND tr_pr_detail.uom LIKE '%$uom%'";

					$result = mysqli_query($koneksi, $sql_qty_close);
					if ($result && mysqli_num_rows($result) > 0) {
						$row = mysqli_fetch_assoc($result);

						$new_qty_close = $row['qty_old'] - $qty;
						if ($new_qty_close < 0) {
							echo "QTY melebihi stok yang di ajukan di PR !";
							exit;
						}
						$id_detail = $row['id_detail'];
						$sql_update_po = "UPDATE tr_pr_detail 
										SET tr_pr_detail.qty_close = '$new_qty_close'
										WHERE tr_pr_detail.code_pr = '$code_pr'
											AND tr_pr_detail.id_detail = '$id_detail'";

						$hasil_update = mysqli_query($koneksi, $sql_update_po);
						$hasil_action = mysqli_query($koneksi, $sql_action);
					} else {
						echo "Data PO tidak ditemukan untuk code_po: $code_po";
					}
				}
			}
			
			if (!$hasil_action) {
				echo "Action error !";
			}
			else if (!$hasil_update) {
				echo "Update data error !";
			}
			else{	
				echo "Data saved!";
			}
		}		
	}

// ================= DETAIL DATA PO=================
	else if($_GET['type'] == "Read_Detil") {
		$code_po  = $_GET['code_po'];
		$jenis    = $_GET['jenis'];

		$data = '';
		$xy1    = base64_encode($code_po);
		$link   = "po_cetak.php?jenis=$jenis&id=$xy1";
		$jenisx = ucwords(strtolower($jenis));

		$data .= '
			<div style="margin-bottom:10px; display:flex; align-items:center; gap:6px;">
				<button class="btn btn-success" 
						style="border-radius:2px" 
						type="button" 
						onClick="TampilData(\''.$jenis.'\')">
					<span class="fa fa-plus-square"></span> <b>Add '.$jenisx.'</b>
				</button>';

		switch ($jenis) {
			case 'route':
				$sql_query = "SELECT 
							tr_po_detail.*,
							CONCAT(m_asal.nama_kota, ' - ', m_tujuan.nama_kota) AS item,
							m_asal.id_kota AS origin,
							m_tujuan.id_kota AS destination,
							tr_po.status,
							tr_po.no_sap
						FROM tr_po_detail 
						LEFT JOIN tr_po ON tr_po.code_po = tr_po_detail.code_po
						LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_po_detail.origin
						LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_po_detail.destination
						WHERE tr_po_detail.code_po = '$code_po' 
							AND tr_po_detail.jenis = '$jenis'
						ORDER BY tr_po_detail.id";
				break;

			case 'item':
				$sql_query = "SELECT 
							tr_po_detail.*,
							m_cost_tr.itemcode AS item,
							tr_po.status,
							tr_po.no_sap
						FROM tr_po_detail 
						LEFT JOIN tr_po ON tr_po.code_po = tr_po_detail.code_po
						LEFT JOIN m_cost_tr ON m_cost_tr.id_cost = tr_po_detail.item
						WHERE tr_po_detail.code_po = '$code_po' 
							AND tr_po_detail.jenis = '$jenis'
						ORDER BY tr_po_detail.id";
				break;

			default:
				$sql_query = "SELECT 
							tr_po_detail.*,
							tr_po.status,
							tr_po.no_sap
						FROM tr_po_detail 
						LEFT JOIN tr_po ON tr_po.code_po = tr_po_detail.code_po
						WHERE tr_po_detail.code_po = '$code_po' 
							AND tr_po_detail.jenis = '$jenis'
						ORDER BY tr_po_detail.id";
				break;
		}

		$query = mysqli_query($koneksi, $sql_query);

		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}

		if(mysqli_num_rows($result) > 0) {
			$firstRow = mysqli_fetch_assoc($result);
			if ($firstRow['no_sap'] != 'error' && !empty($firstRow['no_sap'])) {
				$data .= '
					<button class="btn btn-success" 
							style="border-radius:2px" 
							type="button" 
							onClick="window.open(\''.$link.'\')">
						<span class="fa fa-print"></span> <b>Print PO</b>
					</button>';
			}
			$data .= '</div>';

			// reset pointer ke awal result
			mysqli_data_seek($result, 0);

			$data .= "<table class='table table-hover table-striped' style='width:100%'>
						<thead>
							<tr>    
								<th width='5%' style='text-align: center;'>NO</th>                    
								<th width='15%' style='text-align: center;text-transform:uppercase;'>$jenis</th>
								<th width='10%' style='text-align: center;'>CONTAINER</th>         
								<th width='30%' style='text-align: center;'>DESC</th>
								<th width='10%' style='text-align: center;'>QTY/UoM</th>         
								<th width='10%' style='text-align: center;'>PRICE</th>         
								<th width='5%' style='text-align: center;'>DISC</th>         
								<th width='5%' style='text-align: center;'>PPN</th>         
								<th width='15%' style='text-align: center;'>TOTAL</th>         
								<th width='5%' style='text-align: center;'>EDIT</th>         
							</tr>    
						</thead>";

			$posisi = 0;
			
			while($row = mysqli_fetch_assoc($result)) {
				$posisi++;        
				$harga  = number_format($row['harga'], 0, ',', '.');
				$total  = number_format($row['total'], 0, ',', '.'); 
				$disc   = number_format($row['disc'], 0, ',', '.'); 
				$ppn    = number_format($row['ppn'], 0, ',', '.'); 

				$data .= '<tr>                  
					<td style="text-align:center">'.$posisi.'.</td>
					<td style="text-align:center">'.$row['item'].'</td>    
					<td style="text-align:center">'.$row['container'].'</td>    
					<td style="text-align:center">'.$row['description'].'</td>
					<td style="text-align:center">'.$row['qty'].' '.strtoupper($row['uom']).'</td>
					<td style="text-align:center">'.$harga.'</td>
					<td style="text-align:center">'.$disc.'% </td>
					<td style="text-align:center">'.$ppn.'% </td>
					<td style="text-align:right">'.$total.'</td>';

				if($row['status'] === '0' ) {
					$data .= '<td>
								<button class="btn btn-block btn-default" 
										title="Execute" 
										style="margin:-3px;border-radius:0px" 
										type="button" 
										onClick="javascript:EditDetail('.$row['id'].', \''.$jenis.'\')">
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
	
// ================= SEND PO TO SAP =================
	else if ($_GET['type'] == "ListUpSAP") {
		$cari   = mysqli_real_escape_string($koneksi, $_GET['cari']);
		$id_po  = mysqli_real_escape_string($koneksi, $_GET['id_po']);

		$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead>
				<tr>
					<th width="5%" style="text-align: center;">NO</th>
					<th width="15%" style="text-align: center;">Date</th>
					<th width="15%" style="text-align: center;">SAP Project</th>
					<th width="15%" style="text-align: center;">No PO</th>
					<th width="27%" style="text-align: center;">Nama Customer</th>
					<th width="10%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';

		$sql_po = "SELECT 
					tr_po.*
				FROM tr_po 
				LEFT JOIN tr_po_detail ON tr_po_detail.code_po = tr_po.code_po
				WHERE tr_po.status = '0' 
					AND tr_po.id_po = '$id_po' 
					AND tr_po.no_sap IS NULL
				LIMIT 1";
		// echo $sql_po;
		// die();

		$query_po = mysqli_query($koneksi, $sql_po);

		if ($query_po && mysqli_num_rows($query_po) > 0) {
			$dp         = mysqli_fetch_assoc($query_po);
			$tgl_po     = $dp['delivery_date'];
			$sap_project= $dp['sap_project'];
			$id_cust    = $dp['id_cust'];
			$id_asal    = $dp['id_asal'];
			$id_tujuan  = $dp['id_tujuan'];
		} else {
			$tgl_po = $sap_project = $id_cust = null;
		}

		$SQL = "SELECT 
					tr_po.id_po,
					tr_po.delivery_date,
					sap_project.kode_project,
					tr_po.code_po,
					m_cust_tr.nama_cust
				FROM tr_po
				LEFT JOIN tr_po_detail ON tr_po_detail.code_po = tr_po.code_po
				LEFT JOIN sap_project ON sap_project.rowid = tr_po.sap_project
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_po.user_req
				WHERE tr_po.id_po = '$id_po'
				GROUP BY tr_po.id_po
				LIMIT 0, 10";
				
		$query = mysqli_query($koneksi, $SQL);	
		if (!$query) {
			exit(mysqli_error($koneksi));
		}

		$posisi = 0;
		if (mysqli_num_rows($query) > 0) {
			while($row = mysqli_fetch_assoc($query)) {	
				$posisi++;
				$data .= '<tr>';		
				$data .= '<td style="text-align:center">'.$posisi.'</td>';
				$data .= '<td style="text-align:center">'.$row['delivery_date'].'</td>';
				$data .= '<td style="text-align:center">'.$row['kode_project'].'</td>';
				$data .= '<td style="text-align:center">'.$row['code_po'].'</td>';
				$data .= '<td style="text-align:center">'.$row['nama_cust'].'</td>';

				$id_po = isset($row['id_po']) ? $row['id_po'] : $row['code_po'];
				$data .= '<td style="text-align:center">
							<label>
							<input type="checkbox" 
									name="sap_selected[]" 
									value="'.$id_po.'">
							</label>
						</td>';

				$data .= '</tr>';
			}		
		} else {
			$data .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan</td></tr>';
		}

		$data .= '</table>';
		echo $data;			
	}
	else if ($_POST['type'] == "SaveUpSAP") {
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();
		$ids = isset($_POST['ids']) ? $_POST['ids'] : [];
		$ids = array_unique($ids);
		if (empty($ids)) {
			echo json_encode([
				"success" => false,
				"message" => "Tidak ada data yang dipilih"
			]);
			exit;
		}

		$resultData = [];
		$detailCount = [];

		foreach ($ids as $id_po) {
			$id_po = mysqli_real_escape_string($koneksi, $id_po);

			$sql_header = "SELECT 
								tr_po.code_po,
								tr_po.delivery_date,
								m_vendor_tr.caption,
								m_vendor_tr.nama_vendor,
								sap_project.kode_project
						FROM tr_po 
						LEFT JOIN tr_po_detail ON tr_po_detail.code_po = tr_po.code_po
						LEFT JOIN m_vendor_tr ON m_vendor_tr.id_vendor = tr_po.user_req
						LEFT JOIN sap_project ON sap_project.rowid = tr_po.sap_project
						WHERE tr_po.id_po = '$id_po' LIMIT 1";
						
			$query_header = mysqli_query($koneksi, $sql_header);

			while ($row = mysqli_fetch_assoc($query_header)) {
				if (empty($row['caption'])) {
					echo json_encode([
						"success" => false,
						"message" => "Vendor code masih kosong, silahkan hubungi tim IT-SAP"
					]);
					exit;
				}

				$code_po = mysqli_real_escape_string($koneksi, $row['code_po']);
				$sql_detail = "SELECT 
								tr_po_detail.*,
								m_cost_tr.sap_coa,
								m_cost_tr.sap_corporate,
								m_cost_tr.sap_divisi,
								m_cost_tr.sap_dept,
								m_cost_tr.sap_activity,
								m_cost_tr.sap_location
							FROM tr_po_detail
							LEFT JOIN m_cost_tr ON m_cost_tr.itemcode = tr_po_detail.itemcode
							WHERE tr_po_detail.code_po = '$code_po' AND tr_po_detail.jenis = 'item'";

				// echo $sql_detail;
				// exit;

				$query_detail = mysqli_query($koneksi, $sql_detail);

				$lines = [];
				if ($query_detail) {
					while ($det = mysqli_fetch_assoc($query_detail)) {
						$lines[] = [
							"ItemCode"   	=> $det['itemcode'],
							"ItemName"   	=> $det['description'],
							"Qty"       	=> $det['qty'],
							"UoM"       	=> $det['uom'],
							"Harga" 		=> (int)$det['harga'],
							"Disc" 			=> $det['disc'],
							"PPN"       	=> $det['ppn'],

							"GLAcct"       	=> $det['sap_coa'] ?? '',
							"Corporate"     => $det['sap_corporate'] ?? '',
							"Divisi"       	=> $det['sap_divisi'] ?? '',
							"Department"    => $det['sap_dept'] ?? '',
							"Activity"      => $det['sap_activity'] ?? '',
							"Location"      => $det['sap_location'] ?? '',
						];
					}

					if (!isset($resultData[$key])) {
						$resultData[$key] = [
							"NoPO"    	 => $row['code_po'],
							"TglPO"    	 => $row['delivery_date'],
							"VendorCode" => $row['caption'],
							"Project"  	 => $row['kode_project'],
							"Remarks"  	 => $row['nama_vendor'],
							"Buyer"  	 => 25,
							"TipePurch"	 => 6,
							"Lines"    	 => $lines
						];
					}
				}
			}
		}

		$resultData = array_values($resultData);

		// ----------- NO SEND API (LIHAT JSON) -----------
			// header('Content-Type: application/json');
			// echo "<pre>";
			// echo json_encode($resultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			// echo "<pre>";
			// die();

			// echo json_encode([
			// 	"success" => true,
			// 	"data"    => $resultData
			// ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			

		// ----------- KIRIM API -----------
			// $apiUrl = "https://wsp.mitraadipersada.com/trucking/purch-order.php";
			$apiUrl = "http://192.168.1.153/trucking2/purch-order.php";

			$ch = curl_init($apiUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json'
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array_values($resultData)));

			$response = curl_exec($ch);
			curl_close($ch);
			
			// ----------- CHECK RESPONS -----------
				// $data = json_decode($response, true);
				// echo "<pre>";
				// print_r($data);
				// echo "</pre>";
				// die();

			$apiResponse = json_decode($response, true);

			if (!is_array($apiResponse)) {
				$apiResponse = [];
			}

			$rawData       = mysqli_real_escape_string($koneksi, json_encode($resultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
			$resultDataLog = mysqli_real_escape_string($koneksi, json_encode($apiResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

			$success = false;
			$mssg    = $apiResponse['mssg'] ?? 'Invalid API response';
			
			if (!isset($apiResponse['docnum'])) {
				$sql = "INSERT INTO tr_api_logs (docnum, doctype, raw_data, `desc`, result) 
						VALUES (
						'', 
						'PO', 
						'$rawData', 
						'ERROR-" . mysqli_real_escape_string($koneksi, $mssg) . "', '$resultDataLog')";
				mysqli_query($koneksi, $sql);
			} else {
				$docnum = mysqli_real_escape_string($koneksi, $apiResponse['docnum'] ?? '');
				$desc   = mysqli_real_escape_string($koneksi, 'SUKSES');

				if (empty($docnum)) {
					$sql = "INSERT INTO tr_api_logs 
								(docnum, doctype, raw_data, `desc`, result) 
							VALUES 
							('', 'PO', '$rawData', 'ERROR-" . ($desc ?: 'Docnum kosong') . "', '$resultDataLog')";
					mysqli_query($koneksi, $sql);
				} else {
					$sql = "INSERT INTO tr_api_logs 
								(docnum, doctype, raw_data, `desc`, result) 
							VALUES 
								('$docnum', 'PO', '$rawData', 'SUCCESS', '$resultDataLog')";
					mysqli_query($koneksi, $sql);

					if (!empty($ids) && is_array($ids)) {
						foreach ($ids as $id_po) {
							$id_po = (int)$id_po;
							$sql_update = "UPDATE tr_po 
										SET no_sap = '$docnum' 
										WHERE id_po = $id_po";
							mysqli_query($koneksi, $sql_update);
						}
					}
					$success = true;
				}
			}

			echo json_encode([
				"success" => $success,
				"data"    => $apiResponse,
				"message" => $mssg,
				"sent"    => $resultData
			], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

	}
	else if ($_POST['type'] == "Executed") {
		if($_POST['id'] != '' ){
			$id = $_POST['id'];
			$sql = "UPDATE tr_po SET 
					`status` = '1'
					WHERE id_po = '$id'	";
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
	elseif ($_POST['type'] == "EditData") {
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		$id = $_POST['id'];
		if ($_POST['jenis'] === "route") {
			$query = "SELECT 
						tr_po_detail.id,
						tr_po_detail.item AS id_item,
						CONCAT(m_asal.nama_kota, ' - ', m_tujuan.nama_kota) AS item, 
						tr_po_detail.itemcode,
						tr_po_detail.description,
						tr_po_detail.container,
						tr_po_detail.qty,
						tr_po_detail.uom,
						tr_po_detail.cur,
						tr_po_detail.harga,
						tr_po_detail.disc,
						tr_po_detail.ppn,
						tr_po_detail.nominal_ppn,
						tr_po_detail.total,
						tr_po_detail.jenis,
						m_asal.id_kota AS id_asal,
						m_tujuan.id_kota AS id_tujuan
					FROM tr_po_detail
					LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_po_detail.origin 
					LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_po_detail.destination 
					WHERE tr_po_detail.id = '$id'";			
		} elseif ($_POST['jenis'] === "item") {
			$query = "SELECT 
						tr_po_detail.id,
						tr_po_detail.item AS id_item,
						tr_po_detail.itemcode AS item,
						tr_po_detail.itemcode,
						tr_po_detail.description,
						tr_po_detail.container,
						tr_po_detail.qty,
						tr_po_detail.uom,
						tr_po_detail.cur,
						tr_po_detail.harga,
						tr_po_detail.disc,
						tr_po_detail.ppn,
						tr_po_detail.nominal_ppn,
						tr_po_detail.total,
						tr_po_detail.jenis
					FROM tr_po_detail
					WHERE tr_po_detail.id = '$id'";		
		} else {
			$query = "SELECT 
						tr_po_detail.id,
						tr_po_detail.item AS id_item,
						tr_po_detail.itemcode AS item,
						tr_po_detail.itemcode,
						tr_po_detail.description,
						tr_po_detail.container,
						tr_po_detail.qty,
						tr_po_detail.uom,
						tr_po_detail.cur,
						tr_po_detail.harga,
						tr_po_detail.disc,
						tr_po_detail.ppn,
						tr_po_detail.nominal_ppn,
						tr_po_detail.total,
						tr_po_detail.jenis
					FROM tr_po_detail
					WHERE tr_po_detail.id = '$id'";	
		}

		$dataQuery 	 = mysqli_query($koneksi, $query);
		$data   	 = mysqli_fetch_assoc($dataQuery);

		$id   		 = strtolower($data['id'] ?? '');
		$id_item     = strtolower($data['id_item'] ?? '');
		$item    	 = strtolower($data['item'] ?? '');
		$itemcode    = strtolower($data['itemcode'] ?? '');
		$description = strtolower($data['description'] ?? '');
		$container   = strtolower($data['container'] ?? '');
		$qty 		 = strtolower($data['qty'] ?? '');
		$uom 		 = strtolower($data['uom'] ?? '');
		$cur 		 = strtolower($data['cur'] ?? '');
		$harga 	 	 = strtolower($data['harga'] ?? '');
		$disc 	 	 = strtolower($data['disc'] ?? '');
		$ppn 	 	 = strtolower($data['ppn'] ?? '');
		$nominal_ppn = strtolower($data['nominal_ppn'] ?? '');
		$total 	 	 = strtolower($data['total'] ?? '');
		$jenis 		 = strtolower($data['jenis'] ?? '');
		$id_asal     = strtolower($data['id_asal'] ?? '');
		$id_tujuan   = strtolower($data['id_tujuan'] ?? '');

		echo json_encode([
			"id" 		 => $id,
			"id_item"	 => $id_item,
			"item"	 	 => $item,
			"itemcode"	 => $itemcode,
			"description"=> $description,
			"container"  => $container,
			"qty" 		 => $qty,
			"uom" 		 => $uom,
			"cur" 		 => $cur,
			"harga" 	 => $harga,
			"disc" 		 => $disc,
			"ppn" 		 => $ppn,
			"nominal_ppn"=> $nominal_ppn,
			"total" 	 => $total,
			"jenis" 	 => $jenis,
			"id_asal" 	 => $id_asal,
			"id_tujuan"  => $id_tujuan,
		]);
		exit;
	}

// ================= DETAIL DATA POTR =================
	else if ($_GET['type'] == "AddProject"){
		$query_project = "SELECT * 
				FROM sap_project 
				ORDER BY CAST(SUBSTRING_INDEX(kode_project, ' ', -1) AS UNSIGNED) DESC 
				LIMIT 1
			";
		$run_project  = mysqli_query($koneksi, $query_project);
		$data_project = mysqli_fetch_assoc($run_project);

		$lastKode = $data_project['kode_project'];

		preg_match('/^(.*?)(\d+)$/', $lastKode, $matches);
		$prefix = trim($matches[1]);
		$number = (int)$matches[2];
		$date	= date('Y');

		$newKode = $date . '/TRC ' . ($number + 1);
		// $newKode = '2025/TRC 1282';

		$sendAPI = [
			"ProjectCode" => $newKode,
			"ProjectName" => "",
		];

		// Kirim ke API
		$apiUrl = "https://wsp.mitraadipersada.com/trucking/project.php";
		$ch = curl_init($apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendAPI));

		$response = curl_exec($ch);
		// -------------- SHOW RESPONS --------------
			// echo "<pre>";
			// print_r(json_decode($response, true));
			// echo "</pre>";
			// die();

		if ($response === false) {
			echo "Curl error: " . curl_error($ch);
			curl_close($ch);
			die();
		}

		curl_close($ch);

		$apiResponse = json_decode($response, true);
		$rawData     = json_encode($sendAPI, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$mssg        = isset($apiResponse['mssg']) ? $apiResponse['mssg'] : 'No message';

		if (!$apiResponse || (isset($apiResponse['returncode']) && $apiResponse['returncode'] != 200)) {
			mysqli_query($koneksi, "INSERT INTO tr_api_logs (
					docnum, doctype, raw_data, `desc`
				) VALUES (
					NULL, NULL,
					'" . mysqli_real_escape_string($koneksi, $rawData) . "', 
					'ERROR-" . mysqli_real_escape_string($koneksi, $mssg) . "'
				)");

			$rowid = null;
		} else {
			mysqli_query($koneksi, "INSERT INTO tr_api_logs (
					docnum, doctype, raw_data, `desc`
				) VALUES (
					NULL, NULL,
					'" . mysqli_real_escape_string($koneksi, $rawData) . "', 
					'SUCCESS-" . mysqli_real_escape_string($koneksi, $mssg) . "'
				)");

			mysqli_query($koneksi, "INSERT INTO sap_project (
					kode_project
				) VALUES (
					'" . mysqli_real_escape_string($koneksi, $newKode) . "'
				)");

			$result = mysqli_query($koneksi, "SELECT rowid 
											FROM sap_project 
											WHERE kode_project = '" . mysqli_real_escape_string($koneksi, $newKode) . "' 
											ORDER BY rowid DESC 
											LIMIT 1");
			$row    = mysqli_fetch_assoc($result);
			$rowid  = $row['rowid'];
		}

		echo json_encode([
			"data"    => $apiResponse,
			"newKode" => $newKode,
			"rowid"   => $rowid
		], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}
	elseif ($_POST['type'] == "checkPayment") {
		$id = mysqli_real_escape_string($koneksi, $_POST['id_vendor']);
		$q_check = "SELECT
						m_vendor_bank_tr.nama_rek,
						CONCAT(m_vendor_bank_tr.nama_bank, ' - ', m_vendor_bank_tr.no_rek) AS payment
					FROM m_vendor_tr
					LEFT JOIN m_vendor_bank_tr 
						ON m_vendor_bank_tr.caption = m_vendor_tr.caption
					WHERE m_vendor_tr.id_vendor = '$id'
					LIMIT 1";

		// echo $q_check;
		// exit;

		$res = mysqli_query($koneksi, $q_check);

		if ($row = mysqli_fetch_assoc($res)) {
			echo json_encode([
				"status"   => "success",
				"payment"  => $row['payment'],
				"nama_rek" => $row['nama_rek']
			]);
		} else {
			echo json_encode([
				"status"  => "error",
				"payment" => "",
				"nama_rek"=> ""
			]);
		}
		exit;
	}
	elseif ($_POST['type'] == "checkPPN") {
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		$id = mysqli_real_escape_string($koneksi, $_POST['id_vendor']);
		$q_check = "SELECT
						ppn
					FROM m_vendor_tr
					WHERE m_vendor_tr.id_vendor = '$id'
					LIMIT 1";

		$res = mysqli_query($koneksi, $q_check);

		if ($row = mysqli_fetch_assoc($res)) {
			echo json_encode([
				"status" => "success",
				"ppn"  	 => (float)$row['ppn']
			]);
		} else {
			echo json_encode([
				"status" => "error",
				"ppn"	 => 0
			]);
		}
		exit;
	}

// ================= SEND AP TO SAP =================
	else if ($_GET['type'] == "ListAP") {
		$cari   = mysqli_real_escape_string($koneksi, $_GET['cari']);
		$id_po  = mysqli_real_escape_string($koneksi, $_GET['id_po']);

		$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead>
				<tr>
					<th width="5%" style="text-align: center;">NO</th>
					<th width="15%" style="text-align: center;">Date</th>
					<th width="15%" style="text-align: center;">SAP Project</th>
					<th width="15%" style="text-align: center;">No PO</th>
					<th width="27%" style="text-align: center;">Nama Customer</th>
					<th width="10%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';

		$sql_po = "SELECT 
					tr_po.*
				FROM tr_po 
				LEFT JOIN tr_po_detail ON tr_po_detail.code_po = tr_po.code_po
				WHERE tr_po.status = '0' 
					AND tr_po.id_po = '$id_po' 
					AND tr_po.no_sap IS NOT NULL
				LIMIT 1";

		$query_po = mysqli_query($koneksi, $sql_po);

		if ($query_po && mysqli_num_rows($query_po) > 0) {
			$dp         = mysqli_fetch_assoc($query_po);
			$tgl_po     = $dp['delivery_date'];
			$sap_project= $dp['sap_project'];
			$id_cust    = $dp['id_cust'];
			$id_asal    = $dp['id_asal'];
			$id_tujuan  = $dp['id_tujuan'];
		} else {
			$tgl_po = $sap_project = $id_cust = null;
		}

		$SQL = "SELECT 
					tr_po.id_po,
					tr_po.delivery_date,
					sap_project.kode_project,
					tr_po.code_po,
					m_cust_tr.nama_cust
				FROM tr_po
				LEFT JOIN tr_po_detail ON tr_po_detail.code_po = tr_po.code_po
				LEFT JOIN sap_project ON sap_project.rowid = tr_po.sap_project
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_po.user_req
				WHERE tr_po.id_po = '$id_po'
				GROUP BY tr_po.id_po
				LIMIT 0, 10";
				
		$query = mysqli_query($koneksi, $SQL);	
		if (!$query) {
			exit(mysqli_error($koneksi));
		}

		$posisi = 0;
		if (mysqli_num_rows($query) > 0) {
			while($row = mysqli_fetch_assoc($query)) {	
				$posisi++;
				$data .= '<tr>';		
				$data .= '<td style="text-align:center">'.$posisi.'</td>';
				$data .= '<td style="text-align:center">'.$row['delivery_date'].'</td>';
				$data .= '<td style="text-align:center">'.$row['kode_project'].'</td>';
				$data .= '<td style="text-align:center">'.$row['code_po'].'</td>';
				$data .= '<td style="text-align:center">'.$row['nama_cust'].'</td>';

				$id_po = isset($row['id_po']) ? $row['id_po'] : $row['code_po'];
				$data .= '<td style="text-align:center">
							<label>
							<input type="checkbox" 
									name="ap_selected[]" 
									value="'.$id_po.'">
							</label>
						</td>';

				$data .= '</tr>';
			}		
		} else {
			$data .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan</td></tr>';
		}

		$data .= '</table>';
		echo $data;			
	}
	else if ($_POST['type'] == "SaveAP") {
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		$ids = isset($_POST['ids']) ? $_POST['ids'] : [];

		if (empty($ids)) {
			echo json_encode([
				"success" => false,
				"message" => "Tidak ada data yang dipilih"
			]);
			exit;
		}

		$resultData = [
			"VendorCode" => null,
			"TglAP"      => date('Y-m-d'),
			"Lines"      => []
		];

		foreach ($ids as $id_po) {
			$id_po = mysqli_real_escape_string($koneksi, $id_po);

			// PERBAIKAN QUERY
			$sql_header = "SELECT 
							tr_po.*, 
							m_vendor_tr.caption 
						FROM tr_po
						LEFT JOIN m_vendor_tr ON m_vendor_tr.id_vendor = tr_po.user_req
						WHERE tr_po.id_po = '$id_po'";

			$query_header = mysqli_query($koneksi, $sql_header);

			if (!$query_header) {
				die(json_encode([
					"success" => false,
					"message" => "Query gagal: " . mysqli_error($koneksi),
					"sql" => $sql_header
				]));
			}

			$row = mysqli_fetch_assoc($query_header);

			if ($row) {
				if ($resultData["VendorCode"] === null) {
					$resultData["VendorCode"] = $row['caption'] ?? '';
				}

				$resultData["Lines"][] = [
					"NoPO" => $row['code_po'] 
				];
			}
		}

		$output = [$resultData];


		// ============= NO SEND API (LIHAT JSON) =============
			// header('Content-Type: application/json');
			// echo "<pre>";
			// echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			// echo "<pre>";
			// die();

		// ============= KIRIM API ============= 
			// $apiUrl = "https://wsp.mitraadipersada.com/trucking/sales-invoice.php";
			$apiUrl = "http://192.168.1.153/trucking2/purch-invoice.php";

			$ch = curl_init($apiUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json'
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($output));

			$response = curl_exec($ch);
			curl_close($ch);
			
			// ----------- CHECK RESPONS -----------
				// $data = json_decode($response, true);
				// echo "<pre>";
				// print_r($data);
				// echo "</pre>";
				// die();

			$apiResponse = json_decode($response, true);
			$resultDataLog = json_encode($apiResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			$rawData = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

			if (!$apiResponse || !isset($apiResponse['docnum'])) {
				$success = false;
				$mssg    = $apiResponse['mssg'] ?? 'Invalid API response';
				mysqli_query($koneksi, "INSERT INTO tr_api_logs (docnum, doctype, raw_data, `desc`, result) 
					VALUES (
						'', 
						'AP TR', 
						'" . mysqli_real_escape_string($koneksi, $rawData) . "', 
						'ERROR-" . mysqli_real_escape_string($koneksi, $mssg) . "',
						'" . mysqli_real_escape_string($koneksi, $resultDataLog) . "'
					)
				");
			} else {
				$success = true;
				$desc    = 'SUCCESS';
				mysqli_query($koneksi, "INSERT INTO tr_api_logs (docnum, doctype, raw_data, `desc`, result) 
					VALUES (
						'" . mysqli_real_escape_string($koneksi, $apiResponse['docnum']) . "', 
						'AP TR', 
						'" . mysqli_real_escape_string($koneksi, $rawData) . "', 
						'" . mysqli_real_escape_string($koneksi, $desc) . "',
						'" . mysqli_real_escape_string($koneksi, $resultDataLog) . "'
					)
				");

				foreach ($ids as $id_po) {
					$id_po 	= (int)$id_po;
					$docnum = mysqli_real_escape_string($koneksi, $apiResponse['docnum']);
					$tgl_ap = date('Y-m-d');

					$sql_update = "UPDATE tr_po SET 
							no_ap 	= '$docnum',
							tgl_ap 	= '$tgl_ap'
						WHERE id_po = '$id_po'";
					mysqli_query($koneksi, $sql_update);
				}
			}
			
			echo json_encode([
				"success" => $success,
				"message" => $apiResponse['mssg'] ?? ($success ? "Berhasil" : "Gagal tanpa pesan"),
				"sent"    => $resultDataLog
			], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}


// ================= PO WH =================
	if ($_GET['type'] == "ReadWH"){
		$cari = trim($_GET['cari']);
		$hal = $_GET['hal'];
		$paging = $_GET['paging'];
		
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>	
						<th width="3%" style="text-align: center;">NO</th>
						<th width="10%" style="text-align: center;">SAP PROJECT</th>
						<th width="10%" style="text-align: center;">CODE PR</th>
						<th width="10%" style="text-align: center;">CODE PO</th>
						<th width="7%" style="text-align: center;">REQ DATE</th>
						<th width="15%" style="text-align: center;">USER REQ</th>					
						<th width="7%" style="text-align: center;">BUYER</th>					
						<th width="7%" style="text-align: center;">PAYMENT</th>					
						<th width="35%" style="text-align: center;">REMARK</th>			
						<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>	
						<th width="5%" style="text-align: center;">EXEC</th>				
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
					tr_po.*,
					sap_project.kode_project,
					m_cust_tr.nama_cust
				FROM tr_po 
				LEFT JOIN sap_project ON sap_project.rowid = tr_po.sap_project
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_po.user_req
				WHERE tr_po.code_po LIKE '%$cari%'
				AND tr_po.code_po LIKE '%POWH%'
				ORDER BY tr_po.code_po DESC
				LIMIT $offset, $jmlperhalaman";	

		$query = mysqli_query($koneksi, $SQL);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}

		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result))
			{	
				$xy1	= "Edit|$row[id_po]";
				$xy1	= base64_encode($xy1);
				$link 	= "po_wh_data.php?id=$xy1";

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
				$data .= '<tr><td style="text-align:center">'.$posisi.'.</td>';
				if ($row['no_sap']!= null && $row['status'] === '1' && $row['no_ap']!= null) {
					$data .= '<td style="text-align:center">
						'.$row['kode_project'].'<br>
						PO '.$row['no_sap'].'<br>
						AP '.$row['no_ap'].'
					</td>';
				}elseif ($row['no_sap'] === null && $row['status'] === '1'){
					$data .= '<td style="text-align:center">
						'.$row['kode_project'].'<br>
						<a href="javascript:void(0);" onclick="TampilUpSAP(\''.$row['id_po'].'\')">Send PO <br>
						</a>
						'.$row['no_sap'].'
					</td>';
				} elseif ($row['no_sap'] != null && $row['status'] === '1' && $row['no_ap'] === null) {
					$data .= '<td style="text-align:center">
						'.$row['kode_project'].'<br>
						PO '.$row['no_sap'].'<br>
						<a href="javascript:void(0);" onclick="TampilUpAP(\''.$row['id_po'].'\')">Send AP<br>
						</a>
					</td>';
				} else{
					$data .= '<td style="text-align:center">
						'.$row['kode_project'].'
					</td>';
				}

				$data .= '
					<td style="text-align:center">'.$row['code_pr'].'</td>
					<td style="text-align:center"><a href="'.$link.'">'.$row['code_po'].'</a></td>
					<td style="text-align:center">'.$row['delivery_date'].'</td>
					<td style="text-align:center">'.$row['nama_cust'].'</td>
					<td style="text-align:center">'.$row['buyer'].'</td>
					<td style="text-align:center">'.$row['payment'].'</td>
					<td style="text-align:center">'.$row['remark'].'</td>
					<td style="text-align:center">
						<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
					</td>';

				if($row['status'] == '0' ) {
					$data .= '<td>
							<button class="btn btn-block btn-default" title="Execute" style="margin:-3px;border-radius:0px" type="button" onClick="javascript:Confirm('.$row['id_po'].')">
								<span class="fa fa-check-square-o"></span>
							</button></td>';
						
				}
				$data .='</tr>';

				$number++;
			}		
		}
		else
		{
			$data .= '<tr><td colspan="9" style="text-align:center">Records not found!</td></tr>';
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
?>