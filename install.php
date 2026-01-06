<?php
require 'config.php';

$message = '';
$status = '';

if (isset($_POST['run_install'])) {
    try {
        // Read the schema file
        $sql = file_get_contents('schema.sql');
        
        // Execute the SQL
        // Note: Some PDO drivers do not support multiple statements in one exec call.
        // We split by semicolon to be safe.
        $statements = explode(';', $sql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        $status = 'success';
        $message = "Database tables created successfully! Check your database 'danas234_sub1'.";
    } catch (PDOException $e) {
        $status = 'error';
        $message = "Installation Failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-white h-screen flex items-center justify-center">
    
    <div class="w-full max-w-md p-8 bg-slate-800 rounded-2xl border border-slate-700 shadow-2xl text-center">
        <div class="mb-6">
            <i class="fa-solid fa-database text-5xl text-amber-500"></i>
        </div>
        <h1 class="text-2xl font-bold mb-2">Database Installer</h1>
        <p class="text-slate-400 mb-6 text-sm">This script will create the missing tables (users, subscriptions) in your connected database.</p>

        <?php if ($status == 'success'): ?>
            <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 p-4 rounded-xl mb-6">
                <i class="fa-solid fa-check-circle mr-2"></i> <?php echo $message; ?>
            </div>
            <a href="index.php" class="inline-block w-full py-3 bg-slate-700 hover:bg-slate-600 rounded-xl font-bold transition">
                Go to Login
            </a>
        <?php elseif ($status == 'error'): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-400 p-4 rounded-xl mb-6 text-left text-sm break-words">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i> <?php echo $message; ?>
            </div>
            <a href="install.php" class="text-slate-400 hover:text-white text-sm">Try Again</a>
        <?php else: ?>
            <form method="POST">
                <button type="submit" name="run_install" 
                    class="w-full py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg transform transition hover:scale-105">
                    INSTALL TABLES
                </button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>