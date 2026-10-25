<?php
namespace Database\Seeders;
use App\Models\{Collection,Media,Menu,MenuItem,Page,PageSection,ProcessStep,Service,SiteSetting,ThemeSetting};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder {
 public function run():void { DB::transaction(function(){ $this->seedContent(); }); }
 private function seedContent():void {
  $images = [
   'hero-main'=>'hero/hero-main.jpg','hero-detail'=>'hero/hero-detail.jpg','hero-garment'=>'hero/hero-garment.jpg',
   'about-main'=>'about/about-main.jpg','about-detail'=>'about/about-detail.jpg','about-craft'=>'about/about-craft.jpg',
   'service-sourcing'=>'services/service-sourcing.jpg','service-design-sampling'=>'services/service-design-sampling.jpg','service-fabric-yarn'=>'services/service-fabric-yarn.jpg','service-merchandising'=>'services/service-merchandising.jpg','service-compliance'=>'services/service-compliance.jpg','service-production'=>'services/service-production.jpg',
   'collection-shirts'=>'collections/collection-shirts.jpg','collection-pants'=>'collections/collection-pants.jpg','collection-tshirts'=>'collections/collection-tshirts.jpg','collection-polo'=>'collections/collection-polo.jpg','collection-ladies-tops'=>'collections/collection-ladies-tops.jpg','collection-dresses'=>'collections/collection-dresses.jpg','collection-knitwear'=>'collections/collection-knitwear.jpg','collection-woven'=>'collections/collection-woven.jpg',
   'production-main'=>'production/production-main.jpg','production-detail'=>'production/production-detail.jpg','quality-control'=>'production/quality-control.jpg','contact-main'=>'contact/contact-main.jpg',
  ];
  $m=[];
  foreach($images as $key=>$file){ $m[$key]=Media::firstOrCreate(['disk'=>'assets','path'=>'images/'.$file],[
    'file_name'=>basename($file),'category'=>explode('/',$file)[0],'alt_text'=>ucwords(str_replace('-',' ',$key)), 'mime'=>'image/jpeg',
    'width'=>@getimagesize(public_path('images/'.$file))[0]??null,'height'=>@getimagesize(public_path('images/'.$file))[1]??null,'bytes'=>@filesize(public_path('images/'.$file))?:null
  ])->id; }
  $pages=[
   ['home','Home','Editorial apparel sourcing experience','Welcome to BD SOURCING HUT.','hero-main'],
   ['about','About Us','Design ambition. Sourcing precision.','Learn about the approach and capabilities behind our apparel sourcing coordination.','about-main'],
   ['contact','Contact Us','Begin a conversation.','Tell us what you are creating and what support you need.','contact-main'],
   ['services-overview','Services','WHAT WE DO / 01-06','Specialist coordination across the journey from product idea to final delivery.','service-sourcing'],
   ['collections-overview','Collections','THE COLLECTIONS / 01-08','A product universe of silhouettes, textures, possibilities, and considered details.','collection-shirts'],
  ];
  $p=[];
  foreach($pages as [$slug,$title,$eyebrow,$excerpt,$image]) $p[$slug]=Page::firstOrCreate(['slug'=>$slug],[
    'title'=>$title,'eyebrow'=>$eyebrow,'excerpt'=>$excerpt,'body'=>match($slug){'about'=>"BEYOND THE\nEXPECTED.",'contact'=>"LET'S CREATE\nWHAT'S NEXT.",'services-overview'=>"THE ART OF\nMAKING IT HAPPEN.",'collections-overview'=>"DESIGNED TO\nBE WORN.",default=>"WE SOURCE\nWHAT THE WORLD\nWEARS."},'hero_image_id'=>$m[$image],'is_published'=>true,
    'seo_title'=>$title.' | BD SOURCING HUT','seo_description'=>$excerpt
  ]);
  $sections=[
   ['hero','Cinematic opening','WE SOURCE\nWHAT THE WORLD\nWEARS.','Bangladesh / Global Apparel Sourcing','hero-main','hero-garment','START A PROJECT','/contact',0,'hero'],
   ['statement','Positioning','BUILT IN BANGLADESH.\nCONNECTED TO THE WORLD.','Thoughtful apparel sourcing for ambitious collections.','about-detail','about-craft','OUR APPROACH','/about#approach',1,'statement'],
   ['about','Introduction','WHERE CRAFT\nMEETS COMMITMENT.','We bring together product ideas, materials, development and production coordination—with clarity at every stage.','about-main','about-detail','EXPLORE OUR COMPANY','/about',2,'about'],
   ['services','Capabilities','THE ART OF\nMAKING IT HAPPEN.','From first brief to final handoff, discover the services that connect each stage.','service-sourcing','service-design-sampling','VIEW ALL SERVICES','/services',3,'services'],
   ['collections','Product universe','DESIGNED TO\nBE WORN.\nBUILT TO LAST.','Explore the garment categories we can help you develop and source.','collection-shirts','collection-dresses','EXPLORE COLLECTIONS','/collections',4,'collections'],
   ['production','Our process','FROM FIRST IDEA\nTO FINISHED GARMENT.','An intentional workflow from concept through to delivery.','production-main','production-detail','DISCUSS YOUR BRIEF','/contact',5,'production'],
   ['featured','A closer look','AN EYE FOR\nEVERY DETAIL.','Considered materials, silhouettes and finishing details make each collection distinctive.','hero-detail','collection-polo','EXPLORE COLLECTIONS','/collections',6,'featured'],
   ['cta','Invitation','YOUR NEXT COLLECTION\nSTARTS HERE.','Let us discuss your next apparel sourcing or product development project.','contact-main',null,'START A PROJECT','/contact',7,'cta'],
  ];
  foreach($sections as [$key,$eyebrow,$heading,$body,$image,$second,$cta,$url,$order,$layout]) PageSection::firstOrCreate(['page_id'=>$p['home']->id,'section_key'=>$key],[
   'layout'=>$layout,'eyebrow'=>$eyebrow,'heading'=>str_replace('\\n', "\n", $heading),'body'=>$body,'image_id'=>$m[$image],
   'secondary_image_id'=>$second ? $m[$second] : null,'mobile_image_id'=>null,'image_position'=>'center','overlay_opacity'=>1,'cta_label'=>$cta,'cta_url'=>$url,
   'is_visible'=>true,'sort_order'=>$order
  ]);
  $aboutSections=[
   ['story','Our story','A PRACTICAL PARTNER\nFOR CREATIVE IDEAS.','Our work is centered on connecting design intent with responsible sourcing decisions and clear development coordination.','about-main',0],
   ['approach','Our approach','AN APPROACH\nBUILT ON CLARITY.','We start by understanding your brief, establish the right development path, and coordinate details throughout sourcing and production.','about-detail',1],
   ['bangladesh','Our location','WHY BANGLADESH.','Bangladesh has a broad apparel and textile ecosystem, with varied production capabilities and a long history in global garment supply.','production-main',2],
   ['why-us','Our perspective','WHY BD SOURCING HUT.','One connected point of discussion for sourcing, samples, materials, merchandising, technical review, and production coordination.','about-craft',3],
   ['capabilities','What we do','CAPABILITIES THAT\nCONNECT THE DOTS.','Explore how our service areas work together across product development and sourcing.','service-sourcing',4],
   ['workflow','How we work','FROM THE FIRST BRIEF\nTO THE NEXT STEP.','A structured sequence of concept, sourcing, sampling, production coordination, quality and delivery.','quality-control',5],
  ];
  foreach($aboutSections as [$key,$eyebrow,$heading,$body,$image,$order]) PageSection::firstOrCreate(['page_id'=>$p['about']->id,'section_key'=>$key],[
   'layout'=>$order%2===0?'editorial':'editorial-reverse','eyebrow'=>$eyebrow,'heading'=>str_replace('\\n', "\n", $heading),'body'=>$body,'image_id'=>$m[$image],
   'cta_label'=>$key==='capabilities'?'OUR SERVICES':null,'cta_url'=>$key==='capabilities'?'/services':null,'is_visible'=>true,'sort_order'=>$order
  ]);
  $services=[
   ['Apparel Sourcing','apparel-sourcing','Finding suitable apparel supply options for your product brief.','Collaborative research and sourcing coordination around category, design intent, material preferences and production needs.','service-sourcing',['Product brief review','Category sourcing','Supplier coordination']],
   ['Design & Sampling','design-sampling','Bringing early design ideas into tangible samples.','Support for sample coordination, specifications and iterations, helping refine design intent before production.','service-design-sampling',['Design briefing','Development tracking','Sample feedback']],
   ['Fabric & Yarn','fabric-yarn','Matching fabric and yarn options to product requirements.','Material exploration and fabric sourcing coordination informed by quality, feel, construction and application.','service-fabric-yarn',['Fabric exploration','Material specification','Sourcing follow-up']],
   ['Merchandising','merchandising','Keeping product details and production conversations aligned.','Organized merchandising communication to keep requirements, checkpoints and stakeholders informed.','service-merchandising',['Order coordination','Timeline tracking','Technical communication']],
   ['Technical & Compliance','technical-compliance','Aligning product expectations with quality and technical review.','Technical guidance and quality-focused coordination that consider your defined requirements and applicable standards.','service-compliance',['Specification review','Quality checkpoints','Documentation coordination']],
   ['Production Management','production-management','Coordinating progress from approved sample toward delivery.','Visibility and communication throughout production stages, helping keep execution in sync with agreed requirements.','service-production',['Production follow-up','Milestone tracking','Delivery coordination']],
  ];
  foreach($services as $i=>[$title,$slug,$short,$body,$img,$caps]) Service::firstOrCreate(['slug'=>$slug],[
   'title'=>$title,'short_description'=>$short,'body'=>$body,'capabilities'=>$caps,
   'steps'=>[['title'=>'Discover','description'=>'Review needs and expectations'],['title'=>'Develop','description'=>'Coordinate details and next steps'],['title'=>'Deliver','description'=>'Confirm key milestones and follow-up']],
   'hero_image_id'=>$m[$img],'main_image_id'=>$m[$img],'is_published'=>true,'is_featured'=>true,'sort_order'=>$i,'seo_title'=>$title.' | BD SOURCING HUT', 'seo_description'=>$short
  ]);
  $collections=[
   ['Shirts','shirts','Explore modern shirting possibilities, from relaxed silhouettes to tailored essentials.','collection-shirts'],
   ['Pants & Trousers','pants-trousers','Structured and easy-wearing bottoms for varied design directions.','collection-pants'],
   ['T-Shirts','t-shirts','Jersey essentials and modern casualwear silhouettes.','collection-tshirts'],
   ['Polo Shirts','polo-shirts','Refined knit-collar pieces for versatile collections.','collection-polo'],
   ['Ladies Tops','ladies-tops','Contemporary tops with a focus on fabric and fit.','collection-ladies-tops'],
   ['Dresses','dresses','Fluid and structured dress concepts across seasonal directions.','collection-dresses'],
   ['Knitwear','knitwear','Texture-led knit garments for timeless and modern styles.','collection-knitwear'],
   ['Woven Garments','woven-garments','Designed woven pieces with thoughtful structure and detail.','collection-woven'],
  ];
  foreach($collections as $i=>[$title,$slug,$short,$img]) { $model=Collection::firstOrCreate(['slug'=>$slug],[
    'title'=>$title,'short_description'=>$short,'body'=>$short.' Our coordination approach begins with your specification and intended outcome.',
    'main_image_id'=>$m[$img],'is_published'=>true,'is_featured'=>$i===0,'sort_order'=>$i,'seo_title'=>$title.' | BD SOURCING HUT','seo_description'=>$short
   ]);
   if($model->images()->count()===0){foreach(array_slice([$m[$img],$m['hero-detail'],$m['about-craft']],0,3) as $k=>$id) $model->images()->attach($id,['sort_order'=>$k]);}
  }
  foreach(['Concept','Sourcing','Sampling','Production','Quality','Delivery'] as $i=>$title) ProcessStep::firstOrCreate(['title'=>$title],['sort_order'=>$i,'description'=>[
    'We discuss your vision and requirements.','We explore suitable material and apparel options.','We coordinate early development and samples.','We track agreed production milestones.','We review specifications and quality checkpoints.','We coordinate the final steps and communication.'
   ][$i]]);
  $defaults=[
   'site_name'=>['general','BD SOURCING HUT'],'site_tagline'=>['general','Bangladesh / Global Apparel Sourcing'],
   'contact_email'=>['contact',''],'contact_phone'=>['contact',''],'contact_address'=>['contact','Bangladesh'],
   'social_instagram'=>['social',''],'social_linkedin'=>['social',''],'social_facebook'=>['social',''],
   'logo_text'=>['general','BD SOURCING HUT'],'header_cta_label'=>['general','START A PROJECT'],
   'header_cta_url'=>['general','/contact'],'footer_statement'=>['footer','From inspiration to execution. Sourcing with intention.'],
   'footer_copyright'=>['footer','BD SOURCING HUT. All rights reserved.'],
   'default_seo_title'=>['seo','BD SOURCING HUT | Premium Apparel Sourcing from Bangladesh'],
   'default_seo_description'=>['seo','Discover apparel sourcing, garment development, fabric coordination, merchandising and production management from Bangladesh.'],
   'inquiry_success_message'=>['contact','Thank you for reaching out. Your inquiry has been received.'],
   'contact_intro'=>['contact','Tell us about your project, and we will continue the conversation.'],
   'logo_media_id'=>['general',''],'favicon_media_id'=>['general',''],
   'form_name_label'=>['forms','FULL NAME'],'form_company_label'=>['forms','COMPANY NAME'],
   'form_email_label'=>['forms','BUSINESS EMAIL'],'form_phone_label'=>['forms','PHONE NUMBER'],
   'form_country_label'=>['forms','COUNTRY'],'form_service_label'=>['forms','SERVICE OF INTEREST'],
   'form_collection_label'=>['forms','PRODUCT CATEGORY'],'form_quantity_label'=>['forms','ESTIMATED ORDER QUANTITY'],
   'form_message_label'=>['forms','TELL US MORE ABOUT YOUR PROJECT'],'form_send_label'=>['forms','SEND INQUIRY'],
  ];
  foreach($defaults as $key=>[$group,$value]) SiteSetting::firstOrCreate(['key'=>$key],['group_name'=>$group,'value'=>$value]);
  $colors=[
   'primary'=>'#172B68','secondary'=>'#10111A','accent'=>'#D9BC83','background'=>'#10111A',
   'surface'=>'#17202D','light'=>'#F7F2E9','text'=>'#F7F2E9','muted'=>'#C9CED8','button'=>'#D9BC83',
   'heading_font'=>'Cormorant Garamond','body_font'=>'Manrope','radius'=>'12','shadow_intensity'=>'50','animation_intensity'=>'75'
  ];
  foreach($colors as $key=>$value) ThemeSetting::firstOrCreate(['key'=>$key],['value'=>$value]);
  $primary=Menu::firstOrCreate(['slug'=>'primary'],['name'=>'Primary navigation']);
  $primaryData=[
   ['HOME','/',[]],
   ['ABOUT','/about',[['Our Company','/about#story'],['Our Approach','/about#approach'],['Why Bangladesh','/about#bangladesh'],['Why BD Sourcing Hut','/about#why-us'],['Our Capabilities','/about#capabilities']]],
   ['SERVICES','/services',[['Apparel Sourcing','/services/apparel-sourcing'],['Design & Sampling','/services/design-sampling'],['Fabric & Yarn','/services/fabric-yarn'],['Merchandising','/services/merchandising'],['Technical & Compliance','/services/technical-compliance'],['Production Management','/services/production-management']]],
   ['COLLECTIONS','/collections',[['Shirts','/collections/shirts'],['Pants & Trousers','/collections/pants-trousers'],['T-Shirts','/collections/t-shirts'],['Polo Shirts','/collections/polo-shirts'],['Ladies Tops','/collections/ladies-tops'],['Dresses','/collections/dresses'],['Knitwear','/collections/knitwear'],['Woven Garments','/collections/woven-garments']]],
   ['CONTACT','/contact',[]],
  ];
  foreach($primaryData as $i=>[$label,$url,$children]){
   $item=MenuItem::firstOrCreate(['menu_id'=>$primary->id,'parent_id'=>null,'url'=>$url],['label'=>$label,'sort_order'=>$i]);
   foreach($children as $j=>[$child,$dest]) MenuItem::firstOrCreate(['menu_id'=>$primary->id,'parent_id'=>$item->id,'url'=>$dest],['label'=>$child,'sort_order'=>$j]);
  }
  $footer=Menu::firstOrCreate(['slug'=>'footer'],['name'=>'Footer links']);
  foreach([['Our Company','/about'],['Services','/services'],['Collections','/collections'],['Contact','/contact']] as $i=>[$label,$url]) MenuItem::firstOrCreate(['menu_id'=>$footer->id,'parent_id'=>null,'url'=>$url],['label'=>$label,'sort_order'=>$i]);
 }
}
