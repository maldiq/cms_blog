Pesan kontak baru

Nama: {{ $submission->name }}
Email: {{ $submission->email }}
Telepon: {{ $submission->phone ?? '-' }}
Subjek: {{ $submission->subject }}
Locale: {{ $submission->locale }}

Pesan:
{{ $submission->message }}

---
IP: {{ $submission->ip_address ?? '-' }}
User agent: {{ $submission->user_agent ?? '-' }}
Dikirim: {{ $submission->created_at?->toDateTimeString() }}
