<?php

namespace App\Enum;

enum ProjetStatus: string
{
    case EN_COURS = 'en_cours';
    case TERMINE = 'termine';
    case EN_ATTENTE = 'en_attente';

    public function label(): string
    {
        return match($this) {
            self::EN_COURS => 'En cours',
            self::TERMINE => 'Terminé',
            self::EN_ATTENTE => 'En attente',
        };
    }
}
