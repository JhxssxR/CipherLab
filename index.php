<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';
?>
<!doctype html>
<html lang="en" class="h-full bg-[#0c1014]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta
        name="description"
        content="CipherLab — Real-time educational Vigenère Cipher cryptography workspace."
    >
    <title>CipherLab — Vigenère Cipher</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/tailwind-config.js"></script>
    <link rel="stylesheet" href="assets/cipher.css">
</head>
<body class="h-screen max-h-screen overflow-hidden bg-[#0c1014] text-slate-100 font-sans antialiased selection:bg-teal-500/30 selection:text-teal-200">
    <div class="relative flex h-full flex-col justify-between px-3 sm:px-6 lg:px-10 py-3 lg:py-4 max-w-[1520px] mx-auto">

        <div
            class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-teal-500/[0.07] blur-[100px]"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute -left-24 -bottom-24 h-80 w-80 rounded-full bg-emerald-500/[0.05] blur-[100px]"
            aria-hidden="true"
        ></div>


        <header class="relative z-10 flex shrink-0 items-center justify-between pb-2.5 border-b border-slate-800">
            <a href="index.php" class="flex items-center gap-2.5 text-base font-extrabold tracking-wide text-white group">
                <span class="grid h-8 w-8 place-items-center rounded-lg border border-teal-500/40 bg-teal-500/10 text-teal-300 transition group-hover:scale-105 group-hover:shadow-[0_0_12px_rgba(45,212,191,0.35)]">
                    <svg class="h-4 w-4 fill-none stroke-current stroke-[2]" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                        <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"></path>
                    </svg>
                </span>
                <span>CIPHER<span class="text-teal-400">LAB</span></span>
            </a>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2.5 rounded-full border border-slate-700/80 bg-slate-900/90 px-3.5 py-1.5 font-mono text-xs text-slate-300 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                    <span class="font-medium">Vigenère Cipher</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-teal-300 font-semibold">Reversible</span>
                </div>
            </div>
        </header>


        <div class="relative z-10 flex shrink-0 items-center justify-between py-2 sm:py-2.5">
            <div>
                <h1 class="text-lg sm:text-xl font-bold tracking-tight text-white flex items-center gap-2">
                    Turn plain text into <span class="font-serif italic font-normal text-teal-300">coded messages.</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 hidden sm:block mt-0.5">
                    Polyalphabetic substitution cipher running real-time on your server.
                </p>
            </div>


            <div id="copyToast" class="transition-all duration-300 opacity-0 pointer-events-none flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-teal-500/40 bg-teal-950/80 text-xs font-mono font-medium text-teal-200 shadow-lg">
                <svg class="h-3.5 w-3.5 fill-none stroke-current stroke-2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Result copied!</span>
            </div>
        </div>


        <div
            id="errorAlert"
            class="<?= $error !== '' ? 'flex' : 'hidden' ?> relative z-10 mb-2 shrink-0 items-center gap-2.5 rounded-lg border border-red-500/30 bg-red-950/50 px-3.5 py-2 text-xs sm:text-sm text-red-200"
            role="alert"
        >
            <svg class="h-4 w-4 shrink-0 fill-none stroke-current stroke-2 text-red-400" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 7v6M12 17h.01"></path>
            </svg>
            <span id="errorMessage" class="font-medium"><?= escape($error) ?></span>
        </div>


        <form
            id="cipherForm"
            method="post"
            action="index.php"
            class="relative z-10 flex-1 min-h-0 grid w-full overflow-hidden rounded-2xl border border-slate-700/70 bg-[#12181f] shadow-2xl lg:grid-cols-[1.12fr_0.88fr]"
        >
            <section class="flex flex-col h-full min-h-0 p-4 sm:p-6 bg-[#162029] border-b border-slate-700/60 lg:border-b-0 lg:border-r lg:border-slate-700/60 justify-between gap-3 sm:gap-4 overflow-hidden">
                <div class="flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-md border border-teal-500/30 bg-teal-500/10 text-teal-300">01</span>
                        <h2 class="text-sm sm:text-base font-semibold tracking-wide text-white">Message Input</h2>
                    </div>
                    <span id="charCounterBadge" class="font-mono text-xs font-medium text-slate-300 bg-slate-900/60 px-2.5 py-1 rounded-md border border-slate-800">
                        <span id="liveCharCount" class="text-teal-300 font-bold"><?= $characterCount ?></span> chars
                    </span>
                </div>

                <div class="relative flex-1 min-h-[120px] flex flex-col">
                    <textarea
                        id="message"
                        name="message"
                        placeholder="Type or paste your message to encrypt or decrypt..."
                        spellcheck="false"
                        class="w-full flex-1 resize-none rounded-xl border border-slate-700/80 bg-[#0f151b] p-4 font-mono text-sm sm:text-[15px] leading-relaxed text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-teal-400/80 focus:bg-[#111921] focus:ring-2 focus:ring-teal-400/20"
                    ><?= escape($message) ?></textarea>
                </div>

                <div class="shrink-0 flex items-center justify-between gap-4 rounded-xl border border-slate-700/70 bg-[#10171e] p-3">
                    <div class="flex flex-col">
                        <label for="keyword" class="text-xs sm:text-sm font-semibold text-slate-200">Encryption Keyword</label>
                        <span class="text-xs text-slate-400 font-normal">Letters A-Z only (repeats across text)</span>
                    </div>

                    <div class="flex items-center overflow-hidden rounded-lg border border-slate-700/80 bg-[#0c1217] shadow-inner">
                        <span class="grid place-items-center px-3 py-2 font-mono text-xs font-bold text-slate-400 border-r border-slate-700/80">KEY</span>
                        <input
                            id="keyword"
                            name="keyword"
                            type="text"
                            maxlength="24"
                            pattern="[A-Za-z]+"
                            value="<?= escape($keyword) ?>"
                            placeholder="CIPHER"
                            autocomplete="off"
                            spellcheck="false"
                            class="w-32 sm:w-44 bg-transparent px-3 py-2 text-center font-mono text-sm font-bold uppercase tracking-widest text-teal-300 outline-none placeholder:text-slate-600 focus:text-teal-200"
                        >
                    </div>
                </div>

                <div class="shrink-0 grid grid-cols-3 gap-2.5 pt-0.5">
                    <button
                        type="submit"
                        name="action"
                        value="encrypt"
                        id="btnEncrypt"
                        class="flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-teal-400 to-emerald-400 px-3 text-sm font-bold text-slate-950 transition hover:brightness-110 hover:shadow-[0_0_16px_rgba(45,212,191,0.35)] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-teal-400/50"
                    >
                        <svg class="h-4 w-4 fill-none stroke-current stroke-[2.2]" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"></path>
                        </svg>
                        <span>Encrypt</span>
                    </button>

                    <button
                        type="submit"
                        name="action"
                        value="decrypt"
                        id="btnDecrypt"
                        class="flex h-11 items-center justify-center gap-2 rounded-xl border border-teal-500/40 bg-[#142028] px-3 text-sm font-bold text-teal-200 transition hover:border-teal-400 hover:bg-[#1a2b36] hover:text-white active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-teal-400/25"
                    >
                        <svg class="h-4 w-4 fill-none stroke-current stroke-[2.2]" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 7.5-2M12 14v2"></path>
                        </svg>
                        <span>Decrypt</span>
                    </button>

                    <button
                        type="button"
                        id="btnClear"
                        class="flex h-11 items-center justify-center gap-2 rounded-xl border border-slate-700/70 bg-[#121921] px-3 text-sm font-semibold text-slate-300 transition hover:border-red-400/50 hover:bg-red-950/20 hover:text-red-300 active:scale-[0.98] focus:outline-none"
                    >
                        <svg class="h-4 w-4 fill-none stroke-current stroke-2 text-slate-400 group-hover:text-red-300" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"></path>
                        </svg>
                        <span>Clear</span>
                    </button>
                </div>
            </section>


            <section class="flex flex-col h-full min-h-0 p-4 sm:p-6 bg-[#141d25] justify-between gap-3 sm:gap-4 overflow-hidden">
                <div class="flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-md border border-teal-500/30 bg-teal-500/10 text-teal-300">02</span>
                        <h2 class="text-sm sm:text-base font-semibold tracking-wide text-white">Output Result</h2>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <button
                            type="button"
                            id="btnCopy"
                            class="<?= $isReady ? 'flex' : 'hidden' ?> items-center gap-1.5 px-3 py-1 rounded-lg border border-teal-500/40 bg-teal-950/40 text-xs font-mono font-medium text-teal-300 hover:border-teal-400 hover:bg-teal-900/50 transition shadow-sm"
                            title="Copy result to clipboard"
                        >
                            <svg class="h-3.5 w-3.5 fill-none stroke-current stroke-2" viewBox="0 0 24 24">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                            <span>Copy</span>
                        </button>

                        <span id="statusBadge" class="flex items-center gap-1.5 font-mono text-xs font-semibold uppercase tracking-wider <?= $isReady ? 'text-teal-300' : 'text-slate-400' ?> bg-slate-900/60 px-2.5 py-1 rounded-md border border-slate-800">
                            <span id="statusDot" class="h-2 w-2 rounded-full <?= $isReady ? 'bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.9)]' : 'bg-slate-500' ?>"></span>
                            <span id="statusText"><?= $isReady ? 'Ready' : 'Waiting' ?></span>
                        </span>
                    </div>
                </div>

                <div id="resultCard" class="relative flex-1 min-h-[120px] flex flex-col justify-between rounded-xl border <?= $isReady ? 'border-solid border-teal-500/40 bg-[#0e171e]' : 'border-dashed border-slate-700/80 bg-[#0e151b]' ?> p-4 sm:p-5 overflow-hidden shadow-inner">
                    <div id="resultContent" class="<?= $isReady ? 'flex' : 'hidden' ?> flex-col justify-between h-full w-full overflow-hidden">
                        <div class="flex-1 overflow-y-auto pr-1">
                            <p id="outputText" class="whitespace-pre-wrap break-words font-mono text-[15px] sm:text-base leading-relaxed text-teal-100 font-medium select-text"><?= escape($output) ?></p>
                        </div>
                        <div class="pt-3 shrink-0 border-t border-slate-800 flex items-center justify-between">
                            <span id="actionMeta" class="font-mono text-xs font-semibold text-teal-300/90 tracking-wide">
                                <?= $action === 'encrypt' ? 'Encrypted' : ($action === 'decrypt' ? 'Decrypted' : '') ?> with key <?= escape(strtoupper($keyword)) ?>
                            </span>
                        </div>
                    </div>

                    <div id="placeholderContent" class="<?= $isReady ? 'hidden' : 'flex' ?> flex-col items-center justify-center h-full text-center my-auto">
                        <div class="mb-3 grid h-10 w-10 place-items-center rounded-full border border-slate-700/80 bg-slate-900/50 text-slate-400">
                            <svg class="h-5 w-5 fill-none stroke-current stroke-[1.8]" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"></path>
                            </svg>
                        </div>
                        <strong class="text-sm font-semibold text-slate-200">Your result will appear here</strong>
                        <p class="mt-1 text-xs text-slate-400 max-w-xs">Enter text, configure your key, and select Encrypt or Decrypt.</p>
                    </div>
                </div>

                <div class="shrink-0 grid grid-cols-3 border-t border-slate-800 pt-3 text-center sm:text-left">
                    <div class="flex flex-col">
                        <span class="font-mono text-xs font-semibold uppercase tracking-wider text-slate-400">Characters</span>
                        <strong id="statChars" class="font-mono text-base sm:text-lg font-bold text-teal-300"><?= str_pad((string) $characterCount, 2, '0', STR_PAD_LEFT) ?></strong>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-mono text-xs font-semibold uppercase tracking-wider text-slate-400">Words</span>
                        <strong id="statWords" class="font-mono text-base sm:text-lg font-bold text-teal-300"><?= str_pad((string) $wordCount, 2, '0', STR_PAD_LEFT) ?></strong>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-mono text-xs font-semibold uppercase tracking-wider text-slate-400">Key Length</span>
                        <strong id="statKeyLen" class="font-mono text-base sm:text-lg font-bold text-teal-300"><?= str_pad((string) strlen($effectiveKeyword), 2, '0', STR_PAD_LEFT) ?></strong>
                    </div>
                </div>
            </section>
        </form>

        <footer class="relative z-10 flex shrink-0 items-center justify-between pt-2 border-t border-slate-800 font-mono text-xs text-slate-500">
            <p class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                <span class="text-teal-400/90 font-semibold">Supported characters:</span>
                <span>A–Z and a–z (encrypted)</span>
                <span class="text-slate-600">•</span>
                <span>digits, spaces &amp; punctuation pass through unchanged</span>
            </p>

            <a
                href="test.php"
                class="shrink-0 font-semibold transition hover:text-teal-300"
            >Self-test with fictional samples →</a>
        </footer>
    </div>

    <script src="assets/cipher.js"></script>
</body>
</html>
