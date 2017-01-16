<?php

$dbHandler = new mysqli('localhost', 'root', '', 'sis');
$dbHandler->set_charset("utf8");

if(!empty($_GET)){
    $korisnickoIme = $_GET["akt"];
    $upit = "SELECT * FROM korisnici WHERE korisnicko_ime = '$korisnickoIme';";
    $rezultat = $dbHandler->query($upit);
    if ($rezultat->num_rows > 0){
        $upit = "UPDATE korisnici SET active = true WHERE korisnicko_ime = '$korisnickoIme';";
        $dbHandler->query($upit);
        header("Location:index.php");
    }
}

?>