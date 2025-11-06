


document.addEventListener("DOMContentLoaded", function () {
    loadHomeAdmin();

  });



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
 * Carrega a página de usuários.
 *
 * Faz um fetch na página "users.php", extrai o texto da resposta e
 * o coloca no elemento com id "content-admin". Em seguida, chama a
 * função atualizarTabela() para carregar a tabela de usuários.
 * Adiciona um listener de cliques ao botão de salvar alterações e
 * outro listener de cliques aos botões de excluir.
 *
 * @throws {Error} Erro ao carregar o conteúdo da página de usuários.
 */
  function users() {

  fetch('users.php')
    .then(response => response.text())
    .then(async (data) => {
      document.getElementById("content-admin").innerHTML = data;
      await atualizarTabela();


      document.getElementById("btn-salvar").addEventListener("click", salvarUsuario);


      document.querySelectorAll(".btn-excluir").forEach(botao => {
        botao.addEventListener("click", excluirUsuario);
        
      });

    });
  }



/**
 * Abre o modal de edição de utilizador.
 *
 * @param {HTMLElement} botao - O botão que foi clicado.
 *
 * @throws {Error} Erro ao carregar utilizador.
 */
async function abrirModalEditar(botao) {
    const userId = botao.dataset.id;

    try {
      const response = await fetch(`users_get.php?id=${userId}`);
      const result = await response.json();

      if (result.status === 'success') {
        const user = result.data;

        document.getElementById("edit-id").value = user.id;
        document.getElementById("edit-nome").value = user.nome;
        document.getElementById("edit-apelido").value = user.apelido;
        document.getElementById("edit-email").value = user.email;
        document.getElementById("edit-funcao").value = user.funcao;

        const modal = new bootstrap.Modal(document.getElementById('modalEditarUser'));
        modal.show();
      } else {
        alert("Erro ao carregar utilizador: " + result.message);
      }
    } catch (err) {
      alert("Erro de rede: " + err.message);
    }
  }

  


/**
 * Salva as alterações feitas em um utilizador.
 *
 * @param {Event} e - O evento de clique no botão de salvar.
 *
 * @throws {Error} Erro ao salvar utilizador.
 */
async function salvarUsuario(e) {
  const id = document.getElementById("edit-id").value;
  const nome = document.getElementById("edit-nome").value;
  const apelido = document.getElementById("edit-apelido").value;
  const email = document.getElementById("edit-email").value;
  const funcao = document.getElementById("edit-funcao").value;

  const res = await fetch("editar_user.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id, nome, apelido, email, funcao })
  });

  const data = await res.json();

  if (data.status === "success") {
    alert("Utilizador atualizado com sucesso!");
    bootstrap.Modal.getInstance(document.getElementById('modalEditarUser')).hide();
    await atualizarTabela();
  } else {
    alert("Erro ao salvar: " + data.message);
  }
}



/**
 * Exclui um utilizador da base de dados.
 *
 * @param {Event} e - O evento de clique no botão de excluir.
 *
 * @throws {Error} Erro ao excluir utilizador.
 */
