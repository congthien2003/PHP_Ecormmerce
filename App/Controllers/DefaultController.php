<?php
// Đặt tên theo kiểu PascalCase (viết hoa chữ cái đầu của mỗi từ)
// Đặt tên theo chuẩn PSR-4 là gì? Đọc thêm tại: https://www.php-fig.org/psr/psr-4/

// namespace App\Controllers;

class DefaultController
{
    public function index(){
        // echo "Đây là action index của controller DefaultController";
        //gửi thông tin từ controller sang view
        $title = "XIN CHÀO HUTECH";
        //gửi một danh sách tin tức từ controller sang view
        $newsList = [
            [
                'title' => 'Tin tức 1',
                'description' => 'Mô tả ngắn gọn về tin tức 1.',
                'image' => 'https://via.placeholder.com/150'
            ],
            [
                'title' => 'Tin tức 2',
                'description' => 'Mô tả ngắn gọn về tin tức 2.',
                'image' => 'https://via.placeholder.com/150'
            ],
            [
                'title' => 'Tin tức 3',
                'description' => 'Mô tả ngắn gọn về tin tức 3.',
                'image' => 'https://via.placeholder.com/150'
            ]
        ];
        require_once 'app/views/default/index.php';
    }
}
