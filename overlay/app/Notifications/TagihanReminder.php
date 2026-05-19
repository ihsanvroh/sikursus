<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TagihanReminder extends Notification {
    use Queueable;

    public function __construct(
        public string $judul,
        public string $body,
        public ?int $tagihan_id = null,
    ) {}

    public function via($notifiable): array { return ['database']; }

    public function toArray($notifiable): array {
        return [
            'judul' => $this->judul,
            'body' => $this->body,
            'tagihan_id' => $this->tagihan_id,
            'tipe' => 'tagihan',
            // url biar di-derive role-aware oleh NotifikasiController@open;
            // tapi tetap kasih literal '/tagihan/{id}' sebagai fallback historis (admin).
            'url' => null,
        ];
    }
}
