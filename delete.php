<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $id = $_GET['id'];

  // Read JSON data
  $file = 'data.json';
  $jsonData = file_get_contents($file);
  $data = json_decode($jsonData, true);

  // Find the record to delete
  $recordToDelete = null;
  foreach ($data as $key => $record) {
    if ($record['id'] == $id) {
      $recordToDelete = $record;
      unset($data[$key]);
      break;
    }
  }

  if ($recordToDelete) {
    // Re-index the array keys
    $data = array_values($data);

    // Save the updated data back to the file
    file_put_contents($file, json_encode($data));
  } else {
    echo 'Record not found.';
    exit;
  }

  // Redirect back to the main page
  header('Location: index.php');
  exit;
}
?>
