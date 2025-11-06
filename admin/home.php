<?php

session_start();

$session_timeout = 300; // 5 minutos


if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    // Sessão expirada por inatividade
    session_unset();     // limpa variáveis de sessão
    session_destroy();   // destrói a sessão
    header("Location: ./"); 
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time(); // atualiza o timestamp da última atividade

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ./');
    exit();
}

?>
  
  <form id="form-editar-perfil">
    <div class="row mb-4">
      <!-- Secção: Editar Perfil -->
      <div class="col-md-6">
        <h2>Editar Perfil</h2>
        <div class="mb-3">
          <label for="nome" class="form-label">Nome:</label>
          <input type="text" class="form-control" autocomplete="nome" id="nome" name="nome" required 
                 value="<?php echo htmlspecialchars($_SESSION['nome']); ?>">
        </div>
        <div class="mb-3">
          <label for="apelido" class="form-label">Apelido:</label>
          <input type="text" class="form-control" autocomplete="apelido" id="apelido" name="apelido"  required
                 value="<?php echo htmlspecialchars($_SESSION['apelido']); ?>">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">E-mail:</label>
          <input type="email" class="form-control" autocomplete="email" id="email" name="email" required
                 value="<?php echo htmlspecialchars($_SESSION['email']); ?>">
        </div>
      </div>

      <!-- Secção: Alterar Palavra-passe -->
      <div class="col-md-6">
        <h2>Alterar Palavra-passe</h2>
        <div class="mb-3">
          <label for="pw_atual" class="form-label">Palavra-passe atual:</label>
          <input type="password" class="form-control" id="pw_atual" name="pw_atual" required>
        </div>
        <div class="mb-3">
          <label for="nova_pw" class="form-label">Nova palavra-passe:</label>
          <input type="password" class="form-control" id="nova_pw" name="nova_pw">
        </div>
        <div class="mb-3">
          <label for="confirmar_pw" class="form-label">Confirmar nova palavra-passe:</label>
          <input type="password" class="form-control" id="confirmar_pw" name="confirmar_pw">
        </div>
      </div>
    </div>

    <!-- Botão único para submeter tudo -->
    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary px-4">Guardar alterações</button>
    </div>
  </form>
