# CipherLab — Vigenère Cipher Workspace

CipherLab is an educational cryptography web application implementing a hand-written,
fully reversible Vigenère cipher. It is built as the laboratory activity deliverable for
Information Assurance and Security 1.

## Running the Application

### Option A — XAMPP (recommended)

1. Copy this folder (`CipherLab`) into your XAMPP `htdocs` directory
   (e.g. `C:\xampp\htdocs\CipherLab`).
2. Start Apache in the XAMPP Control Panel.
3. Open the application:
   - Workspace: `http://localhost/CipherLab/index.php`
   - Self-test page: `http://localhost/CipherLab/test.php`

### Option B — PHP built-in server

1. Install PHP 8.1 or newer (no extensions required).
2. From the project folder, run: `php -S localhost:8000`
3. Open `http://localhost:8000/index.php`

## Application Components

- `index.php` — Main interface (message input, encrypt/decrypt/clear controls, formatted output)
- `test.php` — Automated self-test page covering all required test cases
- `Cipher.php` — Hand-written `encrypt()` and `decrypt()` functions (no built-in crypto)
- `app.php` — Input validation and request handling for both HTTP form and AJAX requests
- `assets/cipher.js` — Client-side UI behavior
- `assets/tailwind-config.js`, `assets/cipher.css` — Styling configuration

## Encryption Method

Vigenère polyalphabetic substitution cipher:

- Encryption shifts each letter of the message forward through the alphabet by the
  corresponding letter of the keyword (e.g. keyword C, letter H → J).
- Decryption shifts the same letters backward.
- Only A–Z and a–z characters are encrypted. Digits, spaces, and punctuation pass
  through unchanged.
- Keywords are letters A–Z only and repeat across the message; an empty keyword
  defaults to CIPHER.

Implementation uses only ordinary programming operations: loops, arithmetic
(`ord`/`chr`, module 26 arithmetic), and string handling. No built-in
encryption/decryption functions, cryptographic libraries, or external services are used.
