<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DATAORDER.xls");

$idx = $_GET['id'];	
$x=base64_decode($idx);
$pecah = explode("|", $x);
$tgl1= $pecah[0];
$tgl2= $pecah[1];
$stat = $pecah[2];
$field = $pecah[3];
$cari = $pecah[4];
$field1 = $pecah[5];
$cari1 = $pecah[6];

if($field == 'No Order')
	{
		$f = 'tr_jo.no_jo';	
	}else if($field == 'No Quo'){
		$f = 'tr_quo.quo_no';		
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
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Order </th>
	</tr>
	<tr>
		<th style="font-size:12; width:90px;text-align:left">Date</th>
		<th colspan="7" style="font-size:12; text-align:left">: <?php echo "$tgl1  to  $tgl2 "; ?></th>
	</tr>
	<tr>
		<th style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>


<table border="1">
	
	<tr>
		<th style="font-size:12; width:90px;text-align:center">NO</th>
		<th style="font-size:12; width:90px;text-align:center">DATE</th>
		<th style="font-size:12; width:600px;text-align:center">NO ORDER</th>
		<th style="font-size:12; width:600px;text-align:center">PROJECT CODE</th>
		<th style="font-size:12; width:600px;text-align:center">NO. PO</th>
		<th style="font-size:12; width:700px;text-align:center">POLICE NUM</th>
		<th style="font-size:12; width:700px;text-align:center">DRIVER</th>
		<th style="font-size:12; width:700px;text-align:center">FEET</th>
		<th style="font-size:12; width:700px;text-align:center">NO CONTAINER</th>
		<th style="font-size:12; width:90px;text-align:center">CUSTOMER</th>
		<th style="font-size:12; width:90px;text-align:center">RECEIVER</th>
		<th style="font-size:12; width:90px;text-align:center">ROUTE</th>
		<th style="font-size:12; width:700px;text-align:center">DELIVERY COST</th>
		<th style="font-size:12; width:700px;text-align:center">OTHER COST</th>
		<th style="font-size:12; width:700px;text-align:center">TRAVEL EXPENSE</th>
		<th style="font-size:12; width:700px;text-align:center">RITASE</th>
		<th style="font-size:12; width:700px;text-align:center">OTHER AP</th>
		<th style="font-size:12; width:700px;text-align:center">CLAIM</th>
	</tr>
	
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	// $t1 = "SELECT 
	// 		tr_jo.*, 
	// 		tr_quo.quo_no, 
	// 		m_cust_tr.nama_cust, 
	// 		m_kota_tr.nama_kota AS asal, 
	// 		m_kota1.nama_kota AS tujuan,
	// 		m_mobil_tr.no_polisi, 
	// 		m_supir_tr.nama_supir
	// 	FROM tr_jo 
	// 	LEFT JOIN tr_quo_data ON tr_jo.id_detil_quo = tr_quo_data.id_detil
	// 	LEFT JOIN tr_quo ON tr_quo_data.id_quo = tr_quo.id_quo
	// 	LEFT JOIN m_kota_tr ON tr_jo.id_asal = m_kota_tr.id_kota
	// 	LEFT JOIN m_kota_tr AS m_kota1 ON tr_jo.id_tujuan = m_kota1.id_kota
	// 	LEFT JOIN m_cust_tr ON tr_jo.id_cust = m_cust_tr.id_cust
	// 	LEFT JOIN m_mobil_tr ON tr_jo.id_mobil = m_mobil_tr.id_mobil
	// 	LEFT JOIN m_supir_tr ON tr_jo.id_supir = m_supir_tr.id_supir
		// WHERE tr_jo.tgl_jo between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' 
		// ORDER BY tr_jo.tgl_jo ASC, tr_jo.no_jo ASC";	
	$t1 = "SELECT 
			tr_jo.tgl_jo,
			tr_jo.no_jo,
			tr_jo.no_sap,
			tr_jo.no_do,
			m_mobil_tr.no_polisi,
			m_supir_tr.nama_supir,
			tr_jo_detail.jenis_mobil,
			tr_sj.container,
			m_cust_tr.nama_cust,
			tr_jo.penerima,
			tr_sj.itemname AS rute,
			tr_jo_detail.harga AS deliv_cost,
			COALESCE(SUM(tr_jo_uj.harga), 0) AS other_cost,
			COALESCE(tr_jo_detail.uj, 0) AS travel_expense,
			COALESCE(tr_jo_detail.ritase, 0) AS ritase,
			COALESCE(SUM(tr_sj_uj.biaya), 0) AS other_ap,
			COALESCE(tr_sj.claim, 0) AS claim
		FROM tr_jo 
		LEFT JOIN tr_quo ON tr_quo.id_quo = tr_jo.id_quo
		LEFT JOIN tr_jo_detail ON tr_jo_detail.id_so = tr_jo.id_jo
		LEFT JOIN tr_sj ON tr_sj.no_jo = tr_jo.no_jo
		LEFT JOIN m_mobil_tr ON m_mobil_tr.id_mobil = tr_sj.id_mobil
		LEFT JOIN m_supir_tr ON m_supir_tr.id_supir = tr_sj.id_supir
		LEFT JOIN m_cust_tr ON m_cust_tr.id_cust = tr_jo.id_cust
		LEFT JOIN tr_jo_uj ON tr_jo_uj.id_jo = tr_jo.id_jo
		LEFT JOIN tr_sj_uj ON tr_sj_uj.id_sj = tr_sj.id_sj
		WHERE tr_jo.tgl_jo BETWEEN '$tgl1x' AND '$tgl2x' 
		AND $f LIKE '%$cari%' 
		AND $f1 LIKE '%$cari1%' 
		GROUP BY 
			tr_jo.tgl_jo,
			tr_jo.no_jo,
			tr_jo.no_sap,
			tr_jo.no_do,
			m_mobil_tr.no_polisi,
			m_supir_tr.nama_supir,
			tr_jo_detail.jenis_mobil,
			tr_sj.container,
			m_cust_tr.nama_cust,
			tr_jo.penerima,
			tr_sj.itemname,
			tr_jo_detail.harga,
			tr_jo_detail.uj,
			tr_jo_detail.ritase,
			tr_sj.claim
		ORDER BY 
			tr_jo.tgl_jo ASC, 
			tr_jo.no_jo ASC";

	// echo $t1;
	// exit;

	$h1	= mysqli_query($koneksi, $t1);       

	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
	?>
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tgl_jo]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_jo]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_sap]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_do]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_polisi]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[nama_supir]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[jenis_mobil]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[container]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[nama_cust]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[penerima]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[rute]";?></b></td>

			<!-- DELIV COST -->
			<td style="text-align:right"><?php echo "$d1[deliv_cost]";?></b></td>
			<!-- OTHER COST -->
			<td style="text-align:right"><?php echo "$d1[other_cost]";?></b></td> 
			<!-- TRAVEL EXPENSE -->
			<td style="text-align:right"><?php echo "$d1[travel_expense]";?></b></td>
			<!-- RITASE -->
			<td style="text-align:right"><?php echo "$d1[ritase]";?></b></td>
			<!-- OTHER -->
			<td style="text-align:right"><?php echo "$d1[other_ap]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[claim]";?></b></td>
		</tr>
	<?php }
	?>
	
	
</table>	