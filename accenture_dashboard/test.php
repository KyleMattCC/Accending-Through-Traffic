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
    else if($finalVal1 == 1 && $finalVal2 == 0)
        $ret1 = 1;
    else if($finalVal1 == 2 && $finalVal2 == 0)
        $ret1 = 2;
    else if($finalVal1 == 0 && $finalVal2 == 1)
        $ret1 = 0;
    else if($finalVal1 == 1 && $finalVal2 == 1)
        $ret1 = 1;
    else if($finalVal1 == 2 && $finalVal2 == 1)
        $ret1 = 2;
    else if($finalVal1 == 0 && $finalVal2 == 2)
        $ret1 = 0;
    else if($finalVal1 == 1 && $finalVal2 == 2)
        $ret1 = 1;
    else if($finalVal1 == 2 && $finalVal2 == 2)
        $ret1 = 2;

    //Get second returning val
    if($finalVal2 == 0 && $finalVal1 == 0)
        $ret1 = 0;
    else if($finalVal2 == 1 && $finalVal1 == 0)
        $ret1 = 1;
    else if($finalVal2 == 2 && $finalVal1 == 0)
        $ret1 = 2;
    else if($finalVal2 == 0 && $finalVal1 == 1)
        $ret1 = 0;
    else if($finalVal2 == 1 && $finalVal1 == 1)
        $ret1 = 1;
    else if($finalVal2 == 2 && $finalVal1 == 1)
        $ret1 = 2;
    else if($finalVal2 == 0 && $finalVal1 == 2)
        $ret1 = 0;
    else if($finalVal2 == 1 && $finalVal1 == 2)
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


//readCurrDataDb("2018-07-24T20:17:55.655");

/*
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
*/










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


<!--<html>
<head>
    geogram
</head>
<body>
<div id="dom-target" style="display: none;">
    //php tag
/*    $testing = 0;
    $testing = testHello();
    echo htmlspecialchars($testing); /* You have to escape because the result
                                           will not be valid HTML otherwise. */
    */?>
</div>
<script>
    var div = document.getElementById("dom-target");
    var myData = div.textContent;
    console.log(myData);
</script>

</body>
</html>-->

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>MMDA Traffic Light Dashboard</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="css/themes/all-themes.css" rel="stylesheet" />
</head>

<style>

    #white{
        color:white;
    }
</style>
<body class="theme-red">
<!-- Page Loader -->
<div class="page-loader-wrapper">
    <div class="loader">
        <div class="preloader">
            <div class="spinner-layer pl-red">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
        <p>Please wait...</p>
    </div>
</div>
<!-- #END# Page Loader -->
<!-- Overlay For Sidebars -->
<div class="overlay"></div>
<!-- #END# Overlay For Sidebars -->
<!-- Search Bar -->
<!-- #END# Search Bar -->
<!-- Top Bar -->
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="javascript:void(0);" class="bars"></a>
            <a class="navbar-brand" href="test.php">MMDA Traffic Light Dashboard</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <!-- Notifications -->
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                        <i class="material-icons">notifications</i>
                        <span class="label-count">7</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">NOTIFICATIONS</li>
                        <li class="body">
                            <ul class="menu">
                                <li>
                                    <a href="javascript:void(0);">
                                        <div class="icon-circle bg-light-green">
                                            <i class="material-icons">person_add</i>
                                        </div>
                                        <div class="menu-info">
                                            <h4>ADD NOTIFCATION HERE</h4>
                                            <p>
                                                <i class="material-icons">access_time</i> 14 mins ago
                                            </p>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="javascript:void(0);">View All Notifications</a>
                        </li>
                    </ul>
                </li>
                <!-- #END# Notifications -->
                <li class="pull-right"><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i class="material-icons">more_vert</i></a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- #Top Bar -->
<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <!-- User Info -->
        <div class="user-info">
            <div class="image">
                <img src="images/user.png" width="48" height="48" alt="User" />
            </div>
            <div class="info-container">
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">MMDA Officer</div>
                <div class="email">mmda@gmail.com.ph</div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:void(0);"><i class="material-icons">person</i>Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">settings</i>Settings</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">input</i>Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN NAVIGATION</li>
                <li class="active">
                    <a href="test.php">
                        <i class="material-icons">home</i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="roads.html">
                        <i class="material-icons">traffic</i>
                        <span>Roads</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
                &copy; 2017 - 2018 <a href="javascript:void(0);">MMDA Traffic Light Dashboard</a>.
            </div>
            <div class="version">
                <b>Version: </b> 1.0.5
            </div>
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->
    <!-- Right Sidebar -->
    <aside id="rightsidebar" class="right-sidebar">
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade  in active in active" id="settings">
                <div class="demo-settings">
                    <p>GENERAL SETTINGS</p>
                    <ul class="setting-list">
                        <li>
                            <span>Report Panel Usage</span>
                            <div class="switch">
                                <label><input type="checkbox" checked><span class="lever"></span></label>
                            </div>
                        </li>
                        <li>
                            <span>Email Redirect</span>
                            <div class="switch">
                                <label><input type="checkbox"><span class="lever"></span></label>
                            </div>
                        </li>
                    </ul>
                    <p>SYSTEM SETTINGS</p>
                    <ul class="setting-list">
                        <li>
                            <span>Notifications</span>
                            <div class="switch">
                                <label><input type="checkbox" checked><span class="lever"></span></label>
                            </div>
                        </li>
                        <li>
                            <span>Auto Updates</span>
                            <div class="switch">
                                <label><input type="checkbox" checked><span class="lever"></span></label>
                            </div>
                        </li>
                    </ul>
                    <p>ACCOUNT SETTINGS</p>
                    <ul class="setting-list">
                        <li>
                            <span>Offline</span>
                            <div class="switch">
                                <label><input type="checkbox"><span class="lever"></span></label>
                            </div>
                        </li>
                        <li>
                            <span>Location Permission</span>
                            <div class="switch">
                                <label><input type="checkbox" checked><span class="lever"></span></label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </aside>
    <!-- #END# Right Sidebar -->
