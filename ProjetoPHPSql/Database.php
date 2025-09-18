<?php
require_once "config.php";

class Database {

    private $conn;

    public function __construct() {
        try {
            // Conexão inicial sem selecionar banco
            $this->conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Criar o banco de dados se não existir
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);

            // Conectar ao banco de dados
            $this->conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    public function criarTabela($comando) {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS $comando";
            $this->conn->exec($sql);
            echo "Tabela criada com sucesso<br>";
        } catch (PDOException $e) {
            die("Erro ao criar tabela: " . $e->getMessage());
        }
    }

    public function inserir($tabela, $dados) {
        try {
            $colunas = implode(", ", array_keys($dados));
            $placeholders = ":" . implode(", :", array_keys($dados));

            $sql = "INSERT IGNORE INTO $tabela ($colunas) VALUES ($placeholders)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($dados);

            echo "Dados inseridos com sucesso<br>";
        } catch (PDOException $e) {
            die("Erro ao inserir dados: " . $e->getMessage());
        }
    }

    public function buscarTodos($tabela) {
        $sql = "Select * from $tabela";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Criar instância da classe Database
$db = new Database();

// Criar tabela "usuarios"
$db->criarTabela("
usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
)
");

// Inserir um usuário
$db->inserir('usuarios', ['nome' => 'Arthur Malospirito','email' => 'arthur@gmail.com']);

$busca = $db->buscarTodos('usuarios');
echo '<br>';
var_dump($busca);
echo '<br>';
echo '<h1>'. $busca[0]['nome'].'</h1>';
echo '<br>';

?>

