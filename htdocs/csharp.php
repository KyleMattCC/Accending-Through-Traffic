<?php
$username = $_GET["username"];  //make sure you filter these values, before showing them
if($username == "john")
    echo "0";
else
    echo "1";  //$username == "john"

echo date("Y-m-d") . "T" .  date("h:i:sa");
$currDateTime = date("Y-m-d") . "T" .  date("h:i:sa");
echo substr($currDateTime,0, strlen($currDateTime)-2);
?>