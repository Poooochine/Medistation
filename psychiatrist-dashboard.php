<?php
    require_once 'renewSession.php';
    require_once 'conn.php';
    if ($_SERVER["REQUEST_METHOD"] == "GET"){
        $avaDate = array();

        $sql = "SELECT FName, LName, physicianID FROM physician WHERE AccountID = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $_COOKIE['accountId']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_object();
        $name = $user->FName." ".$user->LName;

        if (isset($_GET['status'])){
            $status = $_GET['status'];
        } else {
            $status = "Booked";
        }

        $results_per_page = 5;
        if (!isset($_GET["page"])){
            $page = 1;
        } else {
            $page = $_GET["page"];
        }

        $sql1 = "SELECT id, avaDate, avaTime, status FROM physicianavailability WHERE physicianID = ? AND status = ?";
        $stmt = $conn->prepare($sql1);
        $stmt->bind_param('ss',$user->physicianID, $status);
        $stmt->execute();
        $stmt->store_result();
        $stmt->fetch();
        $count = $stmt->num_rows;

        $number_of_pages = ceil($count/$results_per_page);
        $this_page_first_result = ($page - 1) * $results_per_page;

        $sql2 = "SELECT id, avaDate, avaTime, status FROM physicianavailability WHERE physicianID = ? AND status = ? LIMIT $this_page_first_result , $results_per_page";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param('ss', $user->physicianID, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $avaDate = $result->fetch_all(MYSQLI_ASSOC);
        $date = date("Y-m-d");

        $sql3 = "SELECT client.FName, client.LName, account.Email, upcomingappointments.id, upcomingappointments.aptDate, 
        upcomingappointments.aptTime, upcomingappointments.roomGenerated, upcomingappointments.room, upcomingappointments.status 
        FROM upcomingappointments INNER JOIN client on upcomingappointments.clientID=client.clientID 
        INNER JOIN account on client.AccountID=account.AccountID WHERE upcomingappointments.physicianID=? AND upcomingappointments.status='Pending' AND upcomingappointments.aptDate >= '$date' ORDER BY upcomingappointments.aptDate ASC LIMIT 5";
        $stmt = $conn->prepare($sql3);
        $stmt->bind_param("s", $user->physicianID);
        $stmt->execute();
        $result = $stmt->get_result();
        $upcomingappointments = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST["addAva"])){
            $date = $_POST["date"];
            $time = $_POST["time"];
            $physicianID = $_POST["physicianID"];
            $status = "Open";
            $sql = "INSERT INTO physicianavailability (avaDate, avaTime, status, physicianID) VALUES(?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $date, $time, $status, $physicianID);
            $val = $stmt->execute();
            if ($val){
                header('Location: psychiatrist-dashboard.php?status=Open&added=true');
            } else {
                header('Location: psychiatrist-dashboard.php?status=Open&added=false');
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
    <title>Document</title>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-[#90a7c1] to-slate-600" id="psyBody">
    <div class="fixed z-50 inset-0 bg-black/50 items-center justify-center hidden" id="psyModal">
        <div class="max-w-[50%] w-full bg-gray-200 shadow-lg rounded-md p-3 flex flex-col">
            <h2 class="font-semibold text-lg text-center">Add New Availability</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" class=" flex flex-col px-20
                space-y-2">
                <div class="flex flex-col space-y-2" id="accountContainer">
                    <div class="flex flex-col justify-center">
                        <label for="date">Date</label>
                        <div
                            class="flex bg-white px-2 py-1 shadow-lg rounded space-x-2 focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500">
                            <input class="outline-none grow" id="date" name="date" type="date" required />
                        </div>
                    </div>
                    <div class="flex flex-col justify-center">
                        <label for="time">Time</label>
                        <div
                            class="flex bg-white px-2 py-1 shadow-lg rounded space-x-2 focus-within:ring-1 focus-within:ring-sky-500 hover:ring-1 hover:ring-sky-500">
                            <input class="outline-none grow" id="time" name="time" type="time" required />
                        </div>
                    </div>
                    <input type="hidden" name="physicianID" value="<?php echo $user->physicianID ?>" />
                </div>
                <div class="w-full flex items-center justify-center">
                    <div class="flex items-center space-x-4 my-2">
                        <button
                            class="px-4 py-2 bg-red-600 text-white rounded hover:scale-105 hover:shadow-lg hover:shadow-red-600/30"
                            type="button" id="cancelPsyModal">Cancel</button>
                        <button
                            class="px-4 py-2 bg-emerald-600 text-white rounded hover:scale-105 hover:shadow-lg hover:shadow-emerald-600/30"
                            type="submit" name="addAva">Add
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="flex flex-1 w-full h-full">
        <!-- Sidebar -->
        <?php include 'includes/psychiatrist-sidebar.php'; ?>
        <div class="flex flex-1 flex-col grow">
            <!-- <nav class="bg-[#6bb135] py-3 px-4 flex space-x-6 justify-end text-white sticky top-0">
                <a href="logout.php" class="cursor-pointer">
                    <span class="material-icons">
                        logout
                    </span>
                </a>
            </nav> -->
            <div class="flex flex-col items-center justify-center grow space-y-6">
                <div class="mx-auto flex flex-col w-[75%] bg-white mt-2 rounded">
                    <h3
                        class="text-center font-semibold p-2 text-2xl bg-green-500 text-white overflow-hidden rounded-t">
                        Upcoming
                        Appointments
                    </h3>
                    <div class="p-2 w-full">
                        <table class="w-full">
                            <tbody>
                                <?php
                                    foreach ($upcomingappointments as $row): ?>
                                <tr class="border-b border-slate-400">
                                    <?php $room = $row['room']; $upId = $row['id']; ?>
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
                                                <a href='generateRoom.php?id=$upId'>
                                                    <button class='bg-slate-600 text-white px-4 py-2 my-0.5 rounded'>Generate Room Now</button>
                                                </a>
                                            </td>";
                                        }
                                    ?>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mx-auto flex flex-col w-[75%] bg-white my-2 rounded">
                    <h3 class="text-center font-semibold text-2xl p-2 bg-amber-500 text-white rounded-t">My Availability
                    </h3>
                    <div class="p-2">
                        <div class="flex w-full items-center justify-between">
                            <form class="w-[25%]" method="GET"
                                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" id="avaFilter">
                                <select class="w-full" id="avaFilterValue" name="status" class="ring-1 ring-black">
                                    <option value="Booked">Booked</option>
                                    <option value="Open">Open</option>
                                </select>
                            </form>
                            <button class="bg-slate-600 text-white px-4 py-2 rounded" id="addAva">Add
                                Availability</button>
                        </div>
                        <table class="border-collapse w-full border border-slate-500 my-2">
                            <thead>
                                <tr class="text-left bg-slate-800 text-white text-xl font-semibold">
                                    <th class="border border-slate-600">Date</th>
                                    <th class="border border-slate-600">Time</th>
                                    <th class="border border-slate-600">Status</th>
                                    <th class="border border-slate-600">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($avaDate as $row): ?>
                                <tr class="bg-slate-500 text-white hover:bg-slate-600 border border-slate-400">
                                    <td><?= date('F j, Y', strtotime($row['avaDate'])) ?></td>
                                    <td><?= date('g:i A', strtotime($row['avaTime'])) ?></td>
                                    <td><?= $row['status'] ?></td>
                                    <?php
                                    if ($row['status'] == 'Open'){
                                        ?>
                                    <td>
                                        <a href='update-appointment.php?id=<?php echo $row['id'] ?>&status=Closed'>
                                            <button class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                                        </a>
                                    </td>
                                    <?php
                                    } else {
                                        echo "<td></td>";
                                    }
                                ?>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                        <?php if ($number_of_pages > 0) {
                        ?>
                        <div class="w-full flex items-center justify-end">
                            <div class="flex items-center space-x-2 bg-slate-900/70 text-white rounded px-2 py-1">
                                <a class="flex items-center" href="psychiatrist-dashboard.php?status=<?php echo $status ?>&page=<?php 
                                if ($page == 1) {
                                    echo $page;
                                } else {
                                    echo $page - 1;
                                }
                                ?> ">
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
                                        href='psychiatrist-dashboard.php?status=$status&page=$p'>$p</a>
                                    ";
                                    } else {
                                    echo "
                                    <a class='bg-slate-800/80 px-2 py-0.5 rounded text-white'
                                        href='psychiatrist-dashboard.php?status=$status&page=$p'>$p</a>
                                    ";
                                    }
                                    ?>
                                    <?php
                                        }
                                    ?>
                                </div>
                                <a class="flex items-center" href="psychiatrist-dashboard.php?status=<?php echo $status ?>&page=<?php 
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
                        </div>
                        <?php
                    } ?>
                    </div>

                </div>
            </div>
            <?php include 'includes/footer.html'?>
        </div>
    </div>
</body>

</html>