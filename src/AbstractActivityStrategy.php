<?php
namespace Urssaf;

//Ici on choisit une classe abstraite pour centraliser l'implémentation de la génération du rapport, commune à tous les régimes. Ici on fait même un mélange entre le pattern *Strategy* et *Template Method*

abstract class AbstractActivityStrategy
{
    //Retourne le rapport qui sera affiché sur la sortie standard.
    public function buildReport(float $caHt, string $taxSystem): string
    {
        $cotisationRate = $this->cotisationRate();
        $subsidy = $this->specificSubsidy($caHt);

        $cotisations = $caHt * $cotisationRate;

        $impot = 0;
        $revenuImposable = 0;

        if ($taxSystem === 'ps') {
            $revenuImposable = $caHt * (1 - $this->abatementRate());
        }

        if ($taxSystem === 'vfl') {
            $impot = $caHt * $this->taxDischargePayment();
        }

        $caTtc = $caHt - $cotisations - $impot + $subsidy;

        $report = "CA HT mensuel: " . number_format($caHt, 2, ',', ' ') . " EUROS\n";
        $report .= "Aide spécifique: " . number_format($subsidy, 2, ',', ' ') . " EUROS\n";
        $report .= "Cotisations sociales: " . number_format($cotisations, 2, ',', ' ') . " EUROS\n";

        if ($taxSystem === 'ps') {
            $report .= "Revenu imposable: " . number_format($revenuImposable, 2, ',', ' ') . " EUROS\n";
        }

        if ($taxSystem === 'vfl') {
            $report .= "Montant de l'impôt à prélever: " . number_format($impot, 2, ',', ' ') . " EUROS\n";
        }

        $report .= "CA TTC mensuel: " . number_format($caTtc, 2, ',', ' ') . " EUROS\n";

        return $report;
    }
    //Retourne le taux de cotisation social
    abstract protected function cotisationRate(): float;
    //Retourne le taux de prélèvement dans le cadre du régime fiscal "Versement fiscal libératoire" (vfl)
    abstract protected function taxDischargePayment(): float;
    //Retourne le taux d'abattement fiscal dans le cadre du régime fiscal "Prélèvement à la source" (ps)
    abstract protected function abatementRate(): float;

    //De la logique métier propre à chaque régime d'activité
    //Calcul des indemnités de frais d'exploitation
    abstract protected function specificSubsidy(float $caHt): float;
}