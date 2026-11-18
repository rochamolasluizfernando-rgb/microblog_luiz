<?php

namespace Microblog\Database;

use Microblog\Helpers\Utils;
use Exception;
use PDO;
use PDOException;

final class ConexaoBD
{

    private function __construct() {}

    private static PDO $conexao;
    private static string $servidor = "sql307.infinityfree.com";
    private static string $usuario = "if0_43472893";
    private static string $senha = "2OYfn6HRgolGzyw";
    private static string $banco = "if0_43472893_microblog";

    public static function getConexao(): PDO
    {
        if (!isset(self::$conexao)) {
            try {
                self::$conexao = new PDO(
                    "mysql:host=" . self::$servidor . ";dbname=" . self::$banco . ";charset=utf8mb4",
                    self::$usuario,
                    self::$senha
                );

                self::$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // echo "deu";
            } catch (PDOException $e) {
                Utils::registrarLog($e);

                throw new Exception(
                    "Erro ao conectar ao banco de dados. Contate o suporte.",
                    0, // Código de erro genérico que não revela detalhes sensíveis
                    $e // Exceção original como causa
                );
            }
        }

        return self::$conexao;
    }
}

// Teste:
// ConexaoBD::getConexao();
