<?php

namespace Microblog\Auth;

final class ControleDeAcesso
{
    private function __construct() {}

    private static function iniciarSessao(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private static function encerrarSessao(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function exigirLogin(): void
    {
        self::iniciarSessao();

        if (!isset($_SESSION['id'])) {
            header("Location: ../login.php?acesso_proibido=1");
            exit;
        }
    }

    public static function login(
        int $id,
        string $nome,
        string $tipo
    ): void {
        self::iniciarSessao();

        $_SESSION['id'] = $id;
        $_SESSION['nome'] = $nome;
        $_SESSION['tipo'] = $tipo;
    }

    public static function logout(): void
    {
        self::iniciarSessao();
        self::encerrarSessao();
        header("Location:../login.php?logout");
    }
}