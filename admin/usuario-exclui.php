<?php

use Microblog\Helpers\Utils;
use Microblog\Services\UsuarioServico;
use Microblog\Auth\ControleDeAcesso;

require_once __DIR__ . '/../vendor/autoload.php';

ControleDeAcesso::exigirAdmin();

$id = Utils::normalizarEntrada($_GET['id'] ?? null, 'inteiro');
Utils::verificarId($id);

// Impedindo a exclusão do próprio usuário logado 
if($id === $_SESSION['id']){
    Utils::alertaErro("Não é possível excluir a própria conta.");
}

try {
    $usuarioServico = new UsuarioServico();
    $usuarioServico->excluir($id);

    header("Location: usuarios.php");
    exit;
} catch (Throwable $e) {
    Utils::registrarLog($e);
    Utils::alertaErro("Não foi possível excluir o usuário. Tente novamente.");
}