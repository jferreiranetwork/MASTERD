  


  
  
  document.addEventListener("DOMContentLoaded", function () {
    loadHome();
    loadNav();

  });
  

/**
 * Carrega o conteúdo da navbar para o container com id "navbar-container".
 *
 * Faz um fetch na página "nav.php", extrai o texto da resposta e
 * o coloca no elemento com id "navbar-container".
 *
 * @throws {Error} Erro ao carregar a navbar.
 */
function loadNav() {
    fetch("nav.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("navbar-container").innerHTML = data;
        })
}


// ocultar menu ao clicar fora do menu em dispositivos móveis

document.addEventListener('click', function(event) {
    const isClickInsideMenu = event.target.closest('.navbar');
    const isClickOnToggler = event.target.closest('.navbar-toggler');
    const menuCollapse = document.getElementById('navbarNav');
    if (!isClickInsideMenu && !isClickOnToggler && menuCollapse.classList.contains('show')) {
        const bsCollapse = new bootstrap.Collapse(menuCollapse, {
        toggle: false
        });
        bsCollapse.hide();
    }
});
/**
 * Carrega o conteúdo da página de orçamento.
 *
 * Faz um fetch na página "orcamento.html", extrai o texto da resposta e
 * o coloca no elemento com id "content". Em seguida, remove todos os
 * links com rel="stylesheet" que contenham "login.css" em seu href e
 * chama a função iniciarFormulario() para inicializar o formulário.
 */
function loadOrcamento() {
    fetch("orcamento.html")
        .then(response => response.text())
        .then(data => {
            document.getElementById("content").innerHTML = data;
            document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                if (link.href.includes('login.css')) {
                    link.remove();
                }
            });
            iniciarFormulario();
        });
}

/**
 * Carrega o conteúdo da página de início.
 *
 * Faz um fetch na página "home.html", extrai o texto da resposta e
 * o coloca no elemento com id "content". Em seguida, remove a classe
 * "login-bg" do body e adiciona a classe "default-bg". Por fim,
 * chama a função carregarNoticiasRTP() para carregar as notícias RTP.
 *
 * @throws {Error} Erro ao carregar o conteúdo da página de início.
 */
function loadHome() {
  fetch("home.html")
    .then(response => response.text())
    .then(data => {
      document.getElementById("content").innerHTML = data;
      document.body.classList.remove('login-bg');
      document.body.classList.add('default-bg');

     /** carregarNoticiasRTP(); */
      carregarNoticiasBD();
    })
    .catch(err => console.error("Erro ao carregar home:", err));
}


/**
 * Carrega o conteúdo da página de portfólio.
 *
 * Faz um fetch na página "galeria.html", extrai o texto da resposta e
 * o coloca no elemento com id "content". Em seguida, remove a classe
 * "login-bg" do body e adiciona a classe "default-bg". Por fim,
 * chama a função carregarProjetos() para carregar os projetos.
 *
 * @throws {Error} Erro ao carregar o conteúdo da página de portfólio.
 */
function loadPortfolio() {
  fetch('galeria.html')
    .then(response => response.text())
    .then(data => {
      document.getElementById("content").innerHTML = data;
      document.body.classList.remove('login-bg');
      document.body.classList.add('default-bg');

      carregarProjetos();

    });

}




/**
 * Carrega o conteúdo da página de contactos.
 *
 * Faz um fetch na página "contactos.html", extrai o texto da resposta e
 * o coloca no elemento com id "content". Em seguida, remove a classe
 * "login-bg" do body e adiciona a classe "default-bg". Por fim,
 * chama a função formcontato() para carregar o formulário de contato.
 *
 * @throws {Error} Erro ao carregar o conteúdo da página de contactos.
 */

function contactos() {
  fetch('contactos.html')
    .then(response => response.text())
    .then(data => {
      document.getElementById("content").innerHTML = data;
      document.body.classList.remove('login-bg');
      document.body.classList.add('default-bg');
      formcontato();
      

    });

}

/**
 * Carrega o conteúdo da página de login do administrador.
 *
 * Faz um fetch na página "admin/login.php", extrai o texto da resposta e
 * o coloca no elemento com id "content". Em seguida, remove a classe
 * "default-bg" do body e adiciona a classe "login-bg". Por fim,
 * adiciona um novo link para o estilo CSS "login.css" no head do
 * documento.
 *
 * @throws {Error} Erro ao carregar o conteúdo da página de login do administrador.
 */
function loadLogin() {
  fetch('admin/login.php')
    .then(response => response.text())
    .then(data => {
      document.getElementById("content").innerHTML = data;
            document.body.classList.remove('default-bg');
            document.body.classList.add('login-bg');
            const novoCSS = document.createElement("link");
            novoCSS.rel = "stylesheet";
            novoCSS.href = "./css/login.css";
            document.head.appendChild(novoCSS);

    });
    
  }



