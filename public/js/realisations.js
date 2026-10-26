/* Réalisations : filtres + visionneuse photos (dialog natif). */
(()=>{
/* Filtres */
const bar=document.querySelector('[data-real-filters]');
if(bar){const cards=[...document.querySelectorAll('.real')];
  bar.addEventListener('click',e=>{const b=e.target.closest('button');if(!b)return;
    bar.querySelectorAll('button').forEach(x=>{x.classList.toggle('on',x===b);x.setAttribute('aria-pressed',x===b)});
    const f=b.dataset.filter;cards.forEach(c=>{c.hidden=!(f==='*'||c.dataset.tags.split(' ').includes(f))});
  });
}
/* Visionneuse */
const dlg=document.querySelector('[data-lightbox-dialog]');if(!dlg||!dlg.showModal)return;
const img=dlg.querySelector('[data-lb-img]'),cap=dlg.querySelector('[data-lb-caption]');
let list=[],i=0,title='';
const show=n=>{i=(n+list.length)%list.length;img.src=list[i];img.alt=title;cap.textContent=title+(list.length>1?' — '+(i+1)+' / '+list.length:'');
  dlg.querySelectorAll('.lb-nav').forEach(b=>b.hidden=list.length<2)};
document.addEventListener('click',e=>{const t=e.target.closest('[data-lightbox]');if(!t)return;
  try{list=JSON.parse(t.dataset.lightbox)}catch(_){return}title=t.dataset.title||'';show(0);dlg.showModal()});
dlg.querySelector('[data-lb-close]').onclick=()=>dlg.close();
dlg.querySelector('[data-lb-prev]').onclick=()=>show(i-1);
dlg.querySelector('[data-lb-next]').onclick=()=>show(i+1);
dlg.addEventListener('click',e=>{if(e.target===dlg)dlg.close()});
dlg.addEventListener('keydown',e=>{if(e.key==='ArrowLeft')show(i-1);if(e.key==='ArrowRight')show(i+1)});
let sx=0;dlg.addEventListener('touchstart',e=>sx=e.touches[0].clientX,{passive:true});
dlg.addEventListener('touchend',e=>{const dx=e.changedTouches[0].clientX-sx;if(Math.abs(dx)>40)show(i+(dx<0?1:-1))});
})();
