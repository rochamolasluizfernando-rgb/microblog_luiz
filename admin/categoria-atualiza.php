<?php
require_once __DIR__ . '/../includes/cabecalho-admin.php';

use Microblog\Models\Categoria;
use Microblog\Services\CategoriaServico;

require_once __DIR__ . '/../vendor/autoload.php';

$servico = new CategoriaServico();

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

$categoria = $servico->buscarPorId($id);

if(!$categoria){
    die("Categoria não encontrada");
}

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $categoria->setNome(
        $_POST["nome"]
    );

    $servico->atualizar($categoria);

    header("Location: categorias.php");
    exit;
}
?>

<h1>Atualizar Categoria</h1>

<form method="post">

    <p>
        <label for="nome">Nome</label>

        <input
            type="text"
            name="nome"
            id="nome"
            required
            value="<?= $categoria->getNome() ?>">
    </p>

    <button type="submit">
        Atualizar
    </button>

</form>