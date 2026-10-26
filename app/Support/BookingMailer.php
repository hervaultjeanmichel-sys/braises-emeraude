<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * E-mails des rendez-vous. Textes modifiables dans Réglages › Textes du site (clés booking.mail_*).
 * Un échec d'envoi est journalisé mais ne bloque jamais la demande.
 */
final class BookingMailer
{
    /** Nouvelle demande : alerte au magasin + accusé de réception au client (« en attente de confirmation »). */
    public static function received(Appointment $a): void
    {
        $a->loadMissing('store');
        $to = $a->store?->email ?: s('leads.fallback_email');
        if ($to) {
            self::send($to, '[Site] Demande de RDV — '.$a->type_label.' — '.$a->whenLabel(), implode("\n", array_filter([
                'Nouvelle demande de rendez-vous à valider — '.$a->store?->name,
                '',
                'Type : '.$a->type_label,
                'Créneau demandé : '.$a->whenLabel().' ('.$a->starts_at->diffInMinutes($a->ends_at).' min)',
                'Lieu : '.$a->placeLabel(),
                '',
                'Client : '.$a->name,
                'Téléphone : '.$a->phone,
                'E-mail : '.$a->email,
                $a->message ? "\nMessage :\n".$a->message : null,
                '',
                'Confirmer ou refuser : '.url('/admin/appointments/'.$a->id.'/edit'),
            ], fn ($l) => $l !== null)), $a->email, $a->name);
        }

        self::send($a->email, s('booking.mail_received_subject', 'Votre demande de rendez-vous — Braises Émeraude'), implode("\n", [
            'Bonjour '.$a->name.',',
            '',
            s('booking.mail_received_intro', 'Nous avons bien reçu votre demande de rendez-vous. Notre équipe la vérifie et vous confirme le créneau par e-mail ou par téléphone, en général sous 24 heures ouvrées.'),
            '',
            'Rendez-vous demandé : '.$a->type_label,
            'Date : '.$a->whenLabel(),
            'Lieu : '.$a->placeLabel(),
            '',
            s('booking.mail_received_pending', 'Attention : ce rendez-vous n’est pas encore confirmé.'),
            '',
            s('booking.mail_signature', 'L’équipe Braises Émeraude').($a->store?->phone ? "\n".$a->store->name.' — '.$a->store->phone : ''),
        ]));
    }

    /** Validation par l'équipe : confirmation au client avec le fichier agenda .ics. */
    public static function confirmed(Appointment $a): void
    {
        $a->loadMissing('store');
        self::send($a->email, s('booking.mail_confirmed_subject', 'Rendez-vous confirmé — Braises Émeraude'), implode("\n", array_filter([
            'Bonjour '.$a->name.',',
            '',
            s('booking.mail_confirmed_intro', 'Votre rendez-vous est confirmé :'),
            '',
            $a->type_label,
            $a->whenLabel(),
            $a->placeLabel(),
            $a->team_note ? "\n".$a->team_note : null,
            '',
            s('booking.mail_confirmed_change', 'Un empêchement ? Appelez-nous pour déplacer le rendez-vous.'),
            '',
            s('booking.mail_signature', 'L’équipe Braises Émeraude').($a->store?->phone ? "\n".$a->store->name.' — '.$a->store->phone : ''),
        ], fn ($l) => $l !== null)), null, null, $a->ics());
    }

    /** Refus (ou créneau impossible) : on invite le client à reprendre un créneau ou à appeler. */
    public static function refused(Appointment $a): void
    {
        $a->loadMissing('store');
        self::send($a->email, s('booking.mail_refused_subject', 'Votre demande de rendez-vous — Braises Émeraude'), implode("\n", array_filter([
            'Bonjour '.$a->name.',',
            '',
            s('booking.mail_refused_intro', 'Nous ne pouvons malheureusement pas retenir le créneau demandé :').' '.$a->whenLabel().'.',
            $a->team_note ? "\n".$a->team_note : null,
            '',
            s('booking.mail_refused_next', 'Vous pouvez choisir un autre créneau en ligne ou nous appeler :').' '.url('/rendez-vous/?magasin='.$a->store_id),
            '',
            s('booking.mail_signature', 'L’équipe Braises Émeraude').($a->store?->phone ? "\n".$a->store->name.' — '.$a->store->phone : ''),
        ], fn ($l) => $l !== null)));
    }

    private static function send(string $to, string $subject, string $body, ?string $replyTo = null, ?string $replyName = null, ?string $ics = null): void
    {
        try {
            Mail::raw($body, function ($m) use ($to, $subject, $replyTo, $replyName, $ics) {
                $m->to($to)->subject($subject);
                if ($replyTo) {
                    $m->replyTo($replyTo, $replyName);
                }
                if ($ics) {
                    $m->attachData($ics, 'rendez-vous.ics', ['mime' => 'text/calendar; charset=utf-8; method=PUBLISH']);
                }
            });
        } catch (\Throwable $e) {
            Log::warning('E-mail de rendez-vous non envoyé', ['to' => $to, 'subject' => $subject, 'error' => $e->getMessage()]);
        }
    }
}
