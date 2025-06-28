<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=ROTASI BAN.xls");

$idx = $_GET['id'];	
$id=base64_decode($idx);
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Detil Rotasi Ban</th>
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
		<th style="font-size:12; width:90px;text-align:center">NO SERI</th>
		<th style="font-size:12; width:90px;text-align:center">JENIS</th>
		<th style="font-size:12; width:90px;text-align:center">MERK</th>
		<th style="font-size:12; width:600px;text-align:center">NO POLISI</th>
		<th style="font-size:12; width:90px;text-align:center">JENIS</th>
		<th style="font-size:12; width:600px;text-align:center">POSISI</th>
		<th style="font-size:12; width:700px;text-align:center">KETEBALAN</th>
		<th style="font-size:12; width:700px;text-align:center">KM</th>
		<th  style="font-size:12; width:700px;text-align:center">KETERANGAN</th>
	</tr>
	
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	$t1 = "select t_ban_detil.*, m_mobil_tr.no_polisi, t_ban.no_seri, t_ban.jenis_ban, t_ban.merk_ban
				from  t_ban_detil left join m_mobil_tr on t_ban_detil.id_mobil = m_mobil_tr.id_mobil 
				left join t_ban on t_ban_detil.id_ban = t_ban.id_ban
				where t_ban_detil.id_ban = '$id' order  by t_ban_detil.tanggal asc, t_ban_detil.id_detil asc";			

	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		
		
		
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tanggal]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_seri]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[jenis_ban]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[merk_ban]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_polisi]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[jenis_pekerjaan]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[posisi]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[ketebalan]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[km]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[ket]";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
