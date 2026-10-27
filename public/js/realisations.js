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
})();
