<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
} else {
    session_destroy();
}
// Gerar token CSRF se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<div class="section text-white mt-5">
    <div class="row justify-content-center mt-4">
        <div class="row justify-content-center">
            <div class="col-12 text-center align-self-center py-5">
                <div class="section pb-5 pt-5 pt-sm-2 text-center">
                    <h6 class="mb-0 pb-3  fw-bold"><span>ENTRAR </span><span>REGISTAR</span></h6>
                    <input class="checkbox" type="checkbox" id="reg-log" name="reg-log"/>
                    <label for="reg-log"></label>
                    <div class="card-3d-wrap mx-auto">
                        <div class="card-3d-wrapper">

                            <!-- Login -->
                            <div class="card-front">
                                <div class="center-wrap">
                                    <div class="section text-center">
                                        <h4 class="mb-4 pb-3">Log In</h4>
                                        <form id="loginForm">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <div class="form-group">
                                                <input type="email" name="username" class="form-style" placeholder="Email" autocomplete="email" required>
                                                <i class="input-icon uil uil-at"></i>
                                            </div>  
                                            <div class="form-group mt-2">
                                                <input type="password" name="password" class="form-style" placeholder="Password" autocomplete="password" required>
                                                <i class="input-icon uil uil-lock-alt"></i>
                                            </div>
                                            <button type="submit" class="btn-login mt-4">Entrar</button>
                                        </form>
                                        <p class="mb-0 mt-4 text-center"><a href="#0" class="link">Esqueceu a palavra-passe?</a></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Sign Up -->
                            <div class="card-back">
                                <div class="center-wrap">
                                    <div class="section text-center">
                                        <h4 class="mb-4 pb-3">Sign Up</h4>
                                        <form id="signupForm">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <div class="form-group">
                                                <input type="text" name="firstname" class="form-style" placeholder="Nome" autocomplete="nome" required>
                                                <i class="input-icon uil uil-user"></i>
                                            </div>  
                                            <div class="form-group mt-2">
                                                <input type="text" name="lastname" class="form-style" placeholder="Apelido" autocomplete="apelido" required>
                                                <i class="input-icon uil uil-user"></i>
                                            </div>  
                                            <div class="form-group mt-2">
                                                <input type="email" name="email" class="form-style" placeholder="Email" autocomplete="email" required>
                                                <i class="input-icon uil uil-at"></i>
                                            </div>  
                                            <div class="form-group mt-2">
                                                <input type="password" name="password" class="form-style" placeholder="Password" autocomplete="password" required>
                                                <i class="input-icon uil uil-lock-alt"></i>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="password" name="confirm_password" class="form-style" placeholder="Confirmar Password" autocomplete="confirm_password" required>
                                                <i class="input-icon uil uil-lock-alt"></i>
                                            <button type="submit" class="btn-login mt-4">Criar Conta</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div> 
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal de Erro -->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header position-relative justify-content-center">
                <h5 class="modal-title text-danger">Aviso</h5>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalMessage"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>