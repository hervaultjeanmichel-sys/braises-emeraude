/* Braises Émeraude — interactions du site : menu mobile, galerie, liste « Mon projet », devis en étapes. JS vanilla, sans dépendance. */
(() => {
'use strict';
const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];
const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

/* ---------- Menu mobile ---------- */
const burger = $('[data-burger]'), mnav = $('#mnav');
if (burger && mnav) burger.addEventListener('click', () => {
  const open = mnav.classList.toggle('open');
  burger.setAttribute('aria-expanded', open ? 'true' : 'false');
});

/* ---------- Galerie fiche produit ---------- */
const main = $('[data-gallery-main]');
$$('[data-gallery] .thumbs button').forEach(b => b.addEventListener('click', () => {
  if (main) main.src = b.dataset.src;
  $$('[data-gallery] .thumbs button').forEach(x => x.classList.toggle('on', x === b));
}));

/* ---------- Liste « Mon projet » (localStorage, sans compte) ---------- */
const KEY = 'be_project';
const load = () => { try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { return []; } };
const save = list => { try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) {} refresh(); };
const has = id => load().some(p => String(p.id) === String(id));

function refresh() {
  const list = load();
  $$('[data-project-count]').forEach(el => { el.textContent = list.length; el.hidden = list.length === 0; });
  $$('[data-project-add]').forEach(btn => {
    const on = has(btn.dataset.projectAdd);
    btn.classList.toggle(btn.classList.contains('add') ? 'on' : 'added', on);
    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
    if (btn.dataset.labelOn) {
      btn.dataset.labelOff = btn.dataset.labelOff || btn.textContent;
      btn.textContent = on ? btn.dataset.labelOn : btn.dataset.labelOff;
    }
  });
}

document.addEventListener('click', e => {
  const btn = e.target.closest('[data-project-add]');
  if (!btn) return;
  e.preventDefault();
  const id = btn.dataset.projectAdd;
  const list = load();
  save(has(id) ? list.filter(p => String(p.id) !== String(id))
               : [...list, { id, name: btn.dataset.name, brand: btn.dataset.brand || '', url: btn.dataset.url || '', img: btn.dataset.img || '', note: '' }]);
});

const pl = $('[data-project-list]');
function renderProject() {
  if (!pl) return;
  const list = load();
  $('[data-project-empty]').hidden = list.length > 0;
  const cta = $('[data-project-cta]');
  if (cta) cta.hidden = list.length === 0;
  pl.innerHTML = list.map(p => `
    <div class="project-item" data-id="${esc(p.id)}">
      ${p.img ? `<img src="${esc(p.img)}" alt="" width="72" height="72" loading="lazy">` : '<span></span>'}
      <div><a href="${esc(p.url)}"><strong>${esc(p.name)}</strong></a>${p.brand ? ` <span style="color:var(--muted)">· ${esc(p.brand)}</span>` : ''}
        <input type="text" maxlength="300" value="${esc(p.note)}" placeholder="${esc(pl.dataset.noteLabel)}" aria-label="${esc(pl.dataset.noteLabel)}" data-note></div>
      <button class="rm" type="button" aria-label="${esc(pl.dataset.removeLabel)} ${esc(p.name)}" data-rm>✕</button>
    </div>`).join('');
}
if (pl) {
  pl.addEventListener('click', e => {
    const rm = e.target.closest('[data-rm]');
    if (!rm) return;
    const id = rm.closest('.project-item').dataset.id;
    save(load().filter(p => String(p.id) !== id));
    renderProject();
  });
  pl.addEventListener('input', e => {
    if (!e.target.matches('[data-note]')) return;
    const id = e.target.closest('.project-item').dataset.id;
    const list = load().map(p => String(p.id) === id ? { ...p, note: e.target.value } : p);
    try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (err) {}
  });
  renderProject();
}

