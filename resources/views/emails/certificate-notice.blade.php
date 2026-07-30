<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kehadiran Anda</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; line-height: 1.6; color: #333; text-align: left;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
                    <!-- Header with Gradient and Icon -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); padding: 50px 20px;">
                            <img src="https://img.icons8.com/nolan/96/certificate.png" alt="Certificate Icon" width="80" style="display: block; margin-bottom: 20px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: 0.5px;">E-Sertifikat Kehadiran</h1>
                            <p style="color: #e0e7ff; margin: 10px 0 0 0; font-size: 16px; font-weight: 500;">Amikom Event Hub</p>
                        </td>
                    </tr>
                    
                    <!-- Email Content Area -->
                    <tr>
                        <td style="padding: 40px 40px 30px; text-align: left;">
                            <h2 style="color: #1e293b; font-size: 22px; margin-top: 0; margin-bottom: 20px;">Halo, {{ $userName ?? ($transaction->customer_name ?? 'Peserta') }}! 👋</h2>
                            
                            <p style="font-size: 16px; color: #475569; margin-bottom: 24px;">
                                Terima kasih telah berpartisipasi dengan antusias dalam event kami:
                            </p>
                            
                            <div style="background-color: #f8fafc; border-left: 4px solid #4f46e5; padding: 20px; border-radius: 6px; margin-bottom: 28px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                <strong style="color: #0f172a; font-size: 18px;">{{ $eventTitle ?? ($transaction->event->title ?? 'Event') }}</strong>
                            </div>

                            <p style="font-size: 16px; color: #475569; margin-bottom: 20px;">
                                Sebagai bentuk apresiasi dari kami, sertifikat keikutsertaan Anda telah diterbitkan. Anda dapat menemukan file <b>PDF sertifikat</b> tersebut pada <strong>lampiran (attachment)</strong> email ini.
                            </p>

                            <p style="font-size: 16px; color: #475569; margin-bottom: 35px;">
                                Kami berharap wawasan yang didapatkan dapat bermanfaat. Jangan lewatkan keseruan di event-event Amikom Event Hub selanjutnya!
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 15px;">
                                        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 0;">
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Sign-off -->
                            <p style="font-size: 15px; color: #64748b; margin-top: 15px; margin-bottom: 5px;">
                                Salam Hangat,
                            </p>
                            <p style="font-size: 16px; color: #1e293b; font-weight: 700; margin: 0;">
                                Panitia Amikom Event Hub
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer Information -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 25px 30px; border-top: 1px solid #f1f5f9;">
                            <p style="font-size: 13px; color: #94a3b8; margin: 0;">
                                Order ID: <strong>{{ $orderId ?? ($transaction->order_id ?? ($transaction->booking_code ?? 'NA')) }}</strong> &middot; © {{ date('Y') }} Amikom Event Hub
                            </p>
                            <p style="font-size: 12px; color: #cbd5e1; margin: 10px 0 0 0;">
                                Pesan ini dikirim secara otomatis. Mohon tidak membalas email ini.<br>
                                Jika ada kendala, hubungi layanan dukungan kami.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>