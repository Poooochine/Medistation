<?php 
    session_start();

    require_once "conn.php";

    $err = "";
    $email = "";
    
    if (isset($_COOKIE['accountType'])){
        if ($_COOKIE['accountType'] == "client"){
            header("Location: client-dashboard.php");
        } else if ($_COOKIE['accountType'] == "System Admin"){
            header("Location: admin.php");
        } else {
            header("Location: psychiatrist-dashboard.php");
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $email = $_POST["email"];
        $password = $_POST["password"];

        $sql = "SELECT * FROM account WHERE Email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = $result->fetch_object();

        if($user){
            if ($user->isActive) {
                if (password_verify($password, $user->Pword)){
                    session_start();
                    setcookie("auth", true, time() + (60 * 15));
                    setcookie("accountId", $user->AccountID, time() + (60 * 15));
                    setcookie("accountType", $user->AccType, time() + (60 * 15));
                    if ($user->AccType == "client"){
                        header("Location: client-dashboard.php");
                    } else if ($user->AccType == "System Admin"){
                        header("Location: admin.php");
                    } else {
                        header("Location: psychiatrist-dashboard.php");
                    }
                    // header("location: home.php");
                } else {
                    $err = "Check email and/or password.";
                }
            } else {
                $err = "Your account has been deactivated. Please contact help desk.";
            }
        } else {
            $err = "Check email and/or password.";
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
    <title>Login - MediStation</title>
</head>

<body class="bg-gray-200">
    <div class="flex flex-col w-full min-h-screen bg-white max-w-6xl mx-auto shadow-lg">
        <!-- Desktop Navbar -->
        <nav class="items-center py-2 hidden lg:flex justify-between">
            <div class="flex items-center">
                <a href="home.php" class="grow pl-4">
                    <img src="./images/logo.png" class="h-[4rem]" />
                </a>
            </div>
            <div class="flex items-center space-x-8 pr-2">
                <a class="font-semibold cursor-pointer hover:underline">Find a Psychiatrist</a>
                <a class="font-semibold cursor-pointer hover:underline">Our Story</a>
                <a class="font-semibold cursor-pointer hover:underline">Contact Us</a>
            </div>
        </nav>
        <!-- Mobile Nav -->
        <nav class="flex lg:hidden">
            <button id="open" class="grow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
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
        <!-- Main -->
        <main class="grow">
            <div class="w-full bg-[url('./images/login.jpg')] bg-cover bg-no-repeat h-[27rem] relative">
                <form
                    class="flex flex-col items-center justify-center absolute lg:right-10 bg-emerald-600/70 w-full h-full lg:w-[27rem]"
                    method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                    <div class="w-full px-12 space-y-4">
                        <h3 class="font-bold text-2xl text-white text-center">Member Login</h3>
                        <div class="w-full flex items-center justify-center">
                            <?php if ($err){ echo "<p class='bg-red-400 w-full text-center py-2 px-1 text-white rounded'>$err</p>"; } ?>
                        </div>
                        <div
                            class="flex items-center space-x-4 bg-white p-1.5 rounded max-w-sm mx-auto focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            <input class="outline-none bg-transparent placeholder:font-sans text-gray-600 grow"
                                placeholder="Enter your email" type="email" name="email" value="<?php echo $email ?>"
                                required />
                        </div>
                        <div
                            class="flex items-center space-x-4 bg-white p-1.5 rounded max-w-sm mx-auto focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <input class="outline-none bg-transparent placeholder:font-sans text-gray-600 grow"
                                placeholder="Enter your password" type="password" name="password" required />
                        </div>
                        <div class="flex w-full items-center justify-center">
                            <button class="bg-indigo-500 text-white px-6 py-2 rounded 
                            hover:bg-indigo-600 hover:shadow-xl 
                            hover:shadow-indigo-600/40 hover:scale-105">Sign In</button>
                        </div>
                        <div class="flex items-center flex-col lg:flex-row justify-center space-x-1 text-white text-sm">
                            <a class="cursor-pointer hover:underline" href="registration.php">Don't have an account?
                                Sign Up</a>
                            <p class="hidden lg:block">|</p>
                            <a href="reset-password.php" class="cursor-pointer hover:underline">Forgot password?</a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
        <!-- Footer -->
        <footer class="bg-gray-300 flex flex-col items-center justify-center space-y-3 pb-2">
            <div class="lg:max-w-[85%] w-full px-6 flex flex-col lg:flex-row items-center justify-between space-x-4">
                <p class="text-center lg:w-[30rem] font-bold text-blue-700">If you are in a crisis or any other person
                    may
                    be in
                    danger,DO NOT USE
                    THIS.
                    Please call 911 or go to
                    the nearest hospital
                    Click here to see international suicide hotlines.</p>
                <img src="images/medias.jpg" class="w-[17rem]" />
            </div>
            <img src="images/sec.png" class="w-[40rem]" />
            <p class="text-gray-600 font-semibold">© 2022 Chimera Tech. All rights reserved</p>
        </footer>
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
    </script>
</body>

</html>