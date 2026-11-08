<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function canManageMarketers(): bool
{
    $designation = strtolower(trim((string) ($_SESSION['designation'] ?? '')));

    return ($_SESSION['loggedin'] ?? false) === true
        && in_array($designation, ['admin', 'md'], true);
}
