<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "SELECT * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='12' ");
$rq	= mysqli_fetch_array($pq);	

$m_edit 	= $rq['m_edit'];
$m_add 		= $rq['m_add'];
$m_del 		= $rq['m_del'];
$m_view 	= $rq['m_view'];
$m_exe 		= $rq['m_exe'];
$m_approval = $rq['m_approval'];

if ($_GET['type'] == "Read"){
	$cari = trim($_GET['cari']);
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	$tgl1 = $_GET['tgl1'];
	$tgl2 = $_GET['tgl2'];
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);	
	$field = $_GET['field'];
	
	if($field == 'Item Number')
	{
		$f = 'kode';	
	}else if($field == 'Description'){
		$f = 'nama';	
	}else{
		$f = 'nama';	
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="10%" style="text-align: center;">NO SAP</th>
					<th rowspan="2" width="9%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="50%" style="text-align: center;">ITEM DESCRIPTION</th>
					<th rowspan="2" width="4%" style="text-align: center;">UoM</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>
					<th colspan="3" width="9%" style="text-align: center;">STOCK</th>
				</tr>
				<tr>
					<th  width="3%" style="text-align: center;">IN</th>
					<th  width="3%" style="text-align: center;">OUT</th>
					<th  width="3%" style="text-align: center;">WHSE</th>
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
	
	$SQL = "SELECT * from m_part where $f LIKE '%$cari%' 	
			order by nama LIMIT $offset, $jmlperhalaman";
			
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			$sisa  = $row['masuk'] - $row['keluar'];
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">KODE SAP</td>
				<td style="text-align:left">'.$row['kode'].'</td>
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['unit'].'</td>';					
				
				if($m_edit == '1' ){
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_part'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}else{
					$data .='<td></td>';
				}
				
				$data .= '<td style="text-align:right">
					<button class="btn btn-block btn-primary"  
						style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button" 
						onClick="javascript:DownloadIn('.$row['id_part'].')"  >
						'.$row['masuk'].'
					</button>
				</td>';

				$data .= '<td style="text-align:right">
					<button class="btn btn-block btn-warning"  
						style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button" 
						onClick="javascript:DownloadOut('.$row['id_part'].')"  >
						'.$row['keluar'].'
					</button>
				</td>';
				
				if($sisa <= 0){
					$data .= '<td style="text-align:right">
					<button class="btn btn-block btn-danger"  
						style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button"   >
						'.$sisa.'
					</button>
					</td>';
				}else{
					$data .= '<td style="text-align:right">
					<button class="btn btn-block btn-success"  
						style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button"   >
						'.$sisa.'
					</button>
					</td>';
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
				$pq = mysqli_query($koneksi, "SELECT count(id_part) as jml from m_part where $f LIKE '%$cari%'   ");					
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
else if ($_POST['type'] == "Del_Data"){
	$id = $_POST['id']; 	
	
    $query = "DELETE FROM m_part WHERE id_part = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }
	
}
else if ($_POST['type'] == "Add_Data"){
	if($_POST['mode'] != '' )
	{	

		$id = $_POST['id'];
		$kode = addslashes(trim(strtoupper($_POST['kode'])));
		$nama = addslashes(trim($_POST['nama']));	
		$unit = addslashes(trim($_POST['unit']));	
		$mode = $_POST['mode'];
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO m_part (kode, nama, unit) values
					('$kode', '$nama', '$unit')";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update m_part set 
					kode = '$kode',
					nama = '$nama',
					unit = '$unit'
					where 	id_part = '$id'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        			
			//exit(mysqli_error($koneksi));
			echo "Data Error...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
	
	
}
else if ($_POST['type'] == "Detil_Data"){
	$id = $_POST['id'];	
    $query = "SELECT * FROM tr_po_detail where id  = '$id'";
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

else if ($_POST['type'] == "Detil_Data_car"){

	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// exit;

	$id = $_POST['id'];	
	
    $query = "SELECT * FROM m_part where id_part  = '$id'";
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

// ========== PART IN ==========
else if ($_GET['type'] == "Read_In"){
	$cari 	= trim($_GET['cari']);
	$hal 	= $_GET['hal'];
	$paging = $_GET['paging'];
	$tgl1 	= $_GET['tgl1'];
	$tgl2 	= $_GET['tgl2'];
	$tgl1x 	= ConverTglSql($tgl1);
	$tgl2x 	= ConverTglSql($tgl2);	
	$field 	= $_GET['field'];
	
	if($field == 'Item Number')
	{
		$f = 'kode';	
	}else if($field == 'Description'){
		$f = 'nama';	
	}else{
		$f = 'nama';	
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="10%" style="text-align: center;">NO GRPO</th>
					<th rowspan="2" width="10%" style="text-align: center;">NO PO</th>
					<th rowspan="2" width="10%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="50%" style="text-align: center;">DESCRIPTION</th>
					<th rowspan="2" width="5%" style="text-align: center;">QTY</th>
					<th rowspan="2" width="5%" style="text-align: center;">UNIT</th>
					<th rowspan="2" width="7%" style="text-align: center;">CREATED</th>
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
				m_part_masuk.*, 
				m_part.nama, 
				m_part.kode, 
				m_part.unit
			FROM  m_part_masuk 
			LEFT JOIN m_part ON m_part_masuk.id_part = m_part.id_part 
			WHERE m_part_masuk.tanggal BETWEEN '$tgl1x' AND '$tgl2x' AND $f LIKE '%$cari%' 
			ORDER BY m_part_masuk.tanggal DESC 
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
			$tanggal = ConverTgl($row['tanggal']);
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>';

			if ($row['no_grpo'] == null) {
				$data .= '<td style="text-align:center">
							'.$row['no_po'].'<br>
							<a href="javascript:void(0);" onclick="TampilUpGRPO(\''.$row['id_masuk'].'\')">Send GRPO<br>
							</a>
						</td>';
			} else {
				$data .= '<td style="text-align:center">
							'.$row['no_po'].'<br>
							GRPO '.$row['no_grpo'].'<br>
						</td>';
			}

			$data .= '<td style="text-align:center">
						'.$row['no_po'].'<br>
					</td>';
			
			$data .= '<td style="text-align:center">'.$row['kode'].'</td>
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['qty'].'</td>
				<td style="text-align:center">'.$row['unit'].'</td>
				<td style="text-align:center">'.$row['created'].'</td>';					
				
				// if($m_del == '1x' ){
				// 	$data .= '<td>
				// 				<button class="btn btn-block btn-default" title="Edit"
				// 					style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
				// 					onClick="javascript:DelData('.$row['id_masuk'].')"  >
				// 					<span class="fa fa-close " ></span>
				// 				</button></td>';
				// }else{
				// 	$data .='<td></td>';
				// }
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
				$pq = mysqli_query($koneksi, "select count(m_part_masuk.id_masuk) as jml
				from  m_part_masuk left join m_part on m_part_masuk.id_part = m_part.id_part 
				where m_part_masuk.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'");					
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
else if ($_POST['type'] == "Add_In"){

	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// exit;

	if (!empty($_POST['mode'])) {	
		$tanggal    = ConverTglSql($_POST['tanggal']);
		$id_part_po = $_POST['id_part'];
		$no_po      = $_POST['no_po'];
		$qty        = str_replace(",", "", $_POST['qty']);
		$ket        = addslashes(trim(strtoupper($_POST['ket'])));
		$id_user    = $_SESSION['id_user'];

		$q_part = "SELECT 
				m_cost_tr.id_cost,
				m_cost_tr.nama_cost,
				m_cost_tr.uom,
				m_cost_tr.itemcode,
				tr_po_detail.qty_pending
			FROM tr_po_detail
			LEFT JOIN m_cost_tr ON tr_po_detail.itemcode = m_cost_tr.itemcode
			WHERE tr_po_detail.id = '$id_part_po'
		";
		$r_part = mysqli_query($koneksi, $q_part);
		$d_part = mysqli_fetch_assoc($r_part);

		if (!$d_part) {
			echo "Data PO Detail tidak ditemukan.";
			exit;
		}

		$itemcode     = $d_part['itemcode'];
		$qty_pending  = (float)$d_part['qty_pending'];
		$uom     	  = $d_part['uom'];
		$itemname     = $d_part['nama_cost'];

		if ($qty > $qty_pending) {
			echo "Qty melebihi sisa PO!";
			exit;
		}

		$q_cost  = "SELECT id_part FROM m_part WHERE kode = '$itemcode' LIMIT 1";
		$r_cost  = mysqli_query($koneksi, $q_cost);
		$d_cost  = mysqli_fetch_assoc($r_cost);

		if ($d_cost) {
			$id_part_new = $d_cost['id_part'];
		} else {

			$i_part = "INSERT INTO m_part (kode, nama, unit)
				VALUES ('$itemcode', '$itemname', '$uom')
			";
			$insert_part = mysqli_query($koneksi, $i_part);

			if (!$insert_part) {
				die('Gagal insert ke m_part: ' . mysqli_error($koneksi));
			}

			$id_part_new = mysqli_insert_id($koneksi);
		}

		$u_part = "UPDATE m_part
			SET masuk = COALESCE(masuk, 0) + $qty
			WHERE id_part = '$id_part_new'
		";
		mysqli_query($koneksi, $u_part);

		$u_po_detail = "UPDATE tr_po_detail
			SET qty_pending = COALESCE(qty_pending, 0) - $qty
			WHERE itemcode = '$itemcode'
		";
		mysqli_query($koneksi, $u_po_detail);

		$i_masuk = "INSERT INTO m_part_masuk (id_part, tanggal, no_po, qty, created)
			VALUES ('$id_part_new', '$tanggal', '$no_po', '$qty', '$id_user')
		";
		mysqli_query($koneksi, $i_masuk);

		echo "Data berhasil disimpan.";
	}

	
}
else if ($_POST['type'] == "Del_In"){
	$id = $_POST['id']; 	
	
	$pq = mysqli_query($koneksi,"select * from m_part_masuk where id_masuk = '$id' ");
	$rq=mysqli_fetch_array($pq);

	$sql = "update m_part set masuk = masuk - '$rq[qty]' where id_part = '$rq[id_part]' ";
	$hasil=mysqli_query($koneksi, $sql);
			
    $query = "DELETE FROM m_part_masuk WHERE id_masuk = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }	

}


// ========== PART OUT ==========
else if ($_GET['type'] == "Read_Out"){

	// echo $m_approval;
	// exit;

	$cari 	= trim($_GET['cari']);
	$hal 	= $_GET['hal'];
	$paging = $_GET['paging'];
	$tgl1 	= $_GET['tgl1'];
	$tgl2 	= $_GET['tgl2'];
	$tgl1x 	= ConverTglSql($tgl1);
	$tgl2x 	= ConverTglSql($tgl2);	
	$field 	= $_GET['field'];
	
	if($field == 'Item Number'){
		$f = 'm_part.kode';	
	}else if($field == 'Description'){
		$f = 'm_part.nama';	
	}else if($field == 'No Polisi'){
		$f = 'm_mobil_tr.no_polisi';	
	}else if($field == 'No SPK'){
		$f = 't_spk.no_spk';	
	}else{
		$f = 'nama';	
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="10%" style="text-align: center;">NO GI</th>
					<th rowspan="2" width="10%" style="text-align: center;">NO. SPK</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. POLICE</th>
					<th rowspan="2" width="10%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="45%" style="text-align: center;">ITEM DESCRIPTION</th>
					<th rowspan="2" width="5%" style="text-align: center;">QTY</th>
					<th rowspan="2" width="5%" style="text-align: center;">UNIT</th>
					<th rowspan="2" width="7%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="5%" style="text-align: center;">APPROVE</th>
				</tr>
			</thead>';		

	if(!isset($_GET['hal'])){ 
		$page 	= 1;       
	} else { 
		$page 	= $_GET['hal']; 
		$posisi	= 0;
	}

	$jmlperhalaman = $paging;
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman); 	
	
	$q_part = "SELECT 
				t_spk_part.*, 
				t_spk_part.no_gi, 
				t_spk.no_spk, 
				t_spk.tanggal, 
				t_spk.created, 
				m_mobil_tr.no_polisi,
				m_part.kode, 
				m_part.nama, 
				m_part.unit
			FROM t_spk_part 
			LEFT JOIN t_spk ON t_spk_part.id_spk = t_spk.id_spk 
			LEFT JOIN m_mobil_tr ON t_spk.id_mobil = m_mobil_tr.id_mobil
			LEFT JOIN m_part ON t_spk_part.id_part = m_part.id_part
			WHERE t_spk.tanggal BETWEEN '$tgl1x' AND '$tgl2x' AND $f LIKE '%$cari%' 
			ORDER BY t_spk_part.id_detil DESC LIMIT $offset, $jmlperhalaman";
			
	$query = mysqli_query($koneksi, $q_part);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }

    if(mysqli_num_rows($result) > 0){
    	while($row = mysqli_fetch_assoc($result)){	
			$posisi++;	
			$tanggal = ConverTgl($row['tanggal']);
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>
				';

			if ($row['approval'] == '1' && $m_add == '1') {
				if (empty($row['no_gi'])) {
					$data .= '<td style="text-align:center">
								<a href="javascript:void(0);" onclick="tampilGI(\'' . $row['id_detil'] . '\')">
									Send GI
								</a>
							</td>';
				} else {
					$data .= '<td style="text-align:center">
							GI ' . htmlspecialchars($row['no_gi']) . '
						</td>';
				}
			} else {
				if (empty($row['no_gi'])) {
					$data .= '<td style="text-align:center">
								-
							</td>';
				} else {
					$data .= '<td style="text-align:center">
							GI ' . htmlspecialchars($row['no_gi']) . '
						</td>';
				}
			}
			
			
			$data .= '<td style="text-align:center">'.$row['no_spk'].'</td>
				<td style="text-align:center">'.$row['no_polisi'].'</td>
				<td style="text-align:center">'.$row['kode'].'</td>
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['qty'].'</td>
				<td style="text-align:center">'.$row['unit'].'</td>
				<td style="text-align:center">'.$row['created'].'</td>';		
			
			
			if ($m_approval == '1') {
				if ($row['approval'] == '0') {
					$data .= '<td style="text-align:center">
						<button class="btn btn-primary btnApproveGI" onclick="ApproveGI(\'' . $row['id_detil'] . '\')">
							Approve GI
						</button>
					</td>';

				} else {
					$data .= '<td style="text-align:center">
								<button class="btn btn-success btnApproveGI">
									Approve GI
								</button>
							</td>';
				}
			} else {
				if ($row['approval'] == '1') {
					$data .= '<td style="text-align:center">
								<button class="btn btn-primary" >
								Approved
							</button>
							</td>';
				} else {
					$data .= '<td style="text-align:center">
								-
							</td>';
				}
			}

			
				
			$data .='</tr>';
    		$number++;
    	}		
    }else{
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }

	// ========= PAGINATE =========
    $data .= '</table>';
	$data .= '<div class="paginate paginate-dark wrapper">
				<ul>';
				$q_part = "SELECT 
						count(t_spk_part.id_detil) as jml
					FROM t_spk_part 
					left join t_spk on t_spk_part.id_spk = t_spk.id_spk 
					left join m_mobil_tr on t_spk.id_mobil = m_mobil_tr.id_mobil
					left join m_part on t_spk_part.id_part = m_part.id_part
					where t_spk.tanggal 
						between '$tgl1x' 
						and '$tgl2x' 
						and $f LIKE '%$cari%'";

				$r_part = mysqli_query($koneksi,$q_part);			
				$rq		= mysqli_fetch_array($r_part);

				$total_record	= $rq['jml'];										
				$total_halaman 	= ceil($total_record / $jmlperhalaman);

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
else if ($_GET['type'] == "ListGI") {

	// echo "<pre>";
	// print_r($_GET);
	// echo "</pre>";
	// exit;

	$cari   	= mysqli_real_escape_string($koneksi, $_GET['cari']);
	$id_detail  = mysqli_real_escape_string($koneksi, $_GET['id_detil']);

	$data = '<table class="table table-hover table-striped" style="width:100%">
		<thead>
			<tr>
				<th width="5%" style="text-align: center;">NO</th>
				<th width="15%" style="text-align: center;">Date</th>
				<th width="15%" style="text-align: center;">No SPK</th>
				<th width="20%" style="text-align: center;">Itemcode</th>
				<th width="30%" style="text-align: center;">Itemname</th>
				<th width="5%" style="text-align: center;">Qty</th>
				<th width="5%" style="text-align: center;">ADD</th>
			</tr>
		</thead>';

	$q_part = "SELECT 
				t_spk.tanggal,
				t_spk.no_spk
			FROM t_spk_part
			LEFT JOIN t_spk ON t_spk.id_spk = t_spk_part.id_spk
			LEFT JOIN m_part ON m_part.id_part = t_spk_part.id_part
			INNER JOIN m_cost_tr ON m_cost_tr.itemcode = m_part.kode
			WHERE t_spk_part.id_detil = '$id_detail' 
				AND t_spk_part.no_gi IS NULL
				AND t_spk_part.approval = '1'
			LIMIT 1";

	$r_part = mysqli_query($koneksi, $q_part);

	if ($r_part && mysqli_num_rows($r_part) > 0) {
		$d_part	 = mysqli_fetch_assoc($r_part);
		$no_spk	 = $d_part['no_spk'];
		$tanggal = $d_part['tanggal'];
	} else {
		$no_spk = $tanggal = null;
	}

	$q_detail = "SELECT 
				t_spk_part.id_detil,
				t_spk.tanggal,
				t_spk.no_spk,
				m_cost_tr.itemcode,
				m_cost_tr.nama_cost,
				t_spk_part.qty
			FROM t_spk_part
			LEFT JOIN t_spk ON t_spk.id_spk = t_spk_part.id_spk
			LEFT JOIN m_part ON m_part.id_part = t_spk_part.id_part
			INNER JOIN m_cost_tr ON m_cost_tr.itemcode = m_part.kode
			WHERE t_spk.no_spk = '$no_spk'
				AND t_spk_part.no_gi IS NULL
				AND t_spk_part.approval = '1'
				ORDER BY t_spk.tanggal DESC
			LIMIT 0, 10";

	// echo $q_detail;
	// exit;
			
	$query = mysqli_query($koneksi, $q_detail);	
	if (!$query) {
		exit(mysqli_error($koneksi));
	}

	$posisi = 0;
	if (mysqli_num_rows($query) > 0) {
		while($row = mysqli_fetch_assoc($query)) {	
			$posisi++;
			$data .= '<tr>';		
			$data .= '<td style="text-align:center">'.$posisi.'</td>';
			$data .= '<td style="text-align:center">'.$row['tanggal'].'</td>';
			$data .= '<td style="text-align:center">'.$row['no_spk'].'</td>';
			$data .= '<td style="text-align:center">'.$row['itemcode'].'</td>';
			$data .= '<td style="text-align:left">'.$row['nama_cost'].'</td>';
			$data .= '<td style="text-align:center">'.$row['qty'].'</td>';

			$id_detil = isset($row['id_detil']) ? $row['id_detil'] : $row['id_detil'];
			$data .= '<td style="text-align:center">
						<label>
						<input type="checkbox" 
								name="sap_selected[]" 
								value="'.$id_detil.'">
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
else if ($_POST['type'] == "SaveUpGI") {
	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// exit;

	$ids = isset($_POST['ids']) ? $_POST['ids'] : [];

	if (empty($ids)) {
		echo json_encode([
			"success" => false,
			"message" => "Tidak ada data yang dipilih"
		]);
		exit;
	}

	// ===== Ambil Header (hanya 1 kali, dari id pertama) =====
	$sql_header = "SELECT 
			t_spk.no_spk,
			t_spk.tanggal,
			CONCAT(t_spk.jenis, ' - ', t_spk.ket) AS remark
		FROM t_spk_part
		LEFT JOIN t_spk ON t_spk.id_spk = t_spk_part.id_spk
		WHERE t_spk_part.id_detil = '" . $ids[0] . "'
		LIMIT 1
	";

	$q_header = mysqli_query($koneksi, $sql_header);
	if (!$q_header) {
		die("Query Header Error: " . mysqli_error($koneksi));
	}

	$row = mysqli_fetch_assoc($q_header);

	// ===== Siapkan array untuk semua lines =====
	$allLines = [];
	$resultData = [];

	foreach ($ids as $id_detil) {
		$sql_detail = "SELECT 
				m_cost_tr.itemcode,
				t_spk_part.qty,
				m_cost_tr.uom, 
				t_spk.no_spk,
				m_params_tr.gi_account,
				m_params_tr.gi_corporate,
				m_params_tr.gi_divisi,
				m_params_tr.gi_dept,
				m_params_tr.gi_activity,
				m_params_tr.gi_location
			FROM t_spk_part
			LEFT JOIN t_spk ON t_spk.id_spk = t_spk_part.id_spk
			LEFT JOIN m_part ON m_part.id_part = t_spk_part.id_part
			LEFT JOIN m_cost_tr ON m_cost_tr.itemcode = m_part.kode
			LEFT JOIN m_params_tr ON m_params_tr.id_param = '1'
			WHERE t_spk_part.id_detil = '$id_detil'
		";

		$q_detail = mysqli_query($koneksi, $sql_detail);
		if (!$q_detail) {
			die("Query Detail Error: " . mysqli_error($koneksi));
		}

		$rows_detail = mysqli_fetch_all($q_detail, MYSQLI_ASSOC);
		if (!$rows_detail) continue;

		foreach ($rows_detail as $det) {
			$allLines[] = [
				"ItemCode"   => $det['itemcode'] ?? '',
				"Qty"        => $det['qty'] ?? 0,
				"UoM" 		 => $det['uom'] ?? '',
				"Warehouse"  => "WH-FG",
				"NoGI" 		 => $det['no_spk'] ?? '',

				"GLAcct"     => $det['gi_account'] ?? '',
				"Corporate"  => $det['gi_corporate'] ?? '',
				"Divisi"     => $det['gi_divisi'] ?? '',
				"Department" => $det['gi_dept'] ?? '',
				"Activity"   => $det['gi_activity'] ?? '',
				"Location"   => $det['gi_location'] ?? ''
			];
		}
	}

	// ===== Gabungkan semua lines di bawah satu header =====
	$resultData[] = [
		"NoGI"      => $row['no_spk'],
		"TglGI"     => $row['tanggal'],
		"Remarks"   => $row['remark'],
		"Lines"     => $allLines
	];


	// ============== NO SEND API (LIHAT JSON) ==============
		// header('Content-Type: application/json');
		// echo "<pre>";
		// echo json_encode($resultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		// echo "<pre>";
		// exit;
		
	// ============== KIRIM API ==============
		// $apiUrl = "https://wsp.mitraadipersada.com/trucking/goods-issue.php";
		$apiUrl = "http://192.168.1.153/trucking2/goods-issue.php";

		$ch = curl_init($apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array_values($resultData)));

		$response = curl_exec($ch);
		curl_close($ch);
		
		// ============== CHECK RESPONS ==============
		// $data = json_decode($response, true);
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit;

		$apiResponse = json_decode($response, true);
		$resultDataLog = json_encode($apiResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$rawData = json_encode($resultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		if (!$apiResponse || !isset($apiResponse['docnum'])) {
			$success = false;
			$mssg    = $apiResponse['mssg'] ?? 'Invalid API response';
			mysqli_query($koneksi, "INSERT INTO tr_api_logs (docnum, doctype, raw_data, `desc`, result) 
				VALUES (
					'', 
					'GI TR', 
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
					'GI TR', 
					'" . mysqli_real_escape_string($koneksi, $rawData) . "', 
					'" . mysqli_real_escape_string($koneksi, $desc) . "',
					'" . mysqli_real_escape_string($koneksi, $resultDataLog) . "'
				)
			");

			foreach ($ids as $id_detil) {
				$id_detil = (int)$id_detil;
				$docnum = mysqli_real_escape_string($koneksi, $apiResponse['docnum']);
				$sql_update = "UPDATE t_spk_part SET 
								no_gi = '$docnum'
							WHERE id_detil = $id_detil";
				mysqli_query($koneksi, $sql_update);
			}
		}

		echo json_encode([
			"success" => $success,
			"message" => $apiResponse['mssg'] ?? ($success ? "Berhasil" : "Gagal tanpa pesan"),
			"sent"    => $resultData
		], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

}

// ========== PART GR ==========
else if ($_GET['type'] == "Read_GR"){
	// echo "<pre>";
	// print_r($_GET);
	// echo "</pre>";
	// exit;

	$cari 	= trim($_GET['cari']);
	$hal 	= $_GET['hal'];
	$paging = $_GET['paging'];
	$tgl1 	= $_GET['tgl1'];
	$tgl2 	= $_GET['tgl2'];
	$tgl1x 	= ConverTglSql($tgl1);
	$tgl2x 	= ConverTglSql($tgl2);	
	$field 	= $_GET['field'];
	
	if($field == 'NO GR WH'){
		$f = '';	
	}else if($field == 'Remark'){
		$f = '';	
	}else{
		$f = '';	
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
		<thead style="font-weight:500px !important">
			<tr>					
				<th rowspan="2" width="3%" style="text-align: center;">NO</th>
				<th rowspan="2" width="7%" style="text-align: center;">DATE</th>
				<th rowspan="2" width="7%" style="text-align: center;">NO GR</th>
				<th rowspan="2" width="30%" style="text-align: center;">REMARK</th>
				<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>
				<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>
				<th rowspan="2" width="2%" style="text-align: center;">EXEC</th>
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
	
	$q_part = "SELECT 
				m_part_gr.*
			FROM  m_part_gr 
			WHERE m_part_gr.tanggal BETWEEN '$tgl1x' AND '$tgl2x'
			ORDER BY 
				m_part_gr.tanggal DESC, 
				m_part_gr.no_grwh DESC
			LIMIT $offset, $jmlperhalaman";
			
	$query = mysqli_query($koneksi, $q_part);	
	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
	
    if(mysqli_num_rows($result) > 0){
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			$tanggal = ConverTgl($row['tanggal']);
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>';

			if ($row['status'] == '1' && $row['gr_sap'] == null) {
				$data .= '<td style="text-align:center">'
					. $row['no_grwh'] . '<br>'
					. '<a href="javascript:void(0);" 
							id="btnSendGR_'.$row['id_part_gr'].'" 
							class="btn-send-gr" 
							onclick="SendGR(\''.$row['id_part_gr'].'\', this)">
							Send GR
						</a>'
					. '</td>';
			}

			else if($row['status'] == '1' && $row['gr_sap'] != null){
				$data .= '<td style="text-align:center">
							'.$row['no_grwh'].'<br>
							GRSAP '.$row['gr_sap'].'<br>
						</td>';
			} 
			else {
				$data .= '<td style="text-align:center">
							'.$row['no_grwh'].'<br>
						</td>';
			}

			if($row['status'] == '0'){
				$label = 'primary';
				$status = 'Open';
				$dis = '';
			} else if($row['status'] == '1'){
				$label = 'success';
				$status = 'Close';
				$dis = 'Disabled';
			}
			$data .= '
				<td style="text-align:left">'.$row['remark'].'</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';			
				
			// if ($row['status'] === '0') {
				$mode = 'Edit';
				$xy1  = base64_encode($row['id_part_gr']);
				$link = "part_gr_data.php?id=$xy1&mode=$mode";

				$data .= '<td>
					<button class="btn btn-block btn-warning"
							title="Execute"
							style="margin:-3px;border-radius:0px"
							type="button"
							onclick="window.location.href=\'' . $link . '\'">
						<span class="fa fa-edit"></span>
					</button>
				</td>';
			// }

			if($row['status'] === '0' ) {
				$data .= '<td>
						<button class="btn btn-block btn-primary" 
								title="Execute" 
								style="margin:-3px;border-radius:0px" 
								type="button" 
								onClick="javascript:ExecPartGR('.$row['id_part_gr'].')">
							<span class="fa fa-check"></span>
						</button>
					</td>';
			}
				$data .='</tr>';
    		$number++;
    	}		
    }else{
    	$data .= '<tr><td colspan="8">Records not found!</td></tr>';
    }
    $data .= '</table>';
	
	$data .= '<div class="paginate paginate-dark wrapper">
				<ul>';
				$pq = mysqli_query($koneksi, "SELECT 
						COUNT(m_part_gr.id_part_gr) AS jml
					FROM m_part_gr 
					WHERE m_part_gr.tanggal BETWEEN '$tgl1x' AND '$tgl2x'
				");

				if (!$pq) {
					die("Query gagal: " . mysqli_error($koneksi));
				}

				$rq = mysqli_fetch_array($pq);


				$total_record 	= $rq['jml'];										
				$total_halaman 	= ceil($total_record / $jmlperhalaman);	

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
else if ($_GET['type'] == "ListPart_GR"){

	// echo "<pre>";
	// print_r($_GET);
	// echo "</pre>";
	// exit;

	$cari 	 = $_GET['cari'];
	$code_po = $_GET['code_po'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="5%" style="text-align: center;">NO</th>
					<th width="30%" style="text-align: center;">ITEMCODE</th>
					<th width="60%" style="text-align: center;">DESCRIPTION</th>
					<th width="5%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "SELECT 
				m_part.* 
			FROM m_part 
			INNER JOIN m_cost_tr on m_cost_tr.itemcode = m_part.kode
			WHERE m_part.nama LIKE '%$cari%' 
			ORDER BY m_part.kode LIMIT 0, 25";
	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result)){	
			$n++;
			$data .= '<tr>';
			$data .= '<td style="text-align:center">'.$n.'.</td>';	
			$data .= '<td style="text-align:left">
						<a href="#" onclick="PilihPart('.$row['id_part'].')" >'.$row['kode'].'</a>
					</td>';
			$data .= '<td style="text-align:left">
						<a href="#" onclick="PilihPart('.$row['id_part'].')" >'.$row['nama'].'</a>
					</td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihPart('.$row['id_part'].')" 
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
else if ($_POST['type'] == "Pilih_itemGR"){

	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// exit;

	$id_part = $_POST['id_part'];	
	
    $query = "SELECT 
			m_part.id_part, 
			m_cost_tr.itemcode, 
			m_cost_tr.uom, 
			m_part.nama
		FROM m_part 
		INNER JOIN m_cost_tr ON m_cost_tr.itemcode = m_part.kode
		WHERE m_part.id_part  = '$id_part'";

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
else if ($_POST['type'] == "Add_GR") {
	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// exit;

    if (!empty($_POST['mode'])) {	
		$id_part_gr   	= $_POST['id_part_gr'];
		$id_detail_gr   = $_POST['id_detail_gr'];
		$id_part   		= $_POST['id_part'];
		$gr_coa   		= $_POST['gr_coa'];
		$qty       		= str_replace(",", "", $_POST['qty']);
		$id_user   		= $_SESSION['id_user'];

		if ($_POST['mode'] == 'Add') {
			$q_partGR = "INSERT INTO m_part_gr_detail (id_part_gr, id_part,qty, gr_coa)
				VALUES ('$id_part_gr', '$id_part', '$qty', '$gr_coa')
			";
	
			$r_partGR = mysqli_query($koneksi, $q_partGR);
		} else {
			$q_partGR = "UPDATE m_part_gr_detail SET 
					qty = $qty,
					gr_coa = '$gr_coa'
				WHERE id_detail_gr = '$id_detail_gr'
			";

			$r_partGR = mysqli_query($koneksi, $q_partGR);
		}


		if ($r_partGR) {
			echo "Data berhasil disimpan.";
			exit;
		} else {
			die("Query gagal: " . mysqli_error($koneksi) . " | Query: " . $q_partGR);
		}
		
    } else {
        echo "Mode kosong.";
        exit;
    }
}
else if ($_POST['type'] == "ExecGR") {
    $id_part_gr = mysqli_real_escape_string($koneksi, $_POST['id_part_gr']);

    $q_part = "SELECT 
			id_part, 
			qty
        FROM m_part_gr_detail 
        WHERE id_part_gr = '$id_part_gr'
    ";
    $r_part = mysqli_query($koneksi, $q_part);

    if (!$r_part) {
        die('Query Error (SELECT): ' . mysqli_error($koneksi));
    }

    $data = mysqli_fetch_all($r_part, MYSQLI_ASSOC);

    mysqli_begin_transaction($koneksi);
    try {
        foreach ($data as $row) {
            $id_part = mysqli_real_escape_string($koneksi, $row['id_part']);
            $qty = (float) $row['qty'];

            $q_update = "UPDATE m_part 
                SET masuk = masuk + $qty 
                WHERE id_part = '$id_part'
            ";
            if (!mysqli_query($koneksi, $q_update)) {
                throw new Exception('Query Error (UPDATE m_part): ' . mysqli_error($koneksi));
            }
        }

        $q_update_part = "UPDATE m_part_gr 
            SET `status` = '1'
            WHERE id_part_gr = '$id_part_gr'
        ";
        if (!mysqli_query($koneksi, $q_update_part)) {
            throw new Exception('Query Error (UPDATE m_part_gr): ' . mysqli_error($koneksi));
        }

        mysqli_commit($koneksi);

        echo json_encode([
            'success' => true,
            'message' => 'GR berhasil dieksekusi dan stok berhasil diperbarui'
        ]);
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}


// ========== DETAIL PART GR ==========
else if($_GET['type'] == "Read_DetilGR") {
	// echo "<pre>";
	// print_r($_GET);
	// echo "</pre>";
	// exit;

	$id_part_gr = $_GET['id_part_gr'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th width="5%" style="text-align: center;">NO</th>					
					<th width="20%" style="text-align: center;">ITEMCODE</th>
					<th width="40%" style="text-align: center;">ITEMNAME</th>	
					<th width="25%" style="text-align: center;">COA</th>	
					<th width="10%" style="text-align: center;">QTY</th>	
					<th width="10%" style="text-align: center;">EDIT</th>	
					<th width="10%" style="text-align: center;">DEL</th>	
				</tr>	
			</thead>';	

	$q_partGR = "SELECT 
		m_part_gr_detail.id_detail_gr,
		m_part.kode,
		m_part.nama,
		m_part_gr_detail.qty,
		m_part_gr.status,
		CONCAT(m_part_gr_coa.coa, '-',m_part_gr_coa.keterangan ) as gr_coa
	FROM m_part_gr_detail
	LEFT JOIN m_part ON m_part.id_part = m_part_gr_detail.id_part
	LEFT JOIN m_part_gr ON m_part_gr.id_part_gr = m_part_gr_detail.id_part_gr
	LEFT JOIN m_part_gr_coa ON m_part_gr_coa.id_gr_coa = m_part_gr_detail.gr_coa
	WHERE m_part_gr_detail.id_part_gr = '$id_part_gr'
	";

	$query = mysqli_query($koneksi, $q_partGR);

	if (!$result = $query) {
		exit(mysqli_error($koneksi));
	}

	if(mysqli_num_rows($result) > 0){
		$idr = 0;
		$usd = 0;
		while($row = mysqli_fetch_assoc($result)) {	
			$posisi++;	
			
			$data .= '<tr>						
				<td style="text-align:center">'.$posisi.'.</td>
				<td style="text-align:center">'.$row['kode'].'</td>	
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:left">'.$row['gr_coa'].'</td>
				<td style="text-align:center">'.$row['qty'].'</td>
				';

			if($row['status'] === '0' ) {
				$data .= '<td>
							<button class="btn btn-block btn-warning" 
									title="Execute" 
									style="margin:-3px;border-radius:0px" 
									type="button" 
									onClick="javascript:EditDetail('.$row['id_detail_gr'].')">
								<span class="fa fa-edit"></span>
							</button>
						</td>';
			}
			if($row['status'] === '0' ) {
				$data .= '<td>
							<button class="btn btn-block btn-danger" 
									title="Delete" 
									style="margin:-3px;border-radius:0px" 
									type="button" 
									onClick="javascript:DelDetail('.$row['id_detail_gr'].')">
								<span class="fa fa-close"></span>
							</button>
						</td>';
			}
				
				$data .='</tr>';
			$number++;
		}
	}else{
		$data .= '<tr><td colspan="7">Records not found!</td></tr>';
	}
	
	$data .= '</table>';
	
	echo $data;	
	
}
elseif ($_POST['type'] == "EditDetailGR") {

    $id_detail_gr = $_POST['id_detail_gr'];

    $query = "SELECT 
            m_part_gr_detail.*,
            m_cost_tr.itemcode,
            m_cost_tr.nama_cost,
            m_cost_tr.uom
        FROM m_part_gr_detail
        LEFT JOIN m_part ON m_part.id_part = m_part_gr_detail.id_part 
        INNER JOIN m_cost_tr ON m_cost_tr.itemcode = m_part.kode
        WHERE m_part_gr_detail.id_detail_gr = '$id_detail_gr'
    ";	

    $dataQuery = mysqli_query($koneksi, $query);

    if (!$dataQuery) {
        die(json_encode(['error' => mysqli_error($koneksi)]));
    }

    $data = mysqli_fetch_assoc($dataQuery);

    // kirim data langsung sebagai object JSON
    echo json_encode($data);
    exit;
}
elseif ($_POST['type'] == "DelDetailGR") {

	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";	
	// exit;

    $id_detail_gr = $_POST['id_detail_gr'];

    $query = "DELETE FROM m_part_gr_detail WHERE id_detail_gr = '$id_detail_gr'
    ";	

    $dataQuery = mysqli_query($koneksi, $query);

    if (!$dataQuery) {
        die(json_encode(['error' => mysqli_error($koneksi)]));
    }

	echo "Data Berhasil di Hapus";
    exit;
}


else if($_GET['type'] == "ListKeluar"){
	$id = $_GET['id'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="5%" style="text-align: center;">NO</th>
					<th rowspan="2" width="12%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="25%" style="text-align: center;">NO. SPK</th>
					<th rowspan="2" width="20%" style="text-align: center;">NO. POLICE</th>
					<th rowspan="2" width="10%" style="text-align: center;">QTY</th>		
					<th rowspan="2" width="10%" style="text-align: center;">CREATED</th>
								
				</tr>
			</thead>';
	$t1="select  t_spk_part.*, t_spk.no_spk, t_spk.tanggal, t_spk.created, m_mobil_tr.no_polisi
				from  
				t_spk_part left join t_spk on t_spk_part.id_spk = t_spk.id_spk 
				left join m_mobil_tr on t_spk.id_mobil = m_mobil_tr.id_mobil
				where t_spk_part.id_part = '$id' order  by t_spk.tanggal asc";
	$h1=mysqli_query($koneksi, $t1);   
	while ($d1=mysqli_fetch_array($h1))		
	{
		$tanggal = ConverTgl($d1['tanggal']);
		$ketebalan = number_format($d1['ketebalan'],2);
		$total = $total + $amount;
		$n++;
		$data .= '<tr>							
			<td style="text-align:center">'.$n.'.</td>
			<td style="text-align:center">'.$tanggal.'</td>	
			<td style="text-align:center">'.$d1['no_spk'].'</td>
			<td style="text-align:center">'.$d1['no_polisi'].'</td>
			<td style="text-align:center">'.$d1['qty'].'</td>
			<td style="text-align:center">'.$d1['created'].'</td>	';	
		
		$data .='</tr>';
	}

    $data .= '</table>';
    echo $data;			
	
	
}
else if($_GET['type'] == "ListMasuk"){
	$id = $_GET['id'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="5%" style="text-align: center;">NO</th>
					<th rowspan="2" width="12%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="45%" style="text-align: center;">NO. PO</th>
					<th rowspan="2" width="10%" style="text-align: center;">QTY</th>		
					<th rowspan="2" width="10%" style="text-align: center;">CREATED</th>	
				</tr>
			</thead>';
	$t1="select m_part_masuk.*, m_part.nama
				from  m_part_masuk left join m_part on m_part_masuk.id_part = m_part.id_part 
				where m_part_masuk.id_part = '$id' order  by m_part_masuk.tanggal asc";
	$h1=mysqli_query($koneksi, $t1);   
	while ($d1=mysqli_fetch_array($h1))		
	{
		$tanggal = ConverTgl($d1['tanggal']);
		$ketebalan = number_format($d1['ketebalan'],2);
		$total = $total + $amount;
		$n++;
		$data .= '<tr>							
			<td style="text-align:center">'.$n.'.</td>
			<td style="text-align:center">'.$tanggal.'</td>	
			<td style="text-align:center">'.$d1['no_po'].'</td>
			<td style="text-align:center">'.$d1['qty'].'</td>
			<td style="text-align:center">'.$d1['created'].'</td>	';
		$data .='</tr>';
	}

    $data .= '</table>';
    echo $data;			
	
}
else if ($_GET['type'] == "ListPart"){
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="6%" style="text-align: center;">NO</th>
					<th width="87%" style="text-align: center;">DESCRIPTION</th>
					<th width="7%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "SELECT * from m_part where nama LIKE '%$cari%' and (masuk-keluar) > '0'  order by nama LIMIT 0, 25";

	// echo $SQL;
	// exit;
	
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
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihPart('.$row['id_part'].')" >'.$row['nama'].'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihPart('.$row['id_cost'].')" 
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

else if ($_GET['type'] == "ListPart_car"){
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="6%" style="text-align: center;">NO</th>
					<th width="87%" style="text-align: center;">DESCRIPTION</th>
					<th width="7%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "SELECT * from m_part where nama LIKE '%$cari%' and (masuk-keluar) > '0'  order by nama LIMIT 0, 25";

	// echo $SQL;
	// exit;
	
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
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihPart('.$row['id_part'].')" >'.$row['nama'].'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihPart('.$row['id_cost'].')" 
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



// ========== LIST PO ==========
else if ($_GET['type'] == "ListpartPO"){
	// echo "<pre>";
	// print_r($_GET);
	// echo "</pre>";
	// exit;

	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="5%" style="text-align: center;">NO</th>
					<th width="20%" style="text-align: center;">CODE PO</th>
					<th width="70%" style="text-align: center;">REMARK</th>
					<th width="5%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$q_listPO = "SELECT 
				tr_po.id_po,
				tr_po.code_po,
				tr_po.remark
			FROM tr_po 
			INNER JOIN tr_po_detail ON tr_po_detail.code_po = tr_po.code_po
			WHERE tr_po_detail.jenis = 'item' 
				AND tr_po.no_sap IS NOT NULL
				AND tr_po.code_po LIKE '%$cari%'
			GROUP BY tr_po.code_po
			ORDER BY tr_po.id_po DESC LIMIT 0,25";
	
	$query = mysqli_query($koneksi, $q_listPO);	
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
			$data .= '<td style="text-align:left">
						<a href="#" onclick="PilihPO('.$row['id_po'].')" >'.$row['code_po'].'</a>
					</td>';
			$data .= '<td style="text-align:left">
						<a href="#" onclick="PilihPO('.$row['id_po'].')" >'.$row['remark'].'</a>
					</td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihPO('.$row['id_po'].')" 
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
else if ($_POST['type'] == "PilihPO"){
	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// die();

	$id_po = $_POST['id_po'];	
	$query = "SELECT * FROM tr_po WHERE id_po  = '$id_po'";
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

else if ($_GET['type'] == "ApproveGI") {
    $id_detil = isset($_GET['id_detil']) ? $_GET['id_detil'] : '';

    if (empty($id_detil)) {
        echo "Gagal: ID tidak ditemukan";
        exit;
    }

    $now = date('Y-m-d H:i:s');

    $q_part = "UPDATE t_spk_part 
        SET 
            approval = '1', 
            approval_time = '$now' 
        WHERE id_detil = '$id_detil'
    ";

    $r_part = mysqli_query($koneksi, $q_part);

    if ($r_part) {
        echo "Sukses Approve GI";
    } else {
        echo "Gagal Approve GI: " . mysqli_error($koneksi);
    }
}


else if ($_GET['type'] == "ListGRPO") {

	// echo "<pre>";
	// print_r($_GET);
	// echo "</pre>";
	// exit;

	$cari   	= mysqli_real_escape_string($koneksi, $_GET['cari']);
	$id_masuk  = mysqli_real_escape_string($koneksi, $_GET['id_masuk']);

	$data = '<table class="table table-hover table-striped" style="width:100%">
		<thead>
			<tr>
				<th width="5%" style="text-align: center;">NO</th>
				<th width="15%" style="text-align: center;">Date</th>
				<th width="15%" style="text-align: center;">No POWH</th>
				<th width="20%" style="text-align: center;">Itemcode</th>
				<th width="30%" style="text-align: center;">Itemname</th>
				<th width="5%" style="text-align: center;">Qty</th>
				<th width="5%" style="text-align: center;">ADD</th>
			</tr>
		</thead>';

	$q_part = "SELECT * FROM m_part_masuk WHERE id_masuk = '$id_masuk'";
	$r_part = mysqli_query($koneksi, $q_part);

	if ($r_part && mysqli_num_rows($r_part) > 0) {
		$d_part	 = mysqli_fetch_assoc($r_part);
		$no_po	 = $d_part['no_po'];
	} else {
		$no_po = null;
	}

	$q_detail = "SELECT 
					m_part_masuk.id_masuk,
					m_part_masuk.tanggal,
					m_part_masuk.no_po,
					m_cost_tr.itemcode,
					m_cost_tr.nama_cost,
					m_part_masuk.qty
				FROM m_part_masuk 
				LEFT JOIN m_part ON m_part.id_part = m_part_masuk.id_part
				INNER JOIN m_cost_tr ON m_cost_tr.itemcode = m_part.kode
				WHERE m_part_masuk.no_po = '$no_po' 
				AND m_part_masuk.no_grpo IS NULL
				ORDER BY m_part_masuk.tanggal DESC
			LIMIT 0, 10";

	// echo $q_detail;
	// exit;
			
	$query = mysqli_query($koneksi, $q_detail);	
	if (!$query) {
		exit(mysqli_error($koneksi));
	}

	$posisi = 0;
	if (mysqli_num_rows($query) > 0) {
		while($row = mysqli_fetch_assoc($query)) {	
			$posisi++;
			$data .= '<tr>';		
			$data .= '<td style="text-align:center">'.$posisi.'</td>';
			$data .= '<td style="text-align:center">'.$row['tanggal'].'</td>';
			$data .= '<td style="text-align:center">'.$row['no_po'].'</td>';
			$data .= '<td style="text-align:center">'.$row['itemcode'].'</td>';
			$data .= '<td style="text-align:left">'.$row['nama_cost'].'</td>';
			$data .= '<td style="text-align:center">'.$row['qty'].'</td>';

			$id_masuk = isset($row['id_masuk']) ? $row['id_masuk'] : $row['id_masuk'];
			$data .= '<td style="text-align:center">
						<label>
						<input type="checkbox" 
								name="grpo_selected[]" 
								value="'.$id_masuk.'">
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
else if ($_POST['type'] == "SaveUpGRPO") {
	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// exit;

	$ids = isset($_POST['ids']) ? $_POST['ids'] : [];

	if (empty($ids)) {
		echo json_encode([
			"success" => false,
			"message" => "Tidak ada data yang dipilih"
		]);
		exit;
	}

	$sql_header = "SELECT 
			m_part_masuk.tanggal,
			m_vendor_tr.caption
		FROM m_part_masuk 
		LEFT JOIN tr_po ON tr_po.code_po = m_part_masuk.no_po
		LEFT JOIN m_vendor_tr ON m_vendor_tr.id_vendor = tr_po.user_req
		WHERE m_part_masuk.id_masuk = '" . $ids[0] . "'
		LIMIT 1
	";

	$q_header = mysqli_query($koneksi, $sql_header);
	if (!$q_header) {
		die("Query Header Error: " . mysqli_error($koneksi));
	}

	$row = mysqli_fetch_assoc($q_header);

	$allLines = [];
	$resultData = [];

	foreach ($ids as $id_masuk) {
		$sql_detail = "SELECT 
				m_part_masuk.no_po,
				tr_po.no_sap,
				m_cost_tr.itemcode,
				m_part_masuk.qty
			FROM m_part_masuk 
			LEFT JOIN tr_po ON tr_po.code_po = m_part_masuk.no_po
			LEFT JOIN m_part ON m_part.id_part = m_part_masuk.id_part
			INNER JOIN m_cost_tr ON m_cost_tr.itemcode = m_part.kode
			WHERE m_part_masuk.id_masuk = '$id_masuk'
		";

		$q_detail = mysqli_query($koneksi, $sql_detail);
		if (!$q_detail) {
			die("Query Detail Error: " . mysqli_error($koneksi));
		}

		$rows_detail = mysqli_fetch_all($q_detail, MYSQLI_ASSOC);
		if (!$rows_detail) continue;

		foreach ($rows_detail as $det) {
			$allLines[] = [
				"NoPO"   	=> $det['no_po'] ?? '',
				"NoPOSAP"   => $det['no_sap'] ?? '',
				"ItemCode"  => $det['itemcode'] ?? '',
				"Qty"       => $det['qty'] ?? 0,
			];
		}
	}

	// ===== Gabungkan semua lines di bawah satu header =====
	$resultData[] = [
		"TglGRPO"	 => $row['tanggal'],
		"VendorCode" => $row['caption'],
		"Lines"      => $allLines
	];

	// ============== NO SEND API (LIHAT JSON) ==============
		// header('Content-Type: application/json');
		// echo "<pre>";
		// echo json_encode($resultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		// echo "<pre>";
		// exit;
		
	// ============== KIRIM API ==============
		// $apiUrl = "https://wsp.mitraadipersada.com/trucking/grpo.php";
		$apiUrl = "http://192.168.1.153/trucking2/grpo.php";

		$ch = curl_init($apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array_values($resultData)));

		$response = curl_exec($ch);
		curl_close($ch);
		
		// ============== CHECK RESPONS ==============
		// $data = json_decode($response, true);
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit;

		$apiResponse = json_decode($response, true);
		$resultDataLog = json_encode($apiResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$rawData = json_encode($resultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		if (!$apiResponse || !isset($apiResponse['docnum'])) {
			$success = false;
			$mssg    = $apiResponse['mssg'] ?? 'Invalid API response';
			mysqli_query($koneksi, "INSERT INTO tr_api_logs (docnum, doctype, raw_data, `desc`, result) 
				VALUES (
					'', 
					'GRPO', 
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
					'GRPO', 
					'" . mysqli_real_escape_string($koneksi, $rawData) . "', 
					'" . mysqli_real_escape_string($koneksi, $desc) . "',
					'" . mysqli_real_escape_string($koneksi, $resultDataLog) . "'
				)
			");

			foreach ($ids as $id_masuk) {
				$id_masuk = (int) $id_masuk;

				$docnum = mysqli_real_escape_string($koneksi, $apiResponse['docnum'] ?? '');
				$sql_update = "UPDATE m_part_masuk 
					SET no_grpo = '$docnum'
					WHERE id_masuk = $id_masuk
				";
				if (!mysqli_query($koneksi, $sql_update)) {
					die("Gagal update GRPO untuk ID $id_masuk: " . mysqli_error($koneksi));
				}
				$sql_part = "SELECT no_po 
					FROM m_part_masuk 
					WHERE id_masuk = $id_masuk 
					LIMIT 1
				";
				$r_part = mysqli_query($koneksi, $sql_part);
				if (!$r_part) {
					die("Gagal ambil data part untuk ID $id_masuk: " . mysqli_error($koneksi));
				}

				$d_part = mysqli_fetch_assoc($r_part);
				if (!$d_part) {
					continue;
				}

				$code_po = mysqli_real_escape_string($koneksi, $d_part['no_po']);
				$q_update_po = "UPDATE tr_po 
					SET grpo = '1' 
					WHERE code_po = '$code_po'
				";
				if (!mysqli_query($koneksi, $q_update_po)) {
					die("Gagal update PO untuk kode $code_po: " . mysqli_error($koneksi));
				}
			}

		}

		echo json_encode([
			"success" => $success,
			"message" => $apiResponse['mssg'] ?? ($success ? "Berhasil" : "Gagal tanpa pesan"),
			"sent"    => $resultData
		], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

}
else if ($_POST['type'] == "SaveUpGR") {
	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// exit;

	$id_part_gr = $_POST['id_part_gr'] ;

	$sql_header = "SELECT 
			*
		FROM m_part_gr
		WHERE id_part_gr = '$id_part_gr'
	";

	$q_header = mysqli_query($koneksi, $sql_header);
	if (!$q_header) {
		die("Query Header Error: " . mysqli_error($koneksi));
	}

	$row = mysqli_fetch_assoc($q_header);

	$allLines = [];
	$resultData = [];

	$sql_detail = "SELECT 
			m_cost_tr.itemcode,
			m_cost_tr.uom,
			m_part_gr_detail.qty,
			m_part_gr_coa.coa,
			m_part_gr_coa.type_receipt
		FROM m_part_gr_detail
		LEFT JOIN m_part ON m_part.id_part = m_part_gr_detail.id_part
		INNER JOIN m_cost_tr ON m_cost_tr.itemcode = m_part.kode
		LEFT JOIN m_part_gr_coa ON m_part_gr_coa.id_gr_coa = m_part_gr_detail.gr_coa
		WHERE m_part_gr_detail.id_part_gr = '$id_part_gr'
	";

	$q_detail = mysqli_query($koneksi, $sql_detail);
	if (!$q_detail) {
		die("Query Detail Error: " . mysqli_error($koneksi));
	}

	$rows_detail = mysqli_fetch_all($q_detail, MYSQLI_ASSOC);

	foreach ($rows_detail as $det) {
		$allLines[] = [
			"ItemCode"  => $det['itemcode'] ?? '',
			"Qty"       => $det['qty'] ?? 0,
			"UoM"   	=> $det['uom'] ?? '',
			"Warehouse"	=> 'WH-FG',
			"Remarks"   => '',
			"GLAcct"   	=> $det['coa'] ?? '',
			"Corporate" => '',
			"Divisi"   	=> '',
			"Department"=> '',
			"Activity"  => '',
			"Location"  => '',
		];
	}

	// ===== Gabungkan semua lines di bawah satu header =====
	$resultData[] = [
		"NoGR"	 		=> $row['no_grwh'],
		"TglGR" 		=> $row['tanggal'],
		"TipeReceipt" 	=> $row['type_receipt'],
		"Remarks" 		=> $row['remark'],
		"JournalRemark" => $row['jurnal_remark'],
		"Lines"     	=> $allLines
	];

	// ============== NO SEND API (LIHAT JSON) ==============
	// header('Content-Type: application/json');
	// echo "<pre>";
	// echo json_encode($resultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	// echo "<pre>";
	// exit;
		
	// ============== KIRIM API ==============
	// $apiUrl = "https://wsp.mitraadipersada.com/trucking/goods-receipt.php";
	$apiUrl = "http://192.168.1.153/trucking2/goods-receipt.php";

	$ch = curl_init($apiUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json'
	]);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array_values($resultData)));

	$response = curl_exec($ch);
	curl_close($ch);
	
	// ============== CHECK RESPONS ==============
	// $data = json_decode($response, true);
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	// exit;

	$apiResponse = json_decode($response, true);
	$resultDataLog = json_encode($apiResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	$rawData = json_encode($resultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

	if (!$apiResponse || !isset($apiResponse['docnum'])) {
		$success = false;
		$mssg    = $apiResponse['mssg'] ?? 'Invalid API response';
		mysqli_query($koneksi, "INSERT INTO tr_api_logs (docnum, doctype, raw_data, `desc`, result) 
			VALUES (
				'', 
				'GRWH', 
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
				'GRWH', 
				'" . mysqli_real_escape_string($koneksi, $rawData) . "', 
				'" . mysqli_real_escape_string($koneksi, $desc) . "',
				'" . mysqli_real_escape_string($koneksi, $resultDataLog) . "'
			)
		");


		$docnum = mysqli_real_escape_string($koneksi, $apiResponse['docnum'] ?? '');
		$sql_update = "UPDATE m_part_gr 
				SET gr_sap = '$docnum'
			WHERE id_part_gr = $id_part_gr
		";
		if (!mysqli_query($koneksi, $sql_update)) {
			die("Gagal update GR untuk ID $id_part_gr: " . mysqli_error($koneksi));
		}

	}

	echo json_encode([
		"success" => $success,
		"message" => $apiResponse['mssg'] ?? ($success ? "Berhasil" : "Gagal tanpa pesan"),
		"sent"    => $resultData
	], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

}


?>