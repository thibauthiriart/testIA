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
            ['nom' => 'Bourg-en-Bresse', 'population' => 41248],
            ['nom' => 'Oyonnax', 'population' => 22653],
            
            // Aisne (02) 
            ['nom' => 'Saint-Quentin', 'population' => 53082],
            ['nom' => 'Soissons', 'population' => 28530],
            
            // Allier (03)
            ['nom' => 'Moulins', 'population' => 19762],
            ['nom' => 'Vichy', 'population' => 25279],
            
            // Alpes-Maritimes (06)
            ['nom' => 'Nice', 'population' => 340017],
            ['nom' => 'Cannes', 'population' => 74152],
            
            // Ardèche (07)
            ['nom' => 'Annonay', 'population' => 16292],
            ['nom' => 'Aubenas', 'population' => 12479],
            
            // Ardennes (08)
            ['nom' => 'Charleville-Mézières', 'population' => 46428],
            ['nom' => 'Sedan', 'population' => 16193],
            
            // Aube (10)
            ['nom' => 'Troyes', 'population' => 61652],
            ['nom' => 'Romilly-sur-Seine', 'population' => 14539],
            
            // Bouches-du-Rhône (13)
            ['nom' => 'Marseille', 'population' => 870018],
            ['nom' => 'Aix-en-Provence', 'population' => 143097],
            
            // Calvados (14)
            ['nom' => 'Caen', 'population' => 105403],
            ['nom' => 'Lisieux', 'population' => 20189],
            
            // Cantal (15)
            ['nom' => 'Aurillac', 'population' => 25499],
            ['nom' => 'Saint-Flour', 'population' => 6643],
            
            // Charente (16)
            ['nom' => 'Angoulême', 'population' => 41740],
            ['nom' => 'Cognac', 'population' => 18754],
            
            // Charente-Maritime (17)
            ['nom' => 'La Rochelle', 'population' => 77196],
            ['nom' => 'Saintes', 'population' => 25149],
            
            // Corrèze (19)
            ['nom' => 'Brive-la-Gaillarde', 'population' => 46961],
            ['nom' => 'Tulle', 'population' => 14812],
            
            // Côte-d'Or (21)
            ['nom' => 'Dijon', 'population' => 156920],
            ['nom' => 'Beaune', 'population' => 20631],
            
            // Côtes-d'Armor (22)
            ['nom' => 'Saint-Brieuc', 'population' => 43605],
            ['nom' => 'Lannion', 'population' => 20040],
            
            // Dordogne (24)
            ['nom' => 'Périgueux', 'population' => 28848],
            ['nom' => 'Bergerac', 'population' => 26823],
            
            // Doubs (25)
            ['nom' => 'Besançon', 'population' => 116775],
            ['nom' => 'Montbéliard', 'population' => 25395],
            
            // Drôme (26)
            ['nom' => 'Valence', 'population' => 64726],
            ['nom' => 'Montélimar', 'population' => 39415],
            
            // Eure (27)
            ['nom' => 'Évreux', 'population' => 46687],
            ['nom' => 'Vernon', 'population' => 23703],
            
            // Finistère (29)
            ['nom' => 'Brest', 'population' => 139163],
            ['nom' => 'Quimper', 'population' => 62985],
            
            // Gard (30)
            ['nom' => 'Nîmes', 'population' => 148104],
            ['nom' => 'Alès', 'population' => 41837],
            
            // Haute-Garonne (31)
            ['nom' => 'Toulouse', 'population' => 493465],
            ['nom' => 'Colomiers', 'population' => 39968],
            
            // Gironde (33)
            ['nom' => 'Bordeaux', 'population' => 260958],
            ['nom' => 'Mérignac', 'population' => 71349],
            
            // Hérault (34)
            ['nom' => 'Montpellier', 'population' => 299096],
            ['nom' => 'Béziers', 'population' => 78683],
            
            // Ille-et-Vilaine (35)
            ['nom' => 'Rennes', 'population' => 222485],
            ['nom' => 'Saint-Malo', 'population' => 46097],
            
            // Indre-et-Loire (37)
            ['nom' => 'Tours', 'population' => 136463],
            ['nom' => 'Joué-lès-Tours', 'population' => 38250],
            
            // Isère (38)
            ['nom' => 'Grenoble', 'population' => 157477],
            ['nom' => 'Saint-Martin-d\'Hères', 'population' => 38974],
            
            // Landes (40)
            ['nom' => 'Mont-de-Marsan', 'population' => 29807],
            ['nom' => 'Dax', 'population' => 20965],
            
            // Loire (42)
            ['nom' => 'Saint-Étienne', 'population' => 173089],
            ['nom' => 'Roanne', 'population' => 34302],
            
            // Loire-Atlantique (44)
            ['nom' => 'Nantes', 'population' => 320732],
            ['nom' => 'Saint-Nazaire', 'population' => 72640],
            
            // Loiret (45)
            ['nom' => 'Orléans', 'population' => 116269],
            ['nom' => 'Olivet', 'population' => 22168],
            
            // Maine-et-Loire (49)
            ['nom' => 'Angers', 'population' => 155850],
            ['nom' => 'Cholet', 'population' => 54186],
            
            // Manche (50)
            ['nom' => 'Cherbourg-en-Cotentin', 'population' => 78549],
            ['nom' => 'Saint-Lô', 'population' => 18931],
            
            // Marne (51)
            ['nom' => 'Reims', 'population' => 182211],
            ['nom' => 'Châlons-en-Champagne', 'population' => 44246],
            
            // Meurthe-et-Moselle (54)
            ['nom' => 'Nancy', 'population' => 104260],
            ['nom' => 'Vandœuvre-lès-Nancy', 'population' => 29871],
            
            // Morbihan (56)
            ['nom' => 'Lorient', 'population' => 57149],
            ['nom' => 'Vannes', 'population' => 54020],
            
            // Moselle (57)
            ['nom' => 'Metz', 'population' => 117890],
            ['nom' => 'Thionville', 'population' => 42001],
            
            // Nord (59)
            ['nom' => 'Lille', 'population' => 236234],
            ['nom' => 'Roubaix', 'population' => 98828],
            
            // Oise (60)
            ['nom' => 'Beauvais', 'population' => 56254],
            ['nom' => 'Compiègne', 'population' => 40258],
            
            // Pas-de-Calais (62)
            ['nom' => 'Calais', 'population' => 67544],
            ['nom' => 'Arras', 'population' => 42030],
            
            // Puy-de-Dôme (63)
            ['nom' => 'Clermont-Ferrand', 'population' => 147284],
            ['nom' => 'Riom', 'population' => 19610],
            
            // Pyrénées-Atlantiques (64)
            ['nom' => 'Pau', 'population' => 75665],
            ['nom' => 'Bayonne', 'population' => 52006],
            
            // Pyrénées-Orientales (66)
            ['nom' => 'Perpignan', 'population' => 119188],
            ['nom' => 'Canet-en-Roussillon', 'population' => 14187],
            
            // Bas-Rhin (67)
            ['nom' => 'Strasbourg', 'population' => 287228],
            ['nom' => 'Haguenau', 'population' => 35353],
            
            // Haut-Rhin (68)
            ['nom' => 'Mulhouse', 'population' => 108038],
            ['nom' => 'Colmar', 'population' => 68784],
            
            // Rhône (69)
            ['nom' => 'Lyon', 'population' => 522679],
            ['nom' => 'Villeurbanne', 'population' => 155027],
            
            // Saône-et-Loire (71)
            ['nom' => 'Chalon-sur-Saône', 'population' => 44360],
            ['nom' => 'Mâcon', 'population' => 33908],
            
            // Savoie (73)
            ['nom' => 'Chambéry', 'population' => 60119],
            ['nom' => 'Aix-les-Bains', 'population' => 30291],
            
            // Haute-Savoie (74)
            ['nom' => 'Annecy', 'population' => 130721],
            ['nom' => 'Thonon-les-Bains', 'population' => 35752],
            
            // Paris (75)
            ['nom' => 'Paris', 'population' => 2145906],
            ['nom' => 'Paris 15e', 'population' => 233484],
            
            // Seine-Maritime (76)
            ['nom' => 'Le Havre', 'population' => 165830],
            ['nom' => 'Rouen', 'population' => 112321],
            
            // Seine-et-Marne (77)
            ['nom' => 'Meaux', 'population' => 56257],
            ['nom' => 'Melun', 'population' => 41415],
            
            // Yvelines (78)
            ['nom' => 'Versailles', 'population' => 84808],
            ['nom' => 'Sartrouville', 'population' => 51599],
            
            // Somme (80)
            ['nom' => 'Amiens', 'population' => 133625],
            ['nom' => 'Abbeville', 'population' => 22776],
            
            // Var (83)
            ['nom' => 'Toulon', 'population' => 176198],
            ['nom' => 'Fréjus', 'population' => 54458],
            
            // Vaucluse (84)
            ['nom' => 'Avignon', 'population' => 90109],
            ['nom' => 'Orange', 'population' => 28919],
            
            // Vienne (86)
            ['nom' => 'Poitiers', 'population' => 90033],
            ['nom' => 'Châtellerault', 'population' => 31511],
            
            // Haute-Vienne (87)
            ['nom' => 'Limoges', 'population' => 130592],
            ['nom' => 'Saint-Junien', 'population' => 11145],
            
            // Vosges (88)
            ['nom' => 'Épinal', 'population' => 30777],
            ['nom' => 'Saint-Dié-des-Vosges', 'population' => 19724],
            
            // Essonne (91)
            ['nom' => 'Évry-Courcouronnes', 'population' => 67131],
            ['nom' => 'Massy', 'population' => 50644],
            
            // Hauts-de-Seine (92)
            ['nom' => 'Boulogne-Billancourt', 'population' => 121334],
            ['nom' => 'Nanterre', 'population' => 96683],
            
            // Seine-Saint-Denis (93)
            ['nom' => 'Montreuil', 'population' => 111367],
            ['nom' => 'Saint-Denis', 'population' => 113154],
            
            // Val-de-Marne (94)
            ['nom' => 'Créteil', 'population' => 92265],
            ['nom' => 'Vitry-sur-Seine', 'population' => 94649],
            
            // Val-d'Oise (95)
            ['nom' => 'Argenteuil', 'population' => 110388],
            ['nom' => 'Cergy', 'population' => 66322],
            
            // Corse-du-Sud (2A)
            ['nom' => 'Ajaccio', 'population' => 71361],
            ['nom' => 'Porto-Vecchio', 'population' => 11574],
            
            // Haute-Corse (2B)
            ['nom' => 'Bastia', 'population' => 48574],
            ['nom' => 'Corte', 'population' => 7507],
            
            // Guadeloupe (971)
            ['nom' => 'Les Abymes', 'population' => 53836],
            ['nom' => 'Pointe-à-Pitre', 'population' => 15410],
            
            // Martinique (972)
            ['nom' => 'Fort-de-France', 'population' => 75516],
            ['nom' => 'Le Lamentin', 'population' => 40554],
            
            // Guyane (973)
            ['nom' => 'Cayenne', 'population' => 65493],
            ['nom' => 'Saint-Laurent-du-Maroni', 'population' => 47621],
            
            // La Réunion (974)
            ['nom' => 'Saint-Denis', 'population' => 153810],
            ['nom' => 'Saint-Pierre', 'population' => 85961],
            
            // Mayotte (976)
            ['nom' => 'Mamoudzou', 'population' => 71437],
            ['nom' => 'Koungou', 'population' => 32156],
            
            // Terres australes et antarctiques françaises (984)
            ['nom' => 'Port-aux-Français', 'population' => 50],
            ['nom' => 'Alfred-Faure', 'population' => 25],
            
            // Wallis-et-Futuna (986)
            ['nom' => 'Mata-Utu', 'population' => 1029],
            ['nom' => 'Leava', 'population' => 479],
            
            // Polynésie française (987)
            ['nom' => 'Papeete', 'population' => 26926],
            ['nom' => 'Faaa', 'population' => 29506],
            
            // Nouvelle-Calédonie (988)
            ['nom' => 'Nouméa', 'population' => 94285],
            ['nom' => 'Dumbéa', 'population' => 35873],
        ];

        $departments = Department::all()->keyBy('code');
        
        $cityIndex = 0;
        foreach ($departments as $code => $department) {
            // Ajouter 2 villes par département
            for ($i = 0; $i < 2; $i++) {
                if ($cityIndex < count($cities)) {
                    City::create([
                        'nom' => $cities[$cityIndex]['nom'],
                        'population' => $cities[$cityIndex]['population'],
                        'department_id' => $department->id,
                    ]);
                    $cityIndex++;
                }
            }
        }
    }
}