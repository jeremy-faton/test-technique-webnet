<?php

namespace App;

use App\Carte;
use App\Valeur;
use App\Couleur;

class Pioche
{
  private array $set = [];

  public function __construct()
  {
    foreach (Couleur::cases() as $couleur) {
      foreach (Valeur::cases() as $valeur) {
        $this->set[] = new Carte($couleur, $valeur);
      }
    }
  }

  public function piocher(int $nbCarte): array
  {
    if (!in_array($nbCarte, range(0, 52))) {
      throw new \Exception("Le nombre de carte à tirer doit être en 0 et 52.");
    }
    shuffle($this->set);
    return array_slice($this->set, 0, $nbCarte);
  }

  public function tri(array $main, array $ordreCouleur, array $ordreValeur): array
  {
    $groupesCouleurs = array_map(
      fn ($couleur) =>
      array_filter(
        $main,
        fn ($carte) => $carte->couleur === $couleur
      ),
      $ordreCouleur
    );

    foreach ($groupesCouleurs as &$groupe) {
      usort(
        $groupe,
        function ($a, $b) use ($ordreValeur) {
          $scoreA = $this->getScoreValeur($ordreValeur, $a);
          $scoreB = $this->getScoreValeur($ordreValeur, $b);
          if ($scoreA === $scoreB) {
            return 0;
          }
          return ($scoreA < $scoreB) ? -1 : 1;
        }
      );
    }

    return array_reduce($groupesCouleurs, fn ($prev, $curr) => [...$prev, ...$curr], []);
  }

  private function getScoreValeur(array $ordreValeur, Carte $carte): int
  {
    foreach ($ordreValeur as $index => $valeur) {
      if ($valeur === $carte->valeur) {
        return $index;
      }
    }
    return 0;
  }
}
