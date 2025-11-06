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

        <div class="text-center">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalCriarProjeto">Criar Novo Projeto</button>          
        </div>


  <div id="mensagem-erro" class="alert alert-danger d-none" role="alert"></div>



  <table class="table table-striped" id="tabela-projetos">
    <thead class="table-dark">
      <tr>
        <th>Titulo</th>
        <th>Descrição</th>
        <th>Tecnologia</th>
        <th>Imagem</th>
        <th>Editar</th>
        <th>Eliminar</th>
      </tr>
    </thead>
    <tbody>
      <tr><td colspan="5" class="text-center">Carregando...</td></tr>
    </tbody>
  </table>
</div>


<!-- Modal Editar portfolio -->

<div class="modal fade" id="modalEditarProjeto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Projeto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="form-editar-projeto">
          <input type="hidden" name="action" value="alterar_portfolio">
          <input type="hidden" id="editar-id" name="id">
          <div class="mb-3">
            <label for="editar-titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="editar-titulo" name="titulo" required>
          </div>
          <div class="mb-3">
            <label for="editar-descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="editar-descricao" name="descricao" required></textarea>
            </div>
            <div class="mb-3">
            <label for="editar-tecnologia" class="form-label">Tecnologia</label>
            <input type="text" class="form-control" id="editar-tecnologia" name="tecnologia" required>
            </div>
            <div class="mb-3">
                <img id="imagem-preview" src="" alt="Preview da Imagem" class="img-fluid mb-2"/>
            </div>
            <div class="mb-3">
                
                <label for="editar-imagem" class="form-label">Escolher Imagem Nova</label>

                <input type="file" accept="image/*" class="form-control" id="editar-imagem" name="imagem">
            </div>
            <button type="submit" class="btn btn-primary">Guardar Alterações</button>
        </form>
        </div>
    </div>
    </div>
</div>
<!-- Modal Criar novo projeto -->

<div class="modal fade" id="modalCriarProjeto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Criar Novo Projeto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="form-criar-projeto">
            <input type="hidden" name="action" value="criar_portfolio">
          <div class="mb-3">
            <label for="criar-titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="criar-titulo" name="titulo" required>
            </div>
            <div class="mb-3">
            <label for="criar-descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="criar-descricao" name="descricao" required></textarea>
            </div>
            <div class="mb-3">
            <label for="criar-tecnologia" class="form-label">Tecnologia</label>
            <input type="text" class="form-control" id="criar-tecnologia" name="tecnologia" required>
            </div>
            <div class="mb-3">
                <img id="imagem-preview-criar" src=""  class="img-fluid mb-2"/>
                <label for="criar-imagem" class="form-label">Escolher Imagem</label>
                <input type="file" class="form-control" id="criar-imagem" name="imagem">
            </div>
            <button type="submit" class="btn btn-primary">Criar Projeto</button>
        </form>
        </div>
    </div>
    </div>
</div>


<!-- Modal Confirmar Excluir Projeto -->

<div class="modal fade" id="modalExcluirProjeto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar Exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Tem certeza que deseja eliminar este projeto?</p>
        <!-- campo oculto para armazenar o id -->
        <input type="hidden" id="excluir-id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btn-confirmar-excluir">Eliminar</button>
      </div>
    </div>
  </div>
</div>

