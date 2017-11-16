<?php

namespace App\Http\Models\Api;

use Illuminate\Support\Facades\DB;

class Transaction
{
    /*
     * Check Transaction
     * @PARAM: [ownerID, bankName]
     * @RETURN: ARRAY
     */
    public function modelRecord($request)
    {
        /*
         * (1) Check Owner Exist, if not, throw error
         * (2) Check bank account Exist, if not throw error
         * (3) Select Bank account Transaction
         */
        try {
            // (1)
            if (empty($ownerInfo = (new \App\Http\Models\Orm\OwnerDB)->ownerSelect($request))) {
                throw new Exception('Owner not exist');
            }

            // (2)
            if (empty($bankInfo = (new \App\Http\Models\Orm\BankAccountDB)->bankAccountSelect([
                'ownerID' => $request['ownerID'],
                'bankName' => $request['bankName']
            ]))) {
                throw new \Exception('Bank account not under this owner, you can use `get current balance` to check your account.');
            }

            // (3)
            $records = (new \App\Http\Models\Orm\TransactionDB)->selectTransactionRecord(['bankAccountID' => $bankInfo[0]->id]);

            return [
                "status" => true,
                "record" => $records
            ];
        } catch (\Exception $e) {
            return [
                "status" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /*
     * Depost in bank account
     * @PARAM: [ownerID, bankName, bankDeposit]
     * @RETURN: ARRAY
     */
    public function modelDeposit($request)
    {
        // Begin Transaction for rollback, if have a problem
        DB::beginTransaction();
        /*
         * (1) Check Owner Exist, if not, throw error
         * (2) Check bank account Exist, if not throw error
         * (3) Update bank account amount and remain
         * (4) Insert record to transaction table for cross check
         */
        try {
            // (1)
            if (empty($ownerInfo = (new \App\Http\Models\Orm\OwnerDB)->ownerSelect($request))) {
                throw new Exception('Owner not exist');
            }

            // (2)
            $bankAccountObj = new \App\Http\Models\Orm\BankAccountDB;
            if (empty($bankInfo = $bankAccountObj->bankAccountSelect([
                'ownerID' => $request['ownerID'],
                'bankName' => $request['bankName']
            ]))) {
                throw new \Exception('Bank account not under this owner, you can use `get current balance` to check your account.');
            }

            // (3)
            $bankAccountObj->bankAccountDeposit($request);

            // (4)
            (new \App\Http\Models\Orm\TransactionDB)->insertTransaction([
                'transactionTypeKey' => 'TOPUP',
                'transactionRef' => time() . $bankInfo[0]->id,
                'bankAccountID' => $bankInfo[0]->id,
                'transactionAmount' => $request['bankDeposit'],
                'createTimeStamp' => time(),
                'createTime' => date('Y-m-d H:i:s')
            ]);
            DB::commit();
            return [
                "status" => true,
                "message" => 'success topup'
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
     * Withdraw in bank account
     * @PARAM: [ownerID, bankName, bankDrawout]
     * @RETURN: ARRAY
     */
    public function modelWithdraw($request)
    {
        // Begin Transaction for rollback, if have a problem
        DB::beginTransaction();
        /*
         * (1) Check Owner Exist, if not, throw error
         * (2) Check bank account Exist, if not throw error
         * (3) Check remain enough for drawout
         * (4) Update bank account remain and drawout
         * (5) Insert record to transaction table for cross check
         */
        try {
            // (1)
            if (empty($ownerInfo = (new \App\Http\Models\Orm\OwnerDB)->ownerSelect($request))) {
                throw new Exception('Owner not exist');
            }

            // (2)
            $bankAccountObj = new \App\Http\Models\Orm\BankAccountDB;
            if (empty($bankInfo = $bankAccountObj->bankAccountSelect([
                'ownerID' => $request['ownerID'],
                'bankName' => $request['bankName']
            ]))) {
                throw new \Exception('Bank account not under this owner, you can use `get current balance` to check your account.');
            }

            // (3)
            if ($bankInfo[0]->bankRemain < $request['bankDrawout']) {
                throw new \Exception('This account remain not enough for you to drawout.');
            }

            // (4)
            $bankAccountObj->bankAccountWithDraw($request);

            // (5)
            (new \App\Http\Models\Orm\TransactionDB)->insertTransaction([
                'transactionTypeKey' => 'DRAWOUT',
                'transactionRef' => time() . $bankInfo[0]->id,
                'bankAccountID' => $bankInfo[0]->id,
                'transactionAmount' => $request['bankDrawout'],
                'createTimeStamp' => time(),
                'createTime' => date('Y-m-d H:i:s')
            ]);
            DB::commit();
            return [
                "status" => true,
                "message" => 'success drawout'
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
     * Transfer money from 1 bank account to another bank account
     * @PARAM: [ownerID, bankName, bankDrawout, receiverID, receiverbankName]
     * @RETURN: ARRAY
     */
    public function modelTransfer($request)
    {
        // Begin Transaction for rollback, if have a problem
        DB::beginTransaction();
        /*
         * (1) Check Owner & Receiver is same
         * (2) Check Owner bank account is same under the same owner
         * (3) Make approve by http://handy.travel/test/success.json
         * (4) Setup MIN Charge & MAX Transfer per account
         * (5) Check Owner Exist, if not, throw error
         * (6) Check Receiver Exist, if not, throw error
         * (7) Check Owner bank Account Exist, if not, throw error
         * (8) Check Owner bank Account remain enough for transfer
         * (9) Check Owner bank Account transfer record is out of limit or not
         * (10) Check Receiver bank Account Exist, if not, throw error
         * (11) Update bank account of owner & receiver
         * (12) Insert record to transaction table for cross check
         */
        try {
            $ownerObj = new \App\Http\Models\Orm\OwnerDB;
            $bankAccountObj = new \App\Http\Models\Orm\BankAccountDB;
            $transactionObj = new \App\Http\Models\Orm\TransactionDB;

            // (1)
            $sameOwner = ($request['ownerID'] == $request['receiverID'])? true : false;

            // (2)
            if ($sameOwner && $request['bankName'] == $request['receiverbankName']) {
                throw new Exception('Cannot transfer to same account');
            }

            // (3)
            if (!$sameOwner) {
                if (!self::makeApprove()) {
                    throw new Exception('This transfer not approved');
                }
            }

            // (4)
            $configInfo = (new \App\Http\Models\Orm\ConfigSettingDB)->configSelect();
            $maxTransfer = 10000;
            $minCharge = 100;
            foreach ($configInfo as $val) {
                if ($val->configKey == 'MAX_TRANSFER') {
                    $maxTransfer = $val->configAmount;
                }
                if ($val->configKey == 'MIN_CHARGE') {
                    $minCharge = $val->configAmount;
                }
            }

            // (5)
            if (empty($ownerInfo = $ownerObj->ownerSelect($request))) {
                throw new Exception('Owner not exist');
            }

            // (6)
            if (!$sameOwner) {
                if (empty($receiverInfo = $ownerObj->ownerSelect(['ownerID' => $request['receiverID']]))) {
                    throw new Exception('Receiver not exist');
                }
            } else {
                $receiverInfo = $ownerInfo;
            }

            // (7)
            if (empty($bankInfo = $bankAccountObj->bankAccountSelect([
                'ownerID' => $request['ownerID'],
                'bankName' => $request['bankName']
            ]))) {
                throw new \Exception('Bank account not under this owner, you can use `get current balance` to check your account.');
            }

            // (8)
            $bankDrawoutWithCharge = (!$sameOwner)? $request['bankDrawout'] + $minCharge : $request['bankDrawout'];
            if ($bankInfo[0]->bankRemain < $bankDrawoutWithCharge) {
                throw new \Exception('This account remain not enough for you to transfer.');
            }

            // (9)
            $transactionInfo = $transactionObj->accountDailyTransferTotal(['bankAccountID' => $bankInfo[0]->id]);
            $total = (!empty($transactionInfo[0]) && isset($transactionInfo[0]->total))? $transactionInfo[0]->total : 0;
            if (($total + $request['bankDrawout']) > $maxTransfer) {
                throw new \Exception('Daily transfer limit of $'.$maxTransfer.' per account.');
            }

            // (10)
            if (empty($receiverBankInfo = $bankAccountObj->bankAccountSelect([
                'ownerID' => $request['receiverID'],
                'bankName' => $request['receiverbankName']
            ]))) {
                throw new \Exception('Bank account not under this receiver, please confirm your account.');
            }

            // (11)
            // Drawout owner for transfer
            $bankAccountObj->bankAccountWithDraw([
                'ownerID' => $request['ownerID'],
                'bankName' => $request['bankName'],
                'bankDrawout' => $bankDrawoutWithCharge
            ]);
            // Deposit receiver for transfer
            $bankAccountObj->bankAccountDeposit([
                'ownerID' => $request['receiverID'],
                'bankName' => $request['receiverbankName'],
                'bankDeposit' => $request['bankDrawout']
            ]);

            // (12)
            $transactionRef = time() . $bankInfo[0]->id;
            $createTimeStamp = time();
            $createDateTime = date('Y-m-d H:i:s');
            $transferArr = [
                [
                    'transactionTypeKey' => 'DRAWOUT',
                    'transactionRef' => $transactionRef,
                    'bankAccountID' => $bankInfo[0]->id,
                    'transactionAmount' => $request['bankDrawout'],
                    'createTimeStamp' => $createTimeStamp,
                    'createTime' => $createDateTime
                ],
                [
                    'transactionTypeKey' => 'TRANSFER_AMOUNT',
                    'transactionRef' => $transactionRef,
                    'bankAccountID' => $receiverBankInfo[0]->id,
                    'transactionAmount' => $request['bankDrawout'],
                    'createTimeStamp' => $createTimeStamp,
                    'createTime' => $createDateTime
                ]
            ];
            if (!$sameOwner) {
                $transferArr[] = [
                    'transactionTypeKey' => 'TRANSFER_FEE',
                    'transactionRef' => $transactionRef,
                    'bankAccountID' => $bankInfo[0]->id,
                    'transactionAmount' => $minCharge,
                    'createTimeStamp' => $createTimeStamp,
                    'createTime' => $createDateTime
                ];
            }
            (new \App\Http\Models\Orm\TransactionDB)->insertMultiTransaction($transferArr);
            DB::commit();
            return [
                "status" => true,
                "message" => 'success transfer'
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
     * Make approve
     * @PARAM: []
     * @RETURN: Boolean
     */
    private static function makeApprove()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL, 'http://handy.travel/test/success.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $server_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($server_code < 400) {
            $result = (string)$server_output;
            $approveArr = json_decode($result, true);
            if (!empty($approveArr['status']) && strtolower($approveArr['status']) == 'success') {
                return true;
            }
        }

        return false;
    }
}
