<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $action === 'joined' ? __('mail.member_joined_subject', ['group_name' => $group->name], $language) : __('mail.member_left_subject', ['group_name' => $group->name], $language) }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #094168; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .group-info { background-color: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('mail.group_update_title', [], $language) }}</h1>
        </div>
        <div class="content">
            @if($action === 'joined')
                <h2>{{ __('mail.member_joined_greeting', ['name' => $user->name, 'group_name' => $group->name], $language) }}</h2>
                <p>{{ __('mail.member_joined_message', [], $language) }}</p>
            @else
                <h2>{{ __('mail.member_left_greeting', ['name' => $user->name, 'group_name' => $group->name], $language) }}</h2>
                <p>{{ __('mail.member_left_message', [], $language) }}</p>
            @endif
            
            <div class="group-info">
                <h3>{{ __('mail.group_details', [], $language) }}</h3>
                <p><strong>{{ __('general.group_name', [], $language) }}:</strong> {{ $group->name }}</p>
                <p><strong>{{ __('general.contribution_amount', [], $language) }}:</strong> ₦{{ number_format($group->contribution_amount) }}</p>
                <p><strong>{{ __('general.frequency', [], $language) }}:</strong> {{ __('general.' . $group->frequency, [], $language) }}</p>
                <p><strong>{{ __('general.current_members', [], $language) }}:</strong> {{ $group->current_members }}/{{ $group->max_members }}</p>
            </div>
        </div>
    </div>
</body>
</html>