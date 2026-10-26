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
    const n=performance.now();if(n-last>60&&sparks.length){last=n;const s=sparks[si++%sparks.length],dx=Math.random()*16-8;s.style.background=Math.random()<.3?'var(--emerald)':'';s.animate([{transform:`translate(${mx}px,${my-18}px) scale(1)`,opacity:1},{transform:`translate(${mx+dx}px,${my-52}px) scale(.2)`,opacity:0}],{duration:700,easing:'ease-out',fill:'forwards'})}
    if(!looping){looping=true;requestAnimationFrame(loop)}},{passive:true});
  const sparks=[];let si=0,looping=false;for(let k=0;k<12;k++){const s=document.createElement('span');s.className='spark';s.style.opacity=0;document.body.appendChild(s);sparks.push(s)}
  function loop(){rx+=(mx-rx)*.22;ry+=(my-ry)*.22;fl.style.transform=`translate(${rx}px,${ry}px)`;if(Math.abs(mx-rx)+Math.abs(my-ry)>.5)requestAnimationFrame(loop);else looping=false}
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


/* Scènes animées de la Saga (canvas, sans photo)
   v2 allégée : sprites pré-dessinés (plus de dégradé ni de flou par particule),
   résolution 1x, seule la scène affichée est dimensionnée et dessinée,
   démarrage après le chargement, moins de particules sur mobile. */
