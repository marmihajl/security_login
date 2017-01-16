<?php
if ($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}
$dbHandler = new mysqli('localhost', 'root', '', 'sis');
$dbHandler->set_charset("utf8");
if(!empty($_POST) && !empty($_GET)){
    $email = $_GET["mail"];
    $lozinka = $_POST["lozinka"];
    $ponovljenjaLozinka = $_POST["pLozinka"];
    if($lozinka !== "" && $ponovljenjaLozinka !== ""){
        if(preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{10,}/", $lozinka)){
            if($lozinka === $ponovljenjaLozinka){
                $upit = "SELECT salt FROM lozinka WHERE korisnik = (SELECT id FROM korisnici WHERE email = '$email');";
                $salt = $dbHandler->query($upit)->fetch_assoc()['salt'];
                $lozinkaSalt = $lozinka.$salt;
                $hashLozinka = password_hash($lozinkaSalt, PASSWORD_DEFAULT);
                $upit = "UPDATE korisnici SET lozinka = '$hashLozinka' WHERE email = '$email';";
                $dbHandler->query($upit);
                header("Location:index.php");
            }  else {
                echo 'Lozinka i ponovljena lozinka nisu iste!';
                exit();
            }
        }  else {
            echo 'Lozinka mora sadrzavati minimalno 10 znakova:1 malo, 1 veliko slovo, 1 broj i 1 specijalni znak.';
            exit();
        }
    }  else {
        echo 'Lozinka i ponovljena lozinka moraju biti iste';
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
            <label>Lozinka: </label><input type="password" id="lozinka" name="lozinka"/><br>
            <label>Ponovljena lozinka: </label><input type="password" id="pLozinka" name="pLozinka"/><br>
            <input type="submit" value="Promijeni lozinku!"/>
        </form>
    </body>
</html>