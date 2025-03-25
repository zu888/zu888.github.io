<?php use Cake\Core\Configure;

/**
 * @var \App\View\AppView $this
 */

$this->loadHelper('Authentication.Identity');
$currentUser = $this->request->getAttribute('identity');
$session = $this->getRequest()->getSession();
$hasPendingDocuments = $session->read('hasPendingDocuments', FALSE);
$currentUser = $this->request->getAttribute('identity');
$role = NULL;
if ($currentUser) {
    $role = $currentUser->role;
}

// Define a variable to store the inline style based on the user's role
$sidebarStyle = '';

if ($role === 'Admin') {
    $sidebarStyle = 'background-color: darkred;'; // Red for admin
} elseif ($role === 'Builder') {
    $sidebarStyle = 'background-color: orange;'; // Orange for builder
} else {
    $sidebarStyle = ''; // Blue for other roles
}
?>
<nav class="navbar navbar-static-top" style="<?php echo $sidebarStyle; ?>">

  <?php if (isset($layout) && $layout == 'top'): ?>
  <div class="container">

    <div class="navbar-header">
      <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'display']) ?>" class="navbar-brand"><?php echo Configure::read('Theme.logo.large') ?></a>
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" >
        <i class="fa fa-bars"></i>
      </button>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li>
      </ul>
      <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" id="navbar-search-input" placeholder="Search">
        </div>
      </form>
    </div>
    <!-- /.navbar-collapse -->
  <?php else: ?>

      <?php if($currentUser): ?>
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
          </a>
      <?php endif; ?>
  <?php endif; ?>



  <div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
      <!-- User Account: style can be found in dropdown.less -->
        <?php if($currentUser){ ?>
            <!-- Notifications: style can be found in dropdown.less -->
            <li class="dropdown notifications-menu">
<!--                <a href="#" class="dropdown-toggle" data-toggle="dropdown">-->
<!--                    <i class="fa fa-bell-o"></i>-->
<!--                    --><?php
//                    if($hasPendingDocuments){
//                        echo '<span class="label label-warning">1</span>';
//                    } else {
//                        echo '<span class="label label-warning"></span>';
//                    }
//                    ?>
<!--                </a>-->
                <ul class="dropdown-menu">
                    <li class="header">Alerts</li>
                    <ul>
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Popup Reminder Example</title>
                        </head>
                        <body>



                        <button onclick="setTimeout(SignOutReminder, 28800000);">mark on start 8-hour working</button>
                        <button onclick="setTimeout(SignOutReminder, 18000000);">mark on start 5-hour working</button>


                        <script>
                            function SignOutReminder() {
                                alert(' time to sign out ');
                            }
                        </script>
                        <p id="demo"></p>

                        <script>
                            /*
                            setInterval(myTimer, 1000);

                            function myTimer() {
                                const currenttime = new Date();
                                document.getElementById("demo").innerHTML = currenttime.toLocaleTimeString();
                            }*/
                        </script>

                        <script>
                            /*
                            setTimeout(myTimeout1, 30000);
                            function myTimeout1() {
                                document.getElementById("demo").innerHTML = "30 seconds";
                            }*/

                        </script>



                        <script>
                            Notification.requestPermission().then(permission => {
                                console.log(permission)
                            })
                            checkTime();




                            // Function to check the time and show a popup
                            function checkTime() {
                                //alert("aest checktime called");
                                //alert();
                                // create a new Date object
                                const now = new Date();

                                // get the current time in UTC
                                const utcTime = now.getTime();

                                // define the time offset for AEST (UTC+12)
                                const aestOffset = 12;

                                // calculate the AEST time by adding the offset to the UTC time
                                const aestTime = new Date(utcTime + (3600000 * aestOffset));

                                // output the AEST time in a human-readable format
                                console.log(`The current time in AEST is: ${aestTime.toLocaleString()}`);
                                var hours = aestTime.getHours();
                                var minutes = aestTime.getMinutes();
                                //alert(aestTime);
                                if(hours == 5 && minutes == 0)
                                {alert("time to logout");}
                                else if
                                (hours == 8 && minutes == 0)
                                {//alert("time to logout");
                                    let popup = window.open("", "Popup Reminder", "width=400,height=200");
                                    popup.document.write("<h2>It's time to logout!</h2>");}
                                // Check if the time is 5 pm or 8 pm AEST
                                /*
                                if ((hours === 17 && minutes === 0) || (hours === 08 && minutes === 00)) {
                                    // Show the popup
                                    alert(" it is time to logout ");

                                    let popup = window.open("", "Popup Reminder", "width=400,height=200");
                                    popup.document.write("<h2>It's time to logout!</h2>");
                                    popup.document.write("<p>It's " + hours + ":" + minutes + " in AEST. Please logout now.</p>");
                                }
                            }*/

                                // Check the time every minute
                                setInterval(checkTime, 60000);


                            /*
                                // Get the current time in AEST
                            let currentTime = new Date().toLocaleString("en-AU", {timeZone: "Australia/Sydney"});
                            //let hours = new Date(currentTime).getHours();
                                let hours = currentTime.getHours();
                            //let minutes = new Date(currentTime).getMinutes();
                                let minutes = currentTime.getMinutes();

                                console.log(currentTime.getHours())
                            */




                        </script>

                        </body>
                        </html>


                    </ul>

                    <li>
                        <!-- inner menu: contains the actual data -->
                        <ul class="menu">
                            <?php if($hasPendingDocuments){ ?>
                                <li>
                                    <a href="<?= $this->Url->build(['controller' => 'signatures', 'action' => 'pending']) ?>">
                                        <i class="fa fa-warning text-yellow"></i> You have documents pending review.
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php if($currentUser->image){?>
                        <?= $this->Html->image('../' . $currentUser->image,
                            array('class' => 'user-image', 'alt' => 'User Image')); ?>
                    <?php }else{ ?>
                    <?php echo $this->Html->image('icons8-user-96.png', array('class' => 'user-image', 'alt' => 'User Image')); ?>
                    <?php } ?>
                    <span class="hidden-xs"><?= h($currentUser->role. ': '.$currentUser->first_name.' '.$currentUser->last_name) ?></span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <li class="user-header">
                        <br/>
                        <?php if($currentUser->image){?>
                            <?= $this->Html->image('../' . $currentUser->image,
                                array('class' => 'user-image', 'alt' => 'User Image')); ?>
                        <?php }else{ ?>
                            <?php echo $this->Html->image('icons8-user-96.png', array('class' => 'user-image', 'alt' => 'User Image')); ?>
                        <?php } ?>
                        <p><?= h($currentUser->role) ?></p>
                        <p><?= h($currentUser->first_name.' '.$currentUser->last_name) ?></p>

                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="<?= $this->Url->build(['controller' => 'users', 'action' => 'view', $currentUser->id]) ?>" class="btn btn-default btn-flat">Account Details</a>
                        </div>
                        <div class="pull-right">
                            <a href="<?= $this->Url->build(['controller' => 'users', 'action' => 'logout']) ?>" class="btn btn-default btn-flat">Sign Out</a>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="dropdown notifications-menu">
                <a href="<?= $this->Url->build(['controller' => 'users', 'action' => 'logout']) ?>" class="dropdown-toggle">Log Out</a>
            </li>
        <?php } else { ?>
            <li class="dropdown notifications-menu">
                <a href="<?= $this->Url->build(['controller' => 'users', 'action' => 'login']) ?>" class="dropdown-toggle">Log In</a>
            </li>
        <?php } ?>

    </ul>
  </div>

  <?php if (isset($layout) && $layout == 'top'): ?>
  </div>
  <?php endif; ?>
</nav>
