<?php
declare(strict_types=1);

require_once __DIR__ . '/Cipher.php';

$samples = [
    ['Lantern', 'PIXIE'],
    ['Agent Nova will rendezvous at Sector 7, not Sector 8.', 'MOONLIGHT'],
    ['The parcel #44 arrives Tuesday; bring exactly $25.', 'ORCHID'],
    ['MiXeD CaSe MANIFESTO', 'LUNA'],
    ['Zara eats 3 apples daily! Run fast, hide deep.', 'VECTOR'],
    [' .?-!  only unwritten  marks  ', 'KEY'],
];

$categories = [
    'A single word',
    'A sentence with spaces',
    'A sentence with spaces',
    'Mixed uppercase and lowercase',
    'Numbers and special characters',
    'Unsupported characters only',
];

$results = [];

foreach ($samples as $index => [$message, $keyword]) {
    $encrypted = Cipher::encrypt($message, $keyword);
    $decrypted = Cipher::decrypt($encrypted, $keyword);

    $results[] = [
        'category' => $categories[$index],
        'message' => $message,
        'keyword' => strtoupper($keyword),
        'encrypted' => $encrypted,
        'decrypted' => $decrypted,
        'passed' => $decrypted === $message,
    ];
}

$emptyRejected = trim('') === '';
$invalidKeyword = 'Runner42!';
$invalidKeywordRejected = !Cipher::isValidKeyword($invalidKeyword);

$handling = [
    [
        'label' => 'Empty input',
        'detail' => 'Message without any characters',
        'expected' => 'Rejected with a clear error message',
        'outcome' => 'Enter a message before encrypting or decrypting.',
        'passed' => $emptyRejected,
    ],
    [
        'label' => 'Invalid keyword',
        'detail' => 'Keyword "' . $invalidKeyword . '" contains digits and symbols',
        'expected' => 'Rejected with a clear error message',
        'outcome' => 'The keyword can only contain letters from A to Z.',
        'passed' => $invalidKeywordRejected,
    ],
];

$passedCount = 0;

foreach ($results as $result) {
    if ($result['passed']) {
        $passedCount++;
    }
}

foreach ($handling as $check) {
    if ($check['passed']) {
        $passedCount++;
    }
}

$allPassed = $passedCount === count($results) + count($handling);
$char = fn(string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<html lang="en" class="h-full bg-[#0c1014]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CipherLab — Self-Test</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/tailwind-config.js"></script>
    <link rel="stylesheet" href="assets/cipher.css">
</head>
<body class="min-h-full bg-[#0c1014] text-slate-100 font-sans antialiased">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">

        <header class="mb-6 flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-lg sm:text-xl font-bold tracking-tight text-white">Self-Test — Required Test Cases</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-slate-400">
                    For valid inputs, encryption followed by decryption returns the original text.
                    Supported characters: A–Z and a–z (encrypted); digits, spaces &amp; punctuation pass through unchanged.
                </p>
            </div>

            <span class="flex items-center gap-1.5 rounded-full border border-slate-700/80 bg-slate-900/90 px-3.5 py-1.5 font-mono text-xs text-slate-300">
                <span class="h-2 w-2 rounded-full <?= $allPassed ? 'bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]' : 'bg-red-500' ?>"></span>
                <?= $passedCount ?>/<?= count($results) + count($handling) ?> passed
            </span>
        </header>

        <h2 class="mb-3 font-mono text-xs font-bold uppercase tracking-wider text-slate-500">Valid inputs — round-trip verification</h2>

        <div class="grid gap-3">
            <?php foreach ($results as $index => $result): ?>
                <div class="rounded-xl <?= $result['passed'] ? 'border border-teal-500/30' : 'border border-red-500/30' ?> bg-[#12181f] p-4 shadow-lg">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="font-mono text-xs text-slate-400">Sample #<?= $index + 1 ?> · <?= $char($result['category']) ?> · key <span class="font-bold text-teal-300"><?= $char($result['keyword']) ?></span></span>
                        <span class="rounded-md border <?= $result['passed'] ? 'border-teal-500/40 bg-teal-500/10 text-teal-300' : 'border-red-500/40 bg-red-500/10 text-red-300' ?> px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider">
                            <?= $result['passed'] ? 'Round-trip PASS' : 'Round-trip FAIL' ?>
                        </span>
                    </div>

                    <dl class="grid gap-1.5 font-mono text-xs leading-relaxed">
                        <div class="flex gap-2">
                            <dt class="w-20 shrink-0 text-slate-500 uppercase text-[10px] pt-0.5">Plain</dt>
                            <dd class="break-words text-slate-200"><?= $char($result['message']) ?></dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="w-20 shrink-0 text-slate-500 uppercase text-[10px] pt-0.5">Encrypted</dt>
                            <dd class="break-words text-teal-200"><?= $char($result['encrypted']) ?></dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="w-20 shrink-0 text-slate-500 uppercase text-[10px] pt-0.5">Decrypted</dt>
                            <dd class="break-words <?= $result['passed'] ? 'text-slate-200' : 'text-red-300' ?>"><?= $char($result['decrypted']) ?></dd>
                        </div>
                    </dl>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 class="mt-6 mb-3 font-mono text-xs font-bold uppercase tracking-wider text-slate-500">Empty or invalid input — handling verification</h2>

        <div class="grid gap-3">
            <?php foreach ($handling as $check): ?>
                <div class="rounded-xl <?= $check['passed'] ? 'border border-teal-500/30' : 'border border-red-500/30' ?> bg-[#12181f] p-4 shadow-lg">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="font-mono text-xs text-slate-400"><?= $char($check['label']) ?> · <span class="text-slate-500"><?= $char($check['detail']) ?></span></span>
                        <span class="rounded-md border <?= $check['passed'] ? 'border-teal-500/40 bg-teal-500/10 text-teal-300' : 'border-red-500/40 bg-red-500/10 text-red-300' ?> px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider">
                            <?= $check['passed'] ? 'Handled PASS' : 'Handled FAIL' ?>
                        </span>
                    </div>
                    <dl class="grid gap-1.5 font-mono text-xs leading-relaxed">
                        <div class="flex gap-2">
                            <dt class="w-20 shrink-0 text-slate-500 uppercase text-[10px] pt-0.5">Expected</dt>
                            <dd class="break-words text-slate-200"><?= $char($check['expected']) ?></dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="w-20 shrink-0 text-slate-500 uppercase text-[10px] pt-0.5">Outcome</dt>
                            <dd class="break-words <?= $check['passed'] ? 'text-teal-200' : 'text-red-300' ?>"><?= $char($check['outcome']) ?></dd>
                        </div>
                    </dl>
                </div>
            <?php endforeach; ?>
        </div>

        <footer class="mt-6 flex items-center justify-between border-t border-slate-800 pt-4 font-mono text-xs text-slate-500">
            <span>Implemented manually — no built-in encryption functions, libraries, or external services.</span>
            <a href="index.php" class="font-semibold transition hover:text-teal-300">← Back to CipherLab</a>
        </footer>
    </div>
</body>
</html>
