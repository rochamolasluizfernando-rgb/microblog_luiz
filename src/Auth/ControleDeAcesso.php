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
        exit;
    }

    // Verifica se o usuário logado é do tipo admin 
    public static function usuarioEhAdmin(): bool
    {
        self::iniciarSessao();

        // Se variável de sessão 'tipo' existe E o valor dela é 'admin'
        // então significa que o usuário logado é um administrador
        return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin';
    }

    public static function exigirAdmin(): void
    {
        // Garanta que está logado
        self::exigirLogin();

        // Verifica se NÃO É um admin
        if(!self::usuarioEhAdmin()){
            // Não sendo, manda pra página nao-autorizado
            header("location:nao-autorizado.php");
            exit;
        }
    }
}