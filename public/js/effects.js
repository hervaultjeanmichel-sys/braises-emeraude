/* Effets saisonniers — un canevas transparent au-dessus de la page (ne bloque aucun clic).
   Config : window.BE_EFFECT = {id, type, intensity}. Rien si « réduire les animations » est demandé.
   Le visiteur peut couper l'effet (bouton) : mémorisé par effet dans son navigateur. */
(()=>{
const cfg=window.BE_EFFECT;if(!cfg||!cfg.type)return;
if(matchMedia('(prefers-reduced-motion: reduce)').matches)return;
const KEY='be_fx_off_'+cfg.id;
let off=false;try{off=localStorage.getItem(KEY)==='1'}catch(e){}

/* Bouton on/off */
const btn=document.createElement('button');btn.type='button';btn.className='fx-toggle';
const label=()=>{btn.textContent=off?(cfg.labelOn||'✨ Animation'):(cfg.labelOff||'✕ Animation');btn.setAttribute('aria-pressed',String(!off))};
label();document.body.appendChild(btn);

const cv=document.createElement('canvas');cv.className='fx-canvas';cv.setAttribute('aria-hidden','true');document.body.appendChild(cv);
const ctx=cv.getContext('2d');
let W=0,H=0;const size=()=>{W=cv.width=innerWidth;H=cv.height=innerHeight};size();addEventListener('resize',size,{passive:true});
const small=innerWidth<760;
const mult={low:.5,medium:1,high:1.8}[cfg.intensity]||1;
const K=2; /* 30 images/s : chaque image avance de 2 pas */
const N=n=>Math.max(4,Math.round(n*mult*(small?.55:1)));
const R=(a,b)=>a+Math.random()*(b-a);

/* Sprites : un dessin, réutilisé à chaque image */
const sprite=(sz,draw)=>{const c=document.createElement('canvas');c.width=c.height=sz;draw(c.getContext('2d'),sz);return c};
const emoji=(ch,sz=48)=>sprite(sz,(c,s)=>{c.font=Math.round(s*.82)+'px "Apple Color Emoji","Segoe UI Emoji","Noto Color Emoji",sans-serif';c.textAlign='center';c.textBaseline='middle';c.fillText(ch,s/2,s/2+s*.04)});
const glow=(rgb)=>sprite(32,(c,s)=>{const g=c.createRadialGradient(s/2,s/2,0,s/2,s/2,s/2);g.addColorStop(0,`rgba(${rgb},1)`);g.addColorStop(.25,`rgba(${rgb},.85)`);g.addColorStop(1,`rgba(${rgb},0)`);c.fillStyle=g;c.fillRect(0,0,s,s)});
const flake=sprite(32,(c,s)=>{const g=c.createRadialGradient(s/2,s/2,0,s/2,s/2,s/2);g.addColorStop(0,'rgba(255,255,255,1)');g.addColorStop(.45,'rgba(255,255,255,.75)');g.addColorStop(1,'rgba(255,255,255,0)');c.fillStyle=g;c.fillRect(0,0,s,s)});

/* Particules « qui tombent / qui montent » génériques */
const fallers=(imgs,n,{vy=[.4,1.2],sz=[14,30],sway=.6,spin=.02,up=false,alpha=[.7,1]}={})=>{
  const mk=(init)=>({x:R(0,W),y:init?R(0,H):(up?H+30:-30),s:R(sz[0],sz[1]),vy:R(vy[0],vy[1])*(up?-1:1),ph:R(0,6.28),rot:R(0,6.28),vr:R(-spin,spin),a:R(alpha[0],alpha[1]),img:imgs[Math.floor(Math.random()*imgs.length)]});
  const ps=Array.from({length:N(n)},()=>mk(true));
  return()=>{for(const p of ps){p.y+=p.vy*K;p.ph+=.02*K;p.x+=Math.sin(p.ph)*sway*K;p.rot+=p.vr*K;
      if(up?p.y<-40:p.y>H+40)Object.assign(p,mk(false));
      ctx.save();ctx.globalAlpha=p.a;ctx.translate(p.x,p.y);ctx.rotate(p.rot);ctx.drawImage(p.img,-p.s/2,-p.s/2,p.s,p.s);ctx.restore()}};
};

/* Feux d'artifice */
const fireworks=()=>{const cols=['255,96,64','255,200,60','138,90,22','140,120,255','255,110,180','255,255,255'];
  const sp=cols.map(glow);let rockets=[],sparks=[],t=0;
  return()=>{t+=K;
    if(t%Math.round(70/mult)<K||(t%Math.round(28/mult)<K&&Math.random()<.4))rockets.push({x:R(W*.15,W*.85),y:H+10,vy:R(-9.5,-7)*(H/900+.4),ty:R(H*.12,H*.45),c:Math.floor(Math.random()*cols.length)});
    rockets=rockets.filter(r=>{r.y+=r.vy*K;r.vy*=Math.pow(.985,K);ctx.drawImage(sp[5],r.x-3,r.y-3,6,6);
      if(r.y<=r.ty||r.vy>-1.5){const n=small?40:70;for(let i=0;i<n;i++){const a=i/n*6.283,v=R(1.6,4.2);sparks.push({x:r.x,y:r.y,vx:Math.cos(a)*v,vy:Math.sin(a)*v,l:1,d:R(.010,.018),c:Math.random()<.2?5:r.c})}return false}return true});
    sparks=sparks.filter(p=>{p.x+=p.vx*K;p.y+=p.vy*K;p.vx*=Math.pow(.985,K);p.vy=p.vy*Math.pow(.985,K)+.045*K;p.l-=p.d*K;if(p.l<=0)return false;
      ctx.globalAlpha=p.l;const s=6*p.l+2;ctx.drawImage(sp[p.c],p.x-s,p.y-s,s*2,s*2);return true});ctx.globalAlpha=1};
};

/* Confettis */
const confetti=()=>{const cols=['#8a5a16','#ff7b30','#f6bf2a','#e2381f','#ffffff','#5cc9e2'];
  const mk=i=>({x:R(0,W),y:i?R(-H,H):-20,w:R(6,11),h:R(3,6),vy:R(1.2,2.6),vx:R(-.6,.6),r:R(0,6),vr:R(-.12,.12),c:cols[Math.floor(Math.random()*cols.length)]});
  const ps=Array.from({length:N(90)},(_,i)=>mk(true));
  return()=>{for(const p of ps){p.y+=p.vy*K;p.x+=(p.vx+Math.sin(p.y*.02)*.4)*K;p.r+=p.vr*K;if(p.y>H+20)Object.assign(p,mk(false));
      ctx.save();ctx.translate(p.x,p.y);ctx.rotate(p.r);ctx.scale(1,Math.cos(p.r*2));ctx.fillStyle=p.c;ctx.fillRect(-p.w/2,-p.h/2,p.w,p.h);ctx.restore()}};
};

const make={
  snow:()=>fallers([flake,flake,flake,emoji('❄️',40)],110,{vy:[.4,1.4],sz:[4,13],sway:.5,spin:.01,alpha:[.55,.95]}),
  halloween:()=>{const a=fallers([emoji('🦇'),emoji('🦇'),emoji('🎃'),emoji('👻')],16,{vy:[.4,1],sz:[22,40],sway:1.4,spin:.01});return a},
  easter:()=>fallers([emoji('🥚'),emoji('🐣'),emoji('🌷'),emoji('🐰'),emoji('🌼')],22,{vy:[.5,1.2],sz:[20,34],sway:.9,spin:.02}),
  hearts:()=>fallers([emoji('❤️'),emoji('💕'),emoji('💗')],22,{vy:[.5,1.1],sz:[16,30],sway:.8,spin:.015,up:true,alpha:[.6,.9]}),
  leaves:()=>fallers([emoji('🍂'),emoji('🍁'),emoji('🍃')],26,{vy:[.6,1.4],sz:[18,32],sway:1.6,spin:.04}),
  embers:()=>fallers([glow('255,123,48'),glow('255,170,60'),glow('138,90,22')],60,{vy:[.4,1.1],sz:[4,10],sway:.5,spin:0,up:true,alpha:[.5,.95]}),
  fireworks,confetti,
};
if(!make[cfg.type]){btn.remove();cv.remove();return}

let draw=null,raf=0,last=0;
const loop=now=>{raf=requestAnimationFrame(loop);if(document.hidden)return;if(now-last<30)return;last=now;ctx.clearRect(0,0,W,H);draw()};
const start=()=>{if(off)return;draw=draw||make[cfg.type]();cv.hidden=false;if(!raf)raf=requestAnimationFrame(loop)};
const stop=()=>{cancelAnimationFrame(raf);raf=0;ctx.clearRect(0,0,W,H);cv.hidden=true};
btn.addEventListener('click',()=>{off=!off;try{localStorage.setItem(KEY,off?'1':'0')}catch(e){}label();off?stop():start()});
if(off){cv.hidden=true}else{document.readyState==='complete'?setTimeout(start,400):addEventListener('load',()=>setTimeout(start,400),{once:true})}
})();
