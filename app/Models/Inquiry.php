<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Inquiry extends Model {
 protected $fillable=['full_name','company_name','email','phone','country','service_interest','product_category','quantity','message','status','internal_notes'];
}
