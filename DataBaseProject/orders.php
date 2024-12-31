<?php
include 'inc/baglan.php';
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['customer_id'])) {
    header("Location: loginPage.php");
    exit();
}
$customer_id = (int)$_SESSION['customer_id'];

// Sepet silme kontrolü
if (isset($_GET['payment_done']) && $_GET['payment_done'] == '1') {
    // Sepeti sil
    $delete_cart_sql = "DELETE FROM cart WHERE Customer_id = ?";
    $stmt_delete_cart = mysqli_prepare($baglanti, $delete_cart_sql);
    mysqli_stmt_bind_param($stmt_delete_cart, 'i', $customer_id);
    mysqli_stmt_execute($stmt_delete_cart);
}

// Siparişleri müşteri kimliğine göre al
$order_query = "
    SELECT
        o.Order_id,
        o.Supplier_id,
        o.OrderDate,
        o.TotalPrice,
        o.ReturnStatus,
        o.ReturnReason,
        o.ReturnDate,
        o.Payment_id,
        o.Product_id,
        o.Quantity,
        o.Customer_id,
        p.ProductName,
        s.CompanyName AS SupplierName
    FROM
        orders o
    JOIN
        product p ON o.Product_id = p.Product_id
    JOIN
        supplier s ON p.Supplier_id = s.Supplier_id
    WHERE
        o.Customer_id = ?
";
$stmt = mysqli_prepare($baglanti, $order_query);
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);

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
<div class="baslik ">  
    <h1><i>Alışverişin Adresi</i></h1>  
</div>  
<div class="container mt-5">  
    <h2><i>Siparişlerim</i></h2>  
    <div class="row h-100">  
        <div class="kategori col-md-2 mt-2">  
            <a href="mainPage.php" class="kat_login">Ana Sayfa</a>  
        </div>  
        <div class="col-md-10 mt-2">  

            <?php if (mysqli_num_rows($order_result) > 0): ?>  
                <table class="table table-bordered">  
                    <thead>  
                        <tr>  
                            <th>Sipariş ID</th>  
                            <th>Tedarikçi Adı</th>  
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
                                <td><?= $order['SupplierName'] ?></td> <!-- Tedarikçi adı burada gösterilecek -->  
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
    </div>  
</div>  
</body>  
</html>
