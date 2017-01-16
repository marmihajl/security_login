<?php
if ($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}
$dbHandler = new mysqli('localhost', 'root', '', 'sis');
$dbHandler->set_charset("utf8");
if(!empty($_POST)){
    $korisnickoIme = $_POST["korisnickoIme"];
    $lozinka = $_POST["lozinka"];
    $upit = "SELECT salt FROM lozinka WHERE korisnik = (SELECT id FROM korisnici WHERE korisnicko_ime = '$korisnickoIme');";
    $rezultat = $dbHandler->query($upit);
    if ($rezultat->num_rows > 0){
        $salt = $rezultat->fetch_assoc()['salt'];
        $lozinkaSalt = $lozinka.$salt;
        $hashLozinka = password_hash($lozinkaSalt, PASSWORD_DEFAULT);
        $upit = "SELECT active FROM korisnici WHERE korisnicko_ime = '$korisnickoIme';";
        $rezultat = (bool)$dbHandler->query($upit)->fetch_assoc()['active'];
        if(!$rezultat){
            echo 'Niste aktivirali korisnicki racun.<br>Provjerite mail.';
            exit();
        }
        $upit = "SELECT lozinka FROM korisnici WHERE korisnicko_ime = '$korisnickoIme';";
        $rezultat = $dbHandler->query($upit)->fetch_assoc()['lozinka'];
        if(password_verify($lozinkaSalt, $rezultat)){
            echo 'Uspjesna prijava';
            exit();
        }else{
            echo 'Unjeli ste pogrešno korisnicko ime i/ili lozinku';
            exit();
        }
    }  else {
        echo 'Unjeli ste pogrešno korisnicko ime i/ili lozinku';
            exit();
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
            <input type="submit" value="Prijavi se!"/>
        </form>
        <input type="button" value="Zaboravljena lozinka" onClick="parent.location='lozinka.php'"/>
        <input type="button" value="Registracija" onClick="parent.location='registracija.php'"/>
    </body>
</html>
