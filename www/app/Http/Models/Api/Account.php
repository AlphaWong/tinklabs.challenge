<?php

namespace App\Http\Models\Api;

use Illuminate\Support\Facades\DB;

class Account
{
    /*
     * Open Bank account
     * @PARAM: [ownerID, bankName, bankDeposit]
     * @RETURN: ARRAY
     */
    public function modelOpen($request)
    {
        // Begin Transaction for rollback, if have a problem
        DB::beginTransaction();
        /*
         * (1) Check Owner Exist, if not, throw error
         * (2) Check bank account duplicate on same Owner and same bank
         * (3) Insert New bank account to database and return bankID
         * (4) Insert record to transaction table for cross check
         */
        try {
            // (1)
            if (empty($ownerInfo = (new \App\Http\Models\Orm\OwnerDB)->ownerSelect($request))) {
                throw new Exception('Owner not exist');
            }

            // (2)
            $bankAccountObj = new \App\Http\Models\Orm\BankAccountDB;
            if (!empty($bankInfo = $bankAccountObj->bankAccountSelect([
                'ownerID' => $request['ownerID'],
                'bankName' => $request['bankName']
            ]))) {
                throw new \Exception('Duplicate bank account, you can use `get current balance` to check which account you already have.');
            }

            // (3)
            $newbankID = $bankAccountObj->bankAccountOpen($request);

            // (4)
            (new \App\Http\Models\Orm\TransactionDB)->insertTransaction([
                'transactionTypeKey' => 'TOPUP',
                'transactionRef' => time() . $newbankID,
                'bankAccountID' => $newbankID,
                'transactionAmount' => $request['bankDeposit'],
                'createTimeStamp' => time(),
                'createTime' => date('Y-m-d H:i:s')
            ]);
            DB::commit();
            return [
                "status" => true,
                "accountID" => $newbankID
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                "status" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /*
     * Close Bank account
     * @PARAM: [ownerID, bankName]
     * @RETURN: ARRAY
     */
    public function modelClose($request)
    {
        // Begin Transaction for rollback, if have a problem
        DB::beginTransaction();
        /*
         * (1) Check Owner Exist, if not, throw error
         * (2) Check bank account is under the same owner, and consider account remain
         * (3) update bank account active to 0 in database
         */
        try {
            // (1)
            if (empty($ownerInfo = (new \App\Http\Models\Orm\OwnerDB)->ownerSelect($request))) {
                throw new \Exception('Owner not exist');
            }

            // (2)
            $bankAccountObj = new \App\Http\Models\Orm\BankAccountDB;
            if (empty($bankInfo = $bankAccountObj->bankAccountSelect([
                'ownerID' => $request['ownerID'],
                'bankName' => $request['bankName']
            ]))) {
                throw new \Exception('Bank account not under this owner');
            }
            if (!empty($bankInfo[0]) && $bankInfo[0]->bankRemain != 0) {
                throw new \Exception('Please settle this account('.$bankInfo[0]->bankRemain.') by withdraw money or deposit money, you also can use `get current balance` to check this account status.');
            }

            // (3)
            $bankAccountObj->bankAccountClose($request);
            DB::commit();
            return [
                "status" => true,
                "message" => "Account Closed"
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                "status" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /*
     * Owner Bank balance
     * @PARAM: [ownerID]
     * @RETURN: ARRAY
     */
    public function modelBalance($request)
    {
        /*
         * (1) Check Owner Exist, if not, throw error
         * (2) Select all account to display this owner
         */
        try {
            // (1)
            if (empty($ownerInfo = (new \App\Http\Models\Orm\OwnerDB)->ownerSelect($request))) {
                throw new \Exception('Owner not exist');
            }

            // (2)
            if (empty($bankInfo = (new \App\Http\Models\Orm\BankAccountDB)->bankAccountSelect([
                'ownerID' => $request['ownerID']
            ]))) {
                throw new \Exception('No Bank account under this owner');
            }
            return [
                "status" => true,
                "bankInfo" => $bankInfo
            ];
        } catch (\Exception $e) {
            return [
                "status" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}