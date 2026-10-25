<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class Media extends Model {
 protected $table='media';
 protected $fillable=['file_name','disk','path','category','alt_text','mime','width','height','bytes'];
 protected $appends=['url','webp_url'];
 public function getWebpUrlAttribute(): ?string {
  if($this->disk!=='assets'||!str_ends_with(strtolower($this->path),'.jpg'))return null;
  $path=substr($this->path,0,-4).'.webp';return file_exists(public_path($path))?asset($path):null;
 }
 public function getUrlAttribute(): string {return $this->disk === 'assets' ? asset(ltrim($this->path,'/')) : Storage::disk($this->disk)->url($this->path);}
}
