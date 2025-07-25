<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Outbound.xls");

$idx = $_GET['id'];	
$x=base64_decode($idx);
$pecah = explode("|", $x);
$tgl1= $pecah[0];
$tgl2= $pecah[1];
$stat= $pecah[2];
$field= $pecah[3];
$cari= $pecah[4];
$field1 = $pecah[5];
$cari1 = $pecah[6];

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
	}else if($field1 == 'No Polisi'){
		$f1 = 't_ware_data.no_polisi';			
	}else{
		$f1 = 't_ware_data.no_doc';
	}
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Delivery Barang</th>
	</tr>
	<tr>
		<th style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>


<table border="1">
	
	<tr>
		<th style="font-size:12; width:90px;text-align:center">NO</th>
		<th style="font-size:12; width:90px;text-align:center">TANGGAL</th>
		<th style="font-size:12; width:600px;text-align:center">NO. DN</th>
		<th style="font-size:12; width:600px;text-align:center">NO. EXT</th>
		<th style="font-size:12; width:90px;text-align:center">NO. SJ</th>
		<th style="font-size:12; width:90px;text-align:center">CUSTOMER</th>
		<th style="font-size:12; width:600px;text-align:center">NO.CONTAINER</th>
		<th style="font-size:12; width:600px;text-align:center">NOPOL</th>
		<th style="font-size:12; width:600px;text-align:center">TUJUAN</th>
		<th style="font-size:12; width:600px;text-align:center">JENIS TAGIHAN</th>
		<th style="font-size:12; width:600px;text-align:center">ITEM NO</th>
		<th style="font-size:12; width:600px;text-align:center">ITEM NAME</th>
		<th style="font-size:12; width:600px;text-align:center">AGING</th>
		<th style="font-size:12; width:600px;text-align:center">QTY</th>
		<th style="font-size:12; width:600px;text-align:center">TANGGAL DITERIMA</th>
		<th style="font-size:12; width:600px;text-align:center">TANGGAL MUAT</th>
		<th style="font-size:12; width:600px;text-align:center">SELISIH HARI</th>
		<th style="font-size:12; width:600px;text-align:center">CBM </th>
		<th style="font-size:12; width:600px;text-align:center">PRICE</th>
		<th style="font-size:12; width:600px;text-align:center">JUMLAH</th>
	</tr>
	
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	
	if($stat == 'All')
	{
		$t1 = "select t_ware_data.*, m_cust_tr.nama_cust
			from 
			t_ware_data inner join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
			where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and t_ware_data.jenis = '1'
			order  by t_ware_data.tanggal asc ";
	}else{
		$t1 = "select t_ware_data.*, m_cust_tr.nama_cust
			from 
			t_ware_data inner join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
			where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and t_ware_data.jenis = '1' and t_ware_data.status = '$stat'
			order  by t_ware_data.tanggal asc ";
	}
	
	
			
	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		if($d1['jasa'] == '1')
		{
			$t2 = "select  t_ware_jasa_biaya.*, m_cost_tr.nama_cost
			from 
			 t_ware_jasa_biaya inner join t_ware_quo_biaya on t_ware_jasa_biaya.id_biaya = t_ware_quo_biaya.id_detil
			left join m_cost_tr on t_ware_quo_biaya.id_biaya = m_cost_tr.id_cost
			where t_ware_jasa_biaya.id_data = '$d1[id_data]'  order by  t_ware_jasa_biaya.id_detil";
		}else{
			$t2 = "select t_ware_data_detil.*, t_ware_data_detil1.no_cont, t_ware_data.tanggal,
			t_ware.nama, t_ware.kode, t_ware.vol, t_ware.unit, 
			t_ware_data1.tanggal as tgl_sj, t_ware_quo.aging_sewa, t_ware_quo.harga_handling
			from 
			t_ware_data_detil inner join t_ware_data_detil as t_ware_data_detil1 on 
			t_ware_data_detil.id_detil_masuk = t_ware_data_detil1.id_detil
			left join t_ware_data on t_ware_data_detil1.id_data = t_ware_data.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware
			left join t_ware_data as t_ware_data1 on t_ware_data_detil.id_data = t_ware_data1.id_data
			left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
			where t_ware_data_detil.id_data = '$d1[id_data]'  order by  t_ware_data_detil.id_detil";
		}
		
			
		$h2=mysqli_query($koneksi, $t2);       
		while ($d2=mysqli_fetch_array($h2))
		{
		$n++;
		
		$tgl_masuk = strtotime($d2['tanggal']);
		$tgl_keluar = strtotime($d2['tgl_sj']);
		$aging = $tgl_keluar - $tgl_masuk; 
		$aging = ($aging/24/60/60);
		$selisih = round($aging);
		$vol = $d2['keluar'] * $d2['vol'];
		
		if($selisih > $d2['aging_sewa'])
		{
			$harga = $d2['harga_handling'];
		}else{
			$harga = 0;
		}
		
		
		if($d1['jasa'] == '1')
		{
			$tgl_sj = $d1['tanggal'];
			$kode = $d2['nama_cost'];
			$nama = $d2['rem'];
			$qty = $d2['qty'];
			$cbm = $d2['qty']; 
			$harga = $d2['harga'];
		}else{
			$tgl_sj = $d2['tgl_sj'];
			$kode = $d2['kode'];
			$nama = $d2['nama'];
			$qty = $d2['keluar'];
			$cbm = $vol;
			
		}
		$jumlah = $harga * $cbm;
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$tgl_sj";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_doc]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_do]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_doc]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[nama_cust]";?></b></td>
			<td style="text-align:center"><?php echo "$d2[no_cont]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_polisi]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[gudang]";?></b></td>
			<td style="text-align:center">Pendapatan Jasa Sharing Warehouse</b></td>
			<td style="text-align:center"><?php echo "$kode";?></b></td>
			<td style="text-align:left"><?php echo "$nama";?></b></td>
			<td style="text-align:center"><?php echo "$d2[aging_sewa]";?></b></td>
			<td style="text-align:center"><?php echo "$qty";?></b></td>
			<td style="text-align:center"><?php echo "$d2[tanggal]";?></b></td>
			<td style="text-align:center"><?php echo "$tgl_sj";?></b></td>
			<td style="text-align:center"><?php echo "$selisih.";?></b></td>
			<td style="text-align:center"><?php echo "$cbm";?></b></td>
			<td style="text-align:right"><?php echo "$harga";?></b></td>
			<td style="text-align:right"><?php echo "$jumlah";?></b></td>
		</tr>

		
	<?php }}
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
