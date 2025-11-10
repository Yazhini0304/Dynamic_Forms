<?php
require_once 'db.php';
$count_sql = "SELECT COUNT(id) AS form_count FROM forms";
$count_result = $conn->query($count_sql);
$form_count = 0;
if ($count_result) {
    $form_count = $count_result->fetch_assoc()['form_count'];
}
$conn->close();
$is_logged_in = false;
$user_name = "Guest";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard | Forms</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 40px; 
            background-color: #f0f8ff;
        }
        .dashboard-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #1e90ff; /* Dodger Blue */
            margin-bottom: 5px;
        }
        p.welcome {
            color: #666;
            margin-bottom: 30px;
        }
        .action-grid {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .action-button {
            width: 45%;
            padding: 30px 15px;
            text-decoration: none;
            color: white;
            font-size: 1.2em;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .start-forms {
            background-color: #007bff; /* Blue */
        }
        .view-history {
            background-color: #28a745; /* Green */
        }
        .admin-link {
            display: block;
            margin-top: 30px;
            color: #dc3545;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="top-bar">
            <a href="logout.php" class="logout-button">
                Logout
            </a>
        </div>
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p class="welcome">Here you can access all available forms and view your submission history.</p>
        
        <hr>

        <div class="action-grid">
            <a href="forms_list.php" class="action-button start-forms">
                <i class="fas fa-list"></i> Start a Form (<?php echo $form_count; ?> Available)
            </a>
        </div>
        
    
</body>
</html>