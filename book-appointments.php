<?php
    require_once 'renewSession.php';
    require_once 'conn.php';

    if($_SERVER["REQUEST_METHOD"] == "GET"){
        $sql = "SELECT physician.physicianID, physician.FName, physician.LName, 
        physician.Speciality, physicianprofile.image FROM physician INNER JOIN physicianprofile on 
        physician.physicianID=physicianprofile.physicianID";
        $stmt = $conn->query($sql);
        $physicians = $stmt->fetch_all(MYSQLI_ASSOC);
        $availability = null;
        $physician = null;

        $sql1 = "SELECT clientID FROM client WHERE AccountID = ? LIMIT 1";
        $stmt = $conn->prepare($sql1);
        $stmt->bind_param("s", $_COOKIE['accountId']);
        $stmt->execute();
        $client = $stmt->get_result()->fetch_object();
        $date = date("Y-m-d");

        if(isset($_GET["id"])){
            $sql = "SELECT *, physician.FName, physician.LName FROM physicianprofile INNER JOIN 
            physician on physicianprofile.physicianID=physician.physicianID WHERE physicianprofile.physicianID = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $_GET["id"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $physician = $result->fetch_object();

            $sql2 = "SELECT id, avaDate, avaTime, physicianID FROM physicianavailability WHERE physicianID = ? AND status = 'Open' AND avaDate >= '$date'";
            $stmt = $conn->prepare($sql2);
            $stmt->bind_param("s", $_GET["id"]);
            $stmt->execute();
            $availability = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        if (isset($_GET["aptId"])) {
            $sql = "SELECT id, avaDate, avaTime, physicianID FROM physicianavailability WHERE id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $_GET["aptId"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $ava = $result->fetch_object();
        }
    } 
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $physicianID = $_POST["physicianID"];
        $clientID = $_POST["clientID"];
        $avaTime = $_POST["time"];
        $avaDate = $_POST["date"];
        $id = $_POST["id"];
        $roomGenerated = 0;
        $status = "Pending";

        $sql3 = "INSERT INTO upcomingappointments (physicianID, clientID, aptDate, aptTime, roomGenerated, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql3);
        $stmt->bind_param("ssssss", $physicianID, $clientID, $avaDate, $avaTime, $roomGenerated, $status);
        $val = $stmt->execute();

        if ($val){
            $sql4 = "UPDATE physicianavailability SET status = 'Booked' WHERE id = ?";
            $stmt = $conn->prepare($sql4);
            $stmt->bind_param("i", $id);
            $val = $stmt->execute();

            if ($val){ 
                header('Location: client-dashboard.php?booking=success');
            } else {
                header('Location: book-appointments.php?booking=fail');
            }
        } else {
            header('Location: book-appointments.php?booking=fail');
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
    <script src="js/book-appointment.js" defer></script>
    <script src="https://js.stripe.com/v3/"></script>
    <title>Document</title>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-[#90a7c1] to-slate-600">
    <div class="flex flex-1 w-full h-full">
        <?php include 'includes/client-sidebar.php' ?>
        <div class="flex flex-col grow">
            <?php include 'includes/client-nav.php' ?>
            <?php 
                if (isset($_GET['type']) && $_GET['type'] == 'therapy') {
                    ?>
            <div class="relative">
                <img src="./images/therapy-banner.jpg" alt="banner" class="h-[13rem] w-full object-cover" />
                <div class="bg-green-600 text-white px-8 py-2 absolute bottom-12 font-bold text-3xl">
                    <p>Therapy</p>
                </div>
                <div class=" absolute right-2 top-2">
                    <img src="./images/logo.png" class="object-cover w-[15rem]" />
                </div>
                <a href="book-appointments.php"><i
                        class="fa fa-arrow-left text-2xl absolute top-2 left-2 cursor-pointer text-white hover:text-red-300"></i></a>
            </div>
            <div class="flex-1 flex h-full items-center">
                <div
                    class="bg-white backdrop-blur-lg rounded max-w-[90%] w-full mx-auto my-2 p-2 shadow-xl px-8 space-y-8 pb-4">
                    <p class="max-w-4xl mx-auto text-xl font-medium text-center">Our psychiatrist are here to help you
                        with life’s challenges. You can schedule an appointment with one of our providers online, or
                        schedule by calling 877-410-5548. Scheduling can be done 24 hours a day, 7 days a week. These
                        professionals have been hand-selected, trained, and certified in telehealth to deliver you the
                        best care possible.</p>
                    <div class="flex flex-wrap items-center justify-center w-full gap-3">
                        <?php
                        foreach ($physicians as $row) : ?>
                        <div
                            class="flex flex-col justify-center items-center bg-gray-700 px-6 py-3 rounded shadow-xl space-y-4">
                            <div class="flex flex-col items-center">
                                <img src="<?php echo $row['image'] ?>"
                                    class="w-[5rem] h-[5rem] object-cover object-center rounded-full" />
                                <p class="text-white text-lg"><?= $row['FName']." ".$row['LName'] ?></p>
                            </div>
                            <a href="<?php echo "book-appointments.php?type=".$_GET['type']."&id=".$row['physicianID']?>"
                                class="bg-emerald-600 text-white px-4 py-1 rounded hover:scale-105">View</a>
                        </div>
                        <?php 
                        endforeach
                    ?>
                    </div>
                </div>
            </div>
            <?php
                } else if (isset($_GET['type']) && $_GET['type'] == 'psychiatry') {
                    ?>
            <div class="relative">
                <img src="./images/therapy-banner.jpg" alt="banner" class="h-[13rem] w-full object-cover" />
                <div class="bg-blue-600 text-white px-8 py-2 absolute bottom-12 font-bold text-3xl">
                    <p>Psychiatry</p>
                </div>
                <div class=" absolute right-2 top-2">
                    <img src="./images/logo.png" class="object-cover w-[15rem]" />
                </div>
                <a href="book-appointments.php"><i
                        class="fa fa-arrow-left text-2xl absolute top-2 left-2 cursor-pointer text-white hover:text-red-300"></i></a>
            </div>
            <div class="flex-1 flex h-full items-center">
                <div
                    class="bg-white backdrop-blur-lg rounded max-w-[90%] w-full mx-auto my-2 p-2 shadow-xl px-8 space-y-8 pb-4">
                    <p class="max-w-4xl mx-auto text-xl font-medium text-center">Our psychiatrists are here to help you
                        with life’s challenges. Psychiatrists can prescribe medication, but at this time OCG
                        psychiatrists are not able to prescribe any psychotropic medications that are deemed controlled
                        substances. You can schedule an appointment with one of our providers online, or by calling
                        877-410-5548. Scheduling can be done 24 hours a day, 7 days a week.</p>
                    <div class="flex flex-wrap items-center justify-center w-full gap-3">
                        <?php
                        foreach ($physicians as $row) : ?>
                        <div
                            class="flex flex-col justify-center items-center bg-gray-700 px-6 py-3 rounded shadow-xl space-y-4">
                            <div class="flex flex-col items-center">
                                <img src="<?php echo $row['image'] ?>"
                                    class="w-[5rem] h-[5rem] object-cover object-center rounded-full" />
                                <p class="text-white text-lg"><?= $row['FName']." ".$row['LName'] ?></p>
                            </div>
                            <a href="<?php echo "book-appointments.php?type=".$_GET['type']."&id=".$row['physicianID']?>"
                                class="bg-emerald-600 text-white px-4 py-1 rounded hover:scale-105">View</a>
                        </div>
                        <?php 
                        endforeach
                    ?>
                    </div>
                </div>
            </div>
            <?php
                } else if (isset($_GET['type']) && $_GET['type'] == 'adolescent-therapy') {
                    ?>
            <div class="relative">
                <img src="./images/therapy-banner.jpg" alt="banner" class="h-[13rem] w-full object-cover" />
                <div class="bg-violet-600 text-white px-8 py-2 absolute bottom-12 font-bold text-3xl">
                    <p>Adolescent Therapy</p>
                </div>
                <div class=" absolute right-2 top-2">
                    <img src="./images/logo.png" class="object-cover w-[15rem]" />
                </div>
                <a href="book-appointments.php"><i
                        class="fa fa-arrow-left text-2xl absolute top-2 left-2 cursor-pointer text-white hover:text-red-300"></i></a>
            </div>
            <div class="flex-1 flex h-full items-center">
                <div
                    class="bg-white backdrop-blur-lg rounded max-w-[90%] w-full mx-auto my-2 p-2 shadow-xl px-8 space-y-8 pb-4">
                    <p class="max-w-4xl mx-auto text-xl font-medium text-center">Please call 877-410-5548 for an
                        appointment. The Adolescent Behavioral Health Practice is available to see children ages 10-17
                        with behavioral and mental health needs. Therapists are ready to help your child with anxiety,
                        ADHD, school problems, eating difficulties, depression or other behavioral or emotional
                        challenges.</p>
                    <div class="flex flex-wrap items-center justify-center w-full gap-3">
                        <?php
                        foreach ($physicians as $row) : ?>
                        <div
                            class="flex flex-col justify-center items-center bg-gray-700 px-6 py-3 rounded shadow-xl space-y-4">
                            <div class="flex flex-col items-center">
                                <img src="<?php echo $row['image'] ?>"
                                    class="w-[5rem] h-[5rem] object-cover object-center rounded-full" />
                                <p class="text-white text-lg"><?= $row['FName']." ".$row['LName'] ?></p>
                            </div>
                            <a href="<?php echo "book-appointments.php?type=".$_GET['type']."&id=".$row['physicianID']?>"
                                class="bg-emerald-600 text-white px-4 py-1 rounded hover:scale-105">View</a>
                        </div>
                        <?php 
                        endforeach
                    ?>
                    </div>
                </div>
            </div>
            <?php
                } else {
                    ?>
            <div class="flex-1 flex h-full items-center">
                <div class="bg-gray-100 rounded max-w-[90%] w-full h-[80%] mx-auto shadow-xl">
                    <h2 class="text-3xl font-medium text-center bg-teal-500 text-white p-2">Choose you service
                    </h2>
                    <div class="flex flex-wrap gap-4 items-center justify-center -my-8 h-full">
                        <a href="book-appointments.php?type=therapy"
                            class="bg-white rounded group hover:-translate-y-4 transition-all duration-300 cursor-pointer max-w-[19rem] space-y-4 shadow-xl">
                            <h2 class="font-medium text-2xl text-white bg-emerald-600 text-center p-2">
                                Therapy
                            </h2>
                            <p class="p-2">Our psychiatrist are here to help you with life’s challenges. You can
                                schedule an
                                appointment
                                with one of our providers online, or schedule by calling 877-410-5548. Scheduling can be
                                done 24 hours a day, 7 days a week. These professionals have been hand-selected,
                                trained,
                                and certified in telehealth to deliver you the best care possible.</p>
                        </a>
                        <a href="book-appointments.php?type=psychiatry"
                            class="bg-white rounded group hover:-translate-y-4 transition-all duration-300 cursor-pointer max-w-[19rem] space-y-4 shadow-xl">
                            <h2 class="font-medium text-2xl text-white bg-blue-800 text-center p-2">
                                Psychiatry
                            </h2>
                            <p class="p-2">Our psychiatrists are here to help you with life’s challenges. Psychiatrists
                                can
                                prescribe
                                medication, but at this time OCG psychiatrists are not able to prescribe any
                                psychotropic
                                medications that are deemed controlled substances. You can schedule an appointment with
                                one
                                of our providers online, or by calling 877-410-5548. Scheduling can be done 24 hours a
                                day,
                                7 days a week.</p>
                        </a>
                        <a href="book-appointments.php?type=adolescent-therapy"
                            class="bg-white rounded group hover:-translate-y-4 transition-all duration-300 cursor-pointer max-w-[19rem] space-y-4 shadow-xl">
                            <h2 class="font-medium text-2xl text-white bg-violet-700 text-center p-2">
                                Adolescent Therapy
                            </h2>
                            <p class="p-2">Please call 877-410-5548 for an appointment. The Adolescent Behavioral Health
                                Practice is
                                available to see children ages 10-17 with behavioral and mental health needs. Therapists
                                are
                                ready to help your child with anxiety, ADHD, school problems, eating difficulties,
                                depression or other behavioral or emotional challenges.</p>
                        </a>
                    </div>
                </div>
            </div>

            <?php
                }
            
            ?>
            <?php include 'includes/footer.html'?>
            <?php
                if ($physician){
                    ?>
            <div
                class="bg-black/40 absolute inset-0 flex flex-col items-center justify-center w-full h-full overflow-hidden">
                <button id="close-btn"
                    class="absolute z-50 top-5 right-5 bg-white flex items-center justify-center hover:bg-red-500 hover:text-white cursor-pointer">
                    <span class="material-icons">
                        close
                    </span>
                </button>
                <div class="bg-slate-200 w-[75%] rounded my-2">
                    <div class="w-full flex">
                        <div class="flex flex-col justify-center space-y-3 p-4 w-[25%]">
                            <img src="<?php echo $physician->image ?>" alt="profile"
                                class="w-[15rem] h-[20rem] object-cover rounded" />
                            <h2 class="text-2xl font-medium">
                                <?php echo $physician->FName." ". $physician->LName ?>
                            </h2>
                            <div>
                                <button id="switch" class="bg-slate-700 text-white px-4 py-2 rounded">Schedule
                                    Appointment</button>
                            </div>
                        </div>
                        <div id="detail" class="bg-[#cbd4e1] grow p-4 h-[450px] space-y-4 overflow-auto">
                            <div class="bg-white ring-1 ring-slate-500 rounded p-2 max-w-3xl">
                                <p><?php echo $physician->description ?></p>
                            </div>
                            <div class="grid grid-cols-2 gap-8">
                                <div class="border-b border-gray-500 py-2 space-y-1">
                                    <p class="text-xl font-medium">Languages</p>
                                    <p class="text-gray-700 font-medium"><?php echo $physician->language ?></p>
                                    </p>
                                </div>
                                <div class="border-b border-gray-500 py-2 space-y-1">
                                    <p class="text-xl font-medium">Education</p>
                                    <p class="text-gray-700 font-medium"><?php echo $physician->education ?></p>
                                    </p>
                                </div>
                                <div class="border-b border-gray-500 py-2 space-y-1">
                                    <p class="text-xl font-medium">Years of Experience</p>
                                    <p class="text-gray-700 font-medium"><?php echo $physician->yearsOfExperience ?>
                                        years</p>
                                    </p>
                                </div>
                                <div class="border-b border-gray-500 py-2 space-y-1">
                                    <p class="text-xl font-medium">Location</p>
                                    <p class="text-gray-700 font-medium"><?php echo $physician->location ?></p>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div id="apt" class="bg-[#cbd4e1] grow p-4 h-[450px] space-y-4 overflow-auto hidden">
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>"
                                id="reserve-form">
                                <input type="hidden" name="date" value="<?php echo $ava->avaDate ?>" />
                                <input type="hidden" name="time" value="<?php echo $ava->avaTime ?>" />
                                <input type="hidden" name="physicianID" value="<?php echo $ava->physicianID ?>" />
                                <input type="hidden" name="clientID" value="<?php echo $client->clientID ?>" />
                                <input type="hidden" name="id" value="<?php echo $ava->id ?>" />
                                <table class="border-collapse w-full mx-auto border border-slate-500 my-2">
                                    <thead>
                                        <tr class="text-left bg-slate-800 text-white text-xl font-semibold">
                                            <th class="border border-slate-600">Date</th>
                                            <th class="border border-slate-600">Time</th>
                                            <th class="border border-slate-600">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                            foreach ($availability as $row): ?>
                                        <tr class="bg-slate-500 text-white hover:bg-slate-600 border border-slate-400">
                                            <td><?= date('F j, Y' ,strtotime($row['avaDate'])) ?></td>
                                            <td><?= date('g:i A' ,strtotime($row['avaTime'])) ?></td>
                                            <td>
                                                <button type="button" id="reserve-button"
                                                    class="bg-emerald-600 text-white px-4 py-1 rounded my-1 hover:scale-105"
                                                    onclick="togglePayment('<?php echo $row['id'] ?>')">Reserve
                                                    Now</button>
                                            </td>
                                        </tr>
                                        <?php
                            endforeach
                        ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="hidden m-auto flex items-center gap-2" id="payment-wrapper">
                            <div
                                class="bg-white m-auto w-[22rem] px-4 p-2 rounded space-y-4 flex flex-col items-center justify-center drop-shadow-xl relative">
                                <p class="text-xl font-medium">Your Payment Summary</p>
                                <table class="w-full">
                                    <tbody>
                                        <tr class="font-medium bg-slate-200 rounded">
                                            <td class="p-1">Description</td>
                                            <td class="p-1">Amount</td>
                                        </tr>
                                        <tr class="pt-2">
                                            <td class="p-1">
                                                Therapy Session
                                            </td>
                                            <td class="p-1">$65.00</td>
                                        </tr>
                                        <td colspan="2">
                                            <div class="border-y border-black mt-6 mb-2"></div>
                                        </td>
                                        <tr>
                                            <td>Subtotal</td>
                                            <td>$65.00</td>
                                        </tr>
                                        <tr>
                                            <td>Processing fee</td>
                                            <td>$2.25</td>
                                        </tr>
                                        <td colspan="2">
                                            <div class="border-y border-black mb-2"></div>
                                        </td>
                                        <tr>
                                            <td>Grand Total</td>
                                            <td>$67.25</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div
                                class="bg-white m-auto p-6 rounded space-y-4 flex flex-col items-center justify-center drop-shadow-xl relative">
                                <button class="absolute right-2 top-2 hover:text-red-400" id="cancel-payment">
                                    <span class="material-icons ">
                                        close
                                    </span>
                                </button>
                                <span class="material-icons text-6xl animate-spin text-violet-600" id="form-loading">
                                    sync
                                </span>
                                <form id="payment-form">
                                    <div id="payment-element">
                                        <!--Stripe.js injects the Payment Element-->
                                    </div>
                                    <button id="submit"
                                        class="items-center justify-center bg-violet-700 text-white px-6 py-2 my-2 rounded w-full hidden">
                                        <span class="material-icons text-3xl animate-spin text-green-600 hidden"
                                            id="spinner">
                                            sync
                                        </span>
                                        <span id="button-text">Pay now</span>
                                    </button>
                                    <div id="payment-message"
                                        class="hidden text-center text-white font-medium bg-slate-700 rounded px-2 py-1">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }
            ?>

            </div>

        </div>
    </div>
</body>

</html>