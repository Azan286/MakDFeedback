<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// ==== COLLECT FORM DATA ====
$name        = $_POST['name'] ?? '';
$email       = $_POST['email'] ?? '';
$department  = $_POST['department'] ?? '';
$project     = $_POST['project'] ?? '';
$experience  = $_POST['experience'] ?? '';
$message     = $_POST['message'] ?? '';
$shortQs     = $_POST['short_questions_summary'] ?? '';
$timestamp   = date("Y-m-d H:i:s");

// ==== SAVE FEEDBACK INTO JSON ====
$feedbackFile = __DIR__ . '/data/feedback.json';
$existing = [];

if (file_exists($feedbackFile)) {
    $existing = json_decode(file_get_contents($feedbackFile), true);
    if (!is_array($existing)) $existing = [];
}

// *** IMPORTANT — ADD UNIQUE ID ***
$entry = [
    'id' => uniqid("fb_", true),
    'name' => $name,
    'email' => $email,
    'department' => $department,
    'project' => $project,
    'experience' => $experience,
    'short_questions' => $shortQs,
    'message' => $message,
    'time' => $timestamp
];

$existing[] = $entry;
file_put_contents($feedbackFile, json_encode($existing, JSON_PRETTY_PRINT));

// ==== SEND EMAIL TO YOUR GMAIL ====
$mail = new PHPMailer(true);

try {
    // SMTP CONFIG
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'newazan06@gmail.com';  
    $mail->Password   = 'pmjm pttb cqht xypo'; // Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Sender / Receiver
    $mail->setFrom('newazan06@gmail.com', 'Expo Feedback System');
    $mail->addAddress('newazan06@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = "New Feedback Received - Project: $project";

    // ==== HTML EMAIL BODY ====
    $mail->Body = "
    <div style='font-family:Arial;padding:20px;background:#f5f5f5;border-radius:10px;'>
        <h2 style='color:#c4161c;'>New Expo Feedback Submitted</h2>

        <p><strong>Name:</strong> $name<br>
        <strong>Email:</strong> $email<br>
        <strong>Department:</strong> $department<br>
        <strong>Project Liked:</strong> $project<br>
        <strong>Overall Experience:</strong> $experience</p>

        <h3>Short Questions</h3>
        <pre style='background:#fff;padding:10px;border:1px solid #ddd;'>$shortQs</pre>

        <h3>Detailed Feedback</h3>
        <p style='background:#fff;padding:10px;border:1px solid #ddd;'>$message</p>

        <p style='color:#777;font-size:12px;margin-top:10px;'>Submitted on $timestamp</p>
    </div>
    ";

    $mail->send();

    // ==== SUCCESS MESSAGE + AUTO REDIRECT ====
    echo "
    <html>
    <head>
      <meta http-equiv='refresh' content='4;url=azan.html' />
      <style>
        body{background:#060608;color:white;font-family:Arial;text-align:center;padding-top:80px;}
      </style>
    </head>
    <body>
      <h2>✔ Feedback Saved & Email Sent Successfully</h2>
      <p>Redirecting in 4 seconds...</p>
    </body>
    </html>
    ";

} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>
