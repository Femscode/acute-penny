@component('mail::message')
# {{ __('mail.group_membership_title') }}

{{ __('mail.greeting', ['name' => $mailData['user_name']]) }}

@if($mailData['action'] === 'joined')
{{ __('mail.you_joined_group_message') }}

**{{ __('mail.group_details') }}:**
- **{{ __('mail.group_name') }}:** {{ $mailData['group_name'] }}
- **{{ __('mail.contribution_amount') }}:** {{ number_format($mailData['contribution_amount'], 2) }}
- **{{ __('mail.frequency') }}:** {{ ucfirst($mailData['frequency']) }}
- **{{ __('mail.start_date') }}:** {{ $mailData['start_date'] }}

@component('mail::button', ['url' => route('groups.show', $mailData['group_uuid'])])
{{ __('mail.view_group') }}
@endcomponent
@else
{{ __('mail.you_left_group_message') }}

{{ __('mail.group_left_note') }}
@endif

{{ __('mail.thanks') }}
@endcomponent