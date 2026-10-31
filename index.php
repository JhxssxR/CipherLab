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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Smooth custom scrollbars */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #10161c;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #2a3d4a;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3d5869;
        }
    </style>
</head>
<body class="h-screen max-h-screen overflow-hidden bg-[#0c1014] text-slate-100 font-sans antialiased selection:bg-teal-500/30 selection:text-teal-200">
    <div class="relative flex h-full flex-col justify-between px-3 sm:px-6 lg:px-10 py-3 lg:py-4 max-w-[1520px] mx-auto">
        <!-- Soft ambient decorative blur (gentle on eyes) -->
        <div
            class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-teal-500/[0.07] blur-[100px]"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute -left-24 -bottom-24 h-80 w-80 rounded-full bg-emerald-500/[0.05] blur-[100px]"
            aria-hidden="true"
        ></div>

        <!-- 1. Header (Clean & balanced) -->
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

        <!-- 2. Subheader Banner -->
        <div class="relative z-10 flex shrink-0 items-center justify-between py-2 sm:py-2.5">
            <div>
                <h1 class="text-lg sm:text-xl font-bold tracking-tight text-white flex items-center gap-2">
                    Turn plain text into <span class="font-serif italic font-normal text-teal-300">coded messages.</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 hidden sm:block mt-0.5">
                    Polyalphabetic substitution cipher running real-time on your server.
                </p>
            </div>

            <!-- Copy Toast Notification -->
            <div id="copyToast" class="transition-all duration-300 opacity-0 pointer-events-none flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-teal-500/40 bg-teal-950/80 text-xs font-mono font-medium text-teal-200 shadow-lg">
                <svg class="h-3.5 w-3.5 fill-none stroke-current stroke-2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Result copied!</span>
            </div>
        </div>

        <!-- 3. Error Alert Container -->
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

        <!-- 4. Main Interactive Workspace (Split Panel: Eye-Friendly Slate-Teal with High Legibility) -->
        <form
            id="cipherForm"
            method="post"
            action="index.php"
            class="relative z-10 flex-1 min-h-0 grid w-full overflow-hidden rounded-2xl border border-slate-700/70 bg-[#12181f] shadow-2xl lg:grid-cols-[1.12fr_0.88fr]"
        >
            <!-- Left Pane: Input Controls -->
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

                <!-- Textarea: Clear readable typography -->
                <div class="relative flex-1 min-h-[120px] flex flex-col">
                    <textarea
                        id="message"
                        name="message"
                        placeholder="Type or paste your message to encrypt or decrypt..."
                        spellcheck="false"
                        class="w-full flex-1 resize-none rounded-xl border border-slate-700/80 bg-[#0f151b] p-4 font-mono text-sm sm:text-[15px] leading-relaxed text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-teal-400/80 focus:bg-[#111921] focus:ring-2 focus:ring-teal-400/20"
                    ><?= escape($message) ?></textarea>
                </div>

                <!-- Keyword Control Row -->
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

                <!-- Action Buttons Row -->
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

            <!-- Right Pane: Result Inspector -->
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

                <!-- Result Box: High legibility & comfortable contrast -->
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

                <!-- Stats Bar: Clear and readable numbers -->
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

        <!-- 5. Footer (Compact) -->
        <footer class="relative z-10 flex shrink-0 items-center justify-between pt-2 border-t border-slate-800 font-mono text-xs text-slate-500">
           
        </footer>
    </div>

    <!-- Client-side Interactive Script -->
    <script>
        (function() {
            // Prevent browser scroll jumps
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }

            const form = document.getElementById('cipherForm');
            const messageEl = document.getElementById('message');
            const keywordEl = document.getElementById('keyword');
            const liveCharCount = document.getElementById('liveCharCount');
            const statChars = document.getElementById('statChars');
            const statWords = document.getElementById('statWords');
            const statKeyLen = document.getElementById('statKeyLen');
            const errorAlert = document.getElementById('errorAlert');
            const errorMessage = document.getElementById('errorMessage');
            const btnClear = document.getElementById('btnClear');
            const btnCopy = document.getElementById('btnCopy');
            const copyToast = document.getElementById('copyToast');

            const resultCard = document.getElementById('resultCard');
            const resultContent = document.getElementById('resultContent');
            const placeholderContent = document.getElementById('placeholderContent');
            const outputText = document.getElementById('outputText');
            const actionMeta = document.getElementById('actionMeta');
            const statusBadge = document.getElementById('statusBadge');
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');

            // Live updates while typing in the textarea
            function updateLiveCounts() {
                const text = messageEl.value;
                const chars = text.length;
                const trimmed = text.trim();
                const words = trimmed === '' ? 0 : trimmed.split(/\s+/).length;

                liveCharCount.textContent = chars;
                statChars.textContent = String(chars).padStart(2, '0');
                statWords.textContent = String(words).padStart(2, '0');
            }

            function updateKeyLen() {
                const key = keywordEl.value.trim() || 'CIPHER';
                statKeyLen.textContent = String(key.length).padStart(2, '0');
            }

            messageEl.addEventListener('input', updateLiveCounts);
            keywordEl.addEventListener('input', updateKeyLen);

            // Handle seamless form actions without page reload or scrolling
            async function performCipherAction(action) {
                const message = messageEl.value;
                const keywordInput = keywordEl.value.trim();
                const keyword = keywordInput !== '' ? keywordInput : 'CIPHER';

                // Client-side quick validation
                if (message === '') {
                    showError('Enter a message before encrypting or decrypting.');
                    return;
                }
                if (!/^[A-Za-z]+$/.test(keyword)) {
                    showError('The keyword can only contain letters from A to Z.');
                    return;
                }

                hideError();

                const formData = new FormData();
                formData.append('message', message);
                formData.append('keyword', keywordInput);
                formData.append('action', action);
                formData.append('ajax', '1');

                try {
                    const response = await fetch('index.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();

                    if (!data.success && data.error) {
                        showError(data.error);
                        return;
                    }

                    // Render result
                    outputText.textContent = data.output;
                    actionMeta.textContent = (data.action === 'encrypt' ? 'Encrypted' : 'Decrypted') + ' with key ' + data.keyword;
                    
                    statChars.textContent = String(data.characterCount).padStart(2, '0');
                    statWords.textContent = String(data.wordCount).padStart(2, '0');
                    statKeyLen.textContent = String(data.keyLength).padStart(2, '0');

                    // Switch states
                    placeholderContent.classList.add('hidden');
                    placeholderContent.classList.remove('flex');

                    resultContent.classList.remove('hidden');
                    resultContent.classList.add('flex');

                    resultCard.classList.remove('border-dashed', 'bg-[#0e151b]', 'border-slate-700/80');
                    resultCard.classList.add('border-solid', 'border-teal-500/40', 'bg-[#0e171e]');

                    btnCopy.classList.remove('hidden');
                    btnCopy.classList.add('flex');

                    statusBadge.classList.remove('text-slate-400');
                    statusBadge.classList.add('text-teal-300');

                    statusDot.classList.remove('bg-slate-500');
                    statusDot.classList.add('bg-teal-400', 'shadow-[0_0_8px_rgba(45,212,191,0.9)]');

                    statusText.textContent = 'Ready';
                } catch (err) {
                    console.error('Cipher request failed:', err);
                    // Native fallback if fetch fails
                    form.submit();
                }
            }

            // Bind buttons
            document.getElementById('btnEncrypt').addEventListener('click', function(e) {
                e.preventDefault();
                performCipherAction('encrypt');
            });

            document.getElementById('btnDecrypt').addEventListener('click', function(e) {
                e.preventDefault();
                performCipherAction('decrypt');
            });

            // Clear button
            btnClear.addEventListener('click', function() {
                messageEl.value = '';
                keywordEl.value = '';
                updateLiveCounts();
                updateKeyLen();
                hideError();

                // Reset result box
                outputText.textContent = '';
                resultContent.classList.add('hidden');
                resultContent.classList.remove('flex');

                placeholderContent.classList.remove('hidden');
                placeholderContent.classList.add('flex');

                resultCard.classList.add('border-dashed', 'bg-[#0e151b]', 'border-slate-700/80');
                resultCard.classList.remove('border-solid', 'border-teal-500/40', 'bg-[#0e171e]');

                btnCopy.classList.add('hidden');
                btnCopy.classList.remove('flex');

                statusBadge.classList.add('text-slate-400');
                statusBadge.classList.remove('text-teal-300');

                statusDot.classList.add('bg-slate-500');
                statusDot.classList.remove('bg-teal-400', 'shadow-[0_0_8px_rgba(45,212,191,0.9)]');

                statusText.textContent = 'Waiting';
            });

            // Copy button
            btnCopy.addEventListener('click', async function() {
                const text = outputText.textContent;
                if (!text) return;
                try {
                    await navigator.clipboard.writeText(text);
                    showToast();
                } catch (err) {
                    // Fallback
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    showToast();
                }
            });

            function showToast() {
                copyToast.classList.remove('opacity-0', 'pointer-events-none');
                copyToast.classList.add('opacity-100');
                setTimeout(() => {
                    copyToast.classList.remove('opacity-100');
                    copyToast.classList.add('opacity-0', 'pointer-events-none');
                }, 2000);
            }

            function showError(msg) {
                errorMessage.textContent = msg;
                errorAlert.classList.remove('hidden');
                errorAlert.classList.add('flex');
            }

            function hideError() {
                errorAlert.classList.add('hidden');
                errorAlert.classList.remove('flex');
            }
        })();
    </script>
</body>
</html>
