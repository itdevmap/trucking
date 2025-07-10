<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='11' ");
$rq=mysqli_fetch_array($pq);	

$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

if ($_GET['type'] == "Read")
{
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	$search_name = $_GET['search_name'];
	$field = $_GET['field'];	
	$stat = $_GET['stat'];
	
	if($stat == 'Tidak Aktif')
	{
		$stat = '0';
	}else if($stat == 'Aktif')
	{
		$stat = '1';
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="8%" style="text-align: center;">POLICE<br>NUMBER</th>	
					<th rowspan="2" width="27%" style="text-align: center;">BRAND</th>
					<th rowspan="2" width="5%" style="text-align: center;">YEAR<br></th>
					<th rowspan="2" width="11%" style="text-align: center;">FRAME NUMBER</th>
					<th rowspan="2" width="13%" style="text-align: center;">COLOR</th>
					<th colspan="3" width="6%" style="text-align: center;">DOWNLOAD<br>DOCUMENT</th>
					<th rowspan="2" width="7%" style="text-align: center;">VALID<br>DATE OF <br>STNK</th>	
					<th rowspan="2" width="7%" style="text-align: center;">VALID<br>DATE OF <br>KIR</th>	
					<th rowspan="2" width="7%" style="text-align: center;">STATUS</th>
					<th colspan="3" width="6%" style="text-align: center;">ACTION</th>						
				</tr>
				<tr>	
					<th width="2%" style="text-align: center;">BPKB</th>
					<th width="2%" style="text-align: center;">STNK</th>
					<th width="2%" style="text-align: center;">KIR</th>		
					<th width="2%" style="text-align: center;">EDIT</th>
					<th width="2%" style="text-align: center;">PHOTO</th>	
					<th width="2%" style="text-align: center;">DOC</th>
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
	if($field == 'No. Polisi')
	{
		$f = 'm_mobil_tr.no_polisi';
	}else if($field == 'Merk'){
		$f = 'm_mobil_tr.merk';
	}else if($field == 'Tahun'){
		$f = 'm_mobil_tr.tahun_buat';
	}else if($field == 'Supir'){
		$f = 'm_supir.nama_supir';	
	}else{
		$f = 'm_mobil_tr.no_polisi';
	}	
	
		$SQL = "select * from m_mobil_tr where $f LIKE '%$search_name%'  
			order by m_mobil_tr.no_polisi asc LIMIT $offset, $jmlperhalaman";
	$query = mysqli_query($koneksi,$SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;	
			$nama = str_replace("\'","'",$row['nama_supir']);
			$term = 0;	
			//STNK
			$tgl_stnk = ConverTgl($row['tgl_stnk']);
			$due = date('Y-m-d',strtotime($row['tgl_stnk']) + (24*3600*$term)); 
			$duex = ConverTgl($due);
			$duey = strtotime($due);
			$tgl_sekarang = date('Y-m-d');
			$tgl_sekarang = strtotime($tgl_sekarang);
			$aging = $tgl_sekarang - $duey; 
			$aging = ($aging/24/60/60);
			$aging = round($aging);
			if($aging > 0 )
			{
				$label_stnk = 'danger';
			}else if($aging >= -7)
			{
				$label_stnk = 'warning';
			}else{
				$label_stnk = 'success';
			}
			
			//KIR
			$tgl_kir = ConverTgl($row['tgl_kir']);
			$due = date('Y-m-d',strtotime($row['tgl_kir']) + (24*3600*$term)); 
			$duex = ConverTgl($due);
			$duey = strtotime($due);
			$tgl_sekarang = date('Y-m-d');
			$tgl_sekarang = strtotime($tgl_sekarang);
			$aging = $tgl_sekarang - $duey; 
			$aging = ($aging/24/60/60);
			$aging = round($aging);
			if($aging > 0 )
			{
				$label_kir = 'danger';
			}else if($aging >= -7)
			{
				$label_kir = 'warning';
			}else{
				$label_kir = 'success';
			}	
			//ID CARD
			$tgl_card = ConverTgl($row['tgl_card']);
			$due = date('Y-m-d',strtotime($row['tgl_card']) + (24*3600*$term)); 
			$duex = ConverTgl($due);
			$duey = strtotime($due);
			$tgl_sekarang = date('Y-m-d');
			$tgl_sekarang = strtotime($tgl_sekarang);
			$aging = $tgl_sekarang - $duey; 
			$aging = ($aging/24/60/60);
			$aging = round($aging);
			if($aging > 0 )
			{
				$label_card = 'danger';
			}else if($aging >= -7)
			{
				$label_card = 'warning';
			}else{
				$label_card = 'success';
			}
			
			if($row['photo'] == '')
			{
				$photo = "mobil/no.jpg";
			}else{
				$photo = strtolower($row['photo']);
			}
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>		
				<td style="text-align:left">
					<div class="hover_img">
						<a href="#" onclick="javascript:ViewData('.$row['id_mobil'].')">'.$row['no_polisi'].'
						<span><img src="'.$photo.'" alt="image" width="120px" height="140px" /></span></a>
					</div>
				</td>
				<td style="text-align:center">'.$row['merk'].'</td>	
				<td style="text-align:center">'.$row['tahun_buat'].'</td>				
				<td style="text-align:center">'.$row['no_rangka'].'</td>
				<td style="text-align:center">'.$row['warna_truck'].'</td>';
				if($row['bpkp'] == '')
				{
					$data .='<td></td>';
				}else{
					$photo = strtolower($row['bpkp']);
					$link = "'$photo'";
					$data .= '<td>
								<button class="btn btn-block btn-default" 
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="window.open('.$link.') "   >
									<span class="fa fa-file-text " ></span>
								</button></td>';
				}
				if($row['stnk'] == '')
				{
					$data .='<td></td>';
				}else{
					$photo = strtolower($row['stnk']);
					$link = "'$photo'";
					$data .= '<td>
								<button class="btn btn-block btn-default" 
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="window.open('.$link.') "   >
									<span class="fa fa-file-text " ></span>
								</button></td>';
				}
				if($row['kir'] == '')
				{
					$data .='<td></td>';
				}else{
					$photo = strtolower($row['kir']);
					$link = "'$photo'";
					$data .= '<td>
								<button class="btn btn-block btn-default" 
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="window.open('.$link.') "   >
									<span class="fa fa-file-text " ></span>
								</button></td>';
				}
			
				$data .='<td style="text-align:center">
					<button type="button" class="btn btn-'.$label_stnk.'" style="width:100%;padding:1px;margin:-3px">'.$tgl_stnk.'</button>
				</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label_kir.'" style="width:100%;padding:1px;margin:-3px">'.$tgl_kir.'</button>
				</td>';
				if($row['status'] =='0' ){
					$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-danger" style="margin:-3px;width:100%;padding:1px;border-radius:1px">&nbsp;In Active &nbsp;</button>
					</td>';
				} else if($row['status'] =='1'){
					$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-success" style="margin:-3px;width:100%;padding:1px;border-radius:1px">Active</button>
					</td>';	
				}
				if($m_edit == '1'  ){
					$xy1="Edit|$row[id_cust]";
					$xy1=base64_encode($xy1);
					$link = "'cust_data.php?id=$xy1'";
					$data .= '<td>
								<button class="btn btn-block btn-default" 
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_mobil'].')"   >
									<span class="fa fa-edit " ></span>
								</button></td>';
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Upload Photo"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:GetImg('.$row['id_mobil'].')"   >
									<span class="fa fa-image" ></span>
								</button></td>';	
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Upload Document"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:GetDoc('.$row['id_mobil'].')"   >
									<span class="fa fa-upload" ></span>
								</button></td>';				
				}
				else
				{
					$data .='<td></td>';
					$data .='<td></td>';
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
				$pq = mysqli_query($koneksi, "select count(id_mobil) as jml from m_mobil_tr where $f LIKE '%$search_name%'     ");
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
		$no_polisi = strtoupper($_POST['no_polisi']);
		$tgl_stnk = ConverTglSql($_POST['tgl_stnk']);
		$tgl_kir = ConverTglSql($_POST['tgl_kir']);
		$tgl_card = ConverTglSql($_POST['tgl_card']);
		$merk = $_POST['merk'];
		$tahun_buat = $_POST['tahun_buat'];
		$tahun_rakit = $_POST['tahun_rakit'];
		$silinder = $_POST['silinder'];
		$warna_truck = strtoupper($_POST['warna_truck']);
		$no_rangka = strtoupper($_POST['no_rangka']);
		$no_mesin = strtoupper($_POST['no_mesin']);
		$no_bpkb = strtoupper($_POST['no_bpkb']);
		$iden = strtoupper($_POST['iden']);
		$warna_tnkb = strtoupper($_POST['warna_tnkb']);
		$bbm = $_POST['bbm'];
		$berat_max = $_POST['berat_max'];
		$no_reg = strtoupper($_POST['no_reg']);
		$mode = $_POST['mode'];
		$stat = $_POST['stat'];
		$silinder = str_replace(",","", $silinder);
		
		if($mode == 'Add')
		{	
			$tanggal = date('Y-m-d');
			$sql = "INSERT INTO m_mobil_tr (no_polisi,merk,tahun_buat,tahun_rakit,silinder,
					warna_truck,no_rangka,no_mesin,no_bpkb,no_kabin,iden,warna_tnkb,bbm,berat_max,no_reg,
					status,created,tanggal, tgl_stnk, tgl_kir)
				values
				('$no_polisi','$merk','$tahun_buat','$tahun_rakit','$silinder',
				'$warna_truck','$no_rangka','$no_mesin','$no_bpkb','$no_kabin','$iden','$warna_tnkb','$bbm','$berat_max','$no_reg',
				'1','$id_user','$tanggal','$tgl_stnk','$tgl_kir')";
			$hasil=mysqli_query($koneksi, $sql);	
		}
		else
		{
			$sql = "update m_mobil_tr set
				no_polisi = '$no_polisi',
				merk = '$merk',
				tgl_stnk = '$tgl_stnk',
				tgl_kir = '$tgl_kir',
				tahun_buat = '$tahun_buat',
				tahun_rakit = '$tahun_rakit',
				silinder = '$silinder',
				warna_truck = '$warna_truck',
				no_rangka = '$no_rangka',
				no_mesin = '$no_mesin',
				no_bpkb = '$no_bpkb',
				no_kabin = '$no_kabin',
				iden = '$iden',
				warna_tnkb = '$warna_tnkb',
				bbm = '$bbm',
				berat_max = '$berat_max',
				no_reg = '$no_reg',
				status = '$stat'
				where id_mobil = '$id'	";
			$hasil=mysqli_query($koneksi, $sql);;
		}
		if (!$hasil) {
	        			
			exit(mysqli_error($koneksi));
			echo "No. Polisi sudah terdaftar...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
	
	
}else if ($_POST['type'] == "detil"){
	$id = $_POST['id'];	
    $query = "select * from m_mobil_tr where id_mobil  = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
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

	


}else if ($_GET['type'] == "ListMobil")
{	
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="90%" style="text-align: center;">NO. POLICE</th>
					<th width="10%" style="text-align: center;">SELECT</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "select * from m_mobil_tr where no_polisi LIKE '%$cari%' order by no_polisi LIMIT 0, 25";	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$data .= '<tr>		
				<td style="text-align:left"><a href="#" onclick="PilihMobil('.$row['id_mobil'].')" >'.$row['no_polisi'].'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihMobil('.$row['id_mobil'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px">Pilih</button>
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