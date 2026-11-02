(function() {
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

    async function performCipherAction(action) {
        const message = messageEl.value;
        const keywordInput = keywordEl.value.trim();
        const keyword = keywordInput !== '' ? keywordInput : 'CIPHER';

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

            outputText.textContent = data.output;
            actionMeta.textContent = (data.action === 'encrypt' ? 'Encrypted' : 'Decrypted') + ' with key ' + data.keyword;

            statChars.textContent = String(data.characterCount).padStart(2, '0');
            statWords.textContent = String(data.wordCount).padStart(2, '0');
            statKeyLen.textContent = String(data.keyLength).padStart(2, '0');

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
            form.submit();
        }
    }

    document.getElementById('btnEncrypt').addEventListener('click', function(e) {
        e.preventDefault();
        performCipherAction('encrypt');
    });

    document.getElementById('btnDecrypt').addEventListener('click', function(e) {
        e.preventDefault();
        performCipherAction('decrypt');
    });

    btnClear.addEventListener('click', function() {
        messageEl.value = '';
        keywordEl.value = '';
        updateLiveCounts();
        updateKeyLen();
        hideError();

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

    btnCopy.addEventListener('click', async function() {
        const text = outputText.textContent;
        if (!text) return;
        try {
            await navigator.clipboard.writeText(text);
            showToast();
        } catch (err) {
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