async function excluirUsuario(e) {
  const botao = e.currentTarget;
  const id = botao.dataset.id;

  if (!id) {
    alert("Nenhum utilizador selecionado.");
    return;
  }

  if (!confirm("Tem certeza que deseja eliminar este utilizador?")) {
    return;
  }

  const res = await fetch("excluir_user.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  });

  const data = await res.json();

  if (data.status === "success") {
    setTimeout(atualizarTabela, 200);
    alert("Utilizador eliminado com sucesso!");
    // aguarda alguns milisegundos para garantir que o backend processou a exclusão
    
  } else {
    alert("Erro ao eliminar: " + data.message);
  }
}

/**
 * Atualiza a tabela de usuários com os dados mais recentes
 *
 * @throws {Error} Erro ao obter dados ou ao renderizar a tabela
 */
  async function atualizarTabela() {
    const tabela = document.querySelector("#tabela-users tbody");
    const erro = document.getElementById("mensagem-erro");

    erro.classList.add("d-none");

    try {
      // Adiciona token CSRF ao cabeçalho
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;


      const headers = {
        'X-CSRF-Token': csrfToken,
        'Content-Type': 'application/json'
      };
      headers['Content-Type'] = 'application/json';
      const res = await fetch('users_db.php', {
       method: 'GET', // ou POST se quiser
       headers: headers
      });
      const data = await res.json();

      tabela.innerHTML = ""; // limpa tabela

      if (data.status === "success") {
        if (data.data.length > 0) {
          data.data.forEach(user => {
            const row = `
              <tr>
                <td>${user.username}</td>
                <td>${user.email}</td>
                <td>${user.funcao}</td>
                <td><button class="btn btn-sm btn-primary btn-editar"  data-id="${user.id}">Editar</button></td>
                <td><button class="btn btn-sm btn-danger btn-excluir"  data-id="${user.id}">Eliminar</button></td>
              </tr>
            `;
            tabela.innerHTML += row;

            // Editar
            document.querySelectorAll(".btn-editar").forEach(botao => {
            botao.addEventListener("click", () => abrirModalEditar(botao));
            });

            // Excluir
            document.querySelectorAll(".btn-excluir").forEach(botao => {
            botao.addEventListener("click", excluirUsuario);
            });
          });
        } else {
          tabela.innerHTML = `<tr><td colspan="5" class="text-center">Nenhum utilizador encontrado.</td></tr>`;
        }
      } else {
        erro.textContent = data.message || "Erro ao obter dados.";
        erro.classList.remove("d-none");
      }





    } catch (err) {
      erro.textContent = "Erro de rede: " + err.message;
      erro.classList.remove("d-none");
    }
  }





/**
 * Carrega a página de início do administrador.
 *
 * Faz um fetch na página "home.php", extrai o texto da resposta e
 * o coloca no elemento com id "content-admin".
 *
 * @throws {Error} Erro ao carregar o conteúdo da página de início do administrador.
 */
function loadHomeAdmin() {

  fetch('home.php')
    .then(response => response.text())
    .then(data => {
      document.getElementById("content-admin").innerHTML = data;
      enviarPerfil();
    });
  }


/**
 * Carrega a página de edição do portfólio.
 *
 * Faz um fetch na página "editarportfolio.php", extrai o texto da resposta e
 * o coloca no elemento com id "content-admin".
 *
 * @throws {Error} Erro ao carregar o conteúdo da página de edição do portfólio.
 */
async function editarportfolio() {
  const res = await fetch('editarportfolio.php');
  const html = await res.text();
  document.getElementById("content-admin").innerHTML = html;
  await atualizarTabelaProjetos();
  configurarEventosGlobais();
}

/** Carrega a página de edição das notícias.
 * 
 * Faz um fetch na página "editar_noticias.php", extrai o texto da resposta e
 * o coloca no elemento com id "content-admin".
 * * @throws {Error} Erro ao carregar o conteúdo da página de edição das notícias.
 */

function editarnoticias() {
  fetch('editar_noticias.php')
    .then(response => response.text())
    .then(async data => {
      document.getElementById("content-admin").innerHTML = data;
      await atualizarTabelaNoticias();
      configurarModaisNoticias();

    });
  }

// -------------------- CONSULTAR AGENDA --------------------
function consultar_agenda() {
  fetch('consultar_agenda.php')
    .then(response => response.text())
    .then(async data => {
      document.getElementById("content-admin").innerHTML = data;
      configurarModais(); // Configura modais apenas uma vez
      await carregarEventos();
    });
}


// -------------------- enviar perfil user --------------------

async function enviarPerfil() {
  


const formCriar = document.getElementById('form-editar-perfil');

formCriar.addEventListener('submit', async function(event) {
  event.preventDefault(); 

  const formData = new FormData(formCriar);
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  try {
    const res = await fetch('editar_perfil_db.php?action=editar_perfil', {
      method: 'POST',
      headers: { 'X-CSRF-Token': csrfToken },
      body: formData
    });

    const data = await res.json();

    if (data.status === "success") {
      alert("Noticia criada com sucesso!");
      atualizarTabelaNoticias();
      bootstrap.Modal.getInstance(document.getElementById("modalCriarNoticia")).hide();
      formCriar.reset();
    } else {
      alert(data.message || "Erro ao criar projeto.");
    }
    } catch (err) {
    alert("Erro de rede: " + err.message);
  }
});

}

// -------------------- CARREGAR EVENTOS --------------------
async function carregarEventos() {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const response = await fetch('agenda_controller.php', {
      headers: {
        'X-CSRF-Token': csrfToken,
        'Content-Type': 'application/json'
      }
    });

    const data = await response.json();
    const tbody = document.getElementById("eventos-tbody");
    tbody.innerHTML = "";

    if (data.status === 'error') {
      tbody.innerHTML = `
        <tr><td colspan="6" class="text-center text-danger">${data.message}</td></tr>`;
      return;
    }

    const estadoParaClasse = {
      "pendente": "btn-warning",
      "confirmada": "btn-success",
      "cancelada": "btn-danger",
      "concluído": "btn-secondary"
    };

    (data.data || []).forEach(evento => {
      const estado = evento.estado.toLowerCase();
      const classeEstado = estadoParaClasse[estado] || "btn-light";
      const textoEstado = estado.charAt(0).toUpperCase() + estado.slice(1);

      const tr = document.createElement("tr");
      tr.classList.add("inner-box");
      tr.innerHTML = `
        <th scope="row"><div class="event-date"><p>${evento.data}</p></div></th>
        <td><div class="event-img"><img src="${evento.imagem_utilizador}" alt="" style="width:70px;height:70px;" /></div></td>
        <td><div class="event-wrap"><p>${evento.nome_utilizador}</p></div></td>
        <td><div class="event-wrap"><small>${evento.categoria}</small></div></td>
        <td><div class="event-wrap"><small>${evento.organizador}</small></div></td>
        <td>
          <button type="button" class="btn ${classeEstado}" 
            name="estado" 
            data-id="${evento.id}" 
            data-funcao="${data.funcao}" 
            data-estado="${estado}">
            ${textoEstado}
          </button>
        </td>`;
      tbody.appendChild(tr);
    });

  } catch (error) {
    console.error("Erro ao carregar eventos:", error);
  }
}

