<?php
require_once __DIR__ . '/../includes/cabecalho-admin.php';

use Microblog\Models\Categoria;
use Microblog\Services\CategoriaServico;

require_once __DIR__ . '/../vendor/autoload.php';

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $categoria = new Categoria(
        $_POST["nome"]
    );

    $servico = new CategoriaServico();

    $servico->inserir($categoria);

    header("Location: categorias.php");
    exit;
}
?>

<h1>Nova Categoria</h1>

<form method="post">

    <p>
        <label for="nome">Nome</label>
        <input
            type="text"
            name="nome"
            id="nome"
            required>
    </p>

    <button type="submit">
        Salvar
    </button>

</form>