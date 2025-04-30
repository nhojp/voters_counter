<?php
include "conn.php";

// SQL query to fetch all data from the voters table
$sql = "SELECT * FROM voters";
$result = $conn->query($sql);

// Get the total counts for each vote type
$countBuhainSql = "SELECT COUNT(*) AS total_buhain FROM voters WHERE vote = 'Buhain'";
$countLeandroSql = "SELECT COUNT(*) AS total_leandro FROM voters WHERE vote = 'Leandro'";
$countUndecidedSql = "SELECT COUNT(*) AS total_undecided FROM voters WHERE vote = 'Undecided'";

$countBuhainResult = $conn->query($countBuhainSql);
$countLeandroResult = $conn->query($countLeandroSql);
$countUndecidedResult = $conn->query($countUndecidedSql);

$countBuhain = $countBuhainResult->fetch_assoc()['total_buhain'];
$countLeandro = $countLeandroResult->fetch_assoc()['total_leandro'];
$countUndecided = $countUndecidedResult->fetch_assoc()['total_undecided'];

// Get distinct precinct numbers for the dropdown
$precinctSql = "SELECT DISTINCT precinct_no FROM voters ORDER BY precinct_no";
$precinctResult = $conn->query($precinctSql);
$precinctNumbers = [];
while($row = $precinctResult->fetch_assoc()) {
    $precinctNumbers[] = $row['precinct_no'];
}

// Get distinct purok values for the dropdown
$purokSql = "SELECT DISTINCT purok FROM voters ORDER BY purok";
$purokResult = $conn->query($purokSql);
$puroks = [];
while($row = $purokResult->fetch_assoc()) {
    $puroks[] = $row['purok'];
}