// -------------------- CONFIGURAR MODAIS --------------------
function configurarModais() {

  // -------- MODAL EDITAR ESTADO --------
  document.addEventListener('click', function (event) {
    if (event.target && event.target.name === 'estado') {
      const botao = event.target;
      const eventoId = botao.getAttribute('data-id');
      const estadoAtual = botao.getAttribute('data-estado');
      const funcaoEvento = botao.getAttribute('data-funcao');

      const selectEstado = document.getElementById('edit-estado');
      const btnSalvar = document.getElementById('btn-salvar-estado');
      document.getElementById('edit-evento-id').value = eventoId;

      // Mostrar opções corretas
      if (funcaoEvento === 'cliente') {
        Array.from(selectEstado.options).forEach(opt => opt.hidden = opt.value !== 'cancelada');
        selectEstado.value = 'cancelada';
      } else {
        Array.from(selectEstado.options).forEach(opt => opt.hidden = false);
        selectEstado.value = estadoAtual;
      }

      // Desativar se estado final
      const estadoFinal = ['cancelada', 'concluído'];
      const desativar = funcaoEvento === 'cliente' && estadoFinal.includes(estadoAtual);
      selectEstado.disabled = desativar;
      btnSalvar.disabled = desativar;

      new bootstrap.Modal(document.getElementById('modalEditarEstado')).show();
    }
  });

  const formEditar = document.getElementById('form-editar-estado');
  if (formEditar) {
    formEditar.addEventListener('submit', async e => {
      e.preventDefault();
      const id = document.getElementById('edit-evento-id').value;
      const estado = document.getElementById('edit-estado').value;
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

      try {
        const res = await fetch('agenda_controller.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
          },
          body: JSON.stringify({ id, estado })
        });
        const data = await res.json();
        if (data.status === 'success') {
          bootstrap.Modal.getInstance(document.getElementById('modalEditarEstado')).hide();
          await carregarEventos();
          mostrarMensagem(data.message);
        } else {
          mostrarMensagem(data.message || 'Erro ao atualizar estado.', 'Erro');
        }
      } catch (error) {
        mostrarMensagem('Erro de rede: ' + error.message, 'Erro');
      }
    });
  }

