<?php
$erroNoticias = false;
try {
    $noticias = (new \Microblog\Services\NoticiaServico())->listarPublicas();
} catch (Exception $e) {
    $erroNoticias = true;
}
?>

<hr class="my-5 w-50 mx-auto">

<section class="row my-1">
    <div class="col-12 px-md-1">
        <h2 class="fs-5 text-center text-muted">Todas as notícias</h2>
        <div class="list-group">
            <?php if ($erroNoticias): ?>
                <p class="alert alert-danger">Não foi possível carregar as notícias.</p>
            <?php elseif (empty($noticias)): ?>
                <p class="text-center">Ainda não há notícias cadastradas.</p>
            <?php else: ?>
                <?php foreach ($noticias as $noticia): ?>
                    <a href="noticia.php?id=<?= (int) $noticia['id'] ?>" class="list-group-item list-group-item-action">
                        <h3 class="fs-6">
                            <time datetime="<?= \Microblog\Helpers\Utils::escaparHtml(date('Y-m-d', strtotime($noticia['data']))) ?>">
                                <?= \Microblog\Helpers\Utils::escaparHtml(date('d/m/Y', strtotime($noticia['data']))) ?>
                            </time>
                            - <?= \Microblog\Helpers\Utils::escaparHtml($noticia['titulo']) ?>
                        </h3>
                        <p><?= \Microblog\Helpers\Utils::escaparHtml($noticia['resumo']) ?></p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
