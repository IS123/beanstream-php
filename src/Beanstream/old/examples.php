<pre><?php

// BEANSTREAM REST API SDK USAGE EXAMPLES

// Get Beanstream Gateway
require_once 'Gateway.php';

// Init api settings (beanstream dashboard > administration > account settings > order settings)
$merchant_id = ''; // INSERT MERCHANT ID (must be a 9 digit string)
$api_key = ''; // INSERT API ACCESS PASSCODE
$api_version = 'v1'; // Default
$platform = 'api'; // Default (or use 'tls12-api' for the TLS 1.2-Only endpoint)

// Generate a random order number, and set a default $amount (only used for example functions)
$order_number = bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
$amount = 1.00;

// Init new Beanstream Gateway object
$beanstream = new \Beanstream\Gateway($merchant_id, $api_key, $platform, $api_version);

// Example array data for use in example functions

// Example payment transaction data
$payment_data = [
    'order_number' => $order_number,
    'amount' => $amount,
    'payment_method' => 'card',
    'card' => [
        'name' => 'Mr. Card Testerson',
        'number' => '4030000010001234',
        'expiry_month' => '07',
        'expiry_year' => '22',
        'cvd' => '123'
    ],
    'billing' => [
        'name' => 'Mr. John Doe',
        'email_address' => 'johndoe@email.com',
        'phone_number' => '1234567890',
        'address_line1' => 'Main St.',
        'city' => 'Anytown',
        'province' => 'BC',
        'postal_code' => 'V8J9I5',
        'country' => 'CA'
    ],
    'shipping' => [
        'name' => 'Shipping Name',
        'email_address' => 'email@email.com',
        'phone_number' => '1234567890',
        'address_line1' => '789-123 Shipping St.',
        'city' => 'Shippingsville',
        'province' => 'BC',
        'postal_code' => 'V8J9I5',
        'country' => 'CA'
    ]
];

// Example profile function test vars
$profile_id = ''; // Enter a profile_id to get a profile
$card_id = '1'; // Default card, 1-based index

// Example profile data to create
$profile_data = [
    'billing' => [
        'name' => 'Profile Billing Name',
        'email_address' => 'email@email.com',
        'phone_number' => '1234567890',
        'address_line1' => '456-123 Shipping St.',
        'city' => 'Shippingville',
        'province' => 'BC',
        'postal_code' => 'V8J9I5',
        'country' => 'CA'
    ]
];

// Example card data to add to a profile
$card_data = [
    'card' => [
        'name' => 'Test Testerson',
        'number' => '4030000010001234',
        'expiry_month' => '07',
        'expiry_year' => '22',
        'cvd' => '123'
    ]
];

// Example unreferenced return data
$return_data = [
    'order_number' => $order_number,
    'amount' => $amount,
    'payment_method' => 'card',
    'card' => [
        'name' => 'Mr. Refund Testerson',
        'number' => '4030000010001234',
        'expiry_month' => '07',
        'expiry_year' => '22',
        'cvd' => '123'
    ]
];

// Example profile payment data
$profile_payment_data = [
    'order_number' => $order_number,
    'amount' => $amount
];


// Example data to simulate getting a legato token
$legato_token_data = [
    'number' => '4030000010001234',
    'expiry_month' => '07',
    'expiry_year' => '22',
    'cvd' => '123'
];

// Example legato payment data
// Name is actually insterted into ['token']['name']
$legato_payment_data = [
    'order_number' => $order_number,
    'amount' => $amount,
    'name' => 'Mrs. Legato Testerson'
];

// Example search criteria data
$search_criteria = [
    'name' => 'TransHistoryMinimal', // Or 'Search',
    'start_date' => '1999-01-01T00:00:00',
    'end_date' => '2016-01-01T23:59:59',
    'start_row' => '1',
    'end_row' => '15000',
    'criteria' => [
        'field' => '1',
        'operator' => '%3E',
        'value' => '1000000'
    ]
];

// Example payment function test vars
$transaction_id = ''; // Enter a transaction id to use in below functions
$complete = true;

// REQUEST EXAMPLE FUNCTIONS BELOW
// UNCOMMENT THE ONES YOU WOULD LIKE TO TEST
try {
    // **** PAYMENTS EXAMPLES

    // Make a credit card payment
    // $result = $beanstream->payments()->makeCardPayment($payment_data, $complete);
    // $transaction_id = $result['id'];

    // Complete a PA
    // $result = $beanstream->payments()->complete($transaction_id, $amount, $order_number);

    // Cash payment
    // $result = $beanstream->payments()->makeCashPayment($payment_data);

    // Cheque payment
    // $result = $beanstream->payments()->makeChequePayment($payment_data);

    // Return a payment
    // $result = $beanstream->payments()->returnPayment($transaction_id, $amount, $order_number);

    // Return a payment (unreferenced)
    // $result = $beanstream->payments()->unreferencedReturn($return_data);

    // Void a payment
    // $result = $beanstream->payments()->voidPayment($transaction_id, $amount);

    // Simulate legato token payment (SHOULD NEVER BE CALLED IN PRODUCTION)
    // $token = $beanstream->payments()->getTokenTest($legato_token_data);

    // Make legato payment with above token
    // $result = $beanstream->payments()->makeLegatoTokenPayment($token, $legato_payment_data, $complete);

    // **** PROFILES EXAMPLES

    // Create a profile
    // $profile_id = $beanstream->profiles()->createProfile($profile_data);

    // Get a profile based on a profile cust code
    // $result = $beanstream->profiles()->getProfile($profile_id);

    // Update a profile based on a profile cust code
    // $result = $beanstream->profiles()->updateProfile($profile_id, $profile_data);

    // Delete a profile
    // $result = $beanstream->profiles()->deleteProfile($profile_id);

    // Add a card to a profile
    // $result = $beanstream->profiles()->addCard($profile_id, $card_data);

    // Profile payment
    // $result = $beanstream->payments()->makeProfilePayment($profile_id, $card_id, $profile_payment_data, $complete);
    // $transaction_id = $result['id'];

    // Complete a profile payment
    // $result = $beanstream->payments()->complete($transaction_id, $profile_payment_data['amount'], $order_number);


    // Get all cards in profile
    // $result = $beanstream->profiles()->getCards($profile_id);

    // Update a specfic card in a profile
    // $result = $beanstream->profiles()->updateCard($profile_id, $card_id, $card_data);

    // Delete a specfic card in a profile
    // $result = $beanstream->profiles()->deleteCard($profile_id, $card_id);

    // **** REPORTING EXAMPLES

    // Search for transactions that match criteria // DOESN'T RETURN ALL TX (ie. VP/VR)?
    // $result = $beanstream->reporting()->getTransactions($search_criteria);

    // Get a specific transaction
    // $result = $beanstream->reporting()->getTransaction($transaction_id);

    // Display result
    is_null($result) ?: print_r($result);
} catch (\Beanstream\Exception $e) {
    /*
     * Handle transaction error, $e->code can be checked for a
     * specific error, e.g. 211 corresponds to transaction being
     * DECLINED, 314 - to missing or invalid payment information
     * etc.
     */
    print_r($e);
}
?></pre>