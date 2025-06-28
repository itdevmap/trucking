<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu = '7' ");
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

	if($field == 'No Order')
	{
		$f = 't_jo_tr.no_jo';	
	}else if($field == 'No PO'){
		$f = 't_jo_tr.no_po';	
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';	
	}else if($field == 'Asal'){
		$f = 'm_kota_tr.nama_kota';
	}else if($field == 'Tujuan'){
		$f = 'm_kota1.nama_kota';	
	}else if($field == 'Jenis'){
		$f = 't_jo_detil_tr.jenis_mobil';	
	}else if($field == 'No SJ'){
		$f = 't_jo_sj_tr.no_sj';	
	}else if($field == 'No Container'){
		$f = 't_jo_detil_tr.no_cont';			
	}else{
		$f = 't_jo_tr.no_jo';
	}
	
	if($field1 == 'No Order')
	{
		$f1 = 't_jo_tr.no_jo';	
	}else if($field1 == 'No PO'){
		$f1 = 't_jo_tr.no_po';	
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';	
	}else if($field1 == 'Asal'){
		$f1 = 'm_kota_tr.nama_kota';
	}else if($field1 == 'Tujuan'){
		$f1 = 'm_kota1.nama_kota';	
	}else if($field1 == 'Jenis'){
		$f1 = 't_jo_detil_tr.jenis_mobil';	
	}else if($field1 == 'No SJ'){
		$f1 = 't_jo_sj_tr.no_sj';		
	}else if($field1 == 'No Container'){
		$f1 = 't_jo_detil_tr.no_cont';	
	}else{
		$f1 = 't_jo_tr.no_jo';
	}
	
	if($stat == 'Open')
	{
		$stat = '0';
	}
	else if($stat == 'Close')
	{
		$stat = '1';
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL</th>	
					<th rowspan="2" width="6%" style="text-align: center;">#NO<br>ORDER</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. PO</th>
					<th rowspan="2" width="17%" style="text-align: center;">CUSTOMER</th>					
					<th rowspan="2" width="8%" style="text-align: center;">ASAL</th>
					<th rowspan="2" width="8%" style="text-align: center;">TUJUAN</th>
					<th rowspan="2" width="7%" style="text-align: center;">JENIS</th>
					<th rowspan="2" width="7%" style="text-align: center;">NO. CONTAINER</th>
					<th rowspan="2" width="5%" style="text-align: center;">BIAYA<br>KIRIM</th>
					<th rowspan="2" width="5%" style="text-align: center;">BIAYA<br> LAINNYA</th>
					<th rowspan="2" width="6%" style="text-align: center;">NO. SJ</th>
					<th rowspan="2" width="5%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="4%" style="text-align: center;">STATUS</th>
					<th colspan="4" width="4%" style="text-align: center;">ACTION</th>	
				</tr>
				<tr>
					<th width="2%" style="text-align: center;">EDIT</th>
					<th width="2%" style="text-align: center;">DEL</th>		
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
		$SQL = "select t_jo_tr.*, t_jo_detil_tr.status as status_sj, t_jo_detil_tr.id_detil, t_jo_detil_tr.jenis_mobil, t_jo_detil_tr.biaya_kirim,
			t_jo_detil_tr.no_cont, t_jo_detil_tr.biaya_lain, t_jo_detil_tr.id_bc,
			m_kota_tr.nama_kota as asal,	m_kota1.nama_kota as tujuan, m_cust_tr.nama_cust, t_jo_sj_tr.no_sj, t_jo_sj_tr.id_sj
			from 
			t_jo_tr left join m_cust_tr on t_jo_tr.id_cust = m_cust_tr.id_cust
			left join t_jo_detil_tr on t_jo_tr.id_jo = t_jo_detil_tr.id_jo
			left join m_kota_tr on t_jo_detil_tr.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota1 on t_jo_detil_tr.id_tujuan = m_kota1.id_kota
			left join t_jo_sj_tr on t_jo_detil_tr.id_sj = t_jo_sj_tr.id_sj
			where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_jo_tr.tipe = 'FCL'	
			order by t_jo_tr.tgl_jo desc, t_jo_tr.no_jo desc
			LIMIT $offset, $jmlperhalaman";
	}else{
		
		$SQL = "select t_jo_tr.*, t_jo_detil_tr.status as status_sj, t_jo_detil_tr.id_detil, t_jo_detil_tr.jenis_mobil, t_jo_detil_tr.biaya_kirim,
			t_jo_detil_tr.no_cont, t_jo_detil_tr.biaya_lain, t_jo_detil_tr.id_bc,
			m_kota_tr.nama_kota as asal,	m_kota1.nama_kota as tujuan, m_cust_tr.nama_cust, t_jo_sj_tr.no_sj, t_jo_sj_tr.id_sj
			from 
			t_jo_tr left join m_cust_tr on t_jo_tr.id_cust = m_cust_tr.id_cust
			left join t_jo_detil_tr on t_jo_tr.id_jo = t_jo_detil_tr.id_jo
			left join m_kota_tr on t_jo_detil_tr.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota1 on t_jo_detil_tr.id_tujuan = m_kota1.id_kota
			left join t_jo_sj_tr on t_jo_detil_tr.id_sj = t_jo_sj_tr.id_sj
			where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_jo_tr.tipe = 'FCL'	
			and t_jo_detil_tr.status = '$stat' order by t_jo_tr.tgl_jo desc, t_jo_tr.no_jo desc
			LIMIT $offset, $jmlperhalaman";	
	}
	
			  
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$tanggal = ConverTgl($row['tgl_jo']);
			$biaya = number_format($row['biaya_kirim'],0);
			$lain = number_format($row['biaya_lain'],0);
			$posisi++;
			$xy1="View|$row[id_jo]";
			$xy1=base64_encode($xy1);
			$link = "fcl_data.php?id=$xy1";
			if($row['status_sj'] == '0')
			{
				$label = 'danger';
				$status = 'Open';
			}
			else if($row['status_sj'] == '1')
			{
				$label = 'success';
				$status = 'Close';
			}
			$xy1="$row[id_sj]";
			$xy1=base64_encode($xy1);
			$link_sj = "cetak_sj_fcl.php?id=$xy1";
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>
				<td style="text-align:center"><a href="'.$link.'"  title="View">'.$row['no_jo'].'</a></td>
				<td style="text-align:center">'.$row['no_po'].'</b></td>				
				<td style="text-align:left">'.$row['nama_cust'].'</b></td>	
				<td style="text-align:center">'.$row['asal'].'</td>
				<td style="text-align:center">'.$row['tujuan'].'</td>
				<td style="text-align:center">'.$row['jenis_mobil'].'</td>
				<td style="text-align:center">'.$row['no_cont'].'</td>
				<td style="text-align:right">'.$biaya.'</td>
				<td style="text-align:right">'.$lain.'</td>
				<td style="text-align:center"><a href="'.$link_sj.'"  target = "_blank">'.$row['no_sj'].'</a></td>
				<td style="text-align:center">'.$row['created'].'</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';
			
				if($m_edit == '1' && $row['status'] == '0' ) {
					$xy1="Edit|$row[id_jo]";
					$xy1=base64_encode($xy1);
					$link = "'fcl_data.php?id=$xy1'";
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="window.location.href = '.$link.' "  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}
				else
				{					
						$data .='<td></td>';
				}
				
				if($m_del == '1' && $row['no_sj'] == '' && $row['id_bc'] == '0') 	
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
				/*
				if($m_add == '1' && $row['no_sj'] == '' && !empty($row['id_detil']) ) {
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:TampilData('.$row['id_detil'].')"  >
									<span class="fa  fa-plus-square" ></span>
								</button></td>';
				}
				else
				{					
						$data .='<td></td>';
				}
				*/
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
					$pq = mysqli_query($koneksi, "select count(t_jo_detil_tr.id_detil) as jml
					from 
					t_jo_tr left join m_cust_tr on t_jo_tr.id_cust = m_cust_tr.id_cust
					left join t_jo_detil_tr on t_jo_tr.id_jo = t_jo_detil_tr.id_jo
					left join m_kota_tr on t_jo_detil_tr.id_asal = m_kota_tr.id_kota
					left join m_kota_tr as m_kota1 on t_jo_detil_tr.id_tujuan = m_kota1.id_kota
					left join t_jo_sj_tr on t_jo_detil_tr.id_sj = t_jo_sj_tr.id_sj
					where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_jo_tr.tipe = 'FCL'	 ");
				}else{
					$pq = mysqli_query($koneksi, "select count(t_jo_detil_tr.id_detil) as jml
					from 
					t_jo_tr left join m_cust_tr on t_jo_tr.id_cust = m_cust_tr.id_cust
					left join t_jo_detil_tr on t_jo_tr.id_jo = t_jo_detil_tr.id_jo
					left join m_kota_tr on t_jo_detil_tr.id_asal = m_kota_tr.id_kota
					left join m_kota_tr as m_kota1 on t_jo_detil_tr.id_tujuan = m_kota1.id_kota
					left join t_jo_sj_tr on t_jo_detil_tr.id_sj = t_jo_sj_tr.id_sj
					where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_jo_tr.tipe = 'FCL'	
					and t_jo_detil_tr.status = '$stat' ");
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

}else if ($_POST['type'] == "Del_Order"){
	$id = $_POST['id']; 	
	
    $query = "DELETE FROM t_jo_tr WHERE id_jo = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	

}else if($_GET['type'] == "Read_Detil")
{
	$id_jo = $_GET['id_jo'];
	$mode = $_GET['mode'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>					
					<th rowspan="2" width="10%" style="text-align: center;">ASAL</th>
					<th rowspan="2" width="10%" style="text-align: center;">TUJUAN</th>
					<th rowspan="2" width="10%" style="text-align: center;">JENIS</th>
					<th rowspan="2" width="10%" style="text-align: center;">NO. CONTAINER</th>
					<th rowspan="2" width="32%" style="text-align: center;">PENERIMA</th>
					<th rowspan="2" width="7%" style="text-align: center;">BIAYA KIRIM</th>
					<th rowspan="2" width="7%" style="text-align: center;">BIAYA LAINNYA</th>
					<th rowspan="2" width="7%" style="text-align: center;">TOTAL</th>
					<th colspan="2" width="4%" style="text-align: center;">ACTION</th>						
				</tr>
				<tr>
					<th width="2%" style="text-align: center;">EDIT</th>
					<th width="2%" style="text-align: center;">DEL</th>
				</tr>	
			</thead>';	
	$SQL = "select t_jo_detil_tr.*, m_kota_tr.nama_kota as asal, m_kota1.nama_kota as tujuan, t_jo_sj_tr.no_sj
		  from 
		  t_jo_detil_tr left join m_kota_tr on t_jo_detil_tr.id_asal = m_kota_tr.id_kota
		  left join m_kota_tr as m_kota1 on t_jo_detil_tr.id_tujuan = m_kota1.id_kota
		  left join t_jo_sj_tr on t_jo_detil_tr.id_sj = t_jo_sj_tr.id_sj
		  where t_jo_detil_tr.id_jo = '$id_jo' order by  t_jo_detil_tr.id_detil";
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
		$idr = 0;
		$usd =0;
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			
			$biaya_kirim = number_format($row['biaya_kirim'],0);	
			$biaya_lain = number_format($row['biaya_lain'],0);			
			$jumlah = $row['biaya_kirim'] + $row['biaya_lain'];
			$jumlahx = number_format($jumlah,0);
			$total = $total + $jumlah;
			$data .= '<tr>						
				<td style="text-align:center">'.$posisi.'.</td>
				
				<td style="text-align:center">'.$row['asal'].'</td>	
				<td style="text-align:center">'.$row['tujuan'].'</td>
				<td style="text-align:center">'.$row['jenis_mobil'].'</td>
				<td style="text-align:center">'.$row['no_cont'].'</td>
				<td style="text-align:left">'.$row['penerima'].'</td>
				<td style="text-align:right">'.$biaya_kirim.'</td>
				<td style="text-align:right">
					<button class="btn btn-block btn-default"  
						style="padding:1px;border-radius:0px;width:100%;text-align:right" type="button" 
						onClick="javascript:List_Lain('.$row['id_detil'].')"  >
						'.$biaya_lain.'
					</button>
				</td>
				<td style="text-align:right">'.$jumlahx.'</td>';
				
				if($mode == 'Edit' && $row['status'] == '0' ){
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
		
		$totalx = number_format($total,0);
			$data .= '<td colspan="7"></td>';
			$data .= '<td colspan="2" style="text-align:right;background:#eee;color:#000"><b>Total  :</b></td>	
						<td style="text-align:right;background:#008d4c;color:#fff"><b>'.$totalx.'</b></td>';
			$data .= '<td colspan="2"></td>';			
			$data .= '</tr>';
		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
	
	
    $data .= '</table>';
	
    echo $data;	
	
}else if ($_POST['type'] == "Del_Detil"){
	$id = $_POST['id']; 
	
	$del = mysqli_query($koneksi, "delete from t_jo_detil_biaya_tr  where id_detil = '$id'	");
	$del = mysqli_query($koneksi, "update t_jo_cont set id_sj_tr = '0'  where id_sj_tr = '$id'	");
    $query = "DELETE FROM t_jo_detil_tr WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	
	
}else if ($_POST['type'] == "Detil_Data"){
	$id = $_POST['id'];	
    $query = "select t_jo_detil_tr.*, t_jo_tr.no_jo from 
			t_jo_detil_tr left join t_jo_tr on t_jo_detil_tr.id_jo = t_jo_tr.id_jo
			where id_detil  = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
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
	
}else if ($_POST['type'] == "Add_Detil"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$id_jo_cont = $_POST['id_jo_cont'];
		$mode = $_POST['mode'];
		$id_jo = $_POST['id_jo'];
		$id_asal = $_POST['id_asal'];
		$id_tujuan = $_POST['id_tujuan'];
		$jenis = $_POST['jenis'];
		$biaya_kirim = $_POST['biaya_kirim'];
		$biaya_kirim = str_replace(",","", $biaya_kirim);
		$penerima = addslashes(trim(strtoupper($_POST['penerima'])));
		$no_cont = addslashes(trim(strtoupper($_POST['no_cont'])));
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO t_jo_detil_tr (id_jo, id_asal, id_tujuan, jenis_mobil, penerima, biaya_kirim, no_cont) values
					('$id_jo', '$id_asal', '$id_tujuan', '$jenis', '$penerima', '$biaya_kirim', '$no_cont')";
			$hasil= mysqli_query($koneksi, $sql);
			
			
			
		}
		else
		{
			$sql = "update t_jo_detil_tr set 
					id_asal = '$id_asal',
					id_tujuan = '$id_tujuan',
					jenis_mobil = '$jenis',
					biaya_kirim = '$biaya_kirim',
					penerima = '$penerima'
					where id_detil = '$id'	";
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
		

}else if($_GET['type'] == "List_Lain")
{
	$id = $_GET['id'];
	$mode = $_GET['mode'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="6%" style="text-align: center;">NO</th>
					<th rowspan="2" width="59%" style="text-align: center;">KETERANGAN</th>
					<th rowspan="2" width="15%" style="text-align: center;">BIAYA</th>
					<th rowspan="2" width="5%" style="text-align: center;">EDIT</th>		
					<th rowspan="2" width="5%" style="text-align: center;">DEL</th>		
				</tr>
			</thead>';
	$t1="select  t_jo_detil_biaya_tr.*, m_cost_tr.nama_cost from
		t_jo_detil_biaya_tr left join m_cost_tr on t_jo_detil_biaya_tr.id_cost = m_cost_tr.id_cost
			where t_jo_detil_biaya_tr.id_detil = '$id' order by t_jo_detil_biaya_tr.id_biaya";
	$h1=mysqli_query($koneksi, $t1);   
	while ($d1=mysqli_fetch_array($h1))		
	{
		$biaya = number_format($d1['biaya'],0);
		$total = $total + $d1['biaya'];
		$n++;
		$data .= '<tr>							
			<td style="text-align:center">'.$n.'.</td>
			<td style="text-align:left">'.$d1['nama_cost'].'</td>	
			<td style="text-align:right">'.$biaya.'</td> ';	
		
			if($mode == 'Edit' ){
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Delete"
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:GetLain('.$d1['id_biaya'].')"  >
		     		<span class="fa fa-edit " ></span>
					</button></td>';
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Delete"
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:DelLain('.$d1['id_biaya'].')"  >
		     		<span class="fa fa-close " ></span>
					</button></td>';
			}
			else
			{
				$data .='<td></td>';
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
	
	$sql = "update t_jo_detil_tr set biaya_lain = '$total' where id_detil = '$id'	";
	$hasil=mysqli_query($koneksi,$sql);
			
    echo $data;	

}else if ($_POST['type'] == "Detil_Lain"){
	$id = $_POST['id'];	
    $query = "select * from t_jo_detil_biaya_tr where id_biaya  = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
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
	
}else if ($_POST['type'] == "Add_Lain"){		
	if($_POST['mode'] != '' )
	{	
		$id_detil = $_POST['id_detil'];
		$mode = $_POST['mode'];
		$id = $_POST['id'];
		$id_cost = $_POST['id_cost'];
		$biaya = $_POST['biaya'];
		$biaya = str_replace(",","", $biaya);
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO  t_jo_detil_biaya_tr (id_detil, id_cost, biaya) values
					('$id_detil', '$id_cost', '$biaya')";
			$hasil= mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update t_jo_detil_biaya_tr set 
					id_cost = '$id_cost',
					biaya = '$biaya'
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
	
}else if ($_POST['type'] == "Del_Lain"){
	$id = $_POST['id']; 
    $query = "DELETE FROM t_jo_detil_biaya_tr WHERE id_biaya = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	
	
}else if ($_POST['type'] == "Add_SJ"){		
	if($_POST['id_detil'] != '' )
	{	
	
		$id_detil = $_POST['id_detil'];
		$tgl_sj = $_POST['tanggal'];
		$id_asal = $_POST['id_asal'];
		$id_tujuan = $_POST['id_tujuan'];
		$barang = trim(addslashes(strtoupper($_POST['barang'])));
		$no_seal = trim(addslashes(strtoupper($_POST['no_seal'])));
		$penerima = trim(addslashes($_POST['penerima']));
		$id_mobil = $_POST['id_mobil'];
		$jenis_mobil = $_POST['jenis_mobil'];
		$id_supir = $_POST['id_supir'];
		$uj = $_POST['uj'];
		$ritase = $_POST['ritase'];
		$no_cont = trim(addslashes(strtoupper($_POST['no_cont'])));
		$ket = trim(addslashes($_POST['ket']));
		$berat = $_POST['berat'];
		$vol = $_POST['vol'];
		$uj = str_replace(",","", $uj);
		$ritase = str_replace(",","", $ritase);
		$berat = str_replace(",","", $berat);
		$vol = str_replace(",","", $vol);
		$tgl_sjx = ConverTglSql($tgl_sj);
		
		$ptgl = explode("-", $tgl_sj);
		$tg = $ptgl[0];
		$bl = $ptgl[1];
		$th = $ptgl[2];	
		$query = "SELECT max(right(no_sj,5)) as maxID FROM t_jo_sj_tr where  year(tgl_sj) = '$th'  ";
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
		
		$sql = "INSERT INTO  t_jo_sj_tr (tipe, no_sj, tgl_sj, no_cont, no_seal, id_asal, id_tujuan, berat, vol, jenis_mobil, id_mobil, 
				id_supir, uj, ritase, ket, created) values
				('FCL', '$no_sj', '$tgl_sjx', '$no_cont', '$no_seal', '$id_asal', '$id_tujuan', '$berat', '$vol', '$jenis_mobil', 
				'$id_mobil', '$id_supir', '$uj', '$ritase', '$ket', '$id_user')";
		$hasil= mysqli_query($koneksi, $sql);
		
		$sql = mysqli_query($koneksi, "select max(id_sj)as id from t_jo_sj_tr ");			
		$row = mysqli_fetch_array($sql);
		$id_sj = $row['id'];
		
		
		$sql = "update t_jo_detil_tr set 
				id_sj = '$id_sj', 
				id_asal = '$id_asal', 
				id_tujuan = '$id_tujuan',
				penerima = '$penerima',
				barang = '$barang',
				jenis_mobil = '$jenis_mobil'
				where id_detil = '$id_detil' ";
		$hasil= mysqli_query($koneksi, $sql);
			
		if (!$hasil) {
	       
			echo "Data Error...!";
	    }
		else
		{	
			
			echo "Data saved!";
		}
	}	

}else if ($_POST['type'] == "Cek_Rate_Cust"){
	$id_asal = $_POST['id_asal'];	
	$id_cust = $_POST['id_cust'];	
	$id_tujuan = $_POST['id_tujuan'];	
	$jenis_mobil = $_POST['jenis_mobil'];	
    $query = "select * from m_cust_rate_tr where id_asal  = '$id_asal' and id_tujuan = '$id_tujuan' and jenis_mobil = '$jenis_mobil' and id_cust = '$id_cust' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
    }
    $response = array();
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response = $row;
        }
    }
    else
    {
		//echo "Data not found!";
        $response['status'] = 200;
        //$response['message'] = "Data not found!";
    }
    echo json_encode($response);	
	
}else if ($_POST['type'] == "Cek_Rate"){
	$id_asal = $_POST['id_asal'];	
	$id_tujuan = $_POST['id_tujuan'];	
	$jenis_mobil = $_POST['jenis_mobil'];	
    $query = "select * from m_rate_tr where id_asal  = '$id_asal' and id_tujuan = '$id_tujuan' and jenis_mobil = '$jenis_mobil' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
    }
    $response = array();
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response = $row;
        }
    }
    else
    {
		echo "Data not found!";
        //$response['status'] = 200;
        //$response['message'] = "Data not found!";
    }
    echo json_encode($response);	
	
	
}else if ($_GET['type'] == "ListCont_PTL")
{	
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="16%" style="text-align: center;">#JO. NO</th>
					<th width="16%" style="text-align: center;">NO. CONTAINER</th>
					<th width="56%" style="text-align: center;">CUSTOMER NAME</th>
					<th width="12%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);

	$SQL = "select t_jo_cont.*, t_jo.deliv, t_jo.id_jo, t_jo.jo_no, m_cust.nama_cust from 
			t_jo_cont inner join t_jo on t_jo_cont.id_jo = t_jo.id_jo
			inner join m_cust on t_jo.id_cust = m_cust.id_cust
			where 
			m_cust.nama_cust LIKE '%$cari%' and t_jo_cont.id_sj_tr = '0'
			or
			t_jo_cont.no_cont LIKE '%$cari%' and t_jo_cont.id_sj_tr = '0'
			order by m_cust.nama_cust LIMIT 0, 25";	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$data .= '<tr>		
				<td style="text-align:center"><a href="#" onclick="PilihJO('.$row['id_cont'].')" >'.$row['jo_no'].'</a></td>	
				<td style="text-align:center"><a href="#" onclick="PilihJO('.$row['id_cont'].')" >'.$row['no_cont'].'</a></td>	
				<td style="text-align:left"><a href="#" onclick="PilihJO('.$row['id_cont'].')" >'.$row['nama_cust'].'</a></td>';	
				$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihJO('.$row['id_cont'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px">ADD</button>
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
	
}else if ($_POST['type'] == "Detil_Cont_PTL"){
	$id = $_POST['id'];	
    $query = "select * from t_jo_cont where id_cont  = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
    }
    $response = array();
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response = $row;
        }
    }
    else
    {
		echo "Data not found!";
        //$response['status'] = 200;
        //$response['message'] = "Data not found!";
    }
    echo json_encode($response);		
	
}

?>