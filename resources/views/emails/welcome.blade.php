<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('mail.welcome_subject', [], $language) }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #094168; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .button { display: inline-block; padding: 12px 24px; background-color: #FF821A; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('mail.welcome_title', [], $language) }}</h1>
        </div>
        <div class="content">
            <h2>{{ __('mail.welcome_greeting', ['name' => $user->name], $language) }}</h2>
            <p>{{ __('mail.welcome_message', [], $language) }}</p>
            <p>{{ __('mail.welcome_features', [], $language) }}</p>
            <ul>
                <li>{{ __('mail.feature_create_groups', [], $language) }}</li>
                <li>{{ __('mail.feature_join_groups', [], $language) }}</li>
                <li>{{ __('mail.feature_track_contributions', [], $language) }}</li>
            </ul>
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ route('dashboard') }}" class="button">{{ __('mail.get_started', [], $language) }}</a>
            </p>
            <p>{{ __('mail.welcome_footer', [], $language) }}</p>
        </div>
    </div>
</body>
</html>