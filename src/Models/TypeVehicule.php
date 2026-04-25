<?php
namespace App\Models;

use App\ConnectionBDD;

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

  public function create() {
    $pdo = ConnectionBDD::getPdo();

    $query = $pdo->prepare("INSERT INTO typevehicule(libelle, price) VALUES (:libelle, :price)");
    $query->execute([
      "libelle" => $this->libelle,
      "price" => $this->price,
    ]);
  }

  public function update() {
    $pdo = ConnectionBDD::getPdo();

    $query = $pdo->prepare("UPDATE typevehicule SET libelle = :libelle, price = :price WHERE id = :id");
    $query->execute([
      "libelle" => $this->libelle,
      "price" => $this->price,
      "id" => $this->id,
    ]);
  }

  public function delete() {
    $pdo = ConnectionBDD::getPdo();

    $query = $pdo->prepare("DELETE FROM typevehicule WHERE id = :id");
    $query->execute([
      "id" => $this->id,
    ]);
  }
}