<?php

use Microblog\Auth\ControleDeAcesso;
use Microblog\Enums\Destaque;
use Microblog\Enums\TipoUsuario;
use Microblog\Helpers\Utils;
use Microblog\Helpers\Validacoes;
use Microblog\Models\Noticia;
use Microblog\Services\CategoriaServico;
use Microblog\Services\NoticiaServico;

require_once __DIR__ . '/../vendor/autoload.php';

ControleDeAcesso::exigirLogin();

$id = Utils::normalizarEntrada($_GET['id'] ?? null, 'inteiro');
Utils::verificarId($id);

$mensagemErro = "";
$dadosNoticia = null;
$listaDeCategorias = [];
$titulo = "";
$texto = "";
$resumo = "";
$destaque = Destaque::NAO->value;
$categoria = 0;
$categoriasCarregadas = false;
$noticiaServico = null;
$categoriaServico = null;

try {
    $tipoUsuario = TipoUsuario::from($_SESSION['tipo']);
    $usuarioId = (int) $_SESSION['id'];

    $noticiaServico = new NoticiaServico();
    $dadosNoticia = $noticiaServico->buscarPorId($id, $tipoUsuario, $usuarioId);

    if ($dadosNoticia === null) {
        $mensagemErro = "Notícia não encontrada ou não autorizada.";
    } else {
        $titulo = $dadosNoticia['titulo'];
        $texto = $dadosNoticia['texto'];
        $resumo = $dadosNoticia['resumo'];
        $destaque = $dadosNoticia['destaque'];
        $categoria = (int) ($dadosNoticia['categoria_id'] ?? 0);

        $categoriaServico = new CategoriaServico();
        $listaDeCategorias = $categoriaServico->listarTodos();
        $categoriasCarregadas = true;
    }
} catch (Throwable $e) {
    Utils::registrarLog($e);
    $mensagemErro = "Não foi possível carregar os dados da notícia.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $dadosNoticia !== null && $mensagemErro === "") {
    try {
        $titulo = Utils::normalizarEntrada($_POST['titulo'] ?? '');
        $texto = Utils::normalizarEntrada($_POST['texto'] ?? '');
        $resumo = Utils::normalizarEntrada($_POST['resumo'] ?? '');
        $destaque = Utils::normalizarEntrada($_POST['destaque'] ?? '');
        $categoria = Utils::normalizarEntrada($_POST['categoria'] ?? '', 'inteiro');

        Validacoes::validarCategoria($categoria);
        Validacoes::validarTitulo($titulo);
        Validacoes::validarTexto($texto);
        Validacoes::validarResumo($resumo);
        Validacoes::validarDestaque($destaque);

        if ($categoriaServico->buscarPorId($categoria) === null) {
            throw new InvalidArgumentException("Selecione uma categoria válida.");
        }

        $imagem = $dadosNoticia['imagem'];
        $arquivoImagem = $_FILES['imagem'] ?? null;
        if ($arquivoImagem !== null && (!is_array($arquivoImagem) || ($arquivoImagem['error'] ?? null) !== UPLOAD_ERR_NO_FILE)) {
            $imagem = Utils::upload($arquivoImagem);
        }

        $noticia = new Noticia(
            $titulo,
            $texto,
            $resumo,
            $imagem,
            Destaque::from($destaque),
            $usuarioId,
            $categoria,
            $id
        );

        if (!$noticiaServico->atualizar($id, $noticia, $tipoUsuario, $usuarioId)) {
            $mensagemErro = "Notícia não encontrada ou não autorizada.";
        } else {
            $_SESSION['mensagemSucesso'] = "Notícia atualizada com sucesso.";
            header("Location: noticias.php");
            exit;
        }
    } catch (InvalidArgumentException $e) {
        $mensagemErro = $e->getMessage();
    } catch (Throwable $e) {
        Utils::registrarLog($e);
        $mensagemErro = "Não foi possível atualizar a notícia. Tente novamente.";
    }
}

require_once __DIR__ . '/../includes/cabecalho-admin.php';
?>

<div class="row">
    <article class="col-12 bg-white rounded shadow my-1 py-4">
        <h2 class="text-center">Atualizar dados da notícia</h2>

        <?php if ($mensagemErro !== ""): ?>
            <div class="alert alert-danger text-center">
                <?= Utils::escaparHtml($mensagemErro) ?>
            </div>
            <?php if ($dadosNoticia === null): ?>
                <p class="text-center"><a class="btn btn-primary" href="noticias.php">Voltar às notícias</a></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($dadosNoticia !== null && $categoriasCarregadas): ?>
            <form class="mx-auto w-75" action="" method="post" id="form-atualizar" name="form-atualizar" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label" for="categoria">Categoria:</label>
                    <select class="form-select" name="categoria" id="categoria" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($listaDeCategorias as $itemCategoria): ?>
                            <option value="<?= Utils::escaparHtml($itemCategoria['id']) ?>" <?= (int) $itemCategoria['id'] === $categoria ? 'selected' : '' ?>>
                                <?= Utils::escaparHtml($itemCategoria['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="titulo">Título:</label>
                    <input value="<?= Utils::escaparHtml($titulo) ?>" class="form-control" type="text" id="titulo" name="titulo" maxlength="150" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="texto">Texto:</label>
                    <textarea class="form-control" name="texto" id="texto" cols="50" rows="6" required><?= Utils::escaparHtml($texto) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="resumo">Resumo (máximo de 300 caracteres):</label>
                    <span id="maximo" class="badge bg-danger">0</span>
                    <textarea class="form-control" name="resumo" id="resumo" cols="50" rows="2" maxlength="300" required><?= Utils::escaparHtml($resumo) ?></textarea>
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-5">
                            <label for="imagem-existente" class="form-label">Imagem atual:</label>
                            <input value="<?= Utils::escaparHtml($dadosNoticia['imagem']) ?>" class="form-control" type="text" id="imagem-existente" readonly disabled>
                        </div>
                        <div class="col-7 text-end">
                            <img src="../images/<?= rawurlencode($dadosNoticia['imagem']) ?>" alt="Imagem da notícia" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="imagem" class="form-label">Caso queira mudar, selecione outra imagem:</label>
                    <input class="form-control" type="file" id="imagem" name="imagem" accept="image/jpeg, image/gif, image/png, image/webp">
                </div>
                <div class="mb-3">
                    <p>Deixar a notícia em destaque?</p>
                    <input type="radio" class="btn-check" name="destaque" id="option1" autocomplete="off" value="nao"
                        <?= $destaque === 'nao' ? 'checked' : '' ?> required>
                    <label class="btn btn-outline-danger" for="option1">Não</label>
                    <input type="radio" class="btn-check" name="destaque" id="option2" autocomplete="off" value="sim"
                        <?= $destaque === 'sim' ? 'checked' : '' ?>>
                    <label class="btn btn-outline-success" for="option2">Sim</label>
                </div>
                <button class="btn btn-primary" type="submit" name="atualizar"><i class="bi bi-arrow-clockwise"></i> Atualizar notícia</button>
            </form>
        <?php endif; ?>
    </article>
</div>

<script src="js/contador.js"></script>
<?php require_once __DIR__ . '/../includes/rodape-admin.php'; ?>
