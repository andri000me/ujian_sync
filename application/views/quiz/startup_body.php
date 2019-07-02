<div class="body-header">
    &nbsp;
</div>
<div class="body-content">
    <div class="col-md-8">
        <div class="hidden-md hidden-lg">
            <div class="form-group">
                <label class="control-label">Nama Peserta :</label>
                <div class="form-control btn-flat"><?php echo $this->session->userdata('user_fullname');?></div>
            </div>
            <div class="form-group">
                <label class="control-label">Nama Tes :</label>
                <div class="form-control btn-flat"><?php echo $quiz->quiz_name;?></div>
            </div>
            <div class="form-group">
                <label class="control-label">Mata Pelajaran :</label>
                <div class="form-control btn-flat">
                    <?php
                    if ($mapel){
                        foreach ($mapel as $val){
                            echo ''.$val->mapel_name.'';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">Batas Waktu :</label>
                <div class="form-control btn-flat"><?php echo $quiz->quiz_timer;?> menit</div>
            </div>
        </div>
        <table width="100%" class="hidden-sm hidden-xs">
            <tr>
                <td width="150px">NAMA PESERTA</td>
                <td width="5px">:</td>
                <td><strong><?php echo $this->session->userdata('user_fullname');?></strong></td>
            </tr>
            <tr>
                <td width="150px">NAMA TES</td>
                <td width="5px">:</td>
                <td><strong><?php echo $quiz->quiz_name;?></strong></td>
            </tr>
            <tr>
                <td valign="top">MATA PELAJARAN</td>
                <td valign="top">:</td>
                <td valign="top" style="font-weight: bold">
                    <?php
                    if ($mapel){
                        echo '<ol type="1">';
                        foreach ($mapel as $val){
                            echo '<li class="text-danger">'.$val->mapel_name.'</li>';
                        }
                        echo '</ol>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>BATAS WAKTU</td>
                <td>:</td>
                <td><strong><?php echo $quiz->quiz_timer;?> menit</strong></td>
            </tr>
        </table>
        <div style="height:50px"></div>
    </div>
    <div class="col-md-4">
        <form id="formSubmit" class="form form-horizontal">
            <div class="form-group">
                <label for="token" class="control-label col-md-2 hidden-xs hidden-sm">TOKEN</label>
                <div class="col-md-10 col-xs-12 col-sm-12">
                    <input type="text" name="token" id="token" class="form-control btn-flat" placeholder="TOKEN">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-10 col-xs-12 col-sm-12">
                    <button type="submit" class="btn btn-lg btn-danger btn-block btn-flat"><i class="fa fa-send"></i> SUBMIT</button>
                </div>
            </div>
        </form>
    </div>
    <div class="clearfix"></div>
</div>