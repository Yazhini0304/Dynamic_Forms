<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Dynamic Form Builder</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 40px; 
            background-color: #a3ace0ff;
        }
        .dashboard-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            position: relative; /* Needed for positioning the logout button */
        }
        .top-bar {
            display: flex;
            justify-content: flex-end; /* Push logout button to the right */
            margin-bottom: -15px; /* Adjust spacing */
        }
        .logout-button {
            text-decoration: none;
            color: white;
            background-color: #dc3545; /* Red */
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .logout-button:hover {
            background-color: #c82333;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        p.subtitle {
             text-align: center; 
             color: #555;
             margin-bottom: 20px;
        }
        .action-grid {
            display: flex;
            justify-content: space-around;
            gap: 20px;
        }
        .action-button {
            flex: 1;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: white;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 6px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .action-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .create-form {
            background-color: #4CAF50; /* Green */
        }
        .view-responses {
            background-color: #007bff; /* Blue */
        }
        .user-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            text-decoration: none;
        }
        .user-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        
        <div class="top-bar">
            <a href="logout.php" class="logout-button">
                Logout
            </a>
        </div>
        
        <h1>Admin Dashboard</h1>
        <p class="subtitle">Manage the dynamic forms and view user submissions.</p>
        
        <hr>

        <div class="action-grid">
            <a href="admin_build_form.html" class="action-button create-form">
             Create New Form 
            </a>

            <a href="admin_responses_list.php" class="action-button view-responses">
                View Submissions
            </a>
        </div>

       
        
       
    </div>
</body>
</html>