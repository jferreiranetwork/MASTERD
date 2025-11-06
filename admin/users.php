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


<div class="container mt-4">
  <h2>Lista de Utilizadores</h2>


  <div id="mensagem-erro" class="alert alert-danger d-none" role="alert"></div>



  <table class="table table-striped" id="tabela-users">
    <thead class="table-dark">
      <tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Função</th>
        <th>Editar</th>
        <th>Eliminar</th>
      </tr>
    </thead>
    <tbody>
      <tr><td colspan="5" class="text-center">Carregando...</td></tr>
    </tbody>
  </table>
</div>


<!-- Modal Editar Utilizador -->
<div class="modal fade" id="modalEditarUser" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Utilizador</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit-id">
        <div class="mb-3">
          <label for="edit-nome" class="form-label">Nome</label>
          <input type="text" class="form-control" id="edit-nome">
        </div>
        <div class="mb-3">
          <label for="edit-apelido" class="form-label">Apelido</label>
          <input type="text" class="form-control" id="edit-apelido">
        </div>
        <div class="mb-3">
          <label for="edit-email" class="form-label">Email</label>
          <input type="email" class="form-control" id="edit-email">
        </div>
        <div class="mb-3">
          <label for="edit-funcao" class="form-label">Função</label>
          <select id="edit-funcao" class="form-select">
            <option value="admin">Admin</option>
            <option value="cliente">Cliente</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button class="btn btn-primary" id="btn-salvar">Salvar</button>
      </div>
    </div>
  </div>
</div>
