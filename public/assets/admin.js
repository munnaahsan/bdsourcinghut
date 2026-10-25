(()=>{'use strict';
 const sidebar=document.querySelector('#admin-sidebar');document.querySelector('.admin-burger')?.addEventListener('click',()=>sidebar?.classList.toggle('open'));
 document.addEventListener('keydown',e=>{if(e.key==='Escape')sidebar?.classList.remove('open')});
 document.querySelectorAll('form[data-confirm]').forEach(form=>form.addEventListener('submit',e=>{if(!window.confirm(form.dataset.confirm||'Are you sure?'))e.preventDefault()}));
 const selectPreview=select=>{const box=document.getElementById(select.dataset.mediaPreview);if(!box)return;const selected=select.options[select.selectedIndex];const url=selected?.dataset.img||'';box.replaceChildren();if(url){const img=document.createElement('img');img.src=url;img.alt='Selected image preview';box.append(img)}else box.textContent='No image selected';};
 document.querySelectorAll('select[data-media-preview]').forEach(sel=>{sel.addEventListener('change',()=>selectPreview(sel));selectPreview(sel)});
 document.querySelectorAll('[data-preview-file]').forEach(input=>input.addEventListener('change',()=>{const parent=input.closest('.panel')||input.parentElement;const box=parent.querySelector('.upload-preview');if(!box)return;box.replaceChildren();for(const file of [...input.files].slice(0,15)){if(!file.type.startsWith('image/'))continue;const url=URL.createObjectURL(file);const img=document.createElement('img');img.alt=file.name;img.src=url;img.addEventListener('load',()=>URL.revokeObjectURL(url),{once:true});box.append(img)}}));
 document.querySelectorAll('[data-color-picker]').forEach(picker=>{const text=document.getElementById(picker.dataset.colorPicker);picker.addEventListener('input',()=>{if(text)text.value=picker.value;refreshTheme()});text?.addEventListener('input',()=>{if(/^#[0-9a-f]{6}$/i.test(text.value)){picker.value=text.value;refreshTheme()}})});
 function refreshTheme(){const panel=document.getElementById('theme-preview');if(!panel)return;const color=name=>document.querySelector(`[name="${name}"]`)?.value;panel.style.backgroundColor=color('background')||'#17283b';const small=panel.querySelector('small');const btn=panel.querySelector('.theme-preview-content span');if(small)small.style.color=color('accent');if(btn){btn.style.backgroundColor=color('button');btn.style.color=color('secondary')}}
 document.querySelectorAll('[data-range-output]').forEach(input=>{const v=document.createElement('small');v.style.marginLeft='12px';v.textContent=input.value;input.after(v);input.addEventListener('input',()=>v.textContent=input.value)});
 // Drag sorting changes gallery_order while checkbox changes are preserved.
 document.querySelectorAll('[data-gallery-editor]').forEach(box=>{
  const output=document.querySelector('[data-gallery-order]');const update=()=>{box.querySelectorAll('.gallery-editor-item').forEach(label=>label.classList.toggle('selected',!!label.querySelector('input')?.checked));if(output)output.value=[...box.querySelectorAll('input:checked')].map(input=>input.value).join(',')};
  box.addEventListener('change',update);let moving=null;
  box.querySelectorAll('.gallery-editor-item').forEach(label=>{
   label.addEventListener('dragstart',e=>{moving=label;label.classList.add('dragging');e.dataTransfer.effectAllowed='move'});
   label.addEventListener('dragover',e=>{e.preventDefault();const under=e.target.closest('.gallery-editor-item');if(under&&moving&&moving!==under){const r=under.getBoundingClientRect();box.insertBefore(moving,(e.clientX<r.left+r.width/2)?under:under.nextSibling)}});
   label.addEventListener('dragend',()=>{moving=null;label.classList.remove('dragging');update()});
  });update();
 });
})();
