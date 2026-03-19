# Agendamento

Projeto web em PHP + MySQL/MariaDB para autenticação e gestão de agendamentos.

## Estrutura

- `index.php`: entrada da aplicação (redireciona para comunicados públicos)
- `config/`: carregamento de ambiente e conexão com banco
- `database/sistema_agendamento.sql`: criação do banco e tabelas
- `src/auth/`: login, logout e sessão
- `src/views/public/index.php`: comunicados públicos da administração
- `src/views/public/agendar.php`: formulário público de agendamento
- `src/views/auth/login.php`: tela de login
- `src/views/auth/register.php`: registro inicial da conta administrativa
- `src/views/auth/change_password.php`: página separada para troca de senha
- `src/views/dashboard/index.php`: painel com indicadores
- `src/views/agendamentos/index.php`: CRUD básico de agendamentos
- `src/views/publicacoes/index.php`: gestão de publicações dos administradores
- `src/views/usuarios/index.php`: gestão de contas administrativas

## Conta administrativa inicial

Na primeira execução, se não existir utilizador na tabela `usuarios`, o sistema redireciona para o registro inicial em `/src/views/auth/register.php`.

Depois que a primeira conta é criada, o registro inicial é bloqueado e o acesso administrativo segue pelo login em `/src/views/auth/login.php`.

## Fluxo disponível

- Página principal pública de comunicados (`/src/views/public/index.php` ou `/`)
- Formulário público de agendamento (`/src/views/public/agendar.php`)
- Registro inicial da conta administrativa (somente sem utilizadores)
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
- Gestão de administradores em `/src/views/usuarios/index.php`:
  - cadastrar outro administrador (com sessão ativa)
  - listar contas administrativas
- Troca de senha administrativa em página própria (`/src/views/auth/change_password.php`)
