<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SK Portal Request Status Changed</title>
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
            background-color: #f0fdf4;
            border: 1px dashed #bbf7d0;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .reference-title {
            font-size: 10px;
            color: #166534;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
            display: block;
        }
        .reference-number {
            font-size: 20px;
            font-family: monospace;
            font-weight: 800;
            color: #15803d;
            margin-top: 2px;
            display: block;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .badge-review {
            background-color: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .badge-approved {
            background-color: #d1fae5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .badge-declined {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
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
            
            <p>The status of your service request has been updated by our Barangay desk officers.</p>
            
            <div class="reference-box">
                <span class="reference-title">Request Type</span>
                <span style="font-weight: 700; font-size: 14px; color: #1e40af;">{{ $typeLabel }}</span>
                
                <span class="reference-title" style="margin-top: 10px;">Reference Number</span>
                <span class="reference-number">{{ $referenceNumber }}</span>
                
                <span class="reference-title" style="margin-top: 10px;">New Status</span>
                @if($requestModel->status == 'approved')
                    <span class="badge badge-approved">Approved</span>
                @elseif($requestModel->status == 'declined')
                    <span class="badge badge-declined">Declined</span>
                @elseif($requestModel->status == 'review')
                    <span class="badge badge-review">Under Review</span>
                @else
                    <span class="badge badge-pending">Pending</span>
                @endif
            </div>

            <!-- Custom message copy based on status -->
            @if($requestModel->status == 'approved')
                <p>🎉 <strong>Congratulations!</strong> Your request has been reviewed and approved. Our staff will coordinate the details of the appointment or service delivery with you shortly.</p>
            @elseif($requestModel->status == 'declined')
                <p>⚠️ <strong>Request Declined:</strong> Unfortunately, your request could not be processed at this time. This may be due to missing details, scheduling conflicts, or program capacity limits. You are welcome to submit a new application with revised inputs.</p>
            @elseif($requestModel->status == 'review')
                <p>⏳ <strong>Under Review:</strong> Your request is now actively being reviewed by our desk officers. We will notify you once evaluation is complete.</p>
            @else
                <p>⏳ Your request is currently under review by our desk officers. No action is required from you at this moment.</p>
            @endif

            <p style="margin-top: 25px;">To track this request or check the live logs, please click the button below:</p>
            
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
