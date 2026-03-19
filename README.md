# Agendamento

Projeto web com frontend em HTML/CSS/JS e backend PHP para configuração/conexão com banco MySQL/MariaDB.

## Estrutura

- `index.php`: ponto de entrada (redireciona para login)
- `config/`: configuração da aplicação (`env.php`, `database.php`)
- `database/`: script SQL inicial (`sistema_agendamento.sql`)
- `public/`: assets estáticos (CSS, JS, ícones)
- `src/views/`: páginas HTML do sistema

## Execução local

1. Subir servidor PHP:
   - Task VS Code: **Run PHP server (agendamento)**
   - ou terminal: `php -S 127.0.0.1:8000 -t .`
2. Acessar: `http://127.0.0.1:8000`

## Banco de dados

- Configuração em `.env`
- Importação inicial:

```bash
sudo mariadb < database/sistema_agendamento.sql
```
