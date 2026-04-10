<?php
namespace App\Models;

class Vehicule
{
  public ?int $id, $type_vehicule_id;
  public ?string $immatriculation, $marque, $modele, $couleur;
  public ?string $created_at, $updated_at, $last_login_at;
}