<?php
session_start();
require_once 'db.php'; 
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['user_role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $sql = "SELECT id, name, password, role FROM users WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
    } else {
    
        $error = "Database query error: " . $conn->error;
        $user = false;
    }

   
    if ($user && $password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: admin_dashboard.php');
            exit;
        } else {
            header('Location: user_dashboard.php');
            exit;
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task System Login</title>
   <style>
    :root {
        --custom-blue: #a3ace0ff;
    }
    
    * {
        box-sizing: border-box;
    }
    
        body {
            height: 100vh;
            background: linear-gradient(135deg, #e0f7fa 0%, var(--custom-blue) 100%); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

    .container {
        width: 100%;
        padding: 15px;
    }

    .login-container {
        max-width: 400px;
        margin: 100px auto;
        padding: 30px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,.05);
    }

    h3, h5 {
        text-align: center;
        margin-bottom: 1rem;
    }

    h3 {
        font-weight: 600;
        color: #120585;
    }

    h5 {
        font-weight: 500;
        color: #120585;
    }

    form {
        margin-top: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 15px;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,.25);
    }

    .mb-3 {
        margin-bottom: 16px;
    }

    .btn {
        display: inline-block;
        width: 100%;
        padding: 10px 16px;
        font-size: 16px;
        font-weight: 500;
        text-align: center;
        color: #fff;
        background-color: var(--custom-blue); 
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #323d83; 
    }

    .alert {
        padding: 10px 15px;
        border-radius: 6px;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }

    .text-center {
        text-align: center;
    }

    .d-grid {
        display: grid;
    }

    .mt-3 {
        margin-top: 1rem;
    }
</style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h3>Dynamic Forms</h3>
            <h5>Login</h5>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn">Log In</button>
                </div>
            </form>
           
        </div>
    </div>
</body>
</html>
