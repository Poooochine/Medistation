<?php 
    date_default_timezone_set('America/Jamaica');
    require_once 'conn.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $newPassword = $_POST["password"];
        $conPassword = $_POST["con-password"];
        $token = $_POST["token"];
        $resetKey = $_POST["reset-key"];

        $uppercase = preg_match('@[A-Z]@', $newPassword);
        $lowercase = preg_match('@[a-z]@', $newPassword);
        $number = preg_match('@[0-9]@', $newPassword);
        $specialChars = preg_match('@[^\w]@', $newPassword);

        $sql = "SELECT * FROM resetaccount WHERE token = ? AND resetKey = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $token, $resetKey);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_object();

        if ($data->expiresAt < time()) {
            header('Location: reset-password.php?token=expire');
        } else {
            if ($newPassword == $conPassword) {
                if (strlen($newPassword) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars){
                    header('Location: create-new-password.php?token='.$token.'&reset='.$resetKey.'&password=invalid');
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $sql = "UPDATE account SET Pword=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $hashedPassword);
                    $val = $stmt->execute();
                    if ($val){
                        $sql = "DELETE FROM resetaccount WHERE AccountID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $data->AccountID);
                        $val = $stmt->execute();
                        if ($val) {
                            header('Location: login.php?reset=success');
                        } else {
                            header('Location: create-new-password.php?token='.$token.'&reset='.$resetKey.'&reset=failed');
                        }
                    }
                }
            } else {
                header('Location: create-new-password.php?token='.$token.'&reset='.$resetKey.'&password=notMatched');
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
    <title>Document</title>
</head>

<body class="min-h-screen flex flex-col bg-[#cbd4e1] items-center justify-center">
    <div class="bg-gray-100 shadow-xl p-4 rounded max-w-xl w-full space-y-4">
        <h1 class="text-3xl font-medium text-center">Create New Password</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" class="space-y-4">
            <input type="hidden" name="token" value="<?php echo $_GET['token'] ?>" />
            <input type="hidden" name="reset-key" value="<?php echo $_GET['reset'] ?>" />
            <div class="flex flex-col space-y-2">
                <label for="new-password">New Password</label>
                <div
                    class="flex gap-1 bg-white px-2 py-1 shadow-md rounded ring-1 ring-slate-600 hover:ring-sky-500 focus-within:ring-sky-500">
                    <span class="material-icons text-slate-700">
                        lock
                    </span>
                    <input id="new-password" class="appearance-none outline-none grow" type="password" name="password"
                        placeholder="********" />
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="con-password">Confirm Password</label>
                <div
                    class="flex gap-1 bg-white px-2 py-1 shadow-md rounded ring-1 ring-slate-600 hover:ring-sky-500 focus-within:ring-sky-500">
                    <span class="material-icons text-slate-700">
                        lock
                    </span>
                    <input id="con-password" class="appearance-none outline-none grow" type="password"
                        name="con-password" placeholder="********" />
                </div>
            </div>
            <div class="flex justify-between items-center">
                <button class="bg-slate-700 text-white rounded px-4 py-1 hover:scale-105">Reset Password</button>
            </div>
        </form>
    </div>
</body>

</html>