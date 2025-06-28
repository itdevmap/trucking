<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=SPK.xls");

$idx = $_GET['id'];	
$x=base64_decode($idx);
$pecah = explode("|", $x);
$tgl1= $pecah[0];
$tgl2= $pecah[1];
$field = $pecah[2];
$cari = $pecah[3];
$field = $pecah[4];
$cari = $pecah[5];

if($field == 'No Polisi')
	{
		$f = 'm_mobil_tr.no_polisi';	
	}else if($field == 'No SPK'){
		$f = 't_spk.no_spk';	
	}else if($field == 'Jenis Pekerjaan'){
		$f = 't_spk.jenis';
	}else{
		$f = 't_spk.ket';	
}
	
if($field1 == 'No Polisi')
	{
		$f1 = 'm_mobil_tr.no_polisi';	
	}else if($field1 == 'No SPK'){
		$f1 = 't_spk.no_spk';	
	}else if($field1 == 'Jenis Pekerjaan'){
		$f1 = 't_spk.jenis';
	}else{
		$f1 = 't_spk.ket';	
}
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Perbaikan Mobil</th>
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
		<th style="font-size:12; width:90px;text-align:center">TANGGAL</th>
		<th style="font-size:12; width:600px;text-align:center">NO SPK</th>
		<th style="font-size:12; width:600px;text-align:center">START</th>
		<th style="font-size:12; width:90px;text-align:center">FINISH</th>
		<th style="font-size:12; width:600px;text-align:center">NO. POLISI</th>
		<th style="font-size:12; width:90px;text-align:center">KM</th>
		<th style="font-size:12; width:700px;text-align:center">JENIS PEKERJAAN</th>
	</tr>
	
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	$t1 = "select t_spk.*, m_mobil_tr.no_polisi
			from 
			t_spk left join m_mobil_tr on t_spk.id_mobil = m_mobil_tr.id_mobil
			where t_spk.tanggal between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' 	
			order by t_spk.tanggal desc";			

	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tanggal]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_spk]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[jam_mulai]:$d1[menit_mulai]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[jam_selesai]:$d1[menit_selesai]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_polisi]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[km]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[jenis]";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
