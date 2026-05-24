# Demo: Concorrencia em banco de dados (Laravel + Livewire)

Este projeto demonstra, na pratica, a diferenca entre um fluxo inseguro e um fluxo seguro para saques concorrentes em banco de dados.

## Principais ideias

- Dois saldos: um inseguro (sem protecao) e um seguro (com transacao e lock).
- Pagina publica de saque por QR code.
- Dashboard Livewire com atualizacao automatica.
- Historico de saques e controles administrativos.

## Dependencias

- PHP 8.3+
- MySQL ou PostgreSQL
- Composer + Node.js

## Setup rapido

```bash
composer install
npm install
```

Configure o banco em `.env` (MySQL ou PostgreSQL) e rode as migrations:

```bash
php artisan migrate --seed
```

Compile os assets:

```bash
npm run build
```

## Executar localmente

```bash
php artisan serve
```

Acesse:

- Dashboard (autenticado): `http://localhost:8000/dashboard`
- Pagina publica de saque: `http://localhost:8000/withdraw/unsafe` e `http://localhost:8000/withdraw/safe`

## Simular concorrencia via CLI

Este script usa `pcntl_fork` para disparar varios saques em paralelo (sem HTTP):

```bash
php scripts/simulate-withdrawals.php unsafe 30 2
php scripts/simulate-withdrawals.php safe 30 2
```

Argumentos: `slug` (unsafe/safe), `workers`, `attemptsPerWorker`.

## Ajustes rapidos

- `config/banking.php` controla o atraso do fluxo inseguro (`unsafe_delay_ms`) e o intervalo de polling do dashboard.
- O QR code usa `simplesoftwareio/simple-qrcode`.

## Estrutura principal

- `app/Models/BankAccount.php`
- `app/Models/Withdrawal.php`
- `app/Actions/Banking/WithdrawUnsafe.php`
- `app/Actions/Banking/WithdrawSafe.php`
- `app/Livewire/BankDashboard.php`
- `app/Livewire/WithdrawPage.php`
- `resources/views/livewire/bank-dashboard.blade.php`
- `resources/views/livewire/withdraw-page.blade.php`
- `database/migrations/2026_05_22_000000_create_bank_accounts_table.php`
- `database/migrations/2026_05_22_000001_create_withdrawals_table.php`

## API de acesso seguro

Endpoint seguro para criar/atualizar usuario e enviar credenciais por email e WhatsApp.

- Rota: `POST /api/access-credentials`
- Headers: `X-Access-Key: <sua-chave>` (ou `Authorization: Bearer <sua-chave>`)
- Body JSON: `name`, `email`, `whatsapp`

Variaveis de ambiente necessarias:

- `BANK_API_ACCESS_KEY`
- `TWILIO_ACCOUNT_SID`
- `TWILIO_AUTH_TOKEN`
- `TWILIO_WHATSAPP_FROM` (ex: `whatsapp:+14155550100`)
- `TWILIO_MESSAGING_SERVICE_SID` (opcional)

Exemplo de payload:

```json
{
  "name": "Maria Doe",
  "email": "maria@example.com",
  "whatsapp": "+5511999999999"
}
```

## Deploy no Laravel Cloud

- Configure as variaveis de ambiente do banco (MySQL ou PostgreSQL).
- Rode as migrations e o seed no ambiente de deploy.
- Garanta que o build do Vite rode durante o deploy (gera `public/build/manifest.json`).

Sugestao de comandos para o pipeline do Laravel Cloud:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force --seed
npm install
npm run build
```
