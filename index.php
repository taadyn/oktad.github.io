<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Okta Dashboard</title>
    <style>
        body {
            margin: 0; padding: 0;
            font-family: 'Times New Roman', Times, serif;
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh; overflow: hidden;
            background: #000; /* Dasar hitam untuk memperkuat efek bintang */
        }

        /* Background Blur Dipo */
        body::before {
            content: "";
            background-image: url('images/undip.jpg');
            background-size: cover; background-position: center;
            filter: blur(8px); position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1; transform: scale(1.1);
        }

        /* --- EFEK BINTANG-BINTANG ESTETIK --- */
        .stars-container {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            opacity: 0.5;
            animation: twinkle var(--duration) infinite ease-in-out;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.5); box-shadow: 0 0 10px white; }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.2); /* Putih Transparan */
            padding: 40px; border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%; max-width: 350px; text-align: center;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            z-index: 10;
        }

        .login-card h2 { color: white; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        
        /* GAYA DASAR INPUT (TRANSPARAN) */
        .login-input {
            width: 100%; padding: 12px; margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.4); 
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.15) !important; 
            outline: none; 
            transition: all 0.3s ease;
            box-sizing: border-box;
            color: white !important;
        }

        .login-input::placeholder { color: rgba(255, 255, 255, 0.7); }

        /* --- EFEK HOVER & FOCUS --- */
        .login-input:hover, .login-input:focus {
            transform: translateY(-5px);
            border-color: white;
            background: rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.2);
        }

        /* Tombol Login (Timbul) */
        .btn-login {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white; border: none; padding: 12px; width: 100%;
            border-radius: 8px; font-weight: bold; cursor: pointer; 
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(106, 17, 203, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(106, 17, 203, 0.6);
        }
    </style>
</head>
<body>
    <div class="stars-container" id="stars"></div>

    <div class="login-card">
        <h2>Member Login</h2>
        <form action="dashboard.php" method="POST">
            <input type="text" name="user" class="login-input" placeholder="Username" required>
            <input type="password" name="pass" class="login-input" placeholder="Password" required>
            <button type="submit" name="status_login" class="btn-login">LOGIN</button>
        </form>
    </div>

    <script>
        // Script sederhana untuk membuat bintang secara acak
        const starsContainer = document.getElementById('stars');
        const count = 50; // Jumlah bintang

        for (let i = 0; i < count; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            
            // Posisi Acak
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            
            // Ukuran Acak
            const size = Math.random() * 3 + 1;
            
            // Durasi Animasi Acak
            const duration = Math.random() * 3 + 2;

            star.style.left = `${x}%`;
            star.style.top = `${y}%`;
            star.style.width = `${size}px`;
            star.style.height = `${size}px`;
            star.style.setProperty('--duration', `${duration}s`);
            
            starsContainer.appendChild(star);
        }
    </script>
</body>
</html>