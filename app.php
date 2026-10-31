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
$keyword = '';
$effectiveKeyword = 'CIPHER';
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

    $effectiveKeyword = $keyword !== '' ? $keyword : 'CIPHER';

    $action = isset($_POST['action'])
        ? (string) $_POST['action']
        : '';

    if ($message === '') {
        $error = 'Enter a message before encrypting or decrypting.';
    } elseif (!Cipher::isValidKeyword($effectiveKeyword)) {
        $error = 'The keyword can only contain letters from A to Z.';
    } elseif ($action === 'encrypt') {
        $output = Cipher::encrypt($message, $effectiveKeyword);
    } elseif ($action === 'decrypt') {
        $output = Cipher::decrypt($message, $effectiveKeyword);
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

if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos((string)$_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => $error === '',
        'error' => $error,
        'output' => $output,
        'isReady' => $isReady,
        'characterCount' => $characterCount,
        'wordCount' => $wordCount,
        'keyLength' => strlen($effectiveKeyword),
        'action' => $action,
        'keyword' => strtoupper($effectiveKeyword),
    ]);
    exit;
}