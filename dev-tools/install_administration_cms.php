<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration CMS Installation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; text-align: center; }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .header p { font-size: 16px; opacity: 0.9; }
        .content { padding: 40px; }
        .step { background: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
        .step h3 { color: #667eea; margin-bottom: 10px; font-size: 20px; }
        .step p { color: #666; line-height: 1.6; margin-bottom: 15px; }
        .btn { display: inline-block; padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; transition: all 0.3s; border: none; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #764ba2; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .success { background: #d4edda; border-left-color: #28a745; color: #155724; }
        .error { background: #f8d7da; border-left-color: #dc3545; color: #721c24; }
        .info { background: #d1ecf1; border-left-color: #17a2b8; color: #0c5460; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; color: #e83e8c; font-family: 'Courier New', monospace; }
        .file-list { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .file-list li { padding: 8px 0; border-bottom: 1px solid #dee2e6; }
        .file-list li:last-child { border-bottom: none; }
        .checklist { list-style: none; }
        .checklist li:before { content: "✓ "; color: #28a745; font-weight: bold; margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Administration CMS Installer</h1>
            <p>Valley View University - Complete Setup Guide</p>
        </div>
        
        <div class="content">
            <?php
            require_once 'includes/db_connect.php';
            
            $step = isset($_GET['step']) ? intval($_GET['step']) : 1;
            $action = isset($_GET['action']) ? $_GET['action'] : '';
            
            if ($action === 'install_schema') {
                try {
                    // Read and execute schema file
                    $schema = file_get_contents('administration_pages_schema.sql');
                    $pdo->exec($schema);
                    echo '<div class="step success">
                        <h3>✅ Step 1 Complete!</h3>
                        <p>Database schema and Vice-Chancellor data installed successfully.</p>
                        </div>';
                    $step = 2;
                } catch (Exception $e) {
                    echo '<div class="step error">
                        <h3>❌ Installation Error</h3>
                        <p>Error: ' . strip_tags($e->getMessage()) . '</p>
                        </div>';
                }
            }
            
            if ($action === 'install_data') {
                try {
                    // Read and execute data file
                    $data = file_get_contents('administration_pages_data.sql');
                    $pdo->exec($data);
                    echo '<div class="step success">
                        <h3>✅ Step 2 Complete!</h3>
                        <p>All administration pages data installed successfully.</p>
                        </div>';
                    $step = 3;
                } catch (Exception $e) {
                    echo '<div class="step error">
                        <h3>❌ Installation Error</h3>
                        <p>Error: ' . strip_tags($e->getMessage()) . '</p>
                        </div>';
                }
            }
            
            if ($step == 1) {
                ?>
                <div class="step info">
                    <h3>📋 Pre-Installation Checklist</h3>
                    <p>Before proceeding, ensure you have:</p>
                    <ul class="checklist">
                        <li>XAMPP running (Apache & MySQL)</li>
                        <li>Database <code>valley_view_uni</code> created</li>
                        <li>All files in the correct locations</li>
                        <li>Admin panel accessible</li>
                    </ul>
                </div>
                
                <div class="step">
                    <h3>Step 1: Install Database Schema</h3>
                    <p>This will create the necessary database tables and install the Vice-Chancellor page data.</p>
                    <p><strong>Tables to be created:</strong></p>
                    <ul style="margin: 15px 0 15px 20px; line-height: 1.8;">
                        <li><code>administration_pages</code> - Page metadata</li>
                        <li><code>administration_content</code> - Content sections</li>
                        <li><code>administration_content_fields</code> - Individual fields</li>
                    </ul>
                    <a href="?step=1&action=install_schema" class="btn">Install Schema & VC Data</a>
                </div>
                <?php
            } elseif ($step == 2) {
                ?>
                <div class="step">
                    <h3>Step 2: Install Remaining Pages Data</h3>
                    <p>This will install content for the following pages:</p>
                    <ul style="margin: 15px 0 15px 20px; line-height: 1.8;">
                        <li>Office of the Pro Vice-Chancellor</li>
                        <li>Office of the Registrar</li>
                        <li>Campus Rectors</li>
                        <li>University Recorders</li>
                    </ul>
                    <a href="?step=2&action=install_data" class="btn">Install Pages Data</a>
                </div>
                <?php
            } elseif ($step == 3) {
                // Verify installation
                try {
                    $pages = $pdo->query("SELECT COUNT(*) FROM administration_pages")->fetchColumn();
                    $content = $pdo->query("SELECT COUNT(*) FROM administration_content")->fetchColumn();
                    $fields = $pdo->query("SELECT COUNT(*) FROM administration_content_fields")->fetchColumn();
                    ?>
                    <div class="step success">
                        <h3>🎉 Installation Complete!</h3>
                        <p><strong>Installation Summary:</strong></p>
                        <ul class="checklist" style="margin: 15px 0; line-height: 1.8;">
                            <li><?php echo $pages; ?> Pages installed</li>
                            <li><?php echo $content; ?> Content sections created</li>
                            <li><?php echo $fields; ?> Editable fields configured</li>
                        </ul>
                    </div>
                    
                    <div class="step">
                        <h3>📝 Next Steps</h3>
                        <p>Your Administration CMS is now ready! Here's what you can do:</p>
                        <div style="margin: 20px 0;">
                            <a href="admin/manage_administration_pages.php" class="btn btn-success" style="margin-right: 10px; display: inline-block; margin-bottom: 10px;">
                                🎛️ Open Admin Dashboard
                            </a>
                            <a href="office_of_the_vice_chancellor.php" class="btn" style="display: inline-block; margin-bottom: 10px;">
                                👁️ View Vice-Chancellor Page
                            </a>
                        </div>
                    </div>
                    
                    <div class="step info">
                        <h3>📚 Documentation</h3>
                        <p>For detailed usage instructions, please read:</p>
                        <p style="margin-top: 10px;"><code>ADMINISTRATION_PAGES_SETUP.md</code></p>
                    </div>
                    
                    <div class="step">
                        <h3>🔐 Security Recommendation</h3>
                        <p><strong>Delete this installer file after installation:</strong></p>
                        <p style="margin-top: 10px;"><code>install_administration_cms.php</code></p>
                        <p style="margin-top: 10px; color: #dc3545; font-weight: bold;">This file should not be accessible in production!</p>
                    </div>
                    <?php
                } catch (Exception $e) {
                    echo '<div class="step error">
                        <h3>⚠️ Verification Error</h3>
                        <p>Installation may be incomplete. Error: ' . strip_tags($e->getMessage()) . '</p>
                        </div>';
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
