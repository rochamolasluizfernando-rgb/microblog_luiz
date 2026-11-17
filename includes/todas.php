<?php
use Microblog\Helpers\Utils;
use Microblog\Services\NoticiaServico;

$todasNoticias = [];
$erroTodasNoticias = false;

try {
    $todasNoticias = (new NoticiaServico())->listarPublicas();
} catch (Exception $e) {
    $erroTodasNoticias = true;
}
?>

<section class="mt-5" aria-labelledby="titulo-todas-noticias">
    <h2 id="titulo-todas-noticias" class="fs-4 mb-4">Todas as notícias</h2>
    <?php if ($erroTodasNoticias): ?>
        <p class="alert alert-danger">Não foi possível carregar as notícias.</p>
    <?php elseif ($todasNoticias === []): ?>
        <p class="alert alert-info">Não há notícias cadastradas.</p>
    <?php else: ?>
        <ul class="lista-noticias">
            <?php foreach ($todasNoticias as $noticia): ?>
                <?php $imagem = basename(str_replace('\\', '/', (string) $noticia['imagem'])); ?>
                <li>
                    <article class="card card-noticia shadow-sm h-100">
                        <a href="noticia.php?id=<?= Utils::escaparHtml($noticia['id']) ?>" class="card-link">
                            <img src="images/<?= Utils::escaparHtml($imagem) ?>"
                                 class="card-img-top" alt="<?= Utils::escaparHtml($noticia['titulo']) ?>">
                            <div class="card-body">
                                <h3 class="fs-5 card-title"><?= Utils::escaparHtml($noticia['titulo']) ?></h3>
                                <p class="card-text"><?= Utils::escaparHtml($noticia['resumo']) ?></p>
                            </div>
                        </a>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
