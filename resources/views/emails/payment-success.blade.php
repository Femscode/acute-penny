<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('mail.payment_success_subject', ['group_name' => $group->name], $language) }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #094168; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .success-badge { background-color: #28a745; color: white; padding: 10px 20px; border-radius: 25px; display: inline-block; margin: 15px 0; }
        .details-box { background-color: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #28a745; }
        .details-box ul { list-style: none; padding: 0; }
        .details-box li { padding: 8px 0; border-bottom: 1px solid #eee; }
        .details-box li:last-child { border-bottom: none; }
        .details-box strong { color: #094168; }
        .button { display: inline-block; padding: 12px 24px; background-color: #FF821A; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('mail.payment_success_title', [], $language) }}</h1>
            <div class="success-badge">✓ {{ __('mail.payment_success_message', ['group_name' => $group->name], $language) }}</div>
        </div>
        <div class="content">
            <h2>{{ __('mail.payment_success_greeting', ['name' => $user->name], $language) }}</h2>
            
            <div class="details-box">
                <h3>{{ __('mail.payment_details', [], $language) }}:</h3>
                <ul>
                    <li><strong>{{ __('mail.group_name', [], $language) }}:</strong> {{ $group->name }}</li>
                    <li><strong>{{ __('mail.amount_paid', [], $language) }}:</strong> ₦{{ number_format($contribution->amount, 2) }}</li>
                    <li><strong>{{ __('mail.payment_method', [], $language) }}:</strong> {{ ucfirst(str_replace('_', ' ', $contribution->payment_method)) }}</li>
                    <li><strong>{{ __('mail.transaction_id', [], $language) }}:</strong> {{ $contribution->transactionId ?? 'N/A' }}</li>
                    <li><strong>{{ __('mail.payment_date', [], $language) }}:</strong> {{ $contribution->updated_at->format('M d, Y H:i') }}</li>
                </ul>
            </div>
            
            <p>{{ __('mail.keep_contributing', [], $language) }}</p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ route('groups.show', $group->uuid) }}" class="button">{{ __('mail.view_group', [], $language) }}</a>
            </p>
            
            <p>{{ __('mail.thanks', [], $language) }}</p>
        </div>
    </div>
</body>
</html>