/**
 * Carrega o feed de notícias da RTP.
 *
 * Faz um fetch na API rss2json.com com a URL do feed da RTP e
 * extrai os dados da resposta. Em seguida, itera sobre os 6 primeiros
 * itens do feed e cria um elemento HTML para cada item. O elemento
 * HTML contém a imagem do item, o título do item e a data de publicação
 * do item. Por fim, adiciona os elementos HTML ao container com id
 * "noticias-rtp" e exibe o container.
 *
 * @throws {Error} Erro ao carregar o feed de notícias da RTP.

function carregarNoticiasRTP() {
  const feedURL = 'https://api.rss2json.com/v1/api.json?rss_url=https://www.rtp.pt/noticias/rss';

  fetch(feedURL)
    .then(res => res.json())
    .then(data => {
      const divnoticias = document.getElementById('noticias');
      const container = document.getElementById('noticias-rtp');
      container.innerHTML = ''; 
      data.items.slice(0, 6).forEach(item => {
        const imgMatch = item.description.match(/<img[^>]+src="([^">]+)"/);
        const imgUrl = imgMatch ? imgMatch[1] : 'https://via.placeholder.com/300x180?text=Sem+Imagem';
        const div = document.createElement('div');
        div.className = 'list-group-item';
        div.innerHTML = `
          <div class="d-flex mb-4">
            <img src="${imgUrl}" class="me-2" style="width:80px; height:60px; object-fit:cover;">
            <div>
              <a href="${item.link}" target="_blank" class="fw-bold">${item.title}</a>
              <div><small class="text-muted">${new Date(item.pubDate).toLocaleDateString()}</small></div>
            </div>
          </div>
        `;
        container.appendChild(div);
      });
      divnoticias.style.display = 'block';
    })
    .catch(err => {
      console.error('Erro ao carregar feed:', err);
      document.getElementById('noticias-rtp').innerHTML = '<p class="text-danger">Erro ao carregar notícias.</p>';
    });
}

 */

async function carregarNoticiasBD() {
  const container = document.getElementById('noticias-rtp');
  container.innerHTML = '';

  try {
    const res = await fetch('noticias_DB.php?action=listar_noticias', {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' }
    });

    const data = await res.json();

    if (data.status === "success") {
      const noticias = data.data || [];

      if (noticias.length > 0) {
        // Mostra apenas as 5 últimas
        const ultimas = noticias.slice(-5);

        ultimas.forEach(noticia => {
          const div = document.createElement('div');
          div.className = 'list-group-item';
          div.style.cursor = 'pointer';

          div.innerHTML = `
            <div class="d-flex mb-3">
              <img src="${noticia.imagem}" class="me-2" style="width:80px; height:60px; object-fit:cover;">
              <div>
                <span class="fw-bold text-primary">${noticia.titulo}</span>
                <div><small class="text-muted">${noticia.data_publicacao}</small></div>
              </div>
            </div>
          `;

          // ✅ Ao clicar, carrega a notícia no corpo principal
          div.addEventListener('click', () => carregarNoticiaCorpo(noticia.id));

          container.appendChild(div);
        });
      } else {
        container.innerHTML = '<p class="text-danger">Nenhuma notícia encontrada.</p>';
      }
    } else {
      container.innerHTML = '<p class="text-danger">Erro ao carregar notícias.</p>';
    }
  } catch (err) {
    console.error('Erro ao carregar notícias internas:', err);
    container.innerHTML = '<p class="text-danger">Erro ao carregar notícias.</p>';
  }
}


async function carregarNoticiaCorpo(id) {
  const corpo = document.querySelector('.col-lg-9');
  corpo.innerHTML = '<p>Carregando notícia...</p>';

  try {
    const res = await fetch(`noticias_DB.php?action=ver_noticia&id=${id}`);
    const data = await res.json();

    if (data.status === "success" && data.data) {
      const n = data.data || [];
      corpo.innerHTML = `
        <h2>${n.titulo}</h2>
        <small class="text-muted">${n.data_publicacao}</small><br>
        <img src="${n.imagem}" alt="${n.titulo}" style="max-width:100%; height:auto; border-radius:8px; margin:20px 0;">
        <p>${n.descricao}</p>
        <button class="btn btn-secondary mt-3" id="voltar">← Voltar</button>
      `;

      document.getElementById('voltar').addEventListener('click', () => {
        location.reload();
      });

    } else {
      corpo.innerHTML = '<p class="text-danger">Erro ao carregar a notícia.</p>';
    }
  } catch (err) {
    console.error('Erro ao carregar notícia:', err);
    corpo.innerHTML = '<p class="text-danger">Erro ao carregar a notícia.</p>';
  }
}





