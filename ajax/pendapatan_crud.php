<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$sql = mysqli_query($koneksi,"SELECT * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='29' ");
$data=mysqli_fetch_array($sql);
$m_edit = $data['m_edit'];
$m_add = $data['m_add'];
$m_del = $data['m_del'];
$m_view = $data['m_view'];
$m_exe = $data['m_exe'];

if ($_GET['type'] == "read") {
    $tahun = $_GET['tahun'];

    $pq = mysqli_query($koneksi, "SELECT COUNT(id_sales) AS jml FROM m_sales_tr");
    $rq = mysqli_fetch_array($pq);
    $jml_sales = $rq['jml'] + 1;
    $lebar = 67 / $jml_sales;

    $data = '<table class="table table-hover table-striped" style="width:100%">'
          . '<thead style="font-weight:500px !important">'
          . '<tr>'
          . '<th rowspan="2" width="10%" style="text-align: center;">' . $tahun . '</th>'
          . '<th colspan="' . $jml_sales . '" width="64%" style="text-align: center;">TOTAL REVENUE</th>'
          . '<th rowspan="2" width="10%" style="text-align: center;">TOTAL REVENUE</th>'
          . '<th rowspan="2" width="10%" style="text-align: center;">TOTAL COST</th>'
          . '<th rowspan="2" width="8%" style="text-align: center;">RATIO<br>(%)</th>'
          . '</tr>';

    $data .= '<tr>';
    $h1 = mysqli_query($koneksi, "SELECT * FROM m_sales_tr ORDER BY nama");
    while ($d1 = mysqli_fetch_array($h1)) {
        $data .= '<th width="' . $lebar . '%" style="text-align: center;">' . $d1['nama'] . '</th>';
    }
    $data .= '<th width="' . $lebar . '%" style="text-align: center;">INTERCOMPANY</th>';
    $data .= '</tr></thead>';

    for ($x = 1; $x <= 12; $x++) {
        $bln = str_pad($x, 2, "0", STR_PAD_LEFT);
        $bulan = date("F", mktime(0, 0, 0, $x, 10));

        $data .= '<tr><td style="text-align:left">&nbsp;' . $bulan . '</td>';

        $revenue_bulanan = 0;
        $h1 = mysqli_query($koneksi, "SELECT * FROM m_sales_tr ORDER BY nama");

		// ADMIN | HARDY | IBRAM
		while ($d1 = mysqli_fetch_array($h1)) {
			$sql = "SELECT 
					tr_jo.id_jo,
					tr_jo.id_detil_quo,
					tr_quo.sales,
					tr_jo.tgl_jo,
					SUM(tr_jo.tagihan) AS jml 
				FROM tr_jo
				LEFT JOIN tr_quo ON tr_quo.id_quo = tr_jo.id_quo 
				WHERE tr_quo.sales LIKE '%{$d1['nama']}%'
				AND tr_jo.status = '1' 
				AND YEAR(tr_jo.tgl_jo) = '$tahun' 
				AND MONTH(tr_jo.tgl_jo) = '$bln'";
			$pq = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));

			$rq 	= mysqli_fetch_array($pq);
			$pend 	= $rq['jml'] ?? 0;
			$revenue_bulanan += $pend;
			$pendx 	= number_format($pend, 0);
			$data 	.= '<td style="text-align: right;">' . $pendx . '</td>';
		}


		// INTERCOMPANY
        $pq = mysqli_query($koneksi, "SELECT SUM(tagihan) AS jml FROM tr_jo WHERE id_detil_quo = '0' AND status = '1' 
            AND YEAR(tgl_jo) = '$tahun' AND MONTH(tgl_jo) = '$bln'");
        $rq = mysqli_fetch_array($pq);
        $pend_int = $rq['jml'] ?? 0;

		// HITUNG REVENUE
        $total_revenue = $revenue_bulanan + $pend_int;
        $pend_intx = number_format($pend_int, 0);
        $revenue_akhir = number_format($total_revenue, 0);

		// HITUNG BIAYA

		$q_cost = "SELECT 
						SUM(tr_jo.total_so) AS jml 
				FROM tr_jo 
				LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo 
				WHERE tr_jo.status = '1' 
					AND YEAR(tgl_jo) = '$tahun' 
					AND MONTH(tgl_jo) = '$bln'
		
					";
		// SELECT 
		// 			SUM(uj + uj_lain + ritase) AS jml 
        //     	FROM tr_jo 
		// 		WHERE `status` = '1' 
		// 			AND YEAR(tgl_jo) = '$tahun' 
		// 			AND MONTH(tgl_jo) = '$bln'
		// echo "</pre>$q_cost</pre>";
		// exit;
		
        $pq 	= mysqli_query($koneksi, $q_cost);

        $rq 	= mysqli_fetch_array($pq);
        $biaya 	= $rq['jml'] ?? 0;
        $biayax = number_format($biaya, 0);

		// HITUNG RATIO
        $ratio = ($total_revenue != 0) ? ($biaya / $total_revenue) * 100 : 0;
        $ratiox = number_format($ratio, 2);

		// INI SHOW NYA KE TABLE
        $data .= '<td style="text-align:right">&nbsp;' . $pend_intx . '</td>';
        $data .= '<td style="text-align:right">&nbsp;' . $revenue_akhir . '</td>';
        $data .= '<td style="text-align:right">&nbsp;' . $biayax . '</td>';
        $data .= '<td style="text-align:right">&nbsp;' . $ratiox . '</td>';
        $data .= '</tr>';
    }

    // INI TOTAL TAHUNAN
    $data .= '<tr><td style="text-align:right;background:#eaebec;color:#000"></td>';
    $total_pend = 0;
    $h1 = mysqli_query($koneksi, "SELECT * FROM m_sales_tr ORDER BY nama");
    while ($d1 = mysqli_fetch_array($h1)) {
        $pq = mysqli_query($koneksi, "SELECT SUM(tr_jo.tagihan) AS jml 
            FROM tr_jo 
            LEFT JOIN tr_quo_data ON tr_jo.id_detil_quo = tr_quo_data.id_detil
            LEFT JOIN tr_quo ON tr_quo_data.id_quo = tr_quo.id_quo
            WHERE tr_quo.sales = '$d1[nama]' AND tr_jo.status = '1' AND YEAR(tr_jo.tgl_jo) = '$tahun'");
        $rq = mysqli_fetch_array($pq);
        $pend = $rq['jml'] ?? 0;
        $total_pend += $pend;
        $pendx = number_format($pend, 0);
        $data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>' . $pendx . '</b></td>';
    }

    $pq = mysqli_query($koneksi, "SELECT SUM(tagihan) AS jml FROM tr_jo WHERE id_detil_quo = '0' AND status = '1' AND YEAR(tgl_jo) = '$tahun'");
    $rq = mysqli_fetch_array($pq);
    $pend_int = $rq['jml'] ?? 0;
    $pend_intx = number_format($pend_int, 0);
    $data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>' . $pend_intx . '</b></td>';

    $total = $total_pend + $pend_int;
    $totalx = number_format($total, 0);
    $data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>' . $totalx . '</b></td>';

    $pq = mysqli_query($koneksi, "SELECT SUM(uj + uj_lain + ritase) AS jml FROM tr_jo WHERE status = '1' AND YEAR(tgl_jo) = '$tahun'");
    $rq = mysqli_fetch_array($pq);
    $biaya = $rq['jml'] ?? 0;
    $biayax = number_format($biaya, 0);
    $data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>' . $biayax . '</b></td>';

    $ratio = ($total != 0) ? ($biaya / $total) * 100 : 0;
    $ratiox = number_format($ratio, 2);
    $data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>' . $ratiox . '</b></td>';

    $data .= '</tr></table>';

    echo $data;
}
else if ($_GET['type'] == "read_truck"){
	$tahun = $_GET['tahun'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="70%" style="text-align: center;">NO. POLICE</th>
					<th rowspan="2" width="10%" style="text-align: center;">REVENUE</th>
					<th rowspan="2" width="10%" style="text-align: center;">COST</th>
					<th rowspan="2" width="7%" style="text-align: center;">MARGIN</th>
				</tr>
			</thead>';		
	
	$SQL = "select * from m_mobil_tr order by no_polisi";
			
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			
			$pq = mysqli_query($koneksi, "select sum(tagihan) as pend, sum(uj+uj_lain+ritase) as biaya 
					  from  
					  tr_jo  where id_mobil = '$row[id_mobil]' and status = '1' 
					  and year(tgl_jo) = '$tahun' ");					
			$rq=mysqli_fetch_array($pq);
			$pend = $rq['pend'] ;
			$biaya = $rq['biaya'] ;
			$pendx = number_format($pend,0);
			$biayax = number_format($biaya,0);
			
			$margin = $pend - $biaya;
			$marginx = number_format($margin,0);
			
			$t_pend = $t_pend + $pend;
			$t_biaya = $t_biaya + $biaya;
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:left">'.$row['no_polisi'].'</td>
				<td style="text-align:right">'.$pendx.'</td>
				<td style="text-align:right">'.$biayax.'</td>
				<td style="text-align:right">'.$marginx.'</td>';
				$data .='</tr>';
    		$number++;
    	}	
		
		$margin = $t_pend - $t_biaya;		
		$pendx = number_format($t_pend,0);	
		$biayax = number_format($t_biaya,0);
		$marginx = number_format($margin,0);		
		$data .= '<tr>';
		$data .= '<td colspan = "2" style="text-align:right;background:#eaebec;color:#000"></td>';	
		$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$pendx.'</b></td>';	
		$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$biayax.'</b></td>';	
		$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$marginx.'</b></td>';
		$data .= '</tr>';
	
	
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
				
    echo $data;	
	
	
}
else if ($_GET['type'] == "read_dock"){
	$tahun = $_GET['tahun'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="19%" style="text-align: center;">CUSTOMER</th>
					<th colspan="12" width="72%" style="text-align: center;">MONTH</th>
					<th rowspan="2" width="6%" style="text-align: center;">TOTAL</th>
				</tr>
				<tr>				
					<th width="6%" style="text-align: center;">JAN</th>
					<th width="6%" style="text-align: center;">FEB</th>
					<th width="6%" style="text-align: center;">MAR</th>
					<th width="6%" style="text-align: center;">APR</th>
					<th width="6%" style="text-align: center;">MAY</th>
					<th width="6%" style="text-align: center;">JUN</th>
					<th width="6%" style="text-align: center;">JUL</th>
					<th width="6%" style="text-align: center;">AUG</th>
					<th width="6%" style="text-align: center;">SEP</th>
					<th width="6%" style="text-align: center;">OCT</th>
					<th width="6%" style="text-align: center;">NOV</th>
					<th width="6%" style="text-align: center;">DEC</th>
				</tr>
			</thead>';		
	
	$SQL = "select t_ware_data.*, m_cust_tr.nama_cust from
			t_ware_data inner join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust	
			where t_ware_data.jenis = '1' and t_ware_data.jasa = '1' group by m_cust_tr.id_cust
	order by m_cust_tr.nama_cust";
			
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
				<td style="text-align:left">'.$row['nama_cust'].'</td>';
				$total = 0;
				for ($x = 1; $x <= 12; $x++) {
					if(strlen($x) == 1)
					{
						$bln = "0$x";
					}else{
						$bln = $x;
					}
					
					$pq = mysqli_query($koneksi, "select sum(tagihan) as jml 
					  from  t_ware_data  where status = '1' and jenis = '1' and jasa = '1'
					  and year(tanggal) = '$tahun' and month(tanggal) = '$bln' and id_cust = '$row[id_cust]' ");		

					$rq=mysqli_fetch_array($pq);
					$pend = $rq['jml'] ;
					$total = $total + $pend;
					$pendx = number_format($pend,0);
					$data .= '<td style="text-align:right">'.$pendx.'</td>';
				}	
				$totalx = number_format($total,0);
				$data .= '<td style="text-align:right">'.$totalx.'</td>';
				$data .='</tr>';
		}	
		
		$total = 0;
		$data .= '<tr>';
		$data .= '<td colspan = "2" style="text-align:right;background:#eaebec;color:#000"></td>';	
		for ($x = 1; $x <= 12; $x++) 
		{
			if(strlen($x) == 1)
			{
				$bln = "0$x";
				}else{
				$bln = $x;
			}
			$pq = mysqli_query($koneksi, "select sum(tagihan) as jml 
					  from  
					  t_ware_data  where status = '1' and jenis = '1' and jasa = '1'
					  and year(tanggal) = '$tahun' and month(tanggal) = '$bln' ");	

			$rq=mysqli_fetch_array($pq);
			$pend = $rq['jml'] ;
			$total = $total + $pend;
			$pendx = number_format($pend,0);
			$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$pendx.'</b></td>';	
		}
		$totalx = number_format($total,0);
		$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$totalx.'</b></td>';	
		$data .= '</tr>';
	
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
				
    echo $data;		
	
	
}

else if ($_GET['type'] == "read_hand"){
	$tahun = $_GET['tahun'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="19%" style="text-align: center;">CUSTOMER</th>
					<th colspan="12" width="72%" style="text-align: center;">MONTH</th>
					<th rowspan="2" width="6%" style="text-align: center;">TOTAL</th>
				</tr>
				<tr>				
					<th width="6%" style="text-align: center;">JAN</th>
					<th width="6%" style="text-align: center;">FEB</th>
					<th width="6%" style="text-align: center;">MAR</th>
					<th width="6%" style="text-align: center;">APR</th>
					<th width="6%" style="text-align: center;">MAY</th>
					<th width="6%" style="text-align: center;">JUN</th>
					<th width="6%" style="text-align: center;">JUL</th>
					<th width="6%" style="text-align: center;">AUG</th>
					<th width="6%" style="text-align: center;">SEP</th>
					<th width="6%" style="text-align: center;">OCT</th>
					<th width="6%" style="text-align: center;">NOV</th>
					<th width="6%" style="text-align: center;">DEC</th>
				</tr>
			</thead>';		
	
	$SQL = "select t_ware_data.*, m_cust_tr.nama_cust from
			t_ware_data inner join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust	
			where t_ware_data.jenis = '1' and t_ware_data.jasa = '0' group by m_cust_tr.id_cust
	order by m_cust_tr.nama_cust";
			
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
				<td style="text-align:left">'.$row['nama_cust'].'</td>';
				$total = 0;
				for ($x = 1; $x <= 12; $x++) {
					if(strlen($x) == 1)
					{
						$bln = "0$x";
					}else{
						$bln = $x;
					}
					
					$pq = mysqli_query($koneksi, "select sum(tagihan) as jml 
					  from  
					  t_ware_data  where status = '1' and jenis = '1' and jasa = '0'
					  and year(tanggal) = '$tahun' and month(tanggal) = '$bln' and id_cust = '$row[id_cust]' ");					
					$rq=mysqli_fetch_array($pq);
					$pend = $rq['jml'] ;
					$total = $total + $pend;
					$pendx = number_format($pend,0);
					$data .= '<td style="text-align:right">'.$pendx.'</td>';
				}	
				$totalx = number_format($total,0);
				$data .= '<td style="text-align:right">'.$totalx.'</td>';
				$data .='</tr>';
		}	
		
		$total = 0;
		$data .= '<tr>';
		$data .= '<td colspan = "2" style="text-align:right;background:#eaebec;color:#000"></td>';	
		for ($x = 1; $x <= 12; $x++) 
		{
			if(strlen($x) == 1)
			{
				$bln = "0$x";
				}else{
				$bln = $x;
			}
			$pq = mysqli_query($koneksi, "select sum(tagihan) as jml 
					  from  
					  t_ware_data  where status = '1' and jenis = '1' and jasa = '0'
					  and year(tanggal) = '$tahun' and month(tanggal) = '$bln' ");					
			$rq=mysqli_fetch_array($pq);
			$pend = $rq['jml'] ;
			$total = $total + $pend;
			$pendx = number_format($pend,0);
			$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$pendx.'</b></td>';	
		}
		$totalx = number_format($total,0);
		$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$totalx.'</b></td>';	
		$data .= '</tr>';
	
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
				
    echo $data;			
	
}
else if ($_GET['type'] == "read_sewa"){
	$tahun = $_GET['tahun'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="19%" style="text-align: center;">CUSTOMER</th>
					<th colspan="12" width="72%" style="text-align: center;">MONTH</th>
					<th rowspan="2" width="6%" style="text-align: center;">TOTAL</th>
				</tr>
				<tr>				
					<th width="6%" style="text-align: center;">JAN</th>
					<th width="6%" style="text-align: center;">FEB</th>
					<th width="6%" style="text-align: center;">MAR</th>
					<th width="6%" style="text-align: center;">APR</th>
					<th width="6%" style="text-align: center;">MAY</th>
					<th width="6%" style="text-align: center;">JUN</th>
					<th width="6%" style="text-align: center;">JUL</th>
					<th width="6%" style="text-align: center;">AUG</th>
					<th width="6%" style="text-align: center;">SEP</th>
					<th width="6%" style="text-align: center;">OCT</th>
					<th width="6%" style="text-align: center;">NOV</th>
					<th width="6%" style="text-align: center;">DEC</th>
				</tr>
			</thead>';		
	
	$SQL = "select t_ware_sewa.*, m_cust_tr.nama_cust from
			t_ware_sewa inner join m_cust_tr on t_ware_sewa.id_cust = m_cust_tr.id_cust	
			group by m_cust_tr.id_cust
	order by m_cust_tr.nama_cust";
			
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
				<td style="text-align:left">'.$row['nama_cust'].'</td>';
				$total = 0;
				for ($x = 1; $x <= 12; $x++) {
					if(strlen($x) == 1)
					{
						$bln = "0$x";
					}else{
						$bln = $x;
					}
					
					$pq = mysqli_query($koneksi, "select sum(tagihan) as jml 
					  from  
					  t_ware_sewa  where status = '1' 
					  and year(tanggal) = '$tahun' and month(tanggal) = '$bln' and id_cust = '$row[id_cust]' ");					
					$rq=mysqli_fetch_array($pq);
					$pend = $rq['jml'] ;
					$total = $total + $pend;
					$pendx = number_format($pend,0);
					$data .= '<td style="text-align:right">'.$pendx.'</td>';
				}	
				$totalx = number_format($total,0);
				$data .= '<td style="text-align:right">'.$totalx.'</td>';
				$data .='</tr>';
		}	
		
		$total = 0;
		$data .= '<tr>';
		$data .= '<td colspan = "2" style="text-align:right;background:#eaebec;color:#000"></td>';	
		for ($x = 1; $x <= 12; $x++) 
		{
			if(strlen($x) == 1)
			{
				$bln = "0$x";
				}else{
				$bln = $x;
			}
			$pq = mysqli_query($koneksi, "select sum(tagihan) as jml 
					  from  
					  t_ware_sewa  where status = '1' 
					  and year(tanggal) = '$tahun' and month(tanggal) = '$bln' ");					
			$rq=mysqli_fetch_array($pq);
			$pend = $rq['jml'] ;
			$total = $total + $pend;
			$pendx = number_format($pend,0);
			$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$pendx.'</b></td>';	
		}
		$totalx = number_format($total,0);
		$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$totalx.'</b></td>';	
		$data .= '</tr>';
	
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
				
    echo $data;				


}
else if ($_GET['type'] == "read_rawat"){
	$tahun = $_GET['tahun'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="10%" style="text-align: center;">NO. POLICE</th>
					<th colspan="12" width="72%" style="text-align: center;">SPK QUANTITY</th>
					<th rowspan="2" width="6%" style="text-align: center;">TOTAL</th>
				</tr>
				<tr>				
					<th width="6%" style="text-align: center;">JAN</th>
					<th width="6%" style="text-align: center;">FEB</th>
					<th width="6%" style="text-align: center;">MAR</th>
					<th width="6%" style="text-align: center;">APR</th>
					<th width="6%" style="text-align: center;">MAY</th>
					<th width="6%" style="text-align: center;">JUN</th>
					<th width="6%" style="text-align: center;">JUL</th>
					<th width="6%" style="text-align: center;">AUG</th>
					<th width="6%" style="text-align: center;">SEP</th>
					<th width="6%" style="text-align: center;">OCT</th>
					<th width="6%" style="text-align: center;">NOV</th>
					<th width="6%" style="text-align: center;">DEC</th>
				</tr>
			</thead>';		
	
	$SQL = "select * from m_mobil_tr order by no_polisi";
			
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
				<td style="text-align:left">'.$row['no_polisi'].'</td>';
				$total = 0;
				for ($x = 1; $x <= 12; $x++) {
					if(strlen($x) == 1)
					{
						$bln = "0$x";
					}else{
						$bln = $x;
					}
					
					$pq = mysqli_query($koneksi, "select count(id_spk) as jml 
					  from  
					  t_spk  where  year(tanggal) = '$tahun' and month(tanggal) = '$bln' and id_mobil = '$row[id_mobil]' ");					
					$rq=mysqli_fetch_array($pq);
					$pend = $rq['jml'] ;
					$total = $total + $pend;
					$pendx = number_format($pend,0);
					$data .= '<td style="text-align:center">'.$pendx.'</td>';
				}	
				$totalx = number_format($total,0);
				$data .= '<td style="text-align:center">'.$totalx.'</td>';
				$data .='</tr>';
		}	
		
		/*
		$total = 0;
		$data .= '<tr>';
		$data .= '<td colspan = "2" style="text-align:right;background:#eaebec;color:#000"></td>';	
		for ($x = 1; $x <= 12; $x++) 
		{
			if(strlen($x) == 1)
			{
				$bln = "0$x";
				}else{
				$bln = $x;
			}
			$pq = mysqli_query($koneksi, "select sum(tagihan) as jml 
					  from  
					  t_ware_data  where status = '1' and jenis = '1' and jasa = '1'
					  and year(tanggal) = '$tahun' and month(tanggal) = '$bln' ");					
			$rq=mysqli_fetch_array($pq);
			$pend = $rq['jml'] ;
			$total = $total + $pend;
			$pendx = number_format($pend,0);
			$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$pendx.'</b></td>';	
		}
		$totalx = number_format($total,0);
		$data .= '<td style="text-align: right;background:#eaebec;color:#000"><b>'.$totalx.'</b></td>';	
		$data .= '</tr>';
		*/
		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
				
    echo $data;		
	
}

?>