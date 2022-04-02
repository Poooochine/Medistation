<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'includes/header.html'?>
    <title>Document</title>
</head>

<body class="bg-gray-200">
    <main class="container bg-white max-w-6xl mx-auto min-h-screen shadow-xl">
        <?php include 'main-nav.html' ?>
        <img src="./images/info.png" alt="info">
        <div class="flex flex-col max-w-7xl mx-auto mt-8 bg-gray-50">
            <h1 class="text-center font-bold text-3xl text-[#044c80]">Speak to a Psychiatrist now!</h1>
            <div class="flex space-x-8 max-w-4xl mx-auto justify-center items-center my-6">
                <div class="flex flex-col justify-center items-center">
                    <img src="./images/device.jpg" alt="info">
                    <div class="flex flex-col justify-center items-center">
                        <h3 class="text-lg font-bold text-[#044c80]">Sign up or log in</h3>
                        <p class="text-sm text-gray-500 text-center">Using your smartphone, table or computer</p>
                    </div>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <img src="./images/doc.jpg" alt="info">
                    <div class="flex flex-col justify-center items-center">
                        <h3 class="text-lg font-bold text-[#044c80]">Choose a doctor</h3>
                        <p class="text-sm text-gray-500 text-center">Review their profile, qualifications and select a
                            psychiatrist that fits your needs.</p>
                    </div>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <img src="./images/meds.jpg" alt="info">
                    <div class="flex flex-col justify-center items-center">
                        <h3 class="text-lg font-bold text-[#044c80]">Feel better faster</h3>
                        <p class="text-sm text-gray-500 text-center">Get advice, treatment options and medication
                            management.</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col my-4 px-2">
                <h3 class="text-center font-bold text-3xl text-[#044c80]">Payment Plans</h3>
                <div class="flex justify-start items-start my-6">
                    <div class="flex flex-col bg-white rounded drop-shadow-md select-none">
                        <!-- Head -->
                        <div class="bg-[#4d6172] rounded-t text-white px-24 py-5">
                            <h2 class="font-bold text-3xl">Weekly Plan</h2>
                        </div>
                        <!-- Body -->
                        <div class="flex flex-col space-y-8 mb-8 justify-center items-center">
                            <div class="flex flex-col justify-center items-center text-[#9eb45e]">
                                <div class="flex">
                                    <p class="text-2xl font-semibold">$</p>
                                    <p class="text-8xl font-bold">12</p>
                                    <p class="text-2xl font-semibold">99</p>
                                </div>
                                <p>/ Per Week</p>
                            </div>
                            <p class="text-xs text-gray-400">FSA/HSA eligible</p>
                            <button
                                class="bg-[#9eb45e] text-white px-16 py-2 rounded-full font-bold text-xl hover:scale-105 hover:shadow-md hover:shadow-[#9eb45e]/30">Select</button>
                        </div>
                    </div>
                    <div class="flex flex-col bg-white rounded drop-shadow-md select-none scale-[1.03] z-10">
                        <!-- Head -->
                        <div class="bg-[#4d6172] rounded-t text-white px-24 py-5">
                            <h2 class="font-bold text-3xl">Monthly Plan</h2>
                        </div>
                        <!-- Body -->
                        <div class="flex flex-col space-y-8 mb-8 justify-center items-center">
                            <div class="flex flex-col justify-center items-center text-[#9eb45e]">
                                <div class="flex">
                                    <p class="text-2xl font-semibold">$</p>
                                    <p class="text-8xl font-bold">26</p>
                                    <p class="text-2xl font-semibold">99</p>
                                </div>
                                <p>/ Per month</p>
                            </div>
                            <div class="flex flex-col space-y-2 text-center justify-center text-xs text-gray-400">
                                <p>5% DISCOUNT</p>
                                <p>FSA/HSA eligible</p>
                            </div>
                            <button
                                class="bg-[#9eb45e] text-white px-16 py-2 rounded-full font-bold text-xl hover:scale-105 hover:shadow-md hover:shadow-[#9eb45e]/30">Select</button>
                        </div>
                    </div>
                    <div class="flex flex-col bg-white rounded drop-shadow-md select-none scale-[1.05] z-20">
                        <!-- Head -->
                        <div class="bg-[#4d6172] rounded-t text-white px-24 py-5">
                            <h2 class="font-bold text-3xl">Yearly Plan</h2>
                        </div>
                        <!-- Body -->
                        <div class="flex flex-col space-y-8 mb-8 justify-center items-center">
                            <div class="flex flex-col justify-center items-center text-[#9eb45e]">
                                <div class="flex">
                                    <p class="text-2xl font-semibold">$</p>
                                    <p class="text-8xl font-bold">203</p>
                                    <p class="text-2xl font-semibold">99</p>
                                </div>
                                <p>/ Per year</p>
                            </div>
                            <div class="flex flex-col space-y-2 text-center justify-center text-xs text-gray-400">
                                <p>15% DISCOUNT</p>
                                <p>Cancel Anytime</p>
                                <p>FSA/HSA eligible</p>
                            </div>
                            <button
                                class="bg-[#9eb45e] text-white px-16 py-2 rounded-full font-bold text-xl hover:scale-105 hover:shadow-md hover:shadow-[#9eb45e]/30">Select</button>
                        </div>
                    </div>
                </div>
                <p class="text-center mt-6 text-gray-400 font-semibold">Failure to make payment results in termination
                    of service</p>
            </div>
        </div>
    </main>
</body>

</html>