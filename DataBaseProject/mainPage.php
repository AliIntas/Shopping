<?php











?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/bootstrap.css">
    <link rel="stylesheet" href="assets/style.css">
    <title>AnaSayfa</title>
</head>

<body>
    <div class="baslik">
        <h1><i>Alışverişin Adresi</i></h1>
    </div>
    <div class="anaEkran">
        <div class="row h-100">
            <div class="col-md-2 mt-2">
                <?php include 'kategori.php'; ?>
            </div>
            <div class="col-md-10 mt-2">
                <div class="content">
                    <h2>Ana Sayfa</h2>
                    <p>Hoşgeldiniz...</p>
                    <?php


                    $sorgu = "SELECT * FROM urunler WHERE durum = 1";
                    if ($kat_id > 0) {
                        $sorgu .= " AND k_id = $kat_id";
                    }

                    $urunler = mysqli_query($baglanti, $sorgu);

                    if (mysqli_num_rows($urunler) > 0) {
                        while ($urun = mysqli_fetch_array($urunler)) {
                            echo '<div class="col-md-3">';
                            echo '<div class="card mb-4">';
                            echo '<div class="card-body">';
                            echo '<img src="admin/' . $urun["pic"] . '" class="card-img-top " alt="' . $urun["urun_adi"] . '" style="width:100%;"> ';
                            echo '<h5 class="card-title">' . $urun["urun_adi"] . '</h5>';
                            echo '<p class="card-text">Fiyat: ' . $urun["price"] . ' TL</p>';
                            echo '<p class="card-text">Stok: ' . $urun["amount"] . '</p>';
                            echo '<a href="_urun_incele.php?id=' . $urun["id"] . '" class="btn btn-primary">Ürünü İncele</a>';
                            if (isset($_SESSION['kullanici'])) {
                                echo ' <a href="sepete_ekle.php?id=' . $urun["id"] . '" class="btn btn-success ml-3">Sepete Ekle</a>';
                                $_SESSION['urun_name'] = $urun["urun_adi"];
                            }
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>Bu kategoride ürün bulunmamaktadır.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>


</body>

</html>