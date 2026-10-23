/* Braises Émeraude — scripts du thème Contrastes (repris de la maquette) */

(()=>{
const reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;

/* Header au scroll */
const top=document.getElementById('top');
if(top)addEventListener('scroll',()=>top.classList.toggle('scrolled',scrollY>10),{passive:true});

/* Diaporama */
const slider=document.getElementById('slider');if(slider){const slides=[...slider.querySelectorAll('.slide')],dots=[...slider.querySelectorAll('.dots button')];
let i=0,timer;
const show=n=>{i=(n+slides.length)%slides.length;slides.forEach((s,k)=>s.classList.toggle('on',k===i));dots.forEach((d,k)=>{d.classList.remove('on');void d.offsetWidth;d.classList.toggle('on',k===i)});restart()};
const restart=()=>{clearInterval(timer);if(!reduce)timer=setInterval(()=>show(i+1),5500)};
document.getElementById('next').onclick=()=>show(i+1);
document.getElementById('prev').onclick=()=>show(i-1);
dots.forEach((d,k)=>d.onclick=()=>show(k));
slider.addEventListener('mouseenter',()=>{slider.classList.add('paused');clearInterval(timer)});
slider.addEventListener('mouseleave',()=>{slider.classList.remove('paused');restart()});
let sx=0;slider.addEventListener('touchstart',e=>sx=e.touches[0].clientX,{passive:true});
slider.addEventListener('touchend',e=>{const dx=e.changedTouches[0].clientX-sx;if(Math.abs(dx)>40)show(i+(dx<0?1:-1))});
restart();
/* Inclinaison 3D du diaporama */
const deck=document.getElementById('slides');
if(!reduce&&matchMedia('(pointer:fine)').matches){
  slider.addEventListener('mousemove',e=>{const r=slider.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;deck.style.transform=`perspective(900px) rotateY(${x*8}deg) rotateX(${-y*8}deg)`});
  slider.addEventListener('mouseleave',()=>deck.style.transform='');
}}


/* Saga : diaporama cinéma */
const saga=document.getElementById('saga');
if(saga){
  const acts=[...saga.querySelectorAll('.act')],reel=[...saga.querySelectorAll('#reel button')],cnt=document.getElementById('ep-count');
  saga.querySelectorAll('[data-split]').forEach(el=>{let k=0;el.innerHTML=el.textContent.split(' ').map(w=>'<span class="w">'+[...w].map(ch=>'<span class="ch" style="transition-delay:'+(0.15+0.045*(k++))+'s">'+ch+'</span>').join('')+'</span>').join(' ')});
  let a=0,tm;
  const go=n=>{a=(n+acts.length)%acts.length;acts.forEach((x,k)=>{x.classList.toggle('on',k===a);x.setAttribute('aria-hidden',k===a?'false':'true');const v=x.querySelector('video');if(v){if(k===a&&!reduce){v.currentTime=0;v.play().catch(()=>{})}else v.pause()}});reel.forEach((r,k)=>{r.classList.remove('on');void r.offsetWidth;r.classList.toggle('on',k===a)});cnt.textContent='0'+(a+1)+' / 0'+acts.length;run()};
  const run=()=>{clearInterval(tm);if(!reduce)tm=setInterval(()=>go(a+1),7000)};
  document.getElementById('s-next').onclick=()=>go(a+1);document.getElementById('s-prev').onclick=()=>go(a-1);
  reel.forEach((r,k)=>r.onclick=()=>go(k));
  saga.addEventListener('mouseenter',()=>{saga.classList.add('paused');clearInterval(tm)});
  saga.addEventListener('mouseleave',()=>{saga.classList.remove('paused');run()});
  let tx=0;saga.addEventListener('touchstart',e=>tx=e.touches[0].clientX,{passive:true});
  saga.addEventListener('touchend',e=>{const dx=e.changedTouches[0].clientX-tx;if(Math.abs(dx)>40)go(a+(dx<0?1:-1))});
  go(0);
}

/* Halo qui suit la souris sur les magasins */
document.querySelectorAll('.store').forEach(c=>c.addEventListener('mousemove',e=>{const r=c.getBoundingClientRect();c.style.setProperty('--mx',(e.clientX-r.left)+'px');c.style.setProperty('--my',(e.clientY-r.top)+'px')}));

/* Ouvert / fermé : calculé côté serveur depuis les horaires de l'admin (classe .closed) */

/* Reveal : seuls les éléments sous la ligne de flottaison sont masqués puis animés */
if(!reduce&&'IntersectionObserver'in window){
  const els=[...document.querySelectorAll('.reveal')].filter(el=>el.getBoundingClientRect().top>innerHeight);
  els.forEach(el=>el.classList.add('pre'));
  const io=new IntersectionObserver(es=>es.forEach(en=>{if(en.isIntersecting){en.target.classList.remove('pre');en.target.classList.add('in');io.unobserve(en.target)}}),{rootMargin:'0px 0px -10% 0px'});
  els.forEach(el=>io.observe(el));
  document.querySelectorAll('.step').forEach(el=>{if(!el.classList.contains('pre'))el.classList.add('in')});
}else document.querySelectorAll('.step').forEach(el=>el.classList.add('in'));

/* Parallaxe */
const px=document.querySelector('[data-parallax]');
if(px&&!reduce){
  const run=()=>{const r=px.parentElement.getBoundingClientRect();if(r.bottom>0&&r.top<innerHeight){const p=(r.top+r.height/2-innerHeight/2)/innerHeight;px.style.transform=`translate3d(0,${p*-90}px,0) scale(1.05)`}};
  addEventListener('scroll',()=>requestAnimationFrame(run),{passive:true});run();
}

/* Curseur flamme + étincelles */
if(!reduce&&matchMedia('(pointer:fine)').matches){
  const c=document.querySelector('.cur'),fl=document.querySelector('.flame');let mx=-99,my=-99,rx=-99,ry=-99,last=0;
  addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;c.style.transform=`translate(${mx}px,${my}px)`;
    const n=performance.now();if(n-last>45){last=n;const s=document.createElement('span');s.className='spark';s.style.left=mx+'px';s.style.top=(my-18)+'px';s.style.setProperty('--dx',(Math.random()*16-8)+'px');if(Math.random()<.3)s.style.background='var(--emerald)';s.style.display='block';document.body.appendChild(s);setTimeout(()=>s.remove(),700)}});
  (function loop(){rx+=(mx-rx)*.22;ry+=(my-ry)*.22;fl.style.transform=`translate(${rx}px,${ry}px)`;requestAnimationFrame(loop)})();
  document.querySelectorAll('a,button,[data-hover],summary').forEach(el=>{el.addEventListener('mouseenter',()=>fl.classList.add('big'));el.addEventListener('mouseleave',()=>fl.classList.remove('big'))});
}

