<?php
include 'inc/baglan.php';
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['customer_id'])) {
    header("Location: loginPage.php");
    exit();
}
$customer_id = (int)$_SESSION['customer_id'];

// Siparişleri müşteri kimliğine göre al
$order_query = "
    SELECT o.Order_id, o.Supplier_id, o.OrderDate, o.TotalPrice, o.ReturnStatus, o.ReturnReason, o.ReturnDate, 
           o.Payment_id, oi.Product_id, oi.Quantity, oi.Customer_id, p.ProductName
    FROM orders o
    JOIN order_items oi ON o.Order_id = oi.Order_id
    JOIN product p ON oi.Product_id = p.Product_id
    WHERE oi.Customer_id = $customer_id
";
$order_result = mysqli_query($baglanti, $order_query);

// İade işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_order_id'])) {
    $order_id = $_POST['return_order_id'];
    $return_reason = mysqli_real_escape_string($baglanti, $_POST['return_reason']);

    // İade işlemini güncelle
    $update_return_sql = "
        UPDATE orders
        SET ReturnStatus = 'Pending', ReturnReason = '$return_reason', ReturnDate = NOW()
        WHERE Order_id = $order_id AND Customer_id = $customer_id
    ";

    if (mysqli_query($baglanti, $update_return_sql)) {
        echo "<script>alert('İade talebiniz alınmıştır.');</script>";
    } else {
        echo "<script>alert('Bir hata oluştu, lütfen tekrar deneyin.');</script>";
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
    <title>Siparişlerim</title>
</head>

<body>
    <div class="container mt-5">
        <h2><i>Siparişlerim</i></h2>
        
        <?php if (mysqli_num_rows($order_result) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sipariş ID</th>
                        <th>Tedarikçi ID</th>
                        <th>Sipariş Tarihi</th>
                        <th>Toplam Tutar</th>
                        <th>İade Durumu</th>
                        <th>İade Sebebi</th>
                        <th>İade Tarihi</th>
                        <th>İade Et</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($order_result)): ?>
                        <tr>
                            <td><?= $order['Order_id'] ?></td>
                            <td><?= $order['Supplier_id'] ?></td>
                            <td><?= $order['OrderDate'] ?></td>
                            <td><?= number_format($order['TotalPrice'], 2) ?> TL</td>
                            <td><?= $order['ReturnStatus'] ? $order['ReturnStatus'] : 'Henüz iade edilmedi' ?></td>
                            <td><?= $order['ReturnReason'] ? $order['ReturnReason'] : 'N/A' ?></td>
                            <td><?= $order['ReturnDate'] ? $order['ReturnDate'] : 'N/A' ?></td>
                            <td>
                                <?php if ($order['ReturnStatus'] == NULL): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="return_order_id" value="<?= $order['Order_id'] ?>">
                                        <textarea name="return_reason" placeholder="İade sebebini yazın" required></textarea>
                                        <button type="submit" class="btn btn-danger">İade Et</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>İade Edildi</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Henüz siparişiniz bulunmamaktadır.</p>
        <?php endif; ?>
    </div>
</body>

</html>
