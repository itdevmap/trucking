<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses where id_role = '$id_role'  and id_menu ='24' ");
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
	
	if($stat == 'Belum Lunas')
	{
		$stat = '0';
	}else if($stat == 'Lunas')
	{
		$stat = '1';
	}
	
	if($field == 'No SJ'){
		$f = 't_jo_mobil.no_sj';		
	}else if($field == 'Supir'){
		$f = 'm_supir.nama_supir';	
	}else{
		$f = 't_jo_mobil.no_sj';
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL</th>
					<th rowspan="2" width="7%" style="text-align: center;">NO SJ</th>
					<th rowspan="2" width="28%" style="text-align: center;">SUPIR</th>
					<th rowspan="2" width="23%" style="text-align: center;">TRIP</th>
					<th rowspan="2" width="7%" style="text-align: center;">UANG JALAN</th>
					<th rowspan="2" width="7%" style="text-align: center;">BAYAR</th>
					<th rowspan="2" width="7%" style="text-align: center;">SISA</th>
					<th rowspan="2" width="7%" style="text-align: center;">STATUS</th>
					<th colspan="2" width="4%" style="text-align: center;">ACTION</th>	
				</tr>
				<tr>	
					<th width="2%" style="text-align: center;">BAYAR</th>		
					<th width="2%" style="text-align: center;">DETAIL</th>		
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
		$SQL = "select t_jo_mobil.*, m_kota.nama_kota as asal, m_kota1.nama_kota as tujuan, m_mobil.no_polisi,
			   m_supir.nama_supir
			   from t_jo_mobil left join m_kota on t_jo_mobil.id_asal = m_kota.id_kota
			   left join m_kota as m_kota1 on t_jo_mobil.id_tujuan = m_kota1.id_kota
			   left join m_mobil on t_jo_mobil.id_mobil = m_mobil.id_mobil
			   left join m_supir on t_jo_mobil.id_supir = m_supir.id_supir
			   where t_jo_mobil.jenis = '0' and t_jo_mobil.komisi > 0 and 
			   t_jo_mobil.tgl_sj between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  
				  order by t_jo_mobil.tgl_sj desc
			  LIMIT $offset, $jmlperhalaman";
	}else{
		$SQL = "select t_jo_mobil.*, m_kota.nama_kota as asal, m_kota1.nama_kota as tujuan, m_mobil.no_polisi,
			   m_supir.nama_supir
			   from t_jo_mobil left join m_kota on t_jo_mobil.id_asal = m_kota.id_kota
			   left join m_kota as m_kota1 on t_jo_mobil.id_tujuan = m_kota1.id_kota
			   left join m_mobil on t_jo_mobil.id_mobil = m_mobil.id_mobil
			   left join m_supir on t_jo_mobil.id_supir = m_supir.id_supir
			   where t_jo_mobil.jenis = '0' and t_jo_mobil.komisi > 0 and 
			   t_jo_mobil.tgl_sj between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and t_jo_mobil.uj_status = '$stat' 
				  order by t_jo_mobil.tgl_sj desc
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
			$tanggal = ConverTgl($row['tgl_sj']);
			$sisa = $row['uj'] - $row['uj_bayar'];
			$tagihan = number_format($row['uj'],0);
			$bayar = number_format($row['uj_bayar'],0);
			$sisax = number_format($sisa,0);
			
			$t_tagihan = $t_tagihan + $row['uj'];
			$t_bayar = $t_bayar + $row['uj_bayar'];
					
			if($sisa <= 0)
			{
				if($row['uj'] > 0 )
				{
					$label = 'success';
					$status = 'Lunas';
					$s = '1';
				}else{
					$label = 'danger';
					$status = 'Belum Lunas';
					$s = '0';
				}
				
			}else{
				$label = 'danger';
				$status = 'Belum Lunas';
				$s = '0';
			}
			$posisi++;		
			
			$update = mysqli_query($koneksi, "update t_jo_mobil set uj_status = '$s' where id_detil = '$row[id_detil]'  ");
			
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>';
				$data .= '<td style="text-align:center">'.$tanggal.'</td>';	
				$data .= '<td style="text-align:center">'.$row['no_sj'].'</td>';	
				$data .= '<td style="text-align:left">'.$row['nama_supir'].'</td>';					
				$data .= '<td style="text-align:center">'.$row['asal'].' - '.$row['tujuan'].'</td>
				<td style="text-align:right;background:#e48f0f;color:#fff">'.$tagihan.'</td>
				<td style="text-align:right;background:#406a94;color:#fff">'.$bayar.'</td>
				<td style="text-align:right;background:#4bc343;color:#fff">'.$sisax.'</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';	
				
				if($m_add == '1' && $s == '0' ){					
					$data .= '<td>
						<button class="btn btn-block btn-default" title="Add Payment"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetPayment('.$row['id_detil'].')">
									<span class="fa  fa-plus-square" ></span>
						</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}	
				$data .='<td   style="text-align:center">
					<button class="btn btn-block btn-default" title="List Payment"
						style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
						onClick="javascript:ListPayment('.$row['id_detil'].')"  >
						<span class="glyphicon glyphicon-search" ></span>
					</button>	
					</td>';	
				
				$data .='</tr>';
    		$number++;
    	}		
		
		$t_tagihanx = number_format($t_tagihan,0);
		$t_bayarx = number_format($t_bayar,0);
		$t_sisa = $t_tagihan - $t_bayar;
		$t_sisax = number_format($t_sisa,0);
		$data .= '<tr>';
		$data .= '<td colspan= "5" style="text-align:right;background:#eaebec;color:#000"><b>Total  :&nbsp;&nbsp;&nbsp;</b></td>	
					<td style="text-align:right;background:#e48f0f;color:#fff"><b>'.$t_tagihanx.'</b></td>
					<td style="text-align:right;background:#406a94;color:#fff"><b>'.$t_bayarx.'</b></td>
					<td style="text-align:right;background:#4bc343;color:#fff"><b>'.$t_sisax.'</b></td>
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
					$pq = mysqli_query($koneksi, "select count(t_jo_mobil.id_detil) as jml
				   from t_jo_mobil left join m_kota on t_jo_mobil.id_asal = m_kota.id_kota
				   left join m_kota as m_kota1 on t_jo_mobil.id_tujuan = m_kota1.id_kota
				   left join m_mobil on t_jo_mobil.id_mobil = m_mobil.id_mobil
				   left join m_supir on t_jo_mobil.id_supir = m_supir.id_supir
				   where t_jo_mobil.jenis = '0' and t_jo_mobil.komisi > 0 and 
				   t_jo_mobil.tgl_sj between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  ");
				}else{
					$pq = mysqli_query($koneksi, "select count(t_jo_mobil.id_detil) as jml
				   from t_jo_mobil left join m_kota on t_jo_mobil.id_asal = m_kota.id_kota
				   left join m_kota as m_kota1 on t_jo_mobil.id_tujuan = m_kota1.id_kota
				   left join m_mobil on t_jo_mobil.id_mobil = m_mobil.id_mobil
				   left join m_supir on t_jo_mobil.id_supir = m_supir.id_supir
				   where t_jo_mobil.jenis = '0' and t_jo_mobil.komisi > 0 and 
				   t_jo_mobil.tgl_sj between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and t_jo_mobil.uj_status = '$stat' ");
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

	
	
	
}else if ($_POST['type'] == "Detil_Komisi"){
	$id = $_POST['id'];	
    $query = "select t_jo_mobil.*, m_kota.nama_kota as asal, m_kota1.nama_kota as tujuan, m_mobil.no_polisi,
			   m_supir.nama_supir
			   from t_jo_mobil left join m_kota on t_jo_mobil.id_asal = m_kota.id_kota
			   left join m_kota as m_kota1 on t_jo_mobil.id_tujuan = m_kota1.id_kota
			   left join m_mobil on t_jo_mobil.id_mobil = m_mobil.id_mobil
			   left join m_supir on t_jo_mobil.id_supir = m_supir.id_supir  
			where t_jo_mobil.id_detil  = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }
    $response = array();
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response = $row;
        }
    }
    else
    {
        $response['status'] = 200;
        $response['message'] = "Data not found!";
    }
    echo json_encode($response);
	
}else if ($_POST['type'] == "Del_Data"){
	$id = $_POST['id']; 
	$jo_no = $_POST['jo_no']; 	
	
    $query = "DELETE FROM t_jo_pr_detil WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }
	
}else if ($_POST['type'] == "Add_Data"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$id_cost = $_POST['id_cost'];
		$id_pr = $_POST['id_pr'];
		$id_jo_mobil = $_POST['id_jo_mobil'];
		$qty = $_POST['qty'];
		$unit = $_POST['unit'];
		$price = $_POST['price'];
		$mode = $_POST['mode'];	
		$qty = str_replace(",","", $qty);
		$price = str_replace(",","", $price);
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO t_jo_pr_detil (id_pr, id_cost, qty, unit, harga) values
					('$id_pr', '$id_cost','$qty','$unit','$price')";
			$hasil= mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update t_jo_pr_detil set 
					id_cost = '$id_cost',
					unit = '$unit',
					qty = '$qty',
					harga = '$price'
					where id_detil = '$id'	";
			$hasil=mysqli_query($koneksi,$sql);
		}
		if (!$hasil) {
	       
			echo "Data Error...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}		
	
	
}else if ($_POST['type'] == "Detil_Komisi"){
	$id = $_POST['id'];	
    $query = "select t_jo_pr.*, m_vendor.nama_vendor from
				t_jo_pr inner join m_vendor on  t_jo_pr.id_vendor = m_vendor.id_vendor 
			    where t_jo_pr.id_pr = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }
    $response = array();
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response = $row;
        }
    }
    else
    {
        $response['status'] = 200;
        $response['message'] = "Data not found!";
    }
    echo json_encode($response);
	
	
}else if ($_POST['type'] == "AddPayment"){		
	if($_POST['id_sj'] != '' )
	{		
		$id_sj = $_POST['id_sj'];
		$tanggal = $_POST['tanggal'];
		$jumlah = $_POST['jumlah'];
		$pph = $_POST['pph'];
		$id_bank = $_POST['id_bank'];
		$no_sj = $_POST['no_sj'];
		$tipe = $_POST['tipe'];
		$nama = $_POST['nama'];
		
		$jumlah = str_replace(",","", $jumlah);
		$jumlah_bayar = str_replace(",","", $jumlah);
		$pph = str_replace(",","", $pph);
		$tanggalx = ConverTglSql($tanggal);	
	
		//JURNAL BAYAR
		$jenis_jurnal = '1';
		$tgl_jurnal = $tanggal;		
		$ket = "UANG JALAN SUPIR $nama ($no_sj)";	
		
		if($tipe == '1')
		{
			$id_coa1 = $s_hpp_lcl;
		}else{
			$id_coa1 = $s_hpp_fcl;
		}
		
		$jumlah1 = $jumlah_bayar + $pph;
		
		$id_coa2 = $id_bank ;
		$jumlah2 = $jumlah_bayar;	
		
		if($pph > 0)
		{
			$id_coa4 = $pph23_keluaran;
			$jumlah4 = $pph;
			$id_bank4 =0;
		}
		
				
		$cur = 'IDR';
		$jumlah = $jumlah_bayar + $pph ;		
		$jenis = '1';
		$kurs = $rate;
		$id_jurnal = Add_Jurnal($tgl_jurnal,$jenis,$ket,$cur,$kurs,$jumlah,
						$id_coa1,$jumlah1,$id_coa2,$jumlah2,$id_coa3,$jumlah3,$id_coa4,$jumlah4,$id_coa5,$jumlah5,
						$id_coa6,$jumlah6,$id_coa7,$jumlah7,$id_coa8,$jumlah8, $id_user );
	
		$sql = "INSERT INTO t_uj_bayar (id_jo_mobil, id_jurnal, bayar, id_bank) 
		    values ('$id_sj','$id_jurnal','$jumlah_bayar', '$id_bank')";
		$hasil=mysqli_query($koneksi, $sql);
	
		$bayar = $jumlah_bayar + $pph;
		$sql = "UPDATE t_jo_mobil set uj_bayar =  uj_bayar + '$bayar' where id_detil = '$id_sj' ";
		$hasil = mysqli_query($koneksi, $sql);
		
		if (!$hasil) {
	        			
			exit(mysqli_error());
			echo "Data Invalid...!";
	    }
		else
		{	
			echo "Data Saved...!";
		}
	}	

}else if($_GET['type'] == "ListPayment")
{
	$id = $_GET['id'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="12%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="12%" style="text-align: center;">NO. JURNAL</th>
					<th rowspan="2" width="48%" style="text-align: center;">CASH/BANK</th>	
					<th rowspan="2" width="13%" style="text-align: center;">JUMLAH</th>	
					<th rowspan="2" width="10%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>				
				</tr>
			</thead>';
	$t1="select t_uj_bayar.*, t_jurnal.id_user, t_jurnal.jumlah, t_jurnal.tgl_jurnal, t_jurnal.no_jurnal,m_bank.nama_bank, m_bank.no_bank from
	        t_uj_bayar inner join t_jurnal on t_uj_bayar.id_jurnal = t_jurnal.id_jurnal
			left join m_bank on t_uj_bayar.id_bank = m_bank.id_bank
			where t_uj_bayar.id_jo_mobil = '$id' order by t_jurnal.tgl_jurnal";
	$h1=mysqli_query($koneksi, $t1);   
	while ($d1=mysqli_fetch_array($h1))		
	{
		$tanggal=ConverTgl($d1['tgl_jurnal']);	
		
		$bayar = number_format($d1['bayar'],0);
		$pph23 = number_format($d1['pph'],0);		
		$amount = $d1['bayar'] + $d1['pph'];
		$amountx = number_format($amount,0);
		$total = $total + $amount;
		$n++;
		$data .= '<tr>							
			<td style="text-align:center">'.$n.'.</td>
			<td style="text-align:center">'.$tanggal.'</td>	
			<td style="text-align:center">'.$d1['no_jurnal'].'</td>	
			<td style="text-align:left">'.$d1['nama_bank'].' ('.$d1['no_bank'].')</td>	
			<td style="text-align:right">'.$amountx.'</td> 
			<td style="text-align:center">'.$d1['id_user'].'</td>';	
		
			if($m_del == '1' ){
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Delete"
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:DelPayment('.$d1['id_bayar'].')"  >
		     		<span class="fa fa-close " ></span>
					</button></td>';
			}
			else
			{
				$data .='<td></td>';
			}				
		$data .='</tr>';
	}
	$totalx = number_format($total,0);
	$data .= '<tr>							
			<td colspan = "3" style="text-align:center;background:#eaebec"></td>	
			<td colspan= "1" style="text-align:right;background:#406a94;color:#fff">Total Pembayaran</td> 
			<td style="text-align:right;background:#406a94;color:#fff">'.$totalx.'</td>';	
	$data .='</tr>';
	$pq = mysqli_query($koneksi, "select * from t_jo_mobil where id_detil = '$id'  ");
	$rq=mysqli_fetch_array($pq);	
	$tagihanx = number_format($rq['uj'],0);
	$sisa = $rq['uj'] - $total;
	$data .= '<tr>							
			<td colspan = "3" style="text-align:center;background:#eaebec"></td>	
			<td colspan= "1" style="text-align:right;background:#e48f0f;color:#fff">Uang Jalan</td> 
			<td style="text-align:right;background:#e48f0f;color:#fff">'.$tagihanx.'</td>';	
	$data .='</tr>';
	$sisax = number_format($sisa,0);
	$data .= '<tr>							
			<td colspan = "3" style="text-align:center;background:#eaebec"></td>	
			<td colspan= "1" style="text-align:right;background:#4bc343;color:#fff"><b>Sisa</b></td> 
			<td style="text-align:right;background:#4bc343;color:#fff"><b>'.$sisax.'</b></td>';	
	$data .='</tr>';
    $data .= '</table>';
    echo $data;	

}else if ($_POST['type'] == "DelPayment"){
	$id = $_POST['id']; 
	$pq = mysqli_query($koneksi, "select * from t_uj_bayar where id_bayar = '$id'  ");
	$rq=mysqli_fetch_array($pq);
	$id_jurnal = $rq['id_jurnal'];
	Del_Jurnal($id_jurnal);
	$jumlah = $rq['bayar'] + $rq['pph'];	
	
	$update = mysqli_query($koneksi, "UPDATE t_jo_mobil set uj_bayar =  uj_bayar - '$jumlah'  where id_detil = '$rq[id_jo_mobil]' ");
    $query = "DELETE FROM  t_uj_bayar WHERE id_bayar = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
    }		
	
}

?>