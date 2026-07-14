<?php
session_start();

$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "sbadmin2";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = "";
$message_class = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_btn'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $repeat_password = trim($_POST['repeat_password']);
        
        if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($password)) {
            if ($password !== $repeat_password) {
                $message = "Passwords do not match!";
                $message_class = "alert-danger";
            } else {
                try {
                    // التحقق من أن الإيميل غير مكرر
                    $check_query = "SELECT id FROM users WHERE email = :email LIMIT 1";
                    $stmt = $conn->prepare($check_query);
                    $stmt->execute(['email' => $email]);
                    
                    if ($stmt->rowCount() > 0) {
                        $message = "Email is already registered!";
                        $message_class = "alert-danger";
                    } else {
                        // تشفير الباسورد للحماية
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        // إدخال البيانات في الجدول الجديد
                        $insert_query = "INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)";
                        $insert_stmt = $conn->prepare($insert_query);
                        $insert_stmt->execute([
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'email' => $email,
                            'password' => $hashed_password
                        ]);
                        
                        $message = "Account created successfully! You can login now.";
                        $message_class = "alert-success";
                    }
                } catch(PDOException $e) {
                    $message = "Error: " . $e->getMessage();
                    $message_class = "alert-danger";
                }
            }
        } else {
            $message = "Please fill in all fields!";
            $message_class = "alert-danger";
        }
    }
}

// استدعاء الهيدر
include "includes/header.php";

include"includes/header.php";


?>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                       <div class="p-5">
    <div class="text-center">
        <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message_class; ?> text-center small" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form class="user" action="register.php" method="POST">
        <div class="form-group row">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <input type="text" name="first_name" class="form-control form-control-user" id="exampleFirstName" placeholder="First Name" required>
            </div>
            <div class="col-sm-6">
                <input type="text" name="last_name" class="form-control form-control-user" id="exampleLastName" placeholder="Last Name" required>
            </div>
        </div>
        
        <div class="form-group">
            <input type="email" name="email" class="form-control form-control-user" id="exampleInputEmail" placeholder="Email Address" required>
        </div>
        
        <div class="form-group row">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <input type="password" name="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password" required>
            </div>
            <div class="col-sm-6">
                <input type="password" name="repeat_password" class="form-control form-control-user" id="exampleRepeatPassword" placeholder="Repeat Password" required>
            </div>
        </div>
        
        <button type="submit" name="register_btn" class="btn btn-primary btn-user btn-block">
            Register Account
        </button>
        <hr>
    </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="forgot-password.php">Forgot Password?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="login.php">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

<?php
include"includes/footer1.php";
include"includes/logmodel.php";
include"includes/nav.php";
?>
</body>

</html>