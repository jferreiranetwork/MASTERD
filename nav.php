<nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">
  <div class="container-fluid">
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
      aria-controls="navbarNav"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav" id="color-link">

        <?php
        if (isset($_SESSION['user_id']) && isset($_SESSION['funcao']) && $_SESSION['funcao'] === 'admin') {
            echo '

                  <li class="nav-item">
                    <a class="nav-link " href="#" onclick="loadHomeAdmin()" > <i class="fa fa-user"></i>  Olá, ' . htmlspecialchars($_SESSION['fullname']) . '</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" href="#" onclick="users()">Consultar Users</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" href="#" onclick="consultar_agenda()">Consultar Agenda</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" <a href="#"  onclick="editarportfolio()">Editar Portfolio</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" <a href="#"  onclick="editarnoticias()">Editar Notícias</a>
                  </li>
            
                  <li class="nav-item">
                    <a class="nav-link" <a href="logout.php">SAIR</a>
                  </li>';


        
        
          } else if (isset($_SESSION['user_id']) && isset($_SESSION['funcao']) && $_SESSION['funcao'] === 'cliente') {

            echo '

                  <li class="nav-item">
                    <a class="nav-link " href="#" onclick="loadHomeAdmin()" > <i class="fa fa-user"></i>  Olá, ' . htmlspecialchars($_SESSION['fullname']) . '</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" href="#" onclick="consultar_agenda()">Consultar Agenda</a>
                  </li>
          
                  <li class="nav-item">
                    <a class="nav-link" <a href="logout.php">SAIR</a>
                  </li>';

          } else { 

            echo '
                  <li class="nav-item">
                    <a class="nav-link active" href="#" onclick="loadHome()">HOME</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#" onclick="loadPortfolio()">PORTFOLIO</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#" onclick="loadOrcamento()">ORÇAMENTO</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#" onclick="contactos()">ONDE ESTAMOS</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#" onclick="loadLogin()" >LOGIN</a>
                  </li>';
          }
        ?>
      </ul>
    </div>
  </div>
</nav>
