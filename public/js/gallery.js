/* Visionneuse photos commune (réalisations, showrooms…).
   Déclencheur : <button data-lightbox='["/a.webp","/b.webp"]' data-index="0" data-title="…">. */
(()=>{
let dlg=document.querySelector('[data-lightbox-dialog]');
if(!document.querySelector('[data-lightbox]'))return;
if(!dlg){dlg=document.createElement('dialog');dlg.className='lightbox';dlg.setAttribute('data-lightbox-dialog','');dlg.setAttribute('aria-label','Galerie photos');
  dlg.innerHTML='<button type="button" class="lb-close" data-lb-close aria-label="Fermer">×</button><button type="button" class="lb-nav lb-prev" data-lb-prev aria-label="Photo précédente">‹</button><figure><img src="" alt="" data-lb-img><figcaption data-lb-caption></figcaption></figure><button type="button" class="lb-nav lb-next" data-lb-next aria-label="Photo suivante">›</button>';
  document.body.appendChild(dlg)}
if(!dlg.showModal)return;
const img=dlg.querySelector('[data-lb-img]'),cap=dlg.querySelector('[data-lb-caption]');
let list=[],i=0,title='';
const show=n=>{i=(n+list.length)%list.length;img.src=list[i];img.alt=title;cap.textContent=title+(list.length>1?' — '+(i+1)+' / '+list.length:'');dlg.querySelectorAll('.lb-nav').forEach(b=>b.hidden=list.length<2)};
document.addEventListener('click',e=>{const t=e.target.closest('[data-lightbox]');if(!t)return;try{list=JSON.parse(t.dataset.lightbox)}catch(_){return}
  title=t.dataset.title||'';show(parseInt(t.dataset.index||'0',10));dlg.showModal()});
dlg.querySelector('[data-lb-close]').onclick=()=>dlg.close();
dlg.querySelector('[data-lb-prev]').onclick=()=>show(i-1);
dlg.querySelector('[data-lb-next]').onclick=()=>show(i+1);
dlg.addEventListener('click',e=>{if(e.target===dlg)dlg.close()});
dlg.addEventListener('keydown',e=>{if(e.key==='ArrowLeft')show(i-1);if(e.key==='ArrowRight')show(i+1)});
let sx=0;dlg.addEventListener('touchstart',e=>sx=e.touches[0].clientX,{passive:true});
dlg.addEventListener('touchend',e=>{const dx=e.changedTouches[0].clientX-sx;if(Math.abs(dx)>40)show(i+(dx<0?1:-1))});
})();
/* Vidéo YouTube / Vimeo « à la demande » : l'iframe n'est chargée qu'au clic. */
document.addEventListener('click',e=>{const b=e.target.closest('[data-embed]');if(!b)return;
  const f=document.createElement('iframe');f.src=b.dataset.embed;f.title=b.dataset.title||'Vidéo';f.allow='autoplay; encrypted-media; picture-in-picture; fullscreen';f.allowFullscreen=true;f.loading='lazy';
  b.replaceWith(f)});
