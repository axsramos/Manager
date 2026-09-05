<section>
    <form action="<?= $data['FormDesign']['Tabs']['Items'][0]['Link']; ?>" method="post">
        <div class="card-body">
            <?php
            if ($data['FormData']) {
                echo '<div class="mb-4">';
                echo '    <div class="row">';
                echo '        <div class="col-sm-4 col-md-3 col-xl-2">';
                echo '            <div class="text-center">';
                echo '                <img src="' . $data['FormData']['CoverImage'] . '" class="img-fluid rounded" alt="...">';
                echo '            </div>';
                echo '        </div>';
                echo '        <div class="col-sm-8 col-md-9 col-xl-10">';
                echo '            <div class="card-body">';
                echo '                <div class="row">';
                echo '                    <div class="col-md-6">';
                echo '                        <h1 class="display-4">' . $data['FormData']['CasAppDsc'] . '</h1>';
                echo '                        <p>' . $data['FormData']['CasAppObs'] . '</p>';
                echo '                        ID: <small>' . $data['FormData']['CasAppCod'] . '</small>';
                echo '                    </div>';
                echo '                    <div class="col-md-6">';
                if (isset($data['FormData']['PACKAGED']['Version']) && ($data['FormData']['PACKAGED']['Version'] == $data['FormData']['CasAppVer'])) {
                    if ($data['FormData']['CONTRACT']['CONTRACTED']) {
                        echo '                <button type="submit" name="btnConfirm" class="btn btn-primary btn-lg mt-3" value="' . $data['FormData']['CasAppCod'] . '">Atualizar</button>';
                    } else {
                        echo '                <button type="submit" name="btnConfirm" class="btn btn-primary btn-lg mt-3" value="' . $data['FormData']['CasAppCod'] . '">Instalar</button>';
                    }
                    echo '                <div class="mt-3"><p>Última atualização: ' . $data['FormData']['LastUpdated'] . '</p></div>';
                }
                if (isset($data['FormData']['PACKAGED']['Version']) && ($data['FormData']['PACKAGED']['Version'] !== $data['FormData']['CasAppVer'])) {
                    echo '<div class="alert alert-warning mt-3" role="alert">';
                    echo '    O pacote de instalação não pertence a esta vessão.';
                    echo '</div>';
                }
                echo '                    </div>';
                echo '                </div>';
                echo '                <hr>';
                echo '                <!-- Badges -->';
                echo '                <div class="mb-3">';
                if ($data['FormData']['CasAppBlq'] == 'S') {
                    echo '                        <span class="badge badge-danger"> PRODUTO INATIVO </span> ';
                } else {
                    echo '                        <span class="badge badge-primary"> PRODUTO ATIVO </span> ';
                }
                if ($data['FormData']['CONTRACT']['CONTRACTED']) {
                    echo '                        <span class="badge badge-success"> CONTRATADO </span> ';
                } else {
                    echo '                        <span class="badge badge-dark"> NÃO CONTRATADO </span> ';
                }
                if ($data['FormData']['CasAppTst'] == 'S') {
                    echo '                        <span class="badge badge-warning"> EM TESTE </span> ';
                }
                echo '                    <span class="badge badge-secondary">' . $data['FormData']['CasAppVer'] . '</span>';
                echo '                </div>';
                echo '                <div class="mb-3">';
                echo '                    <p><a href="' . $data['FormData']['CasAppVerLnk'] . '">' . $data['FormData']['CasAppVerLnk'] . '</a></p>';
                echo '                </div>';
                echo '                ';
                echo '                <!-- Details -->';
                if (isset($data['FormData']['PACKAGED']['Overview'])) {
                echo '                <div class="mt-5 mb-3">';
                echo '                    <h4>Detalhes</h4>';
                echo '                </div>';
                echo '                <div class="mb-3">';
                echo '                    ' . $data['FormData']['PACKAGED']['Overview'];
                echo '                </div>';
                }
                if (isset($data['FormData']['PACKAGED']['Packages'])) {
                    echo '                <!-- Packages -->';
                    echo '                <div class="mt-5 mb-3">';
                    echo '                    <h4>Pacotes</h4>';
                    echo '                       <table class="table">';
                    echo '                           <thead>';
                    echo '                               <tr>';
                    echo '                                   <th scope="col">#</th>';
                    echo '                                   <th scope="col">Data</th>';
                    echo '                                   <th scope="col">Descrição</th>';
                    echo '                                   <th scope="col">Status</th>';
                    echo '                               </tr>';
                    echo '                           </thead>';
                    echo '                           <tbody>';
                    foreach($data['FormData']['PACKAGED']['Packages'] as $packageItem) {
                        echo '                               <tr>';
                        echo '                                   <th scope="row">' . $packageItem['Task'] . '</th>';
                        echo '                                   <td>' . $packageItem['Date'] . '</td>';
                        echo '                                   <td>' . $packageItem['Description'] . '</td>';
                        echo '                                   <td>' . $packageItem['Status'] . '</td>';
                        echo '                               </tr>';
                    }
                    echo '                           </tbody>';
                    echo '                       </table>';
                    echo '                </div>';
                }
                echo '            </div>';
                echo '        </div>';
                echo '    </div>';
                echo '</div>';
            } else {
                echo '<div class="text-center text-muted mb-3">';
                echo '  <div><i class="fas fa-inbox fa-6x"></i></div>';
                echo '  <p><h3>Este repositório está vazio.</h3></p>';
                echo '  <p>Os dados serão exibidas aqui.</p>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Form Button Controls -->
        <?php
        // include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/FormButtonControls.php');
        ?>
    </form>
</section>