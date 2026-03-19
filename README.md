# Agendamento

Projeto web em PHP + MySQL/MariaDB para autenticação e gestão de agendamentos.

O frontend legado em HTML estático foi removido. A página principal agora é pública para o usuário fazer agendamento sem login; o acesso administrativo continua protegido por sessão.

## Estrutura

- `index.php`: entrada da aplicação (redireciona para agendamento público)
- `config/`: carregamento de ambiente e conexão com banco
- `database/sistema_agendamento.sql`: criação do banco e tabelas
- `src/auth/`: login, logout e sessão
- `src/views/public/agendar.php`: formulário público de agendamento
- `src/views/auth/login.php`: tela de login
- `src/views/dashboard/index.php`: painel com indicadores
- `src/views/agendamentos/index.php`: CRUD básico de agendamentos
- `src/views/publicacoes/index.php`: gestão de publicações dos administradores

## Execução local

1. Configure o `.env` com os dados do banco.
2. Importe o SQL inicial:

```bash
sudo mariadb < database/sistema_agendamento.sql
```

3. Suba o servidor PHP:
   - Task VS Code: **Run PHP server (agendamento)**
   - ou terminal: `php -S 127.0.0.1:8000 -t .`

4. Acesse: `http://127.0.0.1:8000`

## Usuário inicial

O script SQL já cria um utilizador padrão:

- Email: `admin@local.test`
- Senha: `123456`

## Fluxo disponível

- Agendamento público sem login (`/src/views/public/agendar.php` ou `/`)
- Página pública exibe publicações dos administradores e formulário de agendamento
- Login administrativo com sessão (`/src/views/auth/login.php`)
- Logout (`/src/auth/logout.php`)
- Dashboard com contagem de agendamentos por status
- CRUD básico em `/src/views/agendamentos/index.php`:
  - criar agendamento
  - atualizar status (`pendente`, `confirmado`, `cancelado`)
  - remover agendamento
- Publicações administrativas em `/src/views/publicacoes/index.php`:
   - criar publicação
   - remover publicação
