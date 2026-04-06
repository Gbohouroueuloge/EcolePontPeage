<?php
namespace App\Models;

class Agent extends User
{
  public ?int $id;
  public ?int $user_id;
  public ?string $fin, $debut;

  public function getDateDebut(): ?\DateTime
  {
    return new \DateTime($this->debut) ?? null;
  }

  public function getDateFin(): ?\DateTime
  {
    return new \DateTime($this->fin) ?? null;
  }
}