<?php
namespace App\Models;

class Paiement {
  public $id;
  public $vehicule_id;
  public $guichet_id;
  public $mode_paiement;
  public $montant;
  public $created_at;
  public $updated_at;

  private $icon = [
    "Espece" => "payments",
    "Mobile Money" => "phone_android",
    "Carte" => "credit_card",
    "Abonnement" => "contactless",
  ];

  public function getIcon(): string {
    return $this->icon[$this->mode_paiement] ?? "payments";
  }

  public function getPrice(): string {
    return $this->mode_paiement === "Abonnement" ? "Abonnement" : number_format($this->montant, 0, ',', ' ');
  }

  public function getCreatedAt(): ?\DateTime
  {
    return new \DateTime($this->created_at) ?? null;
  }
}