/* Braises qui montent dans le hero */
const cv=document.getElementById('embers');
if(cv&&!reduce){
  const ctx=cv.getContext('2d');let W,H,parts=[];const dpr=Math.min(devicePixelRatio||1,2);
  const size=()=>{W=cv.offsetWidth;H=cv.offsetHeight;cv.width=W*dpr;cv.height=H*dpr;ctx.setTransform(dpr,0,0,dpr,0,0)};
  size();addEventListener('resize',size);
  const N=Math.min(70,Math.round(W/16));
  const mk=(y)=>({x:Math.random()*W,y:y??H+10,r:Math.random()*1.8+.4,v:Math.random()*.6+.25,w:Math.random()*Math.PI*2,g:Math.random()<.22,a:Math.random()*.6+.3});
  for(let k=0;k<N;k++)parts.push(mk(Math.random()*H));
  let vis=true;new IntersectionObserver(e=>vis=e[0].isIntersecting).observe(cv);
  (function tick(){
    if(vis){ctx.clearRect(0,0,W,H);
      for(const p of parts){p.y-=p.v;p.w+=.02;p.x+=Math.sin(p.w)*.35;const life=Math.max(0,Math.min(1,p.y/H));
        ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,7);ctx.fillStyle=p.g?`rgba(52,227,165,${p.a*life})`:`rgba(255,${120+Math.round(60*life)},48,${p.a*life})`;
        ctx.shadowBlur=8;ctx.shadowColor=p.g?'rgba(52,227,165,.8)':'rgba(255,123,48,.8)';ctx.fill();
        if(p.y<-10)Object.assign(p,mk());}
      ctx.shadowBlur=0;}
    requestAnimationFrame(tick)})();
}
})();


