<?php
/**
 * Authentication Helper
 */

require_once __DIR__ . '/config.php';

session_name(SESSION_NAME);
session_start();

function is_logged_in() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /admin/');
        exit;
    }
}

function login($password) {
    if ($password === ADMIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header('Location: /admin/');
    exit;
}
