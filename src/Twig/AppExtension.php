<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use DateTime;
use DateTimeZone;
use Twig\TwigFilter;
use DateTimeImmutable;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('date_format_special', [$this, 'dateFormatSpecial']),
        ];
    }

    public function dateFormatSpecial(DateTime|DateTimeImmutable $date)
    {
        $timezone = new DateTimeZone('Europe/Paris'); // UTC+2
        $now = new DateTime('now', $timezone);
        $date = $date->setTimezone($timezone);
        $interval = $now->diff($date);

        if ($interval->y > 0 || $interval->m > 1) {
            return $date->format('d/m H:i');
        } elseif ($interval->d > 0) {
            return 'Il y a ' . $interval->d . ' jours';
        } elseif ($interval->h > 0) {
            return 'Il y a ' . $interval->h . ' heures';
        } elseif ($interval->i > 0) {
            return 'Il y a ' . $interval->i . ' minutes';
        } else {
            return 'À l\'instant';
        }
    }
}
?>