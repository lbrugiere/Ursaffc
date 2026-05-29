<?php

namespace Urssaf;

class BncStrategy extends AbstractActivityStrategy
{
    protected function cotisationRate(): float
    {
        return 0.22;
    }

    protected function taxDischargePayment(): float
    {
        return 0.022;
    }

    protected function abatementRate(): float
    {
        return 0.34;
    }

    protected function specificSubsidy(float $caHt): float
    {
        if ($caHt < 1500) {
            return $caHt * 0.15;
        }

        return 0.0;
    }
}