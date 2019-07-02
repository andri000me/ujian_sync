<?php
$i = 1;
foreach ($data as $val){
    $disabled = '';
    if ($i > 1 && $data[0]->a == 0){ $disabled = 'disabled'; }
    $btn    = '<a href="javascript:;" class="btn-'.$val->type.' '.$disabled.' btn btn-flat btn-block btn-default" onclick="start_sync(this);return false" data="'.$val->type.'"><i class="fa fa-play-circle"></i></a>';
    if ($val->a == $val->b){
        $btn = '<a href="javascript:;" class="btn-'.$val->type.' disabled btn btn-flat btn-block btn-success" onclick="start_sync(this);return false" data="'.$val->type.'"><i class="fa fa-stop-circle"></i></a>';
    }
    echo '<tr>
            <td>
                '.$val->data_name.'
                <div class="progress pwr_'.$i.'" style="display:none">
                    <div class="progress_'.$i.' progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                        0%
                    </div>
                </div>
            </td>
            <td class="lokal_'.$val->type.'">'.$val->a.'</td>
            <td class="remote_'.$val->type.'">'.$val->b.'</td>
            <td>'.$btn.'</td>';
    echo '</tr>';
    $i++;
}