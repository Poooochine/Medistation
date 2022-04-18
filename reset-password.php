<?php 
    date_default_timezone_set('America/Jamaica');
    require_once 'conn.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $server = $_SERVER["HTTP_HOST"];
        $email = $_POST["email"];
        $token = bin2hex(random_bytes(8));
        $resetKey = bin2hex(random_bytes(8));
        $createdAt = time();
        $expiresAt = time() + 3600;
        $url = "http://$server/MediStation/create-new-password.php?token=$token&reset=$resetKey";

        $sql = "SELECT AccountID FROM account WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_object();

        if ($user){
            $sql = "INSERT INTO resetaccount (AccountID, token, resetKey, createdAt, expiresAt) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $user->AccountID, $token, $resetKey, $createdAt, $expiresAt);
            $val = $stmt->execute();
            if ($val) {
                $subject = "Reset Password";
                $message = "<p>Hello User,</p>";
                $message .= "<p>We have received a request to reset your password.</p>";
                $message .= "<p>Below you will find the link to reset your password.</p>";
                $message .= "<p>If you did not make this request ignore this email.</p>";
                $message .= "<a href='$url'>$url</a>";
                $headers = "From: MediStation <donotreply@localhost>\r\n";
                $headers .= "Content-type: text/html\r\n";
                mail($email, $subject, $message, $headers);
                header("Location: reset-password.php?reset=success");
            } else {
               header("Location: reset-password.php?reset=failed"); 
            }
        } else {
            header("Location: reset-password.php?reset=user-not-found");
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'includes/header.html'?>
    <title>Reset Password</title>

</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-[#90a7c1] to-white/60">
    <?php if(isset($_GET['reset'])) {
        ?>
    <div class="absolute top-5 right-5" id="popup">
        <div class="bg-white text-black font-medium px-4 py-3 overflow-hidden rounded relative w-[25rem]">
            <?php if ($_GET['reset'] == "success") {
                echo '<p class="text-green-600">SUCCESS!</p>';
                echo '<p>Check your email for the link to rest you password.</p>';
            } else if ($_GET['reset'] == "failed") {
                echo '<p class="text-red-600">Failed!</p>';
                echo '<p>Something went wrong. Please try again later.</p>';
            } else {
                echo '<p class="text-red-600">Failed!</p>';
                echo '<p>User not found!</p>';
            } ?>
            <button class="absolute top-0 right-2 text-2xl hover:text-red-400" id="close-popup">
                &times;
            </button>
            <div class="absolute top-0 left-0 h-full w-1.5 bg-blue-600"></div>
        </div>
    </div>
    <?php
    } ?>
    <div class="flex flex-1 flex-col items-center justify-center">
        <div class="grow flex items-center justify-center w-full mx-auto">
            <div class="bg-gray-100 shadow-xl p-4 rounded max-w-xl w-full space-y-4 mx-auto">
                <h1 class="text-3xl font-medium text-center">Reset Password</h1>
                <p>Enter your email to receive a link to reset your password.</p>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" class="space-y-4">
                    <div
                        class="flex gap-1 bg-white px-2 py-1 shadow-md rounded ring-1 ring-slate-600 hover:ring-sky-500 focus-within:ring-sky-500">
                        <span class="material-icons text-slate-700">
                            mail
                        </span>
                        <input class="appearance-none outline-none grow" type="email" name="email"
                            placeholder="janedoe@mail.com" />
                    </div>
                    <div class="flex justify-between items-center">
                        <button class="bg-slate-700 text-white rounded px-4 py-1 hover:scale-105">Reset
                            Password</button>
                        <a href="login.php" class="hover:text-sky-500 hover:underline">Back to login</a>
                    </div>
                </form>
            </div>
        </div>
        <?php include 'includes/footer.html'?>
    </div>

    <script>
    const popup = document.getElementById("popup");
    const close = document.getElementById("close-popup");
    close.onclick = () => {
        popup.classList.toggle("hidden");
    }
    setTimeout(() => {
        popup.classList.toggle("hidden");
    }, 5000);
    </script>
</body>

</html>
