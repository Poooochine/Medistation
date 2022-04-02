<?php

    session_start();

    if(isset($_COOKIE["auth"])){
        header("Location: login.php");
        exit;
    }

    require_once 'conn.php';

    $password_err = "";
    $first_name = "";
    $last_name = "";
    $email = "";
    $phone = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $password = $_POST["password"];

        $sql = "SELECT email FROM account WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);

        

        $stmt->execute();
        $result = $stmt->get_result();

        $user = $result->fetch_all(MYSQLI_ASSOC);

        if (!$user){
            $sql = "INSERT INTO account (AccountID, Email, Pword, AccType) VALUES (?, ?, ?, ?)";
            $AccountId = uniqid('cli');
            $AccType = "client";

            $uppercase = preg_match('@[A-Z]@', $password);
            $lowercase = preg_match('@[a-z]@', $password);
            $number = preg_match('@[0-9]@', $password);
            $specialChars = preg_match('@[^\w]@', $password);

            if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars) {
                $password_err = "Password must have at least 8 characters, a number, a lowercase letter, a uppercase letter and a special character.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $AccountId, $email, $hashedPassword, $AccType);
                $val = $stmt->execute();

                if ($val){
                $sql2 = "INSERT INTO client (clientID, FName, LName, Phone, AccountID, registrationStatus) VALUES (?,?,?,?,?,?)";
                $clientId = uniqid();
                $status = "Incomplete";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("ssssss", $clientId, $first_name, $last_name, $phone, $AccountId, $status);
                $val2 = $stmt2->execute();

                if (!$val2){
                    $sql3 = "DELETE FROM account WHERE AccountId = ?";
                    $stmt3 = $conn->prepare($sql3);
                    $stmt3->bind_param("s", $AccountId);
                    $stmt3->execute();
                } else {
                    header('Location: login.php');
                }

                }
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
    <script src="js/main.js" defer></script>
    <title>Registration - MediStation</title>
</head>

<body class="bg-gradient-to-br from-[#90a7c1] to-white/60">
    <div class="bg-black/40 absolute inset-0 min-h-screen overflow-y-auto hidden" id="reg-modal">
        <div class="fixed right-2 md:right-5 top-2 flex items-center bg-black/50 text-white cursor-pointer select-none"
            id="close-reg-modal">
            <span class="material-icons text-3xl">
                close
            </span>
        </div>
        <div class="max-w-4xl mx-auto bg-white p-3 shadow-lg md:my-2 rounded">
            <?php include 'includes/terms-condition.html' ?>
        </div>
    </div>
    <div class="min-h-screen flex flex-col">
        <nav class="items-center py-2 hidden md:flex bg-white justify-between">
            <a href="home.php" class="pl-4">
                <img src="./images/logo.png" class="h-[4rem]" />
            </a>
            <div class="flex items-center space-x-8 pr-2">
                <a class="font-semibold cursor-pointer hover:underline">Find a Psychiatrist</a>
                <a class="font-semibold cursor-pointer hover:underline">Our Story</a>
                <a class="font-semibold cursor-pointer hover:underline">Contact Us</a>
            </div>
        </nav>
        <!-- Mobile Nav -->
        <nav class="flex md:hidden bg-white justify-between items-center">
            <div>
                <button id="open">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            <div>
                <img src="./images/logo.png" class="'w-[3rem] h-[3rem]" />
            </div>
        </nav>
        <!-- Mobile Nav Open -->
        <div class="absolute h-full top-0 left-0 w-[80%] bg-white shadow space-y-4 z-50 hidden" id="menu-open">
            <div class="w-full flex justify-end px-2">
                <button id="close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <ul class="text-[#1c3344] space-y-2 px-2">
                <li class="font-semibold cursor-pointer hover:underline">
                    Find a Psychiatrist
                </li>
                <li class="font-semibold cursor-pointer hover:underline">
                    Our Story
                </li>
                <li class="font-semibold cursor-pointer hover:underline">
                    Contact Us
                </li>
            </ul>
        </div>
        <div class="flex grow my-1">
            <div class="w-[50%] flex-1 items-center justify-center hidden md:flex">
                <img src="./images/logo.png" class="" />
            </div>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>"
                class="w-[50%] flex flex-1 items-center justify-center">
                <div class="bg-gray-100 shadow-xl rounded-lg p-3 space-y-3 lg:w-[80%] w-[95%]">
                    <h1 class="font-semibold text-blue-800 text-3xl text-center">Registration</h1>
                    <div class="flex flex-col items-center space-y-2 px-2 lg:max-w-[80%] mx-auto">
                        <div
                            class="w-full shadow-md p-2 rounded-md flex space-x-2 bg-white focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500">
                            <span class="material-icons text-gray-500">
                                badge
                            </span>
                            <input class="w-full outline-none" placeholder="First Name" name="first_name" type="text"
                                required value="<?php echo $first_name ?>" />
                        </div>
                        <div
                            class="w-full shadow-md p-2 rounded-md flex space-x-2 bg-white focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500">
                            <span class="material-icons text-gray-500">
                                badge
                            </span>
                            <input class="w-full outline-none" placeholder="Last Name" name="last_name" type="text"
                                required value="<?php echo $last_name ?>" />
                        </div>
                        <div
                            class="w-full shadow-md p-2 rounded-md flex space-x-2 bg-white focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500">
                            <span class="material-icons text-gray-500">
                                email
                            </span>
                            <input class="w-full outline-none" placeholder="juliedoe@gmail.com" name="email"
                                type="email" required value="<?php echo $email ?>" />
                        </div>
                        <div
                            class="w-full shadow-md p-2 rounded-md flex space-x-2 bg-white focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500">
                            <span class="material-icons text-gray-500">
                                phone
                            </span>
                            <input class="w-full outline-none" placeholder="(876) 123-4567" name="phone" type="text"
                                required value="<?php echo $phone ?>" />
                        </div>
                        <div
                            class="w-full shadow-md p-2 rounded-md flex space-x-2 bg-white focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500">
                            <span class="material-icons text-gray-500">
                                lock
                            </span>
                            <input class="w-full outline-none" placeholder="***********" name="password" type="password"
                                required />

                        </div>
                        <?php
                            if ($password_err){
                                echo "<span class='text-red-500 text-sm'>$password_err</span>";
                            }
                        ?>
                    </div>
                    <div class="flex flex-col items-center space-y-4">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="agree" required />
                            <p id="openModal" for="agree"
                                class="text-gray-500 select-none cursor-pointer hover:underline">I agree with the
                                Privacy
                                Policy</p>
                        </div>
                        <div class="flex space-x-4 items-center">
                            <button class="bg-emerald-500 text-white px-6 py-2 
                                rounded hover:scale-105 hover:shadow-lg hover:shadow-emerald-500/30"
                                type="submit">Create
                                Account</button>
                            <a href="login.php" class="hover:text-blue-500">Back to login</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php include 'includes/footer.html'?>
    </div>
    <script>
    const menu = document.getElementById("open");
    const close = document.getElementById("close");
    const menuOpen = document.getElementById("menu-open");
    menu.addEventListener("click", (e) => {
        menuOpen.classList.remove("hidden");
        menuOpen.classList.add("block");
    });
    close.addEventListener("click", () => {
        menuOpen.classList.remove("block");
        menuOpen.classList.add("hidden");
    });
    const regModal = document.getElementById("reg-modal");
    const closeReg = document.getElementById("close-reg-modal");
    const openModal = document.getElementById("openModal");

    const toggleRegModal = () => {
        regModal.classList.toggle("hidden")
    }

    closeReg.onclick = toggleRegModal;
    openModal.onclick = toggleRegModal;
    </script>
</body>

</html>