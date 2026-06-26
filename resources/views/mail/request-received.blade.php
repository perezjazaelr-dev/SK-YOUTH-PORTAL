<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SK Portal Request Received</title>
    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }
        .header {
            background-color: #1e40af;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
            font-size: 14px;
        }
        .content h2 {
            font-size: 16px;
            margin-top: 0;
            font-weight: 700;
        }
        .reference-box {
            background-color: #eff6ff;
            border: 1px dashed #bfdbfe;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .reference-title {
            font-size: 10px;
            color: #1e40af;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
            display: block;
        }
        .reference-number {
            font-size: 45px;
            font-family: monospace;
            font-weight: 900;
            color: #1d4ed8;
            margin-top: 2px;
            display: block;
        }
        .btn {
            display: inline-block;
            background-color: #1e40af;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
            text-align: center;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- Header -->
        <div class="header">
            <h1>Sangguniang Kabataan</h1>
            <p>Barangay Namayan Portal</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Hello {{ $requestModel->first_name ?? $requestModel->requestor_first_name }},</h2>
            
            <p>Thank you for submitting your request. We have successfully registered your application in the SK Namayan Youth Portal.</p>
            
            <div class="reference-box">
                <span class="reference-title">Request Type</span>
                <span style="font-weight: 700; font-size: 14px; color: #1e40af;">{{ $typeLabel }}</span>
                
                <span class="reference-title" style="margin-top: 10px;">Reference Number</span>
                <span class="reference-number">{{ $referenceNumber }}</span>
                
                <span class="reference-title" style="margin-top: 10px;">Initial Status</span>
                <span style="font-weight: 700; color: #d97706; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">{{ ucfirst($requestModel->status ?? 'pending') }}</span>
            </div>

            <p>Your application is now queued for verification by our Barangay desk officers. We will review your details shortly and update you when there is a status update.</p>

            <p style="margin-top: 25px;">You can track the live progress of this submission or cancel/edit it while it is pending review by clicking the button below:</p>
            
            <div style="text-align: center;">
                <a href="{{ route('track.index') }}?email={{ urlencode($requestModel->email) }}" class="btn" style="color: #ffffff;">Track Your Request</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Sangguniang Kabataan Barangay Namayan &bull; Mandaluyong City, Metro Manila<br>
            Please do not reply directly to this email. For inquiries, email info@sknamayan.gov.ph.
        </div>

    </div>

</body>
</html>
