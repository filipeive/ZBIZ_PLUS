<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Erro Interno | ReproSys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --dark-color: #1e293b;
        }
        body {
            background: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            color: var(--dark-color);
        }
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: #f59e0b;
            line-height: 1;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .error-message {
            color: #64748b;
            font-size: 1.125rem;
            margin-bottom: 2.5rem;
        }
        .btn-home {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-home:hover {
            background: #2563eb;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        }
        .illustration {
            font-size: 5rem;
            color: #fef3c7;
            margin-bottom: -1.5rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="illustration">
            <i class="fas fa-tools"></i>
        </div>
        <div class="error-code">500</div>
        <h1 class="error-title">Erro Interno do Servidor</h1>
        <p class="error-message">
            Desculpe! Algo deu errado nos nossos servidores. Nossos técnicos já foram notificados. 
            Tente atualizar a página ou volte em alguns instantes.
        </p>
        <a href="{{ url('/dashboard') }}" class="btn-home">
            <i class="fas fa-home"></i>
            Ir para o Dashboard
        </a>
    </div>
</body>
</html>
