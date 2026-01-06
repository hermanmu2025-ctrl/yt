<?php
require 'config.php';

if (!isset($_SESSION['channel_id'])) {
    header("Location: index.php");
    exit;
}

$my_id = $_SESSION['channel_id'];

// Get User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE channel_id = ?");
$stmt->execute([$my_id]);
$user = $stmt->fetch();

// Security Check Logic (Simplified Simulation)
// In production, this requires OAuth token to read private subs.
// Here we check based on DB records vs hypothetical API response.
if ($user['status'] == 'suspended') {
    die("<div style='background:black; color:red; padding:50px; text-align:center; font-family:sans-serif;'><h1>AKUN DIBEKUKAN</h1><p>Anda terdeteksi melakukan Unsubscribe pada channel yang sebelumnya Anda subscribe. Sesuai aturan, seluruh poin dan subscriber yang Anda dapatkan telah ditarik kembali.</p><a href='logout.php' style='color:white'>Logout</a></div>");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pro - <?php echo htmlspecialchars($user['channel_name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</head>
<body class="bg-slate-900 text-slate-200 font-sans">

    <!-- Top Navbar -->
    <nav class="fixed w-full z-50 bg-slate-800 border-b border-slate-700 h-16 flex items-center justify-between px-6 shadow-lg">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-400 to-red-500"></div>
            <span class="font-bold text-xl tracking-tight text-white">ProSub<span class="text-amber-500">Elite</span></span>
        </div>
        <div class="flex items-center gap-6">
            <div class="hidden md:flex flex-col text-right">
                <span class="text-xs text-slate-400">Logged as</span>
                <span class="font-semibold text-amber-400"><?php echo htmlspecialchars($user['channel_name']); ?></span>
            </div>
            <div class="bg-slate-700 px-4 py-1 rounded-full border border-slate-600 flex items-center gap-2">
                <i class="fa-solid fa-coins text-yellow-400"></i>
                <span class="font-bold text-white" id="points-display"><?php echo $user['points']; ?></span>
            </div>
            <a href="logout.php" class="text-slate-400 hover:text-white transition"><i class="fa-solid fa-power-off"></i></a>
        </div>
    </nav>

    <div class="flex pt-16 min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-800 border-r border-slate-700 hidden md:block fixed h-full">
            <div class="p-6 space-y-2">
                <button onclick="showSection('earn')" class="w-full text-left px-4 py-3 rounded-lg bg-slate-700 text-white hover:bg-slate-600 transition flex items-center gap-3">
                    <i class="fa-solid fa-play text-red-500"></i> Cari Subscriber
                </button>
                <button onclick="showSection('history')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-slate-700 transition flex items-center gap-3">
                    <i class="fa-solid fa-clock-rotate-left text-blue-400"></i> Riwayat
                </button>
                <button onclick="showSection('help')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-slate-700 transition flex items-center gap-3">
                    <i class="fa-solid fa-headset text-emerald-400"></i> Admin Help
                </button>
            </div>
            <div class="absolute bottom-0 w-full p-6">
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-700">
                    <p class="text-xs text-slate-400 mb-2">Security Status</p>
                    <div class="flex items-center gap-2 text-emerald-500 text-sm font-bold">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        Monitored
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 p-8 overflow-y-auto">
            
            <!-- Notification Area -->
            <div id="alert-box" class="hidden mb-6 p-4 rounded-lg border"></div>

            <!-- Section: Earn (Dashboard) -->
            <div id="section-earn" class="section-content space-y-6">
                <div class="flex justify-between items-end border-b border-slate-700 pb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-white">Ruang Pertukaran</h2>
                        <p class="text-slate-400">Subscribe channel lain untuk mendapatkan poin.</p>
                    </div>
                    <button onclick="checkIntegrity()" class="text-xs bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600">
                        <i class="fa-solid fa-shield-halved"></i> Check Integrity
                    </button>
                </div>

                <!-- Channel List Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="channel-list">
                    <!-- Loaded via JS -->
                    <div class="animate-pulse bg-slate-800 h-48 rounded-xl"></div>
                    <div class="animate-pulse bg-slate-800 h-48 rounded-xl"></div>
                    <div class="animate-pulse bg-slate-800 h-48 rounded-xl"></div>
                </div>
            </div>

            <!-- Section: History -->
            <div id="section-history" class="section-content hidden space-y-6">
                <h2 class="text-3xl font-bold text-white">Riwayat Aktivitas</h2>
                <div class="bg-slate-800 rounded-xl overflow-hidden border border-slate-700">
                    <table class="w-full text-left">
                        <thead class="bg-slate-900 text-slate-400 text-sm uppercase">
                            <tr>
                                <th class="p-4">Tipe</th>
                                <th class="p-4">Channel Target</th>
                                <th class="p-4">Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body" class="divide-y divide-slate-700">
                            <!-- Loaded via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section: Help -->
            <div id="section-help" class="section-content hidden space-y-6">
                <h2 class="text-3xl font-bold text-white">Pusat Bantuan</h2>
                <div class="bg-slate-800 p-6 rounded-xl border border-slate-700 max-w-2xl">
                    <form onsubmit="sendTicket(event)" class="space-y-4">
                        <div>
                            <label class="block text-sm mb-2 text-slate-300">Pesan Anda</label>
                            <textarea id="help-msg" rows="4" class="w-full bg-slate-900 border border-slate-600 rounded-lg p-3 text-white focus:border-amber-500 outline-none"></textarea>
                        </div>
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-6 rounded-lg transition">
                            Kirim Tiket
                        </button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
        const myChannelId = '<?php echo $my_id; ?>';
        const apiKey = '<?php echo YT_API_KEY; ?>';

        function showSection(id) {
            document.querySelectorAll('.section-content').forEach(el => el.classList.add('hidden'));
            document.getElementById('section-' + id).classList.remove('hidden');
            if(id === 'earn') loadChannels();
            if(id === 'history') loadHistory();
        }

        async function loadChannels() {
            const container = document.getElementById('channel-list');
            const res = await fetch('api.php?action=get_channels');
            const data = await res.json();

            if(data.length === 0) {
                container.innerHTML = `<div class="col-span-3 text-center text-slate-500 py-10">Belum ada channel lain tersedia.</div>`;
                return;
            }

            container.innerHTML = data.map(ch => `
                <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 hover:border-amber-500/50 transition shadow-lg relative group">
                    <div class="absolute top-4 right-4 text-xs bg-slate-700 px-2 py-1 rounded text-slate-300">
                        ID: ${ch.channel_id.substr(0, 5)}...
                    </div>
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div class="w-20 h-20 bg-gradient-to-br from-slate-600 to-slate-700 rounded-full flex items-center justify-center text-3xl font-bold text-slate-400">
                            ${ch.channel_name.charAt(0)}
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-white">${ch.channel_name}</h3>
                            <p class="text-sm text-emerald-400">+1 Point</p>
                        </div>
                        <button onclick="subscribeAction('${ch.channel_id}', this)" 
                            class="w-full py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition flex items-center justify-center gap-2">
                            <i class="fa-brands fa-youtube"></i> SUBSCRIBE
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function subscribeAction(targetId, btn) {
            // 1. Open Youtube
            window.open(`https://www.youtube.com/channel/${targetId}?sub_confirmation=1`, '_blank');

            // 2. UI Feedback
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifikasi...';
            btn.disabled = true;
            btn.classList.replace('bg-red-600', 'bg-slate-600');

            // 3. Simulate Verification & Update DB
            setTimeout(async () => {
                const formData = new FormData();
                formData.append('target_id', targetId);
                
                const res = await fetch('api.php?action=subscribe', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();

                if (result.success) {
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Sukses';
                    btn.classList.replace('bg-slate-600', 'bg-emerald-600');
                    document.getElementById('points-display').innerText = result.new_points;
                    // Remove card after delay
                    setTimeout(() => btn.closest('.group').remove(), 1000);
                } else {
                    alert(result.message);
                    btn.innerHTML = 'Gagal';
                    btn.disabled = false;
                }
            }, 5000); // 5 seconds expected time to sub
        }

        async function loadHistory() {
            const res = await fetch('api.php?action=get_history');
            const data = await res.json();
            const tbody = document.getElementById('history-table-body');
            tbody.innerHTML = data.map(row => `
                <tr class="hover:bg-slate-800/50">
                    <td class="p-4"><span class="text-emerald-400">Subscribed</span></td>
                    <td class="p-4 font-mono text-slate-300">${row.target_channel_id}</td>
                    <td class="p-4 text-slate-500 text-sm">${row.created_at}</td>
                </tr>
            `).join('');
        }

        async function sendTicket(e) {
            e.preventDefault();
            const msg = document.getElementById('help-msg').value;
            const formData = new FormData();
            formData.append('message', msg);
            
            await fetch('api.php?action=send_ticket', {
                method: 'POST',
                body: formData
            });
            alert('Tiket terkirim!');
            document.getElementById('help-msg').value = '';
        }

        // Background Integrity Check (Runs silently)
        function checkIntegrity() {
            fetch('api.php?action=check_integrity')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'banned') {
                        location.reload(); // Will trigger PHP die screen
                    }
                });
        }

        // Init
        loadChannels();
        setInterval(checkIntegrity, 30000); // Check every 30s
    </script>
</body>
</html>