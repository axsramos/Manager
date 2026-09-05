<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="/SBAdmin/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

<?php
if (isset($data['FormDesign']['Scripts']['Body'])) {
    foreach ($data['FormDesign']['Scripts']['Body'] as $scriptItem) {
        switch ($scriptItem) {
            case 'chart-area-demo':
                echo '<script src="/SBAdmin/demo/chart-area-demo.js"></script>';
                break;
                
            case 'chart-bar-demo':
                echo '<script src="/SBAdmin/demo/chart-bar-demo.js"></script>';
                break;

            case 'dataTables':
                echo '<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>';
                echo '<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>';
                echo '<script src="/SBAdmin/demo/datatables-demo.js"></script>';
                break;
            
            case 'tooltip':
                echo '<script>';
                echo '    $(function() {';
                echo '        $('."'".'[data-toggle="tooltip"]'."'".').tooltip()';
                echo '    })';
                echo '</script>';
                break;

            default:
                if (strrchr($scriptItem, '.js')) {
                    echo '<script src="' . $scriptItem . '"></script>';
                }
                break;
        }
    }
}
?>