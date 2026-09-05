<!-- Repositories -->
<?php

use App\Core\AuthSession;

if (isset(AuthSession::get()['REPOSITORIES']) && count(AuthSession::get()['REPOSITORIES']) > 1) {
    echo '<form>';
    echo '    <div class="btn-group">';
    echo '        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    echo '            Repositório';
    echo '        </button>';
    echo '        <div class="dropdown-menu dropdown-menu-right">';
    foreach (AuthSession::get()['REPOSITORIES'] as $key => $value) {
        $classActive = $value['CasRpsCod'] == AuthSession::get()['RPS_ID'] ? 'active' : '';
        echo '            <a class="dropdown-item ' . $classActive . '" type="button" href="/Auth/Login/Swap/' . $value['CasRpsCod'] . '">' . $value['CasRpsDsc'] . '</a>';
    }
    echo '        </div>';
    echo '    </div>';
    echo '</form>';
}
?>