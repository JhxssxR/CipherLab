<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';
?>

/**
 * Applies a Vigenère transformation to ASCII letters.
 * Case is preserved; spaces, numbers, punctuation, and other bytes are unchanged.
 */
function transformVigenere(string $text, string $keyword, int $direction): string
{
    $normalizedKey = strtoupper($keyword);
    $keyLength = strlen($normalizedKey);
    $keyIndex = 0;
    $result = '';

    for ($index = 0, $length = strlen($text); $index < $length; $index++) {
        $character = $text[$index];
        $code = ord($character);
        $isUppercase = $code >= 65 && $code <= 90;
        $isLowercase = $code >= 97 && $code <= 122;

        if (!$isUppercase && !$isLowercase) {
            $result .= $character;
            continue;
        }

        $base = $isUppercase ? 65 : 97;
        $shift = ord($normalizedKey[$keyIndex % $keyLength]) - 65;
        $shifted = ($code - $base + ($direction * $shift) + 26) % 26;
        $result .= chr($base + $shifted);
        $keyIndex++;
    }

    return $result;
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$message = '';
$keyword = 'CIPHER';
$output = '';
$action = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = isset($_POST['message']) ? (string) $_POST['message'] : '';
    $keyword = isset($_POST['keyword']) ? trim((string) $_POST['keyword']) : '';
    $action = isset($_POST['action']) ? (string) $_POST['action'] : '';

    if ($message === '') {
        $error = 'Enter a message before encrypting or decrypting.';
    } elseif ($keyword === '') {
        $error = 'Enter a keyword.';
    } elseif (!preg_match('/^[A-Za-z]+$/', $keyword)) {
        $error = 'The keyword can only contain letters from A to Z.';
    } elseif ($action === 'encrypt' || $action === 'decrypt') {
        $direction = $action === 'encrypt' ? 1 : -1;
        $output = transformVigenere($message, $keyword, $direction);
    }
}

