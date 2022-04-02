<?php
    require_once 'renewSession.php';
    require_once 'conn.php';

    if ($_SERVER['REQUEST_METHOD'] == 'GET'){
        $sql = "SELECT clientID, FName, LName, registrationStatus FROM client WHERE AccountID = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $_COOKIE['accountId']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_object();
        $name = $user->FName." ".$user->LName;
        $date = date("Y-m-d");

        $sql2 = "SELECT physician.FName, physician.LName, account.Email, 
        upcomingappointments.id, upcomingappointments.aptDate, upcomingappointments.aptTime, 
        upcomingappointments.roomGenerated, upcomingappointments.room, upcomingappointments.status 
        FROM upcomingappointments INNER JOIN physician on upcomingappointments.physicianID=physician.physicianID 
        INNER JOIN account on physician.AccountID=account.AccountID WHERE upcomingappointments.clientID=?
        AND upcomingappointments.status='Pending' AND upcomingappointments.aptDate >= '$date' ORDER BY upcomingappointments.aptDate ASC LIMIT 5";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param('s', $user->clientID);
        $stmt->execute();
        $result = $stmt->get_result();
        $upcomingappointments = $result->fetch_all(MYSQLI_ASSOC);
    }

    if ($user && $user->registrationStatus == "Incomplete"){
        header('Location: profile-setup.php');
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

<body class="min-h-screen flex flex-col bg-gradient-to-br from-[#90a7c1] to-slate-600">
    <div class="flex flex-1 w-full h-full">
        <!-- Sidebar -->
        <?php include 'includes/client-sidebar.php' ?>
        <div class="flex flex-col grow">
            <?php include 'includes/client-nav.php' ?>
            <div class="grow flex flex-col justify-center items-center space-y-4 my-4">
                <div class="flex flex-wrap gap-4 items-center justify-center my-4">
                    <a href="book-appointments.php?type=therapy"
                        class="bg-white rounded group hover:-translate-y-4 transition-all duration-300 cursor-pointer max-w-[19rem] space-y-4 shadow-xl">
                        <h2 class="font-medium text-2xl text-white bg-emerald-600 text-center p-2">
                            Therapy
                        </h2>
                        <p class="p-2">Our psychiatrist are here to help you with life’s challenges. You can schedule an
                            appointment
                            with one of our providers online, or schedule by calling 877-410-5548. Scheduling can be
                            done 24 hours a day, 7 days a week. These professionals have been hand-selected, trained,
                            and certified in telehealth to deliver you the best care possible.</p>
                    </a>
                    <a href="book-appointments.php?type=psychiatry"
                        class="bg-white rounded group hover:-translate-y-4 transition-all duration-300 cursor-pointer max-w-[19rem] space-y-4 shadow-xl">
                        <h2 class="font-medium text-2xl text-white bg-blue-800 text-center p-2">
                            Psychiatry
                        </h2>
                        <p class="p-2">Our psychiatrists are here to help you with life’s challenges. Psychiatrists can
                            prescribe
                            medication, but at this time OCG psychiatrists are not able to prescribe any psychotropic
                            medications that are deemed controlled substances. You can schedule an appointment with one
                            of our providers online, or by calling 877-410-5548. Scheduling can be done 24 hours a day,
                            7 days a week.</p>
                    </a>
                    <a href="book-appointments.php?type=adolescent-therapy"
                        class="bg-white rounded group hover:-translate-y-4 transition-all duration-300 cursor-pointer max-w-[19rem] space-y-4 shadow-xl">
                        <h2 class="font-medium text-2xl text-white bg-violet-700 text-center p-2">
                            Adolescent Therapy
                        </h2>
                        <p class="p-2">Please call 877-410-5548 for an appointment. The Adolescent Behavioral Health
                            Practice is
                            available to see children ages 10-17 with behavioral and mental health needs. Therapists are
                            ready to help your child with anxiety, ADHD, school problems, eating difficulties,
                            depression or other behavioral or emotional challenges.</p>
                    </a>
                </div>
                <div class="bg-white max-w-[75%] w-full mx-auto rounded shadow-xl">
                    <h3 class="text-center font-semibold text-2xl bg-amber-600 text-white p-2">Upcoming Appointments
                    </h3>
                    <div class="p-2">
                        <table class="my-2 w-full">
                            <tbody>
                                <?php
                            foreach ($upcomingappointments as $row): ?>
                                <tr class="border-b border-slate-400">
                                    <?php $room = $row['room']; ?>
                                    <td><?= $row['FName']." ".$row['LName'] ?></td>
                                    <td><?= $row['Email'] ?></td>
                                    <td><?= date('F j, Y g:i A', strtotime($row['aptDate']." ".$row['aptTime'])) ?></td>
                                    <?php
                                if ($row['roomGenerated']){
                                    ?>
                                    <?php
                                    echo "
                                    <td>
                                        <a href='video-conference.php?room=$room'>
                                            <button class='bg-emerald-600 text-white px-4 py-2 my-0.5 rounded'>Join Room</button>
                                        </a>
                                    </td>";
                                } else {
                                    echo "
                                    <td>
                                        <p>Room not yet created.</p>
                                    </td>";
                                }
                            ?>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white rounded group transition-all duration-300 w-[35rem] shadow-xl">
                    <h3 class="text-center font-semibold text-2xl bg-cyan-600 text-white p-2">Send us a message
                    </h3>
                    <div class="p-2 space-y-2">
                        <label for="subject">Subject</label>
                        <div class="p-2 ring-1 ring-slate-400 hover:ring-sky-500 focus-within:ring-sky-500 rounded">
                            <input id="message" class="outline-none w-full" />
                        </div>
                    </div>
                    <div class="p-2 space-y-2">
                        <label for="message">Message</label>
                        <div
                            class="p-2 shadow-xl ring-1 ring-slate-400 hover:ring-sky-500 focus-within:ring-sky-500 rounded">
                            <textarea id="message" class="outline-none w-full" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="w-full flex items-center justify-center p-2">
                        <button class="bg-teal-500 text-white px-6 py-2 rounded hover:scale-105">Send</button>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.html'?>
        </div>
    </div>
</body>

</html>