// -------- MODAL ADICIONAR CONSULTA --------
const addButton = document.getElementById('addConsultaModalLabel');
if (addButton) {
  addButton.addEventListener('click', async function () {
    const container = document.getElementById('cliente-options-container');
    while (container.lastElementChild) container.removeChild(container.lastElementChild);

    const modal = new bootstrap.Modal(document.getElementById('modalAdicionarConsulta'));
    modal.show();

    try {
      // Busca o papel do utilizador e lista de clientes
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      const res = await fetch('agenda_controller.php', {
        headers: {
          'X-CSRF-Token': csrfToken,
          'Content-Type': 'application/json'
        }
      });
      const data = await res.json();

      const userRole = data.funcao;  // ← vindo do PHP
      const clientes = data.clientes || [];

      if (userRole === 'admin') {
        const label = document.createElement('label');
        label.textContent = 'Cliente:';
        label.classList.add('form-label');

        const select = document.createElement('select');
        select.id = 'cliente-consulta';
        select.classList.add('form-select', 'text-center');
        select.required = true;

        if (clientes.length > 0) {
          clientes.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.fullname;
            select.appendChild(opt);
          });
        } else {
          const opt = document.createElement('option');
          opt.value = '';
          opt.textContent = 'Nenhum cliente disponível';
          select.appendChild(opt);
        }

        container.appendChild(label);
        container.appendChild(select);
      } 

    } catch (error) {
      console.error("Erro ao carregar função ou clientes:", error);
      mostrarMensagem("Erro ao preparar o formulário de consulta.", "Erro");
    }
  });
}


// -------- SUBMISSÃO DO FORM --------
const formAdicionar = document.getElementById('form-adicionar-consulta');
if (formAdicionar) {
  formAdicionar.addEventListener('submit', async e => {
    e.preventDefault();

    const dataHora = document.getElementById('data-consulta').value.replace('T', ' ') + ':00';
    const cliente = document.getElementById('cliente-consulta')?.value || null;
    const organizador = document.getElementById('organizador-consulta').value;
    const categoria = document.getElementById('categoria-consulta').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    try {
      const res = await fetch('agenda_controller.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({
          tipo: 'adicionar_consulta',
          data_hora: dataHora,
          organizador: organizador,
          categoria : categoria,
          cliente_id: cliente
        })
      });
      const data = await res.json();

      if (data.status === 'success') {
        bootstrap.Modal.getInstance(document.getElementById('modalAdicionarConsulta')).hide();
        formAdicionar.reset();
        mostrarMensagem(data.message, 'Sucesso');
        await carregarEventos();
      } else {
        mostrarMensagem('Erro ao adicionar consulta: ' + (data.message || 'Erro desconhecido.'), 'Erro');
      }
    } catch (error) {
      mostrarMensagem('Erro de rede: ' + error.message, 'Erro');
    }
  });
}

}



// -------------------- MODAL DE MENSAGENS --------------------
function mostrarMensagem(mensagem, titulo = 'Aviso') {
  const modal = document.getElementById('modalMensagem');
  document.getElementById('modalMensagemLabel').innerText = titulo;
  document.getElementById('modalMensagemBody').innerText = mensagem;
  new bootstrap.Modal(modal).show();
}


/**
 * Atualiza a tabela dos projetos com os dados mais recentes
 *
 * @throws {Error} Erro ao obter dados ou ao renderizar a tabela
 */