$characterCount = strlen($message);
$trimmedMessage = trim($message);
$wordCount = $trimmedMessage === '' ? 0 : count(preg_split('/\s+/', $trimmedMessage));
$isReady = $output !== '';
?>
<!doctype html>
<html lang="en" class="bg-[#071111]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta
        name="description"
        content="An educational Vigenère cipher application built with PHP and Tailwind CSS."
    >
    <title>CipherLab — Vigenère Cipher</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#071111] text-[#edf4f4] antialiased">
    <main class="relative min-h-screen overflow-hidden px-4 sm:px-8 lg:px-16 xl:px-24">
        <div
            class="pointer-events-none absolute -right-32 -top-64 h-[420px] w-[420px] rounded-full bg-[#3ff4c9]/10 blur-[120px]"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute -left-72 bottom-20 h-[420px] w-[420px] rounded-full bg-[#3ff4c9]/5 blur-[120px]"
            aria-hidden="true"
        ></div>

        <header class="relative z-10 mx-auto flex min-h-[88px] w-full max-w-7xl items-center justify-between border-b border-[#b8d7d1]/[0.13]">
            <a href="index.php" class="flex items-center gap-3 text-sm font-extrabold tracking-[0.12em] text-white">
                <span class="grid h-8 w-8 place-items-center rounded-lg border border-[#46dcbc] bg-[#46dcbc]/10 text-[#68f1d1]">
                    <svg class="h-4 w-4 fill-none stroke-current stroke-[1.8]" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                        <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"></path>
                    </svg>
                </span>
                <span>CIPHER<span class="text-[#59ebc8]">LAB</span></span>
            </a>

            <div class="flex items-center gap-2 rounded-full border border-[#b8d7d1]/10 bg-[#0d1f1e]/70 px-3 py-2 font-mono text-[10px] uppercase tracking-[0.08em] text-[#8da39f]">
                <span class="h-1.5 w-1.5 rounded-full bg-[#5cecca] shadow-[0_0_10px_rgba(92,236,202,0.8)]"></span>
                PHP mode
            </div>
        </header>

        <section class="relative z-10 mx-auto flex w-full max-w-7xl flex-col items-start justify-between gap-8 py-14 lg:flex-row lg:items-end lg:py-20">
            <div>
                <p class="mb-5 flex items-center gap-3 font-mono text-[10px] uppercase tracking-[0.16em] text-[#6f8581]">
                    <span class="text-[#63e8c9]">01</span>
                    Cryptography workspace
                </p>
                <h1 class="max-w-3xl text-4xl font-semibold leading-none tracking-[-0.05em] sm:text-6xl lg:text-7xl">
                    Turn plain text into
                    <br>
                    <span class="font-serif font-normal italic text-[#64e7c8]">coded messages.</span>
                </h1>
                <p class="mt-6 max-w-xl text-sm leading-7 text-[#879c98]">
                    Explore polyalphabetic encryption with a classic Vigenère cipher.
                    The transformation is handled securely on your PHP server.
                </p>
            </div>

            <div class="grid min-w-48 gap-1 border-l border-[#b8d7d1]/15 pl-6">
                <span class="font-mono text-[9px] uppercase tracking-[0.14em] text-[#677d79]">Current method</span>
                <strong class="text-base font-semibold">Vigenère Cipher</strong>
                <span class="mt-1 w-fit rounded border border-[#63e8c9]/20 bg-[#63e8c9]/[0.07] px-2 py-1 font-mono text-[8px] uppercase tracking-wider text-[#63e8c9]">
                    Reversible
                </span>
            </div>
        </section>

        <?php if ($error !== ''): ?>
            <div
                class="relative z-10 mx-auto mb-4 flex w-full max-w-7xl items-center gap-3 rounded-lg border border-red-300/20 bg-red-300/[0.06] px-4 py-3 text-sm text-red-200"
                role="alert"
            >
                <svg class="h-5 w-5 shrink-0 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v6M12 17h.01"></path>
                </svg>
                <?= escape($error) ?>
            </div>
        <?php endif; ?>

        <form
            method="post"
            action="index.php"
            class="relative z-10 mx-auto grid w-full max-w-7xl overflow-hidden rounded-2xl border border-[#b8d7d1]/15 bg-[#0b1918]/95 shadow-2xl lg:grid-cols-[1.12fr_0.88fr]"
        >
            <section class="border-b border-[#b8d7d1]/10 p-6 sm:p-10 lg:border-b-0 lg:border-r">
                <div class="mb-8 flex items-center gap-3">
                    <span class="font-mono text-[10px] text-[#57dfbf]">01</span>
                    <h2 class="text-sm font-semibold">Message input</h2>
                </div>

                <label for="message" class="text-[11px] font-semibold text-[#b9cac7]">Your message</label>
                <div class="relative mt-2">
                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        placeholder="Type a message to transform..."
                        spellcheck="false"
                        class="w-full resize-y rounded-xl border border-[#b8d7d1]/15 bg-[#081615] px-5 pb-10 pt-5 font-mono text-[13px] leading-7 text-[#e9f5f2] outline-none transition placeholder:text-[#536662] focus:border-[#5de8c8]/60 focus:ring-4 focus:ring-[#5de8c8]/5"
                    ><?= escape($message) ?></textarea>
                    <span class="absolute bottom-3 right-4 font-mono text-[9px] text-[#536662]">
                        <?= $characterCount ?> chars
                    </span>
                </div>

                <div class="mt-7 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <label for="keyword" class="text-[11px] font-semibold text-[#b9cac7]">Keyword</label>
                        <p class="mt-1 text-[10px] text-[#5f7571]">Use letters only. The keyword repeats across your message.</p>
                    </div>
                    <div class="flex w-full overflow-hidden rounded-lg border border-[#b8d7d1]/15 bg-[#081615] sm:w-auto">
                        <span class="grid place-items-center px-3 font-mono text-[8px] text-[#57706b]">KEY</span>
                        <input
                            id="keyword"
                            name="keyword"
                            type="text"
                            maxlength="24"
                            pattern="[A-Za-z]+"
                            value="<?= escape($keyword) ?>"
                            placeholder="SECRET"
                            autocomplete="off"
                            spellcheck="false"
                            class="w-full border-l border-[#b8d7d1]/10 bg-transparent px-3 py-2 text-center font-mono text-xs uppercase text-[#65e8c9] outline-none placeholder:text-[#3f5651] sm:w-40"
                        >
                    </div>
                </div>

                <div class="mt-8 grid gap-2 sm:grid-cols-3">
                    <button
                        type="submit"
                        name="action"
                        value="encrypt"
                        class="flex min-h-12 items-center justify-center gap-2 rounded-lg bg-[#66e9ca] px-3 text-[11px] font-bold text-[#06211b] transition hover:-translate-y-px hover:bg-[#7bf3d6] focus:outline-none focus:ring-4 focus:ring-[#66e9ca]/15"
                    >
                        <svg class="h-[18px] w-[18px] fill-none stroke-current stroke-[1.8]" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"></path>
                        </svg>
                        Encrypt
                    </button>

                    <button
                        type="submit"
                        name="action"
                        value="decrypt"
                        class="flex min-h-12 items-center justify-center gap-2 rounded-lg border border-[#b8d7d1]/15 px-3 text-[11px] font-bold text-[#b8cbc7] transition hover:border-[#65ebcb]/40 hover:text-white focus:outline-none focus:ring-4 focus:ring-[#66e9ca]/10"
                    >
                        <svg class="h-[18px] w-[18px] fill-none stroke-current stroke-[1.8]" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 7.5-2M12 14v2"></path>
                        </svg>
                        Decrypt
                    </button>

                    <a
                        href="index.php"
                        class="flex min-h-12 items-center justify-center gap-2 rounded-lg border border-[#b8d7d1]/15 px-3 text-[11px] font-bold text-[#b8cbc7] transition hover:border-red-300/30 hover:text-red-200 focus:outline-none focus:ring-4 focus:ring-red-300/10"
                    >
                        <svg class="h-[18px] w-[18px] fill-none stroke-current stroke-[1.8]" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"></path>
                        </svg>
                        Clear
                    </a>
                </div>
            </section>

            <section class="flex flex-col bg-[#05100f]/40 p-6 sm:p-10">
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-[10px] text-[#57dfbf]">02</span>
                        <h2 class="text-sm font-semibold">Result</h2>
                    </div>
                    <span class="flex items-center gap-2 font-mono text-[10px] uppercase tracking-wider <?= $isReady ? 'text-[#66ddc2]' : 'text-[#596e6a]' ?>">
                        <span class="h-1.5 w-1.5 rounded-full <?= $isReady ? 'bg-[#5cecca] shadow-[0_0_10px_rgba(92,236,202,0.8)]' : 'bg-[#516561]' ?>"></span>
                        <?= $isReady ? 'Ready' : 'Waiting' ?>
                    </span>
                </div>

                <div class="grid min-h-64 flex-1 place-items-center rounded-xl border <?= $isReady ? 'border-solid border-[#63e8c9]/20' : 'border-dashed border-[#b8d7d1]/15' ?> bg-[#040d0c]/50 p-7">
                    <?php if ($isReady): ?>
                        <div class="flex h-full w-full flex-col justify-between gap-8">
                            <p class="whitespace-pre-wrap break-words font-mono text-sm leading-7 text-[#c9fff2]"><?= escape($output) ?></p>
                            <span class="font-mono text-[9px] uppercase tracking-wider text-[#5f7772]">
                                <?= $action === 'encrypt' ? 'Encrypted' : 'Decrypted' ?> with key <?= escape(strtoupper($keyword)) ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="max-w-xs text-center">
                            <div class="mx-auto mb-5 grid h-11 w-11 place-items-center rounded-full border border-[#b8d7d1]/10 text-[#69807b]">
                                <svg class="h-4 w-4 fill-none stroke-current stroke-[1.8]" viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                                    <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"></path>
                                </svg>
                            </div>
                            <strong class="text-[11px] font-semibold text-[#82938f]">Your result will appear here</strong>
                            <p class="mt-2 text-[9px] leading-4 text-[#4f625e]">Enter a message, choose a keyword, and select an action.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-6 grid grid-cols-3 border-t border-[#b8d7d1]/10">
                    <div class="flex flex-col gap-2 pt-5">
                        <span class="font-mono text-[8px] uppercase tracking-wider text-[#526762]">Characters</span>
                        <strong class="font-mono text-xs font-medium text-[#a9bbb7]"><?= str_pad((string) $characterCount, 2, '0', STR_PAD_LEFT) ?></strong>
                    </div>
                    <div class="flex flex-col gap-2 pt-5">
                        <span class="font-mono text-[8px] uppercase tracking-wider text-[#526762]">Words</span>
                        <strong class="font-mono text-xs font-medium text-[#a9bbb7]"><?= str_pad((string) $wordCount, 2, '0', STR_PAD_LEFT) ?></strong>
                    </div>
                    <div class="flex flex-col gap-2 pt-5">
                        <span class="font-mono text-[8px] uppercase tracking-wider text-[#526762]">Key length</span>
                        <strong class="font-mono text-xs font-medium text-[#a9bbb7]"><?= str_pad((string) strlen($keyword), 2, '0', STR_PAD_LEFT) ?></strong>
                    </div>
                </div>
            </section>
        </form>

        <footer class="relative z-10 mx-auto flex w-full max-w-7xl flex-col justify-between gap-2 py-9 font-mono text-[8px] uppercase tracking-wider text-[#435753] sm:flex-row">
            <span>CipherLab / IA &amp; Security</span>
            <span>For educational use only — not secure encryption</span>
        </footer>
    </main>
</body>
</html>
