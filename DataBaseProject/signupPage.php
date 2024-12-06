<?php
if (isset($_POST["submit"])) {
    include("inc/baglan.php");

    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $birth_date = $_POST["birth_date"];
    $address = $_POST["address"];
    $phone_number = $_POST["phone_number"];

    $sql = "INSERT INTO customer (name, surname, birth_date, address, phone_number) 
            VALUES ('$name', '$surname', '$birth_date', '$address', '$phone_number')";


    mysqli_close($baglanti);
}
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
            <h2 class="mb-1"><i>Müşteri Kayıt</i> </h2>
            <form method="POST" action="">
                <!-- Ad -->
                <input type="text" name="name" placeholder="Ad" required>
                <!-- Soyad -->
                <input type="text" name="surname" placeholder="Soyad" required>
                <!-- Doğum Tarihi -->
                <input type="date" name="birth_date" placeholder="Doğum Tarihi" required>
                <!-- Adres -->
                <input type="text" name="address" placeholder="Adres" required>
                <!-- Telefon Numarası -->
                <input type="tel" name="phone_number" placeholder="Telefon Numarası" maxlength="11"  required>
                <!-- Gönder Butonu -->
                <input type="submit" class="kayitButton" value="Kayıt Ol">
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