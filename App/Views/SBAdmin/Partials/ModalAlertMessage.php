<!-- Modal -->
<div class="modal fade" id="modalAlertMessage" tabindex="-1" role="dialog" aria-labelledby="modalAlertMessageLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAlertMessageLabel"><?= $data['FormDesign']['Message']['Title']; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $data['FormDesign']['Message']['Description']; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
    <div>
        <input type="hidden" id="attMessageType" value="<?= $data['FormDesign']['Message']['Type']; ?>">
        <input type="hidden" id="attMessageTitle" value="<?= $data['FormDesign']['Message']['Title']; ?>">
        <input type="hidden" id="attMessageDescription" value="<?= $data['FormDesign']['Message']['Description']; ?>">
        <input type="hidden" id="attMessageWithModal" value="<?= $data['FormDesign']['Message']['WithModal']; ?>">
    </div>
</div>