<header class="main-header">
    <!-- Logo -->
    <a href="<?php echo base_url('');?>" class="logo">
        <?php
        if ($online){
            echo '<span class="logo-mini"><b>ON</b></span>
                  <span class="logo-lg"><b>ONLINE</b></span>';
        } else {
            echo '<span class="logo-mini"><b>OFF</b></span>
                  <span class="logo-lg"><b>OFFLINE</b></span>';
        }
        ?>

        <!-- logo for regular state and mobile devices -->

    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?php echo base_url('assets/dist/img/avatar5.png');?>" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?php echo $this->session->userdata('user_fullname'); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?php echo base_url('assets/dist/img/avatar5.png');?>" class="img-circle" alt="User Image">

                            <p>
                                <?php echo $this->session->userdata('user_fullname'); ?>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right">
                                <a href="<?php echo base_url('logout');?>" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>