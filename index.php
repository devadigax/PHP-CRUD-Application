<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bootstrap demo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
  <div class="container">
    <h1>PHP CRUD Application</h1>

    <!-- Create Record Modal -->
    <div class="modal fade" id="createRecordModal" tabindex="-1" aria-labelledby="createRecordModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createRecordModalLabel">Create Record</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="post" action="create.php">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" class="form-control" id="name">
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="email">
              </div>
              <button type="submit" class="btn btn-primary">Create</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Record Button -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createRecordModal">
      Create Record
    </button>

    <!-- Search Form -->
    <form method="get" action="index.php" class="mb-3">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        <a href="index.php" class="btn btn-outline-dark">Refresh</a>
      </div>
    </form>

    <!-- Display Records -->
    <h2>Records</h2>
    <?php
      // Read JSON data
      $file = 'data.json';
      $jsonData = file_get_contents($file);
      $data = json_decode($jsonData, true);

      // Pagination variables
      $recordsPerPage = 5;
      $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
      $startIndex = ($currentPage - 1) * $recordsPerPage;
      $totalRecords = count($data);
      $totalPages = ceil($totalRecords / $recordsPerPage);

      // Apply search filter if set
      $filteredData = $data; // Default: show all records
      if (isset($_GET['search'])) {
        $searchTerm = strtolower($_GET['search']);
        $filteredData = array_filter($data, function ($record) use ($searchTerm) {
          $name = strtolower($record['name']);
          $email = strtolower($record['email']);
          return strpos($name, $searchTerm) !== false || strpos($email, $searchTerm) !== false;
        });
      }
      $filteredRecords = array_values($filteredData);

      // Display records for the current page
      $recordsToDisplay = array_slice($filteredRecords, $startIndex, $recordsPerPage);

      if (!empty($recordsToDisplay)) {
        echo '<table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>';
        foreach ($recordsToDisplay as $record) {
          echo '<tr>
                  <td>' . $record['id'] . '</td>
                  <td>' . $record['name'] . '</td>
                  <td>' . $record['email'] . '</td>
                  <td>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editRecordModal-' . $record['id'] . '">Edit</button>
                    <a href="delete.php?id=' . $record['id'] . '" class="btn btn-danger">Delete</a>
                  </td>
                </tr>';

          // Edit Record Modal
          echo '<div class="modal fade" id="editRecordModal-' . $record['id'] . '" tabindex="-1" aria-labelledby="editRecordModalLabel-' . $record['id'] . '" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editRecordModalLabel-' . $record['id'] . '">Edit Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <form method="post" action="edit.php?id=' . $record['id'] . '">
                          <div class="mb-3">
                            <label for="id-' . $record['id'] . '" class="form-label">id</label>
                            <input type="number" name="id" class="form-control" id="id-' . $record['id'] . '" value="' . $record['id'] . '" readonly>
                          </div>
                          <div class="mb-3">
                            <label for="name-' . $record['id'] . '" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="name-' . $record['id'] . '" value="' . $record['name'] . '">
                          </div>
                          <div class="mb-3">
                            <label for="email-' . $record['id'] . '" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email-' . $record['id'] . '" value="' . $record['email'] . '">
                          </div>
                          <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>';
        }
        echo '</tbody></table>';

        // Pagination links
        $maxPaginationLinks = 5; // Maximum number of pagination links to display
        $startPage = max(1, $currentPage - floor($maxPaginationLinks / 2));
        $endPage = min($startPage + $maxPaginationLinks - 1, $totalPages);

        echo '<nav aria-label="Page navigation">';
        echo '<ul class="pagination">';

        // "Previous" link
        if ($currentPage > 1) {
          echo '<li class="page-item"><a class="page-link" href="index.php?page=' . ($currentPage - 1) . '&search=' . urlencode(isset($_GET['search']) ? $_GET['search'] : '') . '">Previous</a></li>';
        }

        // Pagination links
        for ($page = $startPage; $page <= $endPage; $page++) {
          echo '<li class="page-item' . ($page == $currentPage ? ' active' : '') . '"><a class="page-link" href="index.php?page=' . $page . '&search=' . urlencode(isset($_GET['search']) ? $_GET['search'] : '') . '">' . $page . '</a></li>';
        }

        // "Next" link
        if ($currentPage < $totalPages) {
          echo '<li class="page-item"><a class="page-link" href="index.php?page=' . ($currentPage + 1) . '&search=' . urlencode(isset($_GET['search']) ? $_GET['search'] : '') . '">Next</a></li>';
        }

        echo '</ul>';
        echo '</nav>';
      } else {
        echo '<p>No records found.</p>';
      }
    ?>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>