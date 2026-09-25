<?php

namespace App\Notifications;

use App\Models\GameMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class MatchCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $gameMatch;
    protected $context;

    public function __construct(GameMatch $gameMatch, string $context = 'general')
    {
        $this->gameMatch = $gameMatch;
        $this->context = $context;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable): MailMessage
    {
        $match = $this->gameMatch;
        $homeTeam = $match->homeTeam->name ?? 'Unknown';
        $awayTeam = $match->awayTeam->name ?? 'Unknown';
        $score = "{$match->home_score} - {$match->away_score}";

        $subject = match($this->context) {
            'home' => "Match Result: {$homeTeam} {$score} {$awayTeam}",
            'away' => "Match Result: {$awayTeam} {$score} {$homeTeam}",
            'association' => "Match Completed: {$homeTeam} vs {$awayTeam}",
            default => "Match Result: {$homeTeam} vs {$awayTeam}"
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}!")
            ->line("A match has been completed in your competition.")
            ->line("**{$homeTeam}** {$score} **{$awayTeam}**")
            ->line("Competition: {$match->competition->name}")
            ->line("Date: " . $match->match_date?->format('F j, Y') ?? 'TBD')
            ->action('View Match Details', url("/matches/{$match->id}"))
            ->line('Thank you for using our platform!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'match_id' => $this->gameMatch->id,
            'competition_id' => $this->gameMatch->competition_id,
            'home_team' => $this->gameMatch->homeTeam->name ?? 'Unknown',
            'away_team' => $this->gameMatch->awayTeam->name ?? 'Unknown',
            'home_score' => $this->gameMatch->home_score,
            'away_score' => $this->gameMatch->away_score,
            'context' => $this->context,
            'message' => $this->getMessage(),
            'type' => 'match_completed'
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'match_id' => $this->gameMatch->id,
            'competition_id' => $this->gameMatch->competition_id,
            'home_team' => $this->gameMatch->homeTeam->name ?? 'Unknown',
            'away_team' => $this->gameMatch->awayTeam->name ?? 'Unknown',
            'home_score' => $this->gameMatch->home_score,
            'away_score' => $this->gameMatch->away_score,
            'context' => $this->context,
            'message' => $this->getMessage(),
            'type' => 'match_completed',
            'timestamp' => now()->toISOString()
        ]);
    }

    protected function getMessage(): string
    {
        $match = $this->gameMatch;
        $homeTeam = $match->homeTeam->name ?? 'Unknown';
        $awayTeam = $match->awayTeam->name ?? 'Unknown';
        $score = "{$match->home_score} - {$match->away_score}";

        return match($this->context) {
            'home' => "Your team {$homeTeam} played against {$awayTeam}. Final score: {$score}",
            'away' => "Your team {$awayTeam} played against {$homeTeam}. Final score: {$score}",
            'association' => "Match completed: {$homeTeam} vs {$awayTeam}. Final score: {$score}",
            default => "Match completed: {$homeTeam} vs {$awayTeam}. Final score: {$score}"
        };
    }
}
