<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Test Token</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h1>Get Stripe Test Token</h1>
    <form id="payment-form" method="POST" action="{{ route('stripe.store') }}">
        @csrf
        <div id="card-element"></div>
        <button type="submit">Submit Payment</button>
        <div id="card-errors" role="alert"></div>
    </form>

    <script>
        // Initialize Stripe
        const stripe = Stripe('pk_test_51PonCSRo3sAQpLlzywhbHyST4XPeEyKHSpHVcRjqPmB61R2qUiBNde2KUt31U0qf5wJ7vMQV9bX4ZQoZHSlGi57X00Gk0UreNv');
        const elements = stripe.elements();

        // Create an instance of the card Element
        const card = elements.create('card');
        card.mount('#card-element');

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const {error, token} = await stripe.createToken(card);

            if (error) {
                // Display error.message in #card-errors
                document.getElementById('card-errors').textContent = error.message;
            } else {
                // Send the token to your server
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({token: token.id}),
                });

                const result = await response;
               
            }
        });
    </script>
</body>
</html>
