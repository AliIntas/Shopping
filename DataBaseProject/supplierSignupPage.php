<?php







?>


<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="assets/bootstrap.css">
    <link rel="stylesheet" href="assets/style.css">
    <title>Kayıt Sayfası</title>
</head>

<body>
    <div class="kayitBackground">
        <div class="kayit">
            <h2 class="mb-1"><i>Satıcı Kayıt</i> </h2>
            <form method="POST" action="">
                <!--Sirket Adı -->
                <input type="text" name="name" placeholder=" Şirket Adı" required>
                <!-- Eposta -->
                <input type="text" name="eposta" placeholder=" Sirket E-mail" required>
                <!-- Şifre -->
                <input type="password" name="password" placeholder="Password" required>
                <!--Sirket Adres -->
                <input type="text" name="address" placeholder="Sirket Adres" required>
                <!--Sirket Telefon Numarası -->
                <input type="tel" name="phone_number" placeholder="Sirket Telefon Numarası" maxlength="11" required>
                <!-- Gönder Butonu -->
                <input type="submit" class="kayitButton" value="Kayıt Ol">
            </form>
            <div class="buttonContainer mt-3">
                <!-- Satıcı Kayıt Ol Butonu -->
                <div class="giris_kayitButton">
                    <a href="supplierLoginPage.php"> Satıcı Giriş</a>
                </div>
            </div>
        </div>
    </div>

    </div>
</body>

</html>