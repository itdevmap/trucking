<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='14' ");
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
	$field1 = $_GET['field1'];
	$cari1 = trim($_GET['cari1']);
	$stat = trim($_GET['stat']);
	
	if($field == 'Quo No')
	{
		$f = 't_ware_quo.quo_no';	
	}else if($field == 'No Kontrak'){
		$f = 't_ware_quo.no_kontrak';	
	}else if($field == 'Quo No'){
		$f = 't_ware_quo.quo_no';		
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';	
	}else{
		$f = 't_ware_quo.quo_no';	
	}
	
	if($field1 == 'Quo No')
	{
		$f1 = 't_ware_quo.quo_no';	
	}else if($field1 == 'No Kontrak'){
		$f1 = 't_ware_quo.no_kontrak';	
	}else if($field1 == 'Quo No'){
		$f1 = 't_ware_quo.quo_no';		
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';	
	}else{
		$f1 = 't_ware_quo.quo_no';	
	}
	
	if($stat == 'In Progress')
	{
		$stat = '0';
	}
	else if($stat == 'Executed')
	{
		$stat = '1';
	}
	
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="3%" style="text-align: center;">PROJECT CODE</th>
					<th rowspan="2" width="7%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="8%" style="text-align: center;">QUO NO</th>
					<th rowspan="2" width="34%" style="text-align: center;">CUSTOMER</th>
					<th rowspan="2" width="9%" style="text-align: center;">NO CONTRACT</th>
					<th rowspan="2" width="3%" style="text-align: center;">AGING</th>
					<th rowspan="2" width="5%" style="text-align: center;">COST<br>RENTAL</th>
					<th rowspan="2" width="5%" style="text-align: center;">HANDLING</th>
					<th rowspan="2" width="5%" style="text-align: center;">MAX CBM</th>
					<th rowspan="2" width="9%" style="text-align: center;">SALES</th>
					<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>
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
	
	if($stat == 'All')
	{
		$SQL = "select t_ware_quo.*, m_cust_tr.nama_cust
			  from 
		   t_ware_quo left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
		   where t_ware_quo.quo_date between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' 
			order by t_ware_quo.quo_date desc, t_ware_quo.quo_no desc LIMIT $offset, $jmlperhalaman";
	}else{
		$SQL = "select t_ware_quo.*, m_cust_tr.nama_cust
			  from 
		   t_ware_quo left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
		   where t_ware_quo.quo_date between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_ware_quo.status = '$stat'
			order by t_ware_quo.quo_date desc, t_ware_quo.quo_no desc LIMIT $offset, $jmlperhalaman";
	}
	
	
			
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			$tanggal = ConverTgl($row['quo_date']);
			$sewa = number_format($row['harga_sewa'],0);
			$handling = number_format($row['harga_handling'],0);
			$cbm = number_format($row['max_cbm'],2);
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
			
			$xy1="View|$row[id_quo]";
			$xy1=base64_encode($xy1);
			$link = "ware_data.php?id=$xy1";
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">' . (!empty($row['project_code']) ? $row['project_code'] : '-') . '</td>
				<td style="text-align:center">'.$tanggal.'</td>	
				<td style="text-align:center"><a href="'.$link.'" title="">'.$row['quo_no'].'</a></td>	
				<td style="text-align:left">'.$row['nama_cust'].'</td>
				<td style="text-align:center">'.$row['no_kontrak'].'</td>
				<td style="text-align:center">'.$row['aging_sewa'].'</td>	
				<td style="text-align:right">'.$sewa.'</td>
				<td style="text-align:right">'.$handling.'</td>
				<td style="text-align:right">'.$cbm.'</td>
				<td style="text-align:center">'.$row['sales'].'</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';					
				
				//if($m_edit == '1' && $row['status'] == '0' ) {
				if($m_edit == '1'  ) {	
					$xy1="Edit|$row[id_quo]";
					$xy1=base64_encode($xy1);
					$link = "'ware_data.php?id=$xy1'";
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
				
				if($m_del == '1' && $row['status'] == '0') 	
				{
					$data .= '<td>
							<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:DelQuo('.$row['id_quo'].')"  >
								<span class="fa fa-close " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				
				if($row['status'] == '0'  ) {
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Execute"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:Confirm('.$row['id_quo'].')"  >
									<span class="fa fa-check-square-o " ></span>
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
				if($stat == 'All')
				{
					$pq = mysqli_query($koneksi, "select count(t_ware_quo.id_quo) as jml
					  from 
				   t_ware_quo left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
				   where t_ware_quo.quo_date between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%'  ");
				}else{
					$pq = mysqli_query($koneksi, "select count(t_ware_quo.id_quo) as jml
					  from 
				   t_ware_quo left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
				   where t_ware_quo.quo_date between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_ware_quo.status = '$stat' ");
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
else if ($_GET['type'] == "Read_Barang")
{
	$cari = trim($_GET['cari']);
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	
	$field = $_GET['field'];
	$field1 = $_GET['field1'];
	$cari1 = trim($_GET['cari1']);
	$stat = trim($_GET['stat']);
	
	if($field == 'Kode Barang')
	{
		$f = 't_ware.kode';	
	}else if($field == 'Nama Barang'){
		$f = 't_ware.nama';	
	}else if($field == 'Quo No'){
		$f = 't_ware_quo.quo_no';		
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';	
	}else{
		$f = 't_ware.nama';	
	}
	
	if($field1 == 'Kode Barang')
	{
		$f1 = 't_ware.kode';	
	}else if($field1 == 'Nama Barang'){
		$f1 = 't_ware.nama';	
	}else if($field1 == 'Quo No'){
		$f1 = 't_ware_quo.quo_no';		
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';	
	}else{
		$f1 = 't_ware.nama';	
	}
	
	
	
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="11%" style="text-align: center;">ITEM CODE</th>
					<th rowspan="2" width="30%" style="text-align: center;">ITEM NAME</th>
					<th rowspan="2" width="4%" style="text-align: center;">UoM</th>
					<th rowspan="2" width="4%" style="text-align: center;">WEIGHT</th>
					<th rowspan="2" width="4%" style="text-align: center;">LENGTH</th>
					<th rowspan="2" width="4%" style="text-align: center;">WIDE</th>
					<th rowspan="2" width="4%" style="text-align: center;">HEIGHT</th>
					<th rowspan="2" width="4%" style="text-align: center;">VOL</th>
					<th rowspan="2" width="7%" style="text-align: center;">QUO NO</th>
					<th rowspan="2" width="14%" style="text-align: center;">CUSTOMER</th>
					<th colspan="3" width="9%" style="text-align: center;">DATA STOCK</th>
					<th rowspan="2" width="2%" style="text-align: center;">DOWN<br>LOAD</th>
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
	
	
	
	$SQL = "select t_ware.*, t_ware_quo.quo_no,	m_cust_tr.nama_cust
			  from 
		   t_ware left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
		   left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
		   where  t_ware_quo.status = '1' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' 
			order by t_ware.nama LIMIT $offset, $jmlperhalaman";
			
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;				
			$berat = number_format($row['berat'],2);
			$vol = number_format($row['vol'],2);
			$panjang = number_format($row['panjang'],2);
			$lebar = number_format($row['lebar'],2);	
			$tinggi = number_format($row['tinggi'],2);
			$sisa_qty  = $row['masuk'] - $row['keluar'];
			
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
					
				
				<td style="text-align:center">'.$row['kode'].'</td>
				<td style="text-align:left">'.$row['nama'].'</td>				
				<td style="text-align:center">'.$row['unit'].'</td>	
				<td style="text-align:center">'.$berat.'</td>
				<td style="text-align:center">'.$panjang.'</td>
				<td style="text-align:right">'.$lebar.'</td>
				<td style="text-align:right">'.$tinggi.'</td>
				<td style="text-align:center">'.$vol.'</td>
				<td style="text-align:center">'.$row['quo_no'].'</td>
				<td style="text-align:left">'.$row['nama_cust'].'</td>';					
			
				
				$data .= '<td style="text-align:right">
					<button class="btn btn-block btn-primary"  
						style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button" 
						onClick="javascript:ListMasukx('.$row['id_ware'].')"  >
						'.$row['masuk'].'
					</button>
					</td>';				
					$data .= '<td style="text-align:right">
						<button class="btn btn-block btn-warning"  
							style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button" 
							onClick="javascript:ListKeluarx('.$row['id_ware'].')"  >
							'.$row['keluar'].'
						</button>
					</td>';				
					$data .= '<td style="text-align:right">
						<button class="btn btn-block btn-success"  
							style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button"   >
							'.$sisa_qty.'
						</button>
						</td>';	
				
				$data .= '<td>
								<button class="btn btn-block btn-default"  title="Download"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:DownloadData('.$row['id_ware'].')"  >
									<span class="fa fa-file-text" ></span>
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
				$pq = mysqli_query($koneksi, "select count(t_ware.id_ware) as jml
					  from 
				   t_ware left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
				   left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
				   where  t_ware_quo.status = '1' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' ");
					
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


}else if ($_POST['type'] == "Del_Quo"){
	$id = $_POST['id']; 	
	
	$del = mysqli_query($koneksi, "delete from t_ware where id_quo = '$id' ");
	$del = mysqli_query($koneksi, "delete from t_ware_quo_biaya where id_quo = '$id' ");
    $query = "DELETE FROM t_ware_quo WHERE id_quo = '$id' ";
	
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }	

}else if ($_POST['type'] == "Executed"){		
	if($_POST['id'] != '' )
	{	
		$id = $_POST['id'];
		$sql = "update t_ware_quo set 
				status = '1'
				where id_quo = '$id'	";
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
}else if($_GET['type'] == "Read_Data_Quo")
{
	$id_quo = $_GET['id_quo'];
	$mode = $_GET['mode'];
	$stat = $_GET['stat'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>	
					<th rowspan="2" width="12%" style="text-align: center;">ITEM NO</th>
					<th rowspan="2" width="51%" style="text-align: center;">ITEM DESCRIPTION</th>
					<th rowspan="2" width="5%" style="text-align: center;">UOM</th>
					<th rowspan="2" width="5%" style="text-align: center;">WEIGHT</th>
					<th rowspan="2" width="5%" style="text-align: center;">LENGTH</th>
					<th rowspan="2" width="5%" style="text-align: center;">WIDE</th>
					<th rowspan="2" width="5%" style="text-align: center;">HEIGHT</th>
					<th rowspan="2" width="5%" style="text-align: center;">VOL</th>
					<th colspan="2" width="4%" style="text-align: center;">ACTION</th>						
				</tr>
				<tr>
					<th width="2%" style="text-align: center;">EDIT</th>
					<th width="2%" style="text-align: center;">DEL</th>
				</tr>	
			</thead>';	
	$SQL = "select * from t_ware  where id_quo = '$id_quo' order by  id_ware";
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
			$berat = number_format($row['berat'],2);
			$vol = number_format($row['vol'],5);
			$panjang = number_format($row['panjang'],2);
			$lebar = number_format($row['lebar'],2);	
			$tinggi = number_format($row['tinggi'],2);
			
			$data .= '<tr>						
				<td style="text-align:center">'.$posisi.'.</td>			
				<td style="text-align:center">'.$row['kode'].'</td>	
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['unit'].'</td>
				<td style="text-align:center">'.$berat.'</td>
				<td style="text-align:center">'.$panjang.'</td>
				<td style="text-align:center">'.$lebar.'</td>
				<td style="text-align:center">'.$tinggi.'</td>
				<td style="text-align:center">'.$vol.'</td>';
				
				if($mode == 'Edit' ){
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Edit"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_ware'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				
				if($mode == 'Edit' && $stat == '0' ){
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:DelData('.$row['id_ware'].')"  >
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
	
    echo $data;	
	
}else if($_GET['type'] == "Read_Data_Quo_Biaya")
{
	$id_quo = $_GET['id_quo'];
	$mode = $_GET['mode'];
	$stat = $_GET['stat'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>	
					<th rowspan="2" width="85%" style="text-align: center;">COST NAME</th>
					<th rowspan="2" width="8%" style="text-align: center;">COST</th>
					<th colspan="2" width="4%" style="text-align: center;">ACTION</th>						
				</tr>
				<tr>
					<th width="2%" style="text-align: center;">EDIT</th>
					<th width="2%" style="text-align: center;">DEL</th>
				</tr>	
			</thead>';	
	$SQL = "select t_ware_quo_biaya.*, m_cost_tr.nama_cost 
			from 
			t_ware_quo_biaya left join m_cost_tr on t_ware_quo_biaya.id_biaya = m_cost_tr.id_cost
			where t_ware_quo_biaya.id_quo = '$id_quo' order by  t_ware_quo_biaya.id_detil";
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
				<td style="text-align:left">'.$row['nama_cost'].'</td>	
				<td style="text-align:right">'.$harga.'</td>';
				
				if($mode == 'Edit' ){
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Edit"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_detil'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				
				if($mode == 'Edit' && $stat == '0'  ){
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:DelData('.$row['id_detil'].')"  >
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
	
    echo $data;		
	
}else if ($_POST['type'] == "Add_Data_Quo"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$id_quo = $_POST['id_quo'];
		$kode = addslashes(trim(strtoupper($_POST['kode'])));
		$nama = addslashes(trim(strtoupper($_POST['nama'])));	
		$unit = addslashes(trim($_POST['unit']));	
		$panjang = $_POST['panjang'];
		$lebar = $_POST['lebar'];
		$tinggi = $_POST['tinggi'];
		$vol = $_POST['vol'];
		$berat = $_POST['berat'];
		$mode = $_POST['mode'];
		
		$vol = str_replace(",","", $vol);
		// die($vol );
		$berat = str_replace(",","", $berat);
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO t_ware (kode, id_quo, nama, unit, panjang, lebar, tinggi, berat, vol) values ('$kode', '$id_quo', '$nama', '$unit', '$panjang', '$lebar', '$tinggi', '$berat', '$vol')";

			// $hasil=mysqli_query($koneksi, $sql);
		} else {
			$sql = "update t_ware set 
					kode = '$kode',
					nama = '$nama',
					unit = '$unit',
					panjang = '$panjang',
					lebar = '$lebar',
					tinggi = '$tinggi',
					berat = '$berat',
					vol = '$vol'
					where 	id_ware = '$id'	";
			// $hasil=mysqli_query($koneksi, $sql);
		}

		$hasil = mysqli_query($koneksi, $sql);

		if (!$hasil) {
			echo "Data Error: " . mysqli_error($koneksi) . "<br>";
			echo "Query: " . $sql;
		} else {
			echo "Data saved!";
		}
		die();
	}		

}else if ($_POST['type'] == "Detil_Data_Quo"){
	$id = $_POST['id'];	
    $query = "select t_ware.*, t_ware_quo.quo_no, m_cust_tr.nama_cust 
			from 
		   t_ware left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
		   left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
		   where t_ware.id_ware = '$id' ";
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

}else if ($_POST['type'] == "Detil_Data_Quo_Biaya"){
	$id = $_POST['id'];	
    $query = "select t_ware_quo_biaya.*, m_cost_tr.nama_cost
		from t_ware_quo_biaya left join m_cost_tr on t_ware_quo_biaya.id_biaya = m_cost_tr.id_cost
		   where t_ware_quo_biaya.id_detil = '$id' ";
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
	
}else if ($_POST['type'] == "Detil_Data_Barang"){
	$id = $_POST['id'];	
    $query = "select * from t_ware where id_ware = '$id' ";
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
	
}else if ($_POST['type'] == "Del_Data_Quo"){
	$id = $_POST['id']; 	
	
    $query = "DELETE FROM t_ware WHERE id_ware = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }
	
}else if ($_POST['type'] == "Del_Data_Quo_Jasa"){
	$id = $_POST['id']; 	
	
    $query = "DELETE FROM t_ware_quo_biaya WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }	
	
}else if ($_POST['type'] == "Add_Data_Quo_Biaya"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$id_quo = $_POST['id_quo'];
		$id_biaya = $_POST['id_biaya'];
		$mode = $_POST['mode'];
		$harga = $_POST['harga'];
		$harga = str_replace(",","", $harga);
		$berat = str_replace(",","", $berat);
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO t_ware_quo_biaya (id_quo, id_biaya, harga) values
					('$id_quo', '$id_biaya', '$harga')";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update t_ware_quo_biaya set 
					id_biaya = '$id_biaya',
					harga = '$harga'
					where 	id_detil = '$id'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Data Error...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	

}
else if ($_GET['type'] == "Read_In")
{
	
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	$tgl1 = $_GET['tgl1'];
	$tgl2 = $_GET['tgl2'];
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);	
	$field = $_GET['field'];
	$cari = trim($_GET['cari']);
	$field1 = $_GET['field1'];
	$cari1 = trim($_GET['cari1']);
	$stat = $_GET['stat'];
	
	if($field == 'Customer')
	{
		$f = 'm_cust_tr.nama_cust';	
	}else if($field == 'No Doc'){
		$f = 't_ware_data.no_doc';	
	}else if($field == 'Container'){
		$f = 't_ware_data_detil.no_cont';		
	}else if($field == 'Item Number'){
		$f = 't_ware.kode';		
	}else if($field == 'Description'){
		$f = 't_ware.nama';		
	}else{
		$f = 't_ware.nama';
	}
	
	if($field1 == 'Customer')
	{
		$f1 = 'm_cust_tr.nama_cust';	
	}else if($field1 == 'No Doc'){
		$f1 = 't_ware_data.no_doc';	
	}else if($field1 == 'Container'){
		$f1 = 't_ware_data_detil.no_cont';		
	}else if($field1 == 'Item Number'){
		$f1 = 't_ware.kode';		
	}else if($field1 == 'Description'){
		$f1 = 't_ware.nama';		
	}else{
		$f1 = 't_ware.nama';
	}
	
	if($stat == 'In Progress')
	{
		$stat = '0';
	}
	else if($stat == 'Executed')
	{
		$stat = '1';
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">DATE</th>					
					<th rowspan="2" width="6%" style="text-align: center;">#NO DOC</th>
					<th rowspan="2" width="8%" style="text-align: center;">CONTAINER</th>					
					<th rowspan="2" width="13%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="25%" style="text-align: center;">DESCRIPTION</th>
					<th rowspan="2" width="11%" style="text-align: center;">CUSTOMER</th>
					<th rowspan="2" width="7%" style="text-align: center;">LOCATION</th>
					<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>
					<th colspan="3" width="9%" style="text-align: center;">STOCK</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>
					<th rowspan="2" width="2%" style="text-align: center;">EXEC</th>
					<th rowspan="2" width="2%" style="text-align: center;">PRINT</th>
				</tr>
				<tr>
					<th width="3%" style="text-align: center;">IN</th>
					<th width="3%" style="text-align: center;">OUT</th>
					<th width="3%" style="text-align: center;">WHSE</th>
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
		$SQL = "select t_ware_data.*, t_ware_data_detil.id_detil, t_ware_data_detil.no_cont, 
			t_ware_data_detil.masuk, t_ware_data_detil.keluar, t_ware.nama, t_ware.kode, 
			t_ware.vol, t_ware.unit, m_cust_tr.nama_cust, m_lokasi_ware.nama as nama_lokasi
			from  
			t_ware_data left join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
			left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
			left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
			left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
			where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_ware_data.jenis = '0'
			order  by t_ware_data.tanggal desc LIMIT $offset, $jmlperhalaman";
	}else{
		$SQL = "select t_ware_data.*, t_ware_data_detil.id_detil, t_ware_data_detil.no_cont, t_ware_data_detil.masuk, t_ware_data_detil.keluar, t_ware.nama, t_ware.kode, 
			t_ware.vol, t_ware.unit, m_cust_tr.nama_cust, m_lokasi_ware.nama as nama_lokasi
			from  
			t_ware_data left join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
			left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
			left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
			left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
			where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_ware_data.jenis = '0'
			and t_ware_data.status = '$stat'
			order  by t_ware_data.tanggal desc LIMIT $offset, $jmlperhalaman";
	}
	
	
			
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
			$sisa  = $row['masuk'] - $row['keluar'];
			if($row['status'] == '0' )
			{
				$label = 'danger';
				$status = 'In Progress';
				
			}else if($row['status'] == '1' ){
				$label = 'success';
				$status = 'Executed';
			}			
			$xy1="View|$row[id_data]";
			$xy1=base64_encode($xy1);
			$link = "ware_in_data.php?id=$xy1";	
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>
				<td style="text-align:center"><a href="'.$link.'" title="">'.$row['no_doc'].'</a></td>
				<td style="text-align:center">'.$row['no_cont'].'</td>				
				<td style="text-align:center">'.$row['kode'].'</td>
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['nama_cust'].'</td>';
				
				if($sisa > 0 )
				{
					$data .= '<td style="text-align:right">
					<button class="btn btn-block btn-default"  
						style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button" 
						onClick="javascript:GetLokasi('.$row['id_detil'].')"  >
						'.$row['nama_lokasi'].'
						</button>
					</td>';	
				}else{
					$data .='<td>'.$row['nama_lokasi'].'</td>';
				}	
				
				$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';	
				
				if($row['status'] == '1')
				{
					$data .= '<td style="text-align:right">
					<button class="btn btn-block btn-primary"  
						style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button" 
						onClick="javascript:GetStok('.$row['id_detil'].')"  >
						'.$row['masuk'].'
					</button>
					</td>';
					$data .= '<td style="text-align:right">
						<button class="btn btn-block btn-warning"  
							style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button" 
							onClick="javascript:DownloadData('.$row['id_detil'].')"  >
							'.$row['keluar'].'
						</button>
					</td>';				
					$data .= '<td style="text-align:right">
						<button class="btn btn-block btn-success"  
							style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button"   >
							'.$sisa.'
						</button>
						</td>';
				}else{
					$data .= '<td style="text-align:right">
					<button class="btn btn-block btn-default"  
						style="padding:1px;border-radius:0px;width:100%;text-align:center" type="button" 
						onClick="javascript:ListMasukx('.$row['id_ware'].')"  >
						'.$row['masuk'].'
					</button>
					</td>';
					$data .='<td></td>';
					$data .='<td></td>';
				}
				
				
				
				if($m_edit == '1' && $row['status'] =='0' ){
					$xy1="Edit|$row[id_data]";
					$xy1=base64_encode($xy1);
					$link = "'ware_in_data.php?id=$xy1'";
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="window.location.href = '.$link.' "  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				
				if($m_del == '1' && $row['status'] == '0') 	
				{
					if(empty($row['id_detil']))
					{
						$data .= '<td>
							<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:DelData('.$row['id_data'].')"  >
								<span class="fa fa-close " ></span>
								</button></td>';
					}else{
						$data .= '<td>
							<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:DelDetil('.$row['id_detil'].')"  >
								<span class="fa fa-close " ></span>
								</button></td>';
					}
				}
				else
				{
					$data .='<td></td>';
				}
				
				
				if($row['status'] == '0' && !empty($row['id_detil'])) {
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Executed"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:Executed('.$row['id_data'].')"  >
									<span class="fa fa-check-square-o " ></span>
								</button></td>';
						
				}
				else
				{
					$data .='<td></td>';
				}
				$xy1="$row[id_data]";
				$xy1=base64_encode($xy1);
				$link = "'cetak_po_masuk.php?id=$xy1'";
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Print"
						style="margin:-3px;border-radius:0px" type="button" 									
						onClick="window.open('.$link.') ">
						<span class="fa fa-print " ></span>
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
					$pq = mysqli_query($koneksi, "select count(t_ware_data_detil.id_detil) as jml
					from  
					t_ware_data left join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data
					left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
					left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
					left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
					left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
					where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' 
					and t_ware_data.jenis = '0'");		
				}else{
					$pq = mysqli_query($koneksi, "select count(t_ware_data_detil.id_detil) as jml
					from  
					t_ware_data left join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data
					left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
					left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
					left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
					left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
					where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' 
					and t_ware_data.jenis = '0' and t_ware_data.status = '$stat' ");	
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
else if($_GET['type'] == "Read_In_Data")
{
	$id_data = $_GET['id_data'];
	$mode = $_GET['mode'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="8%" style="text-align: center;">CONTAINER</th>
					<th rowspan="2" width="12%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="55%" style="text-align: center;">ITEM DESCRIPTION</th>
					<th rowspan="2" width="5%" style="text-align: center;">QTY</th>
					<th rowspan="2" width="5%" style="text-align: center;">UoM</th>
					<th rowspan="2" width="8%" style="text-align: center;">LOCATION</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>						
				</tr>
			</thead>';	
	$total = 0;		
	$SQL = "select t_ware_data_detil.*, t_ware.nama, t_ware.unit, t_ware.kode, m_lokasi_ware.nama as nama_lokasi
			from 
			t_ware_data_detil inner join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware
			left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
			where t_ware_data_detil.id_data = '$id_data'  order by  t_ware_data_detil.id_detil";
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
				<td style="text-align:center">'.$row['no_cont'].'</td>	
				<td style="text-align:center">'.$row['kode'].'</td>
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['masuk'].'</td>
				<td style="text-align:center">'.$row['unit'].'</td>
				<td style="text-align:center">'.$row['nama_lokasi'].'</td>';
				
				if($mode == 'Edit'){
					
				
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_detil'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';	
					
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:DelData('.$row['id_detil'].')"  >
								<span class="fa fa-close " ></span>
							</button></td>';
							
				}else{
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
    echo $data;			

}else if ($_POST['type'] == "Add_Data_In"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$mode = $_POST['mode'];
		$id_ware = $_POST['id_ware'];
		$id_data = $_POST['id_data'];
		$qty = $_POST['qty'];
		$id_lokasi = $_POST['id_lokasi'];
		$no_cont = addslashes(trim(strtoupper($_POST['no_cont'])));
		$qty = str_replace(",","", $qty);
		
		if($mode == 'Add')
		{
			$sql = "INSERT INTO t_ware_data_detil (id_data, id_ware, no_cont, id_lokasi, masuk, est_masuk) values
						('$id_data', '$id_ware', '$no_cont', '$id_lokasi', '$qty', '$qty')";
			$hasil=mysqli_query($koneksi, $sql);
		}else{
			$sql = "update t_ware_data_detil set id_ware = '$id_ware', no_cont = '$no_cont', masuk = '$qty', est_masuk = '$qty', id_lokasi = '$id_lokasi' where id_detil = '$id' ";
			$hasil=mysqli_query($koneksi, $sql);
			
		}
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Data Error...!";
	    }
		else
		{	
			
			echo "Data saved!";
		}
	}	
}else if ($_POST['type'] == "UpdateStok"){
	$id = $_POST['id']; 	
	$id_ware = $_POST['id_ware']; 
	$qty_masuk = $_POST['qty_masuk']; 
	$qty_lama = $_POST['qty_lama']; 
	 
	$sql = "update t_ware set masuk = masuk - '$qty_lama' where id_ware = '$id_ware' ";
	$hasil=mysqli_query($koneksi, $sql);
	
	$sql = "update t_ware set masuk = masuk + '$qty_masuk' where id_ware = '$id_ware' ";
	$hasil=mysqli_query($koneksi, $sql);
		
	$query = "update t_ware_data_detil set masuk = '$qty_masuk' where id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }else{
		echo "Data saved!";
	}		
	
}	
else if ($_POST['type'] == "Detil_Data_In")
{
	$id = $_POST['id'];	
    $query = "select t_ware_data_detil.*, t_ware.nama, t_ware.kode, t_ware.unit
				from  t_ware_data_detil left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
				where t_ware_data_detil.id_detil  = '$id'";
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
else if ($_POST['type'] == "Del_Data_In")
{
	$id = $_POST['id']; 	
    $query = "DELETE FROM t_ware_data WHERE id_data = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }	
}
else if ($_POST['type'] == "Del_Detil_In")
{
	$id = $_POST['id']; 	

    $query = "DELETE FROM t_ware_data_detil WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }		
	
}
else if ($_POST['type'] == "Executed_In")
{
	$id = $_POST['id']; 	
	
	$t1 = "select * from  t_ware_data_detil	where id_data = '$id'  order by  id_detil ";
	$h1 = mysqli_query($koneksi, $t1); 
	while ($d1=mysqli_fetch_array($h1))
	{
		
		$sql = "update t_ware set masuk = masuk + '$d1[masuk]' where id_ware = '$d1[id_ware]' ";
		$hasil=mysqli_query($koneksi, $sql);
	}
	
	$query = "update t_ware_data set status = '1' where id_data = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }	
	
}
else if ($_POST['type'] == "UpdateLokasi")
{
	$id = $_POST['id']; 	
	$id_lokasi = $_POST['id_lokasi']; 
	$query = "update t_ware_data_detil set id_lokasi = '$id_lokasi' where id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }else{
		echo "Data saved!";
	}
	
}
else if ($_POST['type'] == "Detil_Data_Barang")
{
	$id = $_POST['id'];	
    $query = "select t_ware.*, t_ware_quo.quo_no, m_cust_tr.nama_cust 
			from 
		   t_ware left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
		   left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
		   where t_ware.id_ware = '$id' ";
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
else if ($_GET['type'] == "List_Barang")
{	
	$cari = $_GET['cari'];
	$id_cust = $_GET['id_cust'];
	$filter = $_GET['filter'];
	
	if($filter == 'Item Description')
	{
		$f = 't_ware.nama';	
	}else if($filter == 'Item Number'){
		$f = 't_ware.kode';
	}else{
		$f = 't_ware.nama';	
	}
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="5%" style="text-align: center;">NO</th>
					<th width="11%" style="text-align: center;">QUO NO</th>
					<th width="10%" style="text-align: center;">ITEM NUMBER</th>
					<th width="69%" style="text-align: center;">ITEM DESCRIPTION</th>
					<th width="5%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "select t_ware.*, t_ware_quo.quo_no, m_cust_tr.nama_cust 
			from 
		   t_ware left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
		   left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
		   where $f LIKE '%$cari%' and t_ware_quo.status = '1' and t_ware_quo.id_cust = '$id_cust'  and $f LIKE '%$cari%'
		   order by t_ware_quo.quo_no, t_ware.nama LIMIT 0,25";
	// die($SQL);
	$query = mysqli_query($koneksi, $SQL);

	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }

    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$n++;
			$sisa = $row['masuk'] - $row['keluar'];
			$data .= '<tr>';
			$data .= '<td style="text-align:center">'.$n.'.</td>';	
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihBarang('.$row['id_ware'].')" >'.$row['quo_no'].'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihBarang('.$row['id_ware'].')" >'.$row['kode'].'</a></td>';
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihBarang('.$row['id_ware'].')" >'.$row['nama'].'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihPart('.$row['id_ware'].')" 
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
	$cari = trim($_GET['cari']);
	$field1 = $_GET['field1'];
	$cari1 = trim($_GET['cari1']);
	
	$stat = trim($_GET['stat']);
	if($stat == 'In Progress')
	{
		$stat = '0';
	}
	else if($stat == 'Executed')
	{
		$stat = '1';
	}
	
	if($field == 'No SJ')
	{
		$f = 't_ware_data.no_doc';	
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';	
	}else if($field == 'Gudang'){
		$f = 't_ware_data.gudang';	
	}else if($field == 'Supir'){
		$f = 't_ware_data.supir';	
	}else if($field == 'No Ref'){
		$f = 't_ware_data.no_ref';			
	}else if($field == 'No Polisi'){
		$f = 't_ware_data.no_polisi';			
	}else{
		$f = 't_ware_data.no_doc';
	}
	
	if($field1 == 'No SJ')
	{
		$f1 = 't_ware_data.no_doc';	
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';	
	}else if($field1 == 'Gudang'){
		$f1 = 't_ware_data.gudang';	
	}else if($field1 == 'Supir'){
		$f1 = 't_ware_data.supir';	
	}else if($field1 == 'No Ref'){
		$f1 = 't_ware_data.no_ref';				
	}else if($field1 == 'No Polisi'){
		$f1 = 't_ware_data.no_polisi';			
	}else{
		$f1 = 't_ware_data.no_doc';
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. SJ<br>NO. REF</th>
					<th rowspan="2" width="26%" style="text-align: center;">CUSTOMER</th>
					<th rowspan="2" width="10%" style="text-align: center;">WAREHOUSE</th>
					<th rowspan="2" width="10%" style="text-align: center;">SUPIR</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. POLISI</th>
					<th rowspan="2" width="6%" style="text-align: center;">BILL</th>
					<th rowspan="2" width="7%" style="text-align: center;">CREATED</th>	
					<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>					
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>					
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>
					<th colspan="2" width="4%" style="text-align: center;">PRINT</th>
					<th rowspan="2" width="2%" style="text-align: center;">EXEC</th>
				</tr>
				<tr>					
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
	
	if($stat == 'All')
	{
		
		$SQL = "select t_ware_data.*, m_cust_tr.nama_cust
		  from 
		  t_ware_data left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
		where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and $f1 LIKE '%$cari1%' and t_ware_data.jenis = '1'
		order  by t_ware_data.tanggal desc, t_ware_data.no_doc desc LIMIT $offset, $jmlperhalaman";
	}else{
		$SQL = "select t_ware_data.*, m_cust_tr.nama_cust
		from 
		t_ware_data left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
		where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and $f1 LIKE '%$cari1%' 
		and t_ware_data.jenis = '1' and t_ware_data.status = '$stat'
		order  by t_ware_data.tanggal desc, t_ware_data.no_doc desc LIMIT $offset, $jmlperhalaman";
	}		
			
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
			$tagihan = number_format($row['tagihan'],0);
			$xy1="View|$row[id_data]";
			$xy1=base64_encode($xy1);
			
			if($row['jasa'] == '1')
			{
				$link = "ware_out_jasa.php?id=$xy1";
			}else{
				$link = "ware_out_data.php?id=$xy1";
			}
			if($row['status'] == '0')
			{
				$label = 'danger';
				$status = 'Open';
			}
			else if($row['status'] == '1')
			{
				$label = 'success';
				$status = 'Close';
			}
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>
				<td style="text-align:center"><a href="'.$link.'" title="">'.$row['no_doc'].'</a><br>'.$row['no_ref'].'</td>
				<td style="text-align:left">'.$row['nama_cust'].'</td>
				<td style="text-align:center">'.$row['gudang'].'</td>
				<td style="text-align:center">'.$row['supir'].'</td>
				<td style="text-align:center">'.$row['no_polisi'].'</td>
				<td style="text-align:right">'.$tagihan.'</td>
				<td style="text-align:center">'.$row['created'].'</td>
				<td style="text-align:center">
						<button type="button" class="btn btn-'.$label.'"  
						style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
					</td>';		
					
				if($m_edit == '1' && $row['status'] == '0' ){
					$xy1="Edit|$row[id_data]";
					$xy1=base64_encode($xy1);
					if($row['jasa'] == '1')
					{
						$link = "'ware_out_jasa.php?id=$xy1'";
					}else{
						$link = "'ware_out_data.php?id=$xy1'";
					}
					
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="window.location.href = '.$link.' "  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				if($m_del == '1' && $row['status'] == '0') 	
				{
					$data .= '<td>
							<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:Delete('.$row['id_data'].')"  >
								<span class="fa fa-close " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				
				$xy1="$row[id_data]";
				$xy1=base64_encode($xy1);
				
				if($row['jasa'] == '1')
				{
					$link = "'cetak_inv_jasa.php?id=$xy1'";
				}else{
					$link = "'cetak_inv_ware.php?id=$xy1'";
				}
					$data .= '<td>
						<button class="btn btn-block btn-default"  title="Print"
							style="margin:-3px;border-radius:0px" type="button" 									
							onClick="window.open('.$link.') ">
							<span class="fa fa-print " ></span>
						</button></td>';
						
				$xy1="$row[id_data]";
				$xy1=base64_encode($xy1);
				
				if($row['jasa'] == '1')
				{
					$link = "'cetak_sj_jasa.php?id=$xy1'";
				}else{
					$link = "'cetak_sj_ware.php?id=$xy1'";
				}
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Print"
						style="margin:-3px;border-radius:0px" type="button" 									
						onClick="window.open('.$link.') ">
						<span class="fa fa-print " ></span>
					</button></td>';
						
				if($m_exe == '1' && $row['status'] == '0' ) {
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Executed"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:Executed('.$row['id_data'].')"  >
									<span class="fa fa-check-square-o " ></span>
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
				if($stat == 'All')
				{
					$pq = mysqli_query($koneksi, "select count(t_ware_data.id_data) as jml
					from 
					t_ware_data left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
					where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and $f1 LIKE '%$cari1%' 
					and t_ware_data.jenis = '1'    ");
				}else{
					$pq = mysqli_query($koneksi, "select count(t_ware_data.id_data) as jml
					from 
					t_ware_data left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
					where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and $f1 LIKE '%$cari1%' 
					and t_ware_data.jenis = '1' and t_ware_data.status = '$stat'   ");
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
else if ($_GET['type'] == "ListPart_In")
{	
	$cari = $_GET['cari'];
	$id_cust = $_GET['id_cust'];
	$filter = $_GET['filter'];
	
	if($filter == 'Item Description')
	{
		$f = 't_ware.nama';	
	}else if($filter == 'No Container'){
		$f = 't_ware_masuk.no_cont';
	}else if($filter == 'Item Number'){
		$f = 't_ware.kode';	
	}else{
		$f = 't_ware.nama';	
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="4%" rowspan="2" style="text-align: center;">NO</th>
					<th width="10%" rowspan="2" style="text-align: center;">DATE</th>
					<th width="10%" rowspan="2" style="text-align: center;">NO. DOC</th>
					<th width="10%" rowspan="2" style="text-align: center;">ITEM NUMBER</th>
					<th width="37%" rowspan="2" style="text-align: center;">ITEM DESCRIPTION</th>
					<th width="15%" rowspan="2" style="text-align: center;">CONTAINER</th>
					<th width="10%" rowspan="2" style="text-align: center;">STOCK</th>
					<th width="4%" rowspan="2" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
		
	
	$SQL = "select t_ware_data.*, t_ware_data_detil.id_detil, t_ware_data_detil.no_cont, t_ware_data_detil.masuk, t_ware_data_detil.keluar, 
				t_ware_data_detil.est_keluar, t_ware.nama, t_ware.kode, 
				t_ware.vol, t_ware.unit, m_cust_tr.nama_cust, m_lokasi_ware.nama as nama_lokasi
				from  
				t_ware_data left join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data
				left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
				left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
				left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
				left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
				where t_ware_data.id_cust = '$id_cust' and t_ware_data.status = '1' and $f LIKE '%$cari%' and t_ware_data_detil.masuk - t_ware_data_detil.keluar > 0
				order by nama LIMIT 0, 25";
				
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
		exit(mysqli_error($koneksi));
	}
	if(mysqli_num_rows($result) > 0)
	{
		while($row = mysqli_fetch_assoc($result))
		{	
			$n++;
			$tanggal = ConverTgl($row['tanggal']);
			$sisa = $row['masuk'] - $row['est_keluar'];
			$data .= '<tr>';
			$data .= '<td style="text-align:center">'.$n.'.</td>';	
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihPart('.$row['id_detil'].')" >'.$tanggal.'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihPart('.$row['id_detil'].')" >'.$row['no_doc'].'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihPart('.$row['id_detil'].')" >'.$row['kode'].'</a></td>';
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihPart('.$row['id_detil'].')" >'.$row['nama'].'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihPart('.$row['id_detil'].')" >'.$row['no_cont'].'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihPart('.$row['id_detil'].')" >'.$sisa.'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihPart('.$row['id_detil'].')" 
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

}else if ($_POST['type'] == "Executed_Out"){
	$id = $_POST['id']; 
	
	$t1 = "select * from t_ware_data_detil where id_data = '$id' order by id_detil";
	$h1 = mysqli_query($koneksi, $t1); 
	while ($d1=mysqli_fetch_array($h1))
	{
		
		$sql = "update t_ware set keluar = keluar + '$d1[keluar]' where id_ware = '$d1[id_ware]' ";
		$hasil=mysqli_query($koneksi, $sql);
			
		$sql = "update t_ware_data_detil set keluar = keluar + '$d1[keluar]' where id_detil = '$d1[id_detil_masuk]' ";
		$hasil=mysqli_query($koneksi, $sql);
	}
	
	$query = "update t_ware_data set status = '1' where id_data = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }
	
}
else if ($_POST['type'] == "Del_Out")
{
	$id = $_POST['id']; 	
	$t1="select * from  t_ware_data_detil where id_data = '$id' ";
	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{  		
		$sql = "update t_ware_data_detil set est_keluar = est_keluar - '$d1[keluar]' where id_detil = '$d1[id_detil_masuk]' ";
		$hasil=mysqli_query($koneksi, $sql);
	}
	$del = mysqli_query($koneksi, "DELETE FROM t_ware_data_detil WHERE id_data = '$id'");		
    $query = "DELETE FROM t_ware_data WHERE id_data = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }
	
}
else if($_GET['type'] == "Read_Out_Data")
{
	$id_data = $_GET['id_data'];
	$mode = $_GET['mode'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="11%" style="text-align: center;">BATCH</th>
					<th rowspan="2" width="11%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="27%" style="text-align: center;">ITEM DESCRIPTION</th>									
					<th rowspan="2" width="5%" style="text-align: center;">AGING<br>RENTAL</th>
					<th rowspan="2" width="5%" style="text-align: center;">LENGTH<br>STAY</th>
					<th rowspan="2" width="5%" style="text-align: center;">QTY</th>	
					<th rowspan="2" width="5%" style="text-align: center;">CBM</th>					
					<th rowspan="2" width="5%" style="text-align: center;">HANDLING<br>COST</th>
					<th rowspan="2" width="7%" style="text-align: center;">TOTAL</th>
					<th rowspan="2" width="12%" style="text-align: center;">REMARK</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>						
				</tr>
			</thead>';	
	$total = 0;		
	$y=50;
	
	$SQL = "select t_ware_data_detil.*, t_ware_data_detil1.no_cont, t_ware_data.tanggal,
			t_ware.nama, t_ware.kode, t_ware.vol, t_ware.unit, 
			t_ware_data1.tanggal as tgl_sj, t_ware_quo.aging_sewa, t_ware_quo.harga_handling
			from 
			t_ware_data_detil inner join t_ware_data_detil as t_ware_data_detil1 on 
			t_ware_data_detil.id_detil_masuk = t_ware_data_detil1.id_detil
			left join t_ware_data on t_ware_data_detil1.id_data = t_ware_data.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware
			left join t_ware_data as t_ware_data1 on t_ware_data_detil.id_data = t_ware_data1.id_data
			left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
			where t_ware_data_detil.id_data = '$id_data'  order by  t_ware_data_detil.id_detil";
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
			
			$ptgl = explode("-", $row['tanggal']);
			$th = $ptgl[0];
			$bl = $ptgl[1];		
			$tg = $ptgl[2];
			$year = substr($th,2,2);
			$batch = "$tg.$bl.$year $row[no_cont]";	
		
			
			$tgl_masuk = strtotime($row['tanggal']);
			$tgl_keluar = strtotime($row['tgl_sj']);
			$aging = $tgl_keluar - $tgl_masuk; 
			$aging = ($aging/24/60/60);
			$aging = round($aging);
			
			
			$vol = $row['keluar'] * $row['vol'];
			$volx = number_format($vol,2);
			
			if($aging > $row['aging_sewa'])
			{
				$harga = $row['harga_handling'];
				$hargax = number_format($harga,0);
			}else{
				$harga = 0;
				$hargax = number_format($harga,0);
			}
			
		
			$jumlah = $harga * $vol;
			$jumlahx = number_format($jumlah,0);
			$total = $total + $jumlah;
		
				
			$data .= '<tr>						
				<td style="text-align:center">'.$posisi.'.</td>
				<td style="text-align:center">'.$batch.'</td>	
				<td style="text-align:center">'.$row['kode'].'</td>
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['aging_sewa'].'</td>
				<td style="text-align:center">'.$aging.'</td>
				<td style="text-align:center">'.$row['keluar'].' '.$row['unit'].'</td>
				<td style="text-align:center">'.$volx.'</td>
				<td style="text-align:right">'.$hargax.'</td>
				<td style="text-align:right">'.$jumlahx.'</td>
				<td style="text-align:center">'.$row['rem'].'</td>';
				
				if($mode == 'Edit'){
					
				
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_detil'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';	
					
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:DelData('.$row['id_detil'].')"  >
								<span class="fa fa-close " ></span>
							</button></td>';
							
				}else{
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
			$data .= '<td colspan="3"></td>';			
			$data .= '</tr>';
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
	
	$sql = "update t_ware_data set tagihan = '$total' where id_data = '$id_data'	";
	$hasil=mysqli_query($koneksi,$sql);
			
    echo $data;		

}
else if ($_POST['type'] == "Add_Out")
{		
	if($_POST['id_data'] != '' )
	{	
		$id_data = $_POST['id_data'];
		$id = $_POST['id'];
		$id_ware = $_POST['id_ware'];
		$id_detil_masuk = $_POST['id_detil_masuk'];
		$qty = $_POST['qty'];
		$qty_lama = $_POST['qty_lama'];
		//$rem = addslashes(trim(strtoupper($_POST['rem'])));
		$rem = addslashes(trim($_POST['rem']));
		$mode = $_POST['mode'];
		
		if($mode == 'Add')
		{
			$sql = "INSERT INTO  t_ware_data_detil (id_data, id_detil_masuk, id_ware, keluar,  rem)	 
					values
					('$id_data', '$id_detil_masuk', '$id_ware', '$qty', '$rem')";
			$hasil=mysqli_query($koneksi, $sql);
			
			$sql = "update t_ware_data_detil set est_keluar = est_keluar + '$qty' where id_detil = '$id_detil_masuk' ";
			$hasil=mysqli_query($koneksi, $sql);
		
		}else{
			
			
			$sql = "update t_ware_data_detil set keluar = '$qty', rem = '$rem' where id_detil = '$id' ";
			$hasil=mysqli_query($koneksi, $sql);
			
			$qty =  $qty_lama - $qty ;
			
			$sql = "update t_ware_data_detil set est_keluar = est_keluar - '$qty' where id_detil = '$id_detil_masuk' ";
			$hasil=mysqli_query($koneksi, $sql);
			
		}
		
			
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Data Error...!";
	    }
		else
		{	
			
			echo "Data saved!";
		}
	}

}
else if ($_POST['type'] == "Detil_Data_Out")
{
	$id = $_POST['id'];	
    $query = "select t_ware_data_detil.*, t_ware_data_detil1.no_cont, t_ware_data_detil1.est_keluar as est, t_ware_data_detil1.masuk as msk, 
			t_ware_data_detil1.id_detil as id_detil_masuk,
			t_ware_data.tanggal,
			t_ware.nama, t_ware.kode, t_ware.vol, 
			t_ware_data1.tanggal as tgl_sj
			from 
			t_ware_data_detil inner join t_ware_data_detil as t_ware_data_detil1 on 
			t_ware_data_detil.id_detil_masuk = t_ware_data_detil1.id_detil
			left join t_ware_data on t_ware_data_detil1.id_data = t_ware_data.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware
			left join t_ware_data as t_ware_data1 on t_ware_data_detil.id_data = t_ware_data1.id_data
			where t_ware_data_detil.id_detil  = '$id'";
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
else if ($_POST['type'] == "Del_Data_Out")
{
	$id = $_POST['id']; 	
	
	$pq = mysqli_query($koneksi, "select * from t_ware_data_detil where id_detil = '$id' ");
	$rq=mysqli_fetch_array($pq);
	$id_detil_masuk = $rq['id_detil_masuk'];
	$qty = $rq['keluar'];
	
	$sql = "update t_ware_data_detil set est_keluar = est_keluar - '$qty' where id_detil = '$id_detil_masuk' ";
	$hasil=mysqli_query($koneksi, $sql);		
	
    $query = "DELETE FROM t_ware_data_detil WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }
	
	
	
}else if($_GET['type'] == "ListBiaya_Quo")
{
	$id_quo = $_GET['id_quo'];
	$mode = $_GET['mode'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="5%" style="text-align: center;">NO</th>	
					<th rowspan="2" width="78%" style="text-align: center;">COST NAME</th>
					<th rowspan="2" width="12%" style="text-align: center;">COST</th>
					<th rowspan="2" width="5%" style="text-align: center;">ADD</th>						
				</tr>
			</thead>';	
	$SQL = "select t_ware_quo_biaya.*, m_cost_tr.nama_cost 
			from 
			t_ware_quo_biaya left join m_cost_tr on t_ware_quo_biaya.id_biaya = m_cost_tr.id_cost
			where t_ware_quo_biaya.id_quo = '$id_quo' order by  t_ware_quo_biaya.id_detil";
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
				<td style="text-align:center">'.$posisi.'.</td>';			
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihBiaya('.$row['id_detil'].')" >'.$row['nama_cost'].'</a></td>';
			$data .= '<td style="text-align:right"><a href="#" onclick="PilihBiaya('.$row['id_detil'].')" >'.$harga.'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihBiaya('.$row['id_detil'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px"><span class="fa  fa-plus-square"></span></button>
					</td>';	
				
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
else if($_GET['type'] == "ListBarang_Quo")
{
	$id_quo = $_GET['id_quo'];
	$cari = $_GET['cari'];
	$filter = $_GET['filter'];
	
	if($filter == 'Item Description')
	{
		$f = 'nama';	
	}else if($filter == 'Item Number'){
		$f = 'kode';
	}else{
		$f = 'nama';	
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="5%" style="text-align: center;">NO</th>	
					<th rowspan="2" width="20%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="70%" style="text-align: center;">DESCRIPTION</th>
					<th rowspan="2" width="5%" style="text-align: center;">ADD</th>						
				</tr>
			</thead>';	
	$SQL = "select * from t_ware where id_quo = '$id_quo' and $f LIKE '%$cari%' order by  nama";
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
				<td style="text-align:center">'.$posisi.'.</td>';			
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihBarang('.$row['id_ware'].')" >'.$row['kode'].'</a></td>';
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihBarang('.$row['id_ware'].')" >'.$row['nama'].'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihBarang('.$row['id_ware'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px"><span class="fa  fa-plus-square"></span></button>
					</td>';	
				
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
else if($_GET['type'] == "Read_Jasa_Biaya")
{
	$id_data = $_GET['id_data'];
	$mode = $_GET['mode'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="43%" style="text-align: center;">SERVICE NAME</th>
					<th rowspan="2" width="30%" style="text-align: center;">REMARK</th>
					<th rowspan="2" width="5%" style="text-align: center;">QTY</th>									
					<th rowspan="2" width="7%" style="text-align: center;">COST</th>
					<th rowspan="2" width="8%" style="text-align: center;">TOTAL</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>						
				</tr>
			</thead>';	
	$total = 0;		
	$y=50;
	
	$SQL = "select  t_ware_jasa_biaya.*, m_cost_tr.nama_cost
			from 
			 t_ware_jasa_biaya inner join t_ware_quo_biaya on t_ware_jasa_biaya.id_biaya = t_ware_quo_biaya.id_detil
			left join m_cost_tr on t_ware_quo_biaya.id_biaya = m_cost_tr.id_cost
			where t_ware_jasa_biaya.id_data = '$id_data'  order by  t_ware_jasa_biaya.id_detil";
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;		
			
			$hargax = number_format($row['harga'],0);
			$jumlah = $row['harga'] * $row['qty'];
			$jumlahx = number_format($jumlah,0);
			$total = $total + $jumlah;
		
				
			$data .= '<tr>						
				<td style="text-align:center">'.$posisi.'.</td>
				<td style="text-align:left">'.$row['nama_cost'].'</td>
				<td style="text-align:left">'.$row['rem'].'</td>
				<td style="text-align:center">'.$row['qty'].' '.$row['unit'].'</td>
				<td style="text-align:right">'.$hargax.'</td>
				<td style="text-align:right">'.$jumlahx.'</td>';
				
				if($mode == 'Edit'){
					
				
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_detil'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';	
					
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:DelData('.$row['id_detil'].')"  >
								<span class="fa fa-close " ></span>
							</button></td>';
							
				}else{
					$data .='<td></td>';					
					$data .='<td></td>';
				}		
						
				$data .='</tr>';
    		$number++;
    	}
		$totalx = number_format($total,0);
			$data .= '<td colspan="3"></td>';
			$data .= '<td colspan="2" style="text-align:right;background:#eee;color:#000"><b>Total  :</b></td>	
						<td style="text-align:right;background:#008d4c;color:#fff"><b>'.$totalx.'</b></td>';
			$data .= '<td colspan="3"></td>';			
			$data .= '</tr>';
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
	
	$sql = "update t_ware_data set tagihan = '$total' where id_data = '$id_data'	";
	$hasil=mysqli_query($koneksi,$sql);
			
    echo $data;			
	
}
else if ($_POST['type'] == "Add_Jasa_Biaya")
{		
	if($_POST['id_data'] != '' )
	{	
		$id_data = $_POST['id_data'];
		$id = $_POST['id'];
		$id_biaya = $_POST['id_biaya'];
		$qty = $_POST['qty'];
		$harga = $_POST['harga'];
		$unit = $_POST['unit'];
		//$rem = addslashes(trim(strtoupper($_POST['rem'])));
		$rem = addslashes(trim($_POST['rem']));
		$mode = $_POST['mode'];
		$harga = str_replace(",","", $harga);
		
		if($mode == 'Add')
		{
			$sql = "INSERT INTO  t_ware_jasa_biaya (id_data, id_biaya, qty, harga,  rem, unit)	 
					values
					('$id_data', '$id_biaya', '$qty', '$harga', '$rem', '$unit')";
			$hasil=mysqli_query($koneksi, $sql);
		
		}else{
			
			
			$sql = "update t_ware_jasa_biaya set 
			id_biaya = '$id_biaya', 
			rem = '$rem', 
			qty = '$qty', 
			harga = '$harga',
			unit = '$unit'
			where id_detil = '$id' ";
			$hasil=mysqli_query($koneksi, $sql);
			
		}
		
			
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Data Error...!";
	    }
		else
		{	
			
			echo "Data saved!";
		}
	}	
	
	
	
}
else if ($_POST['type'] == "Detil_Data_Jasa")
{
	$id = $_POST['id'];	
    $query = "select  t_ware_jasa_biaya.*, m_cost_tr.nama_cost
			from 
			 t_ware_jasa_biaya inner join t_ware_quo_biaya on t_ware_jasa_biaya.id_biaya = t_ware_quo_biaya.id_detil
			left join m_cost_tr on t_ware_quo_biaya.id_biaya = m_cost_tr.id_cost
			where t_ware_jasa_biaya.id_detil  = '$id'";
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
else if ($_POST['type'] == "Del_Data_Jasa")
{
	$id = $_POST['id']; 	
	
    $query = "DELETE FROM  t_ware_jasa_biaya WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }
	

}
else if($_GET['type'] == "Read_Jasa_Barang")
{
	$id_data = $_GET['id_data'];
	$mode = $_GET['mode'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="12%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="55%" style="text-align: center;">ITEM DESCRIPTION</th>
					<th rowspan="2" width="6%" style="text-align: center;">QTY</th>									
					<th rowspan="2" width="20%" style="text-align: center;">REMARKS</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>						
				</tr>
			</thead>';	
	$total = 0;		
	$y=50;
	
	$SQL = "select  t_ware_jasa_barang.*, t_ware.nama, t_ware.kode, t_ware.unit
			from 
			  t_ware_jasa_barang inner join t_ware on  t_ware_jasa_barang.id_ware = t_ware.id_ware
			where  t_ware_jasa_barang.id_data = '$id_data'  order by   t_ware_jasa_barang.id_detil";
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
				<td style="text-align:center">'.$row['kode'].'</td>
				<td style="text-align:left">'.$row['nama'].'</td>
				<td style="text-align:center">'.$row['qty'].' '.$row['unit'].'</td>
				<td style="text-align:left">'.$row['rem'].'</td>';
				
				if($mode == 'Edit'){
					
				
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_detil'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';	
					
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:DelData('.$row['id_detil'].')"  >
								<span class="fa fa-close " ></span>
							</button></td>';
							
				}else{
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
	
	$sql = "update t_ware_data set tagihan = '$total' where id_data = '$id_data'	";
	$hasil=mysqli_query($koneksi,$sql);
			
    echo $data;			
	
}
else if ($_POST['type'] == "Add_Jasa_Barang")
{		
	if($_POST['id_data'] != '' )
	{	
		$id_data = $_POST['id_data'];
		$id = $_POST['id'];
		$id_ware = $_POST['id_ware'];
		$qty = $_POST['qty'];
		$rem = addslashes(trim($_POST['rem']));
		$mode = $_POST['mode'];
		$harga = str_replace(",","", $harga);
		
		if($mode == 'Add')
		{
			$sql = "INSERT INTO  t_ware_jasa_barang (id_data, id_ware, qty,  rem)	 
					values
					('$id_data', '$id_ware', '$qty',  '$rem')";
			$hasil=mysqli_query($koneksi, $sql);
		
		}else{
			
			
			$sql = "update t_ware_jasa_barang set 
			id_ware = '$id_ware', 
			rem = '$rem', 
			qty = '$qty'
			where id_detil = '$id' ";
			$hasil=mysqli_query($koneksi, $sql);
			
		}
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Data Error...!";
	    }
		else
		{	
			
			echo "Data saved!";
		}
	}	
	
}
else if ($_POST['type'] == "Detil_Jasa_Barang")
{
	$id = $_POST['id'];	
    $query = "select  t_ware_jasa_barang.*, t_ware.nama, t_ware.kode, t_ware.unit
			from 
			  t_ware_jasa_barang inner join t_ware on  t_ware_jasa_barang.id_ware = t_ware.id_ware
			where t_ware_jasa_barang.id_detil  = '$id'";
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
else if ($_GET['type'] == "Read_Sewa")
{
	$cari = trim($_GET['cari']);
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	$tgl1 = $_GET['tgl1'];
	$tgl2 = $_GET['tgl2'];
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);	
	$field = $_GET['field'];
	$cari = trim($_GET['cari']);
	$field1 = $_GET['field1'];
	$cari1 = trim($_GET['cari1']);
	
	$stat = trim($_GET['stat']);
	if($stat == 'In Progress')
	{
		$stat = '0';
	}
	else if($stat == 'Executed')
	{
		$stat = '1';
	}
	
	if($field == 'No SO')
	{
		$f = 't_ware_sewa.no_sewa';	
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';		
	}else if($field == 'Jenis Sewa'){
		$f = 'm_cost_tr.nama_cost';		
	}else{
		$f = 't_ware_sewa.no_sewa';	
	}
	
	if($field1 == 'No SO')
	{
		$f1 = 't_ware_sewa.no_sewa';	
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';		
	}else if($field1 == 'Jenis Sewa'){
		$f1 = 'm_cost_tr.nama_cost';		
	}else{
		$f1 = 't_ware_sewa.no_sewa';	
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. SO</th>
					<th rowspan="2" width="40%" style="text-align: center;">CUSTOMER</th>
					<th rowspan="2" width="14%" style="text-align: center;">RENTAL TYPE</th>
					<th rowspan="2" width="7%" style="text-align: center;">BILL</th>
					<th rowspan="2" width="7%" style="text-align: center;">CREATED</th>	
					<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>					
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>					
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>
					<th rowspan="2" width="2%" style="text-align: center;">PRINT</th>
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
	
	if($stat == 'All')
	{
		
		$SQL = "select t_ware_sewa.*, m_cust_tr.nama_cust, m_cost_tr.nama_cost
		  from 
		  t_ware_sewa left join m_cust_tr on t_ware_sewa.id_cust = m_cust_tr.id_cust
		  left join m_cost_tr on t_ware_sewa.id_cost = m_cost_tr.id_cost
		  where t_ware_sewa.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and $f1 LIKE '%$cari1%'
		order  by t_ware_sewa.tanggal desc LIMIT $offset, $jmlperhalaman";
	}else{
		$SQL = "select t_ware_sewa.*, m_cust_tr.nama_cust, m_cost_tr.nama_cost
		  from 
		  t_ware_sewa left join m_cust_tr on t_ware_sewa.id_cust = m_cust_tr.id_cust
		  left join m_cost_tr on t_ware_sewa.id_cost = m_cost_tr.id_cost
		  where t_ware_sewa.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and $f1 LIKE '%$cari1%' and t_ware_sewa.status = '$stat'
		order  by t_ware_sewa.tanggal desc LIMIT $offset, $jmlperhalaman";
	
	
	}		

		
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
			$tagihan = number_format($row['tagihan'],0);
			$xy1="View|$row[id_data]";
			$xy1=base64_encode($xy1);
			$link = "ware_sewa_data.php?id=$xy1";
			if($row['status'] == '0')
			{
				$label = 'danger';
				$status = 'Open';
			}
			else if($row['status'] == '1')
			{
				$label = 'success';
				$status = 'Close';
			}
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>	
				
				<td style="text-align:center"><a href="'.$link.'" title="">'.$row['no_sewa'].'</a></td>
				<td style="text-align:left">'.$row['nama_cust'].'</td>
				<td style="text-align:center">'.$row['nama_cost'].'</td>
				<td style="text-align:right">'.$tagihan.'</td>
				<td style="text-align:center">'.$row['created'].'</td>
				<td style="text-align:center">
						<button type="button" class="btn btn-'.$label.'"  
						style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
					</td>';				
				if($m_edit == '1' && $row['status'] == '0' ){
					$xy1="Edit|$row[id_sewa]";
					$xy1=base64_encode($xy1);
					$link = "'ware_sewa_data.php?id=$xy1'";
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="window.location.href = '.$link.' "  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				if($m_del == '1' && $row['status'] == '0') 	
				{
					$data .= '<td>
							<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:Delete('.$row['id_sewa'].')"  >
								<span class="fa fa-close " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				
				$xy1="$row[id_sewa]";
					$xy1=base64_encode($xy1);
					$link = "'cetak_sewa_ware.php?id=$xy1'";
					$data .= '<td>
						<button class="btn btn-block btn-default"  title="Print"
							style="margin:-3px;border-radius:0px" type="button" 									
							onClick="window.open('.$link.') ">
							<span class="fa fa-print " ></span>
						</button></td>';
						
				if($row['status'] == '0'  ) {
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Execute"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:Executed('.$row['id_sewa'].')"  >
									<span class="fa fa-check-square-o " ></span>
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
				if($stat == 'All')
				{
					$pq = mysqli_query($koneksi, "select count(t_ware_sewa.id_sewa) as jml
					  from 
					  t_ware_sewa left join m_cust_tr on t_ware_sewa.id_cust = m_cust_tr.id_cust
					  left join m_cost_tr on t_ware_sewa.id_cost = m_cost_tr.id_cost
					  where t_ware_sewa.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and $f1 LIKE '%$cari1%'  ");
				}else{
					$pq = mysqli_query($koneksi, "select count(t_ware_sewa.id_sewa) as jml
					  from 
					  t_ware_sewa left join m_cust_tr on t_ware_sewa.id_cust = m_cust_tr.id_cust
					  left join m_cost_tr on t_ware_sewa.id_cost = m_cost_tr.id_cost
					  where t_ware_sewa.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and $f1 LIKE '%$cari1%' and t_ware_sewa.status = '$stat' ");
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

}else if ($_POST['type'] == "Del_Sewa"){
	$id = $_POST['id']; 	
	
	
    $query = "DELETE FROM t_ware_sewa WHERE id_sewa = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }
	
}
else if($_GET['type'] == "Read_Sewa_Data")
{
	$id_sewa = $_GET['id_sewa'];
	$id_quo = $_GET['id_quo'];
	$mode = $_GET['mode'];
	$pq = mysqli_query($koneksi, "select t_ware_sewa.*, m_cust_tr.nama_cust
		  from 
		  t_ware_sewa left join m_cust_tr on t_ware_sewa.id_cust = m_cust_tr.id_cust
		  where t_ware_sewa.id_sewa = '$id_sewa'  ");
	$rq=mysqli_fetch_array($pq);	
	$no_sewa = $rq['no_sewa'];
	$bln = $rq['bln'];
	$thn = $rq['thn'];
	$tgl_keluar = "$thn-$bln-01";
	$tgl_keluar = strtotime($rq['tanggal']);
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="12%" style="text-align: center;">BATCH</th>
					<th rowspan="2" width="10%" style="text-align: center;">ITEM NUMBER</th>
					<th rowspan="2" width="52%" style="text-align: center;">ITEM DESCRIPTION</th>
					<th rowspan="2" width="5%" style="text-align: center;">AGING</th>
					<th rowspan="2" width="10%" style="text-align: center;">QTY</th>
					<th rowspan="2" width="8%" style="text-align: center;">CBM</th>			
				</tr>
			</thead>';	
	$total = 0;		

	$SQL = "select t_ware_data.*, t_ware_data_detil.id_detil, t_ware_data_detil.no_cont, t_ware_data_detil.masuk, t_ware_data_detil.keluar, 
			t_ware.nama, t_ware.kode, t_ware.id_quo, t_ware.vol, t_ware.unit, m_cust_tr.nama_cust, m_lokasi_ware.nama as nama_lokasi,
			t_ware_quo.max_cbm, t_ware_quo.harga_sewa, t_ware_quo.aging_sewa
			from  
			t_ware_data left join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
			left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
			left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
			left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
			where t_ware.id_quo = '$id_quo'  and t_ware_data.jenis = '0'
			and t_ware_data.status = '1' 
			order by  t_ware_data.id_data";
			
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;		
			
			$tgl_masuk = strtotime($row['tanggal']);
			
			$aging = $tgl_keluar - $tgl_masuk; 
			$aging = ($aging/24/60/60);
			$aging = round($aging);
	
	
			$ptgl = explode("-", $row['tanggal']);
			$th = $ptgl[0];
			$bl = $ptgl[1];		
			$tg = $ptgl[2];
			$year = substr($th,2,2);
			$batch = "$tg.$bl.$year $row[no_cont]";	
			
			$max_cbm = $row['max_cbm'];
			$harga_sewa = $row['harga_sewa'];
			$max_cbmx = number_format($row['max_cbm'],2);
			$harga_sewax = number_format($row['harga_sewa'],0);
			
			if($aging > $row['aging_sewa'])
			{
				$sisa  = $row['masuk'] - $row['keluar'];
			}else{
				$sisa = 0;
			}
			
			$cbm = $sisa * $row['vol'];	
			$cbmx = number_format($cbm,2);
			$total = $total + $cbm;
			$data .= '<tr>						
			<td style="text-align:center">'.$posisi.'.</td>
			<td style="text-align:center">'.$batch.'</td>
			<td style="text-align:center">'.$row['kode'].'</td>
			<td style="text-align:left">'.$row['nama'].'</td>
			<td style="text-align:center">'.$aging.'</td>
			<td style="text-align:center">'.$sisa.' '.$row['unit'].'</td>
			<td style="text-align:right">'.$cbmx.'</td>';
				
			$data .='</tr>';
			
	
			
    		$number++;
    	}
		$totalx = number_format($total,2);
		$data .= '<tr><td colspan="5"></td>';
		$data .= '<td style="text-align:right;background:#eee;color:#000"><b>Total CBM :</b></td>	
				  <td style="text-align:right;background:#008d4c;color:#fff"><b>'.$totalx.'</b></td>';
		$data .= '</tr>';
		
		$data .= '<tr><td colspan="5"></td>';
		$data .= '<td style="text-align:right;background:#eee;color:#000"><b>Max CBM :</b></td>	
				  <td style="text-align:right;background:#008d4c;color:#fff"><b>'.$max_cbmx.'</b></td>';
		$data .= '</tr>';
		
		$sisa_cbm =  $total - $max_cbm;
		if($sisa_cbm < 0)
		{
			$sisa_cbm = 0;
		}
		$sisa_cbmx = number_format($sisa_cbm,2);
		$data .= '<tr><td colspan="5"></td>';
		$data .= '<td style="text-align:right;background:#eee;color:#000"><b>Tagihan CBM :</b></td>	
				  <td style="text-align:right;background:#008d4c;color:#fff"><b>'.$sisa_cbmx.'</b></td>';
		$data .= '</tr>';
		
		$data .= '<tr><td colspan="5"></td>';
		$data .= '<td style="text-align:right;background:#eee;color:#000"><b>Harga Sewa :</b></td>	
				  <td style="text-align:right;background:#008d4c;color:#fff"><b>'.$harga_sewax.'</b></td>';
		$data .= '</tr>';
		
		$tagihan =  $harga_sewa * $sisa_cbm;
		$tagihanx = number_format($tagihan,0);
		$data .= '<tr><td colspan="5"></td>';
		$data .= '<td style="text-align:right;background:#eee;color:#000"><b>Jumlah Tagihan :</b></td>	
				  <td style="text-align:right;background:#008d4c;color:#fff"><b>'.$tagihanx.'</b></td>';
		$data .= '</tr>';
    }
    else
    {
    	$data .= '<tr><td colspan="6">Records not found!</td></tr>';
    }
	
	$sql = "update t_ware_sewa set tagihan = '$tagihan' where id_sewa = '$id_sewa'	";
	$hasil=mysqli_query($koneksi,$sql);
			
    echo $data;		

}else if ($_POST['type'] == "Executed_Sewa"){
	$id = $_POST['id']; 
	
	
	$query = "update t_ware_sewa set status = '1' where id_sewa = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
    }
	
}

?>