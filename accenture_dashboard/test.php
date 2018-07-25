<?php

function getDataServer(){
    //connecting to server
    $service_url = 'https://playground.devicehive.com:443/api/rest/device/notification/poll?waitTimeout=30';
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //change auth
    $auth = 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJwYXlsb2FkIjp7ImEiOlsyLDMsNCw1LDYsNyw4LDksMTAsMTEsMTIsMTUsMTYsMTddLCJlIjoxNTMyNDY1MjIyMDExLCJ0IjoxLCJ1IjoyNzM5LCJuIjpbIjI3MTIiXSwiZHQiOlsiKiJdfX0.QM-t_EYY2_f4QCPy39sPNTRWOiz3HCY0qJguIVFfqMw';
    $request_headers = array();
    $request_headers[] = 'Authorization: ' . $auth;

    curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);


    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
        die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($curl);

    $decoded = json_decode($curl_response);
    if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
        die('error occured: ' . $decoded->response->errormessage);
    }
    echo 'response ok! printing retrieved';

    $encoded = json_encode($decoded);


    echo nl2br("\n");
    echo json_encode($decoded);

    //printing content of json
    echo nl2br("\n encoded: ");
    echo $encoded;

    echo nl2br("\n retrievalId: ");
    echo nl2br(json_encode($decoded[0]->id));
    $retrievalId = json_encode($decoded[0]->id);

    echo nl2br("\n deviceId: ");
    echo json_encode($decoded[0]->deviceId);
    $deviceId = json_encode($decoded[0]->deviceId);

    echo nl2br("\n networkId: ");
    echo json_encode($decoded[0]->networkId);
    $networkId = json_encode($decoded[0]->networkId);

    echo nl2br("\n deviceTypeId: ");
    echo json_encode($decoded[0]->deviceTypeId);
    $deviceTypeId = json_encode($decoded[0]->deviceTypeId);

    echo nl2br("\n timestamp: ");
    echo json_encode($decoded[0]->timestamp);
    $timestamp = json_encode($decoded[0]->timestamp);
    $currTime = $timestamp;

    echo nl2br("\n notif: ");
    $notifs = json_encode($decoded[0]->notification);

    $subStrNotif = substr($notifs, 2, strlen($notifs) - 4);
    $subStrNotif2 = explode("{", $subStrNotif);

    foreach ($subStrNotif2 as $i) {


        if ($i != null) {
            //store only 0 and 1

            //remove ,
            $i = explode(", ", $i);
            $i[1] = substr($i[1], 0, strlen($i[1]) - 1);

            //write to db
            writeDataDb(substr($i[0], 14, strlen($i[0]) - 2),
                        substr($i[1], 9, strlen($i[1]) - 1),
                        $deviceId,
                        $networkId,
                        $deviceTypeId,
                        $timestamp,
                        $retrievalId);

        }
    }
    var_export($decoded->response);
}


function writeDataDb($class_name, $score, $deviceId, $networkId, $deviceTypeId, $timestamp, $retrievalId){
    $db_host = '127.0.0.1'; // Server Name
    $db_user = 'root'; // Username
    $db_pass = 'root'; // Password
    $db_name = 'accenture_traffic'; // Database Name

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if (!$conn) {
        die ('Failed to connect to MySQL: ' . mysqli_connect_error());
    }

    //insert content
//    $sqlAdd = "INSERT INTO camera_output (class_name, score, deviceId, networkId, deviceTypeId, timestamp, retrievalId) VALUES ('test2', 0.3, 'devId2', 1, 1, 'time2', 1)";

    $sqlAdd = "INSERT INTO camera_output (class_name, score, deviceId, networkId, deviceTypeId, timestamp, retrievalId) VALUES (" . $class_name . ", " . $score . ", " . $deviceId . ", " . $networkId . ", " . $deviceTypeId . ", " . $timestamp . ", " . $retrievalId . ")";

//    echo $sqlAdd;

    $result = mysqli_query($conn, $sqlAdd);

    if (!$result) {
        die ('SQL Error: ' . mysqli_error($conn));
    }
    $conn->close();

}

function readDataDb(){
    $db_host = '127.0.0.1'; // Server Name
    $db_user = 'root'; // Username
    $db_pass = 'root'; // Password
    $db_name = 'accenture_traffic'; // Database Name

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if (!$conn) {
        die ('Failed to connect to MySQL: ' . mysqli_connect_error());
    }

    //get content
    $sql = 'SELECT * FROM camera_output';

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die ('SQL Error: ' . mysqli_error($conn));
    }


    //display content
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"] .
                " - Name: " . $row["class_name"] .
                " - Score: " . $row["score"] .
                " - DeviceId: " . $row["deviceId"] .
                "<br>";

        }
    } else {
        echo "0 results";
    }
    $conn->close();
}


