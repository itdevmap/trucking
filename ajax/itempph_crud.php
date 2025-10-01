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

// ------------ READ DATA ------------
if ($_GET['type'] == "read"){
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	$search_name = $_GET['search_name'];
	$field = $_GET['field'];	
	$tipe = $_GET['tipe'];
	$id = $_GET['id'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>		
					<th rowspan="2" width="10%" style="text-align: center;">CUSTOMER NAME</th>
					<th rowspan="2" width="6%" style="text-align: center;">ITEM CODE</th>
					<th rowspan="2" width="5%" style="text-align: center;">PPH</th>
					<th rowspan="2" width="6%" style="text-align: center;">CREATED BY</th>
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
	
	$SQL = "
		SELECT
			m_cust_tr.id_cust,
			m_cust_tr.nama_cust,
			t_item_pph.sapitemcode,
			t_item_pph.pph,
			m_user_tr.nama_user
		FROM m_cust_tr 
		LEFT JOIN t_item_pph ON t_item_pph.id_cust = m_cust_tr.id_cust
		LEFT JOIN m_user_tr ON t_item_pph.created_by = m_user_tr.id
		WHERE t_item_pph.sapitemcode LIKE '%$search_name%'  
			AND m_cust_tr.id_cust = '$id' 
		ORDER BY m_cust_tr.nama_cust 
		LIMIT $offset, $jmlperhalaman";	

	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
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
				<td style="text-align:center">'.$row['sapitemcode'].'</td>
				<td style="text-align:center">'.$row['pph'].'</td> 
				<td style="text-align:center">'.$row['nama_user'].'</td>';
	
			$data .='
				<td   style="text-align:center">
					<button class="btn btn-block btn-default" title="List Payment"
						style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
						onClick="javascript:GetData('.$row['id_cust'].')"  >
						<span class="fa fa-edit" ></span>
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
				
				$pq = mysqli_query($koneksi, "select count(*) as jml from t_item_pph where id_cust LIKE '%$id%' ");
				
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
}

?>