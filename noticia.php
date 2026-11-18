<?php
use Microblog\Helpers\Utils;
use Microblog\Services\NoticiaServico;

require_once __DIR__ . '/includes/cabecalho.php';

$valorId = $_GET['id'] ?? null;
$id = filter_var($valorId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$noticia = null;
$mensagemErro = '';

if ($id === false) {
    http_response_code(400);
} else {
    try {
        $noticia = (new NoticiaServico())->buscarPublicaPorId($id);
        if ($noticia === null) {
            http_response_code(404);
        }
    } catch (Exception $e) {
        $mensagemErro = 'Não foi possível carregar a notícia.';
    }
}
?>

<?php if ($id === false): ?>
    <p class="alert alert-warning">Identificador de notícia inválido.</p>
<?php elseif ($mensagemErro !== ''): ?>
    <p class="alert alert-danger"><?= Utils::escaparHtml($mensagemErro) ?></p>
<?php elseif ($noticia === null): ?>
    <p class="alert alert-info">Notícia não encontrada.</p>
<?php else: ?>
    <?php
    $imagem = basename(str_replace('\\', '/', (string) $noticia['imagem']));
    $dataPublicacao = strtotime((string) $noticia['data']);
    $dataFormatada = date('d/m/Y', $dataPublicacao);
    $dataIso = date('Y-m-d', $dataPublicacao);
    $autor = $noticia['autor'] ?: 'Equipe Microblog';
    ?>
    <article class="noticia-completa">
        <h2><?= Utils::escaparHtml($noticia['titulo']) ?></h2>
        <p class="text-muted">
            <time datetime="<?= Utils::escaparHtml($dataIso) ?>"><?= Utils::escaparHtml($dataFormatada) ?></time>
            - <?= Utils::escaparHtml($autor) ?>
        </p>
        <img src="images/<?= Utils::escaparHtml($imagem) ?>"
             alt="<?= Utils::escaparHtml($noticia['titulo']) ?>" class="img-fluid imagem-noticia-completa">
        <div class="ajusta-texto mt-3"><?= Utils::escaparHtml($noticia['texto']) ?></div>
    </article>
<?php endif; ?>

<?php
if ($id !== false && $noticia !== null && $mensagemErro === '') {
    require_once __DIR__ . '/includes/todas.php';
}
require_once __DIR__ . '/includes/rodape.php';
?>
