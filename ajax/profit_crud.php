<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses where id_role = '$id_role'  and id_menu ='49' ");
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
	
	if($stat == 'In Progress')
	{
		$stat = '0';
	}else if($stat == 'Completed')
	{
		$stat = '1';
	}
	
	if($field == 'Quo No')
	{
		$f = 't_quo.quo_no';
	}else if($field == 'JO No'){
		$f = 't_jo.jo_no';	
	}else if($field == 'Customer'){
		$f = 'm_cust.nama_cust';		
	}else if($field == 'Loading'){
		$f = 'm_port.nama_port';	
	}else if($field == 'Discharge'){
		$f = 'm_port1.nama_port';	
	}else{
		$f = 't_jo.jo_no';
	}
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="7%" style="text-align: center;">QUO. NO</th>
					<th rowspan="2" width="6%" style="text-align: center;">#JO. NO</th>
					<th rowspan="2" width="4%" style="text-align: center;">TYPE</th>
					<th rowspan="2" width="14%" style="text-align: center;">CUSTOMER</th>
					<th rowspan="2" width="11%" style="text-align: center;">LOADING<br>DISCHARGE</th>
					<th rowspan="2" width="6%" style="text-align: center;">PROFIT</th>					
					<th colspan="6" width="35%" style="text-align: center;">MONITORING</th>
					<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>
					<th rowspan="2" width="2%" style="text-align: center;">PRINT</th>	
				</tr>
				<tr>	
					<th width="7%" style="text-align: center;">TGL BERANGKAT</th>
					<th width="7%" style="text-align: center;">TGL TIBA</th>
					<th width="7%" style="text-align: center;">DOC LENGKAP</th>
					<th width="7%" style="text-align: center;">SPPB/ NPE</th>
					<th width="7%" style="text-align: center;">DELIVERY</th>	
					<th width="7%" style="text-align: center;">INVOICE</th>						
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
		$SQL = "select t_jo.*, m_cust.nama_cust, m_cust.unit, m_port.nama_port as nama_pol, 
			  m_port1.nama_port as nama_pod, t_quo.quo_no, t_quo.sales  from 
			  t_jo inner join m_cust on  t_jo.id_cust = m_cust.id_cust
			  left join m_port on t_jo.id_pol= m_port.id_port
			  left join m_port as m_port1 on t_jo.id_pod = m_port1.id_port
			  left join t_quo on t_jo.id_quo = t_quo.id_quo
			  where t_jo.jo_date between '$tgl1x' and '$tgl2x' and
			  $f LIKE '%$cari%' and t_jo.status <> '2' and t_jo.jenis_jo = '0' and t_quo.sales LIKE '%$id_user%' order by t_jo.jo_date desc
			  LIMIT $offset, $jmlperhalaman";
	}else{
		$SQL = "select t_jo.*, m_cust.nama_cust, m_cust.unit, m_port.nama_port as nama_pol, 
			  m_port1.nama_port as nama_pod, t_quo.quo_no, t_quo.sales  from 
			  t_jo inner join m_cust on  t_jo.id_cust = m_cust.id_cust
			  left join m_port on t_jo.id_pol= m_port.id_port
			  left join m_port as m_port1 on t_jo.id_pod = m_port1.id_port
			  left join t_quo on t_jo.id_quo = t_quo.id_quo
			  where t_jo.jo_date between '$tgl1x' and '$tgl2x' and
			  $f LIKE '%$cari%' and t_jo.status = '$stat' and t_jo.jenis_jo = '0' and t_quo.sales LIKE '%$id_user%' order by t_jo.jo_date desc
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
			$tanggal = ConverTgl($row['jo_date']);
			$etd = ConverTgl($row['etd']);
			$eta = ConverTgl($row['eta']);
			$tipe = substr($row['tipe'],0,1);
			$posisi++;		
			$tgl_berangkat = ConverTgl($row['tgl_berangkat']);
			$tgl_tiba = ConverTgl($row['tgl_tiba']);
			$tgl_doc = ConverTgl($row['tgl_doc']);
			$tgl_sppb = ConverTgl($row['tgl_sppb']);
			$tgl_deliv = ConverTgl($row['tgl_deliv']);
			$tgl_inv = ConverTgl($row['tgl_inv']);
			if($row['status'] == '1')
			{
				$label = 'success';
				$status = 'Completed';
			}else if($row['status'] == '0') {
				$label = 'danger';
				$status = 'In Progress';
			}
			$xy1="View|$row[id_jo]";
			$xy1=base64_encode($xy1);
			$link_jo = "jo_data.php?id=$xy1";
			
			$sale = number_format($row['sale'],2);
			$buy = number_format($row['buy'],2);
			$profit = number_format($row['profit'],2);
			
			$t_sale = $t_sale + $row['sale'];
			$t_buy = $t_buy + $row['buy'];
			$t_profit = $t_profit + $row['profit'];
			
			if($row['jenis_jo'] == '0')
			{
				$jenis = "$tipe $row[deliv]";
			}else{
				$jenis = "$row[tipe]";
			}
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>
				<td style="text-align:center">'.$row['quo_no'].'</td>
				<td style="text-align:center">'.$row['jo_no'].'</td>
				<td style="text-align:center">'.$jenis.'</td>
				<td style="text-align:left">'.$row['nama_cust'].'</td>
				<td style="text-align:center">'.$row['nama_pol'].'<br>'.$row['nama_pod'].'</td>
				<td style="text-align:right;background:#4bc343;color:#fff">'.$profit.'</td>
				<td style="text-align:center">'.$tgl_berangkat.'<br>'.$row['ket_berangkat'].'</td>
				<td style="text-align:center">'.$tgl_tiba.'<br>'.$row['ket_tiba'].'</td>
				<td style="text-align:center">'.$tgl_doc.'<br>'.$row['ket_doc'].'</td>
				<td style="text-align:center">'.$tgl_sppb.'<br>'.$row['ket_sppb'].'</td>
				<td style="text-align:center">'.$tgl_deliv.'<br>'.$row['ket_deliv'].'</td>
				<td style="text-align:center">'.$tgl_inv.'<br>'.$row['ket_inv'].'</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';				
				
				$xy1="$row[id_jo]";
				$xy1=base64_encode($xy1);
				if($row['jenis_jo'] == '1')
				{
					$link = "'cetak_joc_profit.php?id=$xy1'";
				}else{
					$link = "'cetak_jo_profit.php?id=$xy1'";
				}
				
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Print"
						style="margin:-3px;border-radius:0px" type="button" 									
						onClick="window.open('.$link.') ">
						<span class="fa fa-print " ></span>
					</button></td>';
					
				
				$data .='</tr>';
    		$number++;
    	}		
		$t_sale = number_format($t_sale,2);
		$t_buy = number_format($t_buy,2);
		$t_profit = number_format($t_profit,2);
		$data .= '<tr>';
		$data .= '<td colspan= "7" style="text-align:right;background:#eaebec;color:#000"><b>Total  :&nbsp;&nbsp;&nbsp;</b></td>	
					<td style="text-align:right;background:#4bc343;color:#fff"><b>'.$t_profit.'</b></td>
					<td colspan= "5" style="text-align:right;background:#eaebec"</td>	';
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
					$pq = mysqli_query($koneksi, "select count(t_jo.id_jo) as jml from 
					  t_jo inner join m_cust on  t_jo.id_cust = m_cust.id_cust
					  left join m_port on t_jo.id_pol= m_port.id_port
					  left join m_port as m_port1 on t_jo.id_pod = m_port1.id_port
					  left join t_quo on t_jo.id_quo = t_quo.id_quo
					  where t_jo.jo_date between '$tgl1x' and '$tgl2x' and
					  $f LIKE '%$cari%' order by t_jo.jo_no desc");
				}else{
					$pq = mysqli_query($koneksi, "select count(t_jo.id_jo) as jml from 
					  t_jo inner join m_cust on  t_jo.id_cust = m_cust.id_cust
					  left join m_port on t_jo.id_pol= m_port.id_port
					  left join m_port as m_port1 on t_jo.id_pod = m_port1.id_port
					  left join t_quo on t_jo.id_quo = t_quo.id_quo
					  where t_jo.jo_date between '$tgl1x' and '$tgl2x' and
					  $f LIKE '%$cari%' and t_jo.status = '$stat' order by t_jo.jo_no desc");
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

?>