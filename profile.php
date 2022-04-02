<?php
    require_once 'renewSession.php';
    require_once 'conn.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST["delete-img"])){
            $sql = "SELECT image FROM sysadmin WHERE AccountID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $_COOKIE['accountId']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_object();
            if (unlink($user->image)){
                $image = "https://www.pngitem.com/pimgs/m/146-1468479_my-profile-icon-blank-profile-picture-circle-hd.png";
                $sql = "UPDATE sysadmin SET image = ? WHERE AccountID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $image, $_COOKIE['accountId']);
                $val = $stmt->execute();
                if ($val){
                    header("Location: profile.php?delete=True");
                } else {
                    header("Location: profile.php?delete=False");
                }
            } else {
                header("Location: profile.php?delete=False");
            }
        } else if (isset($_POST['upload-image'])) {
            $targetDir = "uploads/";
            $temp = explode(".", basename($_FILES["new-image"]["name"]));
            $newFileName = round(microtime(true)) . '.' . end($temp);
            $targetFile = $targetDir . $newFileName;
            if (move_uploaded_file($_FILES["new-image"]["tmp_name"], $targetFile)) {
                $sql = "UPDATE sysadmin SET image = ? WHERE AccountID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $targetFile, $_COOKIE['accountId']);
                $val = $stmt->execute();
                if ($val){
                    header('Location: profile.php?upload=True');
                } else {
                    header('Location: profile.php?upload=False');
                }
            }
        } else {
            $cur_password = $_POST["cur_password"];
            $new_password = $_POST["new_password"];
            $con_password = $_POST["con_password"];
            if (strlen($cur_password) > 0){ 
                $uppercase = preg_match('@[A-Z]@', $new_password);
                $lowercase = preg_match('@[a-z]@', $new_password);
                $number = preg_match('@[0-9]@', $new_password);
                $specialChars = preg_match('@[^\w]@', $new_password);
                if ($new_password != $con_password){
                    header("Location: profile.php?password=notMatched");
                } else {
                    if (strlen($new_password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars){
                        header("Location: edit-profile.php?password=invalid");
                    } else {
                        $sql = "SELECT Pword FROM account WHERE AccountID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $_COOKIE['accountId']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $user = $result->fetch_object();
                        $cur_password_db = $user->Pword;
            
                        if (password_verify($cur_password, $cur_password_db)){
                            $sql = "UPDATE account SET Pword = ? WHERE AccountID = ?";
                            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ss", $hashedPassword, $_COOKIE['accountId']);
                            $val = $stmt->execute();
                            if ($val){
                            header("Location: profile.php?success=true");  
                            } else {
                            header("Location: profile.php?success=fail");   
                            }
                        } else {
                            header("Location: profile.php?password=false");
                        }
                    }
                }
            } else {
                header("Location: profile.php");
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
    <script src="js/physician-profile.js" defer></script>
    <title>Document</title>

</head>

<body class="min-h-screen bg-gradient-to-br from-[#90a7c1] to-slate-600">
    <div class="flex flex-1 w-full h-full">
        <?php include 'includes/admin-sidebar.php'; ?>
        <div class="flex flex-col grow">
            <div class="grow">
                <div class="flex gap-6 p-4">
                    <div class="w-[30%]">
                        <div class="flex flex-col items-center space-y-3 px-3 py-6 bg-white rounded">
                            <h3 class="text-center font-semibold text-2xl"><?php echo $name ?></h3>
                            <div class="relative w-[8rem] h-[8rem]">
                                <img src="<?php echo $user->image ?>" alt="profile"
                                    class="w-full h-full rounded-full aspect-square object-cover" id="image-area" />
                                <?php
                                        if ($user->image == "https://www.pngitem.com/pimgs/m/146-1468479_my-profile-icon-blank-profile-picture-circle-hd.png"){
                                            ?>
                                <button id="add-img"
                                    class="absolute top-2 right-0 flex items-center rounded-full p-0.5 bg-gray-200 cursor-pointer">
                                    <span class="material-icons text-gray-700">
                                        add
                                    </span>
                                </button>
                                <?php
                                        } else {
                                            ?>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                                    <input type="hidden" name="id" value="<?php echo $_COOKIE['accountId'] ?>" />
                                    <button name="delete-img"
                                        class="absolute top-2 right-0 flex items-center rounded-full p-0.5 bg-gray-200 cursor-pointer">
                                        <span class="material-icons text-gray-700">
                                            delete
                                        </span>
                                    </button>
                                </form>
                                <?php
                                        }
                                    ?>

                            </div>
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>"
                                enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $_COOKIE['accountId'] ?>" />
                                <input type="file" class="hidden" name="new-image" id="new-image" />
                                <button id="upload-image" type="submit" name="upload-image"
                                    class="bg-slate-700 text-white px-6 py-2 rounded disabled:cursor-not-allowed disabled:bg-slate-400/40">Upload
                                    New
                                    Photo</button>
                            </form>
                            <div
                                class="bg-slate-200/50 rounded ring-1 ring-slate-500 text-gray-800 flex flex-col space-y-2 items-center justify-center px-2 py-4">
                                <p class="text-center">Upload a new avatar. Larger image will be resized automatically
                                </p>
                                <p>Maximum upload size is <b>5 MB</b></p>
                            </div>
                            <p>Member Since: <b>01 Jan 2022</b></p>
                        </div>
                    </div>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" class="w-[70%]">
                        <input type="hidden" name="id" value="<?php echo $_COOKIE['accountId'] ?>" />
                        <div class="bg-gray-50 rounded py-6 px-4 space-y-4">
                            <h1 class="text-3xl font-semibold">Edit Profile</h1>
                            <div class="grid grid-cols-2 gap-4 font-medium">
                                <div class="flex flex-col space-y-2">
                                    <label for="name">Full Name</label>
                                    <input id="name" class="bg-white px-4 py-2 outline-none ring-[1.3px] 
                                        ring-slate-500 rounded disabled:bg-gray-200 disabled:hover:ring-slate-500 
                                        font-medium focus-within:ring-sky-500 hover:ring-sky-500"
                                        value="<?php echo $name ?>" disabled />
                                </div>
                                <div class="flex flex-col space-y-2">
                                    <label for="email">Email</label>
                                    <input id="email" class="bg-white px-4 py-2 outline-none ring-[1.3px] 
                                        ring-slate-500 rounded disabled:bg-gray-200 disabled:hover:ring-slate-500 
                                        font-medium focus-within:ring-sky-500 hover:ring-sky-500"
                                        value="<?php echo $user->Email ?>" disabled />
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold">Change Password</h3>
                            <div class="grid grid-cols-2 gap-3 font-semibold">
                                <div class="flex flex-col space-y-2">
                                    <label for="cur_password">Current Password</label>
                                    <input id="cur_password" name="cur_password" class="bg-white px-4 py-2 outline-none ring-[1.3px] 
                                        ring-slate-500 rounded disabled:bg-gray-200 disabled:hover:ring-slate-500 
                                        font-medium focus-within:ring-sky-500 hover:ring-sky-500" type="password" />
                                </div>
                                <div class="flex flex-col space-y-2">
                                    <label for="new_password">New Password</label>
                                    <input id="new_password" name="new_password" class="bg-white px-4 py-2 outline-none ring-[1.3px] 
                                        ring-slate-500 rounded disabled:bg-gray-200 disabled:hover:ring-slate-500 
                                        font-medium focus-within:ring-sky-500 hover:ring-sky-500" type="password" />
                                </div>
                                <div class="flex flex-col space-y-2">
                                    <label for="con_password">Confirm Password</label>
                                    <input id="con_password" name="con_password" class="bg-white px-4 py-2 outline-none ring-[1.3px] 
                                        ring-slate-500 rounded disabled:bg-gray-200 disabled:hover:ring-slate-500 
                                        font-medium focus-within:ring-sky-500 hover:ring-sky-500" type="password" />
                                </div>
                            </div>
                            <button type="submit" name="edit-profile"
                                class="bg-slate-700 text-white px-6 py-2 rounded">Update Info</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php include 'includes/footer.html'?>
        </div>
    </div>
</body>

</html>