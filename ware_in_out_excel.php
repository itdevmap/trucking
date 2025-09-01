<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Ware_InOut.xls");
header("Pragma: no-cache");
header("Expires: 0");

$idx   = isset($_GET['id']) ? $_GET['id'] : '';
$pecah = explode("|", base64_decode($idx));

$tgl1  = isset($pecah[0]) && $pecah[0] !== '' ? $pecah[0] : '1900-01-01';
$tgl2  = isset($pecah[1]) && $pecah[1] !== '' ? $pecah[1] : '2100-12-31';

$tgl1x = date('Y-m-d', strtotime($tgl1));
$tgl2x = date('Y-m-d', strtotime($tgl2));


$sql = "
	SELECT
		td.tanggal AS tanggal,
		td.no_doc AS no_doc,
		w.kode AS kode_barang,
		w.nama AS nama_barang,
		wd.masuk AS qty_masuk,
		wd.keluar AS qty_keluar,
		w.unit AS satuan,
		CASE WHEN td.jenis = '0' THEN 'IN' ELSE 'OUT' END AS jenis
	FROM t_ware_data_detil wd
	JOIN t_ware_data td   ON td.id_data = wd.id_data
	LEFT JOIN t_ware w    ON wd.id_ware = w.id_ware
	WHERE td.tanggal BETWEEN '$tgl1x' AND '$tgl2x'
	ORDER BY td.tanggal ASC, wd.id_detil ASC
";
$k = isset($koneksi) ? $koneksi : (isset($conn) ? $conn : null);
if (!$k) {
    die("ERROR: Koneksi database (\$koneksi / \$conn) tidak ditemukan. Pastikan include 'koneksi.php' benar.");
}

$q = mysqli_query($k, $sql);
if ($q === false) {
    die("<pre>Query gagal:\n" . mysqli_error($k) . "\n\nSQL:\n$sql</pre>");
}

$data = [];
while ($row = mysqli_fetch_assoc($q)) {
    $tanggal = $row['tanggal'];
    $no_doc = $row['no_doc'];
    $kode    = $row['kode_barang'];
    $nama    = $row['nama_barang'];
    $satuan  = $row['satuan'];
    $jenis   = $row['jenis'];

    if ($row['jenis'] === 'IN') {
        $qty = (float)$row['qty_masuk'];
    } else {
        $qty = (float)$row['qty_keluar'];
    }

    if ($qty == 0) continue;

    $data[] = [
        'tanggal'     => $tanggal,
        'no_doc'      => $no_doc,
        'kode_barang' => $kode,
        'nama_barang' => $nama,
        'qty'         => $qty,
        'satuan'      => $satuan,
        'jenis'       => $jenis,
    ];
}

usort($data, function($a, $b) {
    $ta = strtotime($a['tanggal']);
    $tb = strtotime($b['tanggal']);
    if ($ta === $tb) return strcmp($a['kode_barang'], $b['kode_barang']);
    return $ta <=> $tb;
});
?>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>NO Doc</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Jenis</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($data)) { ?>
            <tr>
                <td colspan="7" style="text-align:center">Tidak ada data pada rentang tanggal tersebut.</td>
            </tr>
        <?php } else { $no = 1; foreach ($data as $r) { ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($r['tanggal']); ?></td>
                <td><?php echo htmlspecialchars($r['no_doc']); ?></td>
                <td><?php echo htmlspecialchars($r['kode_barang']); ?></td>
                <td><?php echo htmlspecialchars($r['nama_barang']); ?></td>
                <td style="text-align:right;"><?php echo number_format($r['qty'], 2, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($r['satuan']); ?></td>
                <td><?php echo htmlspecialchars($r['jenis']); ?></td>
            </tr>
        <?php }} ?>
    </tbody>
</table>
