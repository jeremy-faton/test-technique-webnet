<?php

namespace App;

use App\Couleur;
use App\Valeur;

class Carte
{
  public function __construct(
    public readonly Couleur $couleur,
    public readonly Valeur $valeur
  ) {
  }
}
