<?php require_once __DIR__ . '/includes/cabecalho.php'; ?>

<?php
$erroDestaques = false;
try {
    $destaques = (new \Microblog\Services\NoticiaServico())->listarDestaques();
} catch (Exception $e) {
    $erroDestaques = true;
}
?>

<div class="row my-1 mx-md-n1">
    <section class="col-12">
        <h2 class="text-center">Notícias em destaque</h2>

        <?php if ($erroDestaques): ?>
            <p class="alert alert-danger">Não foi possível carregar as notícias em destaque.</p>
        <?php elseif (empty($destaques)): ?>
            <p class="text-center">Não há notícias em destaque cadastradas.</p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($destaques as $noticia): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <article class="card shadow-sm h-100">
                            <a href="noticia.php?id=<?= (int) $noticia['id'] ?>" class="card-link">
                                <img
                                    src="images/<?= rawurlencode((string) $noticia['imagem']) ?>"
                                    class="card-img-top"
                                    alt="<?= \Microblog\Helpers\Utils::escaparHtml($noticia['titulo']) ?>"
                                >
                                <div class="card-body">
                                    <h3 class="fs-4 card-title"><?= \Microblog\Helpers\Utils::escaparHtml($noticia['titulo']) ?></h3>
                                    <p class="card-text"><?= \Microblog\Helpers\Utils::escaparHtml($noticia['resumo']) ?></p>
                                </div>
                            </a>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php
require_once __DIR__ . '/includes/todas.php';
require_once __DIR__ . '/includes/rodape.php';
?>
