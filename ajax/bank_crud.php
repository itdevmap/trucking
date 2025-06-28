<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses where id_role = '$id_role'  and id_menu ='15' ");
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
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="30%" style="text-align: center;">CASH/BANK NAME</th>
					<th rowspan="2" width="15%" style="text-align: center;">ACC NUMBER</th>	
					<th rowspan="2" width="26%" style="text-align: center;">ACC NAME</th>
					<th rowspan="2" width="5%" style="text-align: center;">CUR</th>
					<th rowspan="2" width="5%" style="text-align: center;">INV</th>
					<th rowspan="2" width="8%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="7%" style="text-align: center;">STATUS</th>
					<th rowspan="2" width="3%" style="text-align: center;">EDIT</th>	
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
	$SQL = "select * from m_bank where nama_bank LIKE '%$cari%' order by nama_bank  LIMIT $offset, $jmlperhalaman";	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			if($row['invoice'] == '1')
			{
				$inv = 'Yes';
			}else{
				$inv = 'No';
			}	
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:left">'.$row['nama_bank'].'</td>
				<td style="text-align:center">'.$row['no_bank'].'</td>
				<td style="text-align:center">'.$row['an'].'</td>				
				<td style="text-align:center">'.$row['cur'].'</td>
				<td style="text-align:center">'.$inv.'</td>
				<td style="text-align:center">'.$row['created'].'</td>';					
				if($row['status'] =='0'){
				$data .= '<td style="text-align:center">
					<span class="label label-danger" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
						&nbsp;&nbsp;In Active&nbsp;&nbsp;
					</span>
					</td>';
				} else if($row['status'] =='1'){
				$data .= '<td style="text-align:center"> 
					<span class="label label-success" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
					&nbsp;&nbsp;&nbsp;&nbsp; Active &nbsp;&nbsp;&nbsp;
					</span>
					</td>';
				}
				if($m_edit == '1'  ){
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_bank'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				$data .='</tr>';
    		$number++;
    	}		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
	
	$data .= '<div class="paginate paginate-dark wrapper">
				<ul>';
				$pq = mysqli_query($koneksi, "select count(*) as jml from m_bank where nama_bank LIKE '%$cari%' ");					
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

}else if ($_POST['type'] == "add"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$nama_bank = $_POST['nama_bank'];
		$no_bank = $_POST['no_bank'];
		$cur = $_POST['cur'];
		$an = $_POST['an'];
		$inv = $_POST['inv'];
		$saldo = $_POST['saldo'];
		$alamat = $_POST['alamat'];
		$jenis = $_POST['jenis'];
		$tipe = $_POST['tipe'];
		$stat = $_POST['stat'];
		$mode = $_POST['mode'];		
		$swift = strtoupper($_POST['swift']);	
		$kcp = strtoupper($_POST['kcp']);
		$an = strtoupper($an);
		
		if($mode == 'Add')
		{			
			$nama_coa = "$nama_bank ($no_bank)";
			
			$sql = "INSERT INTO m_coa (id_parent, nama_coa, level, sub, type,kunci) values
					('$tipe','$nama_coa','4','0','1', '1') ";
			$hasil=mysqli_query($koneksi, $sql);
		
			$pq = mysqli_query($koneksi,"select max(id_coa) as id from m_coa ");
			$rq=mysqli_fetch_array($pq);	
			$id_coa = $rq['id'];
			
			$sql = "INSERT INTO m_bank (id_bank, id_coa,nama_bank,no_bank,cur,alamat,saldo_awal,saldo,status,invoice,an,swift,kcp,created, tipe) values
					('$id_coa', '$id_coa','$nama_bank','$no_bank','$cur','$alamat','$saldo','$saldo','$stat','$inv','$an','$swift','$kcp','$id_user', '$tipe')";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$nama_coa = "$nama_bank ($no_bank)";
			$sql = "update m_coa set nama_coa = '$nama_coa', id_parent = '$tipe' 	where id_coa = '$id'					";
			$hasil=mysqli_query($koneksi, $sql);
			
			$sql = "update m_bank set
					id_coa = '$jenis',
					nama_bank = '$nama_bank',
					no_bank = '$no_bank',
					cur = '$cur',
					alamat = '$alamat',
					status = '$stat',
					swift = '$swift',
					invoice = '$inv',
					kcp='$kcp',
					tipe = '$tipe',
					saldo = '$saldo',
					an= '$an'					
					where id_bank = '$id'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Cash/Bank Name has found...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
	
}else if ($_POST['type'] == "detil"){
	$id = $_POST['id'];	
    $query = "select * from m_bank where id_bank  = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
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