/**
 * Inicializa o formulário de orçamento.
 *
 * Esta função inicia o formulário de orçamento com os valores
 * predefinidos. Ela também define os listeners para os eventos
 * "change" e "input" nos elementos do formulário.
 *
 * @return {undefined}
 */
  function iniciarFormulario() {
    const tipoPagina = document.getElementById('tipo_pagina');
    const prazoMeses = document.getElementById('prazo_meses');
    const separadores = [
      'quem_somos',
      'onde_estamos',
      'galeria',
      'noticias',
      'redes_sociais'
    ];
    const valorInput = document.getElementById('valor');
    const errorMsg = document.getElementById('error_msg');
    const success_msg = document.getElementById('success_msg');
    const nome = document.getElementById('nome');
    const apelido = document.getElementById('apelido');
    const email = document.getElementById('email');
    const enviarBtn = document.getElementById('enviar');

/**
 * Atualiza o valor do orçamento com base nos valores
 * predefinidos.
 *
 * Esta função calcula o valor do orçamento com base no
 * tipo de página, prazo em meses e separadores selecionados.
 * Ela também aplica um desconto de 5% por mês, até um máximo
 * de 20%.
 *
 * @return {undefined}
 */
    function atualizarOrcamento() {
      errorMsg.textContent = '';

      const precoBase = parseFloat(tipoPagina.value);

      let meses = parseInt(prazoMeses.value);
      if (isNaN(meses) || meses < 1) meses = 1;

      let countSeparadores = 0;
      separadores.forEach(id => {
        if (document.getElementById(id).checked) countSeparadores++;
      });

      const precoSeparadores = countSeparadores * 400;
      const soma = precoBase + precoSeparadores;


      let desconto = meses * 0.05;
      if (desconto > 0.20) desconto = 0.20;


      const valorFinal = soma * meses * (1 - desconto);

      valorInput.value = valorFinal.toFixed(2);
    }

/**
 * Valida o formulário e verifica se todos os campos foram preenchidos
 * correctamente. Caso haja um erro, apresenta uma mensagem de erro e
 * move o scroll para o campo com erro.
 * 
 * @return {boolean} true se o formulário for válido, false caso contrário
 */
    function validarFormulario() {
      if (nome.value.trim() === '') {
        errorMsg.textContent = 'Por favor, introduza o seu nome.';
        errorMsg.style.display = 'block';
        nome.scrollIntoView({ behavior: 'smooth', block: 'center' });
        nome.focus();
        
        return false;
      }
      if (apelido.value.trim() === '') {
        errorMsg.textContent = 'Por favor, introduza o seu apelido.';
        errorMsg.style.display = 'block';
        apelido.scrollIntoView({ behavior: 'smooth', block: 'center' });
        apelido.focus();
        return false;
      }
      if (email.value.trim() === '') {
        errorMsg.textContent = 'Por favor, introduza o seu email.';
        errorMsg.style.display = 'block';
        email.scrollIntoView({ behavior: 'smooth', block: 'center' });
        email.focus();
        return false;
      }
      const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!regexEmail.test(email.value)) {
        errorMsg.textContent = 'Por favor, introduza um email válido.';
        errorMsg.style.display = 'block';
        email.scrollIntoView({ behavior: 'smooth', block: 'center' });
        email.focus();
        return false;
      }
      const meses = parseInt(prazoMeses.value);
      if (isNaN(meses) || meses < 1) {
        errorMsg.textContent = 'Por favor, introduza um prazo válido (mínimo 1 mês).';
        errorMsg.style.display = 'block';
        prazoMeses.scrollIntoView({ behavior: 'smooth', block: 'center' });
        prazoMeses.focus();
        return false;
      }
      if (tipoPagina.value === '0') {
        errorMsg.textContent = 'Por favor, selecione um tipo de página.';
        errorMsg.style.display = 'block';
        tipoPagina.scrollIntoView({ behavior: 'smooth', block: 'center' });
        tipoPagina.focus();
        return false;
      }

      const checkboxes = document.querySelectorAll('.checkbox-item input[type="checkbox"]');
      const selecionarSeparadores = Array.from(checkboxes).some(checkbox => checkbox.checked);

      if (!selecionarSeparadores) {
        errorMsg.textContent = 'Por favor, selecione pelo menos um Separador.';
        errorMsg.style.display = 'block';
        scrollTo(0, 0);
        return false;
      }
      return true;
    }

    tipoPagina.addEventListener('change', atualizarOrcamento);
    prazoMeses.addEventListener('input', atualizarOrcamento);
    separadores.forEach(id => {
      document.getElementById(id).addEventListener('change', atualizarOrcamento);
    });


    enviarBtn.addEventListener('click', e => {
      e.preventDefault();
      if (validarFormulario()) {
        errorMsg.style.display = 'none';
       success_msg.textContent = 'Pedido enviado com sucesso!';
       scrollTo(0, 0);
       success_msg.style.display = 'block';
       nome.value = '';
       apelido.value = '';
       email.value = '';
       prazoMeses.value = '1';
       tipoPagina.value = '300';
       separadores.forEach(id => {
        document.getElementById(id).checked = false;
        });
      valorInput.value = '285.00';



      }
    });


    atualizarOrcamento();
  }



