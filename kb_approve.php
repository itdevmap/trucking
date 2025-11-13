<?php
include "koneksi.php"; 

$no_doc = $_GET['no_doc'] ?? null;

$no_doc = mysqli_real_escape_string($koneksi, $no_doc);
$q_quo  = "UPDATE tr_jo 
            SET `flag_kb` = 1 
            WHERE no_kb = '$no_doc'";
$hasil = mysqli_query($koneksi, $q_quo);
$success = $hasil ? true : false;

// =============== LOG APPROVAL ===============
    $q_approval   = "INSERT INTO tr_approval_logs
                    (no_doc, keterangan, device)
                VALUES 
                    ('$no_doc','Approval Print AR','$userAgent')";
    mysqli_query($koneksi, $q_approval);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .card {
            width: 420px;
            padding: 30px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        .icon {
            font-size: 50px;
            margin-bottom: 15px;
        }
        .countdown {
            margin-top: 10px;
            font-size: 14px;
            color: #444;
        }
    </style>
</head>
<body>

<div class="card <?= $success ? 'success' : 'error' ?>">
    <div class="icon">
        <?php if ($success): ?>
            <i class="fa-solid fa-circle-check"></i>
        <?php else: ?>
            <i class="fa-solid fa-circle-xmark"></i>
        <?php endif; ?>
    </div>
    <h2>
        <?= $success ? 'Approval Berhasil!' : 'Approval Gagal!' ?>
    </h2>
    <p class="countdown">Menutup halaman dalam <span id="timer">3</span> detik...</p>
</div>

<script>
    let counter = 3;
    const timerEl = document.getElementById('timer');

    const interval = setInterval(() => {
        counter--;
        timerEl.textContent = counter;
        if (counter <= 0) {
            clearInterval(interval);
            window.close(); 
        }
    }, 1000);
</script>

</body>
</html>
