<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            // Métropole - quelques exemples
            ['code' => '01', 'name' => 'Ain'],
            ['code' => '02', 'name' => 'Aisne'],
            ['code' => '03', 'name' => 'Allier'],
            ['code' => '06', 'name' => 'Alpes-Maritimes'],
            ['code' => '07', 'name' => 'Ardèche'],
            ['code' => '08', 'name' => 'Ardennes'],
            ['code' => '10', 'name' => 'Aube'],
            ['code' => '13', 'name' => 'Bouches-du-Rhône'],
            ['code' => '14', 'name' => 'Calvados'],
            ['code' => '15', 'name' => 'Cantal'],
            ['code' => '16', 'name' => 'Charente'],
            ['code' => '17', 'name' => 'Charente-Maritime'],
            ['code' => '19', 'name' => 'Corrèze'],
            ['code' => '21', 'name' => 'Côte-d\'Or'],
            ['code' => '22', 'name' => 'Côtes-d\'Armor'],
            ['code' => '24', 'name' => 'Dordogne'],
            ['code' => '25', 'name' => 'Doubs'],
            ['code' => '26', 'name' => 'Drôme'],
            ['code' => '27', 'name' => 'Eure'],
            ['code' => '29', 'name' => 'Finistère'],
            ['code' => '30', 'name' => 'Gard'],
            ['code' => '31', 'name' => 'Haute-Garonne'],
            ['code' => '33', 'name' => 'Gironde'],
            ['code' => '34', 'name' => 'Hérault'],
            ['code' => '35', 'name' => 'Ille-et-Vilaine'],
            ['code' => '37', 'name' => 'Indre-et-Loire'],
            ['code' => '38', 'name' => 'Isère'],
            ['code' => '40', 'name' => 'Landes'],
            ['code' => '42', 'name' => 'Loire'],
            ['code' => '44', 'name' => 'Loire-Atlantique'],
            ['code' => '45', 'name' => 'Loiret'],
            ['code' => '49', 'name' => 'Maine-et-Loire'],
            ['code' => '50', 'name' => 'Manche'],
            ['code' => '51', 'name' => 'Marne'],
            ['code' => '54', 'name' => 'Meurthe-et-Moselle'],
            ['code' => '56', 'name' => 'Morbihan'],
            ['code' => '57', 'name' => 'Moselle'],
            ['code' => '59', 'name' => 'Nord'],
            ['code' => '60', 'name' => 'Oise'],
            ['code' => '62', 'name' => 'Pas-de-Calais'],
            ['code' => '63', 'name' => 'Puy-de-Dôme'],
            ['code' => '64', 'name' => 'Pyrénées-Atlantiques'],
            ['code' => '66', 'name' => 'Pyrénées-Orientales'],
            ['code' => '67', 'name' => 'Bas-Rhin'],
            ['code' => '68', 'name' => 'Haut-Rhin'],
            ['code' => '69', 'name' => 'Rhône'],
            ['code' => '71', 'name' => 'Saône-et-Loire'],
            ['code' => '73', 'name' => 'Savoie'],
            ['code' => '74', 'name' => 'Haute-Savoie'],
            ['code' => '75', 'name' => 'Paris'],
            ['code' => '76', 'name' => 'Seine-Maritime'],
            ['code' => '77', 'name' => 'Seine-et-Marne'],
            ['code' => '78', 'name' => 'Yvelines'],
            ['code' => '80', 'name' => 'Somme'],
            ['code' => '83', 'name' => 'Var'],
            ['code' => '84', 'name' => 'Vaucluse'],
            ['code' => '86', 'name' => 'Vienne'],
            ['code' => '87', 'name' => 'Haute-Vienne'],
            ['code' => '88', 'name' => 'Vosges'],
            ['code' => '91', 'name' => 'Essonne'],
            ['code' => '92', 'name' => 'Hauts-de-Seine'],
            ['code' => '93', 'name' => 'Seine-Saint-Denis'],
            ['code' => '94', 'name' => 'Val-de-Marne'],
            ['code' => '95', 'name' => 'Val-d\'Oise'],
            
            // Corse
            ['code' => '2A', 'name' => 'Corse-du-Sud'],
            ['code' => '2B', 'name' => 'Haute-Corse'],
            
            // DOM-TOM
            ['code' => '971', 'name' => 'Guadeloupe'],
            ['code' => '972', 'name' => 'Martinique'],
            ['code' => '973', 'name' => 'Guyane'],
            ['code' => '974', 'name' => 'La Réunion'],
            ['code' => '976', 'name' => 'Mayotte'],
            ['code' => '984', 'name' => 'Terres australes et antarctiques françaises'],
            ['code' => '986', 'name' => 'Wallis-et-Futuna'],
            ['code' => '987', 'name' => 'Polynésie française'],
            ['code' => '988', 'name' => 'Nouvelle-Calédonie'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}