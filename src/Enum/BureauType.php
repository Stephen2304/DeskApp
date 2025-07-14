<?php
namespace App\Enum;

enum BureauType: string
{
    case INDIVIDUEL = 'individuel';
    case OUVERT = 'ouvert';
    case REUNION = 'reunion';
    case AUTRE = 'autre';
} 