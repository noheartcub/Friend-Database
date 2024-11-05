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
$setupPath = __DIR__ . "/setup.php";

consoleLog("Starting update process...");

// Step 1: Download the Beta branch ZIP file
consoleLog("Downloading update from GitHub's Beta branch...");
if (file_put_contents($tempZipPath, fopen($repoUrl, 'r'))) {
    consoleLog("Download completed: $tempZipPath");
} else {
    consoleLog("Error: Failed to download update.");
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

// Step 3: Copy files, skipping `includes/config.php` and `setup.php`
consoleLog("Starting file copy to update application...");
$directory = new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $file) {
    $destPath = __DIR__ . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

    // Skip specific files
    if (realpath($destPath) === realpath($configPath) || realpath($destPath) === realpath($setupPath)) {
        consoleLog("Skipping protected file: {$iterator->getSubPathName()}");
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

// Step 4: Cleanup the downloaded ZIP and extracted files
consoleLog("Starting cleanup of temporary files...");

// Updated function to delete directory contents, including hidden files
function deleteDirectory($dir, $protectedDir) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileInfo) {
        $filePath = $fileInfo->getRealPath();

        // Skip deletion if the file is within the `uploads` or `setup.php` path
        if (strpos($filePath, realpath($protectedDir)) === 0 || basename($filePath) === 'setup.php') {
            consoleLog("Skipping protected file or directory: $filePath");
            continue;
        }

        // Delete files and directories, including hidden ones
        $action = $fileInfo->isDir() ? 'rmdir' : 'unlink';
        
        if ($action($filePath)) {
            consoleLog("Deleted " . ($fileInfo->isDir() ? "directory" : "file") . ": $filePath");
        } else {
            consoleLog("Failed to delete " . ($fileInfo->isDir() ? "directory" : "file") . ": $filePath");
        }
    }

    // Try to delete the main directory if it's not the protected one
    if (realpath($dir) !== realpath($protectedDir) && @rmdir($dir)) {
        consoleLog("Deleted directory: $dir");
    } elseif (realpath($dir) === realpath($protectedDir)) {
        consoleLog("Skipping deletion of protected uploads directory.");
    }
}

unlink($tempZipPath);
deleteDirectory($extractPath, $uploadsPath);
consoleLog("Cleanup completed.");
consoleLog("Update completed successfully.");
?>
