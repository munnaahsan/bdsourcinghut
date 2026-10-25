<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SiteSetting extends Model {
 protected $fillable=['key','value','group_name'];
 public static function valueOf(string $key, ?string $fallback=null): ?string {return static::query()->where('key',$key)->value('value') ?? $fallback;}
}
