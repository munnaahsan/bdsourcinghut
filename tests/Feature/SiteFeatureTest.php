<?php
namespace Tests\Feature;
use App\Models\{Inquiry,Page,Service,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class SiteFeatureTest extends TestCase {
 use RefreshDatabase;
 protected function setUp():void{parent::setUp();$this->seed();}
 public function test_every_public_overview_page_is_rendered():void{
  foreach(['/','/about','/services','/collections','/contact','/services/apparel-sourcing','/collections/shirts','/sitemap.xml'] as $path)$this->get($path)->assertOk();
 }
 public function test_unpublished_services_and_pages_return_404():void{
  Service::where('slug','apparel-sourcing')->update(['is_published'=>false]);
  $this->get('/services/apparel-sourcing')->assertNotFound();
  Page::where('slug','about')->update(['is_published'=>false]);
  $this->get('/about')->assertNotFound();
 }
 public function test_contact_form_validates_and_persists_without_exposing_inquiry():void{
  $this->post('/contact',['full_name'=>'A'])->assertSessionHasErrors();
  $payload=['full_name'=>'Test Contact','company_name'=>'Example Brand','email'=>'contact@example.com','phone'=>'+880123456789','country'=>'Bangladesh','service_interest'=>'Apparel Sourcing','product_category'=>'Shirts','quantity'=>'1000 pcs','message'=>'We would like to discuss an apparel sourcing brief.','company_website'=>''];
  $this->post('/contact',$payload)->assertRedirect('/contact')->assertSessionHas('inquiry_success');
  $this->assertDatabaseHas('inquiries',['email'=>'contact@example.com','status'=>'new']);
  $this->get('/admin/inquiries')->assertRedirect('/admin/login');
 }
 public function test_admin_authentication_and_content_editing_are_guarded():void{
  $this->get('/admin')->assertRedirect('/admin/login');
  $user=User::create(['name'=>'Tester','email'=>'tester@example.com','password'=>'a-very-long-test-password','is_admin'=>true]);
  $this->actingAs($user)->get('/admin')->assertOk();
  $this->actingAs($user)->get('/admin/services')->assertOk();
  $service=Service::where('slug','apparel-sourcing')->firstOrFail();
  $this->actingAs($user)->put('/admin/services/'.$service->id,[
   'title'=>'Apparel Sourcing Updated','slug'=>'apparel-sourcing','short_description'=>'Updated by test',
   'body'=>'New text', 'sort_order'=>1,'is_published'=>1,'seo_title'=>'Updated Service','seo_description'=>'New SEO'
  ])->assertRedirect();
  $this->get('/services/apparel-sourcing')->assertSee('Updated by test');
 }
 public function test_non_admin_cannot_see_dashboard():void{
  $viewer=User::create(['name'=>'Viewer','email'=>'viewer@example.com','password'=>'a-very-long-test-password','is_admin'=>false]);
  $this->actingAs($viewer)->get('/admin')->assertForbidden();
 }
 public function test_primary_menu_defaults_to_five():void{
  $this->assertDatabaseCount('menus',2);
  $this->assertSame(5,\App\Models\Menu::where('slug','primary')->firstOrFail()->items()->count());
 }
}
