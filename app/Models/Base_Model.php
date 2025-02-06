<?php
/*--------------------------------------------------------------------
Universal Model that contains all possible universal queries
--------------------------------------------------------------------*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Base_Model extends Model {
    use HasFactory;
    
    //We use insert_record because it may create a conflict with insert function on eloquent
    public static function insert_record($data){
        $query = self::insert($data);
        if($query){ return true; }
        return false;
    }

    public static function get_one_by_where($where){
        $query = self::where($where)->first();
        if($query){ return $query; }
        return false;
    }

    public static function get_all_by_where($where){
        $query = self::where($where)->get();
        if($query){ return $query; }
        return false;
    }
}