<?php

use Microblog\Auth\ControleDeAcesso;
use Microblog\Enums\TipoUsuario;
use Microblog\Helpers\Utils;
use Microblog\Services\NoticiaServico;

require_once __DIR__ . '/../vendor/autoload.php';

ControleDeAcesso::exigirLogin();

$mensagemErro = "";
$dadosNoticia = null;
$idValidado = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
$ehPost = $_SERVER['REQUEST_METHOD'] === 'POST';

if (!isset($_SESSION['csrf_exclusao_noticia']) || !is_string($_SESSION['csrf_exclusao_noticia'])) {
    $_SESSION['csrf_exclusao_noticia'] = bin2hex(random_bytes(32));
}
$tokenCsrf = $_SESSION['csrf_exclusao_noticia'];

if ($idValidado === false) {
    http_response_code(400);
    $mensagemErro = "O identificador da notícia é inválido.";
} else {
    $tipoUsuario = is_string($_SESSION['tipo'] ?? null)
        ? TipoUsuario::tryFrom($_SESSION['tipo'])
        : null;
    $usuarioId = filter_var($_SESSION['id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);

    if ($tipoUsuario === null || $usuarioId === false) {
        http_response_code(403);
        $mensagemErro = "Usuário não autorizado.";
    } else {
        try {
            $noticiaServico = new NoticiaServico();
            $dadosNoticia = $noticiaServico->buscarPorId((int) $idValidado, $tipoUsuario, $usuarioId);

            if ($dadosNoticia === null) {
                http_response_code(404);
                $mensagemErro = "Notícia não encontrada ou não autorizada.";
            } elseif ($ehPost) {
                $tokenEnviado = $_POST['csrf_token'] ?? null;

                if (!is_string($tokenEnviado) || !hash_equals($tokenCsrf, $tokenEnviado)) {
                    $mensagemErro = "A confirmação expirou ou é inválida. Tente novamente.";
                } elseif (($_POST['confirmar'] ?? '') !== 'sim') {
                    $mensagemErro = "Confirme a exclusão para continuar.";
                } elseif ($noticiaServico->excluir((int) $idValidado, $tipoUsuario, $usuarioId)) {
                    unset($_SESSION['csrf_exclusao_noticia']);
                    $_SESSION['mensagemSucesso'] = "Notícia excluída com sucesso.";
                    header("Location: noticias.php");
                    exit;
                } else {
                    $dadosNoticia = null;
                    http_response_code(404);
                    $mensagemErro = "Notícia não encontrada ou não autorizada.";
                }
            }
        } catch (Throwable $e) {
            Utils::registrarLog($e);
            $mensagemErro = "Não foi possível excluir a notícia. Tente novamente.";
        }
    }
}

require_once __DIR__ . '/../includes/cabecalho-admin.php';
?>

<div class="row">
    <article class="col-12 bg-white rounded shadow my-1 py-4">
        <h2 class="text-center">Confirmar exclusão de notícia</h2>

        <?php if ($mensagemErro !== ""): ?>
            <div class="alert alert-danger text-center">
                <?= Utils::escaparHtml($mensagemErro) ?>
            </div>
        <?php endif; ?>

        <?php if ($dadosNoticia !== null): ?>
            <div class="mx-auto w-75">
                <p><strong>Título:</strong> <?= Utils::escaparHtml($dadosNoticia['titulo']) ?></p>
                <p><strong>Resumo:</strong> <?= Utils::escaparHtml($dadosNoticia['resumo']) ?></p>
                <p><strong>Data:</strong> <?= Utils::escaparHtml(Utils::formataData($dadosNoticia['data'])) ?></p>
                <?php if ($tipoUsuario === TipoUsuario::ADMIN): ?>
                    <p><strong>Autor:</strong> <?= Utils::escaparHtml($dadosNoticia['autor'] ?? '') ?></p>
                <?php endif; ?>

                <p class="text-danger">Esta ação não pode ser desfeita. Deseja excluir esta notícia?</p>
                <form action="noticia-exclui.php?id=<?= (int) $idValidado ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= Utils::escaparHtml($tokenCsrf) ?>">
                    <button class="btn btn-danger" type="submit" name="confirmar" value="sim">
                        <i class="bi bi-trash"></i> Confirmar exclusão
                    </button>
                    <a class="btn btn-secondary" href="noticias.php">Cancelar</a>
                </form>
            </div>
        <?php else: ?>
            <p class="text-center">
                <a class="btn btn-primary" href="noticias.php">Voltar às notícias</a>
            </p>
        <?php endif; ?>
    </article>
</div>

<?php require_once __DIR__ . '/../includes/rodape-admin.php'; ?>
