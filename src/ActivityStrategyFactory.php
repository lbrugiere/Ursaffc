<?php
namespace Urssaf;

class ActivityStrategyFactory
{
    public static function make(string $activity): AbstractActivityStrategy
    {
        return match ($activity) {
            'bic' => new BicStrategy(),
            'bnc' => new BncStrategy(),
            'bic-vente' => new BicVenteStrategy(),
            default => throw new \InvalidArgumentException("Régime d'activité inconnu : $activity")
        };
    }
}