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
    if (isset($_POST['login_btn'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (!empty($email) && !empty($password)) {
            try {
                // البحث عن المستخدم بواسطة الإيميل
                $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
                $stmt = $conn->prepare($query);
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // التحقق من وجود المستخدم ومطابقة الباسورد المتشفر
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name'];

                    // التوجيه لصفحة الـ index عند النجاح
                    header("Location: index.php");
                    exit();
                } else {
                    $message = "Invalid email or password!";
                    $message_class = "alert-danger";
                }
            } catch(PDOException $e) {
                $message = "Error: " . $e->getMessage();
                $message_class = "alert-danger";
            }
        } else {
            $message = "Please fill in all fields!";
            $message_class = "alert-danger";
        }
    }
}

include "includes/header.php";
?>
<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>

                                    <!-- عرض الرسائل (خطأ أو نجاح) -->
                                    <?php if (!empty($message)): ?>
                                        <div class="alert <?php echo $message_class; ?> text-center small" role="alert">
                                            <?php echo $message; ?>
                                        </div>
                                    <?php endif; ?>

                                    <form class="user" action="login.php" method="POST">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address..." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        
                                        <!-- زرار اللوجن -->
                                        <button type="submit" name="login_btn" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                        
                                        <!-- زراير السوشيال ميديا -->
                                        <hr>
                                        <a href="#" class="btn btn-google btn-user btn-block">
                                            <i class="fab fa-google fa-fw"></i> Login with Google
                                        </a>
                                        <a href="#" class="btn btn-facebook btn-user btn-block">
                                            <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                                        </a>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.php">Forgot Password?</a>
                                        </div>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

<?php
include "includes/footer1.php";
include "includes/logmodel.php";
?>

</body>
</html>