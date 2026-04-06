<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Invited</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafb; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 520px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #14B8A6, #0d9488); padding: 40px 32px; text-align: center;">
                            <div style="width: 56px; height: 56px; margin: 0 auto 16px; background-color: rgba(255,255,255,0.2); border-radius: 16px; line-height: 56px; text-align: center;">
                                <span style="font-size: 28px;">🤝</span>
                            </div>
                            <h1 style="margin: 0; font-size: 24px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px;">You're Invited!</h1>
                            <p style="margin: 8px 0 0; font-size: 14px; color: rgba(255,255,255,0.8); font-weight: 500;">Join the team on QLine</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 20px; font-size: 15px; color: #374151; line-height: 1.6;">
                                Hi there! 👋
                            </p>
                            <p style="margin: 0 0 20px; font-size: 15px; color: #374151; line-height: 1.6;">
                                <strong style="color: #111827;">{{ $inviterName }}</strong> has invited you to join
                                <strong style="color: #0d9488;">{{ $businessName }}</strong> as a
                                <strong>{{ str_replace('_', ' ', Str::title($invitation->role)) }}</strong>.
                            </p>

                            {{-- Info card --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0fdfa; border-radius: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 6px; font-size: 10px; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 1.5px;">Your Email</p>
                                        <p style="margin: 0 0 16px; font-size: 14px; font-weight: 700; color: #111827;">{{ $invitation->email }}</p>
                                        <p style="margin: 0 0 6px; font-size: 10px; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 1.5px;">Link Expires</p>
                                        <p style="margin: 0; font-size: 14px; font-weight: 700; color: #111827;">{{ $invitation->expires_at->format('M d, Y \a\t h:i A') }}</p>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA Button --}}
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('invite.show', $invitation->token) }}"
                                           style="display: inline-block; padding: 14px 40px; background: linear-gradient(135deg, #14B8A6, #0d9488); color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 800; border-radius: 14px; letter-spacing: 0.3px;">
                                            Accept Invitation →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 24px 0 0; font-size: 12px; color: #9ca3af; line-height: 1.6; text-align: center;">
                                If the button doesn't work, copy and paste this link into your browser:<br>
                                <a href="{{ route('invite.show', $invitation->token) }}" style="color: #14B8A6; word-break: break-all;">
                                    {{ route('invite.show', $invitation->token) }}
                                </a>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 20px 32px; border-top: 1px solid #f3f4f6; text-align: center; background-color: #f9fafb;">
                            <p style="margin: 0; font-size: 11px; font-weight: 700; color: #d1d5db; text-transform: uppercase; letter-spacing: 1px;">
                                Powered by Q<span style="color: #14B8A6;">Line</span>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
