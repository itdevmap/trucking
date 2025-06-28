<?php
include "koneksi.php"; 

//$tahunx = '2023';
$tahunx = date("Y") ;	

$id_user=$_SESSION['id_user'];
$sql = mysqli_query($koneksi, "SELECT  m_user_tr.*, m_role_tr.nama_role from
                    m_user_tr inner join  m_role_tr on m_user_tr.id_role = m_role_tr.id_role where m_user_tr.id_user = '$id_user'  ");
$row=mysqli_fetch_array($sql);
$nama_user=strtoupper($row['nama_user']);
$unit_user =$row['unit'];
$id_cabang = $row['id_cabang'];
$nama_cabang = $row['nama_cabang'];
$caption_cabang = $row['capx'];
$id_role = $row['id_role'];
$nama_role = $row['nama_role'];

$sql1 = mysqli_query($koneksi, "SELECT * from m_pt where id_pt = '1' ");
$row1=mysqli_fetch_array($sql1);
$id_pt = $row1['id_pt'];
$nama_pt = $row1['nama_pt'];
$alamat_pt = $row1['alamat'];
$telp_pt = $row1['telp'];
$fax_pt = $row1['fax'];
$email_pt = $row1['email'];
$web_pt = $row1['web'];
$caption_pt = $row1['caption'];
$logo_pt = $row1['logo'];
$npwp = $row1['npwp'];
$alamat_npwp = $row1['alamat_npwp'];

$periode = Date('Y');
$aplikasi = "TRUCKING EKSPEDISI";

$s_pendapatan_fcl = '50';
$s_pendapatan_lcl = '207';

$s_hpp_fcl = '76';
$s_hpp_lcl = '250';

$s_ppn_masukan = '67';
$s_ppn_keluaran = '251';
$s_piutang_customer = '37';

$pph23_masukan = '173';
$pph23_keluaran = '252';

$s_hutang_vendor = '44';

?>