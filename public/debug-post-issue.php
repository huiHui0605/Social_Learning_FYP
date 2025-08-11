<?php
// Comprehensive debugging script for POST data issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>POST Data Issue Debugging</h1>";

// Test 1: Check current PHP settings
echo "<h2>1. Current PHP Settings</h2>";
$settings = [
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time'),
    'memory_limit' => ini_get('memory_limit'),
    'max_file_uploads' => ini_get('max_file_uploads'),
    'max_input_vars' => ini_get('max_input_vars'),
    'max_input_nesting_level' => ini_get('max_input_nesting_level')
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Setting</th><th>Current Value</th><th>Status</th></tr>";

foreach ($settings as $setting => $value) {
    $status = '❌';
    $color = 'red';
    
    switch ($setting) {
        case 'post_max_size':
            if ($value >= '100M' || $value >= '40M') {
                $status = '✅';
                $color = 'green';
            }
            break;
        case 'upload_max_filesize':
            if ($value >= '100M' || $value >= '40M') {
                $status = '✅';
                $color = 'green';
            }
            break;
        case 'max_execution_time':
            if ($value >= 300) {
                $status = '✅';
                $color = 'green';
            }
            break;
        case 'memory_limit':
            if ($value >= '256M') {
                $status = '✅';
                $color = 'green';
            }
            break;
    }
    
    echo "<tr><td>$setting</td><td>$value</td><td style='color: $color;'>$status</td></tr>";
}
echo "</table>";

// Test 2: Check if .htaccess is working
echo "<h2>2. .htaccess Test</h2>";
echo "<p>If you see values like 8M or 2M above, .htaccess is not working.</p>";
echo "<p>If you see 100M or 40M above, .htaccess is working.</p>";

// Test 3: Check server configuration
echo "<h2>3. Server Configuration</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Path:</strong> " . __FILE__ . "</p>";

// Test 4: Check for loaded modules
echo "<h2>4. Loaded Apache Modules</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    $required = ['mod_php', 'mod_rewrite', 'mod_headers'];
    
    foreach ($required as $module) {
        $loaded = in_array($module, $modules) ? '✅' : '❌';
        echo "<p>$module: $loaded</p>";
    }
} else {
    echo "<p>Cannot check Apache modules (function not available)</p>";
}

// Test 5: Test POST data handling
echo "<h2>5. POST Data Test</h2>";
if ($_POST) {
    echo "<p><strong>POST Data Received:</strong> ✅</p>";
    echo "<p><strong>POST Data Size:</strong> " . strlen(serialize($_POST)) . " bytes</p>";
    echo "<p><strong>POST Variables:</strong> " . count($_POST) . "</p>";
    
    // Test large POST data
    if (isset($_POST['large_data'])) {
        echo "<p><strong>Large Data Test:</strong> ✅ Successfully received " . strlen($_POST['large_data']) . " bytes</p>";
    }
} else {
    echo "<p><strong>POST Data Received:</strong> ❌ No POST data</p>";
    
    // Create test forms
    echo "<h3>Test Forms:</h3>";
    
    // Small POST test
    echo "<form method='POST' style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
    echo "<h4>Small POST Test</h4>";
    echo "<input type='text' name='test_field' value='test value'>";
    echo "<button type='submit'>Submit Small Test</button>";
    echo "</form>";
    
    // Large POST test
    echo "<form method='POST' style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
    echo "<h4>Large POST Test (1MB)</h4>";
    echo "<input type='hidden' name='large_data' value='" . str_repeat('A', 1000000) . "'>";
    echo "<button type='submit'>Submit Large Test</button>";
    echo "</form>";
    
    // File upload test
    echo "<form method='POST' enctype='multipart/form-data' style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
    echo "<h4>File Upload Test</h4>";
    echo "<input type='file' name='test_file'>";
    echo "<button type='submit'>Submit File Test</button>";
    echo "</form>";
}

// Test 6: Check error logs
echo "<h2>6. Error Log Check</h2>";
$logFiles = [
    '/Applications/XAMPP/xamppfiles/logs/error_log',
    '/Applications/XAMPP/xamppfiles/logs/php_error_log',
    __DIR__ . '/../storage/logs/laravel.log'
];

foreach ($logFiles as $logFile) {
    if (file_exists($logFile)) {
        echo "<p><strong>$logFile:</strong> ✅ Exists</p>";
        $size = filesize($logFile);
        echo "<p>Size: " . number_format($size) . " bytes</p>";
        
        if ($size > 0) {
            $lines = file($logFile);
            $recentLines = array_slice($lines, -5);
            echo "<p><strong>Recent 5 lines:</strong></p>";
            echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 200px; overflow-y: auto;'>";
            foreach ($recentLines as $line) {
                echo htmlspecialchars($line);
            }
            echo "</pre>";
        }
    } else {
        echo "<p><strong>$logFile:</strong> ❌ Not found</p>";
    }
}

echo "<h2>7. Recommendations</h2>";
echo "<ul>";
echo "<li>If .htaccess is not working, restart Apache service</li>";
echo "<li>Check Apache error logs for any configuration errors</li>";
echo "<li>Verify that mod_php is loaded in Apache</li>";
echo "<li>Consider editing the main php.ini file directly</li>";
echo "<li>Check if there are multiple php.ini files</li>";
echo "</ul>";
?>
