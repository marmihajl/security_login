<?php
if ($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}
$dbHandler = new mysqli('localhost', 'root', '', 'sis');
$dbHandler->set_charset("utf8");
if(!empty($_POST)){
    $korisnikoIme = $_POST["korisnickoIme"];
    $lozinka = $_POST["lozinka"];
    $ponovljenjaLozinka = $_POST["pLozinka"];
    $ime = $_POST["ime"];
    $prezime = $_POST["prezime"];
    $email = $_POST["mail"];
    if($korisnikoIme !== "" && $lozinka !== "" && $ponovljenjaLozinka !== "" && $ime !== "" && $prezime !== "" && $email !== ""){
        if(preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{10,}/", $lozinka)){
            if($lozinka === $ponovljenjaLozinka){
                $upit = "SELECT * FROM korisnici WHERE korisnicko_ime = '$korisnikoIme' OR email = '$email';";
                $rezultat = $dbHandler->query($upit);
                if($rezultat->num_rows > 0){
                    echo 'Odabrano korisnicko ime ili email se vec koristi.';
                    exit(0);
                }
                srand(time(0));
                rand();
                $salt = '';
                do {
                    for ($i=0 ; $i<10 ; $i++) {
                        $salt .= chr(rand(97,122));
                    }
                    $rezultat = $dbHandler->query("SELECT * FROM lozinka WHERE salt='$salt' LIMIT 1;");
                } while ($rezultat->num_rows > 0);
                $saltLozinka = $lozinka.$salt;
                $hashLozinka = password_hash($saltLozinka, PASSWORD_DEFAULT);
                $upit = "INSERT INTO korisnici VALUES (default, '$korisnikoIme', '$hashLozinka', '$ime', '$prezime', '$email', default);";
                $dbHandler->query($upit);
                $id = mysqli_insert_id($dbHandler);
                $upit = "INSERT INTO lozinka VALUES($id, '$salt');";
                $dbHandler->query($upit);
                $mail_to = $email;
                $mail_from = "From: marmihajl@foi.hr";
                $mail_subject = "Aktivacija korisnickog racuna";
                $mail_body = "Za aktivaciju korisnickog racuna posjetite link: https://localhost/sisProject/aktivacija.php?akt=".$korisnikoIme;
                mail($mail_to, $mail_subject, $mail_body, $mail_from);
                echo "https://localhost/sisProject/aktivacija.php?akt=".$korisnikoIme;
            }  else {
                echo 'Lozinka i ponovljena lozinka moraju biti iste';
                exit(0);
            }
        }else{
            echo 'Lozinka mora sadrzavati minimalno jedno veliko, jedno malo slovo, jedan specijalni znak, jedan broj i minimalno 10 znakova.';
            exit(0);
        }
    }else{
        echo 'Sva polja moraju biti unesena';
        exit(0);
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
            <label>Korisničko ime: </label><input type="text" id="korisnickoIme" name="korisnickoIme"/><br>
            <label>Lozinka: </label><input type="password" id="lozinka" name="lozinka"/><br>
            <label>Ponovljena lozinka: </label><input type="password" name="pLozinka" id="pLozinka"/><br>
            <label>Ime: </label><input type="text" id="ime" name="ime"/>
            <label>Prezime: </label><input type="text" id="prezime" name="prezime"/><br>
            <label>E-mail: </label><input type="email" id="mail" name="mail"/><br>
            <input type="submit" value="Registriraj se!"/>
        </form>
        <input type="button" value="Prijava" onClick="parent.location='index.php'"/>
    </body>
</html>
