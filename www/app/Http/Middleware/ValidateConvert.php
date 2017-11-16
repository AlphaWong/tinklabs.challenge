<?php

namespace App\Http\Middleware;

use Closure;

class ValidateConvert
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!empty($arguments = $request->route()->parameters())) {
            if (!empty($arguments['controller']) && !empty($arguments['action'])) {
                $checklistArr = []; // For store incorrect parameter

                if (strtolower($arguments['controller']) == 'account' ||
                    strtolower($arguments['controller']) == 'transaction') {
                    $banklist = ['HSBC','HSB','SCB','CIT','BOC','BEA','ICB','DBS','DSB','COM','CCB','WHB','CYB','LCH','NYC','FBB','PUB','WLB'];

                    if (empty($request['ownerID']) || !is_numeric($request['ownerID'])) {
                        $checklistArr[] = 'KEY: `ownerID` incorrect. Now only available 1 or 2 for user 1 & user 2';
                    }

                    if (strtolower($arguments['action']) != 'balance') {
                        if (empty($request['bankName']) || !in_array($request['bankName'], $banklist)) {
                            $checklistArr[] = 'KEY: `bankName` not in the list [HSBC, HSB, SCB, CIT, BOC, BEA]';
                        }
                    }

                    if (strtolower($arguments['action']) == 'open' ||
                        strtolower($arguments['action']) == 'deposit') {
                        if (empty($request['bankDeposit']) || !is_numeric($request['bankDeposit'])) {
                            $checklistArr[] = 'KEY: `bankDeposit` should be needed for open account and money deposit';
                        }
                    }

                    if (strtolower($arguments['action']) == 'bankDrawout' ||
                        strtolower($arguments['action']) == 'transfer') {
                        if (empty($request['bankDrawout']) || !is_numeric($request['bankDrawout'])) {
                            $checklistArr[] = 'KEY: `bankDrawout` should be needed for withDraw account or transfer money';
                        }
                    }

                    if (strtolower($arguments['action']) == 'transfer') {
                        if (empty($request['receiverID']) || !is_numeric($request['receiverID'])) {
                            $checklistArr[] = 'KEY: `receiverID` incorrect. Now only available 1 or 2 for user 1 & user 2';
                        }

                        if (empty($request['receiverbankName']) || !in_array($request['receiverbankName'], $banklist)) {
                            $checklistArr[] = 'KEY: `receiverbankName` not in the list [HSBC, HSB, SCB, CIT, BOC, BEA]';
                        }
                    }
                }

                if (count($checklistArr) > 0) {
                    return response()->json([
                            'status' => false,
                            'message' => 'Request parameters incorrect, please follow below key and this challenge available $_GET parameters for testing. E.g. http://localhost:8080/'.$arguments['controller'].'/'.$arguments['action'].'?ownerID=1',
                            'checklist' => $checklistArr
                        ], 422); // Unprocessable Entity
                }

                return $next($request);
            }
        }

        abort(404);
    }
}