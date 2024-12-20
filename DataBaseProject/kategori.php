<?php
session_start(); // Oturumu başlat
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="kategori"> <!-- DB'den kategoriler alınacak -->
        <div>
            <h4>Kategori</h4>
            <ul>
                <li>Kıyafet</li>
                <li>Teknoloji</li>
                <li>Yemek</li>
            </ul>
        </div>

        <div>
            <!-- Kullanıcı durumu -->
            <?php
            if (isset($_SESSION["email"])) { // Kullanıcı giriş yaptıysa
                echo '<p>Hoşgeldiniz, <b>' . htmlspecialchars($_SESSION["kulAdı"]) . '</b></p>';
                echo ' | <a href="logout.php"  text-decoration: none;">Çıkış Yap</a>'; // Çıkış bağlantısı
            } else { // Giriş yapılmamışsa
                echo '<a href="loginPage.php"  text-decoration: none;">Giriş Yap</a>'; // Giriş bağlantısı
            }
            ?>

        </div>
    </div>
</body>

</html>
