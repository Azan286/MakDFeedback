<?php
session_start();
if(isset($_SESSION['admin'])) header('Location: dashboard.php');
$message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = isset($_POST['email']) ? trim($_POST['email']) : '';
    $pass = isset($_POST['password']) ? trim($_POST['password']) : '';
    // hardcoded credentials as requested
    if($id === 'newazan06@gmail.com' && $pass === '1234'){
        $_SESSION['admin'] = $id;
        header('Location: dashboard.php');
        exit;
    } else {
        $message = 'Invalid credentials';
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin Login</title></head>
<body style="font-family:Arial, sans-serif; display:flex; align-items:center; justify-content:center; height:100vh; background:#f5f5f5;">
  <form method="post" style="background:#fff;padding:24px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.08);width:360px;">
    <h2>Admin Login</h2>
    <?php if($message): ?><div style="color:red;margin-bottom:8px;"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <label>Email</label><input type="email" name="email" required style="width:100%;padding:8px;margin:6px 0;">
    <label>Password</label><input type="password" name="password" required style="width:100%;padding:8px;margin:6px 0;">
    <button type="submit" style="padding:10px 14px;margin-top:8px;">Login</button>
  </form>
</body>
</html>
