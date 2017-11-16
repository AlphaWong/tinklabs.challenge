<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccountBalanceTest extends TestCase
{
    /**
     * A basic test of Account Balance.
     *
     * @return void
     */
    public function testAccountBalance()
    {
        // $this->json('GET', '/account/balance?ownerID=1', ['name' => 'Sally'])
        //      ->seeJsonEquals([
        //          'created' => true,
        //      ])   
        $abc = $this->get('/account/balance?ownerID=1')
        // var_dump($abc);
            // ->assertTrue(true);
            ->seeJsonStructure([
                // 'status',
                'bankInfo' => [
                    // '*' => [
                        'id', 'ownerAccountID', 'bankName', 'bankCurrency', 'bankAmount', 'bankRemain', 'bankDrawout', 'isActive', 'createTimeStamp', 'createTime'
                    // ]
                ]
            ]);
    }
}