async function atualizarTabelaProjetos() {
  const tabela = document.querySelector("#tabela-projetos tbody");
  const erro = document.getElementById("mensagem-erro");

  erro.classList.add("d-none");
  erro.textContent = '';

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const params = new URLSearchParams({ action: 'listar_portfolio' });

    const res = await fetch(`editarportfolio_db.php?${params.toString()}`, {
      method: 'GET',
      headers: { 'X-CSRF-Token': csrfToken }
    });

    const data = await res.json();

    tabela.innerHTML = "";

    if (data.status === "success") {
      const projetos = data.projetos || [];

      if (projetos.length > 0) {
        let html = "";
        projetos.forEach(projeto => {
          html += `
            <tr>
              <td>${projeto.titulo}</td>
              <td>${projeto.descricao}</td>
              <td>${projeto.tecnologia}</td>
              <td>${projeto.imagem ? `<img src="${projeto.imagem}" alt="${projeto.titulo}" style="max-width: 100px; max-height: 80px; object-fit: cover;">` : ''}</td>
              <td><button class="btn btn-sm btn-primary btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditarProjeto" data-id="${projeto.id}">Editar</button></td>
              <td><button class="btn btn-sm btn-danger btn-excluir" data-bs-toggle="modal" data-bs-target="#modalExcluirProjeto" data-id="${projeto.id}">Eliminar</button></td>
            </tr>
          `;
        });
        tabela.innerHTML = html;
      } else {
        tabela.innerHTML = `<tr><td colspan="6" class="text-center">Nenhum projeto encontrado.</td></tr>`;
      }
    } else {
      erro.textContent = data.message || "Erro ao obter dados.";
      erro.classList.remove("d-none");
    }
  } catch (err) {
    erro.textContent = "Erro de rede: " + err.message;
    erro.classList.remove("d-none");
  }
}

// Configura eventos globais para botões de editar e excluir

function configurarEventosGlobais() {
  const tabela = document.querySelector("#tabela-projetos tbody");

  tabela.addEventListener("click", async (e) => {
    if (e.target.classList.contains("btn-editar")) {
      abrirModalEditar(e.target.dataset.id);
    }
    

    if (e.target.classList.contains("btn-excluir")) {
      // o ID será tratado pelo evento show.bs.modal do próprio modal
    }
  });


// Modal editar
// Função para abrir modal e preencher dados
async function abrirModalEditar(id) {
  const modal = document.getElementById("modalEditarProjeto");
  const formulario = modal.querySelector("form");
  formulario.reset();

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  try {
    const res = await fetch(`editarportfolio_db.php?id=${id}`, {
      method: 'GET',
      headers: { 'X-CSRF-Token': csrfToken }
    });

    const data = await res.json();

    if (data.status === "success") {
      const projeto = data.projeto;
      formulario.querySelector('[name="id"]').value = projeto.id;
      formulario.querySelector('[name="titulo"]').value = projeto.titulo;
      formulario.querySelector('[name="descricao"]').value = projeto.descricao;
      formulario.querySelector('[name="tecnologia"]').value = projeto.tecnologia;

      const imgPreview = formulario.querySelector("#imagem-preview");
      imgPreview.src = projeto.imagem || "";
    } else {
      alert(data.message || "Erro ao carregar projeto.");
    }
  } catch (err) {
    alert("Erro de rede: " + err.message);
  }
}


// Função para redimensionar imagem mantendo proporção quando exceder os limites
async function redimensionarImagem(file, maxWidth, maxHeight) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    const url = URL.createObjectURL(file);

    img.onload = () => {
      let { width, height } = img;

      // Se a imagem for maior que os limites, redimensiona proporcionalmente
      if (width > maxWidth || height > maxHeight) {
        const ratio = Math.min(maxWidth / width, maxHeight / height);
        width = width * ratio;
        height = height * ratio;
      }

      const canvas = document.createElement('canvas');
      canvas.width = width;
      canvas.height = height;

      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0, width, height);

      canvas.toBlob((blob) => {
        if (blob) {
          resolve(new File([blob], file.name, { type: blob.type }));
        } else {
          reject(new Error("Erro ao converter a imagem"));
        }
      }, file.type || 'image/jpeg', 0.9);

      URL.revokeObjectURL(url);
    };

    img.onerror = () => {
      reject(new Error("Erro ao carregar a imagem"));
      URL.revokeObjectURL(url);
    };

    img.src = url;
  });
}

