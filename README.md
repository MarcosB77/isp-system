# 🚀 Laravel ISP System

Sistema de gerenciamento para provedores de internet (ISP).
Arquitetura: DDD + Clean Architecture + Laravel 11

---

## ⚡ Setup Rápido (Ubuntu + XAMPP)

```bash
# 1. Instalar Laravel
composer create-project laravel/laravel isp-system
cd isp-system

# 2. Copiar os arquivos do scaffolding para o projeto
# (copie as pastas app/, database/, routes/, tests/ para dentro do projeto)

# 3. Configurar .env
cp .env.example .env
# Edite .env com seus dados do MySQL (XAMPP):
# DB_HOST=127.0.0.1
# DB_DATABASE=isp_system
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Criar banco no MySQL
mysql -u root -e "CREATE DATABASE isp_system;"

# 5. Rodar migrations
php artisan migrate

# 6. Gerar chave
php artisan key:generate

# 7. Iniciar servidor
php artisan serve
```

---

## 📡 API Endpoints

| Método | Rota                            | Descrição               |
|--------|---------------------------------|-------------------------|
| GET    | /api/v1/clients                 | Listar clientes         |
| POST   | /api/v1/clients                 | Criar cliente           |
| GET    | /api/v1/clients/{id}            | Ver cliente             |
| POST   | /api/v1/clients/{id}/suspend    | Suspender cliente       |
| POST   | /api/v1/clients/{id}/activate   | Reativar cliente        |
| GET    | /api/v1/invoices                | Listar faturas          |
| POST   | /api/v1/invoices/{id}/pay       | Registrar pagamento     |
| POST   | /api/v1/clients/{id}/tickets    | Abrir chamado           |
| POST   | /api/v1/tickets/{id}/resolve    | Resolver chamado        |

---

## ⚙️ Automação (Scheduler)

Adicione ao crontab do Ubuntu:
```bash
crontab -e
# Adicione:
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

Tarefas automáticas:
- **Diária** → Gera faturas do mês + marca vencidas como overdue
- **Horária** → Suspende clientes inadimplentes

---

## 🧪 Testes

```bash
php artisan test
php artisan test --filter ClientServiceTest
```

---

## 🌐 Mikrotik

Atualmente **mockado** (simulado com logs).
Quando tiver hardware Mikrotik, substitua o `MikrotikService` pela biblioteca:
```bash
composer require evilfreelancer/routeros-api-php
```

---

## 📂 Estrutura

```
app/
├── Domains/
│   ├── Client/     ← regras do cliente, contrato
│   ├── Billing/    ← faturas, suspensão
│   ├── Network/    ← conexão, Mikrotik
│   └── Support/    ← tickets
├── Application/
│   └── UseCases/   ← orquestração
├── Infrastructure/
│   └── External/   ← MikrotikService
└── Http/
    └── Controllers/ ← só entrada/saída
```
