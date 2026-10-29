/* Volet promo — glisse depuis la gauche, se ferme (✕, Échap), mémorisé selon la fréquence choisie dans l'admin. */
(function(){
  var el=document.getElementById('promo-slide');if(!el)return;
  var bd=document.getElementById('promo-backdrop');var key='be_promo_'+el.dataset.key,freq=el.dataset.frequency||'day',delay=Math.max(0,parseInt(el.dataset.delay||'3',10))*1000;
  var ttl={day:864e5,week:6048e5}[freq];
  function seen(){try{
    if(freq==='always')return false;
    if(freq==='session')return sessionStorage.getItem(key)==='1';
    var t=parseInt(localStorage.getItem(key)||'0',10);return t>0&&Date.now()-t<ttl;
  }catch(e){return false}}
  function remember(){try{
    if(freq==='session')sessionStorage.setItem(key,'1');
    else if(freq!=='always')localStorage.setItem(key,String(Date.now()));
  }catch(e){}}
  if(seen())return;
  function open(){el.hidden=false;if(bd)bd.hidden=false;requestAnimationFrame(function(){requestAnimationFrame(function(){el.classList.add('on');if(bd)bd.classList.add('on')})});remember();var c=el.querySelector('.promo-close');if(bd&&c)c.focus({preventScroll:true})}
  function close(){el.classList.remove('on');if(bd)bd.classList.remove('on');document.removeEventListener('keydown',esc);setTimeout(function(){el.hidden=true;if(bd)bd.hidden=true},600)}
  function esc(e){if(e.key==='Escape')close()}
  el.querySelector('.promo-close').addEventListener('click',close);if(bd)bd.addEventListener('click',close);
  el.querySelectorAll('a').forEach(function(a){a.addEventListener('click',remember)});
  document.addEventListener('keydown',esc);
  function start(){setTimeout(open,delay)}
  if(document.readyState==='complete')start();else window.addEventListener('load',start);
})();
