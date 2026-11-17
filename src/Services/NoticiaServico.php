<?php
namespace Microblog\Services;

use Exception;
use Microblog\Database\ConexaoBD;
use Microblog\Enums\TipoUsuario;
use Microblog\Helpers\Utils;
use Microblog\Models\Noticia;
use InvalidArgumentException;
use PDO;
use PDOException;
use RuntimeException;

final class NoticiaServico
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = ConexaoBD::getConexao();
    }

    public function listarDestaques(): array
    {
        $sql = "SELECT id, titulo, resumo, imagem
                FROM noticias
                WHERE destaque = :destaque
                ORDER BY data DESC";

        try {
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":destaque", "sim");
            $consulta->execute();
            return $consulta->fetchAll();
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao listar notícias em destaque");
        }
    }

    public function listarPublicas(?int $categoriaId = null): array
    {
        $sql = "SELECT noticias.id, noticias.data, noticias.titulo, noticias.resumo,
                       noticias.imagem, usuarios.nome AS autor
                FROM noticias
                LEFT JOIN usuarios ON noticias.usuario_id = usuarios.id";

        if ($categoriaId !== null) {
            $sql .= " WHERE noticias.categoria_id = :categoria_id";
        }

        $sql .= " ORDER BY noticias.data DESC";

        try {
            $consulta = $this->conexao->prepare($sql);
            if ($categoriaId !== null) {
                $consulta->bindValue(":categoria_id", $categoriaId, PDO::PARAM_INT);
            }
            $consulta->execute();
            return $consulta->fetchAll();
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao listar notícias públicas");
        }
    }

    public function buscarPublicaPorId(int $id): ?array
    {
        $sql = "SELECT noticias.id, noticias.data, noticias.titulo, noticias.texto,
                       noticias.resumo, noticias.imagem, usuarios.nome AS autor
                FROM noticias
                LEFT JOIN usuarios ON noticias.usuario_id = usuarios.id
                WHERE noticias.id = :id";

        try {
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":id", $id, PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetch() ?: null;
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao buscar notícia pública");
        }
    }

    public function listarTodos(TipoUsuario $tipoUsuario, int $usuarioId): array
    {
        if ($tipoUsuario === TipoUsuario::ADMIN) {
            $sql = "SELECT noticias.id, noticias.titulo, noticias.data, noticias.destaque,
                           usuarios.nome AS autor
                    FROM noticias
                    LEFT JOIN usuarios ON noticias.usuario_id = usuarios.id
                    ORDER BY noticias.data DESC";

            try {
                $consulta = $this->conexao->query($sql);
                return $consulta->fetchAll();
            } catch (PDOException $e) {
                Utils::registrarLog($e);
                throw new Exception("Erro ao listar notícias");
            }
        }

        $sql = "SELECT id, titulo, data, destaque
                FROM noticias
                WHERE usuario_id = :usuario_id
                ORDER BY data DESC";

        try {
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":usuario_id", $usuarioId);
            $consulta->execute();
            return $consulta->fetchAll();
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao listar notícias");
        }
    }

    public function buscarPorId(int $id, TipoUsuario $tipoUsuario, int $usuarioId): ?array
    {
        $sql = "SELECT noticias.id, noticias.data, noticias.titulo, noticias.texto,
                       noticias.resumo, noticias.imagem, noticias.destaque,
                       noticias.usuario_id, noticias.categoria_id, usuarios.nome AS autor
                FROM noticias
                LEFT JOIN usuarios ON noticias.usuario_id = usuarios.id
                WHERE noticias.id = :id";

        if ($tipoUsuario === TipoUsuario::EDITOR) {
            $sql .= " AND noticias.usuario_id = :usuario_id";
        }

        try {
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":id", $id);

            if ($tipoUsuario === TipoUsuario::EDITOR) {
                $consulta->bindValue(":usuario_id", $usuarioId);
            }

            $consulta->execute();
            return $consulta->fetch() ?: null;
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao buscar notícia por ID.");
        }
    }

    public function inserir(Noticia $noticia): void
    {
        $sql = "INSERT INTO noticias (
            titulo, texto, resumo, imagem, destaque, usuario_id, categoria_id
        ) VALUES(
            :titulo, :texto, :resumo, :imagem, :destaque, :usuario_id, :categoria_id
        )";

        try {
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(":titulo", $noticia->getTitulo());
            $consulta->bindValue(":texto", $noticia->getTexto());
            $consulta->bindValue(":resumo", $noticia->getResumo());
            $consulta->bindValue(":imagem", $noticia->getImagem());
            $consulta->bindValue(":destaque", $noticia->getDestaque()->value);
            $consulta->bindValue(":usuario_id", $noticia->getUsuarioId());
            $consulta->bindValue(":categoria_id", $noticia->getCategoriaId());

            $consulta->execute();

        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao inserir notícia");
        }        
    }

    public function atualizar(int $id, Noticia $noticia, TipoUsuario $tipoUsuario, int $usuarioId): bool
    {
        try {
            $this->conexao->beginTransaction();

            $sql = "SELECT id FROM noticias WHERE id = :id";
            if ($tipoUsuario === TipoUsuario::EDITOR) {
                $sql .= " AND usuario_id = :usuario_id";
            }
            $sql .= " FOR UPDATE";

            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":id", $id);
            if ($tipoUsuario === TipoUsuario::EDITOR) {
                $consulta->bindValue(":usuario_id", $usuarioId);
            }
            $consulta->execute();

            if (!$consulta->fetch()) {
                $this->conexao->commit();
                return false;
            }

            $sql = "UPDATE noticias
                    SET titulo = :titulo,
                        texto = :texto,
                        resumo = :resumo,
                        imagem = :imagem,
                        destaque = :destaque,
                        categoria_id = :categoria_id
                    WHERE id = :id";
            if ($tipoUsuario === TipoUsuario::EDITOR) {
                $sql .= " AND usuario_id = :usuario_id";
            }

            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":titulo", $noticia->getTitulo());
            $consulta->bindValue(":texto", $noticia->getTexto());
            $consulta->bindValue(":resumo", $noticia->getResumo());
            $consulta->bindValue(":imagem", $noticia->getImagem());
            $consulta->bindValue(":destaque", $noticia->getDestaque()->value);
            $consulta->bindValue(":categoria_id", $noticia->getCategoriaId());
            $consulta->bindValue(":id", $id);
            if ($tipoUsuario === TipoUsuario::EDITOR) {
                $consulta->bindValue(":usuario_id", $usuarioId);
            }
            $consulta->execute();

            $this->conexao->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }
            Utils::registrarLog($e);
            throw new Exception("Erro ao atualizar notícia");
        }
    }

    public function excluir(int $id, TipoUsuario $tipoUsuario, int $usuarioId): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("O ID da notícia deve ser positivo.");
        }

        try {
            $this->conexao->beginTransaction();

            $sql = "SELECT imagem FROM noticias WHERE id = :id";
            if ($tipoUsuario === TipoUsuario::EDITOR) {
                $sql .= " AND usuario_id = :usuario_id";
            }
            $sql .= " FOR UPDATE";

            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":id", $id, PDO::PARAM_INT);
            if ($tipoUsuario === TipoUsuario::EDITOR) {
                $consulta->bindValue(":usuario_id", $usuarioId, PDO::PARAM_INT);
            }
            $consulta->execute();
            $noticia = $consulta->fetch();

            if (!$noticia) {
                $this->conexao->commit();
                return false;
            }

            $sql = "DELETE FROM noticias WHERE id = :id";
            if ($tipoUsuario === TipoUsuario::EDITOR) {
                $sql .= " AND usuario_id = :usuario_id";
            }

            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":id", $id, PDO::PARAM_INT);
            if ($tipoUsuario === TipoUsuario::EDITOR) {
                $consulta->bindValue(":usuario_id", $usuarioId, PDO::PARAM_INT);
            }
            $consulta->execute();

            if ($consulta->rowCount() !== 1) {
                $this->conexao->commit();
                return false;
            }

            $consulta = $this->conexao->prepare(
                "SELECT COUNT(*) FROM noticias WHERE imagem = :imagem"
            );
            $consulta->bindValue(":imagem", $noticia['imagem']);
            $consulta->execute();
            $imagemAindaUtilizada = (int) $consulta->fetchColumn() > 0;

            $this->conexao->commit();
        } catch (PDOException $e) {
            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }
            Utils::registrarLog($e);
            throw new Exception("Erro ao excluir notícia");
        } catch (\Throwable $e) {
            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }
            throw $e;
        }

        $nomeImagem = $noticia['imagem'];
        if (
            !$imagemAindaUtilizada &&
            is_string($nomeImagem) &&
            $nomeImagem !== '' &&
            basename(str_replace('\\', '/', $nomeImagem)) === $nomeImagem
        ) {
            $caminhoImagem = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR .
                'images' . DIRECTORY_SEPARATOR . $nomeImagem;

            if (is_file($caminhoImagem) && !unlink($caminhoImagem)) {
                Utils::registrarLog(new RuntimeException("Não foi possível remover a imagem órfã da notícia."));
            }
        }

        return true;
    }
}
