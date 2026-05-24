<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vendas Corporativas</title>
    <style>
        body { margin: 0; padding: 0; background: #f7f8fb; font-family: Arial, sans-serif; color: #1f2933; }
        .wrapper { width: 100%; background: #f7f8fb; padding: 24px 0; }
        .container { width: 100%; max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08); }
        .header { background: #ffffff; padding: 24px; text-align: center; border-bottom: 1px solid #e2e8f0; }
        .header img { max-width: 200px; height: auto; }
        .content { padding: 28px 32px; }
        .content h2 { margin: 0 0 16px; font-size: 20px; color: #0f172a; }
        .content p { margin: 0 0 12px; line-height: 1.6; }
        .credentials { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; margin: 16px 0; }
        .credentials strong { display: inline-block; width: 70px; }
        .button { display: inline-block; background: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 20px; border-radius: 6px; font-weight: bold; }
        .button-wrap { margin: 20px 0 8px; }
        .footer { background: #f1f5f9; color: #475569; padding: 20px 24px; font-size: 13px; border-top: 1px solid #e2e8f0; }
        .footer a { color: #16a34a; text-decoration: none; }
        .muted { color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">
        <div class="header">
            <img src="{{ asset('login/images/logo.png') }}" alt="Vendas Corporativas">
        </div>
        <div class="content">
            <h2>Bem-vindo ao sistema de cotacoes</h2>
            <p>Ola {{ $name }}, seu acesso foi criado com sucesso.</p>
            <div class="credentials">
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Senha:</strong> {{ $password }}</p>
            </div>
            <div class="button-wrap">
                <a class="button" href="{{ url('/login') }}">Acessar sistema</a>
            </div>
            <p class="muted">Se nao reconhecer este acesso, ignore este email.</p>
            <p class="muted">Recomendamos trocar a senha no primeiro acesso.</p>
        </div>
        <div class="footer">
            <p><strong>WhatsApp</strong> <a href="https://api.whatsapp.com/send?phone=5511912903010">(11) 91290-3010</a></p>
            <p><strong>Comercial</strong> contato@vendascorporativas.com.br</p>
            <p><strong>Horario de atendimento</strong> Segunda a Sexta-feira das 9h as 17h</p>
        </div>
    </div>
</div>
</body>
</html>
