/* Prise de rendez-vous : magasin → type → jour → créneau → coordonnées. Créneaux lus en JSON sur /rendez-vous/creneaux. */
(()=>{
const form=document.querySelector('[data-booking]');if(!form)return;
const types=JSON.parse(form.dataset.types||'{}'),old=JSON.parse(form.dataset.old||'{}'),url=form.dataset.slotsUrl;
const $=s=>form.querySelector(s),$$=s=>[...form.querySelectorAll(s)];
const step=n=>$('[data-step="'+n+'"]');
const dateIn=$('input[name="date"]'),timeIn=$('input[name="time"]');
const fmtDay=d=>{const x=new Date(d+'T12:00:00');return{dow:x.toLocaleDateString('fr-FR',{weekday:'short'}).replace('.',''),day:x.getDate(),month:x.toLocaleDateString('fr-FR',{month:'short'}).replace('.','')}};
const longDay=d=>new Date(d+'T12:00:00').toLocaleDateString('fr-FR',{weekday:'long',day:'numeric',month:'long'});
const esc=s=>String(s).replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const dur=m=>m>=60?(Math.floor(m/60)+' h'+(m%60?' '+(m%60):'')):m+' min';
let store=null,type=null,first=true;

const show=(name,on)=>{const el=step(name);if(el)el.hidden=!on};
const summary=()=>{const t=(types[store]||[]).find(x=>x.key===type);if(!t||!dateIn.value||!timeIn.value)return;
  const s=form.querySelector('input[name="store_id"]:checked').closest('label').querySelector('strong').textContent;
  $('[data-summary]').innerHTML='<strong>'+esc(t.label)+'</strong> · '+esc(longDay(dateIn.value))+' à '+esc(timeIn.value.replace(':','h'))+' · '+esc(t.at_home?'chez vous':'magasin de '+s);
  $$('[data-home-only]').forEach(e=>e.hidden=!t.at_home);
  ['address','city','postal_code'].forEach(n=>{const i=$('[name="'+n+'"]');if(i)i.required=!!t.at_home});
};

const pickStore=id=>{store=id;type=null;dateIn.value='';timeIn.value='';
  const list=$('[data-types-list]');
  list.innerHTML=(types[id]||[]).map(t=>'<label class="bk-choice"><input type="radio" name="type" value="'+esc(t.key)+'" required><span><strong>'+esc(t.label)+'</strong><small>'+(t.at_home?'À domicile':'Au showroom')+' · '+dur(t.duration)+'</small></span></label>').join('');
  show('type',true);show('when',false);show('contact',false);
  if(first&&old.type&&list.querySelector('[value="'+CSS.escape(old.type)+'"]')){const r=list.querySelector('[value="'+CSS.escape(old.type)+'"]');r.checked=true;pickType(old.type)}
};

const pickType=async key=>{type=key;timeIn.value='';show('when',true);show('contact',false);
  const days=$('[data-days]'),slots=$('[data-slots]'),empty=$('[data-when-empty]');
  days.innerHTML='<p class="hint">Recherche des disponibilités…</p>';slots.innerHTML='';empty.hidden=true;
  try{const r=await fetch(url+'?magasin='+store+'&type='+encodeURIComponent(key),{headers:{Accept:'application/json'}});const j=await r.json();
    if(!j.days||!j.days.length){days.innerHTML='';empty.hidden=false;return}
    days.innerHTML=j.days.map(d=>{const f=fmtDay(d);return'<button type="button" role="option" data-day="'+d+'" aria-selected="false" aria-label="'+esc(longDay(d))+'"><small>'+esc(f.dow)+'</small><b>'+f.day+'</b><small>'+esc(f.month)+'</small></button>'}).join('');
    const want=first&&old.date&&j.days.includes(old.date)?old.date:j.days[0];pickDay(want);
  }catch(e){days.innerHTML='';empty.hidden=false}
};

const pickDay=async d=>{dateIn.value=d;timeIn.value='';show('contact',false);
  $$('[data-day]').forEach(b=>{const on=b.dataset.day===d;b.classList.toggle('on',on);b.setAttribute('aria-selected',on)});
  const sel=$('[data-day="'+d+'"]');if(sel)sel.scrollIntoView({block:'nearest',inline:'center',behavior:'smooth'});
  const slots=$('[data-slots]');slots.innerHTML='<p class="hint">…</p>';
  const r=await fetch(url+'?magasin='+store+'&type='+encodeURIComponent(type)+'&date='+d,{headers:{Accept:'application/json'}});const j=await r.json();
  const am=(j.slots||[]).filter(s=>s<'13:00'),pm=(j.slots||[]).filter(s=>s>='13:00');
  const group=(label,list)=>list.length?'<div class="bk-slot-group"><span>'+label+'</span><div>'+list.map(s=>'<button type="button" data-time="'+s+'">'+s.replace(':','h')+'</button>').join('')+'</div></div>':'';
  slots.innerHTML=(j.slots||[]).length?group('Matin',am)+group('Après-midi',pm):'<p class="hint">Plus de créneau ce jour-là.</p>';
  if(first&&old.time&&old.date===d&&(j.slots||[]).includes(old.time))pickTime(old.time);
  first=false;
};

const pickTime=t=>{timeIn.value=t;$$('[data-time]').forEach(b=>b.classList.toggle('on',b.dataset.time===t));show('contact',true);summary();
  if(!first)step('contact').scrollIntoView({behavior:'smooth',block:'start'})};

form.addEventListener('change',e=>{
  if(e.target.name==='store_id')pickStore(e.target.value);
  if(e.target.name==='type')pickType(e.target.value);
});
form.addEventListener('click',e=>{
  const d=e.target.closest('[data-day]');if(d){pickDay(d.dataset.day);return}
  const t=e.target.closest('[data-time]');if(t)pickTime(t.dataset.time);
});
form.addEventListener('submit',e=>{if(!dateIn.value||!timeIn.value){e.preventDefault();step('when').scrollIntoView({behavior:'smooth'})}});

const checked=form.querySelector('input[name="store_id"]:checked');
if(checked)pickStore(checked.value);else first=false;
})();
