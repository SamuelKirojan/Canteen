<?php
// Debug URL paths
echo "<h2>URL Debug Info</h2>";
echo "<pre>";

echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n\n";

$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
echo "dirname(SCRIPT_NAME): $scriptDir\n";
echo "basename(scriptDir): " . basename($scriptDir) . "\n\n";

$baseUrl = rtrim($scriptDir, '/');
echo "baseUrl (before logic): $baseUrl\n\n";

if (basename($baseUrl) === 'public') {
    $baseUrl = $baseUrl . '/';
    echo "Matched 'public' - baseUrl: $baseUrl\n";
} else {
    $baseUrl = $baseUrl . '/public/';
    echo "Did NOT match 'public' - baseUrl: $baseUrl\n";
}

echo "\nFinal baseUrl: $baseUrl\n";
echo "CSS path would be: {$baseUrl}assets/vendor/bootstrap/css/bootstrap.min.css\n";
echo "Image path would be: {$baseUrl}assets/img/chef.jpg\n\n";

// Check if files exist
$docRoot = $_SERVER['DOCUMENT_ROOT'];
$cssPath = $docRoot . $baseUrl . 'assets/vendor/bootstrap/css/bootstrap.min.css';
$imgPath = $docRoot . $baseUrl . 'assets/img/chef.jpg';

echo "Checking file existence:\n";
echo "CSS: $cssPath\n";
echo "Exists: " . (file_exists($cssPath) ? 'YES' : 'NO') . "\n\n";

echo "Image: $imgPath\n";
echo "Exists: " . (file_exists($imgPath) ? 'YES' : 'NO') . "\n";

echo "</pre>";
?>
