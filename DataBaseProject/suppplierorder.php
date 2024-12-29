<?php
include("inc/baglan.php");
session_start();

// Kullanıcı oturumu kontrolü
if (!isset($_SESSION['kulanici_id'])) {
    header("Location: supplierLoginPage.php");
    exit;
}
$supplierID=$_SESSION["kulanici_id"];

// Sipariş onaylama işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['tracking_number'])) {
    $order_id = intval($_POST['order_id']);
    $tracking_number = mysqli_real_escape_string($baglanti, $_POST['tracking_number']);

    // Shipment tablosuna veri ekle
    $query = "INSERT INTO shipment (Order_id, TrackingNumber,Supplier_id) VALUES ($order_id, '$tracking_number','$supplierID')";
    if (mysqli_query($baglanti, $query)) {
        $success_message = "Sipariş Onaylandı. Kargo numarası kaydedildi.";
    } else {
        $error_message = "Kargo bilgisi kaydedilirken bir hata oluştu: " . mysqli_error($baglanti);
    }
}

// Satıcıya ait siparişler
$seller_id = $_SESSION['kulanici_id'];
$query = "
    SELECT o.Order_id, o.OrderDate, p.ProductName, o.Quantity, c.FirstName, c.LastName, s.TrackingNumber
    FROM orders o
    JOIN product p ON o.Product_id = p.Product_id
    JOIN supplier sp ON o.Supplier_id = sp.Supplier_id
    JOIN customer c ON c.Customer_id = o.Customer_id
    LEFT JOIN shipment s ON o.Order_id = s.Order_id
    WHERE sp.Supplier_id = $seller_id
    ORDER BY o.OrderDate DESC";
$result = mysqli_query($baglanti, $query);
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
    <div class="baslik">
        
        <h1><i>Siparişlerim</i></h1>
    </div>
    <div class=" col-md-3 mt-2">
                <div class="menu">
                    <h4>Menü</h4>
                    <ul>
                        <li><a href="product.php">Ürünlerim</a></li>
                        <li><a href="suppplierorder.php">Siparişlerim</a></li> <!-- Siparişlerim kısmı eklendi -->
                        <li><a href="addProduct.php">Yeni Ürün Ekle</a></li>
                        <li><a href="logout.php">Çıkış Yap</a></li>
                    </ul>
                </div>
            </div>
    <div class="container mt-4">
        <?php if (isset($success_message)) : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif (isset($error_message)) : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <h2>Satıcının Siparişleri</h2>
        <div class="kategori order-list mt-4">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                echo '<table class="table table-bordered">';
                echo '<thead><tr><th>Sipariş Tarihi</th><th>Ürün Adı</th><th>Adet</th><th>Kullanıcı</th><th>Kargo Numarası</th></tr></thead>';
                echo '<tbody>';
                while ($row = mysqli_fetch_assoc($result)) {
                    $order_id = $row['Order_id'];
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row["OrderDate"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["ProductName"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["Quantity"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["FirstName"] . ' ' . $row["LastName"]) . '</td>';

                    // Kargo Numarası Görüntüleme ve Buton Gizleme
                    if (!empty($row['TrackingNumber'])) {
                        echo '<td>' . htmlspecialchars($row['TrackingNumber']) . '</td>';
                    } else {
                        echo '<td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="' . $order_id . '">
                                    <input type="text" name="tracking_number" placeholder="Kargo No" required>
                                    <button type="submit" class="btn btn-success btn-sm">Onayla</button>
                                </form>
                              </td>';
                    }

                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>Henüz siparişiniz yok.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS ve JQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
