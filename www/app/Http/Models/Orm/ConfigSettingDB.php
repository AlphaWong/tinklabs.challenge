<?php

namespace App\Http\Models\Orm;

use Illuminate\Support\Facades\DB;

class ConfigSettingDB
{
    public function configSelect()
    {
        return DB::table('configSetting')
                ->get();
    }
}