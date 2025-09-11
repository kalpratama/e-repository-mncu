<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode OTP Verifikasi</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f9fc; font-family: Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f6f9fc; padding: 20px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #1F3D7B; padding: 24px; text-align: center;">
                            <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #ffffff;">
                                MNC University
                            </h2>
                            <!-- <img src="{{ asset('images/mncu_logo_wide.png') }}" alt="MNCU Logo" style="max-height: 50px;"> -->
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 35px 30px; text-align: center; color: #333;">
                            <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #1F3D7B;">
                                Verifikasi Email Anda
                            </h2>
                            <p style="font-size: 16px; margin: 12px 0 20px; color: #555;">
                                Masukkan kode OTP berikut untuk menyelesaikan proses pendaftaran Anda.
                            </p>

                            <!-- OTP CODE -->
                            <div style="background-color: #f1f5ff; padding: 18px; margin: 15px auto; max-width: 220px; border-radius: 8px; font-size: 28px; font-weight: bold; color: #1F3D7B; letter-spacing: 4px;">
                                {{ $otp }}
                            </div>

                            <!-- CTA BUTTON -->
                            <a href="https://repository.mncu.ac.id/verify-otp?otp={{ $otp }}"
                               style="display: inline-block; margin-top: 20px; padding: 14px 24px; background-color: #1F3D7B; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 8px; transition: background-color 0.3s;">
                                Verifikasi Sekarang
                            </a>

                            <p style="font-size: 13px; margin-top: 20px; color: #888;">
                                Kode ini akan kedaluwarsa dalam <strong>10 menit</strong>.
                                Jika Anda tidak meminta kode ini, abaikan email ini.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f6f9fc; padding: 16px; text-align: center; font-size: 13px; color: #777;">
                            &copy; {{ date('Y') }} MNCU-IR. All Rights Reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
