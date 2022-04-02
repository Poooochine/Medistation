<?php
    require_once 'renewSession.php';
    require_once 'conn.php';

    if ($_SERVER['REQUEST_METHOD'] == 'GET'){
        $accountType = $_COOKIE['accountType'];
        $results_per_page = 10;
        if (!isset($_GET["page"])){
            $pageNum = 1;
        } else {
            $pageNum = $_GET["page"];
        }
        if ($accountType == 'client'){
            $sql = "SELECT clientID FROM client WHERE AccountID = ? LIMIT 1";
        } else {
           $sql = "SELECT physicianID FROM physician WHERE AccountID = ? LIMIT 1"; 
        }
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $_COOKIE['accountId']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_object();
        $date = date("Y-m-d");

        if ($accountType == 'client'){
            $sql = "SELECT COUNT(*) FROM upcomingappointments INNER JOIN physician on 
            upcomingappointments.physicianID=physician.physicianID 
            INNER JOIN account on physician.AccountID=account.AccountID WHERE upcomingappointments.clientID=?
            AND upcomingappointments.status='Pending' AND upcomingappointments.aptDate >= '$date' 
            ORDER BY upcomingappointments.aptDate";
        } else {
            $sql = "SELECT COUNT(*) FROM upcomingappointments INNER JOIN physician on 
            upcomingappointments.physicianID=physician.physicianID 
            INNER JOIN account on physician.AccountID=account.AccountID WHERE upcomingappointments.physicianID=?
            AND upcomingappointments.status='Pending' AND upcomingappointments.aptDate >= '$date' 
            ORDER BY upcomingappointments.aptDate";
        }

        $initialResults = $conn->prepare($sql);
        if ($accountType == 'client'){
            $initialResults->bind_param('s', $user->clientID);
        } else {
            $initialResults->bind_param('s', $user->physicianID);
        }
        $initialResults->execute();
        $initialResults->store_result();
        $initialResults->bind_result($count);
        $initialResults->fetch();
        $number_of_pages = ceil($count/$results_per_page);
        $this_page_first_result = ($pageNum - 1) * $results_per_page;

        if ($accountType == 'client') {
            $sql = "SELECT physician.FName, physician.LName, account.Email, 
            upcomingappointments.id, upcomingappointments.aptDate, upcomingappointments.aptTime, 
            upcomingappointments.roomGenerated, upcomingappointments.room, upcomingappointments.status 
            FROM upcomingappointments INNER JOIN physician on upcomingappointments.physicianID=physician.physicianID 
            INNER JOIN account on physician.AccountID=account.AccountID WHERE upcomingappointments.clientID=?
            AND upcomingappointments.status='Pending' AND upcomingappointments.aptDate >= '$date' 
            ORDER BY upcomingappointments.aptDate ASC LIMIT $this_page_first_result, $results_per_page";
        } else {
            $sql = "SELECT client.FName, client.LName, account.Email, 
            upcomingappointments.id, upcomingappointments.aptDate, upcomingappointments.aptTime, 
            upcomingappointments.roomGenerated, upcomingappointments.room, upcomingappointments.status 
            FROM upcomingappointments INNER JOIN client on upcomingappointments.clientID=client.clientID 
            INNER JOIN account on client.AccountID=account.AccountID WHERE upcomingappointments.physicianID=?
            AND upcomingappointments.status='Pending' AND upcomingappointments.aptDate >= '$date' 
            ORDER BY upcomingappointments.aptDate ASC LIMIT $this_page_first_result, $results_per_page";
        }

        $stmt = $conn->prepare($sql);
        if ($accountType == 'client'){
            $stmt->bind_param('s', $user->clientID);
        } else {
            $stmt->bind_param('s', $user->physicianID);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $upcomingappointments = $result->fetch_all(MYSQLI_ASSOC);
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
        <?php 
        if ($accountType == 'client'){
            include 'includes/client-sidebar.php';
        } else {
            include 'includes/psychiatrist-sidebar.php';
        }
         ?>
        <div class="flex flex-col w-full">
            <?php 
            if ($accountType == 'client'){
                include 'includes/client-nav.php';
            }
            ?>
            <div class="flex flex-col justify-center grow">
                <div class="max-w-5xl mx-auto w-full rounded">
                    <h2 class="bg-purple-700 text-white text-3xl font-medium text-center py-2 rounded-t">Upcoming
                        Appointments
                    </h2>
                    <div class="bg-white rounded-b p-2">
                        <table class="my-2 w-full">
                            <thead>
                                <tr class="text-left bg-slate-800 text-white text-xl font-semibold">
                                    <th class="border border-slate-600">
                                        <?php if ($accountType == 'client') echo "Physician"; else echo "Client";?>
                                    </th>
                                    <th class="border border-slate-600">Email</th>
                                    <th class="border border-slate-600">Date and Time</th>
                                    <th class="border border-slate-600">Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($upcomingappointments as $row): ?>
                                <tr
                                    class="bg-slate-500 text-white hover:bg-slate-600 border border-slate-400 text-lg group">
                                    <?php $room = $row['room']; $upId = $row['id']; ?>
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
                                        if ($accountType == 'client') {
                                            echo "
                                            <td>
                                                <p>Room not yet created.</p>
                                            </td>";
                                        } else {
                                            echo "
                                        <td>
                                            <a href='generateRoom.php?id=$upId'>
                                                <button class='bg-slate-600 text-white px-4 py-2 my-0.5 rounded group-hover:bg-slate-700'>Generate Room Now</button>
                                            </a>
                                        </td>
                                        ";
                                        }
                                    }
                                ?>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                        <div class="w-full flex items-center justify-end mt-6">
                            <?php
                        if ($number_of_pages > 1){
                            ?>
                            <div class="flex items-center space-x-2 bg-slate-900/70 text-white rounded px-2 py-1">
                                <a class="flex items-center" href="upcoming-appointments.php?&page=<?php 
                        if ($pageNum == 1) {
                            echo $pageNum;
                        } else {
                            echo $pageNum - 1;
                        }
                        ?>">
                                    <span class="material-icons cursor-pointer">
                                        arrow_back_ios
                                    </span>
                                </a>
                                <div class="flex items-center space-x-1">
                                    <?php
                                for($p=1; $p<=$number_of_pages; $p++){
                                    if ($p == $pageNum){
                                        echo "
                                            <a class='border border-1 border-sky-600 px-2 py-0.5 rounded text-white cursor-not-allowed'
                                href='upcoming-appointments.php?page=$p'>$p</a>
                            ";
                            } else {
                            echo "
                            <a class='bg-slate-800/80 px-2 py-0.5 rounded text-white'
                                href='upcoming-appointments.php?page=$p'>$p</a>
                            ";
                            }
                            ?>
                                    <?php
                                }
                            ?>
                                </div>
                                <a class="flex items-center" href="upcoming-appointments.php?page=<?php 
                        if ($pageNum == $number_of_pages) {
                            echo $pageNum;
                        } else {
                            echo $pageNum + 1;
                        }
                        ?>">
                                    <span class="material-icons">
                                        arrow_forward_ios
                                    </span>
                                </a>
                            </div>
                            <?php
                    }
                    ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.html'?>
        </div>

    </div>
</body>

</html>