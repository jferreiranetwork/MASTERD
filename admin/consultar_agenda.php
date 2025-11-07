<?php

session_start();

$session_timeout = 300; // 5 minutos


if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    // Sessão expirada por inatividade
    session_unset();     // limpa variáveis de sessão
    session_destroy();   // destrói a sessão
    header("Location: ../"); 
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time(); // atualiza o timestamp da última atividade

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../');
    exit();
}

?>

<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />


<div class="event-schedule-area-two bg-color pad100">
    <div class="container">

                        <!-- botão de adicionar evento -->
        <div class="text-center">
            <button class="btn btn-primary"  id="addConsultaModalLabel">SOLICITAR CONSULTA</button>            
        </div>
        <br>
        <hr>

        <div class="row">
            <div class="col-lg-12">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade active show" id="home" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th class="text-center" scope="col">Data</th>
                                        <th class="text-center" scope="col">Imagem</th>
                                        <th class="text-center" scope="col">Cliente</th>
                                        <th class="text-center" scope="col">Categoria</th>
                                        <th class="text-center" scope="col">Organizador</th>
                                        <th class="text-center" scope="col">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="eventos-tbody">

                                    <!-- Eventos serão carregados aqui via JavaScript -->

                                </tbody>
                            </table>
                        </div>
                        <hr>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<!-- modal editar estado -->
<div class="modal fade text-center" id="modalEditarEstado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center">
                <h5 class="modal-title text-center" id="editEstadoModalLabel">
                    Editar Estado da Consulta
                </h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-editar-estado">
                    <input type="hidden" id="edit-evento-id">
                    <div class="mb-3">
                        <select class="form-select text-center" id="edit-estado" required>
                            <option value="confirmada">Confirmar</option>
                            <option value="cancelada">Cancelar</option>
                            <option value="pendente">Pendente</option>
                            <option value="concluído">Realizado</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" id="btn-salvar-estado">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal adicionar consulta -->

<div class="modal fade text-center" id="modalAdicionarConsulta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center">
                <h5 class="modal-title text-center">
                    Adicionar Nova Consulta
                </h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-adicionar-consulta">
                    <div class="mb-3" id="cliente-options-container">

                        <!-- Opções serão carregadas via JavaScript -->

                    </div>
                    <div class="mb-3">
                        <label for="data-consulta" class="form-label">Data e Hora:</label>
                        <input type="datetime-local" class="form-control text-center" id="data-consulta" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria-consulta" class="form-label">Categoria:</label>
                        <input type="text" class="form-control text-center" id="categoria-consulta" required>
                    </div>
                    <div class="mb-3">
                        <label for="organizador-consulta" class="form-label">Organizador:</label>
                        <input type="text" class="form-control text-center" id="organizador-consulta" required>
                    </div>
                    <button type="submit" class="btn btn-primary" id="btn-adicionar-consulta">Adicionar Consulta</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal mensagem -->

<div class="modal fade text-center" id="modalMensagem" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-center">
        <h5 class="modal-title text-center" id="modalMensagemLabel">Mensagem</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalMensagemBody"></div>
    </div>
  </div>
</div>


