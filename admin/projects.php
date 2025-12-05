<?php
session_start();
if(!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
$data_file = __DIR__ . '/../data/projects.json';
$projects = [];
if(file_exists($data_file)) $projects = json_decode(file_get_contents($data_file), true);

// handle add/edit/delete via POST
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if($action === 'add'){
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        if($name){
            $id = time() . rand(10,99);
            $projects[] = ['id'=>$id,'name'=>$name];
            file_put_contents($data_file, json_encode($projects, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
            header('Location: projects.php');
            exit;
        }
    } elseif($action === 'delete'){
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $projects = array_filter($projects, function($p) use($id){ return $p['id'] != $id; });
        file_put_contents($data_file, json_encode(array_values($projects), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        header('Location: projects.php');
        exit;
    } elseif($action === 'edit'){
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        foreach($projects as &$p) if($p['id']==$id) $p['name']=$name;
        file_put_contents($data_file, json_encode(array_values($projects), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        header('Location: projects.php');
        exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Manage Projects</title></head><body style="font-family:Arial, sans-serif; padding:20px;">
  <a href="dashboard.php">← Back</a>
  <h2>Projects</h2>
  <form method="post" style="margin-bottom:12px;">
    <input type="hidden" name="action" value="add">
    <input type="text" name="name" placeholder="Project name" required style="padding:8px;width:60%;">
    <button type="submit">Add Project</button>
  </form>
  <table style="width:100%;border-collapse:collapse;">
    <thead><tr><th>Name</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach($projects as $p): ?>
      <tr>
        <td><?php echo htmlspecialchars($p['name']); ?></td>
        <td>
          <form method="post" style="display:inline-block;margin-right:6px;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($p['id']); ?>">
            <button type="submit" onclick="return confirm('Delete?')">Delete</button>
          </form>
          <form method="post" style="display:inline-block;">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($p['id']); ?>">
            <input type="text" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required>
            <button type="submit">Save</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</body></html>