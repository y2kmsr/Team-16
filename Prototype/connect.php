<?php
    $host="localhost" ;
    $user="root" ;
    $pass="" ;
    $db="login_team16" ;
    $conn=new mysqli ($host, $user, $pass, $db);

    if($conn -> connect_error) {
    echo "failed to connect to db".$conn -> connect_error;
    }

?>

 <!--  establishes connection to the database -->
