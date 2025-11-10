<?php
require_once 'db.php'; 

$sql = "SELECT f.id, f.form_name, COUNT(r.id) AS submission_count 
        FROM forms f
        LEFT JOIN form_responses r ON f.id = r.form_id
        GROUP BY f.id, f.form_name
        ORDER BY f.created_at DESC";

$result = $conn->query($sql);

$forms = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $forms[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin: View Form Responses</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #a3ace0ff }
        .form-table { width: 80%; border-collapse: collapse; margin-top: 20px; }
        .form-table th, .form-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .form-table th { background-color: #f2f2f2; }
        .action-link { font-weight: bold; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Admin Response Viewer</h1>
    <h2>Select a Form to View Submissions</h2>

    <?php if (!empty($forms)): ?>
        <table class="form-table">
            <thead>
                <tr>
                    <th>Form Name</th>
                    <th>Submissions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($forms as $form): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($form['form_name']); ?></td>
                        <td><?php echo $form['submission_count']; ?></td>
                        <td>
                            <a class="action-link" href="admin_submission_history.php?form_id=<?php echo $form['id']; ?>">
                                View History (<?php echo $form['submission_count']; ?>)
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No forms have been created yet. No responses to display.</p>
    <?php endif; ?>
</body>
</html>