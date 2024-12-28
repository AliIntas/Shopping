<?php
include 'inc/baglan.php';
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['customer_id'])) {
    header("Location: loginPage.php");
    exit();
}
$customer_id = (int)$_SESSION['customer_id'];  // customer_id'yi oturumdan alıyoruz

// Sepet ürünlerini çek
$urunler = mysqli_query(
    $baglanti,
    "
    SELECT p.*, c.Quantity, c.Cart_id 
    FROM cart c
    JOIN product p ON c.Product_id = p.Product_id
    WHERE c.Customer_id = " . $customer_id  // customer_id kullanılıyor
);

$toplam = 0;

// Sepet toplamını hesapla ve Cart_id'yi al
while ($urun = mysqli_fetch_assoc($urunler)) {
    $urun_toplam = $urun['ProductPrice'] * $urun['Quantity'];
    $toplam += $urun_toplam;
    $cart_id = $urun['Cart_id']; // Cart_id'yi alıyoruz
}

// Ödeme işlemi başlatıldı mı kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_number = $_POST['card_number'] ?? '';
    $card_holder = $_POST['card_holder'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    // Form doğrulama
    if (empty($card_number) || empty($card_holder) || empty($expiry_date) || empty($cvv)) {
        $error_message = "Lütfen tüm kart bilgilerini doldurun!";
    } elseif ($toplam > 0) {
        // Veritabanına ödeme bilgilerini kaydetme
        $payment_id = NULL; // Veritabanı otomatik olarak Payment_id'yi oluşturacak

        // 1. Payment tablosuna ödeme kaydını ekleyin
        $payment_sql = "INSERT INTO payment (Cart_id, PaymentStatus,User_id)
                        VALUES ('$cart_id', 1,)"; 

        if (mysqli_query($baglanti, $payment_sql)) {
            // 2. Onlinepayment tablosuna ödeme kart bilgilerini kaydedin
            // Op_id veritabanı tarafından otomatik olarak oluşturulacak
            $onlinepayment_sql = "INSERT INTO onlinepayment (Payment_id, CardNumber, ExpiryDate, CVV)
            VALUES (LAST_INSERT_ID(), '$card_number', '$expiry_date', '$cvv')";

            if (mysqli_query($baglanti, $onlinepayment_sql)) {
                // Ödeme başarılıysa sepeti temizle
                mysqli_query($baglanti, "DELETE FROM cart WHERE Customer_id = $customer_id");

                // Sipariş sayfasına yönlendirme
                header("Location: orders.php");
                exit();
            } else {
                $error_message = "Kart bilgileri kaydedilirken bir hata oluştu.";
            }
        } else {
            $error_message = "Ödeme kaydınız sırasında bir hata oluştu.";
        }
    } else {
        $error_message = "Sepetiniz boş, ödeme yapılamaz!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/bootstrap.css">
    <link rel="stylesheet" href="assets/style.css">
    <title>Ödeme Sayfası</title>
</head>

<body>
    <div class="container mt-5">

        <div class="row">
            <div class="odemeSayfasi col-md-12">
                <h2><i>Ödeme Sayfası</i></h2>
                <h4>Toplam Tutar: <strong><?= $toplam ?> TL</strong></h4>

                <?php if (!empty($error_message)) {
                    echo '<div class="alert alert-danger">' . $error_message . '</div>';
                } ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="card_number">Kart Numarası</label>
                        <input type="text" name="card_number" id="card_number" class="form-control" placeholder="Kart numaranızı giriniz" required>
                    </div>

                    <div class="form-group">
                        <label for="card_holder">Kart Sahibi Adı</label>
                        <input type="text" name="card_holder" id="card_holder" class="form-control" placeholder="Kart sahibinin adını giriniz" required>
                    </div>

                    <div class="form-group">
                        <label for="expiry_date">Son Kullanma Tarihi</label>
                        <input type="text" name="expiry_date" id="expiry_date" class="form-control" placeholder="MM/YY" required>
                    </div>

                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" name="cvv" id="cvv" class="form-control" placeholder="(3 haneli güvenlik kodu)" required>
                    </div>

                    <button type="submit" class="btn btn-success btn-block mt-2">Ödemeyi Tamamla</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
