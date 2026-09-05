<section>
    <?php
    if (isset($data['FormDesign']['TransMode'])) {
        switch ($data['FormDesign']['TransMode']) {
            case 'Delete':
                // <!-- Transaction Mode: Delete -->
                echo '<div class="card-body">';
                echo '    <button type="submit" class="btn btn-danger mr-1" name="btnConfirmDelete">Excluir</button>';
                echo '</div>';
                break;
            case 'Update':
                // <!-- Transaction Mode: Update -->
                echo '<div class="card-body">';
                echo '    <button type="submit" class="btn btn-primary mr-1" name="btnConfirmUpdate">Atualizar</button>';
                if (isset($data['FormDesign']['Buttons'])) {
                    foreach ($data['FormDesign']['Buttons'] as $item) {
                        if ($item['Type'] == 'submit') {
                            echo '    <button type="submit" class="' . $item['Class'] . ' mr-1" name="' . $item['Name'] . '">' . $item['Label'] . '</button>';
                        } else {
                            echo '    <a type="button" class="' . $item['Class'] . ' mr-1" name="' . $item['Name'] . '" href="' . $item['Link'] . '">' . $item['Label'] . '</a>';
                        }
                    }
                }
                echo '</div>';
                break;
            case 'Insert':
                // <!-- Transaction Mode: Insert -->
                echo '<div class="card-body">';
                echo '    <button type="submit" class="btn btn-success mr-1" name="btnConfirmInsert">Confirmar</button>';
                echo '</div>';
                break;
            case 'Display':
                // <!-- Transaction Mode: Display -->
                echo '<div class="card-body">';
                echo '    <button type="submit" class="btn btn-primary mr-1" name="btnActionEdit">Editar</button>';
                echo '    <button type="submit" class="btn btn-danger mr-1" name="btnActionDelete">Excluir</button>';
                if (isset($data['FormDesign']['Buttons'])) {
                    foreach ($data['FormDesign']['Buttons'] as $item) {
                        if ($item['Type'] == 'submit') {
                            echo '    <button type="submit" class="' . $item['Class'] . ' mr-1" name="' . $item['Name'] . '">' . $item['Label'] . '</button>';
                        } else {
                            echo '    <a type="button" class="' . $item['Class'] . ' mr-1" name="' . $item['Name'] . '" href="' . $item['Link'] . '">' . $item['Label'] . '</a>';
                        }
                    }
                }
                echo '</div>';
                break;

            case 'Readonly':
                // <!-- Transaction Mode: Readonly -->
                echo '<div class="card-body">';
                echo '    <button type="submit" class="btn btn-secondary mr-1" name="btnClose">Fechar</button>';
                echo '</div>';
                break;

            case 'Default':
                // <!-- Transaction Mode: Default -->
                echo '<div class="card-body">';
                echo '    <button type="submit" class="btn btn-primary mr-1" name="btnConfirm">Confirmar</button>';
                echo '</div>';
                break;

            case 'StartPage':
                // <!-- Transaction Mode: NextPage -->
                echo '<div class="card-body">';
                echo '    <button type="submit"" class="btn btn-primary mr-1" name="btnConfirmStartPage">Iniciar</a>';
                echo '</div>';
                break;
            case 'PreviusPageAndNextPage':
                // <!-- Transaction Mode: NextPage -->
                echo '<div class="card-body">';
                echo '    <a type="button" href="' . $data['FormDesign']['Tabs']['Items']['LinkPreviusPage'] . '" class="btn btn-secondary mr-1" name="btnConfirmPreviusPage">Voltar</a>';
                echo '    <button type="submit"" class="btn btn-primary mr-1" name="btnConfirmNextPage">Avançar</a>';
                echo '</div>';
                break;
            case 'PreviusPageAndFinish':
                // <!-- Transaction Mode: NextPage -->
                echo '<div class="card-body">';
                echo '    <a type="button" href="' . $data['FormDesign']['Tabs']['Items']['LinkPreviusPage'] . '" class="btn btn-secondary mr-1" name="btnConfirmPreviusPage">Voltar</a>';
                echo '    <button type="submit" class="btn btn-primary mr-1" name="btnConfirmFinish">Finalizar</button>';
                echo '</div>';
                break;

            default:
                // none //
                if (isset($data['FormDesign']['Buttons'])) {
                    foreach ($data['FormDesign']['Buttons'] as $item) {
                        if ($item['Type'] == 'submit') {
                            echo '    <button type="submit" class="' . $item['Class'] . ' mr-1" name="' . $item['Name'] . '">' . $item['Label'] . '</button>';
                        } else {
                            echo '    <a type="button" class="' . $item['Class'] . ' mr-1" name="' . $item['Name'] . '" href="' . $item['Link'] . '">' . $item['Label'] . '</a>';
                        }
                    }
                }
                break;
        }
    }
    ?>

</section>