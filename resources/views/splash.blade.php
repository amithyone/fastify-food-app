<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fastify - Loading</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .splash-container {
            text-align: center;
            color: white;
            animation: fadeIn 0.5s ease-in;
        }

        .logo-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
        }

        .logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
            animation: pulse 2s infinite;
        }

        .logo-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            animation: glow 2s ease-in-out infinite alternate;
        }

        .app-name {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .app-tagline {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .loading-dots {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .dot {
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out;
        }

        .dot:nth-child(1) { animation-delay: -0.32s; }
        .dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes glow {
            from { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
            to { opacity: 0.8; transform: translate(-50%, -50%) scale(1.1); }
        }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            }
        }

        /* Mobile optimization */
        @media (max-width: 480px) {
            .app-name {
                font-size: 2rem;
            }
            
            .app-tagline {
                font-size: 1rem;
            }
            
            .logo-container {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="splash-container">
        <div class="logo-container">
            <div class="logo-glow"></div>
            <img src="{{ asset('favicon.png') }}" alt="Fastify" class="logo">
        </div>
        
        <h1 class="app-name">Fastify</h1>
        <p class="app-tagline">Your Food Experience</p>
        
        <div class="loading-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>

    <script>
        // Redirect to main app after a short delay
        setTimeout(() => {
            window.location.href = '{{ route("dashboard") }}';
        }, 2000);
    </script>
</body>
</html> 