<?php
include("inc/baglan.php");
session_start();

// Giriş yapmış satıcının kimliği
if (!isset($_SESSION['kulanici_id'])) {
    echo "Lütfen giriş yapınız.";
    exit;
}

// Satıcının ürünlerini al
$satici_id = $_SESSION['kulanici_id']; // Giriş yapan satıcının ID'si
$sorgu = "SELECT * FROM product WHERE Supplier_id = $satici_id"; // Satıcıya ait ürünler
$urunler = mysqli_query($baglanti, $sorgu);

// Ürün güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ProductId'])) {
    $productId = $_POST['ProductId'];
    $productName = $_POST['ProductName'];
    $productPrice = $_POST['ProductPrice'];
    $productStock = $_POST['ProductStock'];

    // Ürünü güncelle
    $update_query = "UPDATE product SET ProductName = ?, ProductPrice = ?, ProductStock = ? WHERE Product_id = ?";
    $stmt = $baglanti->prepare($update_query);
    $stmt->bind_param("sdii", $productName, $productPrice, $productStock, $productId);
    $stmt->execute();
    $stmt->close();

    echo '<script>alert("Ürün başarıyla güncellendi!"); window.location.href="product.php";</script>';
}

// Ürün silme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['DeleteProductId'])) {
    $productIdToDelete = $_POST['DeleteProductId'];

    // Ürünü silme sorgusu
    $delete_query = "DELETE FROM product WHERE Product_id = ?";
    $stmt = $baglanti->prepare($delete_query);
    $stmt->bind_param("i", $productIdToDelete);
    $stmt->execute();
    $stmt->close();

    echo '<script>alert("Ürün başarıyla silindi!"); window.location.href="product.php";</script>';
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/bootstrap.css">
    <link rel="stylesheet" href="assets/style.css">
    <title>Satıcı Ürünleri</title>
</head>

<body>
    <div class="baslik">
        <h1><i>Ürünleriniz</i></h1>
    </div>
    <div class="anaEkran">
        <div class="row h-100">
            <div class="col-md-3 mt-2">
                <div class="menu">
                    <h4>Menü</h4>
                    <ul>
                        <li><a href="product.php">Ürünlerim</a></li>
                        <li><a href="suppplierorder.php">Siparişlerim</a></li>
                        <li><a href="addProduct.php">Yeni Ürün Ekle</a></li>
                        <li><a href="logout.php">Çıkış Yap</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-9 mt-2">
                <div class="content">
                    <h2>Satıcı Ürünleri</h2>

                    <?php
                    if ($urunler && mysqli_num_rows($urunler) > 0) {
                        echo '<div class="row">';
                        while ($urun = mysqli_fetch_assoc($urunler)) {
                            echo '<div class="col-md-3">';
                            echo '<div class="card mb-4">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title product-name">' . htmlspecialchars($urun["ProductName"]) . '</h5>';
                            echo '<p class="card-text product-price">Fiyat: ' . htmlspecialchars($urun["ProductPrice"]) . ' TL</p>';
                            echo '<p class="card-text product-stock">Stok: ' . htmlspecialchars($urun["ProductStock"]) . '</p>';
                            // Düzenle butonu
                            echo '<button class="btn btn-warning" data-toggle="modal" data-target="#editModal" onclick="fillEditForm(' . $urun["Product_id"] . ')">Düzenle</button>';
                            // Silme formu
                            echo '<form method="POST" class="mt-2" action="" style="display:inline;">
                                    <input type="hidden" name="DeleteProductId" value="' . $urun["Product_id"] . '">
                                    <button type="submit" class="btn btn-danger">Sil</button>
                                  </form>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo '<p>Ürününüz bulunmamaktadır.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Düzenle -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Ürün Düzenle</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editProductId" name="ProductId">
                        <div class="form-group">
                            <label for="editProductName">Ürün Adı:</label>
                            <input type="text" class="form-control" id="editProductName" name="ProductName" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductPrice">Fiyat:</label>
                            <input type="number" class="form-control" id="editProductPrice" name="ProductPrice" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductStock">Stok Miktarı:</label>
                            <input type="number" class="form-control" id="editProductStock" name="ProductStock" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS ve JQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Modal formu doldurmak için JavaScript
        function fillEditForm(productId) {
            // Ürün bilgilerini almak için PHP'yi kullanıyoruz.
            var row = document.querySelector('button[data-target="#editModal"][onclick*="' + productId + '"]').closest('.card-body');
            var productName = row.querySelector('.product-name').innerText;
            var productPrice = row.querySelector('.product-price').innerText.replace("Fiyat: ", "").replace(" TL", "");
            var productStock = row.querySelector('.product-stock').innerText.replace("Stok: ", "");

            // Modal input'larını form alanları ile doldurma
            document.getElementById('editProductId').value = productId;
            document.getElementById('editProductName').value = productName;
            document.getElementById('editProductPrice').value = productPrice;
            document.getElementById('editProductStock').value = productStock;
        }
    </script>
</body>

</html>
