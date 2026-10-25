/* Accessible interaction layer. GSAP is progressive enhancement only. */
(()=>{'use strict';
 const reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
 const intensity=Number(getComputedStyle(document.documentElement).getPropertyValue('--motion-strength'));
 const header=document.querySelector('#site-header');
 const onScroll=()=>header?.classList.toggle('scrolled',window.scrollY>48);
 onScroll();window.addEventListener('scroll',onScroll,{passive:true});
 const mob=document.querySelector('.mobile-toggle');const menu=document.querySelector('.mobile-menu');
 const setMobile=(open)=>{if(!menu||!mob)return;menu.classList.toggle('open',open);menu.setAttribute('aria-hidden',String(!open));mob.setAttribute('aria-expanded',String(open));mob.setAttribute('aria-label',open?'Close menu':'Open menu');document.body.style.overflow=open?'hidden':'';};
 mob?.addEventListener('click',()=>setMobile(!menu.classList.contains('open')));
 menu?.querySelectorAll('a').forEach(link=>link.addEventListener('click',()=>setMobile(false)));
 document.addEventListener('keydown',e=>{if(e.key==='Escape'){setMobile(false);document.querySelectorAll('.nav-item.open').forEach(el=>{el.classList.remove('open');el.querySelector('.nav-toggle')?.setAttribute('aria-expanded','false');});}});
 document.querySelectorAll('.nav-toggle').forEach(btn=>btn.addEventListener('click',()=>{
  const item=btn.closest('.nav-item'); const opening=!item.classList.contains('open');
  document.querySelectorAll('.nav-item.open').forEach(el=>{el.classList.remove('open');el.querySelector('.nav-toggle')?.setAttribute('aria-expanded','false');});
  item.classList.toggle('open',opening);btn.setAttribute('aria-expanded',String(opening));
 }));
 document.addEventListener('pointerdown',e=>{if(!e.target.closest('.nav-item'))document.querySelectorAll('.nav-item.open').forEach(el=>{el.classList.remove('open');el.querySelector('.nav-toggle')?.setAttribute('aria-expanded','false');});});
 // Feature image transition on keyboard focus as well as desktop hover.
 const show=document.querySelector('[data-service-showcase]');
 if(show){let timer=0;const img=show.querySelector('[data-service-img]'),title=show.querySelector('[data-service-title]'),copy=show.querySelector('[data-service-text]');
  show.querySelectorAll('.service-row').forEach(row=>{const activate=()=>{
   show.querySelectorAll('.service-row').forEach(s=>s.classList.remove('active'));row.classList.add('active');
   if(title)title.textContent=row.dataset.title||'';if(copy)copy.textContent=row.dataset.description||'';
   if(img&&row.dataset.image&&img.getAttribute('src')!==row.dataset.image){img.classList.add('changing');clearTimeout(timer);timer=setTimeout(()=>{img.src=row.dataset.image;img.alt=row.dataset.title||'Service image';img.classList.remove('changing');},180);}
  };row.addEventListener('mouseenter',activate);row.addEventListener('focus',activate);});}
 if(!reduced && intensity>0 && window.matchMedia('(pointer: fine)').matches){
  document.querySelectorAll('[data-depth-scene]').forEach(scene=>{
   const layers=[...scene.querySelectorAll('.depth-layer')];
   if(layers.length===0)return;
   const update=e=>{const r=scene.getBoundingClientRect();const dx=((e.clientX-r.left)/r.width-.5);const dy=((e.clientY-r.top)/r.height-.5);
    layers.forEach(layer=>{const depth=Number(layer.dataset.depth||5)*intensity;layer.style.transform=`translate3d(${(dx*depth).toFixed(2)}px,${(dy*depth).toFixed(2)}px,0) rotateY(${(dx*depth*.22).toFixed(2)}deg) rotateX(${(-dy*depth*.18).toFixed(2)}deg)`;});};
   const reset=()=>layers.forEach(layer=>layer.style.transform='');
   scene.addEventListener('pointermove',update,{passive:true});scene.addEventListener('pointerleave',reset);
  });
  document.querySelectorAll('.tilt-card').forEach(card=>{card.addEventListener('pointermove',e=>{const r=card.getBoundingClientRect();const x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;card.style.transform=`perspective(1000px) rotateX(${(-y*5).toFixed(2)}deg) rotateY(${(x*5).toFixed(2)}deg) translateZ(7px)`;});card.addEventListener('pointerleave',()=>card.style.transform='');});
 }
 if(!reduced&&intensity>0&&window.gsap&&window.ScrollTrigger){
  const gsap=window.gsap;gsap.registerPlugin(window.ScrollTrigger);
  gsap.utils.toArray('.reveal').forEach(node=>{
   gsap.from(node,{opacity:0,y:36,duration:1.1*intensity,ease:'power3.out',scrollTrigger:{trigger:node,start:'top 92%',once:true}});
  });
  gsap.utils.toArray('.production-main,.statement-main,.sculpture-main').forEach(item=>{
   gsap.fromTo(item,{filter:'brightness(.85)'},{filter:'brightness(1)',duration:1.5*intensity,scrollTrigger:{trigger:item,start:'top 92%',once:true}});
  });
  const hero=document.querySelector('.hero-back');if(hero)gsap.fromTo(hero,{scale:1.07},{scale:1,duration:3.5*intensity,ease:'power2.out'});
 }
})();
