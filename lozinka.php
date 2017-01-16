<?php
if ($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}
$dbHandler = new mysqli('localhost', 'root', '', 'sis');
$dbHandler->set_charset("utf8");
if(!empty($_POST)){
    $email = $_POST["email"];
    if($email !== ""){
        $upit = "SELECT * FROM korisnici WHERE email = '$email';";
        $rezultat = $dbHandler->query($upit);
        if($rezultat->num_rows > 0){
            $mail_to = $email;
            $mail_from = "From: marmihajl@foi.hr";
            $mail_subject = "Promjena lozinke";
            echo "https://localhost/sisProject/promjena.php?mail=".$email;
            $mail_body = "Za promjenu lozinke posjeti slijedeći link: https://localhost/sisProject/promjena.php?mail=".$email;
            mail($mail_to, $mail_subject, $mail_body, $mail_from);
        }
        else{
            echo 'Uneseni mail ne postoji';
            exit();
        }
    }  else {
        echo 'Niste unjeli mail';
    }
    
}
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <form method="POST" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
            <label>Email: </label><input type="email" id="email" name="email"/><br>
            <input type="submit" value="Pošalji!"/>
        </form>
    </body>
</html>