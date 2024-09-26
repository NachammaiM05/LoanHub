<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Transactions | LoanHub</title>

    <!-- Include SB Admin 2 styles -->
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #f8f9fc;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4e73df;
        }
        input[type="text"], button {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #d1d3e2;
            border-radius: 5px;
        }
        button {
            background-color: #4e73df;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #2e59d9;
        }
        #payment-result {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Begin Page Content -->
        <div class="container">
            <h2 class="text-center text-primary mb-4"><i class="fas fa-credit-card"></i> Make a Payment</h2>

            <form id="payment-form" action="process_payment.php" method="POST">
                <label for="amount">Amount:</label>
                <input type="text" id="amount" name="amount" required>

                <label for="card_number">Card Number:</label>
                <div id="card-number-element"></div>

                <label for="expiry_date">Expiry Date:</label>
                <div id="expiry-date-element"></div>

                <label for="cvv">CVV:</label>
                <div id="cvv-element"></div>

                <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-check-circle"></i> Pay Now</button>
                <div id="payment-result" class="text-center text-danger mt-3"></div>
            </form>
        </div>
        <!-- End of Page Content -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- SB Admin 2 Scripts -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sb-admin-2.min.js"></script>

    <script src="payment.js"></script>
    <script>
        // Set up Stripe.js and Elements for secure checkout
        var stripe = Stripe('pk_test_your_stripe_publishable_key');
        var elements = stripe.elements();

        // Create an instance of the card Element
        var card = elements.create('card');
        card.mount('#card-number-element');

        var expiryDate = elements.create('cardExpiry');
        expiryDate.mount('#expiry-date-element');

        var cvv = elements.create('cardCvc');
        cvv.mount('#cvv-element');

        // Handle form submission
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            stripe.createPaymentMethod({
                type: 'card',
                card: card,
            }).then(function(result) {
                if (result.error) {
                    // Show error in payment result
                    document.getElementById('payment-result').textContent = result.error.message;
                } else {
                    // Process payment through backend
                    fetch('process_payment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ amount: form.amount.value }),
                    }).then(function(response) {
                        return response.json();
                    }).then(function(data) {
                        // Confirm the payment
                        stripe.confirmCardPayment(data.client_secret, {
                            payment_method: result.paymentMethod.id,
                        }).then(function(confirmResult) {
                            if (confirmResult.error) {
                                // Show error message
                                document.getElementById('payment-result').textContent = confirmResult.error.message;
                            } else {
                                // Payment successful
                                document.getElementById('payment-result').textContent = 'Payment successful!';
                            }
                        });
                    });
                }
            });
        });
    </script>

</body>
</html>
