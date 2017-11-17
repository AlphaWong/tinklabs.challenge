<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test of With Draw Account.
     * @return void
     */
    public function testWithDrawAccount()
    {
        $response = $this->makeConnect('/transaction/withdraw?ownerID=1&bankName=HSBC&bankDrawout=50');
        $this->assertEquals(true, $response['status']);
    }

    /**
     * A basic test of Deposit Account.
     * @return void
     */
    public function testDepositAccount()
    {
        $response = $this->makeConnect('/transaction/deposit?ownerID=1&bankName=HSBC&bankDeposit=10050');
        $this->assertEquals(true, $response['status']);
    }

    /**
     * A basic test of Transfer Money to same account Fail.
     * @return void
     */
    public function testTransferMoneyToSameAccountOverLimit()
    {
        $response = $this->makeConnect('/transaction/transfer?ownerID=1&bankName=HSBC&bankDrawout=15000&receiverID=1&receiverbankName=HSB');
        $this->assertEquals(false, $response['status']);
    }

    /**
     * A basic test of Transfer Money to same account success.
     * @return void
     */
    public function testTransferMoneyToSameAccountSuccessCase()
    {
        $response = $this->makeConnect('/transaction/transfer?ownerID=1&bankName=HSBC&bankDrawout=5000&receiverID=1&receiverbankName=HSB');
        $this->assertEquals(true, $response['status']);
    }

    /**
     * A basic test of Transfer Money to different account success.
     * @return void
     */
    public function testTransferMoneyToDiffAccountNoEnoughCase()
    {
        $response = $this->makeConnect('/transaction/transfer?ownerID=1&bankName=HSBC&bankDrawout=7000&receiverID=2&receiverbankName=HSBC');
        $this->assertEquals(false, $response['status']);
    }

    /**
     * A basic test of Transfer Money to different account success.
     * @return void
     */
    public function testTransferMoneyToDiffAccountSuccessCase()
    {
        $response = $this->makeConnect('/transaction/transfer?ownerID=1&bankName=HSBC&bankDrawout=5000&receiverID=2&receiverbankName=HSBC');
        $this->assertEquals(true, $response['status']);
    }

}