// Preview da imagem antes do envio
document.getElementById('editar-imagem').addEventListener('change', function(event) {
  const preview = document.getElementById('imagem-preview');
  const file = event.target.files[0];
  if (file) {
    preview.src = URL.createObjectURL(file);
  } else {
    preview.src = ""; // limpa preview se não tiver arquivo
  }
});

// Submeter formulário que foi editado via AJAX com redimensionamento da imagem
const formEditar = document.getElementById('form-editar-projeto');

formEditar.addEventListener('submit', async function(event) {
  event.preventDefault(); // evita reload da página

  const formData = new FormData(formEditar);
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  const inputFile = formEditar.querySelector('input[type="file"][name="imagem"]');
  const file = inputFile.files[0];

  // Se tem arquivo, redimensiona antes de enviar
  if (file) {
    try {
      const fileRedimensionado = await redimensionarImagem(file, 400, 400); // ajustar maxWidth e maxHeight aqui
      formData.set('imagem', fileRedimensionado, fileRedimensionado.name);
    } catch (err) {
      alert("Erro ao redimensionar imagem: " + err.message);
      return;
    }
  }

  try {
    const res = await fetch('editarportfolio_db.php', {
      method: 'POST',
      headers: { 'X-CSRF-Token': csrfToken },
      body: formData
    });

    const data = await res.json();

    if (data.status === "success") {
      alert("Projeto atualizado com sucesso!");
      atualizarTabelaProjetos(); // atualiza tabela sem reload
      bootstrap.Modal.getInstance(document.getElementById("modalEditarProjeto")).hide();
    } else {
      alert(data.message || "Erro ao atualizar projeto.");
    }
  } catch (err) {
    alert("Erro de rede: " + err.message);
  }
});



// --- Modal Eliminar ---
const modalExcluir = document.getElementById('modalExcluirProjeto');

modalExcluir.addEventListener('show.bs.modal', function(event) {
  const button = event.relatedTarget; // botão que abriu o modal
  const id = button.getAttribute('data-id'); // pega o ID do projeto
  let inputHidden = modalExcluir.querySelector('#excluir-id');

  if (!inputHidden) {
    inputHidden = document.createElement('input'); // cria se não existir
    inputHidden.type = 'hidden';
    inputHidden.id = 'excluir-id';
    modalExcluir.appendChild(inputHidden);
  }

  inputHidden.value = id;
});


// --- Modal confirmar Eliminar ---

document.getElementById("btn-confirmar-excluir").addEventListener("click", async () => {
  const id = modalExcluir.querySelector('#excluir-id').value;
  if (!id) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  try {
    const res = await fetch('editarportfolio_db.php', {
      method: 'DELETE', // ou POST com ação "delete"
      headers: { 'X-CSRF-Token': csrfToken, 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });

    const data = await res.json();

    if (data.status === "success") {
      alert("Projeto eliminado com sucesso!");
      atualizarTabelaProjetos();
      bootstrap.Modal.getInstance(modalExcluir).hide();
    } else {
      alert(data.message || "Erro ao eliminar projeto.");
    }
  } catch (err) {
    alert("Erro de rede: " + err.message);
  }
});





// submter formulario de um projeto novo

// Preview da imagem antes do envio
document.getElementById('criar-imagem').addEventListener('change', function(event) {
  const preview = document.getElementById('imagem-preview-criar');
  const file = event.target.files[0];
  if (file) {
    preview.src = URL.createObjectURL(file);
  } else {
    preview.src = ""; // limpa preview se não tiver arquivo
  }
});


const formCriar = document.getElementById('form-criar-projeto');

formCriar.addEventListener('submit', async function(event) {
  event.preventDefault(); // evita reload da página

  const formData = new FormData(formCriar);
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  try {
    const res = await fetch('editarportfolio_db.php', {
      method: 'POST',
      headers: { 'X-CSRF-Token': csrfToken },
      body: formData
    });

    const data = await res.json();

    if (data.status === "success") {
      alert("Projeto criado com sucesso!");
      atualizarTabelaProjetos(); // atualiza tabela sem reload
      bootstrap.Modal.getInstance(document.getElementById("modalCriarProjeto")).hide();
    } else {
      alert(data.message || "Erro ao criar projeto.");
    }
    } catch (err) {
    alert("Erro de rede: " + err.message);
  }
});


}


