<?php
use Microblog\Helpers\Utils;
use Microblog\Services\NoticiaServico;

$todasNoticias = [];
$erroTodasNoticias = false;

try {
    $todasNoticias = (new NoticiaServico())->listarPublicas();
} catch (Exception $e) {
    die($e->getMessage());
}
?>

<hr class="my-5 w-50 mx-auto">

<section class="row my-1" aria-labelledby="titulo-todas-noticias">
    <div class="col-12 px-md-1">
        <div class="list-group">
            <h2 id="titulo-todas-noticias" class="fs-6 text-center text-muted">Todas as notícias</h2>
    <?php if ($erroTodasNoticias): ?>
        <p class="list-group-item text-danger">Não foi possível carregar as notícias.</p>
    <?php elseif ($todasNoticias === []): ?>
        <p class="list-group-item text-muted">Não há notícias cadastradas.</p>
    <?php else: ?>
            <?php foreach ($todasNoticias as $noticia): ?>
                <a href="noticia.php?id=<?= Utils::escaparHtml($noticia['id']) ?>"
                   class="list-group-item list-group-item-action">
                    <h3 class="fs-6">
                        <time datetime="<?= Utils::escaparHtml($noticia['data']) ?>">
                            <?= Utils::escaparHtml(Utils::formataData((string) $noticia['data'])) ?>
                        </time>
                        - <?= Utils::escaparHtml($noticia['titulo']) ?>
                    </h3>
                    <p class="mb-0"><?= Utils::escaparHtml($noticia['resumo']) ?></p>
                </a>
            <?php endforeach; ?>
    <?php endif; ?>
        </div>
    </div>
</section>
