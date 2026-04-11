<?php

namespace App\Models;

class Agent extends User
{
  public ?int $id;
  public ?int $user_id;
  public ?string $fin, $debut;

  public function getDateDebut(): ?\DateTime
  {
    return $this->debut ? new \DateTime($this->debut) : null;
  }

  public function getDateFin(): ?\DateTime
  {
    return $this->fin ? new \DateTime($this->fin) : null;
  }

  function is_en_cours(): bool
  {
    $debut = $this->getDateDebut();
    $fin = $this->getDateFin();

    // Si pas de date de début définie, l'agent n'est pas en service
    if ($debut === null) {
      return false;
    }

    return $debut->getTimestamp() <= time() && ($fin === null || $fin->getTimestamp() >= time());
  }
}
