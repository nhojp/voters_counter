<?php
include "conn.php";

// Check if the request is an AJAX POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from POST request
    $id = $_POST['id'];
    $precinct_no = $_POST['precinct_no'];
    $voters_name = $_POST['voters_name'];
    $purok = $_POST['purok'];
    $vote = $_POST['vote'];

    // Update query
    $sql = "UPDATE voters SET precinct_no = ?, voters_name = ?, purok = ?, vote = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $precinct_no, $voters_name, $purok, $vote, $id);

    if ($stmt->execute()) {
        // Return a JSON response with the updated data
        echo json_encode([
            'success' => true,
            'id' => $id,
            'precinct_no' => $precinct_no,
            'voters_name' => $voters_name,
            'purok' => $purok,
            'vote' => $vote
        ]);
    } else {
        // Return error if update fails
        echo json_encode(['success' => false]);
    }
    
    $stmt->close();
    $conn->close();
}
?>