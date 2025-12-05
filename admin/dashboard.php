<?php
session_start();
if (!isset($_SESSION['admin'])) { 
    header('Location: login.php'); 
    exit; 
}

$data_dir = __DIR__ . '/../data';
$projects = [];
$feedbacks = [];

// Load JSON files
if (file_exists($data_dir . '/projects.json'))
    $projects = json_decode(file_get_contents($data_dir . '/projects.json'), true);

if (file_exists($data_dir . '/feedback.json'))
    $feedbacks = json_decode(file_get_contents($data_dir . '/feedback.json'), true);

// ALWAYS RECALCULATE LIVE VOTES
$counts = [];
foreach ($projects as $p) $counts[$p['name']] = 0;

foreach ($feedbacks as $f) {
    if (!empty($f['project']) && isset($counts[$f['project']])) {
        $counts[$f['project']]++;
    }
}

arsort($counts);
$total_feedbacks = count($feedbacks);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background:#0b0c10;
        color:#f0f0f0;
    }

    .card {
        background:#17181d;
        padding:18px;
        border-radius:10px;
        box-shadow:0 2px 10px rgba(0,0,0,0.4);
        margin-bottom:18px;
        border:1px solid rgba(255,255,255,0.08);
    }

    .grid {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:18px;
    }

    /* CLEAN TABLE */
    table {
        width:100%;
        border-collapse:separate;
        border-spacing: 0 8px; /* row gap */
    }

    th {
        background:#20222a;
        padding:14px;
        border: none;
        font-weight:700;
        color:#fff;
    }

    td {
        background:#14151a;
        padding:14px;
        border: none;
        color:#e5e5e5;
        border-bottom:1px solid rgba(255,255,255,0.06);
    }

    /* Rounded row corners */
    tr td:first-child {
        border-top-left-radius:10px;
        border-bottom-left-radius:10px;
    }
    tr td:last-child {
        border-top-right-radius:10px;
        border-bottom-right-radius:10px;
    }

    .button {
        background:#c4161c !important; 
        color:#fff !important;
        padding:8px 12px;
        border-radius:6px;
        text-decoration:none;
        font-weight:bold;
    }

    .button:hover { opacity:0.85; }

    .success {
        padding:12px;
        margin-bottom:14px;
        background:#0f5132;
        color:#d1e7dd;
        border:1px solid #145a3b;
        border-radius:6px;
    }
</style>

</head>
<body>

<h1>Admin Dashboard</h1>

<?php if (isset($_GET['deleted'])): ?>
<div class="success">Feedback deleted successfully.</div>
<?php endif; ?>

<div class="grid">

    <!-- OVERVIEW -->
    <div class="card">
        <h3>Overview</h3>

        <p>Total feedbacks: <strong><?php echo $total_feedbacks; ?></strong></p>
        <p>Total projects: <strong><?php echo count($projects); ?></strong></p>

        <hr style="border:0;border-bottom:1px solid rgba(255,255,255,0.08);margin:12px 0;">

        <h4 style="margin-bottom:8px;color:#4db0ff;">Project Votes</h4>
        <ul style="margin:0;padding-left:18px;line-height:1.7;">
            <?php foreach ($counts as $pname => $votes): ?>
                <li>
                    <strong><?php echo htmlspecialchars($pname); ?></strong>
                    — <span style="color:#4db0ff;"><?php echo $votes; ?> vote<?php echo ($votes==1?'':'s'); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <p style="margin-top:16px;">
            <a class="button" href="projects.php">Manage Projects</a>
            <a class="button" href="logout.php" style="background:#a11218;">Logout</a>
        </p>
    </div>

    <!-- CHART -->
    <div class="card">
        <h3>Projects Popularity</h3>
        <canvas id="projectsChart" height="220"></canvas>
    </div>

</div>

<!-- RECENT FEEDBACK -->
<div class="card">
    <h3>Recent Feedbacks</h3>
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        <?php 
        $latest = array_reverse($feedbacks);
        foreach (array_slice($latest, 0, 20) as $fb): ?>
        
        <tr>
            <td><?php echo htmlspecialchars($fb['project']); ?></td>
            <td><?php echo htmlspecialchars($fb['name']); ?></td>
            <td><?php echo htmlspecialchars($fb['email']); ?></td>
            <td>
                <a class="button" href="view.php?id=<?php echo urlencode($fb['id']); ?>">View</a>
            </td>
        </tr>

        <?php endforeach; ?>

        </tbody>
    </table>
</div>

<script>
const labels = <?php echo json_encode(array_keys($counts)); ?>;
const data = <?php echo json_encode(array_values($counts)); ?>;

new Chart(document.getElementById('projectsChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Votes',
            data: data,
            backgroundColor: '#4db0ff'
        }]
    },
    options: { responsive: true }
});
</script>

</body>
</html>
