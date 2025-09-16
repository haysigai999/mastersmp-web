<?php
$status = file_exists("status.txt") ? file_get_contents("status.txt") : "Unknown";
$motd = file_exists("motd.txt") ? file_get_contents("motd.txt") : "Welcome to MasterSMP!";
$announcement = file_exists("announcement.txt") ? file_get_contents("announcement.txt") : "";
$event = file_exists("event.json") ? json_decode(file_get_contents("event.json"), true) : null;
$leaderboard = file_exists("leaderboard.json") ? json_decode(file_get_contents("leaderboard.json"), true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MasterSMP - Website</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: sans-serif;
      background: url('assets/img/background.png') no-repeat center center fixed;
      background-size: cover;
      color: #2cdede;
      text-align: center;
      padding: 2rem;
    }
    .section {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(5px);
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
  max-width: 900px;
  margin: 40px auto;
}

    .label { font-weight: bold; font-size: 1.3rem; margin-bottom: 0.5rem; }
    .build-img { width: 100%; max-width: 300px; border-radius: 10px; margin: 0.5rem; }
    #countdown { font-size: 1.5rem; color: #ffcc00; }
    
  </style>
</head>
<body>
  <audio autoplay loop style="display: none;">
    <source src="assets/audio/.mp3" type="audio/mpeg">
  </audio>
  <h1>🟢 MasterSMP Website</h1>

  <div id="server-status"><?php include("mcstatus.php"); ?></div>

  <div class="section">
    <div class="label">👥 Online Players:</div>
    <div id="players"></div>
  </div>
  
  <?php if ($event): ?>
  <div class="section">
    <div class="label">Next Event:</div>
    <h3><?= htmlspecialchars($event['title']) ?></h3>
    <div id="countdown">Loading countdown...</div>
    <script>
      const targetTime = new Date("<?= $event['time'] ?>").getTime();
      setInterval(() => {
        const now = new Date().getTime();
        const diff = targetTime - now;
        if (diff <= 0) {
          document.getElementById("countdown").innerText = "Event started!";
          return;
        }
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        document.getElementById("countdown").innerText = `${h}h ${m}m ${s}s`;
      }, 1000);
    </script>
  </div>
  <?php endif; ?>

  <div class="section">
    <div class="label">Top Players:</div>
    <ul>
      <?php
      arsort($leaderboard);
      $icons = ["🥇", "🥈", "🥉"];
      $i = 0;
      foreach ($leaderboard as $name => $score) {
        $rank = $icons[$i] ?? ($i + 1) . ".";
        echo "<li>$rank $name — $score Kills</li>";
        $i++;
      }
      ?>
    </ul>
  </div>

  <div class="section">
    <div class="label">Build Showcase:</div>
    <?php foreach (glob("builds/*.{jpg,png,gif}", GLOB_BRACE) as $img): ?>
      <img src="<?= $img ?>" class="build-img">
    <?php endforeach; ?>
    <p>🏰 Explore our latest builds — from floating islands to underground bunkers!</p>
  </div>

  <?php if ($announcement): ?>
  <div class="section">
    <div class="label">📢 Announcement:</div>
    <p><?= nl2br(htmlspecialchars($announcement)) ?></p>
  </div>
  <?php endif; ?>

  <div class="section">
    <div class="label">💬 Discord Chat:</div>
    <iframe src="https://discord.com/widget?id=1414225205710159966&theme=dark" width="100%" height="500" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-same-origin allow-scripts"></iframe>
  </div>
  <script>
    const server = "mastersmp.danchoimc.net";
const apiUrl = `https://api.mcsrvstat.us/3/${server}`;

fetch(apiUrl)
  .then(res => res.json())
  .then(data => {
    const players = data.players.list || [];
    const container = document.getElementById("players");

    if (players.length === 0) {
      container.innerHTML = "<p>No players online.</p>";
      return;
    }

    players.forEach(p => {
  const name = p.name || "Unknown";
  const rank = p.extensionValues?.primaryGroup?.value || "default";
  const avatar = `https://minotar.net/avatar/${name}/64`;

  const div = document.createElement("div");
  div.className = "player";
  div.innerHTML = `
    <img src="${avatar}" width="64" height="64" alt="${name}">
    <div><strong>${name}</strong></div>
    <div>Rank: ${rank}</div>
  `;
  container.appendChild(div);
    });
  })
  .catch(err => {
    document.getElementById("players").innerHTML = "<p>Error loading player data.</p>";
    console.error("API error:", err);
  });
  </script>
</body>
</html>
