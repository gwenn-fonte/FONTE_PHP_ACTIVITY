<?php
// Mga helper functions na gagamitin ng login.php at register.php

define('USERS_FILE', __DIR__ . '/../data/users.json');

function load_users(): array {
    if (!file_exists(USERS_FILE)) return [];
    $data = json_decode(file_get_contents(USERS_FILE), true);
    return is_array($data) ? $data : [];
}

function save_users(array $users): void {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

// Safe na pag-print ng text sa HTML
function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Ipakita ang error message sa ilalim ng input
function field_error(array $errors, string $key): void {
    if (isset($errors[$key])) {
        echo '<label class="error">' . e($errors[$key]) . '</label>';
    }
}
