<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Notas — App (Tailwind + Vanilla JS)</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* pequenas customizações */
    .truncate-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
  <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <header class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-4">
        <button id="toggleSidebar" class="p-2 rounded-md hover:bg-gray-200 dark:hover:bg-zinc-800" aria-label="Toggle sidebar">
          <!-- icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
          </svg>
        </button>

        <h1 class="text-2xl font-semibold">Notas</h1>
        <span class="text-sm text-zinc-500 dark:text-zinc-400">Aplicativo simples de anotações</span>
      </div>

      <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-full px-3 py-1 shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
          </svg>
          <input id="globalSearch" type="text" placeholder="Buscar notas..." class="bg-transparent outline-none placeholder:text-zinc-400" />
        </div>

        <button id="btnNew" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Nova nota
        </button>
      </div>
    </header>

    <div class="grid grid-cols-12 gap-6">
      <!-- Sidebar -->
      <aside id="sidebar" class="col-span-4 md:col-span-3 lg:col-span-3 bg-white dark:bg-zinc-800 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-medium">Minhas notas</h2>
          <span id="notesCount" class="text-sm text-zinc-500 dark:text-zinc-400">0</span>
        </div>

        <div class="flex flex-col gap-2 mb-4">
          <input id="filterTitle" type="text" placeholder="Filtrar por título..." class="px-3 py-2 rounded-md bg-gray-50 dark:bg-zinc-900 border border-gray-100 dark:border-zinc-700 outline-none" />
        </div>

        <div id="tagsContainer" class="flex items-center gap-2 mb-4 flex-wrap"></div>

        <nav id="notesList" class="space-y-2 overflow-auto max-h-[60vh] pr-1"></nav>
      </aside>

      <!-- Editor -->
      <main class="col-span-12 md:col-span-9 lg:col-span-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-gray-200 dark:border-zinc-700 shadow-sm min-h-[60vh] flex flex-col">
          <div id="emptyEditor" class="flex-1 flex items-center justify-center text-zinc-400">Selecione ou crie uma nota</div>

          <div id="editorPane" class="hidden flex-1 flex-col">
            <div class="flex items-start justify-between gap-4 mb-4">
              <input id="noteTitle" class="text-2xl font-semibold bg-transparent outline-none flex-1" placeholder="Título da nota" />

              <div class="flex items-center gap-2">
                <button id="btnTags" class="px-3 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 text-sm">Tags</button>
                <button id="btnDelete" class="px-3 py-1 rounded-md border border-red-200 text-red-600 text-sm">Excluir</button>
              </div>
            </div>

            <textarea id="noteBody" placeholder="Escreva suas anotações aqui..." class="flex-1 resize-none min-h-[40vh] bg-transparent outline-none text-base"></textarea>

            <div class="mt-4 flex items-center justify-between text-sm text-zinc-500">
              <div id="lastEdited">Última edição: —</div>
              <div>Notas salvas localmente</div>
            </div>
          </div>
        </div>
      </main>

      <!-- Preview -->
      <aside class="col-span-12 md:col-span-12 lg:col-span-3">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-gray-200 dark:border-zinc-700 space-y-4">
          <div>
            <h3 class="font-medium">Preview</h3>
            <p class="text-sm text-zinc-500">Visualize rapidamente sua nota selecionada.</p>
          </div>

          <div id="previewBox" class="p-3 rounded-md bg-gray-50 dark:bg-zinc-900 min-h-[120px]"></div>

          <div>
            <h4 class="font-medium">Atalhos</h4>
            <ul class="text-sm text-zinc-500 list-disc pl-5">
              <li>Nova nota: botão "Nova nota"</li>
              <li>Criar tags: botão "Tags" na nota</li>
              <li>Excluir: botão "Excluir"</li>
            </ul>
          </div>

          <div class="text-xs text-zinc-400">Dica: este layout usa Tailwind — ajuste classes conforme seu design.</div>
        </div>
      </aside>
    </div>
  </div>

  <script>
    // Simple Notes app using localStorage
    const STORAGE_KEY = 'notes_app_v1';

    let notes = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    let activeId = null;
    const elems = {
      notesList: document.getElementById('notesList'),
      notesCount: document.getElementById('notesCount'),
      btnNew: document.getElementById('btnNew'),
      noteTitle: document.getElementById('noteTitle'),
      noteBody: document.getElementById('noteBody'),
      btnDelete: document.getElementById('btnDelete'),
      btnTags: document.getElementById('btnTags'),
      editorPane: document.getElementById('editorPane'),
      emptyEditor: document.getElementById('emptyEditor'),
      previewBox: document.getElementById('previewBox'),
      lastEdited: document.getElementById('lastEdited'),
      filterTitle: document.getElementById('filterTitle'),
      globalSearch: document.getElementById('globalSearch'),
      tagsContainer: document.getElementById('tagsContainer'),
      toggleSidebar: document.getElementById('toggleSidebar'),
      sidebar: document.getElementById('sidebar')
    };

    function save() {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(notes));
      renderList();
    }

    function createNote() {
      const id = Date.now();
      const newNote = { id, title: 'Nova anotação', body: '', tags: [], updatedAt: new Date().toISOString() };
      notes.unshift(newNote);
      activeId = id;
      save();
      openNote(id);
    }

    function updateNoteField(field, value) {
      const note = notes.find(n => n.id === activeId);
      if (!note) return;
      note[field] = value;
      note.updatedAt = new Date().toISOString();
      save();
      renderPreview(note);
    }

    function deleteNote(id) {
      if (!confirm('Excluir esta nota?')) return;
      notes = notes.filter(n => n.id !== id);
      if (activeId === id) activeId = notes[0]?.id ?? null;
      save();
      openNote(activeId);
    }

    function openNote(id) {
      const note = notes.find(n => n.id === id);
      activeId = id ?? null;
      if (!note) {
        elems.editorPane.classList.add('hidden');
        elems.emptyEditor.classList.remove('hidden');
        elems.previewBox.innerHTML = '';
        elems.notesCount.textContent = notes.length;
        return;
      }
      elems.editorPane.classList.remove('hidden');
      elems.emptyEditor.classList.add('hidden');
      elems.noteTitle.value = note.title;
      elems.noteBody.value = note.body;
      elems.lastEdited.textContent = 'Última edição: ' + new Date(note.updatedAt).toLocaleString();
      renderPreview(note);
      renderList();
    }

    function renderPreview(note) {
      if (!note) { elems.previewBox.innerHTML = ''; return; }
      elems.previewBox.innerHTML = `
        <h4 class="font-semibold">${escapeHtml(note.title || 'Sem título')}</h4>
        <p class="text-sm text-zinc-400 whitespace-pre-line">${escapeHtml(note.body || '—')}</p>
        <div class="mt-2 flex gap-2 flex-wrap">${note.tags.map(t => `<span class="text-xs px-2 py-1 bg-indigo-100 dark:bg-indigo-900 rounded-full">#${escapeHtml(t)}</span>`).join('')}</div>
      `;
    }

    function renderList() {
      const q = (elems.filterTitle.value || elems.globalSearch.value || '').toLowerCase();
      const tagFilter = elems.tagsContainer.querySelector('.active')?.dataset?.tag || null;
      const filtered = notes.filter(n => ((n.title + ' ' + n.body).toLowerCase().includes(q)) && (tagFilter ? n.tags.includes(tagFilter) : true));

      elems.notesList.innerHTML = filtered.map(n => `
        <button data-id="${n.id}" class="w-full text-left p-3 rounded-lg flex flex-col gap-1 hover:bg-gray-50 dark:hover:bg-zinc-900 ${n.id === activeId ? 'ring-2 ring-indigo-300 dark:ring-indigo-600 bg-gray-50 dark:bg-zinc-900' : ''}">
          <div class="flex items-center justify-between">
            <strong class="truncate">${escapeHtml(n.title || 'Sem título')}</strong>
            <small class="text-xs text-zinc-400">${escapeHtml(n.tags.join(', '))}</small>
          </div>
          <p class="text-sm text-zinc-500 dark:text-zinc-400 truncate-2">${escapeHtml(n.body || '—')}</p>
        </button>
      `).join('') || '<div class="text-sm text-zinc-500">Nenhuma nota encontrada.</div>';

      elems.notesCount.textContent = notes.length;

      // attach events
      Array.from(elems.notesList.querySelectorAll('button[data-id]')).forEach(btn => {
        btn.addEventListener('click', () => openNote(Number(btn.dataset.id)));
      });

      renderTags();
    }

    function renderTags() {
      const allTags = Array.from(new Set(notes.flatMap(n => n.tags)));
      elems.tagsContainer.innerHTML = '';
      const allBtn = document.createElement('button');
      allBtn.className = 'px-2 py-1 rounded-md text-sm ' + (!elems.tagsContainer.querySelector('.active') ? 'bg-indigo-600 text-white' : 'bg-transparent border border-zinc-200 dark:border-zinc-700');
      allBtn.textContent = 'Todas';
      allBtn.addEventListener('click', () => { clearTagFilter(); renderList(); });
      elems.tagsContainer.appendChild(allBtn);

      if (allTags.length === 0) {
        const span = document.createElement('span');
        span.className = 'text-sm text-zinc-400';
        span.textContent = '(Sem tags)';
        elems.tagsContainer.appendChild(span);
        return;
      }

      allTags.forEach(tag => {
        const b = document.createElement('button');
        b.className = 'px-2 py-1 rounded-md text-sm bg-transparent border border-zinc-200 dark:border-zinc-700';
        b.textContent = '#' + tag;
        b.dataset.tag = tag;
        b.addEventListener('click', () => {
          // toggle
          const currently = elems.tagsContainer.querySelector('.active');
          if (currently) currently.classList.remove('active');
          b.classList.add('active');
          renderList();
        });
        elems.tagsContainer.appendChild(b);
      });
    }

    function clearTagFilter() {
      const a = elems.tagsContainer.querySelector('.active');
      if (a) a.classList.remove('active');
    }

    function escapeHtml(str) {
      return String(str).replace(/[&<>"']/g, function (s) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[s];
      });
    }

    // Events
    elems.btnNew.addEventListener('click', createNote);
    elems.noteTitle.addEventListener('input', e => updateNoteField('title', e.target.value));
    elems.noteBody.addEventListener('input', e => updateNoteField('body', e.target.value));
    elems.btnDelete.addEventListener('click', () => deleteNote(activeId));

    elems.filterTitle.addEventListener('input', renderList);
    elems.globalSearch.addEventListener('input', renderList);

    elems.btnTags.addEventListener('click', () => {
      if (!activeId) return;
      const note = notes.find(n => n.id === activeId);
      const result = prompt('Separar tags por vírgula', note.tags.join(', '));
      if (result !== null) {
        note.tags = result.split(',').map(t => t.trim()).filter(Boolean);
        note.updatedAt = new Date().toISOString();
        save();
        renderList();
        renderPreview(note);
      }
    });

    elems.toggleSidebar.addEventListener('click', () => {
      elems.sidebar.classList.toggle('hidden');
    });

    // initial data if empty
    if (!notes.length) {
      // seed example notes
      notes = [
        { id: Date.now()+2, title: 'Comprar mercado', body: 'Leite, ovos, arroz, feijão, café', tags: ['pessoal'], updatedAt: new Date().toISOString() },
        { id: Date.now()+1, title: 'Projeto Laravel', body: 'Configurar rota /sendmail e testar formulário', tags: ['trabalho'], updatedAt: new Date().toISOString() },
        { id: Date.now(), title: 'Ideias blog', body: 'Escrever sobre créditos de carbono e Hyperledger', tags: ['ideias'], updatedAt: new Date().toISOString() }
      ];
      save();
    }

    // open first note by default
    activeId = notes[0]?.id ?? null;
    openNote(activeId);

  </script>
</body>
</html>
