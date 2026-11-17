<?php
require_once __DIR__ . '/vendor/autoload.php';

\Microblog\Helpers\Utils::verificarId($_GET['id'] ?? null);
$id = (int) $_GET['id'];
$erroNoticia = false;

try {
    $noticia = (new \Microblog\Services\NoticiaServico())->buscarPublicaPorId($id);
} catch (Exception $e) {
    $erroNoticia = true;
    $noticia = null;
}

if ($noticia === null && !$erroNoticia) {
    http_response_code(404);
}

require_once __DIR__ . '/includes/cabecalho.php';
?>

<div class="row my-1 mx-md-n1">
    <article class="col-12">
        <?php if ($erroNoticia): ?>
            <p class="alert alert-danger">Não foi possível carregar a notícia.</p>
        <?php elseif ($noticia === null): ?>
            <h2 class="text-center">Notícia não encontrada</h2>
        <?php else: ?>
            <h2><?= \Microblog\Helpers\Utils::escaparHtml($noticia['titulo']) ?></h2>
            <p class="font-weight-light">
                <time datetime="<?= \Microblog\Helpers\Utils::escaparHtml(date('Y-m-d', strtotime($noticia['data']))) ?>">
                    <?= \Microblog\Helpers\Utils::escaparHtml(date('d/m/Y', strtotime($noticia['data']))) ?>
                </time>
                - <span><?= \Microblog\Helpers\Utils::escaparHtml($noticia['autor'] ?: 'Equipe Microblog') ?></span>
            </p>
            <img
                src="images/<?= rawurlencode((string) $noticia['imagem']) ?>"
                alt="<?= \Microblog\Helpers\Utils::escaparHtml($noticia['titulo']) ?>"
                class="float-start pe-2 img-fluid"
            >
            <p class="ajusta-texto"><?= \Microblog\Helpers\Utils::escaparHtml($noticia['texto']) ?></p>
        <?php endif; ?>
    </article>
</div>

<?php require_once __DIR__ . '/includes/rodape.php'; ?>
