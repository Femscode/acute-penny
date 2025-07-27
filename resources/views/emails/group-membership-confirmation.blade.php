<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('mail.group_membership_title', [], $language) }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #094168; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .button { display: inline-block; padding: 12px 24px; background-color: #FF821A; color: white; text-decoration: none; border-radius: 5px; }
        .group-details { background-color: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .group-details ul { list-style: none; padding: 0; }
        .group-details li { padding: 5px 0; border-bottom: 1px solid #eee; }
        .group-details li:last-child { border-bottom: none; }
        .group-details strong { color: #094168; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('mail.group_membership_title', [], $language) }}</h1>
        </div>
        <div class="content">
            <h2>{{ __('mail.greeting', ['name' => $mailData['user_name']], $language) }}</h2>
            
            @if($mailData['action'] === 'joined')
                <p>{{ __('mail.you_joined_group_message', [], $language) }}</p>
                
                <div class="group-details">
                    <h3>{{ __('mail.group_details', [], $language) }}:</h3>
                    <ul>
                        <li><strong>{{ __('mail.group_name', [], $language) }}:</strong> {{ $mailData['group_name'] }}</li>
                        <li><strong>{{ __('mail.contribution_amount', [], $language) }}:</strong> {{ number_format($mailData['contribution_amount'], 2) }}</li>
                        <li><strong>{{ __('mail.frequency', [], $language) }}:</strong> {{ ucfirst($mailData['frequency']) }}</li>
                        <li><strong>{{ __('mail.start_date', [], $language) }}:</strong> {{ $mailData['start_date'] }}</li>
                    </ul>
                </div>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('groups.show', $mailData['group_uuid']) }}" class="button">{{ __('mail.view_group', [], $language) }}</a>
                </p>
            @else
                <p>{{ __('mail.you_left_group_message', [], $language) }}</p>
                <p>{{ __('mail.group_left_note', [], $language) }}</p>
            @endif
            
            <p>{{ __('mail.thanks', [], $language) }}</p>
        </div>
    </div>
</body>
</html>