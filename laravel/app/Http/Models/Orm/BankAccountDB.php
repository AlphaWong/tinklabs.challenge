<?php

namespace App\Http\Models\Orm;

use Illuminate\Support\Facades\DB;

class BankAccountDB
{
    public function bankAccountSelect($request)
    {
        // All "request" parameter valid in middleware
        $bankQuery = DB::table('bankAccount')
                        ->where('ownerAccountID', '=', $request['ownerID'])
                        ->where('isActive', '=', 1);

        if (!empty($request['bankName'])) {
            $bankQuery->where('bankName', '=', $request['bankName']);
        }

        return $bankQuery->get();
    }

    public function bankAccountOpen($request)
    {
        // All "request" parameter valid in middleware
        return DB::table('bankAccount')->insertGetId([
            'ownerAccountID' => $request['ownerID'],
            'bankName' => $request['bankName'],
            'bankAmount' => $request['bankDeposit'],
            'bankRemain' => $request['bankDeposit'],
            'createTimeStamp' => time(),
            'createTime' => date('Y-m-d H:i:s')
        ]);
    }

    public function bankAccountClose($request)
    {
        // All "request" parameter valid in middleware
        // Log row data for safety update
        return DB::table('bankAccount')
                ->where('ownerAccountID', '=', $request['ownerID'])
                ->where('bankName', '=', $request['bankName'])
                ->lockForUpdate()
                ->update(['isActive' => 0]);
    }

    public function bankAccountDeposit($request)
    {
        // All "request" parameter valid in middleware
        // Log row data for safety update
        return DB::table('bankAccount')
                ->where('ownerAccountID', '=', $request['ownerID'])
                ->where('bankName', '=', $request['bankName'])
                ->where('isActive', '=', 1)
                ->lockForUpdate()
                ->update([
                    'bankAmount' => DB::raw('bankAmount +' . $request['bankDeposit']),
                    'bankRemain' => DB::raw('bankRemain +' . $request['bankDeposit'])
                ]);
    }

    public function bankAccountWithDraw($request)
    {
        // All "request" parameter valid in middleware
        // Log row data for safety update
        return DB::table('bankAccount')
                ->where('ownerAccountID', '=', $request['ownerID'])
                ->where('bankName', '=', $request['bankName'])
                ->where('isActive', '=', 1)
                ->lockForUpdate()
                ->update([
                    'bankRemain' => DB::raw('bankRemain -' . $request['bankDrawout']),
                    'bankDrawout' => DB::raw('bankDrawout +' . $request['bankDrawout'])
                ]);
    }
}
