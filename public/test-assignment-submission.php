<?php
// Test script for assignment submission with large files
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Assignment Submission Test</h1>";

// Check current PHP settings
echo "<h2>Current PHP Settings:</h2>";
echo "<p><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>max_execution_time:</strong> " . ini_get('max_execution_time') . "</p>";
echo "<p><strong>memory_limit:</strong> " . ini_get('memory_limit') . "</p>";

// Test POST data handling
if ($_POST) {
    echo "<h2>POST Test Results:</h2>";
    echo "<p><strong>POST Data Size:</strong> " . strlen(serialize($_POST)) . " bytes</p>";
    echo "<p><strong>POST Variables Count:</strong> " . count($_POST) . "</p>";
    
    if (isset($_FILES['test_file']) && $_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
        echo "<p><strong>File Upload Success:</strong> ✅</p>";
        echo "<p><strong>File Name:</strong> " . $_FILES['test_file']['name'] . "</p>";
        echo "<p><strong>File Size:</strong> " . number_format($_FILES['test_file']['size']) . " bytes</p>";
        echo "<p><strong>File Type:</strong> " . $_FILES['test_file']['type'] . "</p>";
    }
    
    if (isset($_POST['large_text'])) {
        echo "<p><strong>Large Text Received:</strong> ✅ " . strlen($_POST['large_text']) . " characters</p>";
    }
} else {
    echo "<h2>Test Assignment Submission</h2>";
    
    // Create a test form similar to the assignment submission
    echo "<form method='POST' enctype='multipart/form-data' style='margin: 20px; padding: 20px; border: 2px solid #333; border-radius: 10px;'>";
    echo "<h3>Simulated Assignment Submission Form</h3>";
    
    echo "<div style='margin: 10px 0;'>";
    echo "<label><strong>Your Answer:</strong></label><br>";
    echo "<textarea name='submission_content' rows='4' cols='50' placeholder='Write your answer here...' style='width: 100%; padding: 10px;'></textarea>";
    echo "</div>";
    
    echo "<div style='margin: 10px 0;'>";
    echo "<label><strong>Attachment (Optional):</strong></label><br>";
    echo "<input type='file' name='test_file' accept='.pdf,.doc,.docx,.txt,.jpg,.jpeg,.png'>";
    echo "<p style='font-size: 12px; color: #666;'>Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG (Max 100MB)</p>";
    echo "</div>";
    
    echo "<div style='margin: 10px 0;'>";
    echo "<label><strong>Large Text Test:</strong></label><br>";
    echo "<textarea name='large_text' rows='3' cols='50' placeholder='This will test large POST data...' style='width: 100%; padding: 10px;'>" . str_repeat('This is a test of large POST data handling. ', 1000) . "</textarea>";
    echo "</div>";
    
    echo "<button type='submit' style='background: #4F46E5; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Submit Test Assignment</button>";
    echo "</form>";
    
    echo "<h3>Test Instructions:</h3>";
    echo "<ol>";
    echo "<li>Fill in the form above with some content</li>";
    echo "<li>Try uploading a file (any size up to 100MB)</li>";
    echo "<li>Click Submit to test if the POST data is handled correctly</li>";
    echo "<li>If successful, you should see the file details and POST data size</li>";
    echo "</ol>";
    
    echo "<h3>Expected Results:</h3>";
    echo "<ul>";
    echo "<li>✅ POST data should be accepted (up to 100MB)</li>";
    echo "<li>✅ File uploads should work (up to 100MB)</li>";
    echo "<li>✅ No 'POST data too large' errors</li>";
    echo "<li>✅ All form fields should be processed</li>";
    echo "</ul>";
}

echo "<h2>Status Summary:</h2>";
$postMaxSize = ini_get('post_max_size');
$uploadMaxSize = ini_get('upload_max_filesize');

if (strpos($postMaxSize, 'M') !== false) {
    $postMaxMB = (int)$postMaxSize;
    if ($postMaxMB >= 100) {
        echo "<p style='color: green;'>✅ POST max size: $postMaxSize (Sufficient for large uploads)</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ POST max size: $postMaxSize (May be too small for very large files)</p>";
    }
} else {
    echo "<p style='color: red;'>❌ POST max size: $postMaxSize (Check configuration)</p>";
}

if (strpos($uploadMaxSize, 'M') !== false) {
    $uploadMaxMB = (int)$uploadMaxSize;
    if ($uploadMaxMB >= 100) {
        echo "<p style='color: green;'>✅ Upload max size: $uploadMaxSize (Sufficient for large files)</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Upload max size: $uploadMaxSize (May be too small for very large files)</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Upload max size: $uploadMaxSize (Check configuration)</p>";
}

echo "<p><strong>Test completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
