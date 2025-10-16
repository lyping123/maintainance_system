<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <style>
    /* Minimal email-safe inline styles recommended.
       Consider using a premailer (CSS inliner) for convenience. */
    .card { background: #fff; padding: 16px; border-radius: 6px; font-family: Arial, sans-serif; color: #222; }
    .muted { color: #6b7280; font-size: 13px; }
    .btn { display:inline-block; padding:10px 14px; background:#2563eb; color:#fff; border-radius:4px; text-decoration:none; }
  </style>
</head>
<body>
  @foreach ($report as $item)
      <div class="card">
        <h2>Maintenance Report Reminder</h2>

        <p class="muted">Report #{{ $item->id }} — {{ $item->report_type }}</p>

        <p><strong>Location:</strong> {{ $item->places }}</p>
        <p><strong>Issue:</strong><br>{{ $item->report_issue }}</p>

        <p><strong>Status:</strong> {{ $item->status }}</p>

        <p>
        <a class="btn" href="{{ route('report.detail.show', ['id' => $item->id]) }}">
            View report
        </a>
        </p>

        <p class="muted">If you cannot view the report click the link above.</p>
    </div>
  @endforeach
  
</body>
</html>