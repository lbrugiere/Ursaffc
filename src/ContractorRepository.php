<?php

namespace Urssaf;

class ContractorRepository
{
    //Injection de dépendance dans le constructeur de l'instance PDO (accès a la base de données)
    public function __construct(private \PDO $pdo) {}


    /**
     * Retourne l'identifiant généré par la base pour le nouveau record
     * @throws \Exception Si l'insertion en base de données échoue
     * @return int
     */
    public function save(string $fullName, string $siret, string $activity, string $taxSystem): int
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO contractor (full_name, siret, activity, tax_system)
        VALUES (:full_name, :siret, :activity, :tax_system)
        ");

        $ok = $stmt->execute([
        ':full_name' => $fullName,
        ':siret' => $siret,
        ':activity' => $activity,
        ':tax_system' => $taxSystem
        ]);

        if (!$ok) {
        throw new \Exception("Erreur lors de l'insertion");
        }

        return (int)$this->pdo->lastInsertId();
        }

   /**
    * @return Contractor|null
    */
    public function find(int $id): ?Contractor
    {  
        $stmt = $this->pdo->prepare("
        SELECT * FROM contractor WHERE id = :id
        ");

        $stmt->execute([
        ':id' => $id
        ]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
        return null;
        }

        return new Contractor(
            (int)$data['id'],
            $data['full_name'],
            $data['siret'],
            $data['activity'],
            $data['tax_system']
        );
    }

    /**
    * @return array<Contractor>
    */
    public function findAll(): array
    {  
        $stmt = $this->pdo->query("
        SELECT * FROM contractor
        ");

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];

        foreach ($rows as $data) {
            $result[] = new Contractor(
                (int)$data['id'],
                $data['full_name'],
                $data['siret'],
                $data['activity'],
                $data['tax_system']
            );
        }

        return $result;
    }
}
