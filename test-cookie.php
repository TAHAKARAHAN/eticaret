<?php
// Start session
session_start();

// Set a session variable
$_SESSION['test_session'] = 'Session is working!';

// Set a test cookie
setcookie('test_cookie', 'Cookie is working!', time() + 3600, '/');

// Get server information
$phpVersion = phpversion();
$sessionSavePath = session_save_path();
$sessionName = session_name();
$sessionId = session_id();
$sessionStatus = session_status();

// Convert session status to readable format
switch ($sessionStatus) {
    case PHP_SESSION_DISABLED:
        $sessionStatusText = 'Sessions are disabled';
        break;
    case PHP_SESSION_NONE:
        $sessionStatusText = 'Sessions are enabled but none exists';
        break;
    case PHP_SESSION_ACTIVE:
        $sessionStatusText = 'Sessions are enabled and one exists';
        break;
    default:
        $sessionStatusText = 'Unknown session status';
}

// Check write permissions on session directory
$sessionWritable = is_writable($sessionSavePath) ? 'Yes' : 'No';

// Check if cookies are enabled
$cookiesEnabled = isset($_COOKIE['test_cookie']) ? 'Yes' : 'No (wait for page refresh)';

// Check if headers have already been sent
$headersSent = headers_sent($file, $line);
$headersSentText = $headersSent ? "Yes, in $file on line $line" : 'No';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session & Cookie Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .status {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>Session & Cookie Test</h1>
    
    <?php if (isset($_SESSION['test_session'])): ?>
        <div class="status success">
            <strong>Session Test:</strong> <?php echo $_SESSION['test_session']; ?>
        </div>
    <?php else: ?>
        <div class="status error">
            <strong>Session Test:</strong> Failed to create session variable.
        </div>
    <?php endif; ?>
    
    <?php if (isset($_COOKIE['test_cookie'])): ?>
        <div class="status success">
            <strong>Cookie Test:</strong> <?php echo $_COOKIE['test_cookie']; ?>
        </div>
    <?php else: ?>
        <div class="status warning">
            <strong>Cookie Test:</strong> Refresh the page to see if cookies work. If this message persists, cookies are disabled.
        </div>
    <?php endif; ?>
    
    <h2>System Information</h2>
    <table>
        <tr>
            <th>Setting</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>PHP Version</td>
            <td><?php echo $phpVersion; ?></td>
        </tr>
        <tr>
            <td>Session Save Path</td>
            <td><?php echo $sessionSavePath; ?></td>
        </tr>
        <tr>
            <td>Session Writable</td>
            <td><?php echo $sessionWritable; ?></td>
        </tr>
        <tr>
            <td>Session Name</td>
            <td><?php echo $sessionName; ?></td>
        </tr>
        <tr>
            <td>Session ID</td>
            <td><?php echo $sessionId; ?></td>
        </tr>
        <tr>
            <td>Session Status</td>
            <td><?php echo $sessionStatusText; ?></td>
        </tr>
        <tr>
            <td>Cookies Enabled</td>
            <td><?php echo $cookiesEnabled; ?></td>
        </tr>
        <tr>
            <td>Headers Already Sent</td>
            <td><?php echo $headersSentText; ?></td>
        </tr>
    </table>
    
    <h2>Session Data</h2>
    <pre><?php print_r($_SESSION); ?></pre>
    
    <h2>Cookie Data</h2>
    <pre><?php print_r($_COOKIE); ?></pre>
</body>
</html>
