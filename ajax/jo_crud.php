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


$pq = mysqli_query($koneksi, "SELECT * from m_role_akses_tr WHERE id_role = '$id_role'  AND id_menu = '3' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

	if ($_GET['type'] == "Read"){
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
		
		if($stat == 'Open'){
			$stat = '0';
		}
		else if($stat == 'Close')
		{
			$stat = '1';
		}
						
		if($field == 'No Order')
		{
			$f = 'tr_jo.no_jo';	
		}else if($field == 'No Quo'){
			$f = 'tr_quo.quo_no';	
		}else if($field == 'Customer'){
			$f = 'm_cust_tr.nama_cust';		
		}else if($field == 'Origin'){
			$f = 'm_kota_tr.nama_kota';
		}else if($field == 'Destination'){
			$f = 'm_kota1.nama_kota';	
		}else if($field == 'No Cont'){
			$f = 'tr_jo.no_cont';	
		}else if($field == 'Driver'){
			$f = 'm_supir_tr.nama_supir';		
		}else if($field == 'No Police'){
			$f = 'm_mobil_tr.no_polisi';			
		}else{
			$f = 'tr_jo.no_jo';
		}
		
		if($field1 == 'No Order')
		{
			$f1 = 'tr_jo.no_jo';	
		}else if($field1 == 'No Quo'){
			$f1 = 'tr_quo.quo_no';	
		}else if($field1 == 'Customer'){
			$f1 = 'm_cust_tr.nama_cust';		
		}else if($field1 == 'Origin'){
			$f1 = 'm_kota_tr.nama_kota';
		}else if($field1 == 'Destination'){
			$f1 = 'm_kota1.nama_kota';	
		}else if($field1 == 'No Cont'){
			$f1 = 'tr_jo.no_cont';	
		}else if($field1 == 'Driver'){
			$f1 = 'm_supir_tr.nama_supir';		
		}else if($field1 == 'No Police'){
			$f1 = 'm_mobil_tr.no_polisi';			
		}else{
			$f1 = 'tr_jo.no_jo';
		}
		
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
			<tr>					
				<th rowspan="2" width="3%" style="text-align: center;">NO</th>
				<th rowspan="2" width="8%" style="text-align: center;">DATE<br>NO ORDER<br>NO QUO</th>	
				<th rowspan="2" width="10%" style="text-align: center;">PROJECT SAP <br> NO SO <br> NO AR</th>
				<th rowspan="2" width="6%" style="text-align: center;">PROJECT<br>CODE</th>
				<th rowspan="2" width="12%" style="text-align: center;">CUSTOMER<br>NO DO</th>
				<th rowspan="2" width="8%" style="text-align: center;">ORIGIN<br>DESTINATION</th>
				<th colspan="2" width="15%" style="text-align: center;">AR</th>
				<th colspan="3" width="16%" style="text-align: center;">AP</th>
				<th rowspan="2" width="5%" style="text-align: center;">CLAIM</th>
				<th rowspan="2" width="5%" style="text-align: center;">CREATED</th>
				<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>
				<th colspan="3" width="6%" style="text-align: center;">ACTION</th>	
				<th colspan="2" width="4%" style="text-align: center;">PRINT</th>	
				<th rowspan="2" width="4%" style="text-align: center;">ATTC</th>	
			</tr>
			<tr>
				<th width="5%" style="text-align: center;">DELIVERY<br>COST</th>
				<th width="6%" style="text-align: center;">OTHER<br>COST</th>
				<th width="5%" style="text-align: center;">TRAVEL<br>EXPENSE</th>
				<th width="5%" style="text-align: center;">RITASE</th>
				<th width="6%" style="text-align: center;">OTHER</th>
				
				<th width="2%" style="text-align: center;">EDIT</th>
				<th width="2%" style="text-align: center;">DEL</th>	
				<th width="2%" style="text-align: center;">EXEC</th>

				<th width="2%" style="text-align: center;">SO</th>						
				<th width="2%" style="text-align: center;">AR</th>
			</tr>
		</thead>
		';			
		if(!isset($_GET['hal'])){ 
			$page = 1;       
			} else { 
			$page = $_GET['hal']; 
			$posisi=0;
		}
		$jmlperhalaman = $paging;
		$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
		$posisi = (($page * $jmlperhalaman) - $jmlperhalaman); 
		
		if($id_role == '2' || $id_role == '10')
		{
			$sales = $id_user;
		}
		
		if($stat == 'All') {
			$SQL = "SELECT 
					tr_jo.id_jo,
					tr_jo.no_sap,
					tr_quo.quo_no,
					tr_jo.tgl_jo,
					tr_jo.no_do,
					tr_jo.no_jo,
					tr_jo_detail.jenis_mobil,
					tr_jo_detail.harga,
					tr_jo.biaya_kirim_lain,
					tr_jo_detail.uj,
					tr_jo.uj_lain,
					tr_jo_detail.ritase,
					tr_jo.stapel,
					tr_jo.claim,
					tr_jo.ket,
					tr_jo.created,
					tr_jo.status,
					tr_jo.no_ar,
					tr_jo.print_count,
					tr_jo.flag_ar,
					sap_project.kode_project,
					tr_jo.project_code,
					m_cust_tr.nama_cust,
					m_asal.nama_kota AS asal,
					m_tujuan.nama_kota AS tujuan
				FROM tr_jo
				LEFT JOIN tr_quo ON tr_quo.id_quo = tr_jo.id_quo
				LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
				LEFT JOIN sap_project ON tr_jo.sap_project = sap_project.rowid
				LEFT JOIN m_cust_tr ON tr_jo.id_cust = m_cust_tr.id_cust
				LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_jo_detail.id_asal
				LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_jo_detail.id_tujuan
				WHERE tr_jo.tgl_jo BETWEEN '$tgl1x' AND '$tgl2x' 
				AND $f LIKE '%$cari%' 
				AND $f1 LIKE '%$cari1%' 
				ORDER BY tr_jo.tgl_jo DESC, tr_jo.no_jo DESC
				LIMIT $offset, $jmlperhalaman";
				
		}else{
			$SQL = "SELECT 
					tr_jo.id_jo,
					tr_jo.no_sap,
					tr_jo.tgl_jo,
					tr_jo.no_jo,
					tr_jo.no_do,
					tr_jo.biaya_kirim_lain,
					tr_jo_detail.uj,
					tr_jo.uj_lain,
					tr_jo_detail.ritase,
					tr_jo.stapel,
					tr_jo.claim,
					tr_jo.ket,
					tr_jo.created,
					tr_jo.status,
					tr_jo.no_ar,
					tr_jo.print_count,
					tr_jo.flag_ar,
					tr_quo.quo_no,
					sap_project.kode_project,
					tr_jo.project_code,
					m_cust_tr.nama_cust,
					m_asal.nama_kota AS asal,
					m_tujuan.nama_kota AS tujuan,
					tr_jo_detail.jenis_mobil,
					tr_jo_detail.harga
				FROM tr_jo
				LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
				LEFT JOIN tr_quo ON tr_quo.id_quo = tr_jo.id_quo
				LEFT JOIN sap_project ON tr_jo.sap_project = sap_project.rowid
				LEFT JOIN m_cust_tr ON tr_jo.id_cust = m_cust_tr.id_cust
				LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_jo_detail.id_asal
				LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_jo_detail.id_tujuan
				WHERE tr_jo.tgl_jo BETWEEN '$tgl1x' AND '$tgl2x' 
				AND $f LIKE '%$cari%' 
				AND $f1 LIKE '%$cari1%' 
				AND tr_jo.status = '$stat'
				ORDER BY tr_jo.tgl_jo DESC, tr_jo.no_jo DESC
				LIMIT $offset, $jmlperhalaman";
		}

		$query = mysqli_query($koneksi, $SQL);	
		if (!$result = $query) {
			exit(mysqli_error($koneksi));
		}
		if(mysqli_num_rows($result) > 0)
		{
			while($row = mysqli_fetch_assoc($result))
			{	
				$tanggal = ConverTgl($row['tgl_jo']);
				$biaya_kirim = number_format($row['harga'],0);
				$biaya_kirim_lain = number_format($row['biaya_kirim_lain'],0);
				$uj = number_format($row['uj'],0);
				$ritase = number_format($row['ritase'],0);
				$uj_lain = number_format($row['uj_lain'],0);
				$posisi++;
				$xy1="View|$row[id_jo]";
				$xy1=base64_encode($xy1);
				$link = "lcl_data.php?id=$xy1";
				if($row['status'] == '0')
				{
					$label = 'danger';
					$status = 'Open';
					$dis = '';
				}
				else if($row['status'] == '1')
				{
					$label = 'success';
					$status = 'Close';
					$dis = 'Disabled';
				}
				if($row['quo_no'] == '0')
				{
					$no_quo = 'No Quo';
				}else{
					$no_quo = $row['quo_no'];
				}
			
				if($id_role == '2')
				{
					$biaya_kirim = '';
					$biaya_kirim_lain = '';
					$uj = '';
					$ritase = '';
					$uj_lain = '';
				}

				$xy1	= "Edit|$row[id_jo]";
				$xy1	=base64_encode($xy1);
				$link 	= "'so_data.php?id=$xy1'";
				
				$data .= '<tr>
					<td style="text-align:center">'.$posisi.'.</td>	
					<td style="text-align:left">'.$tanggal.'<br>'.$row['no_jo'].'<br>'.$no_quo.'</td>';
					
					if ($row['no_sap'] != null && $row['no_ar'] === null) {
						$data .= '<td style="text-align:center">
							'.$row['kode_project'].'<br>
							SO '.$row['no_sap'].'<br>
							<a href="javascript:void(0);" onclick="TampilUpAR(\''.$row['id_jo'].'\')">
								'.$row['no_ar'].'Send AR<br>
							</a>
						</td>';
					} elseif ($row['no_ar'] != null) {
						$data .= '<td style="text-align:center">
							'.$row['kode_project'].'<br>
							SO '.$row['no_sap'].'<br>
							AR '.$row['no_ar'].'<br>
						</td>';
					}
					else{
						$data .= '<td style="text-align:center">
							<a href="javascript:void(0);" onclick="TampilUpSAP(\''.$row['id_jo'].'\')">
								'.$row['kode_project'].'<br>
							</a>
							'.$row['no_sap'].'
						</td>';
					}


				$data .= '<td style="text-align:center">
						<a href="javascript:void(0);" onClick="window.open('.$link.')">
							'.$row['project_code'].'
						</a>
					</td>

					<td style="text-align:left">'.$row['nama_cust'].'<br>'.$row['no_do'].'</td>				
					<td style="text-align:center">'.$row['asal'].'<br>'.$row['tujuan'].'</td>
					<td style="text-align:right">'.$biaya_kirim.'</td>';
					
					if($id_role != 2)
					{
						$data .= '<td style="text-align:right">
						<button class="btn btn-block btn-default"  
							style="padding:1px;border-radius:0px;width:100%;text-align:right" type="button" 
							onClick="javascript:ListBiaya_Lain('.$row['id_jo'].', '.$row['status'].')"  >
							'.$biaya_kirim_lain.'
						</button>
						</td>';	
					}else{
						$data .='<td></td>';
					}					
						
					$data .= '<td style="text-align:right">'.$uj.'</td>
					<td style="text-align:right">'.$ritase.'</td>';
					
					if($id_role != 2)
					{
						$data .= '<td style="text-align:right">
						<button class="btn btn-block btn-default"  
							style="padding:1px;border-radius:0px;width:100%;text-align:right" type="button" 
							onClick="javascript:ListUJ('.$row['id_jo'].', '.$row['status'].')"  >
							'.$uj_lain.'
						</button>
					</td>';	
					}else{
						$data .='<td></td>';
					}	
					
					
					$data .= '
					<td style="text-align:right">
						<button class="btn btn-block btn-default"  
							style="padding:1px;border-radius:0px;width:100%;text-align:right" type="button" 
							onClick="javascript:ListClaim('.$row['id_jo'].', '.$row['status'].')">
							'.$row['claim'].'
						</button>
					</td>

					<td style="text-align:center">'.$row['created'].'</td>
					<td style="text-align:center">
						<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
					</td>';
				
					if($m_edit == '1' && $row['status'] == '0' ) {
						$xy1="Edit|$row[id_jo]";
						$xy1=base64_encode($xy1);
						$link = "'lcl_data.php?id=$xy1'";
						$data .= '<td>
									<button class="btn btn-block btn-default" title="Edit"
										style="margin:-3px;border-radius:0px" type="button" 
										onClick="javascript:GetData('.$row['id_jo'].')"  >
										<span class="fa fa-edit" ></span>
									</button></td>';
					}
					else
					{					
							$data .='<td></td>';
					}
					
					if($m_del == '1' && $row['status'] == '0' ) 	
					{
						$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:Delete('.$row['id_jo'].')"  >
									<span class="fa fa-close " ></span>
									</button></td>';
					}
					else
					{
						$data .='<td></td>';
					}
					// --------------- EXECUTE SO ---------------
					if($row['status'] == '0' && $id_role != '2'  ) {
						$data .= '<td>
									<button class="btn btn-block btn-default"  title="Execute"
										style="margin:-3px;border-radius:0px" type="button" 
										onClick="javascript:Confirm('.$row['id_jo'].')"  >
										<span class="fa fa-check-square-o " ></span>
									</button></td>';
							
					}else {
						$data .='<td></td>';
					}
					
					// ---------------------- CETAK SO ----------------------	
					if($id_role != '2'){
						$xy1="$row[id_jo]";
						$xy1=base64_encode($xy1);
						$link = "'cetak_so.php?id=$xy1'";
						$data .= '<td>
								<button class="btn btn-block btn-default"  title="Print"
									style="margin:-3px;border-radius:0px" type="button" 									
									onClick="window.open('.$link.') ">
									<span class="fa fa-print" ></span>
								</button></td>';
					}else{
						$data .='<td></td>';
					}	

					// ---------------------- CETAK AR ----------------------	
					if (!empty($row['no_ar'])) {
						$xy1 = base64_encode($row['no_ar']);
						$link = "cetak_ar.php?no_ar={$xy1}";
						if ($row['flag_ar'] === "0") {
							$data .= '<td>
										<button class="btn btn-danger" title="Send Approval"
											style="border-radius:3px" type="button" 
											onclick="ApproveAR(' . $row['no_ar'] . ', this)">
											<span class="fa fa-print"></span>
										</button>
									</td>';
						} else {
							$data .= '<td>
										<button class="btn btn-success" title="Print"
											style="border-radius:3px" type="button" 
											onclick="TampilPrint(' . $row['no_ar'] . ')">
											<span class="fa fa-print"></span>
										</button>
									</td>';
						}
					} else {
						$data .= '<td></td>';
					}

					// ---------------------- CETAK KONTRA BON ----------------------	
					// if (!empty($row['no_ar'])) {
					// 	$xy1 = base64_encode($row['no_ar']);
					// 	$link = "cetak_ar.php?no_ar={$xy1}";
					// 	if ($row['flag_ar'] === "0") {
					// 		$data .= '<td>
					// 					<button class="btn btn-danger" title="Send Approval"
					// 						style="margin:-3px;border-radius:3px" type="button" 
					// 						onclick="ApproveAR(' . $row['no_ar'] . ', this)">
					// 						<span class="fa fa-print"></span>
					// 					</button>
					// 				</td>';
					// 	} else {
					// 		$data .= '<td>
					// 					<button class="btn btn-success" title="Print"
					// 						style="margin:-3px;border-radius:3px" type="button" 
					// 						onclick="TampilPrint(' . $row['no_ar'] . ')">
					// 						<span class="fa fa-print"></span>
					// 					</button>
					// 				</td>';
					// 	}
					// } else {
					// 	$data .= '<td></td>';
					// }
					
					
					$data .= '<td>
							<button class="btn btn-block btn-default" title="Add Attachment"
								style="margin:-3px;border-radius:0px" type="button"
								onClick="AddAttc(' . $row['id_jo'] . ')">
								<span class="fa fa-file"></span>
							</button></td>';
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
					
				if($stat == 'All') {
						$pq = mysqli_query($koneksi, "SELECT count(tr_jo.id_jo) as jml
						from 
						tr_jo left join tr_quo_data on tr_jo.id_detil_quo = tr_quo_data.id_detil
						left join tr_quo on tr_quo_data.id_quo = tr_quo.id_quo
						left join m_kota_tr on tr_jo.id_asal = m_kota_tr.id_kota
						left join m_kota_tr as m_kota1 on tr_jo.id_tujuan = m_kota1.id_kota
						left join m_cust_tr on tr_jo.id_cust = m_cust_tr.id_cust
						left join m_mobil_tr on tr_jo.id_mobil = m_mobil_tr.id_mobil
						left join m_supir_tr on tr_jo.id_supir = m_supir_tr.id_supir
						where tr_jo.tgl_jo between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' ");
						
				}else {
						$pq = mysqli_query($koneksi, "SELECT count(tr_jo.id_jo) as jml
						from 
						tr_jo left join tr_quo_data on tr_jo.id_detil_quo = tr_quo_data.id_detil
						left join tr_quo on tr_quo_data.id_quo = tr_quo.id_quo
						left join m_kota_tr on tr_jo.id_asal = m_kota_tr.id_kota
						left join m_kota_tr as m_kota1 on tr_jo.id_tujuan = m_kota1.id_kota
						left join m_cust_tr on tr_jo.id_cust = m_cust_tr.id_cust
						left join m_mobil_tr on tr_jo.id_mobil = m_mobil_tr.id_mobil
						left join m_supir_tr on tr_jo.id_supir = m_supir_tr.id_supir
						where tr_jo.tgl_jo between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and tr_jo.status = '$stat' ");
				
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

	else if ($_POST['type'] == "Executed"){
		if($_POST['id'] != '' ){	

			$id = $_POST['id'];
			$pq = mysqli_query($koneksi, "SELECT 
					sap_project.kode_project,
					tr_jo.no_jo,
					m_supir_tr.nama_supir,
					m_cust_tr.nama_cust,
					tr_jo.tgl_jo,
					tr_jo.penerima,
					tr_sj.container,
					tr_jo_detail.uj,
					tr_jo.uj_lain,
					tr_jo_detail.ritase,
					tr_jo.ket,
					tr_jo.stapel,
					m_mobil_tr.no_polisi
				FROM tr_jo
				LEFT JOIN sap_project ON sap_project.rowid = tr_jo.sap_project
				LEFT JOIN tr_sj ON tr_sj.no_jo = tr_jo.no_Jo
				LEFT JOIN m_supir_tr ON m_supir_tr.id_supir = tr_sj.id_supir
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_jo.id_cust
				LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
				LEFT JOIN m_mobil_tr ON m_mobil_tr.id_mobil = tr_sj.id_mobil
				WHERE tr_jo.id_jo = '$id'");

			if (!$pq) {
				echo "QUERY ERROR: " . mysqli_error($koneksi);
				exit;
			}

			$attach_sql = "SELECT attachment
						FROM tr_jo_attachment
						WHERE id_jo = '$id'";
			$attachment = mysqli_query($koneksi, $attach_sql);

			$attch = [];
			while ($row = mysqli_fetch_assoc($attachment)) {
				$attch[] = $row['attachment'];
			}

			$foto_so = null;
			$surat_jalan = null;
			$mutasi_rekening = null;

			foreach ($attch as $file) {
				if (strpos($file, 'foto_so') !== false) {
					$foto_so = $file;
				} elseif (strpos($file, 'surat_jalan') !== false) {
					$surat_jalan = $file;
				} elseif (strpos($file, 'mutasi_rekening') !== false) {
					$mutasi_rekening = $file;
				}
			}

			if (is_null($foto_so) || is_null($surat_jalan) || is_null($mutasi_rekening)) {
				echo "GAGAL: Lampiran (foto_so / surat_jalan / mutasi_rekening) belum ada!";
				exit(mysqli_error($koneksi));
			}

			$data = [];
			$total = 0;
			while ($value = mysqli_fetch_assoc($pq)) {
				$nilaiTotal = $value['uj'] + $value['uj_lain'];
				$data[] = [
					'project'     => $value['kode_project'],
					'so'          => $value['no_jo'],
					'driver'      => $value['nama_supir'],
					'customer'    => $value['nama_cust'],
					'tgl_order'   => $value['tgl_jo'],
					'penerima'    => $value['penerima'],
					'total'       => $nilaiTotal,
					'kontainer'   => $value['container'],
					'ritase'      => (int) $value['ritase'],
					'keterangan'  => $value['ket'],
					'stapel'      => $value['stapel'],
					'company'     => '7000',
					'site'        => '9',
					'nopol'       => $value['no_polisi'],
					'foto_so'     		=> $foto_so,
					'surat_jalan'     	=> $surat_jalan,
					'mutasi_rekening'	=> $mutasi_rekening,
				];
				$total += $nilaiTotal;
			}

			$sendApi = [
				'trucking' => $data
			];

			// $payload = json_encode($sendApi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

			header('Content-Type: application/json');
			header('Access-Control-Allow-Origin: *');
			echo json_encode($sendApi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			exit;

			// $ch = curl_init('http://192.168.1.221:8118/api/planning-borong-driver/store');
			// $ch = curl_init('https://cmanco.mitraadipersada.com/api/planning-borong-driver/store');
			$ch = curl_init('http://192.168.1.221:8118/api/planning-borong-driver/store');

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json'
			]);

			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

			$response = curl_exec($ch);

			if (curl_errno($ch)) {
				echo 'cURL Error: ' . curl_error($ch);
			} else {
				echo $response;
			}

			curl_close($ch);


			$sql = "UPDATE tr_jo set 
				status = '1', 
				tagihan = '$total'
				where id_jo = '$id'	";
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
	// else if ($_POST['type'] == "Executed"){
	// 	if($_POST['id'] != '' ){	
			
	// 		$id = $_POST['id'];
	// 		$pq = mysqli_query($koneksi, "SELECT * FROM tr_jo WHERE id_jo = '$id'");
	// 		$rq = mysqli_fetch_array($pq);	
	// 		$harga = (float) $rq['biaya_kirim'];
	// 		$ppn   = (float) $rq['ppn'];
	// 		$pph   = (float) $rq['pph'];
	// 		$claim   = (float) $rq['claim'];

	// 		$total_awal = $harga;
	// 		$t1 = "SELECT tr_jo_biaya.*, m_cost_tr.nama_cost 
	// 			FROM tr_jo_biaya 
	// 			LEFT JOIN m_cost_tr ON tr_jo_biaya.id_cost = m_cost_tr.id_cost
	// 			WHERE tr_jo_biaya.id_jo = '$id' 
	// 			ORDER BY tr_jo_biaya.id_biaya";

	// 		$h1 = mysqli_query($koneksi, $t1); 
	// 		while ($d1 = mysqli_fetch_array($h1)) {
	// 			$total_awal += (float) $d1['harga'];
	// 		}

	// 		$nilai_ppn = ($ppn / 100) * $total_awal;
	// 		$nilai_pph = ($pph / 100) * $total_awal;
	// 		$total = $total_awal + $nilai_ppn - $nilai_pph;


	// 		// ---------------- KIRIM KE API CMANCO ----------------
	// 			// $id_supir = $rq['id_supir'];
	// 			// $supir_sql = "select nama_supir
	// 			// 	from m_supir_tr
	// 			// 	where id_supir = '$id_supir'";
	// 			// $supir 	= mysqli_query($koneksi, $supir_sql); 
	// 			// $spr 	= mysqli_fetch_array($supir);	

	// 			// $id_cust = $rq['id_cust'];
	// 			// $cust_sql = "select nama_cust
	// 			// 	from m_cust_tr
	// 			// 	where id_cust = '$id_cust'";
	// 			// $cust 	= mysqli_query($koneksi, $cust_sql); 
	// 			// $cst 	= mysqli_fetch_array($cust);

	// 			// $id_mobil = $rq['id_mobil'];
	// 			// $mobil_sql = "select no_polisi
	// 			// 	from m_mobil_tr
	// 			// 	where id_mobil = '$id_mobil'";
	// 			// $mobil 	= mysqli_query($koneksi, $mobil_sql); 
	// 			// $no_pol 	= mysqli_fetch_array($mobil);	

	// 			// $attach_sql = "SELECT attachment
	// 			// 			FROM tr_jo_attachment
	// 			// 			WHERE id_jo = '$id'";
	// 			// $attachment = mysqli_query($koneksi, $attach_sql);

	// 			// $attch = [];
	// 			// while ($row = mysqli_fetch_assoc($attachment)) {
	// 			// 	$attch[] = $row['attachment'];
	// 			// }

	// 			// $foto_so = null;
	// 			// $surat_jalan = null;
	// 			// $mutasi_rekening = null;

	// 			// foreach ($attch as $file) {
	// 			// 	if (strpos($file, 'foto_so') !== false) {
	// 			// 		$foto_so = $file;
	// 			// 	} elseif (strpos($file, 'surat_jalan') !== false) {
	// 			// 		$surat_jalan = $file;
	// 			// 	} elseif (strpos($file, 'mutasi_rekening') !== false) {
	// 			// 		$mutasi_rekening = $file;
	// 			// 	}
	// 			// }

	// 			// if (is_null($foto_so) || is_null($surat_jalan) || is_null($mutasi_rekening)) {
	// 			// 	echo "GAGAL: Lampiran (foto_so / surat_jalan / mutasi_rekening) belum ada!";
	// 			// 	exit(mysqli_error($koneksi));
	// 			// }

	// 			// $total_cmanco = $rq['uj'] + $rq['uj_lain'];
	// 			// $data = [
	// 			// 	'project'     		=> $rq['project_code'],
	// 			// 	'so'          		=> $rq['no_jo'],
	// 			// 	'driver'      		=> $spr['nama_supir'],
	// 			// 	'customer'    		=> $cst['nama_cust'],
	// 			// 	'tgl_order'   		=> $rq['tgl_jo'],
	// 			// 	'penerima'    		=> $rq['penerima'],
	// 			// 	'kontainer'   		=> $rq['no_cont'],
	// 			// 	'total'       		=> $total_cmanco,
	// 			// 	'ritase'      		=> $rq['ritase'],
	// 			// 	'keterangan'  		=> $rq['ket'],
	// 			// 	'stapel'  			=> $rq['stapel'],
	// 			// 	'company'     		=> '7000',
	// 			// 	'site'        		=> '9',
	// 			// 	'nopol'       		=> $no_pol['no_polisi'],
	// 			// 	'foto_so'     		=> $foto_so,
	// 			// 	'surat_jalan'     	=> $surat_jalan,
	// 			// 	'mutasi_rekening'	=> $mutasi_rekening,
	// 			// ];

	// 			// $sendApi = [
	// 			// 	'trucking' => $data
	// 			// ];

	// 			// // Encode ke JSON
	// 			// $payload = json_encode($sendApi);
	// 			// // echo $payload;
	// 			// // die();

	// 			// // Inisialisasi cURL
	// 			// // $ch = curl_init('http://127.0.0.1:8000/api/planning-borong-driver/store');
	// 			// // $ch = curl_init('http://192.168.1.221:8118/api/planning-borong-driver/store');
	// 			// $ch = curl_init('https://cmanco.mitraadipersada.com/api/planning-borong-driver/store');

	// 			// // Set opsi cURL
	// 			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 			// curl_setopt($ch, CURLOPT_POST, true);
	// 			// curl_setopt($ch, CURLOPT_HTTPHEADER, [
	// 			// 	'Content-Type: application/json',
	// 			// 	'Content-Length: ' . strlen($payload)
	// 			// ]);
	// 			// curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

	// 			// $response = curl_exec($ch);

	// 			// if (curl_errno($ch)) {
	// 			// 	echo 'cURL Error: ' . curl_error($ch);
	// 			// } else {
	// 			// 	echo "Response dari API:\n";
	// 			// 	echo $response;
	// 			// }
	// 			// curl_close($ch);
	// 		// ---------------- END API  ----------------

	// 		// UPDATE JADI DONE
	// 		$sql = "UPDATE tr_jo set 
	// 			status = '1', 
	// 			tagihan = '$total'
	// 			where id_jo = '$id'	";
	// 		$hasil=mysqli_query($koneksi, $sql);

	// 		if (!$hasil) {	
	// 			exit(mysqli_error($koneksi));
	// 			echo "Data error...!";
	// 		}
	// 		else
	// 		{	
	// 			echo "Data Executed!";
	// 		}
	// 	}	
	// }
	else if ($_POST['type'] == "Add_Order"){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();
		if($_POST['id_cust'] != '' )
		{
			$code_po	 = $_POST['id_po'];
			$id_quo	 	 = $_POST['id_quo'];
			$id_cust	 = $_POST['id_cust'];
			$id_detil_bc = $_POST['id_detil_bc'] ?? '';
			$jenis_po 	 = $_POST['jenis_po'] ?? '';
			$sap_project = $_POST['sap_project'];
			$no_do 		 = addslashes(trim(strtoupper($_POST['no_do'])));
			$penerima 	 = addslashes(trim($_POST['penerima']));
			$id_asal 	 = $_POST['id_asal'];
			$cont_add 	 = $_POST['cont_add'];
			$id_tujuan 	 = $_POST['id_tujuan'];
			$jenis 		 = $_POST['jenis'];	
			$sj_cust 	 = $_POST['sj_cust'];	
			$ket 		 = trim(addslashes($_POST['ket']));
			
			$raw_biaya 	 = $_POST['biaya'];
			$biaya 		 = str_replace(",","", $raw_biaya);
			$raw_uj 	 = $_POST['uj'];
			$uj 		 = str_replace(",","", $raw_uj);
			$raw_ritase	 = $_POST['ritase'];
			$ritase 	 = str_replace(",","", $raw_ritase);

			$tanggal 	 = $_POST['tanggal'];
			$tanggalx 	 = ConverTglSql($tanggal);
			$ptgl 		 = explode("-", $tanggal);
			$tg 		 = $ptgl[0];
			$bl 		 = $ptgl[1];
			$th 		 = $ptgl[2];	

			$query 		 = "SELECT MAX(RIGHT(no_jo,5)) AS maxID 
							FROM tr_jo 
							WHERE YEAR(tgl_jo) = '$th'";
			$hasil 		 = mysqli_query($koneksi, $query);    
			$data  		 = mysqli_fetch_array($hasil);
			$idMax 		 = $data['maxID'];

			// ------------ CHECK AND CREATE PROJECT CODE ------------
				if ($idMax == '99999'){
					$idMax='00000';
				}
				$noUrut = (int) $idMax;   
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
				$year = substr($th,2,2);
				$no_sj = "SO-$year$noUrut";

				
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

			$sql_jo = "INSERT INTO tr_jo (
				sap_project, project_code, id_cust, id_detil_quo, no_jo, id_quo, tgl_jo, no_do, penerima, id_asal, id_tujuan, jenis_mobil, biaya_kirim, uj, ritase, ket, created, jenis_po, id_detil_bc, sj_cust, code_po
			) VALUES (
				'$sap_project','$project_code','$id_cust', '0', '$no_sj', '$id_quo', '$tanggalx', '$no_do', '$penerima',
				'$id_asal', '$id_tujuan', '$jenis', '$biaya', '$uj', '$ritase', '$ket', '$id_user', '$jenis_po', '$id_detil_bc', '$sj_cust', '$code_po'
			)";

			$hasil_jo = mysqli_query($koneksi, $sql_jo);

			if (!$hasil_jo) {
				die("Error insert tr_jo: " . mysqli_error($koneksi));
			}
			$id_so = mysqli_insert_id($koneksi);

			$sql_jo_detail = "INSERT INTO tr_jo_detail (
				id_so, id_asal, id_tujuan, jenis_mobil, harga, uj, ritase, container, remark
			) VALUES (
				'$id_so', '$id_asal', '$id_tujuan', '$jenis', '$biaya', '$uj', '$ritase', '$cont_add', '$ket'
			)";

			$hasil_detail = mysqli_query($koneksi, $sql_jo_detail);

			if (!$hasil_detail) {
				die("Error insert tr_jo_detail: " . mysqli_error($koneksi));
			}

			$sql = mysqli_query($koneksi, "SELECT max(id_jo)as id from tr_jo ");			
			$row = mysqli_fetch_array($sql);
			$id_jo = $row['id'];	
			if (!$hasil) {
				echo "Data DO/PO telah terdaftar...!";
			}
			else {	
				$sql = "UPDATE t_jo_cont SET id_jo_ptj = '$id_jo' WHERE id_cont = '$id_cont' ";	
				$hasil = mysqli_query($koneksi, $sql);
				echo "Data saved!";
			}
		}		
		
	}
	else if ($_POST['type'] == "Update_Order"){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();

		if($_POST['mode'] != '' )
		{	
			$mode 		 = $_POST['mode'];
			$id_jo 		 = $_POST['id_jo'];
			$tanggal 	 = $_POST['tanggal'];
			$no_do 		 = addslashes(trim(strtoupper($_POST['no_do'])));
			$penerima 	 = addslashes(trim($_POST['penerima']));
			$id_asal 	 = $_POST['id_asal'];
			$id_tujuan 	 = $_POST['id_tujuan'];
			$jenis_mobil = $_POST['jenis_mobil'];	
			$id_mobil 	 = $_POST['id_mobil'];
			$id_supir 	 = $_POST['id_supir'];		
			$biaya 		 = $_POST['biaya'];
			$stapel 	 = $_POST['stapel'] ?? 0;
			$uj 		 = $_POST['uj'];
			$ritase 	 = $_POST['ritase'];
			$sj_custx 	 = $_POST['sj_custx'];
			$cont_edit 	 = $_POST['cont_edit'];

			$ket = trim(addslashes($_POST['ket']));
			$biaya = str_replace(",","", $biaya);
			$uj = str_replace(",","", $uj);
			$ritase = str_replace(",","", $ritase);
			$tanggalx = ConverTglSql($tanggal);
			
			if($mode == 'Add')
			{
				$ptgl = explode("-", $tanggal);
				$tg = $ptgl[0];
				$bl = $ptgl[1];
				$th = $ptgl[2];	
				$query = "SELECT max(right(no_jo,5)) as maxID FROM tr_jo where  year(tgl_jo) = '$th'  ";
				$hasil = mysqli_query($koneksi, $query);    
				$data  = mysqli_fetch_array($hasil);
				$idMax = $data['maxID'];
				if ($idMax == '99999'){
					$idMax='00000';
				}
				$noUrut = (int) $idMax;   
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
				$year = substr($th,2,2);
				$no_sj = "SJ-$year$noUrut";
				
				$sql = "INSERT INTO  tr_jo (
						id_detil_quo, no_jo, tgl_jo, no_do, penerima,
						id_asal, id_tujuan, jenis_mobil, biaya_kirim, uj, ritase, ket, created) 
						values(
						'$id_detil', '$no_sj', '$tanggalx', '$no_do', '$penerima','$id_asal', '$id_tujuan', '$jenis_mobil', '$biaya', '$uj', '$ritase', '$ket', '$id_user')";
						
				$hasil= mysqli_query($koneksi, $sql);
			}else{
				$sql = "UPDATE tr_jo set 
							sj_cust = '$sj_custx',
							penerima = '$penerima',
							stapel = '$stapel',
							ket = '$ket'
							where id_jo = '$id_jo'";
				$hasil= mysqli_query($koneksi, $sql);

				$sql_detail 	= "UPDATE tr_jo_detail SET 
								container = '$cont_edit'
								WHERE id_so = '$id_jo'";

				// echo $sql_detail;
				// die();
				$hasil_detail	= mysqli_query($koneksi, $sql_detail);
			}
			
			if (!$hasil) {
				echo "Data Error...!";
			} else {	
				echo "Data saved!";
			}
		}		

	}
	else if ($_POST['type'] == "Update_PPN"){
		if($_POST['id_jo'] != '' )
		{	
			$id_jo = $_POST['id_jo'];
			$ppn = $_POST['ppn'];
			$pph = $_POST['pph'];
			$sql = "update tr_jo set 
					ppn = '$ppn',
					pph = '$pph'
					where id_jo = '$id_jo'	";
			$hasil=mysqli_query($koneksi,$sql);
			if (!$hasil) {
			
				echo "Data Error...!";
			}
			else
			{	
				
				echo "Data saved!";
			}
		}	

		
	}
	else if ($_POST['type'] == "Del_Order"){
		$id = $_POST['id']; 	
				
		$id = mysqli_real_escape_string($koneksi, $_POST['id']);
		$sql2 = "SELECT no_jo FROM tr_jo WHERE id_jo = '$id'";
		$hasil2 = mysqli_query($koneksi, $sql2);

		if ($hasil2 && mysqli_num_rows($hasil2) > 0) {
			$row = mysqli_fetch_assoc($hasil2);
			$no_jo = $row['no_jo'];

			$sqlDel = "DELETE FROM tr_sj WHERE no_jo = '$no_jo'";
			$result = mysqli_query($koneksi, $sqlDel);

		} else {
			echo "Data id_jo $id tidak ditemukan di tr_jo";
		}

		$sql = "UPDATE t_jo_cont SET id_jo_ptj = '0' WHERE id_jo_ptj = '$id' ";	
		$hasil = mysqli_query($koneksi, $sql);
				
		$del = mysqli_query($koneksi, "DELETE FROM tr_jo_uj WHERE id_jo = '$id' ");
		$del = mysqli_query($koneksi, "DELETE FROM tr_jo_biaya WHERE id_jo = '$id' ");
		$query = "DELETE FROM  tr_jo WHERE id_jo = '$id' ";
		if (!$result = mysqli_query($koneksi, $query)) {
			exit(mysqli_error($koneksi));
		}	

	}
	else if ($_POST['type'] == "Detil_Data"){
		$id = $_POST['id'];	
		$query = "SELECT 
					tr_jo.*, 
					tr_jo_detail.jenis_mobil, 
					tr_jo_detail.harga, 
					tr_jo_detail.uj, 
					tr_jo_detail.ritase, 
					tr_jo_detail.container, 
					tr_quo.quo_no, 
					m_cust_tr.nama_cust, 
					m_asal.nama_kota AS asal, 
					m_tujuan.nama_kota AS tujuan,
					m_mobil_tr.no_polisi, 
					m_supir_tr.nama_supir,
					sap_project.kode_project
				FROM tr_jo
				LEFT JOIN tr_quo_data ON tr_jo.id_detil_quo = tr_quo_data.id_detil
				LEFT JOIN tr_quo ON tr_jo.id_quo = tr_quo.id_quo
				LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
				LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_jo_detail.id_asal
				LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_jo_detail.id_tujuan
				LEFT JOIN m_cust_tr ON tr_quo.id_cust = m_cust_tr.id_cust
				LEFT JOIN m_mobil_tr ON tr_jo.id_mobil = m_mobil_tr.id_mobil
				LEFT JOIN m_supir_tr ON tr_jo.id_supir = m_supir_tr.id_supir
				LEFT JOIN sap_project ON tr_jo.sap_project = sap_project.rowid
					WHERE tr_jo.id_jo  = '$id'";

		// echo $query;
		// die();

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

		// echo $id_jo;
		// die();
		echo json_encode($response);

	}
	else if($_GET['type'] == "List_Biaya_Lain"){
		// echo "<pre>";
		// print_r($_GET);
		// echo "</pre>";
		// die();

		$id = $_GET['id'];
		$stat = $_GET['stat'];
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>	
						<th rowspan="2" width="5%" style="text-align: center;">NO</th>
						<th rowspan="2" width="60%" style="text-align: center;">INFORMATION</th>
						<th rowspan="2" width="15%" style="text-align: center;">COST</th>
						<th rowspan="2" width="15%" style="text-align: center;">PPN</th>
						<th rowspan="2" width="15%" style="text-align: center;">WTAX</th>
						<th rowspan="2" width="5%" style="text-align: center;">EDIT</th>		
						<th rowspan="2" width="5%" style="text-align: center;">DEL</th>		
					</tr>
				</thead>';

		$t1="SELECT  
				tr_jo_biaya.*, 
				m_cost_tr.nama_cost 
			FROM tr_jo_biaya 
			LEFT JOIN m_cost_tr ON tr_jo_biaya.id_cost = m_cost_tr.id_cost
				WHERE tr_jo_biaya.id_jo = '$id' ORDER BY tr_jo_biaya.id_biaya";

		$h1=mysqli_query($koneksi, $t1);   
		while ($d1=mysqli_fetch_array($h1))		
		{
			$biaya = number_format($d1['harga'],0);
			$pph = number_format($d1['pph'],0);
			$wtax = number_format($d1['wtax'],0);
			$total = $total + $d1['harga'];
			$n++;
			$data .= '<tr>							
				<td style="text-align:center">'.$n.'.</td>
				<td style="text-align:left">'.$d1['nama_cost'].' - '.$d1['remark'].'</td>	
				<td style="text-align:right">'.$biaya.'</td> 
				<td style="text-align:right">'.$pph.'</td> 
				<td style="text-align:right">'.$wtax.'</td> '
				;	
			
				if($stat != '1' ){
					$data .= '<td>
						<button class="btn btn-block btn-default"  title="Delete"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetBiayaLain('.$d1['id_biaya'].')"  >
						<span class="fa fa-edit"></span>
						</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}	
				if($stat != '1' ){
					
					$data .= '<td>
						<button class="btn btn-block btn-default"  title="Delete"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:DelBiayaLain('.$d1['id_biaya'].')"  >
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
		
		$sql = "UPDATE tr_jo set biaya_kirim_lain = '$total' where id_jo = '$id'	";
		$hasil=mysqli_query($koneksi,$sql);
				
		echo $data;	
	}
	else if ($_POST['type'] == "Add_Biaya_Lain"){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		// die();
		if($_POST['mode'] != '' ) {	
			$id_jo 		 = $_POST['id_jo'];
			$mode 		 = $_POST['mode'];
			$id 		 = $_POST['id'];
			$id_cost	 = $_POST['id_cost'];
			$biaya 		 = $_POST['biaya'];
			$pph 		 = $_POST['pph'];
			$wtax 		 = $_POST['wtax'];
			$remark_cost = $_POST['remark_cost'];
			$biaya		 = str_replace(",","", $biaya);
			
			if ($mode == 'Add') {
				$sql = "INSERT INTO  tr_jo_biaya (id_jo, id_cost, harga, pph, wtax, remark) 
						VALUES ('$id_jo', '$id_cost', '$biaya', '$pph', '$wtax', '$remark_cost')";
				$hasil= mysqli_query($koneksi, $sql);
			} else {
				$sql = "UPDATE tr_jo_biaya SET 
						id_cost = '$id_cost',
						harga = '$biaya',
						pph = '$pph',
						wtax = '$wtax'
						WHERE id_biaya = '$id'	";
				$hasil=mysqli_query($koneksi,$sql);
			}

			if (!$hasil) {
				echo "Data Error...!";
			} else {	
				echo "Data saved!";
			}
		}	
	}

	else if ($_POST['type'] == "Detil_Biaya_Lain"){
		$id = $_POST['id'];	
		$query = "SELECT * FROM  tr_jo_biaya WHERE id_biaya  = '$id'";
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
	else if ($_POST['type'] == "Del_Biaya_Lain"){
		$id = $_POST['id']; 
		$query = "DELETE FROM tr_jo_biaya WHERE id_biaya = '$id' ";
		if (!$result = mysqli_query($koneksi, $query)) {
			exit(mysqli_error($koneksi));
		}	
		
	}
	else if($_GET['type'] == "List_UJ"){
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
		$t1="select  tr_jo_uj.*, m_cost_tr.nama_cost from
			tr_jo_uj left join m_cost_tr on tr_jo_uj.id_cost = m_cost_tr.id_cost
				where tr_jo_uj.id_jo = '$id' order by tr_jo_uj.id_uj";
		$h1=mysqli_query($koneksi, $t1);   
		while ($d1=mysqli_fetch_array($h1))		
		{
			$biaya = number_format($d1['harga'],0);
			$total = $total + $d1['harga'];
			$n++;
			$data .= '<tr>							
				<td style="text-align:center">'.$n.'.</td>
				<td style="text-align:left">'.$d1['nama_cost'].'</td>	
				<td style="text-align:right">'.$biaya.'</td> ';	
			
				if($stat != '1' ){
					$data .= '<td>
						<button class="btn btn-block btn-default"  title="Delete"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetUJ('.$d1['id_uj'].')"  >
						<span class="fa fa-edit " ></span>
						</button></td>';
				}
				else
				{
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
	else if ($_POST['type'] == "Add_UJ"){
		if($_POST['mode'] != '' )
		{	
			$id_jo = $_POST['id_jo'];
			$mode = $_POST['mode'];
			$id = $_POST['id'];
			$id_cost = $_POST['id_cost'];
			$biaya = $_POST['biaya'];
			$biaya = str_replace(",","", $biaya);
			
			if($mode == 'Add')
			{			
				$sql = "INSERT INTO  tr_jo_uj (id_jo, id_cost, harga) values
						('$id_jo', '$id_cost', '$biaya')";
				$hasil= mysqli_query($koneksi, $sql);
			}
			else
			{
				$sql = "update tr_jo_uj set 
						id_cost = '$id_cost',
						harga = '$biaya'
						where id_uj = '$id'	";
				$hasil=mysqli_query($koneksi,$sql);
			}
			if (!$hasil) {
			
				echo "Data Error...!";
			}
			else
			{	
				
				echo "Data saved!";
			}
		}	


	}
	else if ($_POST['type'] == "Add_Claim") {
		$id_jo  = $_POST['id_jo'];
		$status = $_POST['status'];

		$biaya  = str_replace(",", "", $_POST['biaya']);
		$biaya  = floatval($biaya);

		$sql = "UPDATE tr_jo
				SET claim = '$biaya'
				WHERE id_jo = '$id_jo'";
				
		$hasil = mysqli_query($koneksi, $sql);

		if (!$hasil) {
			echo "Data Error...!";
		} else {
			echo "Data saved!";
		}
	}

	else if ($_POST['type'] == "Detil_UJ"){
		$id = $_POST['id'];	
		$query = "select * from  tr_jo_uj where id_uj  = '$id'";
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
	else if ($_POST['type'] == "Del_UJ"){
		$id = $_POST['id']; 
		$query = "DELETE FROM tr_jo_uj WHERE id_uj = '$id' ";
		if (!$result = mysqli_query($koneksi, $query)) {
			exit(mysqli_error($koneksi));
		}	
		
		
	}

	// ------------- LIST PO PTL -------------
	else if ($_GET['type'] == "ListPO"){
		$cari = $_GET['cari'];
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>
						<th width="7%" style="text-align: center;">NO</th>
						<th width="27%" style="text-align: center;">NO. PO</th>
						<th width="18%" style="text-align: center;">NO. CONTAINER</th>
						<th width="11%" style="text-align: center;">FEET</th>
						<th width="28%" style="text-align: center;">TUJUAN</th>
						<th width="9%" style="text-align: center;">ADD</th>
					</tr>
				</thead>';	
		$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
		$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
		
		//$SQL = "select * from m_cust_tr where nama_cust LIKE '%$cari%' and status = '1'  order by nama_cust LIMIT 0, 25";
		
		$SQL = "SELECT 
					t_jo_bc_cont.*, 
					t_jo_cont.no_cont, 
					t_jo_cont.feet, 
					t_jo_tagihan.no_tagihan, 
					m_kota_tr.nama_kota 
				from t_jo_bc_cont 
				left join t_jo_cont on t_jo_bc_cont.id_cont = t_jo_cont.id_cont
				left join t_jo_bc on t_jo_bc_cont.id_bc = t_jo_bc.id_bc
				left join t_jo_tagihan on t_jo_bc.id_jo_tagihan = t_jo_tagihan.id_tagihan
				left join m_kota_tr on t_jo_bc.id_kota = m_kota_tr.id_kota 
				where t_jo_cont.id_jo_ptj <= '0' 
					and t_jo_bc.id_jo_tagihan >= 0
				order by t_jo_cont.no_cont LIMIT 0, 25";

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
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihPO('.$row['id_detil'].')" >'.$row['no_tagihan'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihPO('.$row['id_detil'].')" >'.$row['no_cont'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihPO('.$row['id_detil'].')" >'.$row['feet'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihPO('.$row['id_detil'].')" >'.$row['nama_kota'].'</a></td>';
				$data .= '<td style="text-align:center">
						<button type="button" class="btn btn-default" onClick="javascript:PilihPO('.$row['id_detil'].')" 
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
	else if ($_POST['type'] == "DetilPO"){
		$id = $_POST['id'];	
		$query = "SELECT 
					t_jo_bc_cont.*, 
					t_jo_bc.alamat_ambil, 
					t_jo_bc.id_kota, 
					t_jo_bc.id_asal, 
					t_jo_cont.ket, 
					t_jo_cont.berat, 
					t_jo_cont.vol,
					t_jo_cont.no_cont, 
					t_jo_cont.feet, 
					t_jo_tagihan.no_tagihan,
					 m_kota_tr.nama_kota,
					  m_cust.nama_cust 
				from t_jo_bc_cont 
				left join t_jo_cont on t_jo_bc_cont.id_cont = t_jo_cont.id_cont
				left join t_jo_bc on t_jo_bc_cont.id_bc = t_jo_bc.id_bc
				left join t_jo_tagihan on t_jo_bc.id_jo_tagihan = t_jo_tagihan.id_tagihan
				left join m_kota_tr on t_jo_bc.id_kota = m_kota_tr.id_kota 
				left join t_jo on t_jo_bc.id_jo = t_jo.id_jo
				left join m_cust on t_jo.id_cust = m_cust.id_cust
				where t_jo_bc_cont.id_detil = '$id' ";
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

	// ------------- SEND SO TO SAP -------------
		else if ($_GET['type'] == "ListSAP"){
			$cari = $_GET['cari'];
			$data = '<table class="table table-hover table-striped" style="width:100%">
					<thead style="font-weight:500px !important">
						<tr>
							<th width="7%" style="text-align: center;">NO</th>
							<th width="27%" style="text-align: center;">SAP Project</th>
							<th width="9%" style="text-align: center;">ADD</th>
						</tr>
					</thead>';	
			$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
			$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
			
			$SQL = "SELECT * 
				FROM sap_project 
					WHERE kode_project LIKE '%$cari%'
					ORDER BY CAST(SUBSTRING_INDEX(kode_project, ' ', -1) AS UNSIGNED) DESC
					LIMIT 0, 10";

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

					$data .= '<td style="text-align:center"><a href="#" onclick="PilihSAP('.$row['rowid'].')" >'.$row['kode_project'].'</a></td>';

					$data .= '<td style="text-align:center">
							<button type="button" class="btn btn-default" onClick="javascript:PilihSAP('.$row['rowid'].')" 
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
		else if ($_POST['type'] == "DetilSAP"){
			$id = $_POST['id'];	
			
			$query = "SELECT *
					FROM sap_project 
					WHERE rowid = '$id'";
			
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
		else if ($_GET['type'] == "ListUpSAP") {
			$cari   = mysqli_real_escape_string($koneksi, $_GET['cari']);
			$id_jo  = mysqli_real_escape_string($koneksi, $_GET['id_jo']);

			$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead>
					<tr>
						<th width="5%" style="text-align: center;">NO</th>
						<th width="15%" style="text-align: center;">Date</th>
						<th width="15%" style="text-align: center;">SAP Project</th>
						<th width="15%" style="text-align: center;">No SO</th>
						<th width="27%" style="text-align: center;">Nama Customer</th>
						<th width="10%" style="text-align: center;">ADD</th>
					</tr>
				</thead>';

			$sql_jo = "SELECT 
						tr_jo.tgl_jo, 
						tr_jo.sap_project, 
						tr_jo.id_cust,
						tr_jo_detail.id_asal,
						tr_jo_detail.id_tujuan
					FROM tr_jo 
					LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
					WHERE tr_jo.status = '0' AND tr_jo.id_jo = '$id_jo' AND tr_jo.no_sap IS NULL
					LIMIT 1";

			$query_jo = mysqli_query($koneksi, $sql_jo);

			if ($query_jo && mysqli_num_rows($query_jo) > 0) {
				$dj         = mysqli_fetch_assoc($query_jo);
				$tgl_jo     = $dj['tgl_jo'];
				$sap_project= $dj['sap_project'];
				$id_cust    = $dj['id_cust'];
				$id_asal    = $dj['id_asal'];
				$id_tujuan    = $dj['id_tujuan'];
			} else {
				$tgl_jo = $sap_project = $id_cust = null;
			}

			$SQL = "SELECT 
						tr_jo.*, 
						sap_project.kode_project, 
						m_cust_tr.nama_cust,
						tr_jo_detail.id_asal,
						tr_jo_detail.id_tujuan
					FROM tr_jo 
					LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
					LEFT JOIN sap_project ON sap_project.rowid = tr_jo.sap_project
					LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_jo.id_cust
					WHERE tr_jo.no_jo LIKE '%$cari%' 
						AND tr_jo.tgl_jo = '$tgl_jo'
						AND tr_jo.sap_project = '$sap_project'
						AND tr_jo_detail.id_asal = '$id_asal'
						AND tr_jo_detail.id_tujuan = '$id_tujuan'
						AND tr_jo.no_sap IS NULL
					ORDER BY tr_jo.no_jo 
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
					$data .= '<td style="text-align:center">'.$row['tgl_jo'].'</td>';
					$data .= '<td style="text-align:center">'.$row['kode_project'].'</td>';
					$data .= '<td style="text-align:center">'.$row['no_jo'].'</td>';
					$data .= '<td style="text-align:center">'.$row['nama_cust'].'</td>';

					$id_jo = isset($row['id_jo']) ? $row['id_jo'] : $row['no_jo'];
					$data .= '<td style="text-align:center">
								<label>
								<input type="checkbox" 
										name="sap_selected[]" 
										value="'.$id_jo.'">
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

			if (empty($ids)) {
				echo json_encode([
					"success" => false,
					"message" => "Tidak ada data yang dipilih"
				]);
				exit;
			}

			$resultData = [];
			$detailCount = [];

			foreach ($ids as $id_jo) {
				$id_jo = mysqli_real_escape_string($koneksi, $id_jo);

				$sql_header = "SELECT 
									tr_jo.*,
									tr_jo_detail.jenis_mobil,
									sap_project.kode_project,
									m_cust_tr.nama_cust,
									m_cust_tr.caption,
									kota_asal.nama_kota AS asal,
									kota_tujuan.nama_kota AS tujuan
							FROM tr_jo
							LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
							LEFT JOIN sap_project ON sap_project.rowid = tr_jo.sap_project
							LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_jo.id_cust
							LEFT JOIN m_kota_tr AS kota_asal ON tr_jo_detail.id_asal = kota_asal.id_kota
							LEFT JOIN m_kota_tr AS kota_tujuan ON tr_jo_detail.id_tujuan = kota_tujuan.id_kota
							WHERE tr_jo.id_jo = '$id_jo' LIMIT 1";
				$query_header = mysqli_query($koneksi, $sql_header);

				while ($row = mysqli_fetch_assoc($query_header)) {
					if (empty($row['caption'])) {
						echo json_encode([
							"success" => false,
							"message" => "custcode masih kosong, silahkan hubungi tim IT-SAP"
						]);
						exit;
					}

					$id_jo = mysqli_real_escape_string($koneksi, $row['id_jo']);
					$lines = []; // ✅ Letakkan di sini supaya detail dan biaya tergabung

					// DETAIL JO
					$sql_detail = "SELECT
							CONCAT(m_asal.nama_kota, ' - ', m_tujuan.nama_kota) AS rute_jo,
							tr_jo_detail.jenis_mobil,
							tr_jo_detail.harga,
							tr_sj.container,
							tr_sj.id_sj,
							CONCAT(m_tujuan.nama_kota, '#', tr_jo_detail.jenis_mobil) AS remark
						FROM tr_jo
						INNER JOIN tr_sj ON tr_sj.no_jo = tr_jo.no_jo
						LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
						LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_jo_detail.id_asal 
						LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_jo_detail.id_tujuan 
						WHERE tr_jo.id_jo = '$id_jo'";

					$query_detail = mysqli_query($koneksi, $sql_detail);

					if ($query_detail) {
						while ($det = mysqli_fetch_assoc($query_detail)) {
							$lines[] = [
								"ItemCode"   => "LJLOTO.0000TRUCKIN",
								"ItemName"   => $det['rute_jo'],
								"Qty"   	 => 1,
								"JenisMobil" => $det['jenis_mobil'],
								"Whse"	 	 => "WH-FG",
								"BiayaKirim" => $det['harga'] ?? 0,
								"Disc"	 	 => 0,
								"PPN"	 	 => 0,
								"PPH"	 	 => 0,
								"Container"  => $det['container'],
								"Route" 	 => $det['remark'],
								"Remark" 	 => $det['remark'],
								"NoSO"       => $row['no_jo'],
							];
						}
					}

					if (empty($lines)) {
						echo json_encode([
							"success" => false,
							"message" => $row['no_jo'] ." Masih belum ada SJ, Buat lebih dahulu sebelum di UP ke SAP"
						]);
						exit;
					}

					// BIAYA JO
					$q_biaya = "SELECT
							m_cost_tr.itemcode,
							m_cost_tr.nama_cost,
							CONCAT(m_cost_tr.nama_cost,'-',tr_jo_biaya.remark) AS remark,
							tr_jo_biaya.harga,
							tr_jo_biaya.pph AS ppn,
							tr_jo_biaya.wtax
						FROM tr_jo_biaya
						LEFT JOIN m_cost_tr ON m_cost_tr.id_cost = tr_jo_biaya.id_cost
						WHERE tr_jo_biaya.id_jo = '$id_jo'";
						
					$r_biaya = mysqli_query($koneksi, $q_biaya);

					if ($r_biaya) {
						while ($cost = mysqli_fetch_assoc($r_biaya)) {
							$lines[] = [
								"ItemCode"   => $cost['itemcode'],
								"ItemName"   => $cost['nama_cost'],
								"Qty"   	 => 1,
								"JenisMobil" => '',
								"Whse"	 	 => "WH-FG",
								"BiayaKirim" => $cost['harga'] ?? 0,
								"Disc"	 	 => 0,
								"PPN"	 	 => $cost['ppn'],
								"PPH"	 	 => $cost['wtax'],
								"Container"  => '',
								"Route" 	 => '',
								"Remark" 	 => $cost['remark'],
								"NoSO"       => $row['no_jo'],
							];
						}
					}

					// ✅ Gabungkan ke resultData
					$key = $row['caption'].'|'.$row['tgl_jo'].'|'.$row['kode_project'];

					if (!isset($resultData[$key])) {
						$resultData[$key] = [
							"TglSO"    	=> date('Y-m-d'),
							"TglKirim" 	=> $row['tgl_jo'],
							"CustCode" 	=> $row['caption'],
							"CustName" 	=> $row['nama_cust'],
							"Project"  	=> $row['kode_project'],
							"Remarks"  	=> "",
							"Penerima" 	=> $row['penerima'],
							"Sales" 	=> 22,
							"TipeSales" => 5,
							"Lines"    	=> []
						];
						$detailCount[$key] = 0;
					}

					$resultData[$key]['Lines'] = array_merge($resultData[$key]['Lines'], $lines);
					$detailCount[$key] += count($lines);

					$resultData[$key]['Remarks'] = $row['nama_cust'].' '.$row['tgl_jo'].' '.$row['kode_project'].' '.$detailCount[$key].'x'.$row['jenis_mobil'].' '.$row['asal'].'-'.$row['tujuan'];
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
				$apiUrl = "https://wsp.mitraadipersada.com/trucking/sales-order.php";
				// $apiUrl = "http://192.168.1.153/trucking/sales-order.php";

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
				$resultDataLog = json_encode($apiResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				$rawData = json_encode($resultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

				if (!$apiResponse || !isset($apiResponse['docnum'])) {
					$success = false;
					$mssg    = $apiResponse['mssg'] ?? 'Invalid API response';
					mysqli_query($koneksi, "INSERT INTO tr_api_logs (docnum, raw_data, `desc`, result) 
						VALUES (
							'', 
							'" . mysqli_real_escape_string($koneksi, $rawData) . "', 
							'ERROR-" . mysqli_real_escape_string($koneksi, $mssg) . "',
							'" . mysqli_real_escape_string($koneksi, $resultDataLog) . "'
						)
					");

				} else {
					$success = true;
					$desc    = 'SUCCESS';
					mysqli_query($koneksi, "INSERT INTO tr_api_logs (docnum, raw_data, `desc`, result) 
						VALUES (
							'" . mysqli_real_escape_string($koneksi, $apiResponse['docnum']) . "', 
							'" . mysqli_real_escape_string($koneksi, $rawData) . "', 
							'" . mysqli_real_escape_string($koneksi, $desc) . "',
							'" . mysqli_real_escape_string($koneksi, $resultDataLog) . "'
						)
					");

					foreach ($ids as $id_jo) {
						$id_jo = (int)$id_jo;
						$docnum = mysqli_real_escape_string($koneksi, $apiResponse['docnum']);
						$sql_update = "UPDATE tr_jo 
									SET no_sap = '$docnum' 
									WHERE id_jo = $id_jo";
						mysqli_query($koneksi, $sql_update);
					}
				}

				echo json_encode([
					"success" => $success,
					"data"    => $apiResponse,
					"sent"    => $resultData
				], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		}

	// ------------- SEND SO TO SAP -------------
		else if ($_GET['type'] == "ListUpAR") {
			$cari   = mysqli_real_escape_string($koneksi, $_GET['cari']);
			$id_jo  = mysqli_real_escape_string($koneksi, $_GET['id_jo']);

			$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead>
					<tr>
						<th width="5%" style="text-align: center;">NO</th>
						<th width="15%" style="text-align: center;">Date</th>
						<th width="15%" style="text-align: center;">SAP Project</th>
						<th width="15%" style="text-align: center;">No SO SAP</th>
						<th width="15%" style="text-align: center;">No SO</th>
						<th width="27%" style="text-align: center;">Nama Customer</th>
						<th width="10%" style="text-align: center;">ADD</th>
					</tr>
				</thead>';

			$sql_jo = "SELECT 
						tr_jo.tgl_jo, 
						tr_jo.sap_project, 
						tr_jo.id_cust,
						tr_jo_detail.id_asal,
						tr_jo_detail.id_tujuan
					FROM tr_jo 
					LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
					WHERE tr_jo.status = '0' 
						AND tr_jo.id_jo = '$id_jo' 
						AND tr_jo.no_ar IS NULL
					LIMIT 1";

			$query_jo = mysqli_query($koneksi, $sql_jo);

			if ($query_jo && mysqli_num_rows($query_jo) > 0) {
				$dj         = mysqli_fetch_assoc($query_jo);
				$tgl_jo     = $dj['tgl_jo'];
				$sap_project= $dj['sap_project'];
				$id_cust    = $dj['id_cust'];
				$id_asal    = $dj['id_asal'];
				$id_tujuan    = $dj['id_tujuan'];
			} else {
				$tgl_jo = $sap_project = $id_cust = null;
			}

			$SQL = "SELECT 
						tr_jo.*, 
						sap_project.kode_project, 
						m_cust_tr.nama_cust,
						tr_jo_detail.id_asal,
						tr_jo_detail.id_tujuan
					FROM tr_jo 
					LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
					LEFT JOIN sap_project ON sap_project.rowid = tr_jo.sap_project
					LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_jo.id_cust
					WHERE tr_jo.no_jo LIKE '%$cari%' 
						AND tr_jo.tgl_jo = '$tgl_jo'
						AND tr_jo.sap_project = '$sap_project'
						AND tr_jo_detail.id_asal = '$id_asal'
						AND tr_jo_detail.id_tujuan = '$id_tujuan'
						AND tr_jo.no_ar IS NULL
					ORDER BY tr_jo.no_jo 
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
					$data .= '<td style="text-align:center">'.$row['tgl_jo'].'</td>';
					$data .= '<td style="text-align:center">'.$row['kode_project'].'</td>';
					$data .= '<td style="text-align:center">'.$row['no_sap'].'</td>';
					$data .= '<td style="text-align:center">'.$row['no_jo'].'</td>';
					$data .= '<td style="text-align:center">'.$row['nama_cust'].'</td>';

					$id_jo = isset($row['id_jo']) ? $row['id_jo'] : $row['no_jo'];
					$data .= '<td style="text-align:center">
								<label>
								<input type="checkbox" 
										name="ar_selected[]" 
										value="'.$id_jo.'">
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
		else if ($_POST['type'] == "SaveUpAR") {
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
				"CustCode" => null,
				"Lines"    => []
			];

			foreach ($ids as $id_jo) {
				$id_jo = mysqli_real_escape_string($koneksi, $id_jo);

				$sql_header = "SELECT tr_jo.*,
									m_cust_tr.caption 
								FROM tr_jo 
								LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_jo.id_cust
								WHERE id_jo = '$id_jo'";
				$query_header = mysqli_query($koneksi, $sql_header);
				$row = mysqli_fetch_assoc($query_header);

				if ($row) {
					// Set CustCode sekali saja (ambil dari data pertama)
					if ($resultData["CustCode"] === null) {
						$resultData["CustCode"] = $row['caption'];
					}

					$resultData["Lines"][] = [
						"NoSO" => $row['no_jo']
					];
				}
			}

			$output = [$resultData];

			// ----------- NO SEND API (LIHAT JSON) -----------
				// header('Content-Type: application/json');
				// echo "<pre>";
				// echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				// echo "<pre>";
				// die();

			// ----------- KIRIM API ----------- 
				$apiUrl = "https://wsp.mitraadipersada.com/trucking/sales-invoice.php";
				// $apiUrl = "http://192.168.1.153/trucking/sales-invoice.php";

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
							'AR', 
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
							'AR', 
							'" . mysqli_real_escape_string($koneksi, $rawData) . "', 
							'" . mysqli_real_escape_string($koneksi, $desc) . "',
							'" . mysqli_real_escape_string($koneksi, $resultDataLog) . "'
						)
					");

					foreach ($ids as $id_jo) {
						$id_jo = (int)$id_jo;
						$docnum = mysqli_real_escape_string($koneksi, $apiResponse['docnum']);
						$sql_update = "UPDATE tr_jo 
									SET no_ar = '$docnum' 
									WHERE id_jo = $id_jo";
						mysqli_query($koneksi, $sql_update);


						// ---------------- SEND API CMANCO ----------------
							$id = $_POST['id'];
							$pq = mysqli_query($koneksi, "SELECT 
									sap_project.kode_project,
									tr_jo.no_jo,
									m_supir_tr.nama_supir,
									m_cust_tr.nama_cust,
									tr_jo.tgl_jo,
									tr_jo.penerima,
									tr_sj.container,
									tr_jo_detail.uj,
									tr_jo.uj_lain,
									tr_jo_detail.ritase,
									tr_jo.ket,
									tr_jo.stapel,
									m_mobil_tr.no_polisi
								FROM tr_jo
								LEFT JOIN sap_project ON sap_project.rowid = tr_jo.sap_project
								LEFT JOIN tr_sj ON tr_sj.no_jo = tr_jo.no_Jo
								LEFT JOIN m_supir_tr ON m_supir_tr.id_supir = tr_sj.id_supir
								LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_jo.id_cust
								LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
								LEFT JOIN m_mobil_tr ON m_mobil_tr.id_mobil = tr_sj.id_mobil
								WHERE tr_jo.id_jo = '$id_jo'");

							if (!$pq) {
								echo "QUERY ERROR: " . mysqli_error($koneksi);
								exit;
							}

							$attach_sql = "SELECT attachment
										FROM tr_jo_attachment
										WHERE id_jo = '$id_jo'";
							$attachment = mysqli_query($koneksi, $attach_sql);

							$attch = [];
							while ($row = mysqli_fetch_assoc($attachment)) {
								$attch[] = $row['attachment'];
							}

							$foto_so = null;
							$surat_jalan = null;
							$mutasi_rekening = null;

							foreach ($attch as $file) {
								if (strpos($file, 'foto_so') !== false) {
									$foto_so = $file;
								} elseif (strpos($file, 'surat_jalan') !== false) {
									$surat_jalan = $file;
								} elseif (strpos($file, 'mutasi_rekening') !== false) {
									$mutasi_rekening = $file;
								}
							}

							if (is_null($foto_so) || is_null($surat_jalan) || is_null($mutasi_rekening)) {
								echo "GAGAL: Lampiran (foto_so / surat_jalan / mutasi_rekening) belum ada!";
								exit(mysqli_error($koneksi));
							}

							$data = [];
							$total = 0;
							while ($value = mysqli_fetch_assoc($pq)) {
								$nilaiTotal = $value['uj'] + $value['uj_lain'];
								$data[] = [
									'project'     => $value['kode_project'],
									'so'          => $value['no_jo'],
									'driver'      => $value['nama_supir'],
									'customer'    => $value['nama_cust'],
									'tgl_order'   => $value['tgl_jo'],
									'penerima'    => $value['penerima'],
									'total'       => $nilaiTotal,
									'kontainer'   => $value['container'],
									'ritase'      => (int) $value['ritase'],
									'keterangan'  => $value['ket'],
									'stapel'      => $value['stapel'],
									'company'     => '7000',
									'site'        => '9',
									'nopol'       => $value['no_polisi'],
									'foto_so'     		=> $foto_so,
									'surat_jalan'     	=> $surat_jalan,
									'mutasi_rekening'	=> $mutasi_rekening,
								];
								$total += $nilaiTotal;
							}

							$sendApi = [
								'trucking' => $data
							];

							// $payload = json_encode($sendApi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

							header('Content-Type: application/json');
							header('Access-Control-Allow-Origin: *');
							echo json_encode($sendApi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
							exit;

							// $ch = curl_init('http://192.168.1.221:8118/api/planning-borong-driver/store');
							// $ch = curl_init('https://cmanco.mitraadipersada.com/api/planning-borong-driver/store');
							$ch = curl_init('http://192.168.1.221:8118/api/planning-borong-driver/store');

							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, [
								'Content-Type: application/json'
							]);

							curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

							$response = curl_exec($ch);

							if (curl_errno($ch)) {
								echo 'cURL Error: ' . curl_error($ch);
							} else {
								echo $response;
							}

							curl_close($ch);


							$sql = "UPDATE tr_jo set 
								status = '1', 
								tagihan = '$total'
								where id_jo = '$id_jo' ";
							$hasil=mysqli_query($koneksi, $sql);
						// ---------------- END API  ----------------
					}
				}

				echo json_encode([
					"success" => $success,
					"data"    => $apiResponse,
					"sent"    => $resultData
				], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		}


	else if ($_GET['type'] == "ListItemSQ"){
		$cari = $_GET['cari'];
		$id_quo 	= $_GET['id_quo'];
		$data = '<table class="table table-hover table-striped" style="width:100%">
				<thead style="font-weight:500px !important">
					<tr>
						<th width="7%" style="text-align: center;">NO</th>
						<th width="27%" style="text-align: center;">ORIGIN</th>
						<th width="27%" style="text-align: center;">DESTINATIN</th>
						<th width="27%" style="text-align: center;">TYPE</th>
						<th width="9%" style="text-align: center;">ADD</th>
					</tr>
				</thead>';	
		$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
		$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
		
		$SQL = "SELECT 
					tr_quo_data.*,
					m_asal.nama_kota AS asal,
					m_tujuan.nama_kota AS tujuan
				FROM tr_quo_data 
				LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_quo_data.id_asal
				LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_quo_data.id_tujuan
				WHERE tr_quo_data.id_quo = '$id_quo' 
				ORDER BY tr_quo_data.id_detil 
				LIMIT 0, 10";

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

				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['id_detil'].')" >'.$row['asal'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['id_detil'].')" >'.$row['tujuan'].'</a></td>';
				$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['id_detil'].')" >'.$row['jenis_mobil'].'</a></td>';

				$data .= '<td style="text-align:center">
						<button type="button" class="btn btn-default" onClick="javascript:PilihSQ('.$row['id_detil'].')" 
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

	else if ($_POST['type'] == "DetilDataItem"){
		$id = $_POST['id'];	

		$query = "SELECT 
					tr_quo_data.*,
					m_asal.nama_kota   AS asal,
					m_tujuan.nama_kota AS tujuan,
					m_rate_tr.id_rate,
					m_rate_tr.uj,
					m_rate_tr.ritase
				FROM tr_quo_data
				LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_quo_data.id_asal
				LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_quo_data.id_tujuan
				LEFT JOIN m_rate_tr ON m_rate_tr.id_asal = tr_quo_data.id_asal
					AND m_rate_tr.id_tujuan    = tr_quo_data.id_tujuan
					AND m_rate_tr.jenis_mobil  = tr_quo_data.jenis_mobil
				WHERE id_detil = '$id'
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

	else if ($_POST['type'] == "Add_DetailSO"){
		if($_POST['mode'] != '' ){	
			$id_so 			= $_POST['id_jo'];
			$mode 			= $_POST['mode'];

			$id_asal 		= strtoupper($_POST['id_asal']);
			$id_tujuan 		= strtoupper($_POST['id_tujuan']);

			$jenis_mobil	= strtoupper($_POST['jenis_mobil']);
			$harga 			= $_POST['harga'];
			$uj 			= $_POST['uj'];
			$ritase 		= $_POST['ritase'];

			$container 		= strtoupper($_POST['container']);
			$remark 		= strtoupper($_POST['remark']);
			if($mode == 'Add') {
				
				// ------------ INSERT PO DETAIL ------------
					$sql_insert = "INSERT INTO tr_jo_detail (
							id_so, id_asal, id_tujuan, jenis_mobil, harga, uj, ritase, container, remark
						) VALUES (
							'$id_so', '$id_asal', '$id_tujuan', '$jenis_mobil', '$harga', '$uj', '$ritase', '$container', '$remark'
						)";
					$hasil_insert = mysqli_query($koneksi, $sql_insert);
			}
			
			if (!$hasil_insert) {
				echo "Insert data error !";
			}
			else{	
				echo "Data saved!";
			}
		}		
	}

	// -------------- DETAIL DATA SO DETAIL--------------
		else if($_GET['type'] == "Read_Detil") {
			$id_jo = $_GET['id_jo'];
			
			$data = '<table class="table table-hover table-striped" style="width:100%">
					<thead style="font-weight:500px !important">
						<tr>	
							<th width="5%" style="text-align: center;">NO</th>					
							<th width="10%" style="text-align: center;">ORIGIN</th>
							<th width="10%" style="text-align: center;">DESTINATION</th>
							<th width="10%" style="text-align: center;">CONTAINER</th>
							<th width="10%" style="text-align: center;">TYPE</th>
							<th width="10%" style="text-align: center;">PRICE</th>
							<th width="10%" style="text-align: center;">TRAVEL EXPENSE</th>
							<th width="10%" style="text-align: center;">RITASE</th>			
						</tr>	
					</thead>';	
			$SQL = "SELECT 
						tr_jo_detail.*,
						m_asal.nama_kota AS asal,
						m_tujuan.nama_kota AS tujuan
					FROM tr_jo_detail 
					LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_jo_detail.id_asal
					LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_jo_detail.id_tujuan
					WHERE tr_jo_detail.id_so = '$id_jo'";

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
					$uj = number_format($row['uj'],0);	
					$ritase = number_format($row['ritase'],0);	
					$data .= '<tr>						
						<td style="text-align:center">'.$posisi.'.</td>
						
						<td style="text-align:center">'.$row['asal'].'</td>	
						<td style="text-align:center">'.$row['tujuan'].'</td>
						<td style="text-align:center">'.$row['container'].'</td>
						<td style="text-align:center">'.$row['jenis_mobil'].'</td>
						<td style="text-align:center">'.$harga.'</td>
						<td style="text-align:center">'.$uj.'</td>
						<td style="text-align:center">'.$ritase.'</td>
						';
						
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
		else if ($_POST['type'] == "checkPPH") {

			$id_jo   = mysqli_real_escape_string($koneksi, $_POST['id_jo']);
			$id_cost = mysqli_real_escape_string($koneksi, $_POST['id_cost']);

			$sql_jo = "SELECT 
					m_cust_tr.pph,
					m_cust_tr.nama_cust
				FROM tr_jo
				LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_jo.id_cust
				WHERE tr_jo.id_jo = '$id_jo'
				LIMIT 1";

			// echo $sql_jo;
			// return;

			$query_jo = mysqli_query($koneksi, $sql_jo);
			$row_jo   = mysqli_fetch_assoc($query_jo);

			$sql_cost = "SELECT m_cost_tr.item_pph
				FROM m_cost_tr
				WHERE id_cost = '$id_cost'
				LIMIT 1";

			// echo $sql_cost;
			// return;

			$query_cost = mysqli_query($koneksi, $sql_cost);
			$row_cost   = mysqli_fetch_assoc($query_cost);

			$pph        = strtolower($row_jo['pph'] ?? '');
			$item_pph   = strtolower($row_cost['item_pph'] ?? '');

			// echo $pph;
			// return;

			$pph_fix = ($pph > 0 && $item_pph === 'yes') ? $pph : 0;

			echo json_encode([
				"pph_fix" => $pph_fix
			]);
			exit;
		}


	// -------------- DETAIL DATA SQ --------------
		else if ($_GET['type'] == "ListSQ"){
			$cari = $_GET['cari'];
			$data = '<table class="table table-hover table-striped" style="width:100%">
					<thead style="font-weight:500px !important">
						<tr>
							<th width="5%" style="text-align: center;">NO</th>
							<th width="15%" style="text-align: center;">DATE</th>
							<th width="15%" style="text-align: center;">NO SQ</th>
							<th width="50%" style="text-align: center;">CUSTOMER</th>
							<th width="5%" style="text-align: center;">ADD</th>
						</tr>
					</thead>';	
			$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
			$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
			
			$SQL = "SELECT 
						tr_quo.id_quo,
						tr_quo.quo_date,
						tr_quo.quo_no,
						m_cust_tr.nama_cust
					FROM tr_quo 
					LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_quo.id_cust
					WHERE tr_quo.quo_no LIKE '%$cari%'
					ORDER BY tr_quo.quo_no DESC LIMIT 0, 10";

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

					$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['rowid'].')" >'.$row['quo_date'].'</a></td>';
					$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['rowid'].')" >'.$row['quo_no'].'</a></td>';
					$data .= '<td style="text-align:center"><a href="#" onclick="PilihSQ('.$row['rowid'].')" >'.$row['nama_cust'].'</a></td>';

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
			// echo "<pre>";
			// print_r($_POST);
			// echo "</pre>";
			// die();

			$id_quo = $_POST['id_quo'];	
			$query = "SELECT 
						tr_quo.id_quo, 
						tr_quo.quo_no, 
						tr_quo_data.jenis_mobil,
						tr_quo_data.harga,
						m_cust_tr.id_cust, 
						m_cust_tr.nama_cust,
						m_asal.id_kota AS id_asal,
						m_asal.nama_kota AS kota_asal,
						m_tujuan.id_kota AS id_tujuan,
						m_tujuan.nama_kota AS kota_tujuan
					FROM tr_quo 
					LEFT JOIN m_cust_tr 
						ON m_cust_tr.id_cust = tr_quo.id_cust 
					LEFT JOIN tr_quo_data 
						ON tr_quo_data.id_quo = tr_quo.id_quo 
					LEFT JOIN m_kota_tr AS m_asal 
						ON m_asal.id_kota = tr_quo_data.id_asal
					LEFT JOIN m_kota_tr AS m_tujuan 
						ON m_tujuan.id_kota = tr_quo_data.id_tujuan
					WHERE tr_quo.id_quo = '$id_quo'";

			// echo $query;
			// die();
			
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

	// -------------- DETAIL DATA POTR --------------
		else if ($_GET['type'] == "ListPOTR"){
			$cari = $_GET['cari'];
			$data = '<table class="table table-hover table-striped" style="width:100%">
					<thead style="font-weight:500px !important">
						<tr>
							<th width="5%" style="text-align: center;">NO</th>
							<th width="15%" style="text-align: center;">DATE</th>
							<th width="15%" style="text-align: center;">SAP PROJECT</th>
							<th width="15%" style="text-align: center;">NO PO TR</th>
							<th width="50%" style="text-align: center;">CUSTOMER</th>
							<th width="5%" style="text-align: center;">ADD</th>
						</tr>
					</thead>';	
			$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
			$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
			
			$q_po = "SELECT 
						tr_po.id_po, 
						tr_po.code_po, 
						tr_po.delivery_date, 
						sap_project.kode_project, 
						m_cust_tr.nama_cust 
					FROM tr_po 
					LEFT JOIN tr_po_detail ON tr_po_detail.code_po = tr_po.code_po
					LEFT JOIN sap_project ON sap_project.rowid = tr_po.sap_project  
					LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_po.user_req 
					WHERE tr_po.code_po LIKE '%$cari%' 
						AND tr_po.status = '1' 
						AND tr_po_detail.jenis = 'route'
					ORDER BY tr_po.id_quo DESC LIMIT 0, 10";

			$query = mysqli_query($koneksi, $q_po);	
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

					$data .= '<td style="text-align:center"><a href="#" onclick="PilihPOTR('.$row['id_po'].')" >'.$row['delivery_date'].'</a></td>';
					$data .= '<td style="text-align:center"><a href="#" onclick="PilihPOTR('.$row['id_po'].')" >'.$row['kode_project'].'</a></td>';
					$data .= '<td style="text-align:center"><a href="#" onclick="PilihPOTR('.$row['id_po'].')" >'.$row['code_po'].'</a></td>';
					$data .= '<td style="text-align:center"><a href="#" onclick="PilihPOTR('.$row['id_po'].')" >'.$row['nama_cust'].'</a></td>';

					$data .= '<td style="text-align:center">
							<button type="button" class="btn btn-default" onClick="javascript:PilihPOTR('.$row['id_po'].')" 
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
		else if ($_POST['type'] == "DetilPOTR"){

			// echo "<pre>";
			// print_r($_POST);
			// echo "</pre>";
			// die();

			$id_po = $_POST['id_po'];	
			$query = "SELECT 
						tr_po.id_po,
						tr_po.sap_project,
						tr_po.id_quo,
						tr_po.code_po,
						sap_project.kode_project,
						m_cust_tr.id_cust,
						m_cust_tr.nama_cust,
						m_asal.id_kota AS id_origin,
						m_tujuan.id_kota AS id_destination,
						tr_po_detail.uom AS jenis_mobil,
						tr_quo_data.harga,
						tr_quo.quo_no
					FROM tr_po
					LEFT JOIN sap_project ON sap_project.rowid = tr_po.sap_project
					LEFT JOIN tr_quo ON tr_quo.id_quo = tr_po.id_quo
					LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_quo.id_cust
					LEFT JOIN tr_po_detail ON tr_po_detail.code_po = tr_po.code_po
					LEFT JOIN m_kota_tr AS m_asal ON m_asal.id_kota = tr_po_detail.origin
					LEFT JOIN tr_quo_data ON tr_quo_data.id_quo = tr_po.id_quo
					LEFT JOIN m_kota_tr AS m_tujuan ON m_tujuan.id_kota = tr_po_detail.destination
					WHERE tr_po_detail.jenis = 'route'
					AND tr_po.id_po = '$id_po';
					";

			// echo $query;
			// die();
			
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
		else if ($_POST['type'] == "checkUJ") {

			$type_price  = $_POST['type_price'];	
			$id_asal     = $_POST['id_asal'];	
			$id_tujuan   = $_POST['id_tujuan'];	
			$jenis_mobil = $_POST['jenis_mobil'];	

			$query = "SELECT uj, ritase
					FROM m_rate_tr
					WHERE id_asal = '$id_asal'
						AND id_tujuan = '$id_tujuan'
						AND jenis_mobil LIKE '%$jenis_mobil'
						AND price_type = '$type_price'";

			$result = mysqli_query($koneksi, $query);

			$response = array();

			if ($result && mysqli_num_rows($result) > 0) {
				$response = mysqli_fetch_assoc($result);
			} else {
				$response['uj']      = 0;
				$response['ritase']  = 0;
				$response['status']  = 404;
				$response['message'] = "Data not found!";
			}

			echo json_encode($response);
		}

		else if ($_POST['type'] == "printAR") {
			// echo "<pre>";
			// print_r($_POST);
			// echo "</pre>";
			// die();

			$no_ar   = $_POST['no_ar'];    
			$q_update = "UPDATE tr_jo SET 
							print_count = print_count + 1,
							flag_ar = '0'
						WHERE no_ar = '$no_ar'";
			$d_update = mysqli_query($koneksi, $q_update);

			if ($d_update) {
				$xy1  = base64_encode($no_ar);
				$link = "cetak_ar.php?no_ar={$xy1}";
				
				echo json_encode([
					"status" => "success",
					"url"    => $link
				]);
			} else {
				echo json_encode([
					"status" => "error",
					"msg"    => "Gagal update print_count."
				]);
			}
		}

		else if ($_POST['type'] == "sendApprovalAR") {
			// echo "<pre>";
			// print_r($_POST);
			// echo "</pre>";
			// die();

			$no_ar 	= $_POST['no_ar'];
		
			$q_joAR = "SELECT 
					tr_jo.no_ar,
					tr_jo.tgl_jo
				FROM tr_jo 
				WHERE tr_jo.no_ar = '$no_ar'";
			$query_data = mysqli_query($koneksi, $q_joAR);		
			$data_ar = mysqli_fetch_assoc($query_data);

			$tgl_jo = $data_ar['tgl_jo'];

			$mail	= new PHPMailer(true);
			try {
				$mail->isSMTP();
				$mail->Host       = 'smtp.gmail.com';
				$mail->SMTPAuth   = true;
				$mail->Username   = 'itdivision.map@gmail.com';
				$mail->Password   = 'glpykeqqsaulnhxd'; 
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->Port       = 587;

				$mail->setFrom('itdivision.map@gmail.com', 'Approval AR PETJ');
				$mail->addAddress('director.petj@gmail.com');

				$mail->isHTML(true);
				$mail->Subject = "Approval Cetak AR Trucking" . $no_ar;

				$mail->Body = '
					<table cellspacing="0" cellpadding="4">
						<tr>
							<td><b>No AR</b></td>
							<td>: '. $no_ar . '</td>
						</tr>
						<tr>
							<td><b>Tanggal SO</b></td>
							<td>: ' . $tgl_jo . '</td>
						</tr>
						<tr>
							<td><b>Tujuan Approval</b></td>
							<td>: Approval Cetak AR</td>
						</tr>
						<tr>
							<td><b>Perusahaan</b></td>
							<td>: PETJ</td>
						</tr>
					</table>
					<br><br>
					<a href="http://127.0.0.1/trucking-local/ar_approve.php/' . $no_ar . '" 
						style="display:inline-block;
							padding:10px 16px;
							background-color:#28a745;
							color:#fff;
							text-decoration:none;
							border-radius:4px;
							font-weight:bold;">
						Approve Cetak AR
					</a>
					&nbsp;&nbsp;
					<a href="http://127.0.0.1/trucking-local/ar_reject.php/' . $no_ar . '" 
						style="display:inline-block;
							padding:10px 16px;
							background-color:#dc3545;
							color:#fff;
							text-decoration:none;
							border-radius:4px;
							font-weight:bold;">
						Reject Cetak AR
					</a>
				';

				if ($mail->send()) {
					echo json_encode([
						"status" => "success",
						"msg"    => "Email berhasil dikirim."
					]);
				} else {
					echo json_encode([
						"status" => "error",
						"msg"    => "Email gagal: " . $mail->ErrorInfo
					]);
				}


			} catch (Exception $e) {
				echo json_encode([
					"status" => "error",
					"msg"    => "Exception: {$mail->ErrorInfo}"
				]);
			}
		}

?>