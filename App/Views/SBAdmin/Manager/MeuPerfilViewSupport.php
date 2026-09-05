<section>
    <form action="<?= $data['FormDesign']['Tabs']['Items'][1]['Link']; ?>" method="post">
        <div class="row row-cols-1 row-cols-md-2">

            <!-- Cadastro -->
            <div class="col">
                <div class="card-body">

                    <!-- General -->
                    <?php
                    if (!empty($data['FormData']['MAIL_SUPPORT'])) {
                        echo '<div class="form-floating mb-3">';
                        echo '    <p class="h4">Suporte ao produto </p>';
                        echo '    <p class="lead">';
                        echo '        Para obter mais Informações, entre em contato com a equipe de suporte.';
                        echo '    </p>';
                        echo '    <p><a href="mailto:' . $data['FormData']['MAIL_SUPPORT'] . '">' . $data['FormData']['MAIL_SUPPORT'] . '</a></p>';
                        echo '</div>';
                    }
                    ?>
                    
                    <!-- Packge -->
                    <?php
                    if (in_array(strtoupper(\App\Core\AuthSession::get()['PROFILE']), \App\Core\Config::getTypeUsersRestrictAccess())) {
                        echo '<div class="form-floating mt-5 mb-3">';
                        echo '    <p class="h4">Pacote de Instalação </p>';
                        echo '    <p class="lead">';
                        echo '        Para aplicar atualizações, configurações e correções, obtenha o pacote de instalação diretamente com o suporte.';
                        echo '    </p>';
                        echo '    <p>';
                        echo '        <a type="button" class="btn btn-primary" href="/Support/Package">Pacote de Instalação</a>';
                        echo '    </p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Form Button Controls -->
        <?php
        // include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/FormButtonControls.php');
        ?>
    </form>
</section>