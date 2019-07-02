<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?php echo base_url('assets/dist/img/avatar5.png');?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?php echo $this->session->userdata('user_fullname'); ?></p>
                <?php
                if ($online){
                    echo '<a href="#"><i class="fa fa-circle text-success"></i> Online</a>';
                } else {
                    echo '<a href="#"><i class="fa fa-circle text-muted"></i> Offline</a>';
                }
                ?>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li class="dashboard <?php if ($menu == 'dashboard'){ echo 'active'; }?>">
                <a data-target="dashboard" href="<?php echo base_url('');?>" onclick="load_page(this);return false">
                    <i class="fa fa-home"></i> <span>Beranda</span>
                </a>
            </li>
            <?php if ($this->session->userdata('user_level') == 99) {?>
            <li class="sync <?php if ($menu == 'sync'){ echo 'active'; }?>">
                <a data-target="sync" href="<?php echo base_url('sync');?>" onclick="load_page(this);return false">
                    <i class="fa fa-signal"></i> <span>Singkong Boled</span>
                </a>
            </li>
            <?php } elseif ($this->session->userdata('user_level') == 50) { ?>
            <li class="tes <?php if ($menu == 'tes'){ echo 'active'; }?>">
                <a data-target="tes" href="<?php echo base_url('tes');?>" onclick="load_page(this);return false">
                    <i class="fa fa-check-circle"></i> <span>Status Tes</span>
                </a>
            </li>
            <?php } ?>
            <li class="logout <?php if ($menu == 'logout'){ echo 'active'; }?>">
                <a data-target="logout" href="<?php echo base_url('logout');?>">
                    <i class="fa fa-sign-out"></i> <span>Logout</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>