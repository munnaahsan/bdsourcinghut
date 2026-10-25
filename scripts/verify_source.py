#!/usr/bin/env python3
"""Static verification of the distributable source. Not a substitute for php artisan test."""
from pathlib import Path
import json,re,subprocess,sys,xml.etree.ElementTree as ET
root=Path(__file__).resolve().parents[1]
errors=[]
json.loads((root/'composer.json').read_text())
ET.parse(root/'phpunit.xml')
# All PHP (including Blade text) must be parsable by PHP; actual Blade compilation needs Laravel.
php_files=sorted(root.rglob('*.php'))+[root/'artisan']
for f in php_files:
 result=subprocess.run(['php','-l',str(f)],capture_output=True,text=True)
 if result.returncode:errors.append('PHP syntax: '+str(f.relative_to(root))+' '+result.stderr)
views=list((root/'resources/views').rglob('*.blade.php'))
view_names={str(v.relative_to(root/'resources/views'))[:-len('.blade.php')].replace('/','.') for v in views}
for f in [*root.glob('app/**/*.php'),*views]:
 for v in re.findall(r"(?:view|@extends|@include)\(['\"]([\w.]+)['\"]",f.read_text()):
  if v not in view_names:errors.append('Missing view '+v+' in '+str(f.relative_to(root)))
route_src=(root/'routes/web.php').read_text()
route_names=set(re.findall(r"->name\('([\w.]+)'\)",route_src))
admin_names={"admin."+name for name in route_names}
for f in [*root.glob('app/**/*.php'),*views]:
 for route in re.findall(r"route\(['\"]([\w.]+)['\"]",f.read_text()):
  if route not in route_names|admin_names: errors.append('Missing route '+route+' in '+str(f.relative_to(root)))
for file in (root/'public/images').rglob('*.jpg'):
 try:
  payload=file.read_bytes()
  if not (payload.startswith(b'\xff\xd8') and payload.endswith(b'\xff\xd9')):errors.append('Invalid JPG '+str(file))
 except Exception as ex:errors.append('Unreadable JPG '+str(file)+str(ex))
 if not file.with_suffix('.webp').is_file():errors.append('No WebP derivative '+str(file))
for req in ['hero/hero-main.jpg','hero/hero-detail.jpg','hero/hero-garment.jpg','about/about-main.jpg','about/about-detail.jpg','about/about-craft.jpg',
 'services/service-sourcing.jpg','services/service-design-sampling.jpg','services/service-fabric-yarn.jpg','services/service-merchandising.jpg','services/service-compliance.jpg','services/service-production.jpg',
 'collections/collection-shirts.jpg','collections/collection-pants.jpg','collections/collection-tshirts.jpg','collections/collection-polo.jpg','collections/collection-ladies-tops.jpg','collections/collection-dresses.jpg','collections/collection-knitwear.jpg','collections/collection-woven.jpg',
 'production/production-main.jpg','production/production-detail.jpg','production/quality-control.jpg','contact/contact-main.jpg']:
 if not (root/'public/images'/req).is_file():errors.append('Missing required image '+req)
for f in [root/'public/assets/site.js',root/'public/assets/admin.js']:
 out=subprocess.run(['node','--check',str(f)],capture_output=True,text=True)
 if out.returncode:errors.append('Invalid JS '+str(f)+out.stderr)
print('Checked:',len(php_files),'PHP files,',len(views),'Blade views,',len(list((root/'public/images').rglob('*.jpg'))),'JPGs')
print('Source check:', 'PASS (static only; not a Laravel runtime test)' if not errors else 'FAILED')
for error in errors:print('-',error)
sys.exit(bool(errors))
