<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$sql = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='8' ");
$data=mysqli_fetch_array($sql);
$m_edit = $data['m_edit'];
$m_add = $data['m_add'];
$m_del = $data['m_del'];
$m_view = $data['m_view'];
$m_exe = $data['m_exe'];

if ($_GET['type'] == "read")
{
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	$search_name = $_GET['search_name'];
	$field = $_GET['field'];	
	$tipe = $_GET['tipe'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>		
					<th rowspan="2" width="40%" style="text-align: center;">CUSTOMER NAME</th>
					<th rowspan="2" width="6%" style="text-align: center;">CODE</th>
					<th rowspan="2" width="10%" style="text-align: center;">CONTACT PERSON</th>
					<th rowspan="2" width="8%" style="text-align: center;">PHONE</th>
					<th rowspan="2" width="16%" style="text-align: center;">EMAIL</th>
					<th rowspan="2" width="7%" style="text-align: center;">CREATED</th>					
					<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>		
					<th rowspan="2" width="2%" style="text-align: center;">RATE</th>					
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
	
	$SQL = "select * from m_cust_tr where nama_cust LIKE '%$search_name%'  order by nama_cust LIMIT $offset, $jmlperhalaman";	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			$tanggal = ConverTgl($row['tanggal']);
			$batas = number_format($row['batas'],0);
			$xy1="View|$row[id_cust]";
			$xy1=base64_encode($xy1);
			$link = "cust_data.php?id=$xy1";
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:left">'.$row['nama_cust'].'</td>
				<td style="text-align:center">'.$row['caption'].'</td>
				<td style="text-align:center">'.$row['kontak'].'</td> 	
				<td style="text-align:center">'.$row['telp'].'</td> 
				<td style="text-align:center">'.$row['email'].'</td>
				<td style="text-align:center">'.$row['created'].'</td>';
			
			if($row['status'] =='0' ){
					$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-danger" style="margin:-3px;width:100%;padding:1px;border-radius:1px">&nbsp;In Active &nbsp;</button>
					</td>';
			} else if($row['status'] =='1'){
					$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-success" style="margin:-3px;width:100%;padding:1px;border-radius:1px">Active</button>
					</td>';	
			}
			
			if($m_edit == '1' && $row['id_cust'] != '1'){
				$data .= '<td>
						<button class="btn btn-block btn-default" 
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetData('.$row['id_cust'].')"   >
						<span class="fa fa-edit " ></span>
						</button></td>';					
				}
				else
				{
					$data .='<td></td>';
				}	
			$data .='<td   style="text-align:center">
					<button class="btn btn-block btn-default" title="List Payment"
						style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
						onClick="javascript:ListRate('.$row['id_cust'].')"  >
						<span class="glyphicon glyphicon-search" ></span>
					</button>	
				</td>';			
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
				
				$pq = mysqli_query($koneksi, "select count(*) as jml from m_cust_tr where nama_cust LIKE '%$search_name%' ");
				
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

	
}else if ($_POST['type'] == "AddData"){		
	if($_POST['mode'] != '' )
	{	
		$id_cust = $_POST['id_cust'];
		$nama_cust = trim(addslashes($_POST['nama_cust']));
		$caption = addslashes(strtoupper($_POST['caption']));
		$kontak = addslashes($_POST['kontak']);
		$alamat = addslashes($_POST['alamat']);	
		$telp = addslashes($_POST['telp']);
		$email = addslashes($_POST['email']);
		$stat = $_POST['stat'];
		$mode = $_POST['mode'];
		$tanggal = ConverTglSql($_POST['tanggal']);
		$batas = str_replace(",","", $batas);
		
		if($mode == 'Add')
		{
			$sql = "INSERT INTO m_cust_tr (nama_cust, caption, kontak, alamat, telp, email, status, created, tanggal) values
					('$nama_cust','$caption','$kontak','$alamat','$telp','$email','$stat','$id_user',  '$tanggal')";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update m_cust_tr set 
					nama_cust = '$nama_cust',
					caption = '$caption',
					kontak= '$kontak',
					alamat = '$alamat',
					telp = '$telp',
					status = '$stat',
					email = '$email'
					where id_cust = '$id_cust'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        			
			//exit(mysql_error());
			echo "Partner Name has found...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
}else if ($_POST['type'] == "DetilData"){
	$id = $_POST['id'];	
    $query = "select * from m_cust_tr where id_cust  = '$id'";
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
else if($_GET['type'] == "List_Rate")
{
	$id = $_GET['id'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="5%" style="text-align: center;">NO</th>
					<th rowspan="2" width="30%" style="text-align: center;">ASAL</th>
					<th rowspan="2" width="30%" style="text-align: center;">TUJUAN</th>
					<th rowspan="2" width="16%" style="text-align: center;">JENIS MOBIL</th>
					<th rowspan="2" width="15%" style="text-align: center;">RATE</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>		
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>		
				</tr>
			</thead>';
	$t1="select m_cust_rate_tr.*, m_kota_tr.nama_kota as asal, m_kota_tr1.nama_kota as tujuan 
         	from 
			m_cust_rate_tr left join m_kota_tr on m_cust_rate_tr.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota_tr1 on m_cust_rate_tr.id_tujuan = m_kota_tr1.id_kota	
			where m_cust_rate_tr.id_cust = '$id'
			order by m_kota_tr.nama_kota asc, m_kota_tr1.nama_kota";
	$h1=mysqli_query($koneksi, $t1);   
	while ($d1=mysqli_fetch_array($h1))		
	{
		$rate = number_format($d1['rate'],0);
		$n++;
		$data .= '<tr>							
			<td style="text-align:center">'.$n.'.</td>
			<td style="text-align:center">'.$d1['asal'].'</td>	
			<td style="text-align:center">'.$d1['tujuan'].'</td>
			<td style="text-align:center">'.$d1['jenis_mobil'].'</td>
			<td style="text-align:right">'.$rate.'</td> ';	
		
			if($m_edit == '1' ){
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Delete"
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:GetRate('.$d1['id_rate'].')"  >
		     		<span class="fa fa-edit " ></span>
					</button></td>';
			}
			else
			{
				$data .='<td></td>';
			}	
			if($m_del == '1' ){
				
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Delete"
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:DelRate('.$d1['id_rate'].')"  >
		     		<span class="fa fa-close " ></span>
					</button></td>';
			}
			else
			{
			
				$data .='<td></td>';
			}					
		$data .='</tr>';
	}
	
    echo $data;	


}else if ($_POST['type'] == "Add_Rate"){		
	if($_POST['mode'] != '' )
	{	
		$id_rate = $_POST['id_rate'];
		$id_asal = $_POST['id_asal'];
		$id_tujuan = $_POST['id_tujuan'];
		$jenis_mobil = $_POST['jenis_mobil'];
		$rate = $_POST['rate'];
		$id_cust = $_POST['id_cust'];
		$mode = $_POST['mode'];
		
		$rate = str_replace(",","", $rate);
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO m_cust_rate_tr (id_cust, id_asal, id_tujuan, jenis_mobil, rate) values
					('$id_cust','$id_asal','$id_tujuan','$jenis_mobil', '$rate')";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update m_cust_rate_tr set 
					id_asal = '$id_asal',
					id_tujuan = '$id_tujuan',
					jenis_mobil = '$jenis_mobil',
					rate = '$rate'
					where id_rate = '$id_rate'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Rate has found...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	

}else if ($_POST['type'] == "Detil_Rate"){
	$id = $_POST['id'];	
    $query = "select * from m_cust_rate_tr where id_rate  = '$id'";
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

}else if ($_POST['type'] == "Del_Rate"){
	$id = $_POST['id']; 
    $query = "DELETE FROM m_cust_rate_tr WHERE id_rate = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	

	
}else if ($_GET['type'] == "ListCust")
{	
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="7%" style="text-align: center;">NO</th>
					<th width="83%" style="text-align: center;">CUSTOMER NAME</th>
					<th width="10%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "select * from m_cust_tr where nama_cust LIKE '%$cari%' and status = '1'  order by nama_cust LIMIT 0, 25";
	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;
			$data .= '<tr>';		
			$data .= '<td style="text-align:center">'.$posisi.'.</td>';
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihCust('.$row['id_cust'].')" >'.$row['nama_cust'].'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihCust('.$row['id_cust'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px"><span class="fa  fa-plus-square"></span></button>
					</td>';		
			$data .='</tr>';
    	}		
    }
    else
    {
    	$data .= '<tr><td colspan="7"></td></tr>';
    }
    $data .= '</table>';
    echo $data;		
	

}else if ($_GET['type'] == "ListCust_Quo")
{	
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="5%" style="text-align: center;">NO</th>
					<th width="12%" style="text-align: center;">DATE</th>
					<th width="12%" style="text-align: center;">QUO NO</th>
					<th width="64%" style="text-align: center;">CUSTOMER</th>
					<th width="7%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "select t_ware_quo.*, m_cust_tr.nama_cust
		  from 
		  t_ware_quo left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
		  where m_cust_tr.nama_cust LIKE '%$cari%' and t_ware_quo.status = '1'  
		  order by t_ware_quo.quo_date desc, m_cust_tr.nama_cust LIMIT 0, 25";
	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;
			$tanggal = ConverTgl($row['quo_date']);
			$data .= '<tr>';		
			$data .= '<td style="text-align:center">'.$posisi.'.</td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihCust('.$row['id_quo'].')" >'.$tanggal.'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihCust('.$row['id_quo'].')" >'.$row['quo_no'].'</a></td>';
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihCust('.$row['id_quo'].')" >'.$row['nama_cust'].'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihCust('.$row['id_quo'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px"><span class="fa  fa-plus-square"></span></button>
					</td>';		
			$data .='</tr>';
    	}		
    }
    else
    {
    	$data .= '<tr><td colspan="7"></td></tr>';
    }
    $data .= '</table>';
    echo $data;		

}else if ($_POST['type'] == "DetilCust_Quo"){
	$id = $_POST['id_quo'];	
    $query = "select t_ware_quo.*, m_cust_tr.nama_cust from 
		t_ware_quo left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
		where t_ware_quo.id_quo  = '$id'";
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