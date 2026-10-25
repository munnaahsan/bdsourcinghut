<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Collection extends Model {
 protected $fillable=['title','slug','short_description','body','fabric_information','fit_information','production_information','main_image_id','is_featured','is_published','sort_order','seo_title','seo_description'];
 protected function casts(): array {return ['is_published'=>'boolean','is_featured'=>'boolean'];}
 public function mainImage(){return $this->belongsTo(Media::class,'main_image_id');}
 public function images(){return $this->belongsToMany(Media::class,'collection_images')->withPivot('sort_order')->orderByPivot('sort_order');}
}