// Get distinct vote values for the dropdown
$voteSql = "SELECT DISTINCT vote FROM voters ORDER BY vote";
$voteResult = $conn->query($voteSql);
$votes = [];
while($row = $voteResult->fetch_assoc()) {
    $votes[] = $row['vote'];
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Mylene System</title>
    <link rel="icon" href="images/mylene.jpg" type="image/jpeg">
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #searchBar {
            margin-bottom: 20px;
        }
        .edit-btn {
            color: #fff;
            background-color: #007bff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .filter-dropdown {
            width: 100%;
            padding: 3px;
            font-size: 0.8em;
        }
        .filter-header {
            vertical-align: middle;
        }
        .vote-totals {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Sampaga Voters Data</h2>

        <!-- Row for Vote Totals -->
        <div class="row mb-3">
            <div class="col-md-4 text-center bg-danger text-white p-2">
                <div class="vote-totals">Buhain: <?php echo $countBuhain; ?></div>
            </div>
            <div class="col-md-4 text-center bg-primary text-white p-2">
                <div class="vote-totals">Leandro: <?php echo $countLeandro; ?></div>
            </div>
            <div class="col-md-4 text-center bg-secondary text-white p-2">
                <div class="vote-totals">Undecided: <?php echo $countUndecided; ?></div>
            </div>
        </div>

        <!-- Table to display data -->
        <table class="table table-striped table-hover" id="votersTable">
            <thead class="table-dark">
                <tr>
                    <th class="filter-header">#</th>
                    <th class="filter-header">
                        Precinct No.
                        <select class="filter-dropdown" onchange="filterTable()" id="precinctFilter">
                            <option value="">All</option>
                            <?php foreach($precinctNumbers as $precinct): ?>
                                <option value="<?php echo htmlspecialchars($precinct); ?>"><?php echo htmlspecialchars($precinct); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th class="filter-header">
                        Voter's Name
                        <input type="text" class="filter-dropdown" placeholder="Search name..." id="nameFilter" onkeyup="filterTable()">
                    </th>
                    <th class="filter-header">
                        Purok
                        <select class="filter-dropdown" onchange="filterTable()" id="purokFilter">
                            <option value="">All</option>
                            <?php foreach($puroks as $purok): ?>
                                <option value="<?php echo htmlspecialchars($purok); ?>"><?php echo htmlspecialchars($purok); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th class="filter-header">
                        Vote
                        <select class="filter-dropdown" onchange="filterTable()" id="voteFilter">
                            <option value="">All</option>
                            <?php foreach($votes as $vote): ?>
                                <option value="<?php echo htmlspecialchars($vote); ?>"><?php echo htmlspecialchars($vote); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th class="filter-header">Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php
    if ($result->num_rows > 0) {
        $rowNum = 1; // Counter for the rows
        while($row = $result->fetch_assoc()) {
            echo "<tr id='voter-row-" . $row['id'] . "'>";
            echo "<td>" . $rowNum++ . "</td>"; // Display row number
            echo "<td>" . htmlspecialchars($row['precinct_no']) . "</td>";
            echo "<td>" . htmlspecialchars($row['voters_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['purok']) . "</td>";
            echo "<td>" . htmlspecialchars($row['vote']) . "</td>";
            echo "<td><button class='edit-btn' onclick='editData(" . $row['id'] . ", \"" . htmlspecialchars($row['precinct_no']) . "\", \"" . htmlspecialchars($row['voters_name']) . "\", \"" . htmlspecialchars($row['purok']) . "\", \"" . htmlspecialchars($row['vote']) . "\")'>Edit</button></td>"; // Edit button
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6' class='text-center'>No data found</td></tr>";
    }
    ?>
</tbody>
        </table>
    </div>

    <!-- Modal for editing voter data -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Voter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="edit_voter.php" method="POST">
                        <input type="hidden" name="id" id="voterId">
                        <div class="mb-3">
                            <label for="precinctNo" class="form-label">Precinct No.</label>
                            <input type="text" class="form-control" id="precinctNo" name="precinct_no" required>
                        </div>
                        <div class="mb-3">
                            <label for="voterName" class="form-label">Voter's Name</label>
                            <input type="text" class="form-control" id="voterName" name="voters_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="purok" class="form-label">Purok</label>
                            <input type="text" class="form-control" id="purok" name="purok" required>
                        </div>
                        <div class="mb-3">
                            <label for="vote" class="form-label">Vote</label>
                            <input type="text" class="form-control" id="vote" name="vote" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and Popper.js (for responsiveness and interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script>
        function editData(id, precinctNo, voterName, purok, vote) {
            // Set the form values in the modal
            document.getElementById('voterId').value = id;
            document.getElementById('precinctNo').value = precinctNo;
            document.getElementById('voterName').value = voterName;
            document.getElementById('purok').value = purok;
            document.getElementById('vote').value = vote;

            // Show the modal
            var myModal = new bootstrap.Modal(document.getElementById('editModal'), {});
            myModal.show();
        }

        function filterTable() {
            // Get filter values
            const precinctFilter = document.getElementById('precinctFilter').value.toUpperCase();
            const nameFilter = document.getElementById('nameFilter').value.toUpperCase();
            const purokFilter = document.getElementById('purokFilter').value.toUpperCase();
            const voteFilter = document.getElementById('voteFilter').value.toUpperCase();
            
            // Get table rows
            const table = document.getElementById('votersTable');
            const rows = table.getElementsByTagName('tr');
            
            // Loop through all table rows (skip the header row)
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                const precinctNo = cells[1].textContent.toUpperCase();
                const voterName = cells[2].textContent.toUpperCase();
                const purok = cells[3].textContent.toUpperCase();
                const vote = cells[4].textContent.toUpperCase();
                
                // Check if row matches filters
                const precinctMatch = precinctFilter === '' || precinctNo.includes(precinctFilter);
                const nameMatch = nameFilter === '' || voterName.includes(nameFilter);
                const purokMatch = purokFilter === '' || purok.includes(purokFilter);
                const voteMatch = voteFilter === '' || vote.includes(voteFilter);
                
                // Show/hide row based on matches
                if (precinctMatch && nameMatch && purokMatch && voteMatch) {
                    rows[i].style.display = '';  // Show row
                } else {
                    rows[i].style.display = 'none';  // Hide row
                }
            }
        }
        // Handle the form submission via AJAX
    document.getElementById('editForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent normal form submission

        var formData = new FormData(this); // Form data

        // Use fetch to send data via AJAX
        fetch('edit_voter.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Assuming a JSON response
        .then(data => {
            if (data.success) {
                // Find the row and update it
                var row = document.getElementById('voter-row-' + data.id);
                row.cells[1].textContent = data.precinct_no;
                row.cells[2].textContent = data.voters_name;
                row.cells[3].textContent = data.purok;
                row.cells[4].textContent = data.vote;

                // Close the modal
                var myModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                myModal.hide();
            } else {
                alert('Error updating data');
            }
        })
        .catch(error => console.error('Error:', error));
    });
    </script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
