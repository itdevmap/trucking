<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses where id_role = '$id_role'  and id_menu ='22' ");
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
	
	if($field == 'No PR')
	{
		$f = 't_jo_bc.no_bc';
	}else if($field == 'JOB No'){
		$f = 't_jo.jo_no';			
	}else if($field == 'Customer'){
		$f = 'm_cust.nama_cust';
	}else if($field == 'No Container'){
		$f = 't_jo_cont.no_cont';	
	}else if($field == 'Tujuan'){
		$f = 'm_kota_tr.nama_kota';		
	}else{
		$f = 't_jo.jo_no';
	}
	
	if($stat == 'Request')
	{
		$stat = '0';
	}
	else if($stat == 'Approved')
	{
		$stat = '1';
	}
	else if($stat == 'Reject')
	{
		$stat = '2';
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO.</th>
					<th rowspan="2" width="7%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="7%" style="text-align: center;">NO PR</th>	
					<th rowspan="2" width="7%" style="text-align: center;">JOB NO</th>						
					<th rowspan="2" width="28%" style="text-align: center;">CUSTOMER</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. CONTAINER</th>
					<th rowspan="2" width="4%" style="text-align: center;">FEET</th>
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL<br>PENGAMBILAN</th>
					<th rowspan="2" width="10%" style="text-align: center;">TUJUAN</th>
					<th rowspan="2" width="3%" style="text-align: center;">DOC</th>
					<th rowspan="2" width="6%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>	
					<th rowspan="2" width="2%" style="text-align: center;">ADD<br>ORDER</th>	
					<th rowspan="2" width="2%" style="text-align: center;">REJECT</th>				
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
		$SQL = "select t_jo_bc.*, t_jo_cont.no_cont, t_jo_cont.feet, t_jo.jo_no, m_cust.nama_cust, m_kota_tr.nama_kota
			  from 
			  t_jo_bc inner join t_jo_cont on t_jo_bc.id_jo_cont = t_jo_cont.id_cont
			  inner join t_jo on t_jo_cont.id_jo = t_jo.id_jo
			  left join m_cust on t_jo.id_cust = m_cust.id_cust
			  left join m_kota_tr on t_jo_bc.id_kota = m_kota_tr.id_kota
			where t_jo_bc.tgl_bc between '$tgl1x' and '$tgl2x' and	$f LIKE '%$cari%' 
			order by t_jo_bc.no_bc desc
			LIMIT $offset, $jmlperhalaman";
	}
	else
	{
		$SQL = "select t_jo_bc.*, t_jo_cont.no_cont, t_jo_cont.feet, t_jo.jo_no, m_cust.nama_cust, m_kota_tr.nama_kota
			  from 
			  t_jo_bc inner join t_jo_cont on t_jo_bc.id_jo_cont = t_jo_cont.id_cont
			  inner join t_jo on t_jo_cont.id_jo = t_jo.id_jo
			  left join m_cust on t_jo.id_cust = m_cust.id_cust
			  left join m_kota_tr on t_jo_bc.id_kota = m_kota_tr.id_kota
			where t_jo_bc.tgl_bc between '$tgl1x' and '$tgl2x' and	$f LIKE '%$cari%' and t_jo_bc.status = '$stat'
			order by t_jo_bc.no_bc desc
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
			$tanggal = ConverTgl($row['tgl_bc']);
			$tgl_ambil = ConverTgl($row['tgl_ambil']);
			if($row['status'] == 0 )
			{
				$label = 'warning';
				$status = 'Request';
				
			}
			else if($row['status'] == 1 )
			{
				$label = 'success';
				$status = 'Approved';
			}
			else if($row['status'] == 2 )
			{
				$label = 'danger';
				$status = 'Reject';
			}
				
			$posisi++;
			$xy1="View|$row[id_bc]";
			$xy1=base64_encode($xy1);
			$link_bl = "bc_data.php?id=$xy1";
			$created = strtoupper($row['created_bc']);
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>
				<td style="text-align:center"><a href="'.$link_bl.'"  title="View JO">'.$row['no_bc'].'</a></td>
				<td style="text-align:center">'.$row['jo_no'].'</td>
				<td style="text-align:left">'.$row['nama_cust'].'</td>
				<td style="text-align:center">'.$row['no_cont'].'</td>
				<td style="text-align:center">'.$row['feet'].'</td>
				<td style="text-align:center">'.$tgl_ambil.'</td>
				<td style="text-align:center">'.$row['nama_kota'].'</td>
				<td>
					<button class="btn btn-block btn-default" title="Document"
						style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
						onClick="javascript:ListDoc('.$row['id_bc'].')"  >
						<span class="fa fa-file-text-o " ></span>
					</button></td>
				<td style="text-align:center">'.$row['created'].'</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';
				
				if($m_edit == '1'  && $row['status'] == '0' ){
				
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Add Order"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetOrder('.$row['id_bc'].')"  >
									<span class="fa  fa-plus-square" ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				if($m_del == '1' && $row['status'] == '0' ) 	
				{
					$data .= '<td>
							<button class="btn btn-block btn-default"  title="Reject"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:GetData('.$row['id_bc'].')"  >
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
				
				if($stat == 'All')
				{
					$pq = mysqli_query($koneksi, "select count(t_jo_bc.id_bc) as jml
					  from 
					  t_jo_bc inner join t_jo_cont on t_jo_bc.id_jo_cont = t_jo_cont.id_cont
					  inner join t_jo on t_jo_cont.id_jo = t_jo.id_jo
					  left join m_cust on t_jo.id_cust = m_cust.id_cust
					  left join m_kota_tr on t_jo_bc.id_kota = m_kota_tr.id_kota
					where t_jo_bc.tgl_bc between '$tgl1x' and '$tgl2x' and	$f LIKE '%$cari%' ");
				}
				else
				{
					$pq = mysqli_query($koneksi, "select count(t_jo_bc.id_bc) as jml
					  from 
					  t_jo_bc inner join t_jo_cont on t_jo_bc.id_jo_cont = t_jo_cont.id_cont
					  inner join t_jo on t_jo_cont.id_jo = t_jo.id_jo
					  left join m_cust on t_jo.id_cust = m_cust.id_cust
					  left join m_kota_tr on t_jo_bc.id_kota = m_kota_tr.id_kota
					where t_jo_bc.tgl_bc between '$tgl1x' and '$tgl2x' and	$f LIKE '%$cari%' and t_jo_bc.status = '$stat'");		
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

}else if ($_POST['type'] == "Detil_BC"){
	$id = $_POST['id'];	
    $query = "select t_jo_bc.*, t_jo_cont.no_cont, t_jo_cont.feet, t_jo.jo_no, t_jo.etd, t_jo.eta, m_cust.nama_cust, m_kota_tr.nama_kota
			  from 
			  t_jo_bc inner join t_jo_cont on t_jo_bc.id_jo_cont = t_jo_cont.id_cont
			  inner join t_jo on t_jo_cont.id_jo = t_jo.id_jo
			  left join m_cust on t_jo.id_cust = m_cust.id_cust
			  left join m_kota_tr on t_jo_bc.id_kota = m_kota_tr.id_kota
			  where t_jo_bc.id_bc  = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
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

}else if ($_POST['type'] == "Add_Batal"){		
	if($_POST['id_bc'] != '' )
	{	
		$id_bc = $_POST['id_bc'];
		$ket_batal = addslashes($_POST['ket_batal']);
		
		$tanggal = date('Y-m-d');
		
		$sql = "update t_jo_bc set 
					status = '2',
					ket_status = '$ket_batal',
					tgl_status = '$tanggal',
					created_status = '$id_user'
					where id_bc = '$id_bc'	";
			$hasil=mysqli_query($koneksi,$sql);
			
		if (!$hasil) {
	       
			echo "Data Error...!";
	    }
		else
		{	
			echo "Data saved! ";
		}
	}			
	
}else if ($_POST['type'] == "Add_Order"){		
	if($_POST['id'] != '' )
	{	
		$id = $_POST['id'];
		$jenis_mobil = $_POST['jenis_mobil'];
		$biaya_kirim = $_POST['biaya_kirim'];
		$biaya_kirim = str_replace(",","", $biaya_kirim);
		
		$pq = mysqli_query($koneksi,"select t_jo_bc.*, t_jo_cont.no_cont, t_jo_cont.feet, t_jo.jo_no, m_cust.nama_cust, m_kota_tr.nama_kota
			  from 
			  t_jo_bc inner join t_jo_cont on t_jo_bc.id_jo_cont = t_jo_cont.id_cont
			  inner join t_jo on t_jo_cont.id_jo = t_jo.id_jo
			  left join m_cust on t_jo.id_cust = m_cust.id_cust
			  left join m_kota_tr on t_jo_bc.id_kota = m_kota_tr.id_kota
			  where t_jo_bc.id_bc  = '$id' ");
		$rq=mysqli_fetch_array($pq);
		
		$id_bc = $rq['id_bc'];
		$tgl_bc = $rq['tgl_bc'];
		$no_bc = $rq['no_bc'];
		$id_asal = '4';
		$id_tujuan = $rq['id_kota'];
		$no_cont = $rq['no_cont'];
		$penerima = $rq['nama_cust'];
		
		$ptgl = explode("-", $tgl_bc);
		$tg = $ptgl[2];
		$bl = $ptgl[1];
		$th = $ptgl[0];	
		$query = "SELECT max(right(no_jo,5)) as maxID FROM t_jo_tr where  year(tgl_jo) = '$th' and tipe = 'FCL' ";
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
		$no_jo = "FCL-$year$noUrut";
		
		$sql = "INSERT INTO  t_jo_tr (tipe, no_jo, tgl_jo, id_cust, no_po, ket, created) values
				('FCL', '$no_jo', '$tgl_bc', '1', '$no_bc', '$ket', '$id_user')";
			$hasil= mysqli_query($koneksi, $sql);
		
		$sql = mysqli_query($koneksi, "select max(id_jo)as id from t_jo_tr ");			
		$row = mysqli_fetch_array($sql);
		$id_jo = $row['id'];
		
		$sql = "INSERT INTO t_jo_detil_tr (id_jo, id_asal, id_tujuan, jenis_mobil, penerima, biaya_kirim, no_cont, id_bc) values
					('$id_jo', '$id_asal', '$id_tujuan', '$jenis_mobil', '$penerima', '$biaya_kirim', '$no_cont', '$id_bc')";
		$hasil= mysqli_query($koneksi, $sql);
		
		$tanggal = date('Y-m-d');	
		$sql = "update t_jo_bc set status = '1', tgl_status = '$tanggal', created_status = '$id_user' where id_bc = '$id_bc'	";
		$hasil=mysqli_query($koneksi,$sql);
			
		if (!$hasil) {
	       
			echo "Data Error...!";
	    }
		else
		{	
			echo "Data saved! ";
		}
	}		


}else if($_GET['type'] == "ListDoc")
{
	$id = $_GET['id'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="6%" style="text-align: center;">NO</th>
					<th rowspan="2" width="89%" style="text-align: center;">KETERANGAN</th>
					<th rowspan="2" width="5%" style="text-align: center;">DOC</th>			
				</tr>
			</thead>';
	$t1="select * from t_jo_bc_doc where id_bc  = '$id' order by id_doc";
	$h1=mysqli_query($koneksi, $t1);   
	while ($d1=mysqli_fetch_array($h1))		
	{
		$n++;
		$data .= '<tr>							
			<td style="text-align:center">'.$n.'.</td>
			<td style="text-align:left">'.$d1['ket'].'</td>';
			
			if($d1['doc'] == '')
			{
				$data .='<td></td>';
			}else{
				$photo = strtolower($d1['doc']);
				$photo = "../fw/$photo";
				$link = "'$photo'";
				$data .= '<td>
							<button class="btn btn-block btn-default" 
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="window.open('.$link.') "   >
								<span class="fa fa-file-text " ></span>
							</button></td>';
			}	
		
		$data .='</tr>';
	}
	
    $data .= '</table>';
    echo $data;		
	
}

?>