<?php

require_once 'db.php'; 
$sql = "SELECT id, form_name, created_at FROM forms ORDER BY created_at DESC";
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
    <title>Available Forms</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #a3ace0ff}
        .form-list { list-style: none; padding: 0; }
        .form-list li {
            margin-bottom: 10px;
            padding: 15px;
            border: 1px solid #007bff;
            border-left: 5px solid #007bff;
            border-radius: 4px;
            background-color: #e9f5ff;
        }
        .form-list li a {
            font-size: 1.2em;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .meta { 
            font-size: 0.9em; 
            color: #6c757d; 
            display: block;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>📝 Available Dynamic Forms</h1>

    <?php if (!empty($forms)): ?>
        <ul class="form-list">
            <?php foreach ($forms as $form): ?>
                <li>
                    <a href="form.php?form_id=<?php echo htmlspecialchars($form['id']); ?>">
                        <?php echo htmlspecialchars($form['form_name']); ?>
                    </a>
                    <span class="meta">Created on: <?php echo date("Y-m-d", strtotime($form['created_at'])); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No dynamic forms are available at this time.</p>
    <?php endif; ?>
    
    <hr>


</body>
</html>