async function atualizarTabelaNoticias() {
  const tabela = document.querySelector("#tabela-noticias tbody");
  const erro = document.getElementById("mensagem-erro");

  erro.classList.add("d-none");
  erro.textContent = '';

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const params = new URLSearchParams({ action: 'listar_noticias' });

    const res = await fetch(`editar_noticias_db.php?${params.toString()}`, {
      method: 'GET',
      headers: { 'X-CSRF-Token': csrfToken }
    });

    const data = await res.json();

    tabela.innerHTML = "";

    if (data.status === "success") {
      const noticias = data.data || [];

      if (noticias.length > 0) {
        let html = "";
        noticias.forEach(noticia => {
          html += `
            <tr>
              <td>${noticia.titulo}</td>
              <td>${noticia.descricao}</td>
              <td>${noticia.autor}</td>
              <td>${noticia.imagem ? `<img src="${noticia.imagem}"  style="max-width: 100px; max-height: 80px; object-fit: cover;">` : ''}</td>
              <td><button class="btn btn-sm btn-primary btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditarNoticia" data-id="${noticia.id}" data-titulo="${noticia.titulo}" data-descricao="${noticia.descricao}" data-autor="${noticia.autor}" data-imagem="${noticia.imagem}">Editar</button></td>
              <td><button class="btn btn-sm btn-danger btn-excluir" data-bs-toggle="modal" data-bs-target="#modalExcluirNoticia" data-id="${noticia.id}">Eliminar</button></td>
            </tr>
          `;
        });
        tabela.innerHTML = html;
      } else {
        tabela.innerHTML = `<tr><td colspan="6" class="text-center">Nenhuma noticia encontrado.</td></tr>`;
      }
    } else {
      erro.textContent = data.message || "Erro ao obter dados.";
      erro.classList.remove("d-none");
    }
  } catch (err) {
    erro.textContent = "Erro de rede: " + err.message;
    erro.classList.remove("d-none");
  }
}





function configurarModaisNoticias() {



// Preview da imagem antes do envio
document.getElementById('criar-imagem').addEventListener('change', function(event) {
  const preview = document.getElementById('imagem-preview-criar');
  const file = event.target.files[0];
  if (file) {
    preview.src = URL.createObjectURL(file);
  } else {
    preview.src = ""; 
  }
});

// Preview da imagem antes do envio
document.getElementById('editar-imagem').addEventListener('change', function(event) {
  const preview = document.getElementById('imagem-preview-editar');
  const file = event.target.files[0];
  if (file) {
    preview.src = URL.createObjectURL(file);
  } else {
    preview.src = ""; // limpa preview se não tiver arquivo
  }
});


// -------- SUBMISSÃO DO FORM --------
const formCriar = document.getElementById('form-criar-noticia');

formCriar.addEventListener('submit', async function(event) {
  event.preventDefault(); 

  const formData = new FormData(formCriar);
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  try {
    const res = await fetch('editar_noticias_db.php?action=criar_noticia', {
      method: 'POST',
      headers: { 'X-CSRF-Token': csrfToken },
      body: formData
    });

    const data = await res.json();

    if (data.status === "success") {
      alert("Noticia criada com sucesso!");
      atualizarTabelaNoticias();
      bootstrap.Modal.getInstance(document.getElementById("modalCriarNoticia")).hide();
      formCriar.reset();
    } else {
      alert(data.message || "Erro ao criar projeto.");
    }
    } catch (err) {
    alert("Erro de rede: " + err.message);
  }
});


// Função para redimensionar imagem mantendo proporção quando exceder os limites
async function redimensionarImagem(file, maxWidth, maxHeight) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    const url = URL.createObjectURL(file);

    img.onload = () => {
      let { width, height } = img;

      // Se a imagem for maior que os limites, redimensiona proporcionalmente
      if (width > maxWidth || height > maxHeight) {
        const ratio = Math.min(maxWidth / width, maxHeight / height);
        width = width * ratio;
        height = height * ratio;
      }

      const canvas = document.createElement('canvas');
      canvas.width = width;
      canvas.height = height;

      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0, width, height);

      canvas.toBlob((blob) => {
        if (blob) {
          resolve(new File([blob], file.name, { type: blob.type }));
        } else {
          reject(new Error("Erro ao converter a imagem"));
        }
      }, file.type || 'image/jpeg', 0.9);

      URL.revokeObjectURL(url);
    };

    img.onerror = () => {
      reject(new Error("Erro ao carregar a imagem"));
      URL.revokeObjectURL(url);
    };

    img.src = url;
  });
}


