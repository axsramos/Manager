<section>
    <button type="button" id="btnAuthorizedProgram" name="btnAuthorizedProgram" class="btn btn-danger btn-block" onclick="toggleAuthorization()" data-toggle="tooltip" data-placement="bottom" title="Clique para alternar entre Autorizado e Negado.">Negado</button>
    <table class="table mt-3">
        <thead>
            <tr>
                <th scope="col">Habilitado</th>
                <th scope="col">Funcionalidade do Programa</th>
            </tr>
        </thead>
        <tbody id="listFunctionalities">
            <tr>
                <td>
                    <button type="button" class="btn btn-success btn-block" data-toggle="tooltip" data-placement="bottom" title="Clique para alternar entre SIM e NÃO.">SIM</button>
                </td>
                <td>DISPLAY</td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" id="BaseUrlSearch" value="<?= $data['FormDesign']['Modal']['BaseUrlSearch']; ?>">
    <textarea id="areaContentAuthorized" rows="5" hidden ></textarea>
</section>

<script>
    async function setModalProgramCode(programCode) {
        const url = document.getElementById('BaseUrlSearch').value + programCode;
        const dataContent = await getDataContent(url);
        document.getElementById('btnAuthorizedProgram').value = programCode;
         // Limpa a lista de funcionalidades de forma eficiente
        document.getElementById('listFunctionalities').innerHTML = '';
        if (dataContent && dataContent.functionalities) {
            // btnAuthorizedProgram //
            applyBtnAuthorizedProgram(dataContent);

            // add html prefix //
            let template = `<tbody id="listFunctionalities">`;
            for (const functionality of dataContent.functionalities) {
                template = template + '<tr>';
                template = template + '  <td>';
                if (functionality.CasFunBlq) {
                    template = template + '    <button id="'+ functionality.CasFunCod +'" name="CasFunCod_'+ functionality.CasFunCod +'" type="button" class="btn btn-success btn-block" data-toggle="tooltip" data-placement="bottom" title="Clique para alternar entre SIM e NÃO." onclick="toggleFunctionalities(id)">SIM</button>';
                } else {
                    template = template + '    <button id="'+ functionality.CasFunCod +'" name="CasFunCod_'+ functionality.CasFunCod +'" type="button" class="btn btn-secondary btn-block" data-toggle="tooltip" data-placement="bottom" title="Clique para alternar entre SIM e NÃO." onclick="toggleFunctionalities(id)">NÃO</button>';
                }
                template = template + '  </td>';
                template = template + '  <td>' + functionality.CasFunDsc + '</td>';
                template = template + '</tr>';
            }
            // add html suffix //
            template = template + `</tbody>`;
            // add children html listFunctionalities //
            document.getElementById('listFunctionalities').innerHTML = template;
        }
    }

    async function getDataContent(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching data:', error);
            return null;
        }
    }

    function applyBtnAuthorizedProgram(dataContent) {
        document.getElementById('areaContentAuthorized').value = JSON.stringify(dataContent);
        document.getElementById('btnAuthorizedProgram').textContent = dataContent.Authorized ? 'Autorizado' : 'Negado';
        document.getElementById('btnAuthorizedProgram').className = dataContent.Authorized ? 'btn btn-success btn-block' : 'btn btn-danger btn-block';
    }

    function toggleAuthorization() {
        const dataContent = JSON.parse(document.getElementById('areaContentAuthorized').value);
        dataContent.Authorized = !dataContent.Authorized;
        applyBtnAuthorizedProgram(dataContent);
    }

    function toggleFunctionalities(functionalityCode) {
        // console.log(functionalityCode);
        document.getElementById(functionalityCode).textContent = document.getElementById(functionalityCode).textContent == 'SIM' ? 'NÃO' : 'SIM';
        document.getElementById(functionalityCode).className = document.getElementById(functionalityCode).textContent == 'SIM' ? 'btn btn-success btn-block' : 'btn btn-secondary btn-block';
    }
</script>