/* ---------- Devis en étapes ---------- */
const form = $('[data-wizard]');
if (form) {
  const panels = $$('.step-panel', form);
  const prev = $('[data-prev]', form), next = $('[data-next]', form), submit = $('[data-submit]', form);
  const bar = $('[data-progress-bar]', form), label = $('[data-progress-label]', form), count = $('[data-progress-count]', form);
  let idx = 0;

  // Une étape conditionnelle (ex. « Que remplacez-vous ? ») n'apparaît que si la condition est remplie
  const active = () => panels.filter(p => {
    const cond = p.dataset.if;
    if (!cond) return true;
    const [name, value] = cond.split('=');
    const checked = form.querySelector(`[name="${name}"]:checked`);
    return checked && checked.value === value;
  });

  // Erreur côté serveur : on rouvre directement l'étape des coordonnées
  if ($('.errors', form)) idx = Math.max(0, active().length - 1);

  function show() {
    const list = active();
    idx = Math.min(idx, list.length - 1);
    panels.forEach(p => p.classList.toggle('on', p === list[idx]));
    const last = idx === list.length - 1;
    prev.style.visibility = idx === 0 ? 'hidden' : 'visible';
    next.hidden = last;
    submit.hidden = !last;
    if (bar) bar.style.width = ((idx + 1) / list.length * 100) + '%';
    if (count) count.textContent = (idx + 1) + ' / ' + list.length;
    if (label) label.textContent = $('legend', list[idx]).textContent;
  }

  function valid() {
    const fields = $$('input,select,textarea', active()[idx]).filter(f => f.willValidate);
    for (const f of fields) if (!f.checkValidity()) { f.reportValidity(); return false; }
    return true;
  }

  next.addEventListener('click', () => {
    if (!valid()) return;
    idx++; show();
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  prev.addEventListener('click', () => { idx = Math.max(0, idx - 1); show(); });

  // Un choix unique fait avancer tout seul (étapes à boutons radio)
  form.addEventListener('change', e => {
    if (e.target.type !== 'radio') return;
    const panel = e.target.closest('.step-panel');
    if (panel && $$('input', panel).every(i => i.type === 'radio' && i.name === e.target.name)) {
      setTimeout(() => { idx++; show(); }, 220);
    }
  });

  // Code postal → magasin destinataire proposé (modifiable)
  const cp = $('[data-cp]', form), sel = $('[data-store-select]', form);
  let map = {};
  try { map = JSON.parse(form.dataset.postcodes || '{}'); } catch (e) {}
  if (cp && sel) cp.addEventListener('input', () => {
    if (cp.value.length !== 5 || sel.dataset.touched) return;
    const hit = Object.entries(map).find(([, codes]) => (codes || []).includes(cp.value));
    if (hit) sel.value = hit[0];
  });
  if (sel) sel.addEventListener('change', () => { sel.dataset.touched = '1'; });

  // Sélection « Mon projet » jointe à la demande
  const input = $('[data-products-input]', form);
  const box = $('[data-selection]', form), boxList = $('[data-selection-list]', form);
  let picked = [];
  try { picked = JSON.parse(input.value || '[]') || []; } catch (e) {}
  load().forEach(p => { if (!picked.some(x => String(x.id) === String(p.id))) picked.push({ id: p.id, name: p.name, brand: p.brand, note: p.note }); });
  if (picked.length) {
    input.value = JSON.stringify(picked);
    box.hidden = false;
    boxList.textContent = picked.map(p => p.name + (p.brand ? ' (' + p.brand + ')' : '')).join(', ');
  }

  form.addEventListener('submit', e => {
    if (!form.checkValidity()) { e.preventDefault(); const bad = $$(':invalid', form)[0]; if (bad) { const panel = bad.closest('.step-panel'); idx = Math.max(0, active().indexOf(panel)); show(); bad.reportValidity(); } return; }
    submit.disabled = true;
  });

  show();
}

refresh();
})();
