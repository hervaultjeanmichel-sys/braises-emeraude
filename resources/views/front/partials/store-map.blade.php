{{-- Carte du magasin : OpenStreetMap + Leaflet, chargée seulement à l'approche (pas de clé API, pas de cookie).
     S'affiche si les coordonnées GPS sont renseignées dans l'admin (Magasins › latitude / longitude). --}}
@if($store->latitude && $store->longitude)
<section class="sec bg-mesh" id="carte" aria-labelledby="carte-title">
    <div class="wrap">
        <div class="sec-head">
            <span class="eyebrow">{{ s('store.map_eyebrow', 'Nous trouver') }}</span>
            <h2 id="carte-title">{!! em(s('store.map_title', 'Venez voir *le feu* en vrai')) !!}</h2>
        </div>
        <div class="store-map"
             data-map
             data-lat="{{ $store->latitude }}" data-lng="{{ $store->longitude }}"
             data-name="{{ $store->name }}"
             data-address="{{ $store->addressLine() }}"
             data-phone="{{ $store->phone }}"
             data-directions="{{ $mapUrl }}"
             data-directions-label="{{ s('store.directions', 'Itinéraire') }}"
             role="region" aria-label="{{ s('store.map_aria', 'Carte d\'accès au magasin') }}">
            <a class="store-map-fallback" href="{{ $mapUrl }}" target="_blank" rel="noopener">{{ s('store.map_open', 'Ouvrir la carte et l\'itinéraire') }} →</a>
        </div>
    </div>
</section>
@push('scripts')
<script>
(()=>{
const el=document.querySelector('[data-map]');if(!el)return;
const css='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',js='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
const esc=s=>String(s||'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
let done=false;
const init=()=>{if(done)return;done=true;
  const l=document.createElement('link');l.rel='stylesheet';l.href=css;l.integrity='sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';l.crossOrigin='';document.head.appendChild(l);
  const s=document.createElement('script');s.src=js;s.integrity='sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';s.crossOrigin='';
  s.onload=()=>{
    const d=el.dataset,pos=[parseFloat(d.lat),parseFloat(d.lng)];
    el.innerHTML='';
    const map=L.map(el,{scrollWheelZoom:false,zoomControl:true}).setView(pos,14);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'}).addTo(map);
    const icon=L.divIcon({className:'map-pin',html:'<span></span>',iconSize:[26,26],iconAnchor:[13,13]});
    L.marker(pos,{icon,title:d.name}).addTo(map).bindPopup('<strong>'+esc(d.name)+'</strong><br>'+esc(d.address)+(d.phone?'<br><a href="tel:'+esc(d.phone.replace(/\s/g,''))+'">'+esc(d.phone)+'</a>':'')+'<br><a href="'+esc(d.directions)+'" target="_blank" rel="noopener">'+esc(d.directionsLabel)+' →</a>');
    map.on('click',()=>map.scrollWheelZoom.enable());
  };
  document.head.appendChild(s);
};
'IntersectionObserver' in window ? new IntersectionObserver((e,o)=>{if(e[0].isIntersecting){o.disconnect();init()}},{rootMargin:'400px'}).observe(el) : init();
})();
</script>
@endpush
@endif
