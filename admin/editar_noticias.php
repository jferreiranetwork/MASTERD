<?php
session_start();

$session_timeout = 300; // 5 minutos

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    session_unset();
    session_destroy();
    header("Location: ../");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../');
    exit();
}
?>

<div class="container mt-4">
    <div class="text-center">
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalCriarNoticia">
            Criar Nova Notícia
        </button>
    </div>

    <div id="mensagem-erro" class="alert alert-danger d-none" role="alert"></div>

    <table class="table table-striped" id="tabela-noticias">
        <thead class="table-dark">
            <tr>
                <th>Título</th>
                <th>Descrição</th>
                <th>Autor</th>
                <th>Imagem</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="6" class="text-center">Carregando...</td></tr>
        </tbody>
    </table>
</div>

<!-- Modal Editar Notícia -->
<div class="modal fade" id="modalEditarNoticia" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Notícia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="form-editar-noticia">
          <input type="hidden" name="action" value="alterar_noticia">
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
            <label for="editar-autor" class="form-label">Autor</label>
            <input type="text" class="form-control" id="editar-autor" name="autor" required>
          </div>

          <div class="mb-3">
            <img id="editar-imagem-preview" src="" class="img-fluid mb-2"/>
          </div>

          <div class="mb-3">
            <img id="imagem-preview-editar" src="" class="img-fluid mb-2"/>
            <label for="editar-imagem" class="form-label">Escolher Nova Imagem</label>
            <input type="file" accept="image/*" class="form-control" id="editar-imagem" name="imagem">
          </div>

          <button type="submit" class="btn btn-primary">Guardar Alterações</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Criar Nova Notícia -->
<div class="modal fade" id="modalCriarNoticia" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Criar Nova Notícia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="form-criar-noticia">
          <input type="hidden" name="action" value="criar_noticia">

          <div class="mb-3">
            <label for="criar-titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="criar-titulo" name="titulo" required>
          </div>

          <div class="mb-3">
            <label for="criar-descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="criar-descricao" name="descricao" required></textarea>
          </div>

          <div class="mb-3">
            <label for="criar-autor" class="form-label">Autor</label>
            <input type="text" class="form-control" id="criar-autor" name="autor" required>
          </div>

          <div class="mb-3">
            <img id="imagem-preview-criar" src="" class="img-fluid mb-2"/>
            <label for="criar-imagem" class="form-label">Escolher Imagem</label>
            <input type="file" class="form-control" id="criar-imagem" name="imagem">
          </div>

          <button type="submit" class="btn btn-primary">Criar Notícia</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmar Eliminar Noticia -->
<div class="modal fade" id="modalExcluirNoticia" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar Eliminar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Tem certeza que deseja eliminar esta notícia?</p>
        <input type="hidden" id="excluir-id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btn-confirmar-excluir">Eliminar</button>
      </div>
    </div>
  </div>
</div>
