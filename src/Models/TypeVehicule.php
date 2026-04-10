<?php
namespace App\Models;

class TypeVehicule {
  public $id;
  public $libelle;
  public $price;
  public $created_at;
  public $updated_at;

  private $icon = [
    "Moto" => "motorcycle",
    "Voiture" => "directions_car",
    "Van/SUV" => "airport_shuttle",
    "Poids Lourd" => "local_shipping",
  ];

  public function getIcon(): string {
    return $this->icon[$this->libelle] ?? "commute";
  }

  public function getPrice(): string {
    return number_format($this->price, 0, ',', ' ');
  }

  public function getCreatedAt(): ?\DateTime
  {
    return new \DateTime($this->created_at) ?? null;
  }
}