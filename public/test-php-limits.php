<?php
// Test script to display current PHP configuration values
echo "<h1>PHP Configuration Test</h1>";
echo "<h2>Current PHP Settings:</h2>";

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
echo "<tr><th>Setting</th><th>Current Value</th><th>Recommended</th></tr>";

foreach ($settings as $setting => $value) {
    $recommended = '';
    switch ($setting) {
        case 'post_max_size':
            $recommended = '100M';
            break;
        case 'upload_max_filesize':
            $recommended = '100M';
            break;
        case 'max_execution_time':
            $recommended = '300';
            break;
        case 'max_input_time':
            $recommended = '300';
            break;
        case 'memory_limit':
            $recommended = '256M';
            break;
        case 'max_file_uploads':
            $recommended = '20';
            break;
        case 'max_input_vars':
            $recommended = '3000';
            break;
        case 'max_input_nesting_level':
            $recommended = '64';
            break;
    }
    
    $status = ($value == $recommended || $value >= $recommended) ? '✅' : '❌';
    echo "<tr><td>$setting</td><td>$value $status</td><td>$recommended</td></tr>";
}

echo "</table>";

echo "<h2>Server Information:</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<h2>Test Large POST Data:</h2>";
echo "<form method='POST' action=''>";
echo "<input type='hidden' name='test_data' value='" . str_repeat('A', 1000000) . "'>";
echo "<button type='submit'>Test 1MB POST Data</button>";
echo "</form>";

if ($_POST) {
    echo "<p><strong>POST Data Size:</strong> " . strlen(serialize($_POST)) . " bytes</p>";
    echo "<p><strong>POST Max Size:</strong> " . ini_get('post_max_size') . "</p>";
}
?>
