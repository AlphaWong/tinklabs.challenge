<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 48px;
            }

            .listWrapper {
                margin-top: 16px;
            }
            .list {
                color: #333;
                font-size: 18px;
                font-weight: bold;
                text-align: center;
                text-decoration: none;
                padding: 12px 0;
                border-left: 1px solid #999;
                border-top: 1px solid #999;
                border-right: 1px solid #999;
                display: block;
            }
            .list.last {
                border-bottom: 1px solid #999;
            }
            .list:hover {
                color: #FFF;
                background-color: #333;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Tink Labs</div>
            </div>
            <div class="listWrapper">
                <a href="./account/open" class="list">Open Account</a>
                <a href="./account/close" class="list">Close Account</a>
                <a href="./account/balance" class="list">Get Current Balance</a>
                <a href="./transaction/withdraw" class="list">Withdraw money</a>
                <a href="./transaction/deposit" class="list">Deposit money</a>
                <a href="./transaction/transfer" class="list">Transfer money</a>
                <a href="./transaction/record" class="list last">Account Transaction</a>
            </div>
        </div>
    </body>
</html>