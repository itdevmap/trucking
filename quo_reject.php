
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Quotation</title>
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

<div class="card error">
    <div class="icon">
        <i class="fa-solid fa-circle-xmark"></i>
    </div>
    <h2>Quotation Telah di Reject!</h2>
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
