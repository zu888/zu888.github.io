<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Database\StatementInterface $error
 * @var string $message
 * @var string $url
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;



?>
<style>
    .text-center {
        text-align: center;
    }
</style>
<div class="Error 400">
    <div class="text">
        <h2 class="text-center" style="color:red">
            You are not authorized to view this page.
        </h2>
    </div>
    <br>
    <div style="text-align: center;">
        <a class="btn btn-block btn-primary" style="width: 200px; margin: 0 auto;" href="<?= $this->Url->build(
            ['controller' => 'Projects', 'action' => 'index']) ?>">Return home</a>
    </div>
</div>


