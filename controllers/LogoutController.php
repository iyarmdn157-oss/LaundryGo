<?php

/**
 * LogoutController
 * Controller berbasis OOP (PHP Native) - dibungkus tanpa mengubah alur logika yang sudah ada.
 */
class LogoutController
{
    public function handle(): void
    {
        session_start();

        session_unset();
        session_destroy();

        header("Location: /laundrygo/views/login.php");
        exit;
    }
}

(new LogoutController())->handle();

