<?php
include 'inc/baglan.php';
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['customer_id'])) {
    header("Location: loginPage.php");
    exit();
}
$customer_id = (int)$_SESSION['customer_id'];

// Müşteri bilgilerini çek
$customer_query = mysqli_query(
    $baglanti,
    "SELECT FirstName, LastName, Address FROM customer WHERE Customer_id = $customer_id"
);
$customer_data = mysqli_fetch_assoc($customer_query);

// Müşteri bilgilerini kontrol et
if (!$customer_data) {
    die("Müşteri bilgileri bulunamadı.");
}

$first_name = $customer_data['FirstName'];
$last_name = $customer_data['LastName'];
$address = $customer_data['Address'];

// Sepet ürünlerini çek
$urunler = mysqli_query(
    $baglanti,
    "
    SELECT p.*, c.Quantity, c.Cart_id 
    FROM cart c
    JOIN product p ON c.Product_id = p.Product_id
    WHERE c.Customer_id = $customer_id
");

$toplam = 0;
$cart_ids = [];

while ($urun = mysqli_fetch_assoc($urunler)) {
    $urun_toplam = $urun['ProductPrice'] * $urun['Quantity'];
    $toplam += $urun_toplam;
    $cart_ids[] = $urun['Cart_id'];
}

// Ödeme işlemi başlatıldı mı kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form doğrulama ve işlem
    if ($toplam > 0) {
        mysqli_begin_transaction($baglanti);

        try {
            foreach ($cart_ids as $cart_id) {
                // 1. Payment tablosuna ödeme kaydını ekleyin
                $payment_sql = "INSERT INTO payment (Cart_id, PaymentStatus, Customer_id)
                                VALUES ('$cart_id', 1, $customer_id)";
                mysqli_query($baglanti, $payment_sql);

                $payment_id = mysqli_insert_id($baglanti);

                // 2. CashOnDelivery tablosuna ödeme bilgilerini ekleyin
                $payment_code = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT); // 6 haneli benzersiz ödeme kodu oluştur
                $cashondelivery_sql = "INSERT INTO cashondelivery (Payment_id, PaymentCode, Amount)
                                       VALUES ('$payment_id', '$payment_code', '$toplam')";
                mysqli_query($baglanti, $cashondelivery_sql);
                
                // 3. Payment tablosundaki Cart_id'yi NULL yapın
                $update_payment_sql = "UPDATE payment SET Cart_id = NULL WHERE Cart_id = $cart_id";
                mysqli_query($baglanti, $update_payment_sql);
            }

            // 4. Cart tablosundaki kayıtları silin
            $delete_cart_sql = "DELETE FROM cart WHERE Customer_id = $customer_id";
            mysqli_query($baglanti, $delete_cart_sql);

            mysqli_commit($baglanti);

            
            header("Location: orders.php");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($baglanti);
            echo "Hata: " . $e->getMessage();
        }
    } else {
        echo "Sepetiniz boş, ödeme yapılamaz!";
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
    <title>Kapıda Ödeme</title>
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="odemeSayfasi col-md-12">
                <h2><i>Kapıda Ödeme Sayfası</i></h2>
                <h4>Toplam Tutar: <strong><?= $toplam ?> TL</strong></h4>

                <form method="POST">
                    <div class="form-group">
                        <label>Kargo Alıcı İsmi:</label>
                        <p><?= $first_name . ' ' . $last_name ?></p>
                    </div>

                    <div class="form-group">
                        <label>Kargo Adresi:</label>
                        <p><?= $address ?></p>
                    </div>

                    <button type="submit" class="btn btn-success btn-block mt-2">Siparişi Tamamla</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
