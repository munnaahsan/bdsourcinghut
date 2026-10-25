<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Page extends Model {
 protected $fillable=['title','slug','eyebrow','excerpt','body','hero_image_id','seo_title','seo_description','is_published','sort_order'];
 protected function casts(): array{return ['is_published'=>'boolean'];}
 public function heroImage(){return $this->belongsTo(Media::class,'hero_image_id');}
 public function sections():HasMany{return $this->hasMany(PageSection::class)->orderBy('sort_order');}
 public function visibleSections():HasMany{return $this->sections()->where('is_visible',true);}
}
