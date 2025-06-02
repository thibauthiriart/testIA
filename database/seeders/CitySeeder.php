<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // Ain (01)
            ['name' => 'Bourg-en-Bresse', 'postal_code' => '01000', 'population' => 41248],
            ['name' => 'Oyonnax', 'postal_code' => '01100', 'population' => 22653],
            
            // Aisne (02) 
            ['name' => 'Saint-Quentin', 'postal_code' => '02100', 'population' => 53082],
            ['name' => 'Soissons', 'postal_code' => '02200', 'population' => 28530],
            
            // Allier (03)
            ['name' => 'Moulins', 'postal_code' => '03000', 'population' => 19762],
            ['name' => 'Vichy', 'postal_code' => '03200', 'population' => 25279],
            
            // Alpes-Maritimes (06)
            ['name' => 'Nice', 'postal_code' => '06000', 'population' => 340017],
            ['name' => 'Cannes', 'postal_code' => '06400', 'population' => 74152],
            
            // Ardèche (07)
            ['name' => 'Annonay', 'postal_code' => '07100', 'population' => 16292],
            ['name' => 'Aubenas', 'postal_code' => '07200', 'population' => 12479],
            
            // Ardennes (08)
            ['name' => 'Charleville-Mézières', 'postal_code' => '08000', 'population' => 46428],
            ['name' => 'Sedan', 'postal_code' => '08200', 'population' => 16193],
            
            // Aube (10)
            ['name' => 'Troyes', 'postal_code' => '10000', 'population' => 61652],
            ['name' => 'Romilly-sur-Seine', 'postal_code' => '10100', 'population' => 14539],
            
            // Bouches-du-Rhône (13)
            ['name' => 'Marseille', 'postal_code' => '13000', 'population' => 870018],
            ['name' => 'Aix-en-Provence', 'postal_code' => '13100', 'population' => 143097],
            
            // Calvados (14)
            ['name' => 'Caen', 'postal_code' => '14000', 'population' => 105403],
            ['name' => 'Lisieux', 'postal_code' => '14100', 'population' => 20189],
            
            // Cantal (15)
            ['name' => 'Aurillac', 'population' => 25499],
            ['name' => 'Saint-Flour', 'population' => 6643],
            
            // Charente (16)
            ['name' => 'Angoulême', 'population' => 41740],
            ['name' => 'Cognac', 'population' => 18754],
            
            // Charente-Maritime (17)
            ['name' => 'La Rochelle', 'population' => 77196],
            ['name' => 'Saintes', 'population' => 25149],
            
            // Corrèze (19)
            ['name' => 'Brive-la-Gaillarde', 'population' => 46961],
            ['name' => 'Tulle', 'population' => 14812],
            
            // Côte-d'Or (21)
            ['name' => 'Dijon', 'population' => 156920],
            ['name' => 'Beaune', 'population' => 20631],
            
            // Côtes-d'Armor (22)
            ['name' => 'Saint-Brieuc', 'population' => 43605],
            ['name' => 'Lannion', 'population' => 20040],
            
            // Dordogne (24)
            ['name' => 'Périgueux', 'population' => 28848],
            ['name' => 'Bergerac', 'population' => 26823],
            
            // Doubs (25)
            ['name' => 'Besançon', 'population' => 116775],
            ['name' => 'Montbéliard', 'population' => 25395],
            
            // Drôme (26)
            ['name' => 'Valence', 'population' => 64726],
            ['name' => 'Montélimar', 'population' => 39415],
            
            // Eure (27)
            ['name' => 'Évreux', 'population' => 46687],
            ['name' => 'Vernon', 'population' => 23703],
            
            // Finistère (29)
            ['name' => 'Brest', 'population' => 139163],
            ['name' => 'Quimper', 'population' => 62985],
            
            // Gard (30)
            ['name' => 'Nîmes', 'population' => 148104],
            ['name' => 'Alès', 'population' => 41837],
            
            // Haute-Garonne (31)
            ['name' => 'Toulouse', 'population' => 493465],
            ['name' => 'Colomiers', 'population' => 39968],
            
            // Gironde (33)
            ['name' => 'Bordeaux', 'population' => 260958],
            ['name' => 'Mérignac', 'population' => 71349],
            
            // Hérault (34)
            ['name' => 'Montpellier', 'population' => 299096],
            ['name' => 'Béziers', 'population' => 78683],
            
            // Ille-et-Vilaine (35)
            ['name' => 'Rennes', 'population' => 222485],
            ['name' => 'Saint-Malo', 'population' => 46097],
            
            // Indre-et-Loire (37)
            ['name' => 'Tours', 'population' => 136463],
            ['name' => 'Joué-lès-Tours', 'population' => 38250],
            
            // Isère (38)
            ['name' => 'Grenoble', 'population' => 157477],
            ['name' => 'Saint-Martin-d\'Hères', 'population' => 38974],
            
            // Landes (40)
            ['name' => 'Mont-de-Marsan', 'population' => 29807],
            ['name' => 'Dax', 'population' => 20965],
            
            // Loire (42)
            ['name' => 'Saint-Étienne', 'population' => 173089],
            ['name' => 'Roanne', 'population' => 34302],
            
            // Loire-Atlantique (44)
            ['name' => 'Nantes', 'population' => 320732],
            ['name' => 'Saint-Nazaire', 'population' => 72640],
            
            // Loiret (45)
            ['name' => 'Orléans', 'population' => 116269],
            ['name' => 'Olivet', 'population' => 22168],
            
            // Maine-et-Loire (49)
            ['name' => 'Angers', 'population' => 155850],
            ['name' => 'Cholet', 'population' => 54186],
            
            // Manche (50)
            ['name' => 'Cherbourg-en-Cotentin', 'population' => 78549],
            ['name' => 'Saint-Lô', 'population' => 18931],
            
            // Marne (51)
            ['name' => 'Reims', 'population' => 182211],
            ['name' => 'Châlons-en-Champagne', 'population' => 44246],
            
            // Meurthe-et-Moselle (54)
            ['name' => 'Nancy', 'population' => 104260],
            ['name' => 'Vandœuvre-lès-Nancy', 'population' => 29871],
            
            // Morbihan (56)
            ['name' => 'Lorient', 'population' => 57149],
            ['name' => 'Vannes', 'population' => 54020],
            
            // Moselle (57)
            ['name' => 'Metz', 'population' => 117890],
            ['name' => 'Thionville', 'population' => 42001],
            
            // Nord (59)
            ['name' => 'Lille', 'population' => 236234],
            ['name' => 'Roubaix', 'population' => 98828],
            
            // Oise (60)
            ['name' => 'Beauvais', 'population' => 56254],
            ['name' => 'Compiègne', 'population' => 40258],
            
            // Pas-de-Calais (62)
            ['name' => 'Calais', 'population' => 67544],
            ['name' => 'Arras', 'population' => 42030],
            
            // Puy-de-Dôme (63)
            ['name' => 'Clermont-Ferrand', 'population' => 147284],
            ['name' => 'Riom', 'population' => 19610],
            
            // Pyrénées-Atlantiques (64)
            ['name' => 'Pau', 'population' => 75665],
            ['name' => 'Bayonne', 'population' => 52006],
            
            // Pyrénées-Orientales (66)
            ['name' => 'Perpignan', 'population' => 119188],
            ['name' => 'Canet-en-Roussillon', 'population' => 14187],
            
            // Bas-Rhin (67)
            ['name' => 'Strasbourg', 'population' => 287228],
            ['name' => 'Haguenau', 'population' => 35353],
            
            // Haut-Rhin (68)
            ['name' => 'Mulhouse', 'population' => 108038],
            ['name' => 'Colmar', 'population' => 68784],
            
            // Rhône (69)
            ['name' => 'Lyon', 'population' => 522679],
            ['name' => 'Villeurbanne', 'population' => 155027],
            
            // Saône-et-Loire (71)
            ['name' => 'Chalon-sur-Saône', 'population' => 44360],
            ['name' => 'Mâcon', 'population' => 33908],
            
            // Savoie (73)
            ['name' => 'Chambéry', 'population' => 60119],
            ['name' => 'Aix-les-Bains', 'population' => 30291],
            
            // Haute-Savoie (74)
            ['name' => 'Annecy', 'population' => 130721],
            ['name' => 'Thonon-les-Bains', 'population' => 35752],
            
            // Paris (75)
            ['name' => 'Paris', 'population' => 2145906],
            ['name' => 'Paris 15e', 'population' => 233484],
            
            // Seine-Maritime (76)
            ['name' => 'Le Havre', 'population' => 165830],
            ['name' => 'Rouen', 'population' => 112321],
            
            // Seine-et-Marne (77)
            ['name' => 'Meaux', 'population' => 56257],
            ['name' => 'Melun', 'population' => 41415],
            
            // Yvelines (78)
            ['name' => 'Versailles', 'population' => 84808],
            ['name' => 'Sartrouville', 'population' => 51599],
            
            // Somme (80)
            ['name' => 'Amiens', 'population' => 133625],
            ['name' => 'Abbeville', 'population' => 22776],
            
            // Var (83)
            ['name' => 'Toulon', 'population' => 176198],
            ['name' => 'Fréjus', 'population' => 54458],
            
            // Vaucluse (84)
            ['name' => 'Avignon', 'population' => 90109],
            ['name' => 'Orange', 'population' => 28919],
            
            // Vienne (86)
            ['name' => 'Poitiers', 'population' => 90033],
            ['name' => 'Châtellerault', 'population' => 31511],
            
            // Haute-Vienne (87)
            ['name' => 'Limoges', 'population' => 130592],
            ['name' => 'Saint-Junien', 'population' => 11145],
            
            // Vosges (88)
            ['name' => 'Épinal', 'population' => 30777],
            ['name' => 'Saint-Dié-des-Vosges', 'population' => 19724],
            
            // Essonne (91)
            ['name' => 'Évry-Courcouronnes', 'population' => 67131],
            ['name' => 'Massy', 'population' => 50644],
            
            // Hauts-de-Seine (92)
            ['name' => 'Boulogne-Billancourt', 'population' => 121334],
            ['name' => 'Nanterre', 'population' => 96683],
            
            // Seine-Saint-Denis (93)
            ['name' => 'Montreuil', 'population' => 111367],
            ['name' => 'Saint-Denis', 'population' => 113154],
            
            // Val-de-Marne (94)
            ['name' => 'Créteil', 'population' => 92265],
            ['name' => 'Vitry-sur-Seine', 'population' => 94649],
            
            // Val-d'Oise (95)
            ['name' => 'Argenteuil', 'population' => 110388],
            ['name' => 'Cergy', 'population' => 66322],
            
            // Corse-du-Sud (2A)
            ['name' => 'Ajaccio', 'population' => 71361],
            ['name' => 'Porto-Vecchio', 'population' => 11574],
            
            // Haute-Corse (2B)
            ['name' => 'Bastia', 'population' => 48574],
            ['name' => 'Corte', 'population' => 7507],
            
            // Guadeloupe (971)
            ['name' => 'Les Abymes', 'population' => 53836],
            ['name' => 'Pointe-à-Pitre', 'population' => 15410],
            
            // Martinique (972)
            ['name' => 'Fort-de-France', 'population' => 75516],
            ['name' => 'Le Lamentin', 'population' => 40554],
            
            // Guyane (973)
            ['name' => 'Cayenne', 'population' => 65493],
            ['name' => 'Saint-Laurent-du-Maroni', 'population' => 47621],
            
            // La Réunion (974)
            ['name' => 'Saint-Denis', 'population' => 153810],
            ['name' => 'Saint-Pierre', 'population' => 85961],
            
            // Mayotte (976)
            ['name' => 'Mamoudzou', 'population' => 71437],
            ['name' => 'Koungou', 'population' => 32156],
            
            // Terres australes et antarctiques françaises (984)
            ['name' => 'Port-aux-Français', 'population' => 50],
            ['name' => 'Alfred-Faure', 'population' => 25],
            
            // Wallis-et-Futuna (986)
            ['name' => 'Mata-Utu', 'population' => 1029],
            ['name' => 'Leava', 'population' => 479],
            
            // Polynésie française (987)
            ['name' => 'Papeete', 'population' => 26926],
            ['name' => 'Faaa', 'population' => 29506],
            
            // Nouvelle-Calédonie (988)
            ['name' => 'Nouméa', 'population' => 94285],
            ['name' => 'Dumbéa', 'population' => 35873],
        ];

        $departments = Department::all()->keyBy('code');
        
        $cityIndex = 0;
        foreach ($departments as $code => $department) {
            // Ajouter 2 villes par département
            for ($i = 0; $i < 2; $i++) {
                if ($cityIndex < count($cities)) {
                    // Generate code_postal from department code
                    $baseCode = str_pad($code, 2, '0', STR_PAD_LEFT);
                    $codePostal = $baseCode . ($i == 0 ? '000' : '100');
                    
                    // Use explicit code_postal if available
                    if (isset($cities[$cityIndex]['code_postal'])) {
                        $codePostal = $cities[$cityIndex]['code_postal'];
                    }
                    
                    City::create([
                        'name' => $cities[$cityIndex]['name'],
                        'postal_code' => $codePostal,
                        'population' => $cities[$cityIndex]['population'],
                        'department_id' => $department->id,
                    ]);
                    $cityIndex++;
                }
            }
        }
    }
}