(()=>{
const reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;
const canvases=[...document.querySelectorAll('canvas.scene')];
if(!canvases.length)return;
const R=(a,b)=>a+Math.random()*(b-a);
const small=matchMedia('(max-width: 899px)').matches||(navigator.hardwareConcurrency||8)<=4||(navigator.connection&&navigator.connection.saveData);
let mouseX=.5;
if(matchMedia('(pointer:fine)').matches)addEventListener('mousemove',e=>{mouseX=e.clientX/innerWidth},{passive:true});

/* ---------- Sprites ---------- */
const sprite=(size,paint)=>{const c=document.createElement('canvas');c.width=c.height=size;paint(c.getContext('2d'),size/2);return c};
const flameColor=(life,a)=>{
  if(life>.75)return `rgba(255,235,180,${a})`;
  if(life>.5)return `rgba(255,${170+Math.round((life-.5)*260)},60,${a})`;
  if(life>.25)return `rgba(255,${90+Math.round((life-.25)*320)},30,${a})`;
  return `rgba(${120+Math.round(life*500)},40,20,${a*life*3})`;
};
/* 20 paliers de vie pour les flammes : une seule image par palier */
const FL=Array.from({length:20},(_,k)=>{const life=(k+.5)/20;return sprite(64,(c,h)=>{const g=c.createRadialGradient(h,h,0,h,h,h);g.addColorStop(0,flameColor(life,.55));g.addColorStop(1,flameColor(life,0));c.fillStyle=g;c.fillRect(0,0,64,64)})});
const flame=(ctx,x,y,r,life)=>{const s=FL[Math.max(0,Math.min(19,Math.floor(life*20)))];ctx.drawImage(s,x-r,y-r,r*2,r*2)};
/* Point lumineux (remplace shadowBlur) */
const dot=col=>sprite(32,(c,h)=>{const g=c.createRadialGradient(h,h,0,h,h,h);g.addColorStop(0,`rgba(${col},1)`);g.addColorStop(.18,`rgba(${col},.95)`);g.addColorStop(.4,`rgba(${col},.35)`);g.addColorStop(1,`rgba(${col},0)`);c.fillStyle=g;c.fillRect(0,0,32,32)});
const DOT_G=dot('52,227,165'),DOT_O=dot('255,140,55');
const glow=(ctx,img,x,y,r,a)=>{ctx.globalAlpha=a;ctx.drawImage(img,x-r*4,y-r*4,r*8,r*8);ctx.globalAlpha=1};

function setup(cv){
  const ctx=cv.getContext('2d',{alpha:false});
  const s={cv,ctx,W:0,H:0,t:0,parts:[],extra:[],ready:false};
  s.resize=()=>{const w=cv.offsetWidth,h=cv.offsetHeight;if(!w||!h)return;if(s.ready&&w===s.W&&h===s.H)return;s.W=w;s.H=h;cv.width=w;cv.height=h;s.fx=w<900?w*.5:w*.72;s.init();s.ready=true};
  return s;
}

/* ---------- Épisode I : le Maître du Feu ---------- */
function fire(s){
  s.init=()=>{s.parts=[];s.extra=[];for(let i=0;i<(small?28:46);i++)s.extra.push({a:R(0,6.28),r:R(.6,1.4),y:R(0,1),sp:R(.006,.016),z:R(.5,1.5),g:Math.random()<.25})};
  s.draw=()=>{
    const {ctx,W,H}=s,fx=s.fx+(mouseX-.5)*30,base=H*.8,sc=Math.min(1.2,Math.max(.6,H/700));
    const bg=ctx.createRadialGradient(fx,base,10,fx,base,Math.max(W,H)*.7);
    bg.addColorStop(0,'#3a1606');bg.addColorStop(.25,'#160905');bg.addColorStop(.6,'#050807');bg.addColorStop(1,'#000');
    ctx.fillStyle=bg;ctx.fillRect(0,0,W,H);
    const fl=ctx.createRadialGradient(fx,base+20,5,fx,base+20,260*sc);fl.addColorStop(0,'rgba(255,120,40,.35)');fl.addColorStop(1,'rgba(255,120,40,0)');
    ctx.fillStyle=fl;ctx.beginPath();ctx.ellipse(fx,base+22,300*sc,46*sc,0,0,7);ctx.fill();
    ctx.save();ctx.translate(fx,base+8);
    [[-.32],[.32],[0]].forEach(([r],k)=>{ctx.save();ctx.rotate(r);ctx.fillStyle=k===2?'#2a160c':'#1d0f08';const w=170*sc,h=24*sc;ctx.beginPath();ctx.roundRect(-w/2,-h/2+(k===2?6*sc:0),w,h,h/2);ctx.fill();
      ctx.strokeStyle=`rgba(255,110,30,${.35+.25*Math.sin(s.t*3+k)})`;ctx.lineWidth=2;ctx.stroke();ctx.restore()});
    ctx.restore();
    ctx.globalCompositeOperation='lighter';
    for(let i=0;i<(small?5:9);i++)s.parts.push({x:fx+R(-55,55)*sc,y:base-4,vx:R(-.4,.4),vy:R(-2.6,-1.4)*sc,l:1,d:R(.010,.018),r:R(14,30)*sc});
    s.parts=s.parts.filter(p=>p.l>0);
    for(const p of s.parts){p.x+=p.vx+(fx-p.x)*.012+Math.sin(s.t*6+p.y*.05)*.6;p.y+=p.vy;p.l-=p.d;flame(ctx,p.x,p.y,p.r*(.35+p.l*.75),p.l)}
    for(const e of s.extra){e.a+=e.sp;e.y-=.0016*e.z;if(e.y<0)e.y=1;
      const rad=(120+e.r*90)*sc*(.6+e.y*.6),x=fx+Math.cos(e.a)*rad,y=base-30*sc-(1-e.y)*H*.62+Math.sin(e.a)*rad*.22,front=Math.sin(e.a)>0;
      glow(ctx,e.g?DOT_G:DOT_O,x,y,(front?2.6:1.4)*e.z,(front?.95:.35)*Math.min(1,e.y*3))}
    ctx.globalCompositeOperation='source-over';
  };
}

/* ---------- Épisode II : le Gardien du Foyer ---------- */
function stove(s){
  s.init=()=>{s.parts=[];s.extra=[];for(let i=0;i<(small?80:160);i++)s.extra.push({x:R(0,s.W),y:R(0,s.H),v:R(7,13),l:R(10,22)})};
  s.draw=()=>{
    const {ctx,W,H}=s,fx=s.fx+(mouseX-.5)*20,sc=Math.min(1.15,Math.max(.62,H/720));
    ctx.fillStyle='#04080a';ctx.fillRect(0,0,W,H);
    ctx.strokeStyle='rgba(150,190,210,.16)';ctx.lineWidth=1;ctx.beginPath();
    for(const d of s.extra){d.y+=d.v;d.x-=d.v*.18;if(d.y>H){d.y=-20;d.x=R(0,W+100)}ctx.moveTo(d.x,d.y);ctx.lineTo(d.x+d.l*.18,d.y-d.l)}
    ctx.stroke();
    const sw=150*sc,sh=300*sc,top=H*.82-sh;
    const halo=ctx.createRadialGradient(fx,top+sh*.45,10,fx,top+sh*.45,Math.max(W,H)*.55);
    halo.addColorStop(0,`rgba(255,120,40,${.30+.05*Math.sin(s.t*7)})`);halo.addColorStop(.35,'rgba(120,45,15,.18)');halo.addColorStop(1,'rgba(0,0,0,0)');
    ctx.fillStyle=halo;ctx.fillRect(0,0,W,H);
    ctx.fillStyle='rgba(255,120,40,.12)';ctx.beginPath();ctx.ellipse(fx,top+sh+8*sc,sw*1.6,22*sc,0,0,7);ctx.fill();
    ctx.fillStyle='#121615';ctx.fillRect(fx-16*sc,0,32*sc,top+4);
    ctx.fillStyle='rgba(255,255,255,.05)';ctx.fillRect(fx-10*sc,0,4*sc,top);
    const g=ctx.createLinearGradient(fx-sw/2,0,fx+sw/2,0);g.addColorStop(0,'#0c0f0e');g.addColorStop(.45,'#232927');g.addColorStop(1,'#0a0c0b');
    ctx.fillStyle=g;ctx.beginPath();ctx.roundRect(fx-sw/2,top,sw,sh,22*sc);ctx.fill();
    ctx.fillStyle='#0a0c0b';ctx.fillRect(fx-sw*.42,top+sh,sw*.12,10*sc);ctx.fillRect(fx+sw*.30,top+sh,sw*.12,10*sc);
    const wx=fx-sw*.36,wy=top+sh*.18,ww=sw*.72,wh=sh*.46;
    ctx.save();ctx.beginPath();ctx.roundRect(wx,wy,ww,wh,12*sc);ctx.clip();
    ctx.fillStyle='#1a0703';ctx.fillRect(wx,wy,ww,wh);
    ctx.globalCompositeOperation='lighter';
    for(let i=0;i<(small?4:6);i++)s.parts.push({x:fx+R(-ww*.3,ww*.3),y:wy+wh-6,vx:R(-.3,.3),vy:R(-1.6,-.9)*sc,l:1,d:R(.014,.024),r:R(9,17)*sc});
    s.parts=s.parts.filter(p=>p.l>0);
    for(const p of s.parts){p.x+=p.vx+Math.sin(s.t*7+p.y*.08)*.4;p.y+=p.vy;p.l-=p.d;flame(ctx,p.x,p.y,p.r*(.4+p.l*.7),p.l)}
    ctx.restore();
    ctx.strokeStyle='rgba(255,255,255,.12)';ctx.lineWidth=1.5;ctx.beginPath();ctx.roundRect(wx,wy,ww,wh,12*sc);ctx.stroke();
    ctx.fillStyle='rgba(255,255,255,.05)';ctx.beginPath();ctx.moveTo(wx+ww*.15,wy);ctx.lineTo(wx+ww*.35,wy);ctx.lineTo(wx+ww*.1,wy+wh);ctx.lineTo(wx-ww*.1,wy+wh);ctx.fill();
    ctx.fillStyle='#2e3532';ctx.fillRect(fx+sw*.38,wy+wh*.35,5*sc,wh*.3);
    const tx=Math.min(W<900?fx+sw*.55:fx+sw*.75,W-90*sc);
    ctx.font=`${Math.round(64*sc)}px "DM Serif Display",Georgia,serif`;ctx.textAlign='left';
    ctx.fillStyle='rgba(52,227,165,.18)';ctx.fillText('21°',tx-1,top+sh*.32+1);ctx.fillText('21°',tx+1,top+sh*.32-1);
    ctx.fillStyle='rgba(52,227,165,.9)';ctx.fillText('21°',tx,top+sh*.32);
    ctx.font=`600 ${Math.round(11*sc)}px Manrope,system-ui,sans-serif`;ctx.fillStyle='rgba(238,244,241,.55)';
    ctx.fillText('DEDANS',tx+4,top+sh*.32+20*sc);
  };
}

/* ---------- Épisode III : le Retour du Feu ---------- */
function forest(s){
  const pine=(ctx,x,base,h,w)=>{ctx.beginPath();for(let i=0;i<4;i++){const ty=base-h*(i/4)*.92,tw=w*(1-i/4*.75);ctx.moveTo(x-tw/2,ty);ctx.lineTo(x,ty-h*.42);ctx.lineTo(x+tw/2,ty)}ctx.rect(x-w*.04,base-h*.08,w*.08,h*.08);ctx.fill()};
  s.init=()=>{
    s.parts=[];
    /* chaque plan de forêt est dessiné une seule fois dans une image (plus large pour la parallaxe) */
    const cols=['#0b2a20','#071a14','#020806'];
    s.layers=[0,1,2].map(k=>{const pad=40+k*22,c=document.createElement('canvas');c.width=s.W+pad*2;c.height=s.H;const x2=c.getContext('2d');x2.fillStyle=cols[k];
      let x=-40;while(x<s.W+pad*2+80){const h=R(.28,.52)*s.H*(1-k*.18);pine(x2,x,s.H*(.86+k*.06),h,h*R(.32,.42));x+=R(28,70)*(1+k*.3)}
      return {c,pad,amp:10+k*22}});
    s.stars=Array.from({length:small?50:90},()=>({x:R(0,s.W),y:R(0,s.H*.5),r:R(.3,1.2),p:R(0,6)}));
  };
  s.draw=()=>{
    const {ctx,W,H,t}=s;
    const sky=ctx.createLinearGradient(0,0,0,H);sky.addColorStop(0,'#010403');sky.addColorStop(.55,'#04120d');sky.addColorStop(1,'#0a1f17');ctx.fillStyle=sky;ctx.fillRect(0,0,W,H);
    ctx.fillStyle='#dcfff0';
    for(const st of s.stars){ctx.globalAlpha=.35+.35*Math.sin(t*2+st.p);ctx.fillRect(st.x-st.r,st.y-st.r,st.r*2,st.r*2)}
    ctx.globalAlpha=1;
    ctx.globalCompositeOperation='lighter';
    for(let b=0;b<3;b++){ctx.beginPath();const y0=H*(.18+b*.07);ctx.moveTo(0,y0);
      for(let x=0;x<=W;x+=40)ctx.lineTo(x,y0+Math.sin(x*.004+t*.6+b)*40+Math.sin(x*.011+t*.3)*18);
      ctx.lineTo(W,y0+160);ctx.lineTo(0,y0+160);ctx.closePath();
      const ag=ctx.createLinearGradient(0,y0-40,0,y0+160);ag.addColorStop(0,'rgba(52,227,165,0)');ag.addColorStop(.3,`rgba(52,227,165,${.10-b*.025})`);ag.addColorStop(1,'rgba(52,227,165,0)');ctx.fillStyle=ag;ctx.fill()}
    ctx.globalCompositeOperation='source-over';
    for(const L of s.layers)ctx.drawImage(L.c,-L.pad+(mouseX-.5)*L.amp,0);
    ctx.fillStyle='#020806';ctx.fillRect(0,H*.98,W,H*.02);
    if(Math.random()<(small?.3:.5))s.parts.push({x:R(0,W),y:H*R(.8,.98),vx:R(-.3,.3),vy:R(-1.2,-.5),rot:R(0,6),vr:R(-.04,.04),sz:R(4,8)});
    s.parts=s.parts.filter(p=>p.y>-20);
    ctx.globalCompositeOperation='lighter';
    for(const p of s.parts){p.x+=p.vx+Math.sin(t*2+p.y*.02)*.5;p.y+=p.vy;p.rot+=p.vr;
      const k=Math.min(1,Math.max(0,(H*.75-p.y)/(H*.4)));
      if(k<1){ctx.save();ctx.translate(p.x,p.y);ctx.rotate(p.rot);ctx.fillStyle=`rgba(52,227,165,${.75*(1-k)})`;ctx.beginPath();ctx.ellipse(0,0,p.sz,p.sz*.45,0,0,7);ctx.fill();ctx.restore()}
      if(k>0)glow(ctx,DOT_O,p.x,p.y,1.2+1.6*k,.9*k*Math.max(0,Math.min(1,p.y/H+.2)))}
    ctx.globalCompositeOperation='source-over';
  };
}

const makers={fire,stove,forest};
const scenes=canvases.map(cv=>{const s=setup(cv);makers[cv.dataset.scene](s);return s});
const active=()=>scenes.find(s=>s.cv.closest('.act').classList.contains('on'));
const warm=(s,n)=>{s.resize();if(!s.ready)return;for(let i=0;i<n;i++){s.t+=1/60;s.draw()}};
addEventListener('resize',()=>{const s=active();if(s){s.ready=false;s.resize()}},{passive:true});

let visible=true;
const host=document.getElementById('saga');
if(host)new IntersectionObserver(e=>visible=e[0].isIntersecting).observe(host);
function frame(now){
  requestAnimationFrame(frame);
  if(!visible||document.hidden)return;
  const s=active();if(!s)return;
  if(!s.ready)warm(s,40);
  s.t+=1/60;s.draw();
}
const start=()=>{
  const s=active();
  if(reduce){scenes.forEach(x=>warm(x,1));return}
  if(s)warm(s,40);
  requestAnimationFrame(frame);
};
/* on laisse la page s'afficher d'abord */
if(document.readyState==='complete')setTimeout(start,50);else addEventListener('load',()=>setTimeout(start,50),{once:true});
})();
