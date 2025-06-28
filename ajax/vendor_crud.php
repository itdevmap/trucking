<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$sql = mysqli_query($koneksi,"select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='9' ");
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
					<th rowspan="2" width="42%" style="text-align: center;">VENDOR NAME</th>
					<th rowspan="2" width="6%" style="text-align: center;">CODE</th>
					<th rowspan="2" width="10%" style="text-align: center;">CONTACT PERSON</th>
					<th rowspan="2" width="8%" style="text-align: center;">PHONE</th>
					<th rowspan="2" width="16%" style="text-align: center;">EMAIL</th>
					<th rowspan="2" width="7%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>						
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
	
	$SQL = "select * from m_vendor_tr where nama_vendor LIKE '%$search_name%'  order by nama_vendor LIMIT $offset, $jmlperhalaman";	
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
			$xy1="View|$row[id_vendor]";
			$xy1=base64_encode($xy1);
			$link = "cust_data.php?id=$xy1";
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:left">'.$row['nama_vendor'].'</td>
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
			if($m_edit == '1' ){
				$data .= '<td>
						<button class="btn btn-block btn-default" 
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetData('.$row['id_vendor'].')"   >
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
				
				$pq = mysqli_query($koneksi, "select count(*) as jml from m_vendor_tr where nama_vendor LIKE '%$search_name%' ");
				
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
		$id_vendor = $_POST['id_vendor'];
		$nama_vendor = trim(addslashes(strtoupper($_POST['nama_vendor'])));
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
			$sql = "INSERT INTO m_vendor_tr (nama_vendor, caption, kontak, alamat, telp, email, status, created, tanggal) values
					('$nama_vendor','$caption','$kontak','$alamat','$telp','$email','$stat','$id_user',  '$tanggal')";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update m_vendor_tr set 
					nama_vendor = '$nama_vendor',
					caption = '$caption',
					kontak= '$kontak',
					alamat = '$alamat',
					telp = '$telp',
					status = '$stat',
					email = '$email'
					where id_vendor = '$id_vendor'	";
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
    $query = "select * from m_vendor_tr where id_vendor  = '$id'";
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

	
}else if ($_GET['type'] == "ListCust")
{	
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="7%" style="text-align: center;">NO</th>
					<th width="83%" style="text-align: center;">PARTNER NAME</th>
					<th width="10%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "select * from m_vendor_tr where nama_vendor LIKE '%$cari%' and status = '1'  order by nama_vendor LIMIT 0, 25";
	
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
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihCust('.$row['id_vendor'].')" >'.$row['nama_vendor'].'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihCust('.$row['id_vendor'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px">Add</button>
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
	

}

?>