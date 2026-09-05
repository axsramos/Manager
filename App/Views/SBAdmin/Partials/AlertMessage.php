<!-- Alert Messages -->
<?php
if (!empty($data['FormDesign']['Message']['Description'])) {
    echo '<div class="alert alert-'. strtolower($data['FormDesign']['Message']['Type']) .'" role="alert">';
    echo $data['FormDesign']['Message']['Description'];
    echo '</div>';
}
if ($data['FormDesign']['TransMode'] == 'Delete') {
    echo '<div class="alert alert-warning" role="alert">';
    echo 'Para confirmar a exclusão clique em EXCLUIR.';
    echo '</div>';
}
?>