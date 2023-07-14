<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Read JSON data
  $file = 'data.json';
  $jsonData = file_get_contents($file);
  $data = json_decode($jsonData, true);

  // Generate a new ID
  $newId = 1;
  if (!empty($data)) {
    $lastRecord = end($data);
    $newId = $lastRecord['id'] + 1;
  }

  // Create a new record
  $newRecord = array(
    'id' => $newId,
    'name' => $_POST['name'],
    'email' => $_POST['email']
  );

  $data[] = $newRecord;

  // Save the updated data back to the file
  $jsonData = json_encode($data);
  file_put_contents($file, $jsonData);

  // Redirect back to the main page
  header('Location: index.php');
  exit;
}
?>
