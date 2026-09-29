Komentar baru pada {{ class_basename($comment->commentable_type) }} #{{ $comment->commentable_id }}

Penulis: {{ $comment->authorDisplayName() }}
Email: {{ $comment->author_email ?? '-' }}
Status: {{ $comment->status }}

---
{{ $comment->content }}
---

Moderation: {{ url('/kelola/comments') }}
