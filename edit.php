<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $id = $_GET['id'];

  // Read JSON data
  $file = 'data.json';
  $jsonData = file_get_contents($file);
  $data = json_decode($jsonData, true);

  // Find the record to edit
  $recordToEdit = null;
  foreach ($data as $record) {
    if ($record['id'] == $id) {
      $recordToEdit = $record;
      break;
    }
  }

  if (!$recordToEdit) {
    echo 'Record not found.';
    exit;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Read JSON data
  $file = 'data.json';
  $jsonData = file_get_contents($file);
  $data = json_decode($jsonData, true);

  // Update the record
  $id = $_POST['id'];
  $index = null;
  foreach ($data as $i => $record) {
    if ($record['id'] == $id) {
      $index = $i;
      break;
    }
  }

  if ($index !== null) {
    $data[$index]['name'] = $_POST['name'];
    $data[$index]['email'] = $_POST['email'];

    // Save the updated data back to the file
    file_put_contents($file, json_encode($data));

    // Redirect back to the main page
    header('Location: index.php');
    exit;
  } else {
    echo 'Record not found.';
    exit;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Record</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
  <div class="container">
    <h1>Edit Record</h1>

    <form method="post" action="edit.php?id=<?php echo $_GET['id']; ?>">
      <div class="mb-3">
        <label for="id" class="form-label">ID</label>
        <input type="number" name="id" class="form-control" id="id" value="<?php echo $recordToEdit['id']; ?>" readonly>
      </div>
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" class="form-control" id="name" value="<?php echo $recordToEdit['name']; ?>">
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" id="email" value="<?php echo $recordToEdit['email']; ?>">
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
