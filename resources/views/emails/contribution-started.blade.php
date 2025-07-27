<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('mail.contribution_started_subject', ['group_name' => $group->name], $language) }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #094168; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .group-info { background-color: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .turn-info { background-color: #FF821A; color: white; padding: 15px; border-radius: 5px; margin: 15px 0; text-align: center; }
        .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('mail.contribution_started_title', [], $language) }}</h1>
        </div>
        <div class="content">
            <h2>{{ __('mail.contribution_started_greeting', ['name' => $user->name], $language) }}</h2>
            <p>{{ __('mail.contribution_started_message', ['group_name' => $group->name], $language) }}</p>
            
            <div class="group-info">
                <h3>{{ __('mail.group_details', [], $language) }}</h3>
                <p><strong>{{ __('general.group_name', [], $language) }}:</strong> {{ $group->name }}</p>
                <p><strong>{{ __('general.contribution_amount', [], $language) }}:</strong> ₦{{ number_format($group->contribution_amount) }}</p>
                <p><strong>{{ __('general.frequency', [], $language) }}:</strong> {{ __('general.' . $group->frequency, [], $language) }}</p>
                <p><strong>{{ __('general.start_date', [], $language) }}:</strong> {{ $group->start_date->format('M d, Y') }}</p>
            </div>
            
            <div class="turn-info">
                <h3>{{ __('mail.your_turn_info', [], $language) }}</h3>
                <p><strong>{{ __('mail.your_position', [], $language) }}:</strong> {{ $userTurnInfo['position'] }}</p>
                <p><strong>{{ __('mail.your_turn_date', [], $language) }}:</strong> {{ $userTurnInfo['turn_date'] }}</p>
                <p><strong>{{ __('mail.payout_amount', [], $language) }}:</strong> ₦{{ number_format($userTurnInfo['payout_amount']) }}</p>
            </div>
            
            <div class="warning">
                <h4>{{ __('mail.important_reminder', [], $language) }}</h4>
                <p>{{ __('mail.payment_reminder', [], $language) }}</p>
                <p>{{ __('mail.prompt_payment_advice', [], $language) }}</p>
            </div>
        </div>
    </div>
</body>
</html>