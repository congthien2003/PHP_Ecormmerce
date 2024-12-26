<?php
require 'App/Middlewares/auth.php';
requireLogin();
requireUserRole();
echo $_COOKIE['token'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .product-card {
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .card-text {
            font-size: 0.9rem;
        }

        .btn {
            margin-right: 5px;
        }

        .favorite-btn i {
            color: #dc3545;
        }

        .add-to-cart-btn {
            color: #fff;
            background-color: #28a745;
            border: none;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-img-top {
            max-height: 180px;
            object-fit: cover;
        }

        .cart-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
        }

        .modal-body {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Danh sách sản phẩm</h1>
    <div class="header">
        <div class="text-center mb-4">
            <a href="/php/S4_PHP/Product/add" class="btn btn-primary">Thêm sản phẩm mới</a>
            <a href="/php/S4_PHP/cart" class="btn btn-primary">Giỏ hàng</a>
        
    </div>
        <a href="/php/S4_PHP/user/logout">Đăng xuất</a>

    </div>
    <div class="container">
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 product-card">
                    <div class="card">
                        <img src="<?= $product->ImageURL ?>" class="card-img-top" alt="<?= $product->Name ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $product->Name ?></h5>
                            <p class="card-text"><?= $product->Description ?></p>
                            <p class="card-text text-success"><?= number_format($product->Price, 0, ',', '.') ?> VND</p>
                            <div class="d-flex">
                                <button class="btn btn-outline-danger favorite-btn" data-product-id="<?= $product->ID ?>">
                                    Yêu thích <i class="fa-regular fa-heart"></i>
                                </button>
                               <form method="POST" action="/php/S4_PHP/Cart/addToCart" class="d-inline">
                                    <input type="hidden" name="idProduct" value="<?= $product->ID ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" class="btn btn-success">Thêm vào giỏ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
                </div>
             <div>     
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            // Danh sách yêu thích
            let listFavorite = JSON.parse(localStorage.getItem('listFavorite')) || [];
            listFavorite.forEach(item => {
                let $button = $(`.favorite-btn[data-product-id="${item.productId}"]`);
                if (item.isFavorite) {
                    $button.find('i').removeClass('fa-regular').addClass('fa-solid');
                }
            });

            $('.favorite-btn').click(function () {
                let productId = $(this).data('product-id');
                let $icon = $(this).find('i');
                let isFavorite = false;

                if ($icon.hasClass('fa-solid')) {
                    $icon.removeClass('fa-solid').addClass('fa-regular');
                    isFavorite = false;
                } else {
                    $icon.removeClass('fa-regular').addClass('fa-solid');
                    isFavorite = true;
                }

                if (isFavorite) {
                    if (!listFavorite.find(e => e.productId == productId)) {
                        listFavorite.push({ productId, isFavorite });
                    }
                } else {
                    listFavorite = listFavorite.filter(item => item.productId !== productId);
                }

                localStorage.setItem('listFavorite', JSON.stringify(listFavorite));
            });

            // // Giỏ hàng
            // let cart = JSON.parse(localStorage.getItem('cart')) || [];
            // $('.add-to-cart-btn').click(function () {
            //     let productId = $(this).data('product-id');
            //     let productName = $(this).data('product-name');
            //     let productPrice = $(this).data('product-price');

            //     let product = cart.find(item => item.productId === productId);
            //     if (product) {
            //         product.quantity++;
            //     } else {
            //         cart.push({ productId, productName, productPrice, quantity: 1 });
            //     }
                
            //     localStorage.setItem('cart', JSON.stringify(cart));
            //     alert('Đã thêm sản phẩm vào giỏ hàng!');
            // });

            // // Hiển thị giỏ hàng trong modal
            // $('#cartModal').on('show.bs.modal', function () {
            //     let cartItemsContainer = $('#cartItems');
            //     let cartTotal = 0;
            //     cartItemsContainer.empty();

            //     if (cart.length === 0) {
            //         cartItemsContainer.html('<p class="text-center">Giỏ hàng trống.</p>');
            //     } else {
            //         cart.forEach(item => {
            //             let itemTotal = item.productPrice * item.quantity;
            //             cartTotal += itemTotal;

            //             cartItemsContainer.append(`
            //                 <div class="d-flex justify-content-between align-items-center border-bottom py-2">
            //                     <div>
            //                         <strong>${item.productName}</strong>
            //                         <p>${item.quantity} x ${item.productPrice.toLocaleString()} VND</p>
            //                     </div>
            //                     <div>${itemTotal.toLocaleString()} VND</div>
            //                 </div>
            //             `);
            //         });
            //     }

            //     $('#cartTotal').text(cartTotal.toLocaleString());
            // });
        });
    </script>
</body>

</html>
