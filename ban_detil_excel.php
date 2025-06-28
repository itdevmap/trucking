<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=BAN.xls");

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
	}else if($field == 'No Seri'){
		$f = 't_ban.no_seri';		
	}else if($field == 'Jenis'){
		$f = 't_ban.jenis_ban';	
	}else if($field == 'Merk'){
		$f = 't_ban.merk_ban';		
	}else{
		$f = 't_ban.no_seri';	
	}
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Detil Ban</th>
	</tr>
	
	<tr>
		<th style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>


<table border="1">
	
	<tr>
		<th style="font-size:12; width:90px;text-align:center">KODE</th>
		<th style="font-size:12; width:600px;text-align:center">JENIS PEKERJAAN</th>
		<th style="font-size:12; width:600px;text-align:center">TANGGAL</th>
		<th style="font-size:12; width:90px;text-align:center">KM</th>
		<th style="font-size:12; width:600px;text-align:center">POSISI</th>
		<th style="font-size:12; width:90px;text-align:center">TEBAL</th>
		<th style="font-size:12; width:700px;text-align:center">NO. POLISI</th>
		<th style="font-size:12; width:700px;text-align:center">REMARK</th>
	</tr>
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	$t1 = "select t_ban.*, m_mobil_tr.no_polisi
			from 
			t_ban left join m_mobil_tr on t_ban.id_mobil = m_mobil_tr.id_mobil
			where t_ban.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' 	
			order by t_ban.posisi";			

	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		
		
		
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$d1[no_seri]";?></b></td>
			<td style="text-align:center"><?php echo "";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tanggal]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[km]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[posisi]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[ketebalan]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_polisi]";?></b></td>
			<td style="text-align:center"><?php echo "";?></b></td>
		</tr>

		<?php
		$t2 = "select t_ban_detil.*, m_mobil_tr.no_polisi
				from  t_ban_detil left join m_mobil_tr on t_ban_detil.id_mobil = m_mobil_tr.id_mobil 
				where t_ban_detil.id_ban = '$d1[id_ban]' order  by t_ban_detil.tanggal asc, t_ban_detil.id_detil asc";			

		$h2=mysqli_query($koneksi, $t2);       
		while ($d2=mysqli_fetch_array($h2))
		{
		?>
		
		<tr>
			<td style="text-align:center"><?php echo "$d1[no_seri]";?></b></td>
			<td style="text-align:center"><?php echo "$d2[jenis_pekerjaan]";?></b></td>
			<td style="text-align:center"><?php echo "$d2[tanggal]";?></b></td>
			<td style="text-align:center"><?php echo "$d2[km]";?></b></td>
			<td style="text-align:center"><?php echo "$d2[posisi]";?></b></td>
			<td style="text-align:center"><?php echo "$d2[ketebalan]";?></b></td>
			<td style="text-align:center"><?php echo "$d2[no_polisi]";?></b></td>
			<td style="text-align:center"><?php echo "$d2[ket]";?></b></td>
		</tr>
		
	<?php }
	}
	?>
	
	
</table>	
