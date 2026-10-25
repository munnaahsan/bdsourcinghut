<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Service extends Model {
 protected $fillable=['title','slug','short_description','body','capabilities','steps','hero_image_id','main_image_id','is_featured','is_published','sort_order','seo_title','seo_description'];
 protected function casts(): array {return ['is_published'=>'boolean','is_featured'=>'boolean','capabilities'=>'array','steps'=>'array'];}
 public function heroImage(){return $this->belongsTo(Media::class,'hero_image_id');}
 public function mainImage(){return $this->belongsTo(Media::class,'main_image_id');}
}
