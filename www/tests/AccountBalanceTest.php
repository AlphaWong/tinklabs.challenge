<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccountBalanceTest extends TestCase
{
    /**
     * A basic test of Open Account.
     * @return void
     */
    public function testOpenAccount()
    {
        $response = $this->makeConnect('/account/open?ownerID=1&bankName=SCB&bankDeposit=100');
        $this->assertEquals(true, $response['status']);
    }

    /**
     * A basic test of Close Account reject case.
     * @return void
     */
    public function testCloseAccountRejectCase()
    {
        $response = $this->makeConnect('/account/close?ownerID=1&bankName=SCB');
        $this->assertEquals(false, $response['status']);
    }

    /**
     * A basic test of Withdraw money for close account.
     * @return void
     */
    public function testWithDrawMoneyForCloseAccount()
    {
        $response = $this->makeConnect('/transaction/withdraw?ownerID=1&bankName=SCB&bankDrawout=100');
        $this->assertEquals(true, $response['status']);
    }

    /**
     * A basic test of Close Account success after settle.
     * @return void
     */
    public function testCloseAccountSuccessCase()
    {
        $response = $this->makeConnect('/account/close?ownerID=1&bankName=SCB');
        $this->assertEquals(true, $response['status']);
    }

    /**
     * A basic test of Account Balance.
     * @return void
     */
    public function testCurrentBalance()
    {
        $response = $this->makeConnect('/account/balance?ownerID=1');
        $this->assertEquals(true, $response['status']);
        $this->assertEquals(true, is_array($response['bankInfo']));
    }

}