<?php
$srcPath = 'assets/logo.png';
$destPath = 'assets/favicon.png';

if (!file_exists($srcPath)) {
    die("Logo not found.");
}

$srcInfo = getimagesize($srcPath);
$srcType = $srcInfo[2];

if ($srcType == IMAGETYPE_PNG) {
    $srcImg = imagecreatefrompng($srcPath);
} elseif ($srcType == IMAGETYPE_JPEG) {
    $srcImg = imagecreatefromjpeg($srcPath);
} else {
    die("Unsupported format");
}

$width = imagesx($srcImg);
$height = imagesy($srcImg);
$size = min($width, $height);

// Crop to square first
$squareImg = imagecreatetruecolor($size, $size);
imagealphablending($squareImg, false);
imagesavealpha($squareImg, true);
$transparent = imagecolorallocatealpha($squareImg, 255, 255, 255, 127);
imagefill($squareImg, 0, 0, $transparent);
imagecopyresampled($squareImg, $srcImg, 0, 0, ($width - $size) / 2, ($height - $size) / 2, $size, $size, $size, $size);

// Create circle mask
$circleImg = imagecreatetruecolor($size, $size);
imagealphablending($circleImg, false);
imagesavealpha($circleImg, true);
$transparent = imagecolorallocatealpha($circleImg, 255, 255, 255, 127);
imagefill($circleImg, 0, 0, $transparent);
// Draw black circle in the middle
$black = imagecolorallocate($circleImg, 0, 0, 0);
imagefilledellipse($circleImg, $size/2, $size/2, $size, $size, $black);

// Apply mask
for ($x = 0; $x < $size; $x++) {
    for ($y = 0; $y < $size; $y++) {
        $alpha = (imagecolorat($circleImg, $x, $y) >> 24) & 0xFF;
        if ($alpha > 0) { // If it's transparent in mask
            imagesetpixel($squareImg, $x, $y, $transparent);
        }
    }
}

// Resize to standard favicon size (64x64)
$favicon = imagecreatetruecolor(64, 64);
imagealphablending($favicon, false);
imagesavealpha($favicon, true);
imagefill($favicon, 0, 0, $transparent);
imagecopyresampled($favicon, $squareImg, 0, 0, 0, 0, 64, 64, $size, $size);

imagepng($favicon, $destPath);
echo "Favicon created.";
