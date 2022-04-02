<?php

    require_once 'renewSession.php';
    // session_start();
    // if(!isset($_COOKIE['auth'])){
    //     header('Location: login.php');
    // }

    $AccountId = $_COOKIE['accountId'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $gender = $_POST['gender'];
        $DOB = $_POST['dob'];
        $title = $_POST['title'];
        $religious = $_POST['religious'];
        $prevThep = $_POST['prevThep'];
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'includes/header.html'?>
    <title>Profile Setup - MediStation</title>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-blue-500 to-emerald-700">
    <nav class="items-center justify-between py-2 hidden md:flex bg-white">
        <div class="flex items-center">
            <a href="home.php" class="pl-4">
                <img src="./images/logo.png" class="h-[4rem]" />
            </a>
        </div>
        <div class="flex items-center space-x-8 pr-2">
            <a class="font-semibold cursor-pointer hover:underline">Find a Psychiatrist</a>
            <a class="font-semibold cursor-pointer hover:underline">Our Story</a>
            <a class="font-semibold cursor-pointer hover:underline">Contact Us</a>
            <a href="logout.php"
                class="bg-red-400 text-white px-6 py-2 rounded hover:scale-105 hover:shadow-lg hover:shadow-red-400/30">Logout</a>
        </div>
    </nav>
    <!-- Mobile Nav -->
    <nav class="flex md:hidden bg-white justify-between items-center">
        <div>
            <button id="open">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
            <li>
                <button>Logout</button>
            </li>
        </ul>
    </div>
    <div class="grow flex items-center justify-center">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>"
            class="bg-gray-100 p-4 md:rounded-md md:max-w-[70%] mx-auto w-full shadow-lg">
            <h2 class="text-center font-bold text-3xl text-blue-700">Please Answer All Questions</h2>
            <div class="flex flex-col md:flex-row md:space-x-8 w-full justify-between my-4 space-y-8 md:space-y-0">
                <div class="flex flex-col space-y-8 w-full">
                    <div class="flex flex-col md:flex-row md:items-center md:space-x-1">
                        <label for="gender">What is your gender?</label>
                        <select id="gender" class="grow"></select>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center md:space-x-2">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" class="grow outline outline-1 outline-gray-500" />
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center md:space-x-2">
                        <label for="identity">How do you identify?</label>
                        <select id="identity" class="grow"></select>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center md:space-x-2">
                        <label for="reg">Do you consider yourself to be religious?</label>
                        <select id="reg" class="grow"></select>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center md:space-x-2">
                        <label for="prev">Have you been in therapy before?</label>
                        <select id="prev" class="grow"></select>
                    </div>
                </div>
                <div class="flex flex-col space-y-8 md:space-y-2 w-full">
                    <div class="flex flex-col">
                        <label for="exp">What are your expectations from your psychiatrist?</label>
                        <textarea id="exp"
                            class="grow outline-none shadow-md rounded-md p-2 focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500"
                            rows="3"></textarea>
                    </div>
                    <div class="flex flex-col">
                        <label for="led">What lead you to consider therapy?</label>
                        <textarea id="led"
                            class="grow outline-none shadow-md rounded-md p-2 focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500"
                            rows="3"></textarea>
                    </div>
                    <div class="flex space-x-2">
                        <label for="exp">How do you rate your physical health?</label>
                        <div class="flex space-x-2">
                            <div class="flex items-center space-x-1">
                                <label for="good">Good</label>
                                <input type="radio" id="good" name="phy-health" />
                            </div>
                            <div class="flex items-center space-x-1">
                                <label for="fair">Fair</label>
                                <input type="radio" id="fair" name="phy-health" />
                            </div>
                            <div class="flex items-center space-x-1">
                                <label for="poor">Poor</label>
                                <input type="radio" id="poor" name="phy-health" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full flex items-center justify-center">
                <button
                    class="bg-emerald-500 text-white px-6 py-2 rounded hover:scale-105 hover:shadow-xl hover:shadow-emerald-500/30">Submit</button>
            </div>
        </form>
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