function formcontato() {
  const officeLatLng = [38.733555, -9.141132];

  const map = L.map('map').setView(officeLatLng, 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
  }).addTo(map);

  const officeMarker = L.marker(officeLatLng).addTo(map).bindPopup('Onde Estamos').openPopup();

  function mostrarRota(userLatLng) {
    L.marker(userLatLng).addTo(map).bindPopup('A sua localização').openPopup();

    const latlngs = [userLatLng, officeLatLng];
    const polyline = L.polyline(latlngs, {color: 'blue'}).addTo(map);

    map.fitBounds(polyline.getBounds());
  }

  if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(
      position => {
        const userCoords = [position.coords.latitude, position.coords.longitude];
        mostrarRota(userCoords);
      },
      err => {
        console.warn(`Geolocalização falhou ou negada: ${err.message}`);
      }
    );
  } else {
    console.warn('Geolocalização não suportada pelo navegador.');
  }


}




document.addEventListener('submit', function(e) {
    const form = e.target;

    if (form && (form.id === 'loginForm' || form.id === 'signupForm')) {
        e.preventDefault();

        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        const endpoint = form.id === 'loginForm' ? './admin/login_db.php' : './admin/registo_db.php';

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'error') {
                document.getElementById('modalMessage').innerText = data.message;
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            } else if (data.status === 'success') {
              document.getElementById('modalMessage'). innerText = data.message;
              const loginModal = new bootstrap.Modal(document.getElementById('loginModal')); 
              loginModal.show();
              // Após fechar o modal, limpar o formulário e recarregar a página
              document.getElementById('loginModal').addEventListener('hidden.bs.modal', function () {
              form.reset(); // Limpar o formulário
              location.reload();
              });

            } else if (data.status === 'login_success') {
              window.location.href = data.redirect;  

            } else {
              
                document.getElementById('modalMessage').innerText = 'Resposta inesperada do servidor.';
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            }

        })
        .catch(error => {
            document.getElementById('modalMessage').innerText = 'Erro inesperado: ' + error.message;
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        });
    }
});



let projetos = []; 

/**
 * Carrega os projetos do servidor e os coloca no container com id "projetos-container".
 * 
 * Esta função faz um fetch na página "get_projetos.php", extrai os dados da resposta
 * e os coloca no container com id "projetos-container". Cada projeto é
 * colocado em um card com a classe "col-md-4" e contém a imagem do
 * projeto, o título do projeto e um botão para abrir o projeto.
 * 
 * Se o container com id "projetos-container" não for encontrado, uma mensagem
 * de erro será impressa no console.
 * 
 * @throws {Error} Erro ao carregar projetos.
 */
function carregarProjetos() {
  fetch('get_projetos.php')
    .then(res => res.json())
    .then(data => {
      projetos = data; 

      const container = document.getElementById('projetos-container');
      if (!container) {
        console.error('Elemento #projetos-container não encontrado!');
        return;
      }

      container.innerHTML = ''; 

      projetos.forEach((projeto, index) => {
        const col = document.createElement('div');
        col.className = 'col-md-4';

        col.innerHTML = `
          <div class="card shadow-sm h-100" style="cursor: pointer;" onclick="abrirProjeto(${index})">
            <img src="${projeto.imagem}" class="card-img-top" style="min-height: 200px; object-fit: cover;" alt="${projeto.titulo}">
            <div class="card-body">
              <h5 class="card-title">${projeto.titulo}</h5>
            </div>
          </div>
        `;

        container.appendChild(col);
      });
    })
    .catch(error => {
      console.error('Erro ao carregar projetos:', error);
    });
}



/**
 * Abre o modal com as informações do projeto de acordo com o
 * índice passado.
 *
 * @param {number} index - Índice do projeto no array
 *     projetos.
 *
 * @throws {Error} Se o índice for inválido ou se o projeto
 *     não existe.
 */
function abrirProjeto(index) {
  const projeto = projetos[index];
  document.getElementById("modalProjetoLabel").textContent = projeto.titulo;
  document.getElementById("imagemProjeto").src = projeto.imagem;
  document.getElementById("descricaoProjeto").textContent = projeto.descricao;
  document.getElementById("tecnologiasProjeto").textContent = projeto.tecnologias;

  const modal = new bootstrap.Modal(document.getElementById('modalProjeto'));
  modal.show();
}