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
		$f = 't_jo_tr.no_jo';	
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';	
	}else if($field == 'Asal'){
		$f = 'm_kota_tr.nama_kota';
	}else if($field == 'Tujuan'){
		$f = 'm_kota1.nama_kota';	
	}else if($field == 'No SJ'){
		$f = 't_jo_sj_tr.no_sj';		
	}else if($field == 'Nama Barang'){
		$f = 't_jo_tr.nama_barang';	
	}else{
		$f = 't_jo_tr.no_jo';
	}
	
	if($field1 == 'No Order')
	{
		$f1 = 't_jo_tr.no_jo';	
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';	
	}else if($field1 == 'Asal'){
		$f1 = 'm_kota_tr.nama_kota';
	}else if($field1 == 'Tujuan'){
		$f1 = 'm_kota1.nama_kota';	
	}else if($field1 == 'No SJ'){
		$f1 = 't_jo_sj_tr.no_sj';
	}else if($field1 == 'Nama Barang'){
		$f1 = 't_jo_tr.nama_barang';	
	}else{
		$f1 = 't_jo_tr.no_jo';
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="6%" style="text-align: center;">TANGGAL</th>	
					<th rowspan="2" width="6%" style="text-align: center;">#NO<br>ORDER</th>
					<th rowspan="2" width="19%" style="text-align: center;">CUSTOMER</th>
					<th rowspan="2" width="7%" style="text-align: center;">ASAL</th>
					<th rowspan="2" width="7%" style="text-align: center;">TUJUAN</th>
					<th rowspan="2" width="9%" style="text-align: center;">NAMA BARANG</th>
					<th rowspan="2" width="5%" style="text-align: center;">BERAT<br>(Kg)</th>
					<th rowspan="2" width="5%" style="text-align: center;">VOL<br>(M3)</th>
					<th rowspan="2" width="6%" style="text-align: center;">BIAYA</th>
					<th rowspan="2" width="6%" style="text-align: center;">NO. SJ</th>
					<th rowspan="2" width="6%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>
					<th colspan="2" width="4%" style="text-align: center;">ACTION</th>	
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
	
	if($id_role == '2' || $id_role == '10')
	{
		$sales = $id_user;
	}
	
	if($stat == 'All')
	{
		$SQL = "select t_jo_tr.*, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal,
				m_kota1.nama_kota as tujuan, t_jo_sj_tr.no_sj
			  from 
			  t_jo_tr inner join m_cust_tr on  t_jo_tr.id_cust = m_cust_tr.id_cust
			  left join m_kota_tr on t_jo_tr.id_asal = m_kota_tr.id_kota
			  left join m_kota_tr as m_kota1 on t_jo_tr.id_tujuan = m_kota1.id_kota
			  left join t_jo_sj_tr on t_jo_tr.id_sj = t_jo_sj_tr.id_sj
		    where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%'  and t_jo_tr.tipe = 'LCL'
		  order by t_jo_tr.tgl_jo desc, t_jo_tr.no_jo desc
		  LIMIT $offset, $jmlperhalaman";
			  
	}else{
			
		$SQL = "select t_jo_tr.*, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal,
				m_kota1.nama_kota as tujuan, t_jo_sj_tr.no_sj
			  from 
			  t_jo_tr inner join m_cust_tr on  t_jo_tr.id_cust = m_cust_tr.id_cust
			  left join m_kota_tr on t_jo_tr.id_asal = m_kota_tr.id_kota
			  left join m_kota_tr as m_kota1 on t_jo_tr.id_tujuan = m_kota1.id_kota
			  left join t_jo_sj_tr on t_jo_tr.id_sj = t_jo_sj_tr.id_sj
		   where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%'  and t_jo_tr.tipe = 'LCL'
		and t_jo_tr.status = '$stat'
		 order by t_jo_tr.tgl_jo desc, t_jo_tr.no_jo desc
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
			$berat = number_format($row['berat'],2);
			$vol = number_format($row['vol'],2);
			$tagihan = number_format($row['tagihan'],0);
			$posisi++;
			$xy1="View|$row[id_jo]";
			$xy1=base64_encode($xy1);
			$link = "lcl_data.php?id=$xy1";
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
			$xy1="$row[id_sj]";
			$xy1=base64_encode($xy1);
			$link_sj = "cetak_sj_lcl.php?id=$xy1";
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>
				<td style="text-align:center"><a href="'.$link.'"  title="View">'.$row['no_jo'].'</a></td>
				<td style="text-align:left">'.$row['nama_cust'].'</b></td>				
				<td style="text-align:center">'.$row['asal'].'</td>
				<td style="text-align:center">'.$row['tujuan'].'</td>
				<td style="text-align:center">'.$row['nama_barang'].'</td>
				<td style="text-align:center">'.$berat.'</td>
				<td style="text-align:center">'.$vol.'</td>		
				<td style="text-align:right">'.$tagihan.'</td>		
				<td style="text-align:center"><a href="'.$link_sj.'"  target = "_blank">'.$row['no_sj'].'</a></td>				
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
									onClick="window.location.href = '.$link.' "  >
									<span class="fa fa-edit " ></span>
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
					$pq = mysqli_query($koneksi, "select count(t_jo_tr.id_jo) as jml
				   from 
				  t_jo_tr inner join m_cust_tr on  t_jo_tr.id_cust = m_cust_tr.id_cust
			  left join m_kota_tr on t_jo_tr.id_asal = m_kota_tr.id_kota
			  left join m_kota_tr as m_kota1 on t_jo_tr.id_tujuan = m_kota1.id_kota
			  left join t_jo_sj_tr on t_jo_tr.id_sj = t_jo_sj_tr.id_sj
				   where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%'  and t_jo_tr.tipe = 'LCL' ");
				}else{
					$pq = mysqli_query($koneksi, "select count(t_jo_tr.id_jo) as jml
				   from 
				   t_jo_tr inner join m_cust_tr on  t_jo_tr.id_cust = m_cust_tr.id_cust
			  left join m_kota_tr on t_jo_tr.id_asal = m_kota_tr.id_kota
			  left join m_kota_tr as m_kota1 on t_jo_tr.id_tujuan = m_kota1.id_kota
			  left join t_jo_sj_tr on t_jo_tr.id_sj = t_jo_sj_tr.id_sj
				   where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%'  and t_jo_tr.tipe = 'LCL'
					and t_jo_tr.status = '$stat'");
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
	
	$delete = mysqli_query($koneksi, "delete from t_jo_biaya_tr where id_jo = '$id' ");
    $query = "DELETE FROM t_jo_tr WHERE id_jo = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	

}
else if($_GET['type'] == "Read_Biaya")
{
	$id_jo = $_GET['id_jo'];
	$mode = $_GET['mode'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="67%" style="text-align: center;">KETERANGAN</th>
					<th rowspan="2" width="5%" style="text-align: center;">QTY</th>
					<th rowspan="2" width="7%" style="text-align: center;">UNIT</th>
					<th rowspan="2" width="7%" style="text-align: center;">HARGA</th>
					<th rowspan="2" width="7%" style="text-align: center;">JUMLAH</th>
					<th colspan="2" width="4%" style="text-align: center;">ACTION</th>						
				</tr>
				<tr>
					<th width="2%" style="text-align: center;">EDIT</th>
					<th width="2%" style="text-align: center;">DEL</th>
				</tr>	
			</thead>';	
	$total = 0;		
	$SQL = "select t_jo_biaya_tr.*, m_cost_tr.nama_cost 
			from t_jo_biaya_tr inner join m_cost_tr on t_jo_biaya_tr.id_cost = m_cost_tr.id_cost
			where t_jo_biaya_tr.id_jo = '$id_jo'  order by  t_jo_biaya_tr.id_detil";
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			$jumlah = $row['harga'] * $row['qty'];			
			$harga = number_format($row['harga'],0);	
			$jumlahx = number_format($jumlah,0);		
			$total = $total + $jumlah;		
			$data .= '<tr>						
				<td style="text-align:center">'.$posisi.'.</td>
				<td style="text-align:left">'.$row['nama_cost'].'</td>	
				<td style="text-align:center">'.$row['qty'].'</td>
				<td style="text-align:center">'.$row['unit'].'</td>
				<td style="text-align:right">'.$harga.'</td>
				<td style="text-align:right">'.$jumlahx.'</td>';
				
				if($mode == 'Edit' && $row['kunci'] != '1' ){
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Edit"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:GetBiaya('.$row['id_detil'].')"  >
								<span class="fa fa-edit " ></span>
							</button></td>';
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:DelBiaya('.$row['id_detil'].')"  >
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
		$data .= '<tr>						
				<td colspan = "4" style="text-align:center; background:#eee"></td>
				<td style="text-align:right; background:#ddd"><b>TOTAL :</b></td>
				<td style="text-align:right;background:#00a65a; color:#fff "><b>'.$totalx.'</b></td>';
				$data .='</tr>';
		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
	
	$sql = "update t_jo_tr set tagihan = '$total' where id_jo = '$id_jo'	";
	$hasil=mysqli_query($koneksi, $sql);
			
    echo $data;		

}else if ($_POST['type'] == "Add_Biaya"){		
	if($_POST['mode'] != '' )
	{	
		$id_jo = $_POST['id_jo'];
		$id_biaya = $_POST['id_biaya'];
		$id_cost = $_POST['id_cost'];
		$qty = $_POST['qty'];
		$unit = $_POST['unit'];
		$harga = $_POST['harga'];
		$mode = $_POST['mode'];
		$harga = str_replace(",","", $harga);
		$qty = str_replace(",","", $qty);
		
		if($mode == 'Add')
		{
			$sql = "INSERT INTO t_jo_biaya_tr (id_jo, id_cost, qty, unit, harga) values
					('$id_jo','$id_cost','$qty','$unit','$harga')";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update t_jo_biaya_tr set 
					id_cost = '$id_cost',
					qty = '$qty',
					unit= '$unit',
					harga = '$harga'
					where id_detil = '$id_biaya'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        			
			exit(mysql_error());
			echo "Data Error...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}

}else if ($_POST['type'] == "Detil_Biaya"){
	$id = $_POST['id'];	
    $query = "select * from t_jo_biaya_tr where id_detil  = '$id'";
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
	
}else if ($_POST['type'] == "Del_Biaya"){
	$id = $_POST['id']; 
    $query = "DELETE FROM  t_jo_biaya_tr WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	
	
}

?>