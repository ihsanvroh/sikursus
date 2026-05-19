<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PesanJadwalNotification extends Notification {
    use Queueable;

    public function __construct(
        public string $judul,
        public string $body,
        public ?int $kelasId = null,
        public ?string $url = null,
        public string $sumber = 'guru', // 'guru' / 'system'
    ) {}

    public function via($notifiable): array {
        return ['database'];
    }

    public function toArray($notifiable): array {
        return [
            'judul' => $this->judul,
            'body' => $this->body,
            'kelas_id' => $this->kelasId,
            'url' => $this->url,
            'sumber' => $this->sumber,
            'tipe' => 'jadwal',
        ];
    }
}
