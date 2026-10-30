/* Cartes des magasins — Leaflet 1.9.4 + OpenStreetMap, chargés à l'approche (pas de clé, pas de cookie).
   <div data-map data-markers='[{"lat":..,"lng":..,"name":"..","address":"..","phone":"..","url":"/..","directions":"https://.."}]'
        data-labels='{"directions":"Itinéraire","page":"Voir le magasin"}'> */
(()=>{
const maps=[...document.querySelectorAll('[data-map]')];
if(!maps.length)return;
const CSS={href:'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',integrity:'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY='};
const JS={src:'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',integrity:'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo='};
const esc=s=>String(s??'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
let loading=null;
const leaflet=()=>loading||(loading=new Promise((ok,ko)=>{
  const l=document.createElement('link');l.rel='stylesheet';l.href=CSS.href;l.integrity=CSS.integrity;l.crossOrigin='';document.head.appendChild(l);
  const s=document.createElement('script');s.src=JS.src;s.integrity=JS.integrity;s.crossOrigin='';s.onload=ok;s.onerror=ko;document.head.appendChild(s);
}));
const draw=el=>{
  let markers=[],labels={};
  try{markers=JSON.parse(el.dataset.markers||'[]');labels=JSON.parse(el.dataset.labels||'{}')}catch(e){return}
  markers=markers.filter(m=>m.lat&&m.lng);if(!markers.length)return;
  el.innerHTML='';
  const map=L.map(el,{scrollWheelZoom:false});
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'}).addTo(map);
  const pts=markers.map(m=>{
    const icon=L.divIcon({className:'map-pin'+(m.isNew?' is-new':''),html:'<span></span>'+(markers.length>1?'<b>'+esc(m.short||m.name)+'</b>':''),iconSize:[26,26],iconAnchor:[13,13]});
    const html='<strong>'+esc(m.name)+'</strong><br>'+esc(m.address)
      +(m.phone?'<br><a href="tel:'+esc(String(m.phone).replace(/\s/g,''))+'">'+esc(m.phone)+'</a>':'')
      +(m.url?'<br><a href="'+esc(m.url)+'">'+esc(labels.page||'Voir le magasin')+' →</a>':'')
      +(m.directions?'<br><a href="'+esc(m.directions)+'" target="_blank" rel="noopener">'+esc(labels.directions||'Itinéraire')+' →</a>':'');
    L.marker([m.lat,m.lng],{icon,title:m.name}).addTo(map).bindPopup(html);
    return [m.lat,m.lng];
  });
  // cercles de zone (carte du groupe uniquement) : cadrage d'abord, cercles ensuite (Leaflet exige une vue)
  const pal=['#8a5a16','#0d8bb5','#ff7b30','#7a5af5'];
  const zoned=markers.length>1?markers.filter(m=>m.radius>0):[];
  if(pts.length===1)map.setView(pts[0],14);
  else if(zoned.length){
    const b=L.latLngBounds(pts);
    zoned.forEach(m=>b.extend(L.latLng(m.lat,m.lng).toBounds(m.radius*2000)));
    map.fitBounds(b,{padding:[20,20]});
  }
  else map.fitBounds(pts,{padding:[60,60]});
  zoned.forEach(m=>{const col=m.isNew?'#ff7b30':pal[markers.indexOf(m)%pal.length];
    L.circle([m.lat,m.lng],{radius:m.radius*1000,color:col,weight:2,opacity:.85,fillColor:col,fillOpacity:.14,interactive:false}).addTo(map)});
  map.on('click',()=>map.scrollWheelZoom.enable());
};
const start=el=>leaflet().then(()=>draw(el)).catch(()=>{});
maps.forEach(el=>'IntersectionObserver' in window
  ? new IntersectionObserver((e,o)=>{if(e[0].isIntersecting){o.disconnect();start(el)}},{rootMargin:'400px'}).observe(el)
  : start(el));
})();
