<?php

namespace Database\Seeders;

use App\Models\BulletinPaie;
use App\Models\Conge;
use App\Models\Contrat;
use App\Models\Employe;
use App\Models\Formation;
use App\Models\Presence;
use App\Models\Prime;
use Illuminate\Database\Seeder;

class CleanDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        BulletinPaie::query()->delete();
        Prime::query()->delete();
        Formation::query()->delete();
        Presence::query()->delete();
        Conge::query()->delete();
        Contrat::query()->delete();
        Employe::query()->delete();
    }
}