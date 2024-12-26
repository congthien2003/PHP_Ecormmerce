<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4"><?=$title?></h1>
        <div class="row">
            <?php foreach($newsList as $news): ?>
                <div class="col-4">
                    <div class="card mb-4">
                        <img src="<?=$news['image']?>" class="card-img-top" alt="<?=$news['title']?>">
                        <div class="card-body">
                            <h5 class="card-title"><?=$news['title']?></h5>
                            <p class="card-text"><?=$news['description']?></p>
                        </div>
                    </div>  
                </div> 
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html></div>