<?php
declare(strict_types=1);

//Chargement de l'autoloader
require_once __DIR__ . '/vendor/autoload.php';
use Urssaf\ContractorRepository;
use Urssaf\ActivityStrategyFactory;

// 1. Configuration PDO SQLite...
$pdo = new PDO(
    'sqlite:' . __DIR__ . '/data/database.sqlite'
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2. Création automatique de la table si elle n'existe pas...
$pdo->exec("CREATE TABLE IF NOT EXISTS contractor (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    siret TEXT NOT NULL UNIQUE,
    activity TEXT NOT NULL,
    tax_system TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_DATE
)
");

//Repository
$repository = new ContractorRepository($pdo);

// Extraction des arguments passés au script
$command = $argv[1] ?? null;

switch ($command) {
    case 'add':
        $fullName = $argv[2] ?? null;
        $siret = $argv[3] ?? null;
        $activity = $argv[4] ?? null;
        $taxSystem = $argv[5] ?? null;
        
        if (!$fullName || !$siret || !$activity || !$taxSystem) {
            echo "Erreur: Arguments manquants pour 'add'.\n";
            exit(1);
        }
        // 1. Valider le SIRET, gérer les doublons, et insérer en BDD
        if (!preg_match('/^\d{14}$/', $siret)) {
            echo "Le SIRET n'est pas valide.\n";
            exit(1);
        }

        try {
            $id = $repository->save($fullName, $siret, $activity, $taxSystem);
            echo "Entreprise enregistrée avec ID $id\n";
        } catch (PDOException $e) {
            echo "L'autoentreprise avec le SIRET $siret existe déjà.\n";
        }
        break;

    case 'ls':
        // 1. Lister les microentreprises
        $list = $repository->findAll();

        foreach ($list as $c) {
            echo $c->getId() . " "
                . $c->getFullName() . " "
                . $c->getActivity() . " "
                . $c->getTaxSystem() . PHP_EOL;
        }
        break;

    case 'dry-declare':
        $id = isset($argv[2]) ? (int)$argv[2] : null;
        $caHt = isset($argv[3]) ? (float)$argv[3] : null;
        
        if (!$id || !$caHt) {
            echo "Erreur: Arguments manquants pour 'dry-declare'.\n";
            exit(1);
        }
        //1. Récupérer les données de l'auto-entreprise
        $contractor = $repository->find($id);

        if (!$contractor) {
            echo "Entreprise introuvable.\n";
            exit(1);
        }
        //2. Injecter la Strategy a un objet Contractor (autoentreprise) 
        $strategy = ActivityStrategyFactory::make($contractor->getActivity());
        
        //3. Calculer les cotisations sociales, appliquer la fiscalité et construire le rapport
        //Imprimer le rapport
        echo $strategy->buildReport($caHt, $contractor->getTaxSystem());
        break;

    //Par convention, lancer une commande sans argument affiche le manuel
    default:
        echo "Usage:\n";
        echo "  php urssafc.php add \"NOM_COMPLET\" SIRET REGIME_ACTIVITE REGIME_FISCAL\n";
        echo "  php urssafc.php ls\n";
        echo "  php urssafc.php dry-declare ID CA_HT\n";
        exit(1);
}