<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses where id_role = '$id_role'  and id_menu ='23' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

if ($_GET['type'] == "Read")
{
	$cur = trim($_GET['cur']);
	$tgl1 = $_GET['tgl1'];
	$tgl2 = $_GET['tgl2'];
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	if($cur == 'IDR')
	{
		$curx = '1';
	}else{
		$curx = '2';
	}	

	//PENDAPATAN
	$data = '<div class="col-md-7" >
				<div class="box box-success box-solid" style="height:600px;padding:5px;border:1px solid #ccc;background:#fff !important;">
					<table  style="width:100%;border:none">
						<tr>
						<td  style="border:none;width:65%;text-align: right;"></td>
						<td  style="border:none;width:15%;text-align: right;"></td>
						<td  style="border:none;width:15%;text-align: right;"></td>
						<td  style="border:none;width:5%;text-align: left;"></td>
						</tr>';
						
						$pendapatan =0;
						$sql1 = mysqli_query($koneksi, "select * from m_coa where id_coa ='4' order by id_coa " );		
						while($row1 = mysqli_fetch_assoc($sql1))
						{
							$data .= '<tr>
							<td  style="border:none;background:none;width:65%;text-align: left;padding:5px"><b>'.$row1['nama_coa'].'</b></td>
							<td  style="border:none;background:none;width:15%text-align: left;"></td>
							<td  style="border:none;background:none;width:15%;text-align: left;"></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
							</tr>';
							
							
							$sql2 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row1[id_coa]' order by id_coa" );		
							while($row2 = mysqli_fetch_assoc($sql2))
							{
								$saldo = Hitung_LR($row2['id_coa'],$tgl1,$tgl2,$cur);							;
								$pendapatan = $pendapatan + $saldo;
								if ($row2['sub']=='0')
								{
									$saldox = number_format($saldo,0);
								}else{
									$saldox ='';
								}
								$idx="$tgl1|$tgl2|$cur|$row2[id_coa]";								
								$idx=base64_encode($idx); 
								$link = "detil_lr.php?id=$idx";
								$data .= '<tr>
								<td  style="border:none;background:fff;width:65%;text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;'.$row2['nama_coa'].'</td>
								<td  style="border:none;background:fff;width:15%;text-align: right;">
								<a href="'.$link.'"  target= "_blank" title="Detil Transaction">'.$saldox.'</a></td>
								<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
								<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
								</tr>';
							}
						}		
						$pendapatanx = number_format ($pendapatan,0);						
						$data .= '<tr>
							<td  colspan="2" style="border:none;background:none;width:65%;text-align: right;"><b> TOTAL PENDAPATAN</b></td>
							<td  style="border:none;background:none;width:15%;text-align: right;"><b>'.$pendapatanx.'</b></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
						</tr>';
						
						
						
						$data .= '<tr>
							<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
							<td  style="border:none;width:15%;text-align: right;"></td>
							<td  style="border:none;width:5%;text-align: right;"><b></b></td>
							<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';
								
								
								
						
						$hpp =0;
						$sql1 = mysqli_query($koneksi, "select * from m_coa where id_coa ='5' order by id_coa " );		
						while($row1 = mysqli_fetch_assoc($sql1))
						{
							$data .= '<tr>
							<td  style="border:none;background:none;width:65%;text-align: left;padding:5px"><b>'.$row1['nama_coa'].'</b></td>
							<td  style="border:none;background:none;width:15%text-align: left;"></td>
							<td  style="border:none;background:none;width:15%;text-align: left;"></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
							</tr>';
							
							
							$sql2 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row1[id_coa]' order by id_coa" );		
							while($row2 = mysqli_fetch_assoc($sql2))
							{
								$saldo = Hitung_LR($row2['id_coa'],$tgl1,$tgl2,$cur);							;
								$hpp = $hpp + $saldo;
								if ($row2['sub']=='0')
								{
									$saldox = number_format($saldo,0);
								}else{
									$saldox ='';
								}
								$idx="$tgl1|$tgl2|$cur|$row2[id_coa]";								
								$idx=base64_encode($idx); 
								$link = "detil_lr.php?id=$idx";
								$data .= '<tr>
								<td  style="border:none;background:fff;width:65%;text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;'.$row2['nama_coa'].'</td>
								<td  style="border:none;background:fff;width:15%;text-align: right;">
								<a href="'.$link.'"  target= "_blank" title="Detil Transaction">'.$saldox.'</a></td>
								<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
								<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
								</tr>';
							}
						}		
						$hppx = number_format ($hpp,0);						
						$data .= '<tr>
							<td  colspan="2" style="border:none;background:none;width:65%;text-align: right;"><b> TOTAL HPP</b></td>
							<td  style="border:none;background:none;width:15%;text-align: right;"><b>'.$hppx.'</b></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
						</tr>';
						
						$data .= '<tr>
							<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
							<td  style="border:none;width:15%;text-align: right;"></td>
							<td  style="border:none;width:5%;text-align: right;"><b></b></td>
							<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';
						
						$laba_kotor = $pendapatan - $hpp;
						$laba_kotorx = number_format ($laba_kotor,0);
						$data .= '<tr>
							<td  colspan="2" style="border:none;background:none;width:65%;text-align: right;"><b> LABA KOTOR</b></td>
							<td  style="border:none;background:none;width:15%;text-align: right;"><b>'.$laba_kotorx.'</b></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
						</tr>';
						
						
						
						
						
						
						$data .= '<tr>
							<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
							<td  style="border:none;width:15%;text-align: right;"></td>
							<td  style="border:none;width:5%;text-align: right;"><b></b></td>
							<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';
						
						$biaya =0;
						$sql1 = mysqli_query($koneksi, "select * from m_coa where id_coa ='6' order by id_coa " );		
						while($row1 = mysqli_fetch_assoc($sql1))
						{
							$data .= '<tr>
							<td  style="border:none;background:none;width:65%;text-align: left;padding:5px"><b>'.$row1['nama_coa'].'</b></td>
							<td  style="border:none;background:none;width:15%text-align: left;"></td>
							<td  style="border:none;background:none;width:15%;text-align: left;"></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
							</tr>';
							
							
							$sql2 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row1[id_coa]' order by id_coa" );		
							while($row2 = mysqli_fetch_assoc($sql2))
							{
								$saldo = Hitung_LR($row2['id_coa'],$tgl1,$tgl2,$cur);							;
								$biaya = $biaya + $saldo;
								if ($row2['sub']=='0')
								{
									$saldox = number_format($saldo,0);
								}else{
									$saldox ='';
								}
								$idx="$tgl1|$tgl2|$cur|$row2[id_coa]";								
								$idx=base64_encode($idx); 
								$link = "detil_lr.php?id=$idx";
								$data .= '<tr>
								<td  style="border:none;background:fff;width:65%;text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;'.$row2['nama_coa'].'</td>
								<td  style="border:none;background:fff;width:15%;text-align: right;">
								<a href="'.$link.'"  target= "_blank" title="Detil Transaction">'.$saldox.'</a></td>
								<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
								<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
								</tr>';
								
								$sql3 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row2[id_coa]' order by id_coa" );		
								while($row3 = mysqli_fetch_assoc($sql3))
								{
									$saldo = Hitung_LR($row3['id_coa'],$tgl1,$tgl2,$cur);							;
									$biaya = $biaya + $saldo;
									if ($row3['sub']=='0')
									{
										$saldox = number_format($saldo,0);
									}else{
										$saldox ='';
									}
									$idx="$tgl1|$tgl2|$cur|$row3[id_coa]";								
									$idx=base64_encode($idx); 
									$link = "detil_lr.php?id=$idx";
									$data .= '<tr>
									<td  style="border:none;background:fff;width:65%;text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row3['nama_coa'].'</td>
									<td  style="border:none;background:fff;width:15%;text-align: right;">
									<a href="'.$link.'"  target= "_blank" title="Detil Transaction">'.$saldox.'</a></td>
									<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
									<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
									</tr>';
								}
							}
						}		
						$biayax = number_format ($biaya,0);						
						$data .= '<tr>
							<td  colspan="2" style="border:none;background:none;width:65%;text-align: right;"><b> TOTAL BIAYA</b></td>
							<td  style="border:none;background:none;width:15%;text-align: right;"><b>'.$biayax.'</b></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
						</tr>';
						
						$data .= '<tr>
							<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
							<td  style="border:none;width:15%;text-align: right;"></td>
							<td  style="border:none;width:5%;text-align: right;"><b></b></td>
							<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';
						
						
						$laba = $laba_kotor - $biaya;
						$labax = number_format ($laba,0);
						if($laba < 0 )
						{
							$ket = 'RUGI';
						}else{
							$ket = 'LABA';
						}
						$data .= '<tr>
							<td  colspan="2" style="border:none;background:none;width:65%;text-align: right;"><b>'.$ket.'  BERSIH</b></td>
							<td  style="border:none;background:none;width:15%;text-align: right;"><b>'.$labax.'</b></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
						</tr>';
					
					 $data .= '</table>					
				</div>
			 </div>';
			 
			 
	
	
			 
echo $data;	
}

?>