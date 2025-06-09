<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Restaurant Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/sidebar.css">
    
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="login-container">
            <h2 class="login-title">Login</h2>
            <form class="login-form" action="login_process.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>