/* Scènes animées de la Saga (canvas, sans photo) */
(()=>{
const reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;
const canvases=[...document.querySelectorAll('canvas.scene')];
if(!canvases.length)return;
const R=(a,b)=>a+Math.random()*(b-a);
let mouseX=.5,mouseY=.5;
addEventListener('mousemove',e=>{mouseX=e.clientX/innerWidth;mouseY=e.clientY/innerHeight},{passive:true});

function setup(cv){
  const ctx=cv.getContext('2d'),dpr=Math.min(devicePixelRatio||1,2);
  const s={cv,ctx,W:0,H:0,t:0,parts:[],extra:[]};
  s.resize=()=>{s.W=cv.offsetWidth;s.H=cv.offsetHeight;cv.width=s.W*dpr;cv.height=s.H*dpr;ctx.setTransform(dpr,0,0,dpr,0,0);s.fx=s.W<900?s.W*.5:s.W*.72;if(s.init)s.init()};
  return s;
}
const flameColor=(life,a)=>{ // life 1 -> 0
  if(life>.75)return `rgba(255,${235},${180},${a})`;
  if(life>.5)return `rgba(255,${170+Math.round((life-.5)*260)},60,${a})`;
  if(life>.25)return `rgba(255,${90+Math.round((life-.25)*320)},30,${a})`;
  return `rgba(${120+Math.round(life*500)},40,20,${a*life*3})`;
};

/* ---------- Épisode I : le Maître du Feu ---------- */
function fire(s){
  s.init=()=>{s.parts=[];s.extra=[];for(let i=0;i<46;i++)s.extra.push({a:R(0,6.28),r:R(.6,1.4),y:R(0,1),sp:R(.006,.016),z:R(.5,1.5),g:Math.random()<.25})};
  s.draw=()=>{
    const {ctx,W,H}=s,fx=s.fx+(mouseX-.5)*30,base=H*.8,sc=Math.min(1.2,Math.max(.6,H/700));
    ctx.globalCompositeOperation='source-over';
    const bg=ctx.createRadialGradient(fx,base,10,fx,base,Math.max(W,H)*.7);
    bg.addColorStop(0,'#3a1606');bg.addColorStop(.25,'#160905');bg.addColorStop(.6,'#050807');bg.addColorStop(1,'#000');
    ctx.fillStyle=bg;ctx.fillRect(0,0,W,H);
    // sol
    const fl=ctx.createRadialGradient(fx,base+20,5,fx,base+20,260*sc);fl.addColorStop(0,'rgba(255,120,40,.35)');fl.addColorStop(1,'rgba(255,120,40,0)');
    ctx.fillStyle=fl;ctx.beginPath();ctx.ellipse(fx,base+22,300*sc,46*sc,0,0,7);ctx.fill();
    // bûches
    ctx.save();ctx.translate(fx,base+8);
    [[-.32],[.32],[0]].forEach(([r],k)=>{ctx.save();ctx.rotate(r);ctx.fillStyle=k===2?'#2a160c':'#1d0f08';const w=170*sc,h=24*sc;ctx.beginPath();ctx.roundRect(-w/2,-h/2+(k===2?6*sc:0),w,h,h/2);ctx.fill();
      ctx.strokeStyle=`rgba(255,110,30,${.35+.25*Math.sin(s.t*3+k)})`;ctx.lineWidth=2;ctx.stroke();ctx.restore()});
    ctx.restore();
    // flammes
    ctx.globalCompositeOperation='lighter';
    for(let i=0;i<(reduce?0:9);i++)s.parts.push({x:fx+R(-55,55)*sc,y:base-4,vx:R(-.4,.4),vy:R(-2.6,-1.4)*sc,l:1,d:R(.010,.018),r:R(14,30)*sc});
    s.parts=s.parts.filter(p=>p.l>0);
    for(const p of s.parts){p.x+=p.vx+(fx-p.x)*.012+Math.sin(s.t*6+p.y*.05)*.6;p.y+=p.vy;p.l-=p.d;const r=p.r*(.35+p.l*.75);
      const g=ctx.createRadialGradient(p.x,p.y,0,p.x,p.y,r);g.addColorStop(0,flameColor(p.l,.55));g.addColorStop(1,flameColor(p.l,0));ctx.fillStyle=g;ctx.beginPath();ctx.arc(p.x,p.y,r,0,7);ctx.fill()}
    // braises en orbite (le « maître » contrôle le feu)
    for(const e of s.extra){e.a+=e.sp*(reduce?0:1);e.y-=reduce?0:.0016*e.z;if(e.y<0)e.y=1;
      const rad=(120+e.r*90)*sc*(.6+e.y*.6),x=fx+Math.cos(e.a)*rad,y=base-30*sc-(1-e.y)*H*.62+Math.sin(e.a)*rad*.22,front=Math.sin(e.a)>0;
      const al=(front?.95:.35)*Math.min(1,e.y*3);const rr=(front?2.6:1.4)*e.z;
      ctx.fillStyle=e.g?`rgba(52,227,165,${al})`:`rgba(255,${150+Math.round(e.y*80)},60,${al})`;ctx.shadowBlur=12;ctx.shadowColor=e.g?'#34e3a5':'#ff7b30';ctx.beginPath();ctx.arc(x,y,rr,0,7);ctx.fill()}
    ctx.shadowBlur=0;ctx.globalCompositeOperation='source-over';
  };
}

/* ---------- Épisode II : le Gardien du Foyer ---------- */
function stove(s){
  s.init=()=>{s.parts=[];s.extra=[];for(let i=0;i<160;i++)s.extra.push({x:R(0,s.W),y:R(0,s.H),v:R(7,13),l:R(10,22)})};
  s.draw=()=>{
    const {ctx,W,H}=s,fx=s.fx+(mouseX-.5)*20,sc=Math.min(1.15,Math.max(.62,H/720));
    ctx.globalCompositeOperation='source-over';
    ctx.fillStyle='#04080a';ctx.fillRect(0,0,W,H);
    // pluie dehors
    ctx.strokeStyle='rgba(150,190,210,.16)';ctx.lineWidth=1;ctx.beginPath();
    for(const d of s.extra){if(!reduce){d.y+=d.v;d.x-=d.v*.18;if(d.y>H){d.y=-20;d.x=R(0,W+100)}}ctx.moveTo(d.x,d.y);ctx.lineTo(d.x+d.l*.18,d.y-d.l)}
    ctx.stroke();
    const sw=150*sc,sh=300*sc,top=H*.82-sh;
    // halo chaud
    const halo=ctx.createRadialGradient(fx,top+sh*.45,10,fx,top+sh*.45,Math.max(W,H)*.55);
    halo.addColorStop(0,`rgba(255,120,40,${.30+.05*Math.sin(s.t*7)})`);halo.addColorStop(.35,'rgba(120,45,15,.18)');halo.addColorStop(1,'rgba(0,0,0,0)');
    ctx.fillStyle=halo;ctx.fillRect(0,0,W,H);
    // sol + reflet
    ctx.fillStyle='rgba(255,120,40,.12)';ctx.beginPath();ctx.ellipse(fx,top+sh+8*sc,sw*1.6,22*sc,0,0,7);ctx.fill();
    // conduit
    ctx.fillStyle='#121615';ctx.fillRect(fx-16*sc,0,32*sc,top+4);
    ctx.fillStyle='rgba(255,255,255,.05)';ctx.fillRect(fx-10*sc,0,4*sc,top);
    // corps du poêle
    const g=ctx.createLinearGradient(fx-sw/2,0,fx+sw/2,0);g.addColorStop(0,'#0c0f0e');g.addColorStop(.45,'#232927');g.addColorStop(1,'#0a0c0b');
    ctx.fillStyle=g;ctx.beginPath();ctx.roundRect(fx-sw/2,top,sw,sh,22*sc);ctx.fill();
    ctx.fillStyle='#0a0c0b';ctx.fillRect(fx-sw*.42,top+sh,sw*.12,10*sc);ctx.fillRect(fx+sw*.30,top+sh,sw*.12,10*sc);
    // vitre
    const wx=fx-sw*.36,wy=top+sh*.18,ww=sw*.72,wh=sh*.46;
    ctx.save();ctx.beginPath();ctx.roundRect(wx,wy,ww,wh,12*sc);ctx.clip();
    ctx.fillStyle='#1a0703';ctx.fillRect(wx,wy,ww,wh);
    ctx.globalCompositeOperation='lighter';
    for(let i=0;i<(reduce?0:6);i++)s.parts.push({x:fx+R(-ww*.3,ww*.3),y:wy+wh-6,vx:R(-.3,.3),vy:R(-1.6,-.9)*sc,l:1,d:R(.014,.024),r:R(9,17)*sc});
    s.parts=s.parts.filter(p=>p.l>0);
    for(const p of s.parts){p.x+=p.vx+Math.sin(s.t*7+p.y*.08)*.4;p.y+=p.vy;p.l-=p.d;const r=p.r*(.4+p.l*.7);
      const fg=ctx.createRadialGradient(p.x,p.y,0,p.x,p.y,r);fg.addColorStop(0,flameColor(p.l,.6));fg.addColorStop(1,flameColor(p.l,0));ctx.fillStyle=fg;ctx.beginPath();ctx.arc(p.x,p.y,r,0,7);ctx.fill()}
    ctx.restore();
    ctx.strokeStyle='rgba(255,255,255,.12)';ctx.lineWidth=1.5;ctx.beginPath();ctx.roundRect(wx,wy,ww,wh,12*sc);ctx.stroke();
    // reflet vitre
    ctx.fillStyle='rgba(255,255,255,.05)';ctx.beginPath();ctx.moveTo(wx+ww*.15,wy);ctx.lineTo(wx+ww*.35,wy);ctx.lineTo(wx+ww*.1,wy+wh);ctx.lineTo(wx-ww*.1,wy+wh);ctx.fill();
    // poignée
    ctx.fillStyle='#2e3532';ctx.fillRect(fx+sw*.38,wy+wh*.35,5*sc,wh*.3);
    // 21°
    ctx.font=`${Math.round(64*sc)}px "DM Serif Display",Georgia,serif`;ctx.textAlign='left';
    ctx.fillStyle='rgba(52,227,165,.9)';ctx.shadowBlur=24;ctx.shadowColor='rgba(52,227,165,.6)';
    const tx=W<900?fx+sw*.55:fx+sw*.75;ctx.fillText('21°',Math.min(tx,W-90*sc),top+sh*.32);ctx.shadowBlur=0;
    ctx.font=`600 ${Math.round(11*sc)}px Manrope,system-ui,sans-serif`;ctx.fillStyle='rgba(238,244,241,.55)';
    ctx.fillText('DEDANS',Math.min(tx,W-90*sc)+4,top+sh*.32+20*sc);
  };
}

/* ---------- Épisode III : le Retour du Feu ---------- */
function forest(s){
  s.init=()=>{s.parts=[];s.layers=[0,1,2].map(k=>{const arr=[];let x=-40;while(x<s.W+80){const h=R(.28,.52)*s.H*(1-k*.18);arr.push({x,h,w:h*R(.32,.42)});x+=R(28,70)*(1+k*.3)}return arr});s.stars=Array.from({length:90},()=>({x:R(0,s.W),y:R(0,s.H*.5),r:R(.3,1.2),p:R(0,6)}))};
  const pine=(ctx,x,base,h,w)=>{ctx.beginPath();const tiers=4;for(let i=0;i<tiers;i++){const ty=base-h*(i/tiers)*.92,tw=w*(1-i/tiers*.75);ctx.moveTo(x-tw/2,ty);ctx.lineTo(x,ty-h*.42);ctx.lineTo(x+tw/2,ty)}ctx.rect(x-w*.04,base-h*.08,w*.08,h*.08);ctx.fill()};
  s.draw=()=>{
    const {ctx,W,H,t}=s;
    ctx.globalCompositeOperation='source-over';
    const sky=ctx.createLinearGradient(0,0,0,H);sky.addColorStop(0,'#010403');sky.addColorStop(.55,'#04120d');sky.addColorStop(1,'#0a1f17');ctx.fillStyle=sky;ctx.fillRect(0,0,W,H);
    for(const st of s.stars){ctx.fillStyle=`rgba(220,255,240,${.35+.35*Math.sin(t*2+st.p)})`;ctx.beginPath();ctx.arc(st.x,st.y,st.r,0,7);ctx.fill()}
    // aurore émeraude
    ctx.globalCompositeOperation='lighter';
    for(let b=0;b<3;b++){ctx.beginPath();const y0=H*(.18+b*.07);ctx.moveTo(0,y0);
      for(let x=0;x<=W;x+=20)ctx.lineTo(x,y0+Math.sin(x*.004+t*.6+b)*40+Math.sin(x*.011+t*.3)*18);
      ctx.lineTo(W,y0+160);ctx.lineTo(0,y0+160);ctx.closePath();
      const ag=ctx.createLinearGradient(0,y0-40,0,y0+160);ag.addColorStop(0,'rgba(52,227,165,0)');ag.addColorStop(.3,`rgba(52,227,165,${.10-b*.025})`);ag.addColorStop(1,'rgba(52,227,165,0)');ctx.fillStyle=ag;ctx.fill()}
    ctx.globalCompositeOperation='source-over';
    // forêt en 3 plans (parallaxe souris)
    const cols=['#0b2a20','#071a14','#020806'];
    s.layers.forEach((L,k)=>{ctx.fillStyle=cols[k];const off=(mouseX-.5)*(10+k*22);for(const tr of L)pine(ctx,tr.x+off,H*(.86+k*.06),tr.h,tr.w)});
    ctx.fillStyle='#020806';ctx.fillRect(0,H*.98,W,H*.02);
    // feuilles qui montent et deviennent braises
    if(!reduce&&Math.random()<.5)s.parts.push({x:R(0,W),y:H*R(.8,.98),vx:R(-.3,.3),vy:R(-1.2,-.5),l:0,rot:R(0,6),vr:R(-.04,.04),sz:R(4,8)});
    s.parts=s.parts.filter(p=>p.y>-20);
    ctx.globalCompositeOperation='lighter';
    for(const p of s.parts){p.x+=p.vx+Math.sin(t*2+p.y*.02)*.5;p.y+=p.vy;p.rot+=p.vr;p.l=Math.min(1,p.l+.004);
      const k=Math.min(1,Math.max(0,(H*.75-p.y)/(H*.4))); // 0 = feuille, 1 = braise
      if(k<1){ctx.save();ctx.translate(p.x,p.y);ctx.rotate(p.rot);ctx.fillStyle=`rgba(52,227,165,${.75*(1-k)})`;ctx.beginPath();ctx.ellipse(0,0,p.sz,p.sz*.45,0,0,7);ctx.fill();ctx.restore()}
      if(k>0){ctx.fillStyle=`rgba(255,${130+Math.round(80*(1-k))},50,${.9*k*Math.max(0,p.y/H+.2)})`;ctx.shadowBlur=10;ctx.shadowColor='#ff7b30';ctx.beginPath();ctx.arc(p.x,p.y,1.2+1.6*k,0,7);ctx.fill();ctx.shadowBlur=0}}
    ctx.globalCompositeOperation='source-over';
  };
}

const makers={fire,stove,forest};
const scenes=canvases.map(cv=>{const s=setup(cv);makers[cv.dataset.scene](s);s.resize();return s});
addEventListener('resize',()=>scenes.forEach(s=>s.resize()));
let visible=true;
const host=document.getElementById('saga');
if(host)new IntersectionObserver(e=>visible=e[0].isIntersecting).observe(host);
function frame(){
  if(visible)scenes.forEach(s=>{if(s.cv.closest('.act').classList.contains('on')){s.t+=1/60;s.draw()}});
  if(!reduce)requestAnimationFrame(frame);
}
if(reduce)scenes.forEach(s=>{s.t=1;for(let i=0;i<1;i++)s.draw()});
else{
  // pré-chauffe : la scène démarre déjà « allumée »
  scenes.forEach(s=>{for(let i=0;i<90;i++){s.t+=1/60;s.draw()}});
  requestAnimationFrame(frame);
}
})();
