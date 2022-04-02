<?php
    require_once 'renewSession.php';
    require_once 'conn.php';

    if($_COOKIE['accountType'] != 'System Admin'){
        header('Location: login.php');
    }

    if ($_SERVER['REQUEST_METHOD'] == "GET"){
        $sql = "SELECT FName, LName, image FROM sysadmin WHERE AccountID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_COOKIE['accountId']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_object();

        $name = $user->FName." ".$user->LName;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST["id"])){
            $id = $_POST["id"];
            $page = $_POST["page"];
            $isActive = $_POST["isActive"];
            $isActive = !$isActive;
 
            $sql = "UPDATE account SET isActive = ? WHERE AccountID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $isActive, $id);
            $val = $stmt->execute();
            if ($val){
                header("Location: admin.php?staff=$page&success=True.php");
            } else {
                header("Location: admin.php?staff=$page&success=False.php");
            }
        } else {
            function sendActivateEmail($AccountId, $email, $first_name, $last_name){
                $BD_HOST = 'localhost';
                $DB_USER = 'root';
                $DB_PASSWORD = '';
                $DB_NAME = 'medistation';
                $conn = mysqli_connect($BD_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);            
    
                $activator = bin2hex(random_bytes(8));
                $token = bin2hex(random_bytes(8));
                $server = $_SERVER["HTTP_HOST"];
                $url = "http://$server/MediStation/activate-account.php?activator=$activator&token=$token";
    
                $sql = "INSERT INTO activateaccount (AccountID, activator, token) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $AccountId, $activator, $token);
                $val = $stmt->execute();
                if($val){
                    $subject = "Activate Account";
                    $message = "<p>Hello $first_name $last_name,</p>";
                    $message .= "<p>Bellow you will find the link to activate your account.</p>";
                    $message .= "<a href='$url'>$url</a>";
                    $headers = "From: MediStation <donotreply@localhost>\r\n";
                    $headers .= "Content-type: text/html\r\n";
                    mail($email, $subject, $message, $headers);
                }
            }
    
            $accountType = $_POST["accountType"];
            $first_name = $_POST["firstName"];
            $last_name = $_POST["lastName"];
            $email = $_POST["email"];
            $gender = $_POST["gender"];
            $phone = $_POST["phone"];
            if ($accountType == "Pharmacist"){
                $sql = "INSERT INTO account (AccountID, Email, Pword, AccType, isActive) VALUES (?, ?, ?, ?, ?)";
                $AccountId = uniqid("phr");
                $password = uniqid();
                $isActive = 0;
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $AccountId, $email, $hashedPassword, $accountType, $isActive);
                $val = $stmt->execute();
    
                if ($val){
                    $sql2 = "INSERT INTO pharmacist (pharmacistID, FName, LName, Gender, Phone, AccountID) VALUES (?, ?, ?, ?, ?, ?)";
                    $phyId = uniqid("phy");
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param("ssssss", $phyId, $first_name, $last_name, $gender, $phone, $AccountId);
                    $val2 = $stmt2->execute();
                    if (!$val2){
                        $sql3 = "DELETE FROM account WHERE AccountID = ?";
                        $stmt3 = $conn->prepare($sql3);
                        $stmt3->bind_param("s", $AccountId);
                        $stmt3->execute();
                    }
                    sendActivateEmail($AccountId, $email, $first_name, $last_name);
                }
                
            } else if ($accountType == "Physician") {
                $sql = "INSERT INTO account (AccountID, Email, Pword, AccType, isActive) VALUES (?, ?, ?, ?, ?)";
                $AccountId = uniqid("phy");
                $password = uniqid();
                $isActive = 0;
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $AccountId, $email, $hashedPassword, $accountType, $isActive);
                $val = $stmt->execute();
    
                if ($val){
                    $sql = "INSERT INTO physician (physicianID, FName, LName, Gender, Phone, Speciality, AccountID) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $phyId = uniqid("phy");
                    $specialty = $_POST["specialty"];
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssss", $phyId, $first_name, $last_name, $gender, $phone, $specialty, $AccountId);
                    $val = $stmt->execute();
                    if ($val){
                        $sql = "INSERT INTO physicianprofile (physicianID) VALUES (?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $phyId);
                        $val = $stmt->execute();
                        if (!$val){
                            $sql = "DELETE FROM account WHERE AccountID = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("s", $AccountId);
                            $stmt->execute();
                        } else {
                           sendActivateEmail($AccountId, $email, $first_name, $last_name); 
                        }
                    } else {
                        $sql = "DELETE FROM account WHERE AccountID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $AccountId);
                        $stmt->execute();
                    }
                }
            } else {
                $sql = "INSERT INTO account (AccountID, Email, Pword, AccType, isActive) VALUES (?, ?, ?, ?, ?)";
                $AccountId = uniqid("sys");
                $password = uniqid();
                $isActive = 0;
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $AccountId, $email, $hashedPassword, $accountType, $isActive);
                $val = $stmt->execute();
    
                if ($val){
                    $sql2 = "INSERT INTO sysadmin (sysAdminID, FName, LName, AccountID, Gender, Phone) VALUES (?, ?, ?, ?, ?, ?)";
                    $phyId = uniqid("phy");
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param("ssssss", $phyId, $first_name, $last_name, $AccountId, $gender, $phone);
                    $val2 = $stmt2->execute();
                    if (!$val2){
                        $sql3 = "DELETE FROM account WHERE AccountID = ?";
                        $stmt3 = $conn->prepare($sql3);
                        $stmt3->bind_param("s", $AccountId);
                        $stmt3->execute();
                    }
                    sendActivateEmail($AccountId, $email, $first_name, $last_name);
                }
            }
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET"){
        $data = array();
        if (isset($_GET['staff'])){
            $accountType = $_GET["staff"];
        } else {
            $accountType = "Pharmacist";
        }
        $results_per_page = 10;
        if (!isset($_GET["page"])){
            $page = 1;
        } else {
            $page = $_GET["page"];
        }
        
        $sql = "SELECT $accountType.FName, $accountType.LName, account.Email, account.isActive FROM $accountType INNER JOIN account on $accountType.AccountID=account.AccountID";
        $initialResults = $conn->query($sql);
        $count = $initialResults->num_rows;
        $number_of_pages = ceil($count/$results_per_page);
        $this_page_first_result = ($page - 1) * $results_per_page;

        $sql1 = "SELECT $accountType.FName, $accountType.LName, account.Email, account.isActive, account.AccountID FROM $accountType INNER JOIN account on $accountType.AccountID=account.AccountID LIMIT $this_page_first_result , $results_per_page";
        $result = $conn->query($sql1);
        if ($result){
            $data = $result->fetch_all(MYSQLI_ASSOC);
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
    <script src="js/systemAdmin.js" defer></script>
    <title>Admin - MediStation</title>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-[#90a7c1] to-slate-600">
    <div class="absolute w-full h-full bg-black/50 items-center justify-center hidden" id="modal">
        <div class="max-w-[50%] w-full bg-gray-200 shadow-lg rounded-md p-3 flex flex-col">
            <h2 class="font-semibold text-lg text-center">Add New Staff</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" class=" flex flex-col px-20
                space-y-2">
                <div class="flex flex-col justify-center">
                    <label for="accountType">Account Type</label>
                    <select class="grow ring-1 ring-black" id="accountType" name="accountType">
                        <option value="Pharmacist">Pharmacist</option>
                        <option value="Physician">Physician</option>
                        <option value="System Admin">System Admin</option>
                    </select>
                </div>
                <div class="flex flex-col space-y-2" id="accountContainer">
                    <div class="flex flex-col justify-center">
                        <label for="firstName">First Name</label>
                        <div class="flex bg-white px-2 py-1 shadow-lg rounded space-x-2">
                            <span class="material-icons text-gray-500">
                                badge
                            </span>
                            <input class="outline-none grow" id="firstName" name="firstName" required />
                        </div>
                    </div>
                    <div class="flex flex-col justify-center">
                        <label for="lastName">Last Name</label>
                        <div class="flex bg-white px-2 py-1 shadow-lg rounded space-x-2">
                            <span class="material-icons text-gray-500">
                                badge
                            </span>
                            <input class="outline-none grow" id="lastName" name="lastName" required />
                        </div>
                    </div>
                    <div class="flex flex-col justify-center">
                        <label for="email">Email</label>
                        <div class="flex bg-white px-2 py-1 shadow-lg rounded space-x-2">
                            <span class="material-icons text-gray-500">
                                mail
                            </span>
                            <input class="outline-none grow" id="email" name="email" type="email" required />
                        </div>
                    </div>
                    <div class="flex flex-col justify-center">
                        <label for="gender">Gender</label>
                        <select class="grow ring-1 ring-black" id="gender" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="flex flex-col justify-center">
                        <label for="phone">Phone</label>
                        <div class="flex bg-white px-2 py-1 shadow-lg rounded space-x-2">
                            <span class="material-icons text-gray-500">
                                phone
                            </span>
                            <input class="outline-none grow" id="phone" name="phone" type="text" required />
                        </div>
                    </div>
                </div>
                <div class="w-full flex items-center justify-center">
                    <div class="flex items-center space-x-4 my-2">
                        <button
                            class="px-4 py-2 bg-red-600 text-white rounded hover:scale-105 hover:shadow-lg hover:shadow-red-600/30"
                            type="button" id="cancel">Cancel</button>
                        <button
                            class="px-4 py-2 bg-emerald-600 text-white rounded hover:scale-105 hover:shadow-lg hover:shadow-emerald-600/30"
                            type="submit">Add
                            Staff</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="flex flex-1 w-full h-full">
        <!-- Sidebar -->
        <?php include 'includes/admin-sidebar.php' ?>
        <!-- Table -->
        <div class="flex flex-col grow">
            <div class="bg-white p-3 max-w-[90%] lg:max-w-[85%] w-full mx-auto my-auto rounded-md">
                <h2 class="text-center font-semibold text-3xl">Account Management</h2>
                <div class="flex w-full justify-between items-center">
                    <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" id="filter">
                        <select class="" id="staff" name="staff">
                            <option value="Client">Client</option>
                            <option value="Pharmacist">Pharmacist</option>
                            <option value="Physician">Physician</option>
                            <option value="sysadmin">System Admin</option>
                        </select>
                    </form>
                    <button
                        class="bg-slate-600 text-white px-4 py-2 rounded hover:scale-105 hover:shadow-lg hover:shadow-slate-600/30"
                        id="openModal">Add
                        Staff</button>
                </div>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                    <table class="border-collapse w-full border border-slate-500 my-2">
                        <thead>
                            <tr class="text-left bg-slate-800 text-white text-xl font-semibold">
                                <th class="border border-slate-600">First Name</th>
                                <th class="border border-slate-600">Last Name</th>
                                <th class="border border-slate-600">Email</th>
                                <th class="border border-slate-600">Is Active</th>
                                <th class="border border-slate-600">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                    foreach ($data as $row): ?>
                            <tr class="bg-slate-500 text-white hover:bg-slate-600 border border-slate-400">
                                <td><?= $row['FName'] ?></td>
                                <td><?= $row['LName'] ?></td>
                                <td><?= $row['Email'] ?></td>
                                <?php if($row['isActive'] == 1) { 
                                    echo '
                                    <td class="py-1 my-2">
                                        <span class="material-icons text-green-400">
                                            check_circle
                                        </span>
                                    </td>';
                                } else {
                                    echo '
                                    <td class="py-1 my-2">
                                        <span class="material-icons text-red-400">
                                            cancel
                                        </span>
                                    </td>';
                                } ?>
                                <td class="py-1 my-2">
                                    <?php
                                        if ($row['isActive']) {
                                            ?>
                                    <button class="bg-red-500 text-white px-4 py-1 rounded" type="submit"
                                        name="deactivate">Deactivate</button>
                                    <?php
                                        } else {
                                            ?>
                                    <button class="bg-green-500 text-white px-4 py-1 rounded" type="submit"
                                        name="deactivate">Activate</button>
                                    <?php
                                        }
                                    ?>

                                </td>
                            </tr>
                            <input type="hidden" name="id" value="<?php echo $row['AccountID']?>" />
                            <input type="hidden" name="page" value="<?php echo $_GET['staff']?>" />
                            <input type="hidden" name="isActive" value="<?php echo $row['isActive']?>" />
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </form>
                <div class="w-full flex items-center justify-end">
                    <?php
                        if ($number_of_pages > 0){
                            ?>
                    <div class="flex items-center space-x-2 bg-slate-900/70 text-white rounded px-2 py-1">
                        <a class="flex items-center" href="admin.php?staff=<?php echo $accountType ?>&page=<?php 
                        if ($page == 1) {
                            echo $page;
                        } else {
                            echo $page - 1;
                        }
                        ?>">
                            <span class="material-icons cursor-pointer">
                                arrow_back_ios
                            </span>
                        </a>
                        <div class="flex items-center space-x-1">
                            <?php
                                for($p=1; $p<=$number_of_pages; $p++){
                                    if ($p == $page){
                                        echo "
                                            <a class='border border-1 border-sky-600 px-2 py-0.5 rounded text-white cursor-not-allowed'
                                href='admin.php?staff=$accountType&page=$p'>$p</a>
                            ";
                            } else {
                            echo "
                            <a class='bg-slate-800/80 px-2 py-0.5 rounded text-white'
                                href='admin.php?staff=$accountType&page=$p'>$p</a>
                            ";
                            }
                            ?>
                            <?php
                                }
                            ?>
                        </div>
                        <a class="flex items-center" href="admin.php?staff=<?php echo $accountType ?>&page=<?php 
                        if ($page == $number_of_pages) {
                            echo $page;
                        } else {
                            echo $page + 1;
                        }
                        ?>">
                            <span class="material-icons">
                                arrow_forward_ios
                            </span>
                        </a>
                    </div>
                    <?PHP
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>