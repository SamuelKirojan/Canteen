<?php
// Define base URL for assets
if (!isset($baseUrl)) {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = rtrim($scriptDir, '/');
    
    // If we're in /public/, go up one level for base, but assets are in public
    if (basename($baseUrl) === 'public') {
        $baseUrl = dirname($baseUrl) . '/public/';
    } else {
        // If not in public subdirectory, assets should be relative to current
        $baseUrl = $baseUrl . '/';
    }
}
?>
<section class="py-5">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-md-6">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
