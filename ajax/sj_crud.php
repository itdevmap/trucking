<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, 
"SELECT * FROM m_role_akses_tr WHERE id_role = '$id_role' AND id_menu ='63' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

// ============ Read Data ============
	if ($_GET['type'] == "Read"){
		$cari 	= trim($_GET['cari']);
		$hal 	= $_GET['hal'];
		$paging = $_GET['paging'];
		
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>	
						<th rowspan="2" width="3%" style="text-align: center;">NO</th>
						<th rowspan="2" width="7%" style="text-align: center;">DATE</th>
						<th rowspan="2" width="10%" style="text-align: center;">ROUTE</th>
						<th rowspan="2" width="6%" style="text-align: center;">NO JO</th>
						<th rowspan="2" width="6%" style="text-align: center;">NO SJ</th>
						<th rowspan="2" width="10%" style="text-align: center;">NO CONTAINER</th>
						<th rowspan="2" width="6%" style="text-align: center;">NO SEAL</th>
						<th rowspan="2" width="10%" style="text-align: center;">DRIVER</th>
						<th rowspan="2" width="10%" style="text-align: center;">NO POL</th>
						<th colspan="4" width="10%" style="text-align: center;">AP</th>
						<th colspan="3" width="10%" style="text-align: center;">ACTION</th>
					</tr>
					<tr>
						<th width="5%" style="text-align: center;">TRAVEL<br>EXPENSE</th>
						<th width="5%" style="text-align: center;">RITASE</th>
						<th width="5%" style="text-align: center;">OTHER</th>
						<th width="5%" style="text-align: center;">CLAIM</th>

						<th rowspan="2" width="3%" style="text-align: center;">PRINT</th>
						<th rowspan="2" width="3%" style="text-align: center;">DEL</th>
						<th rowspan="2" width="3%" style="text-align: center;">ATCH</th>
					</tr>
				</thead>';		

		if (!isset($_GET['hal']) || !is_numeric($_GET['hal']) || $_GET['hal'] < 1) {
			$page = 1;
		} else {
			$page = (int) $_GET['hal'];
		}

		$jmlperhalaman = isset($_GET['paging']) && is_numeric($_GET['paging']) && $_GET['paging'] > 0
			? (int) $_GET['paging']
			: 10;

		$offset = ($page - 1) * $jmlperhalaman;
		$posisi = $offset;

		$q_sj = "SELECT 
					tr_sj.*,
					m_supir_tr.nama_supir,
					m_mobil_tr.no_polisi,
					tr_jo_detail.uj,
					tr_jo_detail.ritase,
					tr_sj.uj_lain,
					tr_sj.claim
				FROM tr_sj
				LEFT JOIN tr_jo ON tr_jo.no_jo = tr_sj.no_jo
				LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
				LEFT JOIN m_supir_tr ON m_supir_tr.id_supir = tr_sj.id_supir
				LEFT JOIN m_mobil_tr ON m_mobil_tr.id_mobil = tr_sj.id_mobil
				WHERE tr_sj.no_jo LIKE '%$cari%'
				ORDER BY  tr_sj.no_jo DESC";	

		$query = mysqli_query($koneksi, $q_sj);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_assoc($result)) {    

				$id_sj = mysqli_real_escape_string($koneksi, $row['id_sj']);

				$q_uj = "SELECT SUM(biaya) AS total_uj_lain FROM tr_sj_uj WHERE id_sj = '$id_sj'";
				$r_uj = mysqli_query($koneksi, $q_uj);

				if (!$r_uj) {
					die("Query Error (UJ_LAIN): " . mysqli_error($koneksi) . " | SQL: " . $q_uj);
				}

				$data_uj = mysqli_fetch_assoc($r_uj);
				$uj_lain = $data_uj['total_uj_lain'] ?? 0;

				$uj      = number_format($row['uj'],0);
				$ritase  = number_format($row['ritase'],0);
				$claim   = number_format($row['claim'],0);
				
				$posisi++;

				$data .= '<tr>
					<td style="text-align:center">'.$posisi.'.</td>
					<td style="text-align:center">'.$row['tanggal'].'</td>
					<td style="text-align:center">'.$row['itemname'].'</td>
					<td style="text-align:center">'.$row['no_jo'].'</td>
					<td style="text-align:center">'.$row['code_sj'].'</td>
					<td style="text-align:center">'.$row['container'].'</td>
					<td style="text-align:center">'.$row['seal'].'</td>
					<td style="text-align:center">'.$row['nama_supir'].'</td>
					<td style="text-align:center">'.$row['no_polisi'].'</td>';

				$data .= '<td style="text-align:right">'.$uj.'</td>
						<td style="text-align:right">'.$ritase.'</td>';

				// ============== OTHER AP ==============
				if($id_role != 2) {
					$data .= '<td style="text-align:right">
						<button class="btn btn-block btn-default"  
							style="padding:1px;border-radius:0px;width:100%;text-align:right" type="button" 
							onClick="javascript:ListUJ('.$row['id_sj'].', '.$row['status'].')"  >
							'.number_format($uj_lain,0).'
						</button>
					</td>';    
				} else {
					$data .= '<td></td>';
				}

				// ============== CLAIM AP ==============
				$data .= '<td style="text-align:right">
					<button class="btn btn-block btn-default"
						style="padding:1px;border-radius:0px;width:100%;text-align:right" type="button" 
						onClick="javascript:ListClaim('.$row['id_sj'].', '.$row['status'].')">
						'.$claim.'
					</button>
				</td>';

				$xy1 = base64_encode($row['id_sj']);
				$link = "'cetak_sj.php?id=$xy1'";

				$data .= '<td>
					<button class="btn btn-block btn-default" title="Print"
						style="margin:-3px;border-radius:0px" type="button"
						onClick="window.open('.$link.')">
						<span class="fa fa-print"></span>
					</button>
				</td>';

				// ============== DELETE SJ ==============
				if($m_edit == '1' && $row['id_user'] != 'admin') {
					$data .= '<td>
						<button class="btn btn-block btn-default" title="Delete"
							style="margin:-3px;border-radius:0px;" type="button" 
							onClick="javascript:Delete('.$row['id_sj'].')"  >
							<span class="fa fa-close"></span>
						</button>
					</td>';
				} else {
					$data .= '<td></td>';
				}

				// ============== ATTACH SJ ==============
				$data .= '<td>
					<button class="btn btn-block btn-default" title="Add Attachment"
						style="margin:-3px;border-radius:0px" type="button"
						onClick="AddAttc(' . $row['id_jo'] . ')">
						<span class="fa fa-file"></span>
					</button>
				</td>';

				$data .= '</tr>';

				$number++;
			}
		}else{
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

// ============ STORE DATA ============
	else if ($_POST['type'] == "Add_Data"){
		if(!empty($_POST['mode'])) {	
			$id     		= $_POST['id'];
			$mode   		= $_POST['mode'];

			$no_jo 			= $_POST['no_jo'];
			$project_code	= $_POST['project_code'];
			$no_do   		= $_POST['no_do'];

			$container   	= $_POST['container'];
			$route   		= $_POST['route'];
			$seal   		= $_POST['seal'];
			$id_mobil   	= $_POST['id_mobil'];
			$id_supir   	= $_POST['id_supir'];
			$desc   		= $_POST['desc'];
			$tanggal 		= date("Y-m-d");

			// BUILD SJ
				$tahun = date("y");
				$q = "SELECT MAX(RIGHT(code_sj,4)) as last_num 
					FROM tr_sj 
					WHERE SUBSTRING(code_sj,4,2) = '$tahun'";

				$res = mysqli_query($koneksi, $q);
				$row = mysqli_fetch_assoc($res);
				$nextNum = ($row['last_num'] ?? 0) + 1;

				$urut = str_pad($nextNum, 4, "0", STR_PAD_LEFT);
				$code_sj = "SJ-" . $tahun . $urut;

				$code_sj = $tahun . $urut;
				$code_sj = "SJ-" . $code_sj;

			$id_user = $_SESSION['id'] ?? 0;

			if ($mode == 'Add') {

				$sql = "INSERT INTO tr_sj (
					no_jo, 
					project_code, 
					code_sj, 
					tanggal, 
					itemname, 
					berat, 
					vol, 
					container, 
					seal, 
					id_mobil, 
					id_supir, 
					keterangan
				) VALUES (
					'$no_jo',
					'$project_code',
					'$code_sj',
					'$tanggal',
					'$route',
					0,
					0,
					'$container',
					'$seal',
					'$id_mobil',
					'$id_supir',
					'$desc'
				)";

				$hasil = mysqli_query($koneksi, $sql);

				if ($hasil) {
					echo "DATA BERHASIL DI TAMBAHKAN";
				} else {
					echo "ERROR: " . mysqli_error($koneksi);
				}
				exit;

			} else if ($mode == 'Edit') {
				$sql = "UPDATE m_route_tr SET 
							vendor     = '$vendor',
							rute       = '$rute',
							cost       = '$cost',
							updated_by = '$id_user',
							updated_at = NOW()
						WHERE id_route = '$id'";
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

// ============ SHOW SJ ============
	else if ($_GET['type'] == "ListSO"){
		$cari = $_GET['cari'];
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>
						<th width="5%" style="text-align: center;">NO</th>
						<th width="10%" style="text-align: center;">NO JO</th>
						<th width="10%" style="text-align: center;">PROJECT CODE</th>
						<th width="10%" style="text-align: center;">CONTAINER</th>
						<th width="20%" style="text-align: center;">ROUTE</th>
						<th width="10%" style="text-align: center;">ADD</th>
					</tr>
				</thead>';	
		$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
		$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
		
		$SQL = "SELECT 
				tr_jo.id_jo,
				tr_jo.no_jo,
				tr_quo.quo_no,
				sap_project.kode_project,
				tr_jo.project_code,
				m_asal.nama_kota AS asal,
				m_tujuan.nama_kota AS tujuan,
				tr_jo_detail.id,
				CONCAT(m_asal.nama_kota, ' - ', m_tujuan.nama_kota) AS rute,
				tr_jo_detail.container
			FROM tr_jo
			LEFT JOIN tr_quo ON tr_quo.id_quo = tr_jo.id_quo
			LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
			LEFT JOIN sap_project ON tr_jo.sap_project = sap_project.rowid
			LEFT JOIN m_cust_tr ON tr_jo.id_cust = m_cust_tr.id_cust
			LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_jo_detail.id_asal
			LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_jo_detail.id_tujuan
			WHERE tr_jo.no_jo LIKE '%$cari%' 
			ORDER BY tr_jo.id_jo DESC LIMIT 0,10";
		
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
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSO('.$row['id'].')" >'.$row['no_jo'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSO('.$row['id'].')" >'.$row['project_code'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSO('.$row['id'].')" >'.$row['container'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSO('.$row['id'].')" >'.$row['rute'].'</a></td>';
				$data .= '<td style="text-align:center">
						<button type="button" class="btn btn-default" onClick="javascript:PilihSO('.$row['id'].')" 
						style="margin:-3px;width:100%;padding:6px;border-radius:1px"><span class="fa  fa-plus-square"></span></button>
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

// ============ CHOISE SJ ============
	else if ($_POST['type'] == "DetilData"){
		$id = $_POST['id'];	

		$query = "SELECT 
					tr_jo_detail.id,
					tr_jo_detail.id_so,
					tr_jo_detail.jenis_mobil,
					tr_jo_detail.harga,
					tr_jo_detail.uj,
					tr_jo_detail.ritase,
					tr_jo_detail.container,
					tr_jo_detail.remark,
					tr_jo.no_jo,
					tr_jo.project_code,
					tr_jo.penerima,
					CONCAT(m_asal.nama_kota, ' - ', m_tujuan.nama_kota) AS rute,
					m_cust_tr.nama_cust
				FROM tr_jo
				LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
				LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_jo_detail.id_asal
				LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_jo_detail.id_tujuan
				LEFT JOIN m_cust_tr ON tr_jo.id_cust = m_cust_tr.id_cust
				WHERE tr_jo_detail.id = '$id'
				";

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

// ============ READ SJ ============
	else if($_GET['type'] == "Read_Detil"){
		$no_jo = $_GET['no_jo'];
		$mode = $_GET['mode'];
		
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>	
						<th rowspan="2" width="3%" style="text-align: center;">NO</th>					
						<th rowspan="2" width="31%" style="text-align: center;">NO JO</th>
						<th rowspan="2" width="31%" style="text-align: center;">PROJECT CODE</th>
						<th rowspan="2" width="20%" style="text-align: center;">NO SJ</th>
						<th width="3%" style="text-align: center;">DEL</th>
					</tr>	
				</thead>';	

		$SQL = "SELECT 
					tr_sj.id_sj,
					tr_sj.no_jo,
					tr_sj.project_code,
					tr_sj.code_sj
				FROM tr_sj
				LEFT JOIN tr_jo ON tr_jo.no_jo = tr_sj.no_jo
				WHERE tr_sj.no_jo = '$no_jo' ORDER BY  tr_sj.id_sj";

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
					
					<td style="text-align:center">'.$row['no_jo'].'</td>	
					<td style="text-align:center">'.$row['project_code'].'</td>
					<td style="text-align:center">'.$row['code_sj'].'</td>';
					
					if($mode == 'Edit' ){
						$data .= '<td>
									<button class="btn btn-block btn-default"  title="Delete"
										style="margin:-3px;border-radius:0px" type="button" 
										onClick="javascript:DelDetil('.$row['id_sj'].')"  >
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
		}
		else
		{
			$data .= '<tr><td colspan="7">Records not found!</td></tr>';
		}
		
		
		$data .= '</table>';
		
		echo $data;	
		
	}

// ============ DEL SJ ============
	else if ($_POST['type'] == "Del_Order"){
		$id = $_POST['id']; 	
	
		$query = "DELETE FROM tr_sj WHERE id_sj = '$id' ";
		if (!$result = mysqli_query($koneksi, $query)) {
			exit(mysqli_error($koneksi));
		}	

	}

// ============ ADD AP UJ ============
	else if ($_POST['type'] == "Add_UJ"){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		if($_POST['mode'] != '' ){
			$id_sj 	 = $_POST['id_sj'];
			$id_cost = $_POST['id_cost'];
			$mode 	 = $_POST['mode'];
			$remark  = $_POST['remark'];

			$biaya 	 = $_POST['biaya'];
			$biaya 	 = str_replace(",","", $biaya);
			
			if($mode == 'Add'){			
				$q_insert = "INSERT INTO tr_sj_uj (id_sj, id_cost, biaya, remark) VALUES ('$id_sj', '$id_cost', '$biaya', '$remark')";
				$hasil = mysqli_query($koneksi, $q_insert);

				$q_update = "UPDATE tr_sj SET uj_lain = COALESCE(uj_lain, 0) + $biaya WHERE id_sj = '$id_sj'";
				$hasil_update = mysqli_query($koneksi, $q_update);
			} else {
				$sql = "UPDATE tr_sj_uj set 
						id_cost = '$id_cost',
						biaya 	= '$biaya'
						remark 	= '$remark'
						where id_sj = '$id_sj'";
				$hasil=mysqli_query($koneksi,$sql);
			}

			if (!$hasil) {
				echo "Data Error...!";
			} else {	
				
				echo "Data saved!";
			}
		}	
	}

	else if($_GET['type'] == "List_UJ"){

		// echo "<pre>";
		// print_r($_GET);
		// echo "</pre>";
		// die();

		$id = $_GET['id'];
		$stat = $_GET['stat'];
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>	
						<th rowspan="2" width="6%" style="text-align: center;">NO</th>
						<th rowspan="2" width="59%" style="text-align: center;">INFORMATION'.$stat.'</th>
						<th rowspan="2" width="15%" style="text-align: center;">COST</th>
						<th rowspan="2" width="5%" style="text-align: center;">EDIT</th>		
						<th rowspan="2" width="5%" style="text-align: center;">DEL</th>		
					</tr>
				</thead>';
		$t1="SELECT 
				tr_sj_uj.*, 
				m_cost_tr.nama_cost 
			FROM tr_sj_uj 
			LEFT JOIN m_cost_tr ON tr_sj_uj.id_cost = m_cost_tr.id_cost
			WHERE tr_sj_uj.id_sj = '$id' ORDER BY tr_sj_uj.id_sj";
		$h1=mysqli_query($koneksi, $t1);  

		while ($d1=mysqli_fetch_array($h1)) {
			$biaya = number_format($d1['biaya'],0);
			$total = $total + $d1['biaya'];
			$n++;
			$data .= '<tr>							
				<td style="text-align:center">'.$n.'.</td>
				<td style="text-align:left">'.$d1['remark'].'</td>	
				<td style="text-align:right">'.$biaya.'</td> ';	
			
				if($stat != '1' ){
					$data .= '<td>
						<button class="btn btn-block btn-default"  title="Delete"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetUJ('.$d1['id_uj'].')"  >
						<span class="fa fa-edit " ></span>
						</button></td>';
				}
				else{
					$data .='<td></td>';
				}	
				if($stat != '1' ){
					
					$data .= '<td>
						<button class="btn btn-block btn-default"  title="Delete"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:DelUJ('.$d1['id_uj'].')"  >
						<span class="fa fa-close " ></span>
						</button></td>';
				}
				else
				{
				
					$data .='<td></td>';
				}					
			$data .='</tr>';
		}
		$totalx = number_format($total,0);
		
		$data .= '<tr>							
				<td colspan = "1" style="text-align:center;background:#eaebec"></td>	
				<td colspan= "1" style="text-align:right;background:#eaebec;color:#000"><b>TOTAL</b></td> 
				<td style="text-align:right;background:#4bc343;color:#fff"><b>'.$totalx.'</b></td>';	
		$data .='</tr>';
		$data .= '</table>';
		
		$sql = "update tr_jo set uj_lain = '$total' where id_jo = '$id'	";
		$hasil=mysqli_query($koneksi,$sql);
				
		echo $data;		
		
		
	}
	else if ($_POST['type'] == "Del_UJ"){
		$id = $_POST['id']; 
		$query = "DELETE FROM tr_sj_uj WHERE id_uj = '$id' ";
		if (!$result = mysqli_query($koneksi, $query)) {
			exit(mysqli_error($koneksi));
		}	
		
		
	}

// ============ ADD AP CLAIm ============
	else if ($_POST['type'] == "Add_Claim") {

		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		$id_sj  = $_POST['id_sj'];

		$biaya  = str_replace(",", "", $_POST['biaya']);
		$biaya  = floatval($biaya);

		$sql = "UPDATE tr_sj
				SET claim = '$biaya'
				WHERE id_sj = '$id_sj'";
				
		$hasil = mysqli_query($koneksi, $sql);

		if (!$hasil) {
			echo "Data Error...!";
		} else {
			echo "Data saved!";
		}
	}
?>