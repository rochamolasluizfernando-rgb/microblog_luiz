<?php
require_once __DIR__ . '/vendor/autoload.php';

\Microblog\Helpers\Utils::verificarId($_GET['id'] ?? null);
$id = (int) $_GET['id'];
$erroCategoria = false;
$categoria = null;
$noticias = [];

try {
    $categoria = (new \Microblog\Services\CategoriaServico())->buscarPorId($id);
    if ($categoria !== null) {
        $noticias = (new \Microblog\Services\NoticiaServico())->listarPublicasPorCategoria($id);
    }
} catch (Exception $e) {
    $erroCategoria = true;
}

if ($categoria === null && !$erroCategoria) {
    http_response_code(404);
}

require_once __DIR__ . '/includes/cabecalho.php';
?>

<div class="row my-1 mx-md-n1">
    <article class="col-12">
        <?php if ($erroCategoria): ?>
            <p class="alert alert-danger">Não foi possível carregar as notícias desta categoria.</p>
        <?php elseif ($categoria === null): ?>
            <h2 class="text-center">Categoria não encontrada</h2>
        <?php else: ?>
            <h2 class="text-center">
                Notícias sobre <span class="badge bg-primary"><?= \Microblog\Helpers\Utils::escaparHtml($categoria['nome']) ?></span>
            </h2>

            <div class="row my-1">
                <div class="col-12 px-md-1">
                    <div class="list-group">
                        <?php if (empty($noticias)): ?>
                            <p class="text-center">Não há notícias cadastradas nesta categoria.</p>
                        <?php else: ?>
                            <?php foreach ($noticias as $noticia): ?>
                                <a href="noticia.php?id=<?= (int) $noticia['id'] ?>" class="list-group-item list-group-item-action">
                                    <h3 class="fs-6"><?= \Microblog\Helpers\Utils::escaparHtml($noticia['titulo']) ?></h3>
                                    <p>
                                        <time datetime="<?= \Microblog\Helpers\Utils::escaparHtml(date('Y-m-d', strtotime($noticia['data']))) ?>">
                                            <?= \Microblog\Helpers\Utils::escaparHtml(date('d/m/Y', strtotime($noticia['data']))) ?>
                                        </time>
                                        - <?= \Microblog\Helpers\Utils::escaparHtml($noticia['autor'] ?: 'Equipe Microblog') ?>
                                    </p>
                                    <p><?= \Microblog\Helpers\Utils::escaparHtml($noticia['resumo']) ?></p>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </article>
</div>

<?php
require_once __DIR__ . '/includes/rodape.php';
?>
