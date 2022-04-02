<?php 
    // session_start();
    // if (!isset($_COOKIE['auth'])){
    //     header('Location: login.php');
    // }
    require_once 'renewSession.php';
    require_once 'conn.php';

    if ($_COOKIE['accountType'] == "client"){
        $sql = "SELECT FName, LName FROM client WHERE AccountId = ?";
    } else {
        $sql = "SELECT FName, LName FROM physician WHERE AccountId = ?";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $_COOKIE['accountId']);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();
    $name = $value->FName." ". $value->LName;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/animations.css" />
    <?php include 'includes/header.html'?>
    <script type="module" src="js/bundle.js"></script>
    <title>Therapy Session - MediStation</title>
    <style>
    body {
        overflow: overlay
    }

    ::-webkit-scrollbar {
        width: 7px;
    }

    ::-webkit-scrollbar-track {
        display: none;
    }

    ::-webkit-scrollbar-thumb {
        background: #f3f3f3;
        border-radius: 99999px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    </style>
</head>

<body class="bg-slate-700 w-full">
    <div class="fixed top-[50%] left-[50%] translate-x-[-50%] translate-y-[-50%] items-center justify-center bg-black/70 rounded text-white px-4 py-2 hidden space-x-2"
        id="reconnect">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 animate-spin rotate-180" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <p id="connect-text">Reconnecting</p>
    </div>
    <div class="flex flex-col w-full min-h-screen items-center overflow-auto">
        <div class="grow flex items-center justify-between w-full">
            <div class="grow flex-1 flex items-center justify-center">
                <div id="main" class="flex flex-wrap items-center justify-center gap-2">
                </div>
            </div>
            <div class="h-[calc(100vh-56px)] lg:w-[25%] w-[45%] bg-slate-400 chat hidden" id="chat">
                <div class="flex flex-col h-full">
                    <div class="text-center font-bold text-3xl text-emerald-400 sticky top-0 bg-white py-1">
                        <h3>Therapy Chat</h3>
                    </div>
                    <div class="grow overflow-auto flex flex-col space-y-2 w-full py-1" id="message-container">
                        <!-- <div class="flex mx-2">
                            <div
                                class="flex flex-col bg-blue-500 text-white px-2 py-1 rounded-lg min-w-[40%] max-w-[85%]">
                                <p>Hey</p>
                                <span
                                    class="text-xs text-right text-gray-200 justify-end text-ellipsis overflow-hidden">Jonathan
                                    Johnson</span>
                            </div>
                        </div> -->
                    </div>
                    <div class="bg-white p-2">
                        <div class="flex item-center space-x-2">
                            <div class="bg-slate-800 rounded-full px-2 grow flex items-center">
                                <input class="outline-none bg-transparent text-white grow p-1"
                                    placeholder="Type your message here" id="message-input" />
                            </div>
                            <button class="bg-blue-500 rounded-full p-1 items-center justify-center text-white hidden"
                                id="send-button">
                                <span class="material-icons">
                                    send
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full bg-black/95">
            <div class="flex items-center justify-center max-w-md mx-auto space-x-8 py-2">
                <div id="muted">
                    <button class="p-2 rounded bg-gray-700 text-white flex items-center justify-center" id="old-button">
                        <span class="material-icons">
                            keyboard_voice
                        </span>
                    </button>
                </div>
                <div id="video">
                    <button class="p-2 rounded bg-gray-700 text-white flex items-center justify-center" id="old-video">
                        <span class="material-icons">
                            videocam
                        </span>
                    </button>
                </div>
                <button class="p-2 rounded bg-gray-700 text-white flex items-center justify-center" id="chat-button">
                    <span class="material-icons">
                        chat
                    </span>
                </button>
                <?php
                    if ($_COOKIE['accountType'] != 'client'){
                        echo '
                        <div id="record">
                            <button class="p-2 rounded bg-gray-700 text-white flex items-center justify-center"
                                id="record-button">
                                <span class="material-icons">
                                    fiber_manual_record
                                </span>
                            </button>
                        </div>
                        ';
                    }
                ?>
                <button class="text-white bg-red-500 p-2 rounded flex items-center justify-center" id="end-call">
                    <span class="material-icons">
                        call_end
                    </span>
                </button>
            </div>
        </div>
    </div>
    <input type="hidden" id="username" value="<?php echo $name ?>" />
    <input type="hidden" id="accountId" value="<?php echo $_COOKIE['accountId'] ?>" />
</body>

</html>