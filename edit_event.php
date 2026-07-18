<?php
/**
 * Auth Guard
 * Sertakan file ini di setiap halaman yang membutuhkan login admin.
 */
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
