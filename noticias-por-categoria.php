<?php
use Microblog\Helpers\Utils;
use Microblog\Services\CategoriaServico;
use Microblog\Services\NoticiaServico;

require_once __DIR__ . '/includes/cabecalho.php';

$valorId = $_GET['id'] ?? null;
$id = filter_var($valorId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$categoria = null;
$noticias = [];
$mensagemErro = '';

if ($id === false) {
    http_response_code(400);
} else {
    try {
        $categoria = (new CategoriaServico())->buscarPorId($id);
        if ($categoria === null) {
            http_response_code(404);
        } else {
            $noticias = (new NoticiaServico())->listarPublicas($id);
        }
    } catch (Exception $e) {
        $mensagemErro = 'Não foi possível carregar as notícias da categoria.';
    }
}
?>

<?php if ($id === false): ?>
    <p class="alert alert-warning">Identificador de categoria inválido.</p>
<?php elseif ($mensagemErro !== ''): ?>
    <p class="alert alert-danger"><?= Utils::escaparHtml($mensagemErro) ?></p>
<?php elseif ($categoria === null): ?>
    <p class="alert alert-info">Categoria não encontrada.</p>
<?php else: ?>
    <section aria-labelledby="titulo-categoria">
        <h2 id="titulo-categoria" class="text-center my-4">
            Notícias sobre <span class="badge bg-primary"><?= Utils::escaparHtml($categoria['nome']) ?></span>
        </h2>
        <?php if ($noticias === []): ?>
            <p class="alert alert-info">Não existem notícias cadastradas nesta categoria.</p>
        <?php else: ?>
            <ul class="lista-noticias">
                <?php foreach ($noticias as $noticia): ?>
                    <?php
                    $imagem = basename(str_replace('\\', '/', (string) $noticia['imagem']));
                    $dataPublicacao = strtotime((string) $noticia['data']);
                    $dataFormatada = date('d/m/Y', $dataPublicacao);
                    $dataIso = date('Y-m-d', $dataPublicacao);
                    $autor = $noticia['autor'] ?: 'Equipe Microblog';
                    ?>
                    <li>
                        <article class="card card-noticia shadow-sm h-100">
                            <a href="noticia.php?id=<?= Utils::escaparHtml($noticia['id']) ?>" class="card-link">
                                <img src="images/<?= Utils::escaparHtml($imagem) ?>"
                                     class="card-img-top" alt="<?= Utils::escaparHtml($noticia['titulo']) ?>">
                                <div class="card-body">
                                    <h3 class="fs-5 card-title"><?= Utils::escaparHtml($noticia['titulo']) ?></h3>
                                    <p class="text-muted mb-2">
                                        <time datetime="<?= Utils::escaparHtml($dataIso) ?>"><?= Utils::escaparHtml($dataFormatada) ?></time>
                                        - <?= Utils::escaparHtml($autor) ?>
                                    </p>
                                    <p class="card-text"><?= Utils::escaparHtml($noticia['resumo']) ?></p>
                                </div>
                            </a>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/rodape.php'; ?>