function readCurrDataDb($currTimeToUse){

    //deviceId
    $cam1 = 'janzxps';
    $cam2 = 'janzxps';

    //class_name
    $class1 = 'car';
    $class2 = 'truck';

//    $class1 = 'bottle';
//    $class2 = 'cup';


    //cam1
    //get cam1 content car
    $sqlCam1Class1 = "SELECT * FROM camera_output WHERE timestamp LIKE '" .  $currTimeToUse . "' AND deviceId LIKE'" . $cam1 . "' AND class_name LIKE '" . $class1 . "'";

    //get cam1 content truck
    $sqlCam1Class2 = "SELECT * FROM camera_output WHERE timestamp LIKE '" .  $currTimeToUse . "' AND deviceId LIKE'" . $cam1 . "' AND class_name LIKE '" . $class2 . "'";


    //cam2
    //get cam2 content car
    $sqlCam2Class1 = "SELECT * FROM camera_output WHERE timestamp LIKE '" .  $currTimeToUse . "' AND deviceId LIKE'" . $cam2 . "' AND class_name LIKE '" . $class1 . "'";

    //get cam2 content truck
    $sqlCam2Class2 = "SELECT * FROM camera_output WHERE timestamp LIKE '" .  $currTimeToUse . "' AND deviceId LIKE'" . $cam2 . "' AND class_name LIKE '" . $class2 . "'";

    //retrieve cam1 content from database
    $numCam1Class1 = getDataBasedQuery($sqlCam1Class1);
    $numCam1Class2 = getDataBasedQuery($sqlCam1Class2);

    //retrieve cam2 content from database
    $numCam2Class1 = getDataBasedQuery($sqlCam2Class1);
    $numCam2Class2 = getDataBasedQuery($sqlCam2Class2);

    $pointsCam1 = 0;
    $pointsCam2 = 0;

    $pointsClass1 = 1;
    $pointsClass2 = 5;

    //compute stop light
    $pointsCam1 = ($numCam1Class1 * $pointsClass1) + ($numCam1Class2 * $pointsClass2);
    $pointsCam2 = ($numCam2Class1 * $pointsClass1) + ($numCam2Class2 * $pointsClass2);

    //compare 2 stop light priority
    if($pointsCam1 > $pointsCam2) {
        echo "points cam1: " . $pointsCam1;
        echo "points cam2: " . $pointsCam2;
        echo nl2br("\n");
        echo "green cam1";

        //send data to arduino
    }
    else {
        echo "points cam1: " . $pointsCam1;
        echo "points cam2: " . $pointsCam2;
        echo nl2br("\n");
        echo "green cam2";

        //send data to arduino
    }

}

function getDataBasedQuery($sql){
    $db_host = '127.0.0.1'; // Server Name
    $db_user = 'root'; // Username
    $db_pass = 'root'; // Password
    $db_name = 'accenture_traffic'; // Database Name

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if (!$conn) {
        die ('Failed to connect to MySQL: ' . mysqli_connect_error());
    }

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die ('SQL Error: ' . mysqli_error($conn));
    }


    //display content
    $result = $conn->query($sql);

    //uncomment to see content
    /*
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"] .
                " - Name: " . $row["class_name"] .
                " - Score: " . $row["score"] .
                " - DeviceId: " . $row["deviceId"] .
                "<br>";

        }
    } else {
        echo "0 results";
    }
    */


    $numObjects =  $result->num_rows;
    $conn->close();

    return $numObjects;
}


//readCurrDataDb("2018-07-24T20:17:55.655");


$count = 0;
$currTime = '';
while (true) {
    //retrieve data every second from server
    getDataServer();

    echo 'got data';

    if($count %4==0){
        readCurrDataDb($currTime);
    }

    sleep(1);

    $count++;


}








/*

    $db_host = '127.0.0.1'; // Server Name
    $db_user = 'root'; // Username
    $db_pass = 'root'; // Password
    $db_name = 'accenture_traffic'; // Database Name

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if (!$conn) {
        die ('Failed to connect to MySQL: ' . mysqli_connect_error());
    }

    //insert content
    $sqlAdd = "INSERT INTO camera_output (class_name, score, deviceId, networkId, deviceTypeId, timestamp, retrievalId)
    VALUES ('test2', 0.3, 'devId2', 1, 1, 'time2', 1)";

    $result = mysqli_query($conn, $sqlAdd);

    if (!$result) {
        die ('SQL Error: ' . mysqli_error($conn));
    }



    //get content
    $sql = 'SELECT * FROM camera_output';

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die ('SQL Error: ' . mysqli_error($conn));
    }


    //display content
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"] .
                " - Name: " . $row["class_name"] .
                " - Score: " . $row["score"] .
                " - DeviceId: " . $row["deviceId"] .
                "<br>";

        }
    } else {
        echo "0 results";
    }
    $conn->close();

*/

?>

<html>
<head>
    geogram
</head>
<body>
</body>
</html>