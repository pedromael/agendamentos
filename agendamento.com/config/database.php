<?php
class conexao
{
    public function conectar()
    {
        $servidor = "localhost";
        $user = "root";
        $senha = "";
        $database = "sistema_agendamento";
        $conn = new mysql($servidor,$user,$senha,$database);
        if(!$conn)
        {
            echo "ups! conexão falhou";
        }       
    }
}
?>