</section>

<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <h2>DASHBOARD</h2>
        </div>


        <!-- CPU Usage -->
        <div class="row clearfix">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="header">
                        <div class="row clearfix">
                            <div class="col-xs-12 col-sm-6">
                                <h2>CURRENT TRAFFIC LOAD</h2>
                            </div>
                            <div class="col-xs-12 col-sm-6 align-right">
                                <div class="switch panel-switch-btn">
                                    <span class="m-r-10 font-12">REAL TIME</span>
                                    <label>OFF<input type="checkbox" id="realtime" checked><span class="lever switch-col-cyan"></span>ON</label>
                                </div>
                            </div>
                        </div>
                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="javascript:void(0);">Action</a></li>
                                    <li><a href="javascript:void(0);">Another action</a></li>
                                    <li><a href="javascript:void(0);">Something else here</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="body">
                        <div id="real_time_chart" class="dashboard-flot-chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #END# CPU Usage -->

        <!-- Visitors -->
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" >
            <div class="card">
                <div class="header">
                    <h2 class=>AVERAGE TRAFFIC</h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown" >
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">Action</a></li>
                                <li><a href="javascript:void(0);">Another action</a></li>
                                <li><a href="javascript:void(0);">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="body bg-white">
                        <div class="sparkline" data-type="line" data-spot-Radius="4" data-highlight-Spot-Color="rgb(233, 30, 99)" data-highlight-Line-Color="#fff"
                             data-min-Spot-Color="rgb(233,30,90)" data-max-Spot-Color="rgb(233,30,90)" data-spot-Color="rgb(233,30,90)"
                             data-offset="90" data-width="100%" data-height="92px" data-line-Width="2" data-line-Color="rgba(233,30,90,0.7)"
                             data-fill-Color="rgba(0, 188, 212, 0)">
                            12,10,9,6,5,6,10,5,7,5,12,13,7,12,11
                        </div>
                        <ul class="dashboard-stat-list">
                            <li>
                                TODAY
                                <span class="pull-right"><b>1 200</b> <small>CARS</small></span>
                            </li>
                            <li>
                                YESTERDAY
                                <span class="pull-right"><b>3 872</b> <small>CARS</small></span>
                            </li>
                            <li>
                                LAST WEEK
                                <span class="pull-right"><b>26 582</b> <small>CARS</small></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- #END# Visitors  -->
        <!-- Browser Usage -->
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="header">
                    <h2>BROWSER USAGE</h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">Action</a></li>
                                <li><a href="javascript:void(0);">Another action</a></li>
                                <li><a href="javascript:void(0);">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div id="donut_chart" class="dashboard-donut-chart"></div>
                </div>
            </div>
        </div>
        <!-- #END# Browser Usage -->
    </div>
    </div>
</section>

<!-- Jquery Core Js -->
<script src="plugins/jquery/jquery.min.js"></script>

<!-- Bootstrap Core Js -->
<script src="plugins/bootstrap/js/bootstrap.js"></script>

<!-- Select Plugin Js -->
<script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>

<!-- Slimscroll Plugin Js -->
<script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

<!-- Waves Effect Plugin Js -->
<script src="plugins/node-waves/waves.js"></script>

<!-- Jquery CountTo Plugin Js -->
<script src="plugins/jquery-countto/jquery.countTo.js"></script>

<!-- Morris Plugin Js -->
<script src="plugins/raphael/raphael.min.js"></script>
<script src="plugins/morrisjs/morris.js"></script>

<!-- ChartJs -->
<script src="plugins/chartjs/Chart.bundle.js"></script>

<!-- Flot Charts Plugin Js -->
<script src="plugins/flot-charts/jquery.flot.js"></script>
<script src="plugins/flot-charts/jquery.flot.resize.js"></script>
<script src="plugins/flot-charts/jquery.flot.pie.js"></script>
<script src="plugins/flot-charts/jquery.flot.categories.js"></script>
<script src="plugins/flot-charts/jquery.flot.time.js"></script>

<!-- Sparkline Chart Plugin Js -->
<script src="plugins/jquery-sparkline/jquery.sparkline.js"></script>

<!-- Custom Js -->
<script src="js/admin.js"></script>
<script src="js/pages/index.js"></script>

<!-- Demo Js -->
<script src="js/demo.js"></script>
</body>

</html>
