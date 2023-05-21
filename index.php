<?php

include 'bootstrap.php';

$json = file_get_contents('./blockchain.json');
$blockchain = json_decode($json, true);


ini_set('max_execution_time', '600');

if (isset($_POST) and !empty($_POST)) {
    $newBlock = [
        'from' => $_POST['from'],
        'to' => $_POST['to'],
        'coast' => $_POST['coast'],
        'good' => $_POST['good'],
    ];

    $block = getBlock($blockchain, $newBlock);
    if ($block){
        $blockchain[] = $block;
        file_put_contents('./blockchain.json', json_encode($blockchain));
        header('Location: /api/test.php');
    }
    echo 'Не удалось смайнить';

}

$isVal = isValid($blockchain);

function h($block)
{
    return md5(json_encode($block));
}

function getBlock($blockchain, $data)
{
    if (!isValid($blockchain)) {
        return null;
    }

    // хеш последнего блока
    $lastIndex = count($blockchain) - 1;
    $lastBlock = $blockchain[$lastIndex];
    $prevHash = h($lastBlock);

    $time = (new DateTime())->format('Y-m-d H:i:s');


    $keepMining = true;
    while ($keepMining) {

        $block = [
            'data' => $data,
            'prev_hash' => $prevHash,
            'time' => $time,
            'salt' => randomString()
        ];

        if (str_starts_with(h($block), '000000')) {
            return $block;
        }
    }

    return null;

}

function randomString($length = 16): string
{
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function isValid($chain): bool
{
    $prevHash = null;
    foreach ($chain as $block) {
        // dl([$prevHash, $block['prev_hash']]);
        if ($prevHash != $block['prev_hash']) {
            return false;
        }
        $prevHash = h($block);
    }
    return true;
}



?>


<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Блокчейн</title>
    <!-- bootstrap -->
    <link rel="stylesheet" href="../libs/bootstrap.min.css">

    <!-- tagify -->
    <script src="../libs/tagify/tagify.js"></script>
    <script src="../libs/tagify/tagify.polyfills.min.js"></script>
    <link href="../libs/tagify/tagify.css" rel="stylesheet" type="text/css"/>

    <!-- dayjs -->
    <script src="../libs/dayjs.min.js"></script>

    <!-- fontawesome -->
    <link href="../libs/fontawesome/css/all.min.css" rel="stylesheet"/>

</head>
<body>

<div class="container">
    <h1>Привет </h1>

    <?php if ($isVal): ?>
        <div class="alert alert-success">Блокчейн кошерный</div>
    <?php else: ?>
        <div class="alert alert-danger">Ошибка в блокчейне</div>

    <?php endif ?>

    <?php foreach ($blockchain as $good): ?>
        <div class="card mt-4">
            <div class="card-header">
                <?= h($good) ?>
            </div>
            <div class="card-body">

                <h4>
                    <?= $good['data']['coast'] ?>
                    <i class="fa fa-zap"></i>
                </h4>
                <?= $good['data']['good'] ?>


            </div>

            <div class="card-footer">[<?= $good['time'] ?>] <?= $good['data']['from'] ?> <i class="fa fa-arrow-right"></i> <?= $good['data']['to'] ?></div>
        </div>


    <?php endforeach; ?>
    <a href="/api/test.php" class="btn btn-primary mt-2">Проверить блокчейн</a>


    <form action="/api/test.php" method="post">

        <div class="card mt-4">
            <div class="card-header">
                Смайнить
            </div>
            <div class="card-body">

                <input type="text"
                       id="action-input"
                       class="mt-2 form-control  form-control-lg"
                       placeholder="От кого"
                       name="from"
                       value="Bob"
                       aria-label>

                <input type="text"
                       id="action-input"
                       class="mt-2 form-control  form-control-lg"
                       placeholder="Кому"
                       name="to"
                       value="Eve"
                       aria-label>

                <input type="text"
                       id="action-input"
                       class="mt-2 form-control  form-control-lg"
                       placeholder="Сколько"
                       value="13"
                       name="coast"
                       aria-label>

                <input type="text"
                       id="action-input"
                       class="mt-2 form-control  form-control-lg"
                       placeholder="Что"
                       value="Tomato"
                       name="good"
                       aria-label>

                <button type="submit" href="/" class="btn btn-primary mt-2">Смайнить</
                >

            </div>

        </div>

    </form>

</div>


</body>
</html>
