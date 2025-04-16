<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Urquhart Library</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            width: 100vw;
            box-sizing: border-box;
            background: url('assets/images/theme-image.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        /* Animated gradient overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 0;
            pointer-events: none;
            background: linear-gradient(120deg, rgba(74,144,226,0.25) 0%, rgba(231,76,60,0.18) 50%, rgba(44,62,80,0.22) 100%);
            animation: gradientMove 8s ease-in-out infinite alternate;
        }
        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }
            100% {
                background-position: 100% 50%;
            }
        }
        .overlay {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(8px) saturate(150%);
            -webkit-backdrop-filter: blur(8px) saturate(150%);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 40px 60px;
            text-align: center;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInUp 1.2s cubic-bezier(.39,.575,.565,1) 0.2s forwards;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: none;
            }
        }
        h1 {
            color: #fff;
            font-size: 2.5rem;
            margin-bottom: 30px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 1px;
            text-shadow: 0 2px 16px rgba(44,62,80,0.18);
        }
        .btn-login {
            background: linear-gradient(90deg, #4a90e2 0%, #357abd 100%);
            color: #fff;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(74,144,226,0.12);
        }
        .btn-login:hover {
            background: linear-gradient(90deg, #357abd 0%, #4a90e2 100%);
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 4px 16px rgba(74,144,226,0.18);
        }
        @media (max-width: 600px) {
            .overlay {
                padding: 20px 10px;
            }
            h1 {
                font-size: 1.5rem;
            }
            .btn-login {
                padding: 10px 20px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="overlay">
        <h1>Welcome to Urquhart Library</h1>
        <a href="login.php" class="btn-login">Login</a>
    </div>
</body>
</html> 