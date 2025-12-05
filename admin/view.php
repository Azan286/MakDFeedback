<?php
session_start();
if(!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
$id = isset($_GET['id']) ? $_GET['id'] : '';
$data_file = __DIR__ . '/../data/feedback.json';
$feedbacks = [];
if(file_exists($data_file)) $feedbacks = json_decode(file_get_contents($data_file), true);
$found = null;
foreach($feedbacks as $f){
    if($f['id'] == $id){ $found = $f; break; }
}
if(!$found){ echo "Feedback not found"; exit; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>View Feedback</title>
</head>

<body style="font-family:Arial, sans-serif; padding:20px;">
  <a href="dashboard.php">← Back</a>
  <h2>Feedback Details</h2>

  <p><strong>Project:</strong> <?php echo htmlspecialchars($found['project']); ?></p>
  <p><strong>Name:</strong> <?php echo htmlspecialchars($found['name']); ?></p>
  <p><strong>Email:</strong> <?php echo htmlspecialchars($found['email']); ?></p>

  <h3>Quick Questions</h3>
  <pre style="white-space:pre-wrap;"><?php echo htmlspecialchars($found['short_questions']); ?></pre>

  <h3>Full Message</h3>
  <p><?php echo nl2br(htmlspecialchars($found['message'])); ?></p>

  <!-- DELETE BUTTON -->
  <div style="margin-top:30px; text-align:center;">
    <a href="delete_feedback.php?id=<?php echo urlencode($found['id']); ?>"
       onclick="return confirm('Are you sure you want to delete this feedback?');"
       style="background:#c4161c; color:white; padding:10px 18px; border-radius:8px;
              text-decoration:none; font-weight:bold;">
       Delete Feedback
    </a>
  </div>

</body>
</html>
