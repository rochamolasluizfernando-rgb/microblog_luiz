<?php
namespace Microblog\Services;

use Exception;
use Microblog\Database\ConexaoBD;
use Microblog\Helpers\Utils;
use Microblog\Models\Categoria;
use PDO;
use PDOException;

final class CategoriaServico
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = ConexaoBD::getConexao();
    }

    public function listarTodos(): array
    {
        $sql = "SELECT * FROM categorias ORDER BY nome";
        
        try {
            $consulta = $this->conexao->query($sql);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao listar categorias");
        }
        
    }

    public function buscarPorId(int $id): ?Categoria
    {
        $sql = "SELECT * FROM categorias WHERE id = :id";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":id", $id);
        $consulta->execute();

        $dados = $consulta->fetch(PDO::FETCH_ASSOC);

        if(!$dados){
            return null;
        }

        return new Categoria(
            $dados["nome"],
            $dados["id"]
        );
    }

    public function inserir(Categoria $categoria): void
    {
        $sql = "INSERT INTO categorias(nome)
                VALUES(:nome)";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":nome",
            $categoria->getNome());

        $consulta->execute();
    }

    public function atualizar(Categoria $categoria): void
    {
        $sql = "UPDATE categorias
                SET nome = :nome
                WHERE id = :id";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":nome",
            $categoria->getNome());

        $consulta->bindValue(":id",
            $categoria->getId());

        $consulta->execute();
    }
}
