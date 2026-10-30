<?php
declare(strict_types=1);

require_once __DIR__ . '/Cipher.php';

function escape(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$message = '';
$keyword = 'CIPHER';
$output = '';
$action = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = isset($_POST['message'])
        ? (string) $_POST['message']
        : '';

    $keyword = isset($_POST['keyword'])
        ? trim((string) $_POST['keyword'])
        : '';

    $action = isset($_POST['action'])
        ? (string) $_POST['action']
        : '';

    if ($message === '') {
        $error = 'Enter a message before encrypting or decrypting.';
    } elseif ($keyword === '') {
        $error = 'Enter a keyword.';
    } elseif (!Cipher::isValidKeyword($keyword)) {
        $error = 'The keyword can only contain letters from A to Z.';
    } elseif ($action === 'encrypt') {
        $output = Cipher::encrypt($message, $keyword);
    } elseif ($action === 'decrypt') {
        $output = Cipher::decrypt($message, $keyword);
    } else {
        $error = 'Select a valid operation.';
    }
}

$characterCount = strlen($message);

$trimmedMessage = trim($message);

$wordCount = $trimmedMessage === ''
    ? 0
    : count(preg_split('/\s+/', $trimmedMessage));

$isReady = $output !== '';