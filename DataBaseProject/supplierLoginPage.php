<?php







?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="assets/bootstrap.css">
    <link rel="stylesheet" href="assets/style.css">
    <title>Supplier Login Page </title>
</head>

<body>
    <div class="loginBackground">
        <div class="login">
            <h2> <i>Satıcı Girişi</i></h2>
            <form method="POST" action="">
                <input type="text" id="username" name="username" placeholder="Satıcı Kullanıcı Adı" required>
                <input type="password" id="password" name="password" placeholder="Şifre" required>
                <input type="submit" class="loginButton form-control mt-2" value="Giriş" name="submit">
            </form>
            
            <div class="buttonContainer mt-3">
                <!-- Satıcı Kayıt Ol Butonu -->
                <div class="giris_kayitButton">
                    <a href="supplierSignupPage.php"> Satıcı Kayıt</a>
                </div>
                
                <!-- Üye Girişi -->
                <div class="KullanıcıGiris">
                    <a href="loginPage.php">Üye Girişi</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>