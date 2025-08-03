<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instant Payment Modal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #333;
        }
        .modal-content {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
        }
        .modal-header {
            border-bottom: none;
        }
        .modal-body {
            text-align: center;
        }
        .amount-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
        }
        .note {
            font-size: 12px;
            color: red;
        }
        .justify-between {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

<div style="background: rgba(0,0,0,0.53)" class="modal show d-block" tabindex="-1">
    <div class="modal-dialog-centered">
        <div style="background: rgb(247,248,255)" class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title"><span class="text-danger">Instant</span> Payment</h5>
                <a href="/fund-wallet"><button type="button" class="btn-close" aria-label="Close"></button></a>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="card-tab" data-bs-toggle="tab" data-bs-target="#card" type="button" role="tab" aria-selected="false">Card</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="transfer-tab" data-bs-toggle="tab" data-bs-target="#transfer" type="button" role="tab" aria-selected="true">Transfer</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ussd-tab" data-bs-toggle="tab" data-bs-target="#ussd" type="button" role="tab" aria-selected="false">USSD</button>
                    </li>
                </ul>

                <div class="tab-content mt-3">
                    <div class="tab-pane fade" id="card" role="tabpanel" aria-labelledby="card-tab">On Maintenance</div>
                    <div class="tab-pane fade active show" id="transfer" role="tabpanel" aria-labelledby="transfer-tab">
                        <div class="amount-box">
                            <p class="center">Please proceed to transfer <br> the <strong class="text-danger">exact amount</strong> below:</p>
                            <strong>Amount to pay</strong> <h4 class="strong">NGN {{$amount}}</h4>
                            <hr>
                            <p class="justify-between"><strong>Bank:</strong> <span>{{$bank_name}}</span></p>
                            <hr>
                            <p class="justify-between">
                                <strong>Account Number:</strong>
                                <span id="accountNumber" onclick="copyAccountNumber()">{{$virtual_account}}</span>
                            </p>
                            <hr>
                            <p class="justify-between"><strong>Account Name:</strong> {{$account_name}}</p>
                            <hr>
                        </div>
                        <p class="note">This account number expires in 10 minutes.<br>A wrong transfer may take up to 72hrs to be refunded.</p>

                        <a href="/fund-wallet"><button id="transferBtn" class="btn btn-primary w-100">I have made the transfer</button></a>
                    </div>
                    <div class="tab-pane fade" id="ussd" role="tabpanel" aria-labelledby="ussd-tab">On Maintenance</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyAccountNumber() {
        var accountNumber = document.getElementById("accountNumber").innerText;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(accountNumber).then(() => {
                alert("Account number copied!");
            }).catch(err => {
                console.error("Clipboard copy failed:", err);
                fallbackCopy(accountNumber);
            });
        } else {
            fallbackCopy(accountNumber);
        }
    }

    function fallbackCopy(text) {
        var textarea = document.createElement("textarea");
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand("copy");
        document.body.removeChild(textarea);
        alert("Account number copied!");
    }

    
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
