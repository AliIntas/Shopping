<?php
session_start();

if (isset($_POST["submit"])) {
    include("inc/baglan.php");
    $username = $_POST["username"];
    $password = ($_POST["password"]);

    $kul = mysqli_query($baglanti, 'SELECT * FROM user WHERE tcno="' . $username . '" AND password="' . $password . '" AND status=1 AND admin=0');

    if (mysqli_num_rows($kul) > 0) {
        $user = mysqli_fetch_array($kul);
        $_SESSION["username"] = $user["name_surname"];
        $_SESSION["kullanici"] = $user["name_surname"];
        $_SESSION['email'] = $user["email"];
        $_SESSION['tel'] = $user["phone_number"];
        $_SESSION['admin'] = $user["admin"];
        $_SESSION['id'] = $user["id"]; //iletisim.php için
        header("Location: index.php");

        exit();
    } else {
        echo "Kullanıcı adı ,şifre yanlış veya kullanıcı pasif veya Admin";
    }
}
?>


<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="assets/bootstrap.css">
    <link rel="stylesheet" href="assets/style.css">
    <title> Login Page </title>
</head>

<body>
    <div class="loginBackground">
        <div class="login">
            <h2 style="color:black"> <i>Üye Girişi</i></h2> <!-- Rengi düzenle-->
            <form method="POST" action="">
                <input type="text" id="username" name="username" placeholder="Kullanıcı Adı" required>
                <input type="password" id="password" name="password" placeholder="Şifre" required>
                <input type="submit" class="loginButton form-control mt-2" value="Giriş" name="submit">
            </form>
            
            <div class="buttonContainer mt-3">
                <!-- Kayıt Ol Butonu -->
                <div class="giris_kayitButton">
                    <a href="signupPage.php">Kayıt Ol</a>
                </div>
                
                <!-- Satıcı Girişi -->
                <div class="saticiGiris">
                    <a href="supplierLoginPage.php">Satıcı Girişi</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
