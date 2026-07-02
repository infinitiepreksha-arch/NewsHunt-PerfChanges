<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>NewsHunt - Download Now</title>
    <meta name="description" content="NewsHunt - Latest news at your fingertips">
    <meta name="keywords" content="news app, news hunt">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://newshunt.infinitietech.com/">
    <meta property="og:title" content="NewsHunt">
    <meta property="og:image" content="https://newshunt.infinitietech.com/storage/settings/ypGXhC7wVqS5vFGTtKzSblb9ujqwltJBKOjj8Z7A.png">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://newshunt.infinitietech.com/">
    <meta property="twitter:title" content="NewsHunt">
    <meta property="twitter:image" content="https://newshunt.infinitietech.com/storage/settings/ypGXhC7wVqS5vFGTtKzSblb9ujqwltJBKOjj8Z7A.png">
    
    <!-- Favicon -->
    <link rel="icon" href="https://newshunt.infinitietech.com/storage/settings/ypGXhC7wVqS5vFGTtKzSblb9ujqwltJBKOjj8Z7A.png" type="image/png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for Web Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #e62323;
            --primary-dark: #c51d1d;
            --secondary: #d946ef;
            --secondary-dark: #b72ecb;
            --accent: #4a46ff;
            --success: #22c55e;
            --warning: #eab308;
            --dark-bg: #0a0a0a;
            --dark-surface: #151518;
            --dark-card: #1c1c21;
            --text-white: rgba(255, 255, 255, 0.95);
            --text-muted: rgba(255, 255, 255, 0.65);
            --text-dim: rgba(255, 255, 255, 0.4);
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(255, 255, 255, 0.15);
            --glass: rgba(255, 255, 255, 0.03);
            --glow-primary: rgba(230, 35, 35, 0.4);
            --glow-secondary: rgba(217, 70, 239, 0.3);
            --icon-android: #3DDC84;
            --icon-apple: #A2AAAD;
            --icon-web: #4285F4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            height: 100%;
            overflow-x: hidden;
            /* Fix for constantly moving scrollbar */
            overflow-y: scroll;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: 
                radial-gradient(circle at 20% 20%, rgba(230, 35, 35, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(217, 70, 239, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 60%, rgba(133, 146, 163, 0.06) 0%, transparent 50%),
                linear-gradient(135deg, var(--dark-bg) 0%, #0f0f12 100%);
            background-attachment: fixed; /* Fix background to prevent scrollbar movement */
            color: var(--text-white);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            position: relative;
            min-height: 100%;
        }
        
        /* Animated background particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }
        
        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: var(--primary);
            border-radius: 50%;
            opacity: 0.3;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 0.8; }
        }
        
        .wrapper {
            flex: 1 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            position: relative;
            min-height: 100vh;
        }
        
        /* Enhanced background elements */
        .bg-orb {
            position: fixed; /* Fixed position to prevent scrollbar movement */
            border-radius: 50%;
            filter: blur(60px);
            z-index: -1;
            animation: pulse 4s ease-in-out infinite;
        }
        
        .bg-orb-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            top: -200px;
            left: -100px;
            opacity: 0.08;
            animation-delay: 0s;
        }
        
        .bg-orb-2 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, var(--secondary), var(--warning));
            bottom: -150px;
            right: -50px;
            opacity: 0.06;
            animation-delay: 2s;
        }
        
        .bg-orb-3 {
            width: 250px;
            height: 250px;
            background: linear-gradient(90deg, var(--success), var(--accent));
            top: 50%;
            right: -100px;
            opacity: 0.04;
            animation-delay: 1s;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.04; }
            50% { transform: scale(1.1) rotate(180deg); opacity: 0.08; }
        }
        
        .container {
            max-width: 900px;
            width: 100%;
            text-align: center;
            padding: 50px 40px;
            backdrop-filter: blur(20px);
            border-radius: 32px;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.3),
                0 8px 32px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            background: 
                linear-gradient(135deg, var(--glass) 0%, rgba(255, 255, 255, 0.01) 100%),
                var(--dark-card);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            opacity: 0.6;
        }
        
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 32px;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.3));
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }
        
        .logo:hover {
            transform: scale(1.1) rotate(5deg);
            filter: drop-shadow(0 12px 24px rgba(230, 35, 35, 0.4));
        }
        
        .logo-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, var(--glow-primary) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            z-index: -1;
            animation: logoGlow 3s ease-in-out infinite;
        }
        
        @keyframes logoGlow {
            0%, 100% { opacity: 0.3; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.6; transform: translate(-50%, -50%) scale(1.2); }
        }
        
        h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(135deg, 
                var(--primary) 0%, 
                #f45151 25%, 
                var(--secondary) 50%, 
                var(--warning) 75%, 
                var(--success) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -2px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: titleFloat 6s ease-in-out infinite;
        }
        
        @keyframes titleFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .subtitle {
            font-size: 1.25rem;
            font-weight: 400;
            color: var(--text-muted);
            margin-bottom: 50px;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
            position: relative;
        }
        
        .highlight {
            color: var(--primary);
            font-weight: 600;
            position: relative;
        }
        
        .highlight::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            animation: underlineGrow 2s ease-out 1s forwards;
        }
        
        @keyframes underlineGrow {
            to { transform: scaleX(1); }
        }
        
        .buttons-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 24px;
            margin: 50px 0 30px;
        }
        
        .btn {
            display: flex;
            align-items: center;
            padding: 18px 32px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.3px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid var(--border);
            background-color: var(--dark-surface);
            color: var(--text-white);
            position: relative;
            overflow: hidden;
            min-width: 180px;
            justify-content: center;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.6s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.3),
                0 8px 16px rgba(0, 0, 0, 0.2);
            border-color: var(--border-hover);
        }
        
        .btn:active {
            transform: translateY(-3px) scale(1.01);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #ff4a4a 100%);
            border-color: transparent;
            box-shadow: 0 8px 24px var(--glow-primary);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #ff3c3c 0%, #ff6262 100%);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.3),
                0 8px 32px var(--glow-primary);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #c526d9 0%, var(--secondary) 100%);
            border-color: transparent;
            box-shadow: 0 8px 24px var(--glow-secondary);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #d43ee7 0%, #e06df2 100%);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.3),
                0 8px 32px var(--glow-secondary);
        }
        
        .btn-neutral {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
        }
        
        .btn-neutral:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--border-hover);
        }
        
        .btn i {
            font-size: 22px;
            margin-right: 14px;
            transition: transform 0.3s ease;
        }
        
        .btn:hover i {
            transform: scale(1.1);
        }
        
        .icon-android {
            color: var(--icon-android);
            filter: drop-shadow(0 0 8px rgba(61, 220, 132, 0.5));
        }
        
        .icon-apple {
            color: var(--icon-apple);
            filter: drop-shadow(0 0 8px rgba(162, 170, 173, 0.5));
        }
        
        .icon-web {
            color: var(--icon-web);
            filter: drop-shadow(0 0 8px rgba(66, 133, 244, 0.5));
        }
        
        /* Stats section */
        .stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--border);
        }
        
        .stat {
            text-align: center;
            opacity: 0.8;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            display: block;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: var(--text-dim);
            margin-top: 4px;
        }
        
        footer {
            flex-shrink: 0;
            padding: 30px 20px;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-dim);
            width: 100%;
            background: 
                linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.2) 100%),
                var(--dark-surface);
            border-top: 1px solid var(--border);
            backdrop-filter: blur(10px);
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-links a {
            color: var(--text-dim);
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            color: var(--primary);
            transform: translateY(-2px);
        }
        
        /* Custom scrollbar to prevent constant movement */
        ::-webkit-scrollbar {
            width: 10px;
            background-color: var(--dark-bg);
        }
        
        ::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            border: 2px solid var(--dark-bg);
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 40px 25px;
                margin: 20px;
            }
            
            h1 {
                font-size: 3rem;
                letter-spacing: -1px;
            }
            
            .subtitle {
                font-size: 1.1rem;
                margin-bottom: 40px;
            }
            
            .buttons-container {
                flex-direction: column;
                align-items: center;
                gap: 20px;
            }
            
            .btn {
                width: 100%;
                max-width: 280px;
            }
            
            .stats {
                gap: 30px;
            }
            
            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            h1 {
                font-size: 2.5rem;
            }
            
            .subtitle {
                font-size: 1rem;
            }
        }
        
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: rippleEffect 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes rippleEffect {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Animated particles -->
    <div class="particles" id="particles"></div>
    
    <div class="wrapper">
        <!-- Enhanced background orbs -->
        <div class="bg-orb bg-orb-1"></div>
        <div class="bg-orb bg-orb-2"></div>
        <div class="bg-orb bg-orb-3"></div>
        
        <div class="container">
            <div style="position: relative; display: inline-block;">
                <div class="logo-glow"></div>
                <img src="https://newshunt.infinitietech.com/storage/settings/ypGXhC7wVqS5vFGTtKzSblb9ujqwltJBKOjj8Z7A.png" alt="NewsHunt Logo" class="logo">
            </div>
            
            <h1>NewsHunt</h1>
            <p class="subtitle">
                Experience the <span class="highlight">latest news</span> that matters to you. Your personalized news companion for staying informed.
            </p>
            
            <div class="buttons-container">
                <a href="https://play.google.com/store/apps/details?id=com.newsHunt.hunt&hl=en" target="_blank" rel="noopener" class="btn btn-primary">
                    <i class="fab fa-android icon-android"></i>
                    Download on Google Play
                </a>
                
                <a href="https://testflight.apple.com/join/b5kR5QRe" target="_blank" rel="noopener" class="btn btn-secondary">
                    <i class="fab fa-apple icon-apple"></i>
                    Download on TestFlight
                </a>
                
                <a href="https://newshunt.infinitietech.com" target="_blank" rel="noopener" class="btn btn-neutral">
                    <i class="fas fa-globe icon-web"></i>
                    Try Web Demo
                </a>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="footer-content">
            <div>&copy; 2025 NewsHunt. All rights reserved.</div>
            <div class="social-links">
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                <a href="#" aria-label="GitHub"><i class="fab fa-github"></i></a>
            </div>
        </div>
    </footer>

    <script>
        // Create animated particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 15;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 4 + 4) + 's';
                particlesContainer.appendChild(particle);
            }
        }
        
        // Mouse movement parallax effect - gentle movement to avoid affecting scrollbar
        document.addEventListener('mousemove', (e) => {
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            const orbs = document.querySelectorAll('.bg-orb');
            orbs.forEach((orb, index) => {
                const speed = (index + 1) * 0.015; // Reduced speed
                const x = (mouseX - 0.5) * speed * 100;
                const y = (mouseY - 0.5) * speed * 100;
                orb.style.transform = `translate(${x}px, ${y}px)`;
            });
        });
        
        // Initialize particles
        createParticles();
        
        // Add smooth scroll behavior for better UX
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Button click animations
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    </script>
</body>
</html>