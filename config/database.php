<?php
require_once __DIR__ . '/env.php';

class DatabaseConnection
{
    public function conectar(): mysqli
    {
        $servidor = env('DB_HOST', '127.0.0.1');
        $porta = (int) env('DB_PORT', '3306');
        $user = env('DB_USER', 'root');
        $senha = env('DB_PASS', '');
        $database = env('DB_NAME', 'sistema_agendamento');

        $conn = new mysqli($servidor, $user, $senha, $database, $porta);

        if ($conn->connect_error) {
            throw new RuntimeException('ups! conexão falhou: ' . $conn->connect_error);
        }

        $conn->set_charset(env('DB_CHARSET', 'utf8mb4'));

        return $conn;
    }
}

class conexao extends DatabaseConnection
{
}
?>