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
	$cur = trim($_GET['cur']);
	$tgl1 = $_GET['tgl1'];
	$tgl2 = $_GET['tgl2'];
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl0);
	
	if($cur == 'IDR')
	{
		$curx = '1';
	}else{
		$curx = '2';
	}	

	//ASET
	$data = '<div class="col-md-6" >
				<div class="box box-success box-solid" style="height:600px;padding:5px;border:1px solid #ccc;background:#fff !important;">
					<table  style="width:100%;border:none">
						<tr>
						<td  style="border:none;width:65%;text-align: right;"></td>
						<td  style="border:none;width:15%;text-align: right;"></td>
						<td  style="border:none;width:15%;text-align: right;"></td>
						<td  style="border:none;width:5%;text-align: left;"></td>
						</tr>';
						
						$asset =0;
						$sql1 = mysqli_query($koneksi, "select * from m_coa where id_coa ='1' order by id_coa " );		
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
								$saldo = Hitung_Saldo($row2['id_coa'],$tgl1,$tgl2,$cur);							;
								$total = $total + $saldo;
								if ($row2['sub']=='0')
								{
									$saldox = number_format($saldo,0);
								}else{
									$saldox ='';
								}
								$data .= '<tr>
								<td  style="border:none;background:fff;width:65%;text-align: left;padding:5px"><b>'.$row2['nama_coa'].'</b></td>
								<td  style="border:none;background:fff;width:15%text-align: right;">'.$saldox.'</td>
								<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
								<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
								</tr>';
								
								$total = 0;
								$sql3 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row2[id_coa]' order by id_coa" );		
								while($row3 = mysqli_fetch_assoc($sql3))
								{
									$saldo = Hitung_Saldo($row3['id_coa'],$tgl1,$tgl2,$cur);	
									$total = $total + $saldo;
									if ($row3['sub']=='0')
									{
										$saldox = number_format($saldo,0);
									}else{
										$saldox ='';
									}
									$idx="$tgl1|$tgl2|$cur|$row3[id_coa]";								
									$idx=base64_encode($idx); 
									$link = "detil_neraca.php?id=$idx";
									$data .= '<tr>
									<td  style="border:none;background:fff;width:65%;text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;
									'.$row3['nama_coa'].'</td>
									<td  style="border:none;background:fff;width:15%;text-align: right;">
										<a href="'.$link.'"  target= "_blank" title="Detil Transaction">'.$saldox.'</a>
									</td>
									<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
									<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
									</tr>';
									
									$sql4 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row3[id_coa]' order by id_coa" );		
									while($row4 = mysqli_fetch_assoc($sql4))
									{
									
										$saldo = Hitung_Saldo($row4['id_coa'],$tgl1,$tgl2,$cur);	
										$total = $total + $saldo;
										if ($row4['sub']=='0')
										{
											$saldox = number_format($saldo,0);
										}else{
											$saldox ='';
										}
										$idx="$tgl1|$tgl2|$cur|$row4[id_coa]";								
										$idx=base64_encode($idx); 
										$link = "detil_neraca.php?id=$idx";
										$data .= '<tr>
										<td  style="border:none;background:fff;width:65%;text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;
										&nbsp;&nbsp;&nbsp;&nbsp;'.$row4['nama_coa'].'</td>
										<td  style="border:none;background:fff;width:15%;text-align: right;">
											<a href="'.$link.'"  target= "_blank" title="Detil Transaction">'.$saldox.'</a>
										</td>
										<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
										<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
										</tr>';
									}
								}
								
								$asset = $asset + $total;
								$totalx = number_format ($total,0);
								$data .= '<tr>
									<td  colspan ="1" style="border:none;width:15%;text-align: left;"><b>Total '.$row2['nama_coa'].'</b></td>
									<td  style="border:none;width:15%;text-align: right;"></td>
									<td  style="border:none;width:5%;text-align: right;"><b>'.$totalx.'</b></td>
									<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
								</tr>';
								
								$data .= '<tr>
									<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
									<td  style="border:none;width:15%;text-align: right;"></td>
									<td  style="border:none;width:5%;text-align: right;"><b></b></td>
									<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
								</tr>';
							}
						}		
						$assetx = number_format ($asset,0);
						$data .= '<tr>
									<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
									<td  style="border:none;width:15%;text-align: right;"></td>
									<td  style="border:none;width:5%;text-align: right;"><b></b></td>
									<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
								</tr>';
						$data .= '<tr>
							<td  colspan="1" style="border:none;background:none;width:65%;text-align: left;"><b> TOTAL ASSET</b></td>
							<td  style="border:none;background:none;width:15%;text-align: right;"></td>
							<td  style="border:none;background:none;width:15%;text-align: right;"><b>'.$assetx.'</b></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
						</tr>';
						$data .= '<tr>
									<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
									<td  style="border:none;width:15%;text-align: right;"></td>
									<td  style="border:none;width:5%;text-align: right;"><b></b></td>
									<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
								</tr>';
						
					 $data .= '</table>					
				</div>
			 </div>';
			 
			 
	
	//HUTANG + MODAL
	$data .= '<div class="col-md-6" >
				<div class="box box-success box-solid" style="height:600px;padding:5px;border:1px solid #ccc;background:#fff !important;">
					<table  style="width:100%;border:none">
						<tr>
						<td  style="border:none;width:65%;text-align: right;"></td>
						<td  style="border:none;width:15%;text-align: right;"></td>
						<td  style="border:none;width:15%;text-align: right;"></td>
						<td  style="border:none;width:5%;text-align: left;"></td>
						</tr>';
						
						//HUTANG
						$hutang=0;
						$sql1 = mysqli_query($koneksi, "select * from m_coa where id_coa ='2' order by id_coa ");		
						while($row1 = mysqli_fetch_assoc($sql1))
						{
							$data .= '<tr>
							<td  style="border:none;background:none;width:65%;text-align: left;padding:5px"><b>'.$row1['nama_coa'].'</b></td>
							<td  style="border:none;background:none;width:15%text-align: left;"></td>
							<td  style="border:none;background:none;width:15%;text-align: left;"></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
							</tr>';
							
							$total =0 ;
							$sql2 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row1[id_coa]' order by id_coa");		
							while($row2 = mysqli_fetch_assoc($sql2))
							{
								$saldo = Hitung_Saldo($row2['id_coa'],$tgl1,$tgl2,$cur);	
								$total = $total + $saldo;
								if ($row2['sub']=='0')
								{
									$saldox = number_format($saldo,0);
								}else{
									$saldox ='';
								}
								$hutang = $hutang + $saldo;
								$idx="$tgl1|$tgl2|$cur|$row2[id_coa]";								
								$idx=base64_encode($idx); 
								$link = "detil_neraca.php?id=$idx";
								$data .= '<tr>
								<td  style="border:none;background:fff;width:65%;text-align: left;">'.$row2['nama_coa'].'</td>
								<td  style="border:none;background:fff;width:15%;text-align: right;">
									<a href="'.$link.'"  target= "_blank" title="Detil Transaction">'.$saldox.'</a>
								</td>
								<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
								<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
								</tr>';
								
								$sql3 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row2[id_coa]' order by id_coa");		
								while($row3 = mysqli_fetch_assoc($sql3))
								{
									$saldo = Hitung_Saldo($row3['id_coa'],$tgl1,$tgl2,$cur);	
									$total = $total + $saldo;
									if ($row3['sub']=='0')
									{
										$saldox = number_format($saldo,0);
									}else{
										$saldox ='';
									}
									$hutang = $hutang + $saldo;
									$idx="$tgl1|$tgl2|$cur|$row3[id_coa]";								
									$idx=base64_encode($idx); 
									$link = "detil_neraca.php?id=$idx";
									$data .= '<tr>
									<td  style="border:none;background:fff;width:65%;text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;'.$row3['nama_coa'].'</td>
									<td  style="border:none;background:fff;width:15%;text-align: right;">
										<a href="'.$link.'"  target= "_blank" title="Detil Transaction">'.$saldox.'</a>
									</td>
									<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
									<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
									</tr>';
									
								}
							}
							$hutangx = number_format ($hutang,0);
							$data .= '<tr>
										<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
										<td  style="border:none;width:15%;text-align: right;"></td>
										<td  style="border:none;width:5%;text-align: right;"><b></b></td>
										<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
									</tr>';
							$data .= '<tr>
								<td  colspan="1" style="border:none;background:none;width:65%;text-align: left;"><b> TOTAL HUTANG</b></td>
								<td  style="border:none;background:none;width:15%;text-align: right;"></td>
								<td  style="border:none;background:none;width:15%;text-align: right;"><b>'.$hutangx.'</b></td>
								<td  style="border:none;background:none;width:5%;text-align: left;"></td>
							</tr>';
						}		
						
						$data .= '<tr>
							<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
							<td  style="border:none;width:15%;text-align: right;"></td>
							<td  style="border:none;width:5%;text-align: right;"><b></b></td>
							<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';
						$data .= '<tr>
							<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
							<td  style="border:none;width:15%;text-align: right;"></td>
							<td  style="border:none;width:5%;text-align: right;"><b></b></td>
							<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';			
						
						
						
						$modal=0;
						$sql1 = mysqli_query($koneksi, "select * from m_coa where id_coa ='3' order by id_coa ");		
						while($row1 = mysqli_fetch_assoc($sql1))
						{
							$data .= '<tr>
							<td  style="border:none;background:none;width:65%;text-align: left;"><b>'.$row1['nama_coa'].'</b></td>
							<td  style="border:none;background:none;width:15%text-align: left;"></td>
							<td  style="border:none;background:none;width:15%;text-align: left;"></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
							</tr>';
							
							$data .= '<tr>
							<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
							<td  style="border:none;width:15%;text-align: right;"></td>
							<td  style="border:none;width:5%;text-align: right;"><b></b></td>
							<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';
						
							$sql2 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row1[id_coa]' order by id_coa");		
							while($row2 = mysqli_fetch_assoc($sql2))
							{
								$saldo = Hitung_Saldo($row2['id_coa'],$tgl1,$tgl2,$cur);	
								$total = $total + $saldo;
								if ($row2['sub']=='0')
								{
									$saldox = number_format($saldo,0);
								}else{
									$saldox ='';
								}
								$modal = $modal + $saldo;
								$idx="$tgl1|$tgl2|$cur|$row2[id_coa]";								
								$idx=base64_encode($idx); 
								$link = "detil_neraca.php?id=$idx";
								$data .= '<tr>
								<td  style="border:none;background:fff;width:65%;text-align: left;">'.$row2['nama_coa'].'</td>
								<td  style="border:none;background:fff;width:15%;text-align: right;">
									<a href="'.$link.'"  target= "_blank" title="Detil Transaction">'.$saldox.'</a>
								</td>
								<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
								<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
								</tr>';
								
								$sql3 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row2[id_coa]' order by id_coa");		
								while($row3 = mysqli_fetch_assoc($sql3))
								{
									$saldo = Hitung_Saldo($row3['id_coa'],$tgl1,$tgl2,$cur);	
									$total = $total + $saldo;
									if ($row3['sub']=='0')
									{
										$saldox = number_format($saldo,0);
									}else{
										$saldox ='';
									}
									$modal = $modal + $saldo;
									$idx="$tgl1|$tgl2|$cur|$row3[id_coa]";								
									$idx=base64_encode($idx); 
									$link = "detil_neraca.php?id=$idx";
									$data .= '<tr>
									<td  style="border:none;background:fff;width:65%;text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;'.$row3['nama_coa'].'</td>
									<td  style="border:none;background:fff;width:15%;text-align: right;">
										<a href="'.$link.'"  target= "_blank" title="Detil Transaction">'.$saldox.'</a>
									</td>
									<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
									<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
									</tr>';
									
								}
							}
						}
						
						$laba = $asset - ( $hutang + $modal) ;
						$labax = number_format($laba,0);
						if($laba < 0 )
						{
							$ket = 'Rugi Bersih';
						}else{
							$ket = 'Laba Bersih';
						}
						
						$data .= '<tr>
						<td  style="border:none;background:fff;width:65%;text-align: left;">'.$ket.'</td>
						<td  style="border:none;background:fff;width:15%;text-align: right;">'.$labax.'</a></td>
						<td  style="border:none;background:fff;width:15%;text-align: left;"></td>
						<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';
						
						
						$total_modal = $modal + $laba;
						$total_modalx = number_format($total_modal,0);
						$data .= '<tr>
							<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
							<td  style="border:none;width:15%;text-align: right;"></td>
							<td  style="border:none;width:5%;text-align: right;"><b></b></td>
							<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';
						$data .= '<tr>
							<td  style="border:none;background:none;width:65%;text-align: left;"><b>TOTAL MODAL</b></td>
							<td  style="border:none;background:none;width:15%text-align: left;"></td>
							<td  style="border:none;background:none;width:15%;text-align: right;"><b>'.$total_modalx.'</b></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
							</tr>';
						
						$total_hutang_modal = $modal + $laba + $hutang;
						$total_hutang_modalx = number_format($total_hutang_modal,0);	
						$data .= '<tr>
							<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
							<td  style="border:none;width:15%;text-align: right;"></td>
							<td  style="border:none;width:5%;text-align: right;"><b></b></td>
							<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';
						$data .= '<tr>
							<td  style="border:none;background:none;width:65%;text-align: left;"><b>TOTAL HUTANG DAN MODAL</b></td>
							<td  style="border:none;background:none;width:15%text-align: left;"></td>
							<td  style="border:none;background:none;width:15%;text-align: right;"><b>'.$total_hutang_modalx.'</b></td>
							<td  style="border:none;background:none;width:5%;text-align: left;"></td>
							</tr>';	
						$data .= '<tr>
							<td  colspan ="1" style="border:none;width:15%;text-align: left;"></td>
							<td  style="border:none;width:15%;text-align: right;"></td>
							<td  style="border:none;width:5%;text-align: right;"><b></b></td>
							<td  style="border:none;background:fff;width:5%;text-align: left;"></td>
						</tr>';	
							
							
					 $data .= '</table>					
				</div>
			 </div>';	 
			 
echo $data;	
}

?>