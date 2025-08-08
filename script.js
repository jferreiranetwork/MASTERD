
document.addEventListener("DOMContentLoaded", function () {
    carregarNoticiasRTP();
    loadHome();
  
});

window.addEventListener("load", function () {
    setTimeout(() => {
        alert("Bem-vindo MASTERD!!");
    }, 5000);
});



document.addEventListener("DOMContentLoaded", () => {
    fetch("nav.html")
        .then(response => response.text())
        .then(data => {
            document.getElementById("navbar-container").innerHTML = data;
        })

});


function loadOrcamento() {
    fetch("orcamento.html")
        .then(response => response.text())
        .then(data => {
            document.getElementById("content").innerHTML = data;
              iniciarFormulario();
            
        });
}

function loadHome() {
    fetch("home.html")
        .then(response => response.text())
        .then(data => {
            document.getElementById("content").innerHTML = data;

        });
}

function loadPortfolio() {
  fetch('galeria.html')
    .then(response => response.text())
    .then(data => {
      document.getElementById("content").innerHTML = data;

    });

}



function carregarNoticiasRTP() {
  const feedURL = 'https://api.rss2json.com/v1/api.json?rss_url=https://www.rtp.pt/noticias/rss';

  fetch(feedURL)
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('noticias-rtp');
      container.innerHTML = ''; 
      data.items.slice(0, 8).forEach(item => {
        const imgMatch = item.description.match(/<img[^>]+src="([^">]+)"/);
        const imgUrl = imgMatch ? imgMatch[1] : 'https://via.placeholder.com/300x180?text=Sem+Imagem';
        const div = document.createElement('div');
        div.className = 'list-group-item';
        div.innerHTML = `
          <div class="d-flex">
            <img src="${imgUrl}" class="me-2" style="width:80px; height:60px; object-fit:cover;">
            <div>
              <a href="${item.link}" target="_blank" class="fw-bold">${item.title}</a>
              <div><small class="text-muted">${new Date(item.pubDate).toLocaleDateString()}</small></div>
            </div>
          </div>
        `;
        container.appendChild(div);
      });
    })
    .catch(err => {
      console.error('Erro ao carregar feed:', err);
      document.getElementById('noticias-rtp').innerHTML = '<p class="text-danger">Erro ao carregar notícias.</p>';
    });
}



const projetos = [
  {
    titulo: "Projeto Website",
    imagem: "img/projeto1.jpg",
    descricao: "Website moderno para empresa de tecnologia.",
    tecnologias: "HTML, CSS, JavaScript",
    data: "2024"
  },
  {
    titulo: "Aplicação Móvel",
    imagem: "img/projeto2.jpg",
    descricao: "App de gestão de tarefas com sincronização em nuvem.",
    tecnologias: "Flutter, Firebase",
    data: "2025"
  },
  {
    titulo: "Loja Online",
    imagem: "img/projeto3.jpg",
    descricao: "Loja online para venda de produtos digitais.",
    tecnologias: "React, Node.js, Stripe",
    data: "2023"
  }
];




function abrirProjeto(index) {
  const projeto = projetos[index];
  document.getElementById("modalProjetoLabel").textContent = projeto.titulo;
  document.getElementById("imagemProjeto").src = projeto.imagem;
  document.getElementById("descricaoProjeto").textContent = projeto.descricao;
  document.getElementById("tecnologiasProjeto").textContent = projeto.tecnologias;
  document.getElementById("dataProjeto").textContent = projeto.data;

  const modal = new bootstrap.Modal(document.getElementById('modalProjeto'));
  modal.show();
}


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
    const nome = document.getElementById('nome');
    const apelido = document.getElementById('apelido');
    const email = document.getElementById('email');
    const enviarBtn = document.getElementById('enviar');

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

    function validarFormulario() {
      if (nome.value.trim() === '') {
        errorMsg.textContent = 'Por favor, introduza o seu nome.';
        nome.focus();
        return false;
      }
      if (apelido.value.trim() === '') {
        errorMsg.textContent = 'Por favor, introduza o seu apelido.';
        apelido.focus();
        return false;
      }
      if (email.value.trim() === '') {
        errorMsg.textContent = 'Por favor, introduza o seu email.';
        email.focus();
        return false;
      }
      const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!regexEmail.test(email.value)) {
        errorMsg.textContent = 'Por favor, introduza um email válido.';
        email.focus();
        return false;
      }
      const meses = parseInt(prazoMeses.value);
      if (isNaN(meses) || meses < 1) {
        errorMsg.textContent = 'Por favor, introduza um prazo válido (mínimo 1 mês).';
        prazoMeses.focus();
        return false;
      }
      errorMsg.textContent = '';
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
        alert(`Pedido enviado!\nValor estimado: ${valorInput.value} €`);

      }
    });


    atualizarOrcamento();
  }





