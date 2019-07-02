<?php
if ($data){
    foreach ($data as $valPes){
        if ($valPes->ses_active == 0) {
            $status = '<span class="label label-default">DIRESET</span>';
        } elseif ($valPes->ses_active == 1){
            $status = '<span class="label label-primary">LOGIN</span>';
        } elseif ($valPes->ses_active == 2) {
            $status = '<span class="label label-success">Sedang Mengerjakan</span>';
        } elseif ($valPes->ses_active == 3){
            $status = '<span class="label label-warning">Finishing</span>';
        } else {
            $status = '<span class="label label-danger">Selesai Tes</span>';
        }
        if ($valPes->ses_upload == 1){
            $upload = '<span class="label label-success">Sudah Upload</span>';
        } else {
            $upload = '<span class="label label-default">Belum Upload</span>';
        }
        echo '<tr class="row_'.$valPes->ses_id.'">
                <td align="center"><input type="checkbox" name="ses_id[]" value="'.$valPes->ses_id.'"></td>
                <td align="center">'.$valPes->user_nopes.'</td>
                <td>'.$valPes->user_fullname.'</td>
                <td align="center">'.$status.'</td>
                <td>'.(round($valPes->ses_time_left/60)).' menit</td>
                <td align="center">'.$jmSoal.'</td>
                <td align="center">'.$valPes->jmlSelesai.'</td>
                <td>'.$upload.'</td>
              </tr>';
    }
}
?>
<script>
    $('#tbPes tbody input:checkbox').click(function () {
        var dtlen   = $('#tbPes tbody input:checkbox:checked').length;
        if (dtlen == 0){
            $('.btn-delete').addClass('disabled');
        } else {
            $('.btn-delete').removeClass('disabled');
        }
    })
</script>
