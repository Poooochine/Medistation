<?php 
    require_once 'renewSession.php';
    require_once 'conn.php';
    $date = date("Y-m-d");

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $results_per_page = 10;
        if (!isset($_GET["page"])){
            $page = 1;
        } else {
            $page = $_GET["page"];
        }

        
        $sql = "SELECT aptDate, aptTime, room FROM upcomingappointments WHERE status = 'PENDING' AND aptDate < '$date'";
        $stmt = $conn->query($sql);
        $count = $stmt->num_rows;

        $number_of_pages = ceil($count/$results_per_page);
        $this_page_first_result = ($page - 1) * $results_per_page;

        $sql = "SELECT aptDate, aptTime, room FROM upcomingappointments WHERE status = 'PENDING' AND aptDate < '$date' LIMIT $this_page_first_result , $results_per_page";
        $result = $conn->query($sql);
        $upcomingappointments = $result->fetch_all(MYSQLI_ASSOC);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST["delete-room"])) {
            $id = $_POST["room-id"];
            $sql = "DELETE FROM rooms WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $id);
            $val = $stmt->execute();

            if ($val) {
                header('Location: appointments.php?success=true');
            } else {
                header('Location: appointments.php?success=false');
            }
        }
        if (isset($_POST['complete'])) {
            $sql = "UPDATE upcomingappointments SET status = 'Completed' WHERE aptDate < '$date'";
            $val = $conn->query($sql);

            if ($val) {
                header('Location: appointments.php?success=true');
            } else {
                header('Location: appointments.php?success=true');
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
    <title>Document</title>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-[#90a7c1] to-slate-600">
    <div class="flex flex-1 w-full h-full">
        <?php include 'includes/admin-sidebar.php' ?>
        <div class="flex flex-col grow">
            <div class="bg-white p-3 max-w-[90%] lg:max-w-[70%] w-full mx-auto my-auto rounded-md">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                    <table class="border-collapse w-full border border-slate-500 my-2">
                        <thead>
                            <tr class="text-left bg-slate-800 text-white text-xl font-semibold">
                                <th class="border border-slate-600">Appointment Date</th>
                                <th class="border border-slate-600">Appointment Time</th>
                                <th class="border border-slate-600">Room</th>
                                <th class="border border-slate-600">Manage Room</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($upcomingappointments as $row): ?>
                            <tr class="bg-slate-500 text-white hover:bg-slate-600 border border-slate-400">
                                <td><?= date('F j, Y', strtotime($row['aptDate'])) ?></td>
                                <td><?= date('g:i A', strtotime($row['aptTime'])) ?></td>
                                <td><?= $row['room'] ?></td>
                                <td><button class="bg-red-500 text-white px-4 py-1 rounded" name="delete-room">Delete
                                        Room</button></td>
                            </tr>
                            <input type="hidden" name="room-id" value="<?php echo $row['room'] ?>" />
                            <?php endforeach ?>
                        </tbody>
                    </table>
                    <?php
                         if (count($upcomingappointments) == 0) {
                            ?>
                    <p class="text-center font-medium text-lg">No Appointments</p>
                    <?php
                         }
                    ?>
                    <?php if ($number_of_pages > 1) {
                        ?>
                    <div class="w-full flex items-center justify-end">
                        <div class="flex items-center space-x-2 bg-slate-900/70 text-white rounded px-2 py-1">
                            <a class="flex items-center" href="appointments.php?page=<?php 
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
                                        href='appointments.php?page=$p'>$p</a>
                                    ";
                                    } else {
                                    echo "
                                    <a class='bg-slate-800/80 px-2 py-0.5 rounded text-white'
                                        href='appointments.php?page=$p'>$p</a>
                                    ";
                                    }
                                    ?>
                                <?php
                                        }
                                    ?>
                            </div>
                            <a class="flex items-center" href="appointments.php?page=<?php 
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
                </form>
                <?php
                    if (count($upcomingappointments) > 0) {
                        ?>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                    <div class="w-full flex items-center justify-end">
                        <button class="bg-green-500 text-white px-4 py-2 rounded" name="complete">Mark Appointments as
                            Complete</button>
                    </div>
                </form>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>
</body>

</html>