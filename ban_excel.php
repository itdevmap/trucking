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
		<th colspan="7" style="font-size:24;text-align:left">Data Monitoring Ban</th>
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
		<th rowspan="2" style="font-size:12; width:90px;text-align:center">NO</th>
		<th rowspan="2" style="font-size:12; width:90px;text-align:center">TANGGAL</th>
		<th rowspan="2" style="font-size:12; width:600px;text-align:center">POSISI</th>
		<th rowspan="2" style="font-size:12; width:600px;text-align:center">NO POLISI</th>
		<th rowspan="2" style="font-size:12; width:90px;text-align:center">NO SERI</th>
		<th rowspan="2" style="font-size:12; width:600px;text-align:center">JENIS</th>
		<th rowspan="2" style="font-size:12; width:90px;text-align:center">MERK</th>
		<th rowspan="2" style="font-size:12; width:700px;text-align:center">KETEBALAN</th>
		<th rowspan="2" style="font-size:12; width:700px;text-align:center">KM</th>
		<th colspan="4" style="font-size:12; width:700px;text-align:center">POSISI SAAT INI</th>
	</tr>
	
	<tr>
		<th style="font-size:12; width:90px;text-align:center">POSISI</th>
		<th style="font-size:12; width:90px;text-align:center">NO POLISI</th>
		<th style="font-size:12; width:600px;text-align:center">KETEBALAN</th>
		<th style="font-size:12; width:600px;text-align:center">KM</th>
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
		
		$pq = mysqli_query($koneksi,"select t_ban_detil.*, m_mobil_tr.no_polisi
				from  t_ban_detil left join m_mobil_tr on t_ban_detil.id_mobil = m_mobil_tr.id_mobil 
				where t_ban_detil.id_ban = '$d1[id_ban]' order  by t_ban_detil.tanggal desc, t_ban_detil.id_detil desc ");
		$rq=mysqli_fetch_array($pq);	
		$no_polisi = $rq['no_polisi'];
		$posisi_rotasi = $rq['posisi'];
		$ketebalan_rotasi = number_format($rq['ketebalan'],2);
		$km_rotasi = number_format($rq['km'],0);
		
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tanggal]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[posisi]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_polisi]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_seri]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[jenis_ban]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[merk_ban]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[ketebalan]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[km]";?></b></td>
			
			<td style="text-align:center"><?php echo "$rq[posisi]";?></b></td>
			<td style="text-align:center"><?php echo "$rq[no_polisi]";?></b></td>
			<td style="text-align:center"><?php echo "$rq[ketebalan]";?></b></td>
			<td style="text-align:center"><?php echo "$rq[km]";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
