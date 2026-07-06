<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
$has_error = false;
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $output = shell_exec('/Applications/XAMPP/xamppfiles/bin/php -l ' . escapeshellarg($file->getRealPath()) . ' 2>&1');
        if (strpos($output, 'No syntax errors detected') === false) {
            echo $output . "\n";
            $has_error = true;
        }
    }
}
if (!$has_error) {
    echo "All files passed syntax check.\n";
}
