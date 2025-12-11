<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PDF Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        .error-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            color: #e74c3c;
            margin-top: 0;
        }
        .error-message {
            background: #fee;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 20px 0;
        }
        .trace {
            background: #f8f8f8;
            padding: 15px;
            overflow-x: auto;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>⚠️ Error Generating PDF</h1>
        <div class="error-message">
            <strong>Error:</strong> <?= htmlspecialchars($message) ?>
        </div>
        
        <?php if (isset($trace) && $trace): ?>
        <details>
            <summary style="cursor: pointer; padding: 10px; background: #f0f0f0;">Show Stack Trace</summary>
            <div class="trace">
                <pre><?= htmlspecialchars($trace) ?></pre>
            </div>
        </details>
        <?php endif; ?>
        
        <p style="margin-top: 30px;">
            <a href="javascript:window.close()" style="color: #3498db;">Close Window</a>
        </p>
    </div>
</body>
</html>