<!DOCTYPE html>
<html lang="pt-BR">

<?php
include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Head.php');
?>

<body class="sb-nav-fixed">
    <?php
    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/TopMenu.php');
    ?>
    <div id="layoutSidenav">
        <?php
        include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/SideMenu.php');
        ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <?php
                    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Breadcrumb.php');
                    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/AlertMessage.php');
                    ?>

                    <!-- Tabs -->
                    <div class="card mb-4">
                        <?php
                        include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Tabs.php');
                        ?>

                        <!-- Content -->
                        <div class="card-body">
                            <?php
                            if (!empty($data['FormDesign']['Tabs']['LoadFile'])) {
                                include_once($data['FormDesign']['Tabs']['LoadFile']);
                            }
                            ?>
                        </div>
                        <!-- End Content -->

                    </div>
                    <!-- End Tabs -->

                </div>
            </main>

            <?php
            include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Footer.php');
            ?>
        </div>
    </div>

    <?php
    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/BodyScripts.php');
    ?>

    <!-- Modal -->
    <form action="<?= $data['FormDesign']['Tabs']['Items'][1]['Link']; ?>" method="post">
        <div class="modal fade" id="modalActions" tabindex="-1" role="dialog" aria-labelledby="modalActionsLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalActionsLabel"><?= ($data['FormDesign']['Modal']['Title'] ?? ''); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php
                        if (isset($data['FormDesign']['Modal']['LoadFile'])) {
                            include_once($data['FormDesign']['Modal']['LoadFile']);
                        }
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" name="btnConfirmModal" onclick="Enviar()">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Script para o modal #modalActions -->
    <script>
        $(document).ready(function() {
            $('#modalActions').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                if (typeof setModalProgramCode === 'function') {
                    setModalProgramCode(button.val());
                }
            });
        });

        function Enviar() {
            const itens = document.getElementById('listFunctionalities');
            let baseURL = (document.getElementById('BaseUrlSearch').value + document.getElementById('btnAuthorizedProgram').value).replace('/getData', '/setData');
            let itemId = '';
            let itemValue = '';
            let dataForm = {};
            dataForm['AuthorizedProgram'] = document.getElementById('btnAuthorizedProgram').textContent.toUpperCase();
            dataForm['Functionalities'] = [];

            for (let i = 0; i < itens.childElementCount; i++) {
                itemId = itens.children[i].getElementsByTagName('button')[0]['id'];
                itemValue = itens.children[i].getElementsByTagName('button')[0]['textContent'].substring(0, 1);
                dataForm['Functionalities'].push({
                    'CasFunCod': itemId,
                    'CasFunBlq': itemValue
                });
            }

            // enviar post //
            fetch(baseURL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dataForm)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                }).finally(() => {
                    $('#modalActions').modal('hide');
                    location.reload();
                });
        }
    </script>
</body>

</html>