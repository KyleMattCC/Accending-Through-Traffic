<?php
    $intersection = $_GET["intersection"];
    /*
    $currTimeToUse = date("Y-m-d") . "T" . date("h:i:sa");
    //deviceId
    $cam1 = 'janzxps';
    $cam2 = 'janzxps';

    //class_name
    $class1 = 'car';
    $class2 = 'truck';

    //$class1 = 'bottle';
    //$class2 = 'cup';


    //cam1
    //get cam1 content car
    $sqlCam1Class1 = "SELECT * FROM camera_output WHERE timestamp LIKE '" . $currTimeToUse . "' AND deviceId LIKE'" . $cam1 . "' AND class_name LIKE '" . $class1 . "'";

    //get cam1 content truck
    $sqlCam1Class2 = "SELECT * FROM camera_output WHERE timestamp LIKE '" . $currTimeToUse . "' AND deviceId LIKE'" . $cam1 . "' AND class_name LIKE '" . $class2 . "'";


    //cam2
    //get cam2 content car
    $sqlCam2Class1 = "SELECT * FROM camera_output WHERE timestamp LIKE '" . $currTimeToUse . "' AND deviceId LIKE'" . $cam2 . "' AND class_name LIKE '" . $class1 . "'";

    //get cam2 content truck
    $sqlCam2Class2 = "SELECT * FROM camera_output WHERE timestamp LIKE '" . $currTimeToUse . "' AND deviceId LIKE'" . $cam2 . "' AND class_name LIKE '" . $class2 . "'";

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
    if ($cam1Percent <= $lowTraffic)
        $finalVal1 = 0;
    else if ($cam1Percent <= $medTraffic)
        $finalVal1 = 1;
    else
        $finalVal1 = 2;


    //get cam 2 finalval
    if ($cam2Percent <= $lowTraffic)
        $finalVal2 = 0;
    else if ($cam2Percent <= $medTraffic)
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

/*
    $ret1 = 0;
    $ret2 = 0;
    */
    //Get first returning val
if($intersection == "0"){
    echo "0";
    /*
    if ($finalVal1 == 0 && $finalVal2 == 0)
        echo "0";
    else if ($finalVal1 == 1 && $finalVal2 == 0)
        echo "1";
    else if ($finalVal1 == 2 && $finalVal2 == 0)
        echo "2";
    else if ($finalVal1 == 0 && $finalVal2 == 1)
        echo "0";
    else if ($finalVal1 == 1 && $finalVal2 == 1)
        echo "0";
    else if ($finalVal1 == 2 && $finalVal2 == 1)
        echo "2";
    else if ($finalVal1 == 0 && $finalVal2 == 2)
        echo "0";
    else if ($finalVal1 == 1 && $finalVal2 == 2)
        echo "0";
    else if ($finalVal1 == 2 && $finalVal2 == 2)
        echo "2";*/
}

else{
    echo "2";
    //Get second returning val
    /*
    if ($finalVal2 == 0 && $finalVal1 == 0)
        $ret1 = 0;
    else if ($finalVal2 == 1 && $finalVal1 == 0)
        $ret1 = 1;
    else if ($finalVal2 == 2 && $finalVal1 == 0)
        $ret1 = 2;
    else if ($finalVal2 == 0 && $finalVal1 == 1)
        $ret1 = 0;
    else if ($finalVal2 == 1 && $finalVal1 == 1)
        $ret1 = 0;
    else if ($finalVal2 == 2 && $finalVal1 == 1)
        $ret1 = 2;
    else if ($finalVal2 == 0 && $finalVal1 == 2)
        $ret1 = 0;
    else if ($finalVal2 == 1 && $finalVal1 == 2)
        $ret1 = 0;
    else if ($finalVal2 == 2 && $finalVal1 == 2)
        $ret1 = 2;*/
}
?>
