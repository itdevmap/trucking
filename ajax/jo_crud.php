<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu = '3' ");
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
	$stat = $_GET['stat'];
	$field1 = $_GET['field1'];
	$cari1 = trim($_GET['cari1']);
	
	if($stat == 'Open')
	{
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
	}else if($field == 'No DO'){
		$f = 'tr_jo.no_do';		
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';		
	}else if($field == 'Asal'){
		$f = 'm_kota_tr.nama_kota';
	}else if($field == 'Tujuan'){
		$f = 'm_kota1.nama_kota';	
	}else if($field == 'No Cont'){
		$f = 'tr_jo.no_cont';	
	}else if($field == 'Supir'){
		$f = 'm_supir_tr.nama_supir';		
	}else if($field == 'No Polisi'){
		$f = 'm_mobil_tr.no_polisi';			
	}else{
		$f = 't_jo_tr.no_jo';
	}
	
	if($field1 == 'No Order')
	{
		$f1 = 'tr_jo.no_jo';	
	}else if($field1 == 'No Quo'){
		$f1 = 'tr_quo.quo_no';	
	}else if($field1 == 'No DO'){
		$f1 = 'tr_jo.no_do';		
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';		
	}else if($field1 == 'Asal'){
		$f1 = 'm_kota_tr.nama_kota';
	}else if($field1 == 'Tujuan'){
		$f1 = 'm_kota1.nama_kota';	
	}else if($field1 == 'No Cont'){
		$f1 = 'tr_jo.no_cont';	
	}else if($field1 == 'Supir'){
		$f1 = 'm_supir_tr.nama_supir';		
	}else if($field1 == 'No Polisi'){
		$f1 = 'm_mobil_tr.no_polisi';			
	}else{
		$f1 = 't_jo_tr.no_jo';
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="8%" style="text-align: center;">DATE<br>NO ORDER<br>NO QUO</th>	
					<th rowspan="2" width="12%" style="text-align: center;">PROJECT<br>CODE</th>
					<th rowspan="2" width="12%" style="text-align: center;">CUSTOMER<br>NO DO</th>
					<th rowspan="2" width="8%" style="text-align: center;">ORIGIN<br>DESTINATION</th>
					<th rowspan="2" width="7%" style="text-align: center;">NO. CONT<br>NO POLICE</th>
					<th rowspan="2" width="3%" style="text-align: center;">TYPE</th>
					<th rowspan="2" width="9%" style="text-align: center;">DRIVER</th>
					<th colspan="2" width="15%" style="text-align: center;">AR</th>
					<th colspan="3" width="16%" style="text-align: center;">AP</th>
					<th rowspan="2" width="5%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>
					<th colspan="3" width="6%" style="text-align: center;">ACTION</th>	
					<th colspan="2" width="4%" style="text-align: center;">PRINT</th>	
					<th rowspan="2" width="4%" style="text-align: center;">ATTC</th>	
				</tr>
				<tr>
					<th width="5%" style="text-align: center;">DELIVERY<br>COST</th>
					<th width="6%" style="text-align: center;">OTHER<br>COST</th>
					<th width="5%" style="text-align: center;">ROAD<br>FEE</th>
					<th width="5%" style="text-align: center;">RITASE</th>
					<th width="6%" style="text-align: center;">OTHER</th>
					<th width="2%" style="text-align: center;">EDIT</th>
					<th width="2%" style="text-align: center;">DEL</th>	
					<th width="2%" style="text-align: center;">EXEC</th>					
					<th width="2%" style="text-align: center;">SO</th>	
					<th width="2%" style="text-align: center;">SJ</th>					
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
	
	if($id_role == '2' || $id_role == '10')
	{
		$sales = $id_user;
	}
	
	if($stat == 'All')
	{
			$SQL = "select tr_jo.*, tr_quo.quo_no, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal, m_kota1.nama_kota as tujuan,
			m_mobil_tr.no_polisi, m_supir_tr.nama_supir
			from 
			tr_jo left join tr_quo_data on tr_jo.id_detil_quo = tr_quo_data.id_detil
			left join tr_quo on tr_quo_data.id_quo = tr_quo.id_quo
			left join m_kota_tr on tr_jo.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota1 on tr_jo.id_tujuan = m_kota1.id_kota
			left join m_cust_tr on tr_jo.id_cust = m_cust_tr.id_cust
			left join m_mobil_tr on tr_jo.id_mobil = m_mobil_tr.id_mobil
			left join m_supir_tr on tr_jo.id_supir = m_supir_tr.id_supir
			where tr_jo.tgl_jo between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' 
			  order by tr_jo.tgl_jo desc, tr_jo.no_jo desc
			  LIMIT $offset, $jmlperhalaman";
			  
	}else{
			$SQL = "select tr_jo.*, tr_quo.quo_no, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal, m_kota1.nama_kota as tujuan,
			m_mobil_tr.no_polisi, m_supir_tr.nama_supir
			from 
			tr_jo left join tr_quo_data on tr_jo.id_detil_quo = tr_quo_data.id_detil
			left join tr_quo on tr_quo_data.id_quo = tr_quo.id_quo
			left join m_kota_tr on tr_jo.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota1 on tr_jo.id_tujuan = m_kota1.id_kota
			left join m_cust_tr on tr_jo.id_cust = m_cust_tr.id_cust
			left join m_mobil_tr on tr_jo.id_mobil = m_mobil_tr.id_mobil
			left join m_supir_tr on tr_jo.id_supir = m_supir_tr.id_supir
			where tr_jo.tgl_jo between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and tr_jo.status = '$stat'
			  order by tr_jo.tgl_jo desc, tr_jo.no_jo desc
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
			$biaya_kirim = number_format($row['biaya_kirim'],0);
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
			if($row['id_detil_quo'] == 0)
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
			
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:left">'.$tanggal.'<br>'.$row['no_jo'].'<br>'.$no_quo.'</td>
				<td style="text-align:left">'.$row['project_code'].'</td>
				<td style="text-align:left">'.$row['nama_cust'].'<br>'.$row['no_do'].'</td>				
				<td style="text-align:center">'.$row['asal'].'<br>'.$row['tujuan'].'</td>
				<td style="text-align:center">'.$row['no_cont'].'<br>'.$row['no_polisi'].'</td>
				<td style="text-align:center">'.$row['jenis_mobil'].'</td>
				<td style="text-align:center">'.$row['nama_supir'].'</td>
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
				
				// $data .= '<td style="text-align:right">
				// 	<button class="btn btn-block btn-default"  
				// 		style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button" 
				// 		onClick="javascript:GetPPN('.$row['id_jo'].')" '.$dis.' >
				// 		'.$row['ppn'].'
				// 	</button>
				// 	</td>
				// 	<td style="text-align:right">
				// 	<button class="btn btn-block btn-default"  
				// 		style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button" 
				// 		onClick="javascript:GetPPN('.$row['id_jo'].')"  '.$dis.' >
				// 		'.$row['pph'].'
				// 	</button>
				// 	</td>';
					
					
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
				
				$data .= '<td style="text-align:center">'.$row['created'].'</td>
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
						
				}
				else
				{
					$data .='<td></td>';
				}
				
				if($id_role != '2')
				{
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
				$link = "'cetak_sj.php?id=$xy1'";
				
				$data .= '<td>
						<button class="btn btn-block btn-default"  title="Print"
							style="margin:-3px;border-radius:0px" type="button" 									
							onClick="window.open('.$link.') ">
							<span class="fa fa-print"></span>
						</button></td>';	
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
				
				
			if($stat == 'All')
			{
					$pq = mysqli_query($koneksi, "select count(tr_jo.id_jo) as jml
					from 
					tr_jo left join tr_quo_data on tr_jo.id_detil_quo = tr_quo_data.id_detil
					left join tr_quo on tr_quo_data.id_quo = tr_quo.id_quo
					left join m_kota_tr on tr_jo.id_asal = m_kota_tr.id_kota
					left join m_kota_tr as m_kota1 on tr_jo.id_tujuan = m_kota1.id_kota
					left join m_cust_tr on tr_jo.id_cust = m_cust_tr.id_cust
					left join m_mobil_tr on tr_jo.id_mobil = m_mobil_tr.id_mobil
					left join m_supir_tr on tr_jo.id_supir = m_supir_tr.id_supir
					where tr_jo.tgl_jo between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' ");
					  
			}else{
					$pq = mysqli_query($koneksi, "select count(tr_jo.id_jo) as jml
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

} else if ($_POST['type'] == "Executed"){		
	if($_POST['id'] != '' )
	{	
		$id = $_POST['id'];
		$pq = mysqli_query($koneksi, "select * from tr_jo where id_jo  = '$id'");
		$rq=mysqli_fetch_array($pq);	

		$harga = $rq['biaya_kirim'];
		$ppn = $rq['ppn'];
		$pph = $rq['pph'];
		$total = $total + $harga;
		
		$t1 = "select tr_jo_biaya.*, m_cost_tr.nama_cost from
			tr_jo_biaya left join m_cost_tr on tr_jo_biaya.id_cost = m_cost_tr.id_cost
			where tr_jo_biaya.id_jo = '$id' order by tr_jo_biaya.id_biaya ";

		$h1 = mysqli_query($koneksi, $t1); 
		while ($d1=mysqli_fetch_array($h1))
		{
			$total = $total + $d1['harga'];
		}
		$nilai_ppn = ($ppn/100) * $total;
		$nilai_pph = ($pph/100) * $total;
		$total = $total + $nilai_ppn - $nilai_pph;
		
		$sql = "update tr_jo set 
				status = '1', tagihan = '$total'
				where id_jo = '$id'	";
		$hasil=mysqli_query($koneksi, $sql);


		// ---------------- KIRIM KE API CMANCO ----------------
		$id_supir = $rq['id_supir'];
		$supir_sql = "select nama_supir
			from m_supir_tr
			where id_supir = '$id_supir'";
		$supir 	= mysqli_query($koneksi, $supir_sql); 
		$spr 	= mysqli_fetch_array($supir);	

		$id_cust = $rq['id_cust'];
		$cust_sql = "select nama_cust
			from m_cust_tr
			where id_cust = '$id_cust'";
		$cust 	= mysqli_query($koneksi, $cust_sql); 
		$cst 	= mysqli_fetch_array($cust);

		$id_mobil = $rq['id_mobil'];
		$mobil_sql = "select no_polisi
			from m_mobil_tr
			where id_mobil = '$id_mobil'";
		$mobil 	= mysqli_query($koneksi, $mobil_sql); 
		$no_pol 	= mysqli_fetch_array($mobil);	

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

		// echo "<pre>";
		// echo "Foto SO         : " . ($foto_so ?? '-') . "\n";
		// echo "Surat Jalan     : " . ($surat_jalan ?? '-') . "\n";
		// echo "Mutasi Rekening : " . ($mutasi_rekening ?? '-') . "\n";
		// echo "</pre>";
		// die();

		$data = [
			'project'     		=> $rq['project_code'],
			'so'          		=> $rq['no_jo'],
			'driver'      		=> $spr['nama_supir'],
			'customer'    		=> $cst['nama_cust'],
			'tgl_order'   		=> $rq['tgl_jo'],
			'penerima'    		=> $rq['penerima'],
			'kontainer'   		=> $rq['no_cont'],
			'total'       		=> $total,
			'ritase'      		=> $rq['ritase'],
			'keterangan'  		=> $rq['ket'],
			'company'     		=> '7000',
			'site'        		=> '9',
			'nopol'       		=> $no_pol['no_polisi'],
			'foto_so'     		=> $foto_so,
			'surat_jalan'     	=> $surat_jalan,
			'mutasi_rekening'	=> $mutasi_rekening,
		];

		$sendApi = [
			'trucking' => $data
		];

		// Encode ke JSON
		$payload = json_encode($sendApi);
		// die($payload);

		// Inisialisasi cURL
		// $ch = curl_init('http://127.0.0.1:8000/api/planning-borong-driver/store');
		$ch = curl_init('http://192.168.1.221:8118/api/planning-borong-driver/store');

		// Set opsi cURL
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($payload)
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			echo 'cURL Error: ' . curl_error($ch);
		} else {
			echo "Response dari API:\n";
			echo $response;
		}

		curl_close($ch);

		if (!$hasil) {	
			exit(mysqli_error($koneksi));
			echo "Data error...!";
	    }
		else
		{	
			echo "Data Executed!";
		}
	}	
	
	
}else if ($_POST['type'] == "Add_Order"){		
	if($_POST['id_cust'] != '' )
	{
		die();
		$id_cust = $_POST['id_cust'];
		$id_detil_bc = $_POST['id_detil_bc'];
		$id_cont = $_POST['id_cont'];
		$jenis_po = $_POST['jenis_po'];
		$tanggal = $_POST['tanggal'];
		$no_do = addslashes(trim(strtoupper($_POST['no_do'])));
		$penerima = addslashes(trim($_POST['penerima']));
		$barang = addslashes(trim(strtoupper($_POST['barang'])));
		$berat = $_POST['berat'];
		$vol = $_POST['vol'];
		$no_cont = trim(addslashes(strtoupper($_POST['no_cont'])));
		$no_seal = trim(addslashes(strtoupper($_POST['no_seal'])));
		$id_asal = $_POST['id_asal'];
		$id_tujuan = $_POST['id_tujuan'];
		$jenis = $_POST['jenis'];	
		$id_mobil = $_POST['id_mobil'];
		$id_supir = $_POST['id_supir'];		
		$biaya = $_POST['biaya'];
		$uj = $_POST['uj'];
		$ritase = $_POST['ritase'];
		$ket = trim(addslashes($_POST['ket']));
		$biaya = str_replace(",","", $biaya);
		$uj = str_replace(",","", $uj);
		$ritase = str_replace(",","", $ritase);
		$berat = str_replace(",","", $berat);
		$vol = str_replace(",","", $vol);
		$tanggalx = ConverTglSql($tanggal);
		
		
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

		// PEMBUATAN PROJECT CODE
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

		$sql = "INSERT INTO  tr_jo (project_code, id_cust, id_detil_quo, no_jo, tgl_jo, no_do, penerima, barang, berat, vol, no_cont, no_seal,
				id_asal, id_tujuan, jenis_mobil, id_mobil, id_supir, biaya_kirim, uj, ritase, ket, created, jenis_po, id_detil_bc) values
					('$project_code','$id_cust', '0', '$no_sj', '$tanggalx', '$no_do', '$penerima', '$barang', '$berat', '$vol', '$no_cont', '$no_seal',
				'$id_asal', '$id_tujuan', '$jenis', '$id_mobil', '$id_supir', '$biaya', '$uj', '$ritase', '$ket', '$id_user' , '$jenis_po', '$id_detil_bc')";
		$hasil= mysqli_query($koneksi, $sql);
		
		$sql = mysqli_query($koneksi, "select max(id_jo)as id from tr_jo ");			
		$row = mysqli_fetch_array($sql);
		$id_jo = $row['id'];	
		
		if (!$hasil) {
			echo "Data DO/PO telah terdaftar...!";
	    }
		else
		{	
			$sql = "UPDATE t_jo_cont set id_jo_ptj = '$id_jo' where id_cont = '$id_cont' ";	
			$hasil = mysqli_query($koneksi, $sql);
			echo "Data saved!";
		}
	}		
	
}else if ($_POST['type'] == "Update_Order"){		
	if($_POST['mode'] != '' )
	{	
		$mode = $_POST['mode'];
		$id_jo = $_POST['id_jo'];
		$tanggal = $_POST['tanggal'];
		$no_do = addslashes(trim(strtoupper($_POST['no_do'])));
		$penerima = addslashes(trim($_POST['penerima']));
		$barang = addslashes(trim(strtoupper($_POST['barang'])));
		$berat = $_POST['berat'];
		$vol = $_POST['vol'];
		$no_cont = trim(addslashes(strtoupper($_POST['no_cont'])));
		$no_seal = trim(addslashes(strtoupper($_POST['no_seal'])));
		$id_asal = $_POST['id_asal'];
		$id_tujuan = $_POST['id_tujuan'];
		$jenis_mobil = $_POST['jenis_mobil'];	
		$id_mobil = $_POST['id_mobil'];
		$id_supir = $_POST['id_supir'];		
		$biaya = $_POST['biaya'];
		$uj = $_POST['uj'];
		$ritase = $_POST['ritase'];
		$ket = trim(addslashes($_POST['ket']));
		$biaya = str_replace(",","", $biaya);
		$uj = str_replace(",","", $uj);
		$ritase = str_replace(",","", $ritase);
		$berat = str_replace(",","", $berat);
		$vol = str_replace(",","", $vol);
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
			
			$sql = "INSERT INTO  tr_jo (id_detil_quo, no_jo, tgl_jo, no_do, penerima, barang, berat, vol, no_cont, no_seal,
					id_asal, id_tujuan, jenis_mobil, id_mobil, id_supir, biaya_kirim, uj, ritase, ket, created) values
						('$id_detil', '$no_sj', '$tanggalx', '$no_do', '$penerima', '$barang', '$berat', '$vol', '$no_cont', '$no_seal',
					'$id_asal', '$id_tujuan', '$jenis_mobil', '$id_mobil', '$id_supir', '$biaya', '$uj', '$ritase', '$ket', '$id_user')";
			$hasil= mysqli_query($koneksi, $sql);
		}else{
			$sql = "update tr_jo set 
						tgl_jo = '$tanggalx', 
						no_do = '$no_do',
						penerima = '$penerima',
						barang = '$barang',
						berat = '$berat',
						vol = '$vol',
						no_cont = '$no_cont',
						no_seal = '$no_seal',
						id_mobil = '$id_mobil',
						id_supir = '$id_supir',
						ket = '$ket'
						where id_jo = '$id_jo' 	";
			$hasil= mysqli_query($koneksi, $sql);
			
		}
		
			
		if (!$hasil) {
	       
			echo "Data Error...!";
	    }
		else
		{	
			
			echo "Data saved!";
		}
	}		

}else if ($_POST['type'] == "Update_PPN"){		
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

	
}else if ($_POST['type'] == "Del_Order"){
	$id = $_POST['id']; 	
	
	$sql = "UPDATE t_jo_cont set id_jo_ptj = '0' where id_jo_ptj = '$id' ";	
	$hasil = mysqli_query($koneksi, $sql);
			
			
	$del = mysqli_query($koneksi, "delete from tr_jo_uj where id_jo = '$id' ");
	$del = mysqli_query($koneksi, "delete from tr_jo_biaya where id_jo = '$id' ");
    $query = "DELETE FROM  tr_jo WHERE id_jo = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }	

}else if ($_POST['type'] == "Detil_Data"){
	$id = $_POST['id'];	
    $query = "select tr_jo.*, tr_quo.quo_no, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal, m_kota1.nama_kota as tujuan,
			m_mobil_tr.no_polisi, m_supir_tr.nama_supir
			from 
			tr_jo left join tr_quo_data on tr_jo.id_detil_quo = tr_quo_data.id_detil
			left join tr_quo on tr_quo_data.id_quo = tr_quo.id_quo
			left join m_kota_tr on tr_jo.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota1 on tr_jo.id_tujuan = m_kota1.id_kota
			left join m_cust_tr on tr_quo.id_cust = m_cust_tr.id_cust
			left join m_mobil_tr on tr_jo.id_mobil = m_mobil_tr.id_mobil
			left join m_supir_tr on tr_jo.id_supir = m_supir_tr.id_supir
				where tr_jo.id_jo  = '$id'";
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


}else if($_GET['type'] == "List_Biaya_Lain")
{
	$id = $_GET['id'];
	$stat = $_GET['stat'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="6%" style="text-align: center;">NO</th>
					<th rowspan="2" width="59%" style="text-align: center;">INFORMATION</th>
					<th rowspan="2" width="15%" style="text-align: center;">COST</th>
					<th rowspan="2" width="15%" style="text-align: center;">PPN</th>
					<th rowspan="2" width="15%" style="text-align: center;">WTAX</th>
					<th rowspan="2" width="5%" style="text-align: center;">EDIT</th>		
					<th rowspan="2" width="5%" style="text-align: center;">DEL</th>		
				</tr>
			</thead>';
	$t1="select  tr_jo_biaya.*, m_cost_tr.nama_cost from
		tr_jo_biaya left join m_cost_tr on tr_jo_biaya.id_cost = m_cost_tr.id_cost
			where tr_jo_biaya.id_jo = '$id' order by tr_jo_biaya.id_biaya";
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
			<td style="text-align:left">'.$d1['nama_cost'].'</td>	
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
	
	$sql = "update tr_jo set biaya_kirim_lain = '$total' where id_jo = '$id'	";
	$hasil=mysqli_query($koneksi,$sql);
			
    echo $data;	

}else if ($_POST['type'] == "Add_Biaya_Lain"){		
	if($_POST['mode'] != '' )
	{	
		$id_jo = $_POST['id_jo'];
		$mode = $_POST['mode'];
		$id = $_POST['id'];
		$id_cost = $_POST['id_cost'];
		$biaya = $_POST['biaya'];
		$pph = $_POST['pph'];
		$wtax = $_POST['wtax'];
		$biaya = str_replace(",","", $biaya);
		
		if($mode == 'Add')
		{
			$sql = "INSERT INTO  tr_jo_biaya (id_jo, id_cost, harga, pph, wtax) values
					('$id_jo', '$id_cost', '$biaya', '$pph', '$wtax')";
			$hasil= mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update tr_jo_biaya set 
					id_cost = '$id_cost',
					harga = '$biaya',
					pph = '$pph',
					wtax = '$wtax'
					where id_biaya = '$id'	";
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


}else if ($_POST['type'] == "Detil_Biaya_Lain"){
	$id = $_POST['id'];	
    $query = "select * from  tr_jo_biaya where id_biaya  = '$id'";
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

}else if ($_POST['type'] == "Del_Biaya_Lain"){
	$id = $_POST['id']; 
    $query = "DELETE FROM tr_jo_biaya WHERE id_biaya = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }	
	
}else if($_GET['type'] == "List_UJ")
{
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
	
	
}else if ($_POST['type'] == "Add_UJ"){		
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


}else if ($_POST['type'] == "Detil_UJ"){
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

}else if ($_POST['type'] == "Del_UJ"){
	$id = $_POST['id']; 
    $query = "DELETE FROM tr_jo_uj WHERE id_uj = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }	
	
	
}else if ($_GET['type'] == "ListPO")
{	
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
	
	$SQL = "select t_jo_bc_cont.*, t_jo_cont.no_cont, t_jo_cont.feet, t_jo_tagihan.no_tagihan, m_kota_tr.nama_kota 
			from 
			t_jo_bc_cont left join t_jo_cont on t_jo_bc_cont.id_cont = t_jo_cont.id_cont
			left join t_jo_bc on t_jo_bc_cont.id_bc = t_jo_bc.id_bc
			left join t_jo_tagihan on t_jo_bc.id_jo_tagihan = t_jo_tagihan.id_tagihan
			left join m_kota_tr on t_jo_bc.id_kota = m_kota_tr.id_kota where t_jo_cont.id_jo_ptj <= '0' and t_jo_bc.id_jo_tagihan >= 0
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
	
}else if ($_POST['type'] == "DetilPO"){
	$id = $_POST['id'];	
    $query = "select t_jo_bc_cont.*, t_jo_bc.alamat_ambil, t_jo_bc.id_kota, t_jo_bc.id_asal, t_jo_cont.ket, t_jo_cont.berat, t_jo_cont.vol,
			t_jo_cont.no_cont, t_jo_cont.feet, t_jo_tagihan.no_tagihan, m_kota_tr.nama_kota, m_cust.nama_cust 
			from 
			t_jo_bc_cont left join t_jo_cont on t_jo_bc_cont.id_cont = t_jo_cont.id_cont
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

?>