<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('mail.withdrawal_request_subject', ['group_name' => $group->name], $language) }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #094168; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .info-badge { background-color: #17a2b8; color: white; padding: 10px 20px; border-radius: 25px; display: inline-block; margin: 15px 0; }
        .details-box { background-color: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #17a2b8; }
        .details-box ul { list-style: none; padding: 0; }
        .details-box li { padding: 8px 0; border-bottom: 1px solid #eee; }
        .details-box li:last-child { border-bottom: none; }
        .details-box strong { color: #094168; }
        .highlight { background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 15px 0; }
        .button { display: inline-block; padding: 12px 24px; background-color: #FF821A; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('mail.withdrawal_request_title', [], $language) }}</h1>
            <div class="info-badge">{{ __('mail.withdrawal_request_message', ['group_name' => $group->name], $language) }}</div>
        </div>
        <div class="content">
            <h2>{{ __('mail.withdrawal_request_greeting', ['name' => $user->name], $language) }}</h2>
            
            <div class="details-box">
                <h3>{{ __('mail.withdrawal_details', [], $language) }}:</h3>
                <ul>
                    <li><strong>{{ __('mail.group_name', [], $language) }}:</strong> {{ $group->name }}</li>
                    <li><strong>{{ __('mail.gross_amount', [], $language) }}:</strong> ₦{{ number_format($withdrawalRequest->gross_amount, 2) }}</li>
                    <li><strong>{{ __('mail.service_charge', [], $language) }}:</strong> ₦{{ number_format($withdrawalRequest->service_charge, 2) }}</li>
                    <li><strong>{{ __('mail.net_amount', [], $language) }}:</strong> ₦{{ number_format($withdrawalRequest->net_amount, 2) }}</li>
                </ul>
            </div>
            
            <div class="details-box">
                <h3>{{ __('mail.bank_details', [], $language) }}:</h3>
                <ul>
                    <li><strong>{{ __('mail.bank_name', [], $language) }}:</strong> {{ $withdrawalRequest->bank_name }}</li>
                    <li><strong>{{ __('mail.account_number', [], $language) }}:</strong> {{ $withdrawalRequest->account_number }}</li>
                    <li><strong>{{ __('mail.account_name', [], $language) }}:</strong> {{ $withdrawalRequest->account_name }}</li>
                </ul>
            </div>
            
            <div class="highlight">
                <h3>{{ __('mail.processing_info', [], $language) }}:</h3>
                <p>{{ __('mail.processing_time', [], $language) }}</p>
                <p>{{ __('mail.status_updates', [], $language) }}</p>
            </div>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ route('groups.show', $group->uuid) }}" class="button">{{ __('mail.view_group', [], $language) }}</a>
            </p>
            
            <p>{{ __('mail.contact_support', [], $language) }}</p>
            <p>{{ __('mail.thanks', [], $language) }}</p>
        </div>
    </div>
</body>
</html>