<?php
use Cake\Core\Configure;
/**
 * @var $redirect
 */
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo Configure::read('Theme.title'); ?> | <?php echo $this->fetch('title'); ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <?php echo $this->Html->css('AdminLTE./bower_components/bootstrap/dist/css/bootstrap.min'); ?>
  <!-- Font Awesome -->
  <?php echo $this->Html->css('AdminLTE./bower_components/font-awesome/css/font-awesome.min'); ?>
  <!-- Ionicons -->
  <?php echo $this->Html->css('AdminLTE./bower_components/Ionicons/css/ionicons.min'); ?>
  <!-- Theme style -->
  <?php echo $this->Html->css('AdminLTE.AdminLTE.min'); ?>
  <!-- iCheck -->
  <?php echo $this->Html->css('AdminLTE./plugins/iCheck/square/blue'); ?>

    <?php echo $this->Html->css('main'); ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <?php echo $this->fetch('css'); ?>

</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Please sign in to access SiteX.</p>
    <?php echo $this->fetch('content'); ?>
        <form id="login" method="post" accept-charset="utf-8" action="<?= $this->Url->build(['controller' => 'users', 'action' => 'login', '?' => ['redirect' => $redirect]])?>">
          <fieldset>
              <legend></legend>
              <div class="input email required pt-3"><label for="email">Email</label><br/>
                  <input class="form-control w-25" style="min-width: 200px;" type="email" name="email" required="required" id="email" aria-required="true"></div>
              <br/>
              <div class="input password required py-3"><label for="password">Password</label><br/>
                  <input class="form-control w-25" style="min-width: 200px;" type="password" name="password" required="required" id="password" aria-required="true"></div>
          </fieldset>
            <br/>
            <a type="submit" id='submitByLink' class="btn btn-block btn-primary" style="width: 100%px" href="javascript:void(0)" >Log In</a> <!--onclick="document.getElementById('login').submit()"-->
            <button hidden name="login" type="submit" class="login">Login</button>

            <span style='display:block;margin-top:12px;'>
              <input id='savePassword' type="checkbox" input type="hidden"> Remember me
                <?php if (Configure::read('Theme.login.show_remember')): ?>
                    <a href="<?= $this->Url->build(['action' => 'requestpassword']) ?>" style="float: right;">Forgot Password?</a><br>
                <?php endif; ?>
            </span>
            <noscript>
                <button name="login" type="submit" class="login">Log In</button>

            </noscript>
            <br/>
        </form>

      <?php if (Configure::read('Theme.login.show_register')): ?>
          <div style="text-align: center;">
              <span style="text-decoration: none;">Not a member? </span>

              <?php if($redirectstatus){ ?>
                   <a href="<?= $urlWithQuery ?>" style="text-decoration: underline;">Create an account</a>
              <?php  }else{?>
              <a href="<?= $this->Url->build(['action' => 'signup']) ?>" style="text-decoration: underline;">Create an account</a>
              <?php };?>
          </div>
      <?php endif; ?>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<?php echo $this->Html->script('AdminLTE./bower_components/jquery/dist/jquery.min'); ?>
<!-- Bootstrap 3.3.7 -->
<?php echo $this->Html->script('AdminLTE./bower_components/bootstrap/dist/js/bootstrap.min'); ?>
<!-- iCheck -->
<?php echo $this->Html->script('AdminLTE./plugins/iCheck/icheck.min'); ?>

<?php echo $this->fetch('script'); ?>

<?php echo $this->fetch('scriptBottom'); ?>

<script>
  $(function () {
    $("#savePassord").attr("checked","checked")
    var _email = localStorage.getItem("email")
    var _password = localStorage.getItem("password")
    $("#email").val(_email)
    $("#password").val(_password)

    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
    $("#submitByLink").on("click", function(){
      var passwordisCheck = $("#savePassord").is(':checked');
      if(passwordisCheck){
        var email = $("#email").val();
        var password = $("#password").val();
        localStorage.setItem("email", email)
        localStorage.setItem("password", password)
      }else{
        localStorage.removeItem("email")
        localStorage.removeItem("password")
      }
      document.getElementById('login').submit()
    })
  });


</script>
