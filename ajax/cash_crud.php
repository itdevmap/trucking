<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses where id_role = '$id_role'  and id_menu ='25' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

if ($_GET['type'] == "Read")
{
	$cari = trim($_GET['cari']);
	$tgl1 = $_GET['tgl1'];
	$tgl2 = $_GET['tgl2'];
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);	
	$field = $_GET['field'];
	$tipe = $_GET['tipe'];
	$cur_filter = $_GET['cur'];
	
	if($field == '#No')
	{
		$f = 't_jurnal.no_jurnal';
	}else if($field == 'Keterangan'){
		$f = 't_jurnal.ket';		
	}else{
		$f = 't_jurnal.no_jurnal';
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL</th>
					<th rowspan="2" width="6%" style="text-align: center;">#NO</th>
					<th rowspan="2" width="60%" style="text-align: center;">KETERANGAN</th>	
					<th colspan="2" width="16%" style="text-align: center;">JUMLAH</th>
					<th rowspan="2" width="8%" style="text-align: center;">SALDO</th>						
				</tr>
				<tr>	
					<th width="8%" style="text-align: center;">DEBET</th>
					<th width="8%" style="text-align: center;">CREDIT</th>
				</tr>';	
				
	if($cur_filter == 'IDR')
	{
		$curx = '1';
	}else{
		$curx = '2';
	}			
	$saldo = Saldo_Neraca($tgl1,$tgl2,$tipe,$curx);
	if($saldo > 0 )
	{
		$total_debet = $saldo;
	}else{
		$total_kredit = $saldo;
	}
	$saldo_debetx = number_format($total_debet,2);
	$saldo_kreditx = number_format($total_kredit,2);
	$data .= '<tr>							
				<td colspan ="4" style="text-align:right;background:#eaebec;"><b>Saldo Akhir : </b></td>
				<td style="text-align:right;background:#000;color:#fff"><b>'.$saldo_debetx.'</b></td>
				<td style="text-align:right;background:#000;color:#fff"><b>'.$saldo_kreditx.'</b></td>';	
	$data .='</tr>';		
	$SQL = "select t_jurnal_detil.*,t_jurnal.cur, t_jurnal.tgl_jurnal,t_jurnal.no_jurnal,t_jurnal.ket,
	        t_jurnal.status as stat,t_jurnal.id_user, t_jurnal.kurs
			from t_jurnal_detil inner join t_jurnal on t_jurnal_detil.id_jurnal = t_jurnal.id_jurnal 
			where t_jurnal_detil.id_coa = '$tipe'  and  t_jurnal.tgl_jurnal between '$tgl1x' and '$tgl2x' 
			and $f LIKE '%$cari%'
			order by t_jurnal.tgl_jurnal asc,t_jurnal.no_jurnal desc";
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$n++;
			$cur_jurnal = $row['cur'];
			$tanggal = ConverTgl($row['tgl_jurnal']);
			
			if($row['kurs'] <= 0)
			{
				$pq = mysqli_query($koneksi, "select * from t_kurs where tanggal = '$row[tgl_jurnal]' ");
				$rq = mysqli_fetch_array($pq);	
				$kurs = $rq['kurs'];
			}else{
				$kurs = $row['kurs'];
			}
			if(empty($kurs))
			{
				$kurs = 1;
			}
			$kursx = number_format($kurs,0);
			
			if($cur_filter == 'USD' && $cur_jurnal == 'IDR')
			{
				$jumlah = $row['jumlah']/$kurs;
			}else if ($cur_filter == 'IDR' && $cur_jurnal == 'USD'){
				$jumlah = $row['jumlah']*$kurs;
			}else{
				$jumlah = $row['jumlah'];
			}
			$amount = number_format($row['jumlah'],2);
			
			if($row['status'] == 'D')
			{
				$deb = $jumlah;
				$kre =0;
				$total_debet = $total_debet + $jumlah;
				}else{
				$kre = $jumlah;
				$total_kredit = $total_kredit +$jumlah;
				$deb =0;
			}	
			$krex = number_format($kre,0);
			$debx = number_format($deb,0);
			$saldo = $saldo + ($deb - $kre);
			$saldox = number_format($saldo,0);
			$data .= '<tr>							
				<td style="text-align:center">'.$n.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>
				<td style="text-align:center">'.$row['no_jurnal'].'</td>
				<td style="text-align:left">'.$row['ket'].'</td>
				<td style="text-align:right">'.$debx.'</td>
				<td style="text-align:right">'.$krex.'</td>
				<td style="text-align:right">'.$saldox.'</td>';				
			$data .='</tr>';
    	}		
		
		$saldo = $saldo;	
		if($saldo < 0)
		{
			$debet = 0;
			$kredit = $saldo ;
		}else{
			$kredit =0;
			$debet = $saldo;
		}
		$debetx = number_format(abs($debet),0);
		$kreditx = number_format(abs($kredit),0);			
		$total_debetx = number_format($total_debet,0);
		$total_kreditx = number_format($total_kredit,0);
		$data .= '<tr>							
					<td colspan ="4" style="text-align:right;background:#eaebec;"><b>Total : </b></td>
					<td style="text-align:right;background:#eaebec;"><b>'.$total_debetx.'</b></td>
					<td style="text-align:right;background:#eaebec;"><b>'.$total_kreditx.'</b></td>';					
		$data .= '<tr>							
					<td colspan ="4" style="text-align:right;background:#eaebec;"><b>Saldo Akhir : </b></td>
					<td style="text-align:right;background:#406a94;color:#fff"><b>'.$debetx.'</b></td>
					<td style="text-align:right;background:#e48f0f;color:#fff"><b>'.$kreditx.'</b></td>';	
		$data .='</tr>';
		
		if($debet > 0)
		{
			$nl = $debet - $kredit;
		}else{
			$nl = $kredit - $debet;
		}
		$tanggal = $_GET['tgl2'];
		$ptgl = explode("-", $tanggal);
		$tgx = $ptgl[0];
		$blx = $ptgl[1];
		$thx = $ptgl[2];
		Update_Saldo($tgx,$blx,$thx,$tipe,$nl,$cur_filter);
		
		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
	
    echo $data;

}else if ($_POST['type'] == "AddPayment"){		
	if($_POST['id_coa'] != '' )
	{		
		$jenis = $_POST['jenis'];
		$tanggal = $_POST['tanggal'];		
		$id_bank = $_POST['id_bank'];
		$id_coa = $_POST['id_coa'];
		$desc = $_POST['desc'];
		$jumlah = $_POST['jumlah'];
		$kurs = $_POST['kurs'];
		$cur = $_POST['cur'];
		$jumlah = str_replace(",","", $jumlah);
		$kurs = str_replace(",","", $kurs);
		$tanggalx = ConverTglSql($tanggal);		
		$tgl_jurnal = $tanggal;		
		$ket = $desc;		
		$jumlah1 = $jumlah;
		$jumlah2 = $jumlah;
		if($jenis == '1')
		{
			$id_coa1 = $id_bank;
			$id_coa2 = $id_coa;
		}else{
			$id_coa1 = $id_coa;
			$id_coa2 = $id_bank;
		}
		$jenis = '3';
		$id_jurnal = Add_Jurnal($tgl_jurnal,$jenis,$ket,$cur,$kurs,$jumlah,
						$id_coa1,$jumlah1,$id_coa2,$jumlah2,$id_coa3,$jumlah3,$id_coa4,$jumlah4,$id_coa5,$jumlah5,
						$id_coa6,$jumlah6,$id_coa7,$jumlah7,$id_coa8,$jumlah8, $id_user );
						
		if (empty($id_jurnal)) {
	        			
			//exit(mysqli_error());
			echo "Data Invalid...!";
	    }
		else
		{	
			$pq = mysqli_query($koneksi,"select * from t_jurnal where id_jurnal = '$id_jurnal'   ");
			$rq=mysqli_fetch_array($pq);	
			$no = $rq['no_jurnal'];
			
			$mode = strtoupper($mode);
			$tanggalku = date("Y-m-d h:i:sa");
			$ket = "ADD CASH/BANK NO. $no";
			$audit = mysqli_query($koneksi,"INSERT INTO m_audit (tanggal,created,ket) values ('$tanggalku','$id_user','$ket')" );
			
			echo "Data Saved...!";
		}
	}		
	
}else if ($_POST['type'] == "CekCur"){
	$id = $_POST['id_bank'];	
    $query = "select * from m_bank where id_bank = '$id' ";
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

}

?>