const formEditar = document.getElementById('form-editar-noticia');

// Quando o modal for aberto, envia os dados para o formulário
document.getElementById('modalEditarNoticia').addEventListener('show.bs.modal', function(event) {
  formEditar.reset();
  const button = event.relatedTarget; // botão que abriu o modal


  const id = button.getAttribute('data-id');
  const titulo = button.getAttribute('data-titulo');
  const descricao = button.getAttribute('data-descricao');
  const autor = button.getAttribute('data-autor');
  const imagem = button.getAttribute('data-imagem');

  const modal = this;
  const formulario = modal.querySelector('form');
  formulario.reset();

  formulario.querySelector('[name="id"]').value = id;
  formulario.querySelector('[name="titulo"]').value = titulo;
  formulario.querySelector('[name="descricao"]').value = descricao;
  formulario.querySelector('[name="autor"]').value = autor;

  const imgPreview = formulario.querySelector('#editar-imagem-preview');
  if (imagem) {
    imgPreview.src = imagem;
    imgPreview.style.display = 'block';
  } else {
    imgPreview.style.display = 'none';
  }
});



// Submeter formulário que foi editado via AJAX com redimensionamento da imagem


formEditar.addEventListener('submit', async function(event) {
  event.preventDefault();

  const formData = new FormData(formEditar);
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  const inputFile = formEditar.querySelector('input[type="file"][name="imagem"]');
  const file = inputFile.files[0];

  if (file) {
    try {
      const fileRedimensionado = await redimensionarImagem(file, 400, 400); 
      formData.set('imagem', fileRedimensionado, fileRedimensionado.name);
    } catch (err) {
      alert("Erro ao redimensionar imagem: " + err.message);
      return;
    }
  }

  try {
    const res = await fetch('editar_noticias_db.php', {
      method: 'POST',
      headers: { 'X-CSRF-Token': csrfToken },
      body: formData
    });

    const data = await res.json();

    if (data.status === "success") {
      alert("Noticia atualizada com sucesso!");
      atualizarTabelaNoticias(); 
      bootstrap.Modal.getInstance(document.getElementById("modalEditarNoticia")).hide();
      formEditar.reset();
    } else {
      alert(data.message || "Erro ao atualizar projeto.");
    }
  } catch (err) {
    alert("Erro de rede: " + err.message);
  }
});



// --- Modal Eliminar ---
const modalExcluir = document.getElementById('modalExcluirNoticia');

modalExcluir.addEventListener('show.bs.modal', function(event) {
  const button = event.relatedTarget; 
  const id = button.getAttribute('data-id');
  let inputHidden = modalExcluir.querySelector('#excluir-id');

  inputHidden.value = id;
});


// --- Modal confirmar Eliminar ---

document.getElementById("btn-confirmar-excluir").addEventListener("click", async () => {
  const id = modalExcluir.querySelector('#excluir-id').value;
  if (!id) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  try {
    const res = await fetch('editar_noticias_db.php?action=eliminar_noticia', {
      method: 'POST', 
      headers: { 'X-CSRF-Token': csrfToken, 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });

    const data = await res.json();

    if (data.status === "success") {
      alert("Noticia eliminada com sucesso!");
      atualizarTabelaProjetos();
      bootstrap.Modal.getInstance(modalExcluir).hide();
    } else {
      alert(data.message || "Erro ao eliminar Noticia.");
    }
  } catch (err) {
    alert("Erro de rede: " + err.message);
  }
});


}

