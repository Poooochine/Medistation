<?php 
    session_start();
    require_once 'conn.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $activator = $_POST['activator'];
        $token = $_POST['token'];
        if ($activator && $token){
            $sql = "SELECT AccountID FROM activateaccount WHERE activator = ? AND token = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $activator, $token);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_object();
            $AccountID = $data->AccountID;

            $password = $_POST['password'];
            $con_password = $_POST['con_password'];

            if ($password == $con_password){
                $uppercase = preg_match('@[A-Z]@', $password);
                $lowercase = preg_match('@[a-z]@', $password);
                $number = preg_match('@[0-9]@', $password);
                $specialChars = preg_match('@[^\w]@', $password);

                if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars) {
                    $password_err = "Password must have at least 8 characters, a number, a lowercase letter, a uppercase letter and a special character.";
                    header ("Location: activate-account.php?activator=$activator&token=$token&status=weak");
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $isActive = 1;
                    $sql2 = "UPDATE account SET Pword = ?, isActive = ? WHERE AccountID = ?";
                    $stmt = $conn->prepare($sql2);
                    $stmt->bind_param("sss", $hashedPassword, $isActive, $AccountID);
                    $val = $stmt->execute();

                    if ($val){
                        $sql3 = "DELETE FROM activateaccount WHERE AccountID = ?";
                        $stmt = $conn->prepare($sql3);
                        $stmt->bind_param("s", $AccountID);
                        $stmt->execute();
                        header ('Location: login.php');
                    } else {
                        header ("Location: activate-account.php?activator=$activator&token=$token");
                    }
                }
            } else {
                header ("Location: activate-account.php?activator=$activator&token=$token&status=notmatch");
            }
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
    <title>Activate Account - MediStation</title>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-blue-500 to-emerald-700">
    <div class="bg-gray-100 p-3 max-w-[90%] md:max-w-[40%] w-full mx-auto h-full my-auto rounded-md">
        <h3 class="text-center font-semibold text-xl">Activate Account</h3>
        <form class="flex flex-col space-y-4" method="POST"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <div class="flex flex-col justify-center">
                <label for="password">Password</label>
                <div
                    class="flex bg-white px-2 py-1 shadow-lg rounded space-x-2 focus-within:border focus-within:border-1 focus-within:border-sky-500">
                    <input class="outline-none grow" id="password" name="password" type="password" required />
                </div>
            </div>
            <div class="flex flex-col justify-center">
                <label for="con_password">Confirm Password</label>
                <div
                    class="flex bg-white px-2 py-1 shadow-lg rounded space-x-2 focus-within:border focus-within:border-1 focus-within:border-sky-500">
                    <input class="outline-none grow" id="con_password" name="con_password" type="password" required />
                </div>
            </div>
            <?php
                if (isset($_GET["status"])){
                    if ($_GET["status"] == "weak"){
                        echo '<span class="text-red-500 text-base">Password must have at least 8 characters, a number, a lowercase letter, a uppercase letter and a special character.</span>';
                    } else {
                        echo '<span class="text-red-500 text-base">Password did not match.</span>';
                    }
                }
            ?>
            <div class="w-full flex items-center justify-center">
                <button
                    class="bg-slate-700 text-white px-6 py-2 rounded hover:shadow-xl hover:shadow-slate-700/40 hover:scale-105"
                    type="submit">
                    Activate
                </button>
            </div>
            <input type="hidden" value="<?php echo $_GET['activator'] ?>" name="activator" />
            <input type="hidden" value="<?php echo $_GET['token'] ?>" name="token" />
        </form>
    </div>

</body>

</html>