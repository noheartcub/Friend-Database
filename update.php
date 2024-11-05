<?php
// Log each console message to a file
function consoleLog($message) {
    file_put_contents('console_log.txt', $message . "\n", FILE_APPEND);
}

// Clear previous log contents
file_put_contents('console_log.txt', "Initializing update process...\n");

// Include database configuration
include_once 'includes/config.php';

$repoUrl = "https://github.com/noheartcub/Friend-Manager/archive/refs/heads/Beta.zip";
$tempZipPath = __DIR__ . "/update.zip";
$extractPath = __DIR__ . "/update";
$configPath = __DIR__ . "/includes/config.php";
$uploadsPath = __DIR__ . "/uploads";

consoleLog("Starting update process...");

// Step 1: Download the Beta branch ZIP file with error check
consoleLog("Downloading update from GitHub's Beta branch...");
$context = stream_context_create(['http' => ['ignore_errors' => true]]);
$response = @file_get_contents($repoUrl, false, $context);

if ($response === FALSE) {
    consoleLog("Error: Failed to access the update URL. Please check your internet connection or the GitHub repository URL.");
    exit;
}

// Check for HTTP response code, especially 404 errors
$httpCode = null;
if (isset($http_response_header)) {
    foreach ($http_response_header as $header) {
        if (stripos($header, 'HTTP/') === 0) {
            $httpCode = (int) substr($header, 9, 3);
            break;
        }
    }
}

if ($httpCode === 404) {
    consoleLog("Error: GitHub repository or file not found (404). Please ensure the repository URL is correct.");
    exit;
}

if (file_put_contents($tempZipPath, $response)) {
    consoleLog("Download completed: $tempZipPath");
} else {
    consoleLog("Error: Failed to save downloaded update.");
    exit;
}

// Step 2: Extract the ZIP file
consoleLog("Extracting update...");
$zip = new ZipArchive;
if ($zip->open($tempZipPath) === TRUE) {
    $zip->extractTo($extractPath);
    $zip->close();
    consoleLog("Extraction completed: $extractPath");
} else {
    consoleLog("Error: Failed to open the update ZIP file.");
    exit;
}

// Detect extracted folder
$extractedDir = glob($extractPath . "/*", GLOB_ONLYDIR);
if (empty($extractedDir)) {
    consoleLog("Error: Could not find the extracted directory.");
    exit;
}
$sourcePath = $extractedDir[0];
consoleLog("Detected extracted directory: $sourcePath");

// Step 3: Copy files, skipping `includes/config.php`
consoleLog("Starting file copy to update application...");
$directory = new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $file) {
    $destPath = __DIR__ . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

    if (realpath($destPath) === realpath($configPath)) {
        consoleLog("Skipping config.php to preserve settings.");
        continue;
    }

    if ($file->isDir()) {
        if (!is_dir($destPath)) {
            mkdir($destPath, 0755, true);
            consoleLog("Created directory: $destPath");
        }
    } else {
        if (copy($file, $destPath)) {
            consoleLog("Copied file: {$file->getRealPath()} to $destPath");
        } else {
            consoleLog("Failed to copy file: {$file->getRealPath()}");
        }
    }
}

consoleLog("File copy completed.");

// Cleanup Section with Explicit Checks

consoleLog("Starting cleanup of temporary files...");

// Function to delete a file with logging and confirmation
function deleteFileWithConfirmation($filePath) {
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            consoleLog("Deleted file: $filePath");
        } else {
            consoleLog("Failed to delete file: $filePath");
        }
    } else {
        consoleLog("File does not exist: $filePath");
    }
}

// Function to delete directories except protected ones (like uploads)
function deleteDirectory($dir, $protectedDir = null) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileInfo) {
        $filePath = $fileInfo->getRealPath();

        // Skip deletion if the file is within the protected directory
        if ($protectedDir && strpos($filePath, realpath($protectedDir)) === 0) {
            consoleLog("Skipping protected file or directory: $filePath");
            continue;
        }

        // Perform deletion and confirm
        if ($fileInfo->isDir()) {
            if (rmdir($filePath)) {
                consoleLog("Deleted directory: $filePath");
            } else {
                consoleLog("Failed to delete directory: $filePath");
            }
        } else {
            deleteFileWithConfirmation($filePath);
        }
    }

    // Delete the main directory if it's not protected
    if (!$protectedDir || realpath($dir) !== realpath($protectedDir)) {
        if (rmdir($dir)) {
            consoleLog("Deleted main directory: $dir");
        } else {
            consoleLog("Failed to delete main directory: $dir");
        }
    }
}

// Call delete function for temp files and directories
deleteFileWithConfirmation($tempZipPath);
deleteDirectory($extractPath, $uploadsPath);
deleteFileWithConfirmation(__DIR__ . '/console_log.txt');
deleteFileWithConfirmation(__DIR__ . '/database.sql');

consoleLog("Cleanup completed.");
consoleLog("Update completed successfully.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Console</title>
    <style>
        body { font-family: monospace; background-color: #1e1e1e; color: #0f0; }
        #console { max-width: 800px; margin: 20px auto; padding: 10px; border: 1px solid #333; background-color: #000; height: 400px; overflow-y: scroll; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div id="console">Initializing update...</div>

    <script>
        const consoleDiv = document.getElementById('console');

        async function fetchConsoleUpdates() {
            try {
                const response = await fetch('console_log.txt');
                const data = await response.text();
                consoleDiv.innerHTML = data;
                consoleDiv.scrollTop = consoleDiv.scrollHeight;
            } catch (error) {
                consoleDiv.innerHTML += "<br>Error fetching updates: " + error;
            }
        }

        // Fetch every second until update completion
        const interval = setInterval(fetchConsoleUpdates, 1000);

        fetchConsoleUpdates().then(() => {
            clearInterval(interval);
            consoleDiv.style.overflowY = 'auto';
        });
    </script>
</body>
</html>
