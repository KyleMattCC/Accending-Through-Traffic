<?php

function getDataServer(){
    //connecting to server
    $service_url = 'https://playground.devicehive.com:443/api/rest/device/notification/poll?waitTimeout=30';
    echo '1';

    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    echo '2';

    //change auth
    $auth = 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJwYXlsb2FkIjp7ImEiOlsyLDMsNCw1LDYsNyw4LDksMTAsMTEsMTIsMTUsMTYsMTddLCJlIjoxNTMyNzM5NTAyOTg4LCJ0IjoxLCJ1IjoyNzM5LCJuIjpbIjI3MTIiXSwiZHQiOlsiKiJdfX0.W8yomu6zABZO1TotUoWUONfDKqV9oUJhRFNiZcsPIzE';
    echo '3';
    $request_headers = array();
    $request_headers[] = 'Authorization: ' . $auth;
    echo '4';

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
    echo 'helloooooo2';
    $subStrNotif2 = explode("{", $subStrNotif);

    echo 'jello';

    foreach ($subStrNotif2 as $i) {


        if ($i != null) {
            //store only 0 and 1

            echo 'helloooooo';

            //remove ,
            $i = explode(", ", $i);
            $i[1] = substr($i[1], 0, strlen($i[1]) - 1);
            echo '5';

            //write to db
            writeDataDb(substr($i[0], 14, strlen($i[0]) - 2),
                substr($i[1], 9, strlen($i[1]) - 1),
                $deviceId,
                $networkId,
                $deviceTypeId,
                $timestamp,
                $retrievalId);

        }
        echo '6';
    }
    var_export($decoded->response);
    echo '7';
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


    $sqlAdd = "INSERT INTO camera_output (class_name, score, deviceId, networkId, deviceTypeId, timestamp, retrievalId) VALUES (" . $class_name . ", 0" . ", " . $deviceId . ", " . $networkId . ", " . $deviceTypeId . ", " . $timestamp . ", " . $retrievalId . ")";
    echo $sqlAdd;
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



function getTotalNumClassType($class_name){
    //get content
    $sql = "SELECT * FROM camera_output WHERE class_name LIKE '" . $class_name . "'";

    return getDataBasedQuery($sql);
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


    //chewy's code

    //hardcoded low med high
    $lowTraffic = 25;
    $medTraffic = 50;
    $highTraffic = 75;

    //compute value per cam
    $totalPoints = $pointsCam1 + $pointsCam2;

    $cam1Percent = ($pointsCam1 / $totalPoints) * 100;
    $cam2Percent = ($pointsCam2 / $totalPoints) * 100;


    $finalVal1 = 0;
    $finalVal2 = 0;

    //0 = low
    //1 = med
    //2 = high

    //get cam 1 finalval
    if($cam1Percent <= $lowTraffic)
        $finalVal1 = 0;
    else if($cam1Percent <= $medTraffic)
        $finalVal1 = 1;
    else
        $finalVal1 = 2;


    //get cam 2 finalval
    if($cam2Percent <= $lowTraffic)
        $finalVal2 = 0;
    else if($cam2Percent <= $medTraffic)
        $finalVal2 = 1;
    else
        $finalVal2 = 2;



    /*
    0 + 0 = 0
    1 + 0 = 1
    2 + 0 = 2

    0 + 1 = 0
    1 + 1 = 1
    2 + 1 = 2

    0 + 2 = 0
    1 + 2 = 1
    2 + 2 = 2
    */


    $ret1 = 0;
    $ret2 = 0;
    //Get first returning val
    if($finalVal1 == 0 && $finalVal2 == 0)
        $ret1 = 0;
    else if($finalVal1 == 1 && $finalVal2 == 1)
        $ret1 = 1;
    else if($finalVal1 == 2 && $finalVal2 == 2)
        $ret1 = 2;
    else if($finalVal1 == 0 && $finalVal2 == 0)
        $ret1 = 0;
    else if($finalVal1 == 1 && $finalVal2 == 1)
        $ret1 = 1;
    else if($finalVal1 == 2 && $finalVal2 == 2)
        $ret1 = 2;
    else if($finalVal1 == 0 && $finalVal2 == 0)
        $ret1 = 0;
    else if($finalVal1 == 1 && $finalVal2 == 1)
        $ret1 = 1;
    else if($finalVal1 == 2 && $finalVal2 == 2)
        $ret1 = 2;

    //Get second returning val
    if($finalVal2 == 0 && $finalVal1 == 0)
        $ret1 = 0;
    else if($finalVal2 == 1 && $finalVal1 == 1)
        $ret1 = 1;
    else if($finalVal2 == 2 && $finalVal1 == 2)
        $ret1 = 2;
    else if($finalVal2 == 0 && $finalVal1 == 0)
        $ret1 = 0;
    else if($finalVal2 == 1 && $finalVal1 == 1)
        $ret1 = 1;
    else if($finalVal2 == 2 && $finalVal1 == 2)
        $ret1 = 2;
    else if($finalVal2 == 0 && $finalVal1 == 0)
        $ret1 = 0;
    else if($finalVal2 == 1 && $finalVal1 == 1)
        $ret1 = 1;
    else if($finalVal2 == 2 && $finalVal1 == 2)
        $ret1 = 2;



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


function getDataBasedQueryArray($sql){
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

    return $result;
}

function testHello(){
    return 11230;
}


//readCurrDataDb("2018-07-24T20:17:55.655");



//UI components
//Pie chart
$currNumCar = 0;
$currNumTruck = 0;


$count = 0;
$currTime = '';
$class_name_car = "bottle";
$class_name_truck = "cup";


//while (true) {
//retrieve data every second from server

echo 'gasdf';

getDataServer();


echo 'got data';

if($count %4==0){
    readCurrDataDb($currTime);
}



//getTotal num car
//$currNumCar = getTotalNumClassType($class_name_car);
//$currNumTruck = getTotalNumClassType($class_name_truck);

//    sleep(1);
//
//    $count++;
//
//
//}

?>

<html>
    <head>

    </head>

    <body>

    </body>
</html>
