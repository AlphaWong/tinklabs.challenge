<?php

namespace App\Http\Models\Orm;

use Illuminate\Support\Facades\DB;

class OwnerDB
{
    public function ownerSelect($request)
    {
        // All "request" parameter valid in middleware
        return DB::table('ownAccount')
                ->where('id', '=', $request['ownerID'])
                ->where('isActive', '=', 1)
                ->get();
    }
}
