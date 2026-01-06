<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['channel_id'])) {
    $channel_id = trim($_POST['channel_id']);
    
    // Check if user exists, if not create
    $stmt = $pdo->prepare("SELECT * FROM users WHERE channel_id = ?");
    $stmt->execute([$channel_id]);
    $user = $stmt->fetch();

    if (!$user) {
        // Fetch Channel Name via API (Simplified logic)
        $apiUrl = "https://www.googleapis.com/youtube/v3/channels?part=snippet&id={$channel_id}&key=" . YT_API_KEY;
        $response = @file_get_contents($apiUrl);
        $data = json_decode($response, true);
        $channel_name = $data['items'][0]['snippet']['title'] ?? 'New User';

        $ins = $pdo->prepare("INSERT INTO users (channel_id, channel_name) VALUES (?, ?)");
        $ins->execute([$channel_id, $channel_name]);
    }

    $_SESSION['channel_id'] = $channel_id;
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProSub - Elite Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white h-screen flex items-center justify-center overflow-hidden relative">
    
    <!-- Background Effects -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute w-96 h-96 bg-purple-600 rounded-full blur-3xl opacity-20 -top-20 -left-20"></div>
        <div class="absolute w-96 h-96 bg-blue-600 rounded-full blur-3xl opacity-20 bottom-0 right-0"></div>
    </div>

    <div class="relative z-10 w-full max-w-md p-8 bg-slate-800/80 backdrop-blur-lg rounded-2xl border border-slate-700 shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-amber-200 to-yellow-500 bg-clip-text text-transparent drop-shadow-lg">
                PRO SUB 4 SUB
            </h1>
            <p class="text-slate-400 mt-2 text-sm">Platform Pertukaran Interaktif Premium</p>
        </div>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">YouTube Channel ID</label>
                <input type="text" name="channel_id" required 
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition text-center tracking-wider placeholder-slate-600"
                    placeholder="UCxxxxxxxxxxxxxxxxx">
            </div>
            <button type="submit" 
                class="w-full py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg transform transition hover:scale-105 active:scale-95">
                MASUK DASHBOARD
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-xs text-slate-500">
                Security Protocol: Active.<br>
                Sistem mendeteksi kecurangan secara real-time.
            </p>
        </div>
    </div>
</body>
</html>