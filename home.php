<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include 'includes/header.html'?>
    <script src="js/main.js"></script>
    <title>Home - MediStation</title>
</head>

<body class="lg:bg-gray-200 bg-white select-none">
    <div class="relative min-h-screen max-w-6xl mx-auto shadow-lg lg:bg-[url('./images/counseling.jpeg')] bg-no-repeat bg-cover"
        style="background-position: 230px 0px">
        <!-- Mobile Nav -->
        <div class="block md:hidden">
            <div class="flex justify-between items-center bg-white/70 p-1">
                <button id="open">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <img src="./images/logo.png" class="'w-10 h-10" />
            </div>
        </div>

        <!-- Tablet Nav -->

        <div class="hidden items-center w-full bg-white space-x-4 px-2 py-1.5 md:flex lg:hidden">
            <img src="./images/logo.png" class="'w-10 h-10" />
            <ul class="flex grow items-center justify-between text-[#1c3344]">
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
                    <a class="bg-[#6cb038] text-white px-8 py-1.5 shadow-md cursor-pointer hover:scale-105 hover:shadow-md hover:shadow-[#6cb038a9]"
                        href="login.php">
                        Login
                    </a>
                </li>
            </ul>
        </div>

        <!-- Nav Open -->
        <div class="absolute h-full top-0 left-0 w-[80%] bg-white shadow space-y-4 hidden" id="menu-open">
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
                <li>
                    <a class="bg-[#6cb038] text-white px-8 py-1.5 shadow-md cursor-pointer hover:scale-105 hover:shadow-md hover:shadow-[#6cb038a9]"
                        href="login.php">
                        Login
                    </a>
                </li>
            </ul>
        </div>

        <!-- left panel -->
        <div class="bg-white lg:absolute left-0 top-0 h-full lg:max-w-[40%] lg:space-y-20 lg:shadow relative">
            <div class="w-full flex items-center justify-center">
                <img src="./images/logo.png" class="hidden lg:block w-[21rem] mt-3" />
            </div>
            <div class="space-y-8 md:mt-20 lg:mt-0 md:px-20 lg:px-0">
                <div class="p-2 space-y-8 px-6">
                    <h3 class="text-[#044c80] font-bold text-5xl tracking-wide leading-[3.75rem]">
                        Your story is valuable, and we want to hear it.
                    </h3>
                    <p class="text-[1.1rem] leading-10">
                        At MediStation, we believe that every detail matters because you
                        matter. We're ready to listen , even if you're story hasn't gone
                        as planned. Together, we'll write your next chapter.
                    </p>
                    <div class="lg:hidden items-center space-y-4 flex flex-col w-full justify-center">
                        <p class="text-lg">Ready to share your story?</p>
                        <button
                            class="bg-[#6cb038] text-white px-12 py-1.5 hover:scale-105 hover:shadow-md hover:shadow-[#6cb038a9]">
                            Learn More
                        </button>
                    </div>
                </div>
                <div class="hidden w-[115%] lg:flex items-center space-x-16 bg-gray-50 shadow-xl p-2 py-6 rounded">
                    <p class="text-xl ml-4">Ready to share your story?</p>
                    <button
                        class="bg-[#6cb038] text-white px-12 py-1.5 hover:scale-105 hover:shadow-md hover:shadow-[#6cb038a9]">
                        Learn More
                    </button>
                </div>
            </div>
        </div>
        <!-- Right nav -->
        <div class="hidden lg:block absolute right-0 top-5 bg-white/40 max-w-xl w-full shadow backdrop-blur-lg">
            <ul class="flex justify-between items-center space-x-2 ml-20 text-[#0f2536]">
                <li class="font-semibold cursor-pointer hover:underline">
                    Find a Psychiatrist
                </li>
                <li class="font-semibold cursor-pointer hover:underline">
                    Our Story
                </li>
                <li class="font-semibold cursor-pointer hover:underline">
                    Contact Us
                </li>
                <a href="login.php">
                    <li
                        class="bg-[#6cb038] text-white px-8 py-1.5 shadow-md cursor-pointer hover:scale-105 hover:shadow-md hover:shadow-[#6cb038a9]">
                        Login
                    </li>
                </a>
            </ul>
        </div>
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