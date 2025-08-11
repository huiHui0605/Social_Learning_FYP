<?php
// Simple test script to check PHP configuration
echo "<h1>PHP Configuration Test</h1>";

// Check key settings
echo "<h2>Current PHP Settings:</h2>";
echo "<p><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>max_execution_time:</strong> " . ini_get('max_execution_time') . "</p>";
echo "<p><strong>memory_limit:</strong> " . ini_get('memory_limit') . "</p>";

// Test simple POST
if ($_POST) {
    echo "<h2>POST Test Results:</h2>";
    echo "<p><strong>POST Data Size:</strong> " . strlen(serialize($_POST)) . " bytes</p>";
    echo "<p><strong>POST Variables Count:</strong> " . count($_POST) . "</p>";
    echo "<p><strong>POST Content:</strong> " . htmlspecialchars(print_r($_POST, true)) . "</p>";
} else {
    echo "<h2>Test POST Data:</h2>";
    echo "<form method='POST'>";
    echo "<input type='text' name='test_field' value='test value'>";
    echo "<button type='submit'>Submit Test</button>";
    echo "</form>";
}

// Check if .htaccess is working
echo "<h2>.htaccess Test:</h2>";
echo "<p>If .htaccess is working, you should see custom values above.</p>";
echo "<p>If you see default values (like 8M), .htaccess is not working.</p>";

// Show server info
echo "<h2>Server Information:</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
?>
