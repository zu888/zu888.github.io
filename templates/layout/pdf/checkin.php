<!DOCTYPE html>
<html>
<head>
    <title><?= h($title) ?></title>
    <?php
    use Cake\Routing\Router;
    ?>
    <style>
        @page {
            margin-top: 20px !important;
            margin-bottom: 20px !important;
            margin-right: 50px !important;
            margin-left: 50px !important;
            padding: 0px 0px 0px 0px !important;
        }
        body{
            font-family: 'Calibri', sans-serif;
        }
        heading{
            text-align: center;
            font-family: 'Calibri', sans-serif;
            font-size: 72px;
            font-weight: bold;
            color: green;
        }

        .column {
            float: left;
            width: 50%;
        }

    </style>
</head>
<body>
<br/>
<div class="row" style="text-align:center">
    <heading>SITE SIGN-IN/OUT</heading><br/><br/>
    <b><?= h($name) ?></b><br/>
    <?= h($address) ?><br/><br/>
</div>
<div class="row">
    <div class="column">
        <img src="uploads/qr_checkin/<?= $id ?>/checkinQR.png" />
    </div>
    <div class="column">
        <br/>
        <div class="row">
            <br/><br/>
            <b><h1>Site Contact:</h1></b>
            <div style="padding-bottom: 6px">
                <?= h($builderName) ?> <br/>
            </div>
            <div style="padding-bottom: 6px">
                Phone: <?= h($builderMobilePhone) ?> <br/>
            </div>
            <div style="padding-bottom: 6px">
                Email:   <?= h($builderEmail) ?> <br/>
            </div>
            <br/>
        </div>
        <br/>
    </div>
</div>
<br/>
<br/>
<br/>
<br/>
<div class="row" style="text-align:center">
    <heading>SITE SIGN-IN/OUT</heading><br/><br/>
    <b><?= h($name) ?></b><br/>
    <?= h($address) ?><br/><br/>
</div>
<div class="row">
    <div class="column">
        <img src="uploads/qr_checkin/<?= $id ?>/checkinQR.png" />
    </div>
    <div class="column">
        <br/>
        <div class="row">
            <br/><br/>
            <b><h1>Site Contact:</h1></b>
            <div style="padding-bottom: 6px">
                <?= h($builderName) ?> <br/>
            </div>
            <div style="padding-bottom: 6px">
                Phone: <?= h($builderMobilePhone) ?> <br/>
            </div>
            <div style="padding-bottom: 6px">
                Email:   <?= h($builderEmail) ?> <br/>
            </div>
            <br/>
        </div>
        <br/>
    </div>
</div>
</body>
</html>
