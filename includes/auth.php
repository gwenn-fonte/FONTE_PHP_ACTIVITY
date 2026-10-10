<?php
// Bantay ng landing page: kapag hindi naka-login, balik sa login.php
session_start();
if (empty($_SESSION['loggedIn'])) {
    header('Location: login.php');
    exit;
}
