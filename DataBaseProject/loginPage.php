<?php
session_start(); 

if (isset($_POST["submit"])) {
    include("inc/baglan.php");

    // Formdan gelen verileri al
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Kullanıcıyı veritabanında arayın ve kullanıcı adı ile birlikte join yapın
    $secim = "SELECT user.email, user.password, user.authorization, customer.name 
              FROM user 
              JOIN customer ON user.user_ID = customer.user_ID 
              WHERE user.email = '$email'";
              
    $calistir = mysqli_query($baglanti, $secim);

    if ($calistir && mysqli_num_rows($calistir) > 0) {
        // Kullanıcı bulundu, bilgileri al
        $ilgili_kayit = mysqli_fetch_assoc($calistir);

        // Şifre ve yetki kontrolü
        if ($ilgili_kayit["password"] === $password && $ilgili_kayit["authorization"] == 1) {
            // Kullanıcı adı ve e-posta oturuma ekleniyor
            $_SESSION["email"] = $ilgili_kayit["email"];
            $_SESSION["kulAdı"] = $ilgili_kayit["name"]; // İsim bilgisini oturuma ekliyoruz

            // Ana sayfaya yönlendirme
            header("Location: mainpage.php");
            exit();
        } else {
            $hata = "Şifre yanlış veya yetkisiz giriş!";
        }
    } else {
        $hata = "Bu e-posta ile kayıtlı kullanıcı bulunamadı!";
    }

    // Veritabanı bağlantısını kapat
    mysqli_close($baglanti);
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/bootstrap.css">
    <link rel="stylesheet" href="assets/style.css">
    <title>Giriş Sayfası</title>
</head>

<body>
    <div class="loginBackground">
        <div class="login">
            <h2 style="color:black">Üye Girişi</h2>

            <!-- Giriş Formu -->
            <form method="POST" action="">
                <input type="text" id="email" name="email" placeholder="E-posta" required>
                <input type="password" id="password" name="password" placeholder="Şifre" required>
                <input type="submit" class="loginButton form-control mt-2" value="Giriş" name="submit">
            </form>

            <!-- Kayıt Ol ve Satıcı Girişi -->
            <div class="buttonContainer mt-3">
                <div class="giris_kayitButton">
                    <a href="signupPage.php">Kayıt Ol</a>
                </div>
                <div class="saticiGiris">
                    <a href="supplierLoginPage.php">Satıcı Girişi</a>
                </div>
            </div>
            <?php
            // Hata mesajı varsa göster
            if (isset($hata)) {
                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($hata) . '</div>';
            }
            ?>
        </div>

    </div>
</body>

</html>