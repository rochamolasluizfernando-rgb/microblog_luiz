<?php

use Microblog\Helpers\Utils;
use Microblog\Services\NoticiaServico;

require_once __DIR__ . '/includes/cabecalho.php';

$destaques = [];
$mensagemErro = '';

try {
    $servicoNoticia = new NoticiaServico();
    $destaques = $servicoNoticia->listarDestaques();
} catch (Exception $e) {
    die($e->getMessage());
}
?>

<section aria-labelledby="titulo-destaques">
    <h2 id="titulo-destaques" class="fs-4 my-4">Notícias em destaque</h2>
    <?php if ($mensagemErro !== ''): ?>
        <p class="alert alert-danger"><?= Utils::escaparHtml($mensagemErro) ?></p>
    <?php elseif ($destaques === []): ?>
        <p class="alert alert-info">Não há notícias em destaque cadastradas.</p>
    <?php else: ?>
        <div class="row my-1 mx-md-n1">
            <?php foreach ($destaques as $noticia): ?>
                <?php $imagem = basename(str_replace('\\', '/', (string) $noticia['imagem'])); ?>
                <div class="col-md-6 my-1 px-md-1">
                    <article class="card card-noticia shadow-sm h-100">
                        <a href="noticia.php?id=<?= Utils::escaparHtml($noticia['id']) ?>" class="card-link">
                            <img src="images/<?= Utils::escaparHtml($imagem) ?>"
                                class="card-img-top" alt="<?= Utils::escaparHtml($noticia['titulo']) ?>">
                            <div class="card-body">
                                <h3 class="fs-4 card-title"><?= Utils::escaparHtml($noticia['titulo']) ?></h3>
                                <p class="card-text"><?= Utils::escaparHtml($noticia['resumo']) ?></p>
                            </div>
                        </a>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
require_once __DIR__ . '/includes/todas.php';
require_once __DIR__ . '/includes/rodape.php';
?>