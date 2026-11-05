<?php
namespace Microblog\Services;

use Microblog\Database\ConexaoBD;
use Microblog\Models\Categoria;
use PDO;

class CategoriaServico
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = ConexaoBD::getConexao();
    }

    public function listar(): array
    {
        $sql = "SELECT * FROM categorias ORDER BY nome";

        $consulta = $this->conexao->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
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

    public function excluir(int $id): void
    {
        $sql = "DELETE FROM categorias
                WHERE id = :id";

        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(":id", $id);

        $consulta->execute();
    }
}