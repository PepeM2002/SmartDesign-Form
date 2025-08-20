<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrando sesión...</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #001685;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .cerrando {
            text-align: center;
        }

        .spinner {
            margin: 20px auto;
            width: 50px;
            height: 50px;
            border: 5px solid #fff;
            border-top: 5px solid #00f;
            border-radius: 50%;
            animation: girar 1s linear infinite;
        }

        @keyframes girar {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="cerrando">
        <h2>Cerrando sesión...</h2>
        <div class="spinner"></div>
    </div>

    <script>
        setTimeout(function () {
            window.location.href = 'login.php';
        }, 2000);
    </script>
</body>

</html>