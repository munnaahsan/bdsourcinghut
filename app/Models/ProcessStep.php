<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProcessStep extends Model {
 protected $fillable=['title','description','sort_order','is_visible'];
 protected function casts(): array{return ['is_visible'=>'boolean'];}
}
