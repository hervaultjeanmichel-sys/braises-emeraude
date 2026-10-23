{{-- Icônes SVG du thème Contrastes. Usage : @include('front.partials.icon', ['name' => 'pin']) --}}
@switch($name)
@case('bois')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 2c1 3 4 5 4 9a4 4 0 0 1-8 0c0-2 1-3 1-3s0 2 2 2c0-3-2-5 1-8z"/><path d="M6 21h12"/></svg>@break
@case('granules')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><circle cx="12" cy="13" r="3.5"/><path d="M9 6h6"/></svg>@break
@case('mixte')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 3c.8 2 3 3 3 6a3 3 0 0 1-6 0c0-2 3-3 3-6zM16 9c.8 2 3 3 3 6a3 3 0 0 1-6 0c0-2 3-3 3-6z"/><path d="M4 21h16"/></svg>@break
@case('cuisson')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 11h18v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M7 11V7M12 11V5M17 11V7"/></svg>@break
@case('pin')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s-7-6.3-7-11a7 7 0 0 1 14 0c0 4.7-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>@break
@case('phone')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>@break
@case('clock')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>@break
@case('calendar')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 4h18v18H3zM16 2v4M8 2v4M3 10h18"/></svg>@break
@case('gift')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 12v10H4V12M2 7h20v5H2zM12 22V7M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7zM12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>@break
@case('mail')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>@break
@case('arrow')<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>@break
@case('prev')<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>@break
@case('next')<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>@break
@case('leaf')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 19c0-9 6-14 15-15-1 9-6 15-15 15z"/><path d="M5 19l7-7"/></svg>@break
@case('check')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg>@break
@case('scribble')<svg class="scribble" viewBox="0 0 150 14" fill="none" aria-hidden="true"><path d="M2 9c30-6 70-8 146-3" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>@break
@case('project')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M9 4h6l1 3h4v13H4V7h4z"/><path d="M9 13l2 2 4-4"/></svg>@break
@case('plus')<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>@break
@case('menu')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>@break
@case('x')<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>@break
@case('flame')<svg viewBox="0 0 26 36" aria-hidden="true"><path d="M13 1c2 6 11 10 11 20a11 11 0 0 1-22 0c0-5 3-8 3-8s0 5 4 5c0-7-3-11 4-17z" fill="#ff7b30"/><path class="core" d="M13 15c1 3 6 6 6 11a6 6 0 0 1-12 0c0-3 2-4 2-4s0 3 2 3c0-4-1-6 2-10z" fill="#ffd166"/></svg>@break
@endswitch
