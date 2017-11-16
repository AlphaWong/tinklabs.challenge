<?php

namespace App\Http\Models\Orm;

use Illuminate\Support\Facades\DB;

class TransactionDB
{
    public function insertTransaction($request)
    {
        // All "request" parameter will pass by internal
        return DB::table('paymentTransaction')->insertGetId($request);
    }

    public function insertMultiTransaction($request)
    {
        // All "request" parameter will pass by internal
        return DB::table('paymentTransaction')->insert($request);
    }

    public function accountDailyTransferTotal($request)
    {
        // All "request" parameter will pass by internal
        $today = date('Y-m-d');
        return DB::table('paymentTransaction')
                        ->select(DB::raw("SUM(`transactionAmount`) as total"))
                        ->where('bankAccountID', '=', $request['bankAccountID'])
                        ->where('transactionTypeKey', '=', 'TRANSFER_AMOUNT')
                        ->whereBetween('createTime', [$today.' 00:00:00', $today.'23:59:59'])
                        ->groupBy('bankAccountID')
                        ->get();
    }

    public function selectTransactionRecord($request)
    {
        // All "request" parameter will pass by internal
        return DB::table('paymentTransaction')
                        ->where('bankAccountID', '=', $request['bankAccountID'])
                        ->get();
    }
}
