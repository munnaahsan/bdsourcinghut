<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PageSection extends Model {
 protected $fillable=['page_id','section_key','layout','eyebrow','heading','body','cta_label','cta_url','background_color','image_id','secondary_image_id','mobile_image_id','image_position','overlay_opacity','extra','is_visible','sort_order'];
 protected function casts(): array{return ['is_visible'=>'boolean','extra'=>'array','overlay_opacity'=>'float'];}
 public function page(){return $this->belongsTo(Page::class);}
 public function image(){return $this->belongsTo(Media::class,'image_id');}
 public function mobileImage(){return $this->belongsTo(Media::class,'mobile_image_id');}
 public function secondaryImage(){return $this->belongsTo(Media::class,'secondary_image_id');}
}
