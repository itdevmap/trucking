<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='12' ");
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
					<th rowspan="2" width="9%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="73%" style="text-align: center;">ITEM DESCRIPTION</th>
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
	
	$SQL = "select * from m_part where $f LIKE '%$cari%' 	
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
				}
				else
				{
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
				
				if($sisa <= 0)
				{
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
				$pq = mysqli_query($koneksi, "select count(id_part) as jml from m_part where $f LIKE '%$cari%'   ");					
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


}else if ($_POST['type'] == "Del_Data"){
	$id = $_POST['id']; 	
	
    $query = "DELETE FROM m_part WHERE id_part = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }
	
}else if ($_POST['type'] == "Add_Data"){		
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
	
	
}else if ($_POST['type'] == "Detil_Data"){
	$id = $_POST['id'];	
    $query = "select * from m_part where id_part  = '$id'";
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
else if ($_GET['type'] == "Read_In")
{
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
					<th rowspan="2" width="7%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO PO</th>
					<th rowspan="2" width="10%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="53%" style="text-align: center;">DESCRIPTION</th>
					<th rowspan="2" width="5%" style="text-align: center;">QTY</th>
					<th rowspan="2" width="5%" style="text-align: center;">UNIT</th>
					<th rowspan="2" width="7%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>
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
	
	$SQL = "select m_part_masuk.*, m_part.nama, m_part.kode, m_part.unit
				from  m_part_masuk left join m_part on m_part_masuk.id_part = m_part.id_part 
				where m_part_masuk.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' 
				order  by m_part_masuk.tanggal desc LIMIT $offset, $jmlperhalaman";
			
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
				<td style="text-align:center">'.$tanggal.'</td>
				<td style="text-align:center">'.$row['no_po'].'</td>
				<td style="text-align:center">'.$row['kode'].'</td>
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['qty'].'</td>
				<td style="text-align:center">'.$row['unit'].'</td>
				<td style="text-align:center">'.$row['created'].'</td>';					
				
				if($m_del == '1x' ){
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:DelData('.$row['id_masuk'].')"  >
									<span class="fa fa-close " ></span>
								</button></td>';
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

}else if ($_POST['type'] == "Add_In"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$mode = $_POST['mode'];
		$id_part = $_POST['id_part'];
		$tanggal = ConverTglSql($_POST['tanggal']);
		$qty = $_POST['qty'];
		$no_po = addslashes(trim(strtoupper($_POST['no_po'])));
		$qty = str_replace(",","", $qty);
		$ket = addslashes(trim(strtoupper($_POST['ket'])));
		
		$sql = "INSERT INTO m_part_masuk (id_part, tanggal, no_po, qty, created) values
					('$id_part', '$tanggal', '$no_po', '$qty', '$id_user')";
		$hasil=mysqli_query($koneksi, $sql);
		
		if (!$hasil) {
	        			
			//exit(mysqli_error($koneksi));
			echo "Data Error...!";
	    }
		else
		{	
			$sql = "update m_part set masuk = masuk + '$qty' where id_part = '$id_part' ";
			$hasil=mysqli_query($koneksi, $sql);
			echo "Data saved!";
		}
	}	
	
}else if ($_POST['type'] == "Del_In"){
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
else if ($_GET['type'] == "Read_Out")
{
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
					<th rowspan="2" width="10%" style="text-align: center;">NO. SPK</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. POLICE</th>
					<th rowspan="2" width="10%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="45%" style="text-align: center;">ITEM DESCRIPTION</th>
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
	
	$SQL = "select  t_spk_part.*, t_spk.no_spk, t_spk.tanggal, t_spk.created, m_mobil_tr.no_polisi,
				m_part.kode, m_part.nama, m_part.unit
				from  
				t_spk_part left join t_spk on t_spk_part.id_spk = t_spk.id_spk 
				left join m_mobil_tr on t_spk.id_mobil = m_mobil_tr.id_mobil
				left join m_part on t_spk_part.id_part = m_part.id_part
				where t_spk.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' 
				order  by t_spk.tanggal desc LIMIT $offset, $jmlperhalaman";
			
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
				<td style="text-align:center">'.$tanggal.'</td>
				<td style="text-align:center">'.$row['no_spk'].'</td>
				<td style="text-align:center">'.$row['no_polisi'].'</td>
				<td style="text-align:center">'.$row['kode'].'</td>
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['qty'].'</td>
				<td style="text-align:center">'.$row['unit'].'</td>
				<td style="text-align:center">'.$row['created'].'</td>';					
				
				
				
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
				$pq = mysqli_query($koneksi, "select  count(t_spk_part.id_detil) as jml
				from  
				t_spk_part left join t_spk on t_spk_part.id_spk = t_spk.id_spk 
				left join m_mobil_tr on t_spk.id_mobil = m_mobil_tr.id_mobil
				left join m_part on t_spk_part.id_part = m_part.id_part
				where t_spk.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' ");					
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

}else if($_GET['type'] == "ListKeluar")
{
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
	
	
}else if($_GET['type'] == "ListMasuk")
{
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
	
}else if ($_GET['type'] == "ListPart")
{	
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
	
	$SQL = "select * from m_part where nama LIKE '%$cari%' and (masuk-keluar) > '0'  order by nama LIMIT 0, 25";
	
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
	
	
}else if ($_GET['type'] == "ListPart_In")
{	
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
	
	$SQL = "select * from m_part where nama LIKE '%$cari%' order by nama LIMIT 0, 25";
	
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

?>