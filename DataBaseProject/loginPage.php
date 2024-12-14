<?php

if (isset($_POST["submit"])) {
    session_start();
    include("inc/baglan.php");
    $email = $_POST["email"];
    $password = ($_POST["password"]);
    $authorization=1;
    $secim="SELECT * FROM user WHERE   email='$email'";
    $calistir=mysqli_query($baglanti,$secim);
    $kayit_sayisi=mysqli_num_rows($calistir);
    if ($kayit_sayisi> 0) {

        while($ilgili_kayit=mysqli_fetch_assoc($calistir)){
            $authorizationDB=$ilgili_kayit["authorization"];
            if ($authorizationDB==$authorization) {
            $sifre=$ilgili_kayit["password"];
                if ($sifre== $password) {
                $_SESSION   ["username"] = $username;
                header("location:mainpage.php");
                }
            }
        
        }
        
    }
    else{
        echo '<div class="alert alert-danger"role ="allert">
        kullanici adi veya şifre yanlış
        </div>';   
    }
    mysqli_close($baglanti);   
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
                <input type="text" id="email" name="email" placeholder="email" required>
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
