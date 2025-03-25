<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkin $checkin
 * @var \App\Model\Entity\Project $project
 * @var $checkout
 */
$currentUser = $this->request->getAttribute('identity');
$title = 'Check in to site: ';
$buttonLabel = 'Check In';
if ($checkout){
    $title = 'Check out of site: ';
    $buttonLabel = 'Check Out';
}
?>
<style>
    .row.content{
        display:flex;
        align-items:center;
        width:100%;
    }
    .column-responsive{
        width:100%;
    }
    .check_result{
        display:flex;
        align-items:center;
        width:200px;
        height:200px;
        line-height:36px;
        text-align:center;
        font-size:30px;
        background:#3fd791;
        margin:0 auto;
        border-radius:50%;
        color:#fff;
    }

</style>
<div class="row content">
    <div class="column-responsive">
        <div class="check_result"><?= $buttonLabel; ?> Successful</div>
    </div>
</div>
