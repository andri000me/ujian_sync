<section class="content-header">
    <h1>
        BERANDA
        <small></small>
    </h1>
    <ol class="breadcrumb">
        <li class="active">Beranda</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">&nbsp;</h3>

            <div class="box-tools pull-right">
            </div>
        </div>
        <div class="box-body">
            <table width="100%" class="table table-bordered">
                <tr>
                    <td width="200px">STATUS REMOTE SERVER</td>
                    <td width="">
                        <?php
                        if ($online){
                            echo '<strong class="text-success">ONLINE</strong>';
                        } else {
                            echo '<strong class="text-danger">OFFLINE</strong>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>STATUS LOCAL SERVER</td>
                    <td>
                        <?php
                        if ($server){
                            echo '<strong class="text-success">ONLINE</strong>';
                        } else {
                            echo '<strong class="text-danger">OFFLINE</strong>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>ID SERVER</td>
                    <td>
                        <?php
                        if ($server){
                            echo '<strong class="text-success">'.$server->sv_id.'</strong>';
                        } else {
                            echo 'Masukkan <strong class="text-warning">ID SERVER</strong> pada menu SINGKONG BOLED';
                        }
                        ?>
                    </td>
                </tr>
            </table>

        </div>
        <!-- /.box-body -->
        <div class="box-footer">

        </div>
        <!-- /.box-footer-->
    </div>
    <!-- /.box -->

</section>