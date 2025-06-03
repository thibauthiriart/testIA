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
            ['name' => 'Bourg-en-Bresse', 'postal_code' => '01000', 'population' => 41248, 'latitude' => 46.2047, 'longitude' => 5.2256],
            ['name' => 'Oyonnax', 'postal_code' => '01100', 'population' => 22653, 'latitude' => 46.2561, 'longitude' => 5.6553],
            
            // Aisne (02) 
            ['name' => 'Saint-Quentin', 'postal_code' => '02100', 'population' => 53082, 'latitude' => 49.8467, 'longitude' => 3.2872],
            ['name' => 'Soissons', 'postal_code' => '02200', 'population' => 28530, 'latitude' => 49.3814, 'longitude' => 3.3236],
            
            // Allier (03)
            ['name' => 'Moulins', 'postal_code' => '03000', 'population' => 19762, 'latitude' => 46.5683, 'longitude' => 3.3333],
            ['name' => 'Vichy', 'postal_code' => '03200', 'population' => 25279, 'latitude' => 46.1281, 'longitude' => 3.4242],
            
            // Alpes-Maritimes (06)
            ['name' => 'Nice', 'postal_code' => '06000', 'population' => 340017, 'latitude' => 43.7102, 'longitude' => 7.2620],
            ['name' => 'Cannes', 'postal_code' => '06400', 'population' => 74152, 'latitude' => 43.5528, 'longitude' => 7.0174],
            
            // Ardèche (07)
            ['name' => 'Annonay', 'postal_code' => '07100', 'population' => 16292, 'latitude' => 45.2394, 'longitude' => 4.6694],
            ['name' => 'Aubenas', 'postal_code' => '07200', 'population' => 12479, 'latitude' => 44.6203, 'longitude' => 4.3900],
            
            // Ardennes (08)
            ['name' => 'Charleville-Mézières', 'postal_code' => '08000', 'population' => 46428, 'latitude' => 49.7632, 'longitude' => 4.7197],
            ['name' => 'Sedan', 'postal_code' => '08200', 'population' => 16193, 'latitude' => 49.7019, 'longitude' => 4.9394],
            
            // Aube (10)
            ['name' => 'Troyes', 'postal_code' => '10000', 'population' => 61652, 'latitude' => 48.2973, 'longitude' => 4.0744],
            ['name' => 'Romilly-sur-Seine', 'postal_code' => '10100', 'population' => 14539, 'latitude' => 48.5147, 'longitude' => 3.7269],
            
            // Bouches-du-Rhône (13)
            ['name' => 'Marseille', 'postal_code' => '13000', 'population' => 870018, 'latitude' => 43.2965, 'longitude' => 5.3698],
            ['name' => 'Aix-en-Provence', 'postal_code' => '13100', 'population' => 143097, 'latitude' => 43.5297, 'longitude' => 5.4473],
            
            // Calvados (14)
            ['name' => 'Caen', 'postal_code' => '14000', 'population' => 105403, 'latitude' => 49.1829, 'longitude' => -0.3707],
            ['name' => 'Lisieux', 'postal_code' => '14100', 'population' => 20189, 'latitude' => 49.1439, 'longitude' => 0.2281],
            
            // Cantal (15)
            ['name' => 'Aurillac', 'population' => 25499, 'latitude' => 44.9317, 'longitude' => 2.4439],
            ['name' => 'Saint-Flour', 'population' => 6643, 'latitude' => 45.0347, 'longitude' => 3.0947],
            
            // Charente (16)
            ['name' => 'Angoulême', 'population' => 41740, 'latitude' => 45.6500, 'longitude' => 0.1600],
            ['name' => 'Cognac', 'population' => 18754, 'latitude' => 45.6967, 'longitude' => -0.3300],
            
            // Charente-Maritime (17)
            ['name' => 'La Rochelle', 'population' => 77196, 'latitude' => 46.1603, 'longitude' => -1.1511],
            ['name' => 'Saintes', 'population' => 25149, 'latitude' => 45.7467, 'longitude' => -0.6333],
            
            // Corrèze (19)
            ['name' => 'Brive-la-Gaillarde', 'population' => 46961, 'latitude' => 45.1581, 'longitude' => 1.5331],
            ['name' => 'Tulle', 'population' => 14812, 'latitude' => 45.2667, 'longitude' => 1.7703],
            
            // Côte-d'Or (21)
            ['name' => 'Dijon', 'population' => 156920, 'latitude' => 47.3220, 'longitude' => 5.0414],
            ['name' => 'Beaune', 'population' => 20631, 'latitude' => 47.0236, 'longitude' => 4.8372],
            
            // Côtes-d'Armor (22)
            ['name' => 'Saint-Brieuc', 'population' => 43605, 'latitude' => 48.5147, 'longitude' => -2.7661],
            ['name' => 'Lannion', 'population' => 20040, 'latitude' => 48.7322, 'longitude' => -3.4597],
            
            // Dordogne (24)
            ['name' => 'Périgueux', 'population' => 28848, 'latitude' => 45.1844, 'longitude' => 0.7214],
            ['name' => 'Bergerac', 'population' => 26823, 'latitude' => 44.8533, 'longitude' => 0.4839],
            
            // Doubs (25)
            ['name' => 'Besançon', 'population' => 116775, 'latitude' => 47.2378, 'longitude' => 6.0241],
            ['name' => 'Montbéliard', 'population' => 25395, 'latitude' => 47.5103, 'longitude' => 6.7986],
            
            // Drôme (26)
            ['name' => 'Valence', 'population' => 64726, 'latitude' => 44.9333, 'longitude' => 4.8914],
            ['name' => 'Montélimar', 'population' => 39415, 'latitude' => 44.5581, 'longitude' => 4.7508],
            
            // Eure (27)
            ['name' => 'Évreux', 'population' => 46687, 'latitude' => 49.0242, 'longitude' => 1.1511],
            ['name' => 'Vernon', 'population' => 23703, 'latitude' => 49.0939, 'longitude' => 1.4844],
            
            // Finistère (29)
            ['name' => 'Brest', 'population' => 139163, 'latitude' => 48.3900, 'longitude' => -4.4864],
            ['name' => 'Quimper', 'population' => 62985, 'latitude' => 47.9956, 'longitude' => -4.1028],
            
            // Gard (30)
            ['name' => 'Nîmes', 'population' => 148104, 'latitude' => 43.8367, 'longitude' => 4.3606],
            ['name' => 'Alès', 'population' => 41837, 'latitude' => 44.1253, 'longitude' => 4.0811],
            
            // Haute-Garonne (31)
            ['name' => 'Toulouse', 'population' => 493465, 'latitude' => 43.6047, 'longitude' => 1.4442],
            ['name' => 'Colomiers', 'population' => 39968, 'latitude' => 43.6111, 'longitude' => 1.3331],
            
            // Gironde (33)
            ['name' => 'Bordeaux', 'population' => 260958, 'latitude' => 44.8378, 'longitude' => -0.5792],
            ['name' => 'Mérignac', 'population' => 71349, 'latitude' => 44.8344, 'longitude' => -0.6567],
            
            // Hérault (34)
            ['name' => 'Montpellier', 'population' => 299096, 'latitude' => 43.6108, 'longitude' => 3.8767],
            ['name' => 'Béziers', 'population' => 78683, 'latitude' => 43.3431, 'longitude' => 3.2156],
            
            // Ille-et-Vilaine (35)
            ['name' => 'Rennes', 'population' => 222485, 'latitude' => 48.1173, 'longitude' => -1.6778],
            ['name' => 'Saint-Malo', 'population' => 46097, 'latitude' => 48.6489, 'longitude' => -2.0256],
            
            // Indre-et-Loire (37)
            ['name' => 'Tours', 'population' => 136463, 'latitude' => 47.3941, 'longitude' => 0.6848],
            ['name' => 'Joué-lès-Tours', 'population' => 38250, 'latitude' => 47.3531, 'longitude' => 0.6639],
            
            // Isère (38)
            ['name' => 'Grenoble', 'population' => 157477, 'latitude' => 45.1885, 'longitude' => 5.7245],
            ['name' => 'Saint-Martin-d\'Hères', 'population' => 38974, 'latitude' => 45.1667, 'longitude' => 5.7667],
            
            // Landes (40)
            ['name' => 'Mont-de-Marsan', 'population' => 29807, 'latitude' => 43.8939, 'longitude' => -0.5014],
            ['name' => 'Dax', 'population' => 20965, 'latitude' => 43.7097, 'longitude' => -1.0547],
            
            // Loire (42)
            ['name' => 'Saint-Étienne', 'population' => 173089, 'latitude' => 45.4397, 'longitude' => 4.3872],
            ['name' => 'Roanne', 'population' => 34302, 'latitude' => 46.0331, 'longitude' => 4.0686],
            
            // Loire-Atlantique (44)
            ['name' => 'Nantes', 'population' => 320732, 'latitude' => 47.2184, 'longitude' => -1.5536],
            ['name' => 'Saint-Nazaire', 'population' => 72640, 'latitude' => 47.2736, 'longitude' => -2.2131],
            
            // Loiret (45)
            ['name' => 'Orléans', 'population' => 116269, 'latitude' => 47.9022, 'longitude' => 1.9036],
            ['name' => 'Olivet', 'population' => 22168, 'latitude' => 47.8667, 'longitude' => 1.9000],
            
            // Maine-et-Loire (49)
            ['name' => 'Angers', 'population' => 155850, 'latitude' => 47.4784, 'longitude' => -0.5632],
            ['name' => 'Cholet', 'population' => 54186, 'latitude' => 47.0606, 'longitude' => -0.8789],
            
            // Manche (50)
            ['name' => 'Cherbourg-en-Cotentin', 'population' => 78549, 'latitude' => 49.6403, 'longitude' => -1.6161],
            ['name' => 'Saint-Lô', 'population' => 18931, 'latitude' => 49.1156, 'longitude' => -1.0914],
            
            // Marne (51)
            ['name' => 'Reims', 'population' => 182211, 'latitude' => 49.2583, 'longitude' => 4.0317],
            ['name' => 'Châlons-en-Champagne', 'population' => 44246, 'latitude' => 48.9575, 'longitude' => 4.3678],
            
            // Meurthe-et-Moselle (54)
            ['name' => 'Nancy', 'population' => 104260, 'latitude' => 48.6921, 'longitude' => 6.1844],
            ['name' => 'Vandœuvre-lès-Nancy', 'population' => 29871, 'latitude' => 48.6567, 'longitude' => 6.1797],
            
            // Morbihan (56)
            ['name' => 'Lorient', 'population' => 57149, 'latitude' => 47.7483, 'longitude' => -3.3661],
            ['name' => 'Vannes', 'population' => 54020, 'latitude' => 47.6581, 'longitude' => -2.7606],
            
            // Moselle (57)
            ['name' => 'Metz', 'population' => 117890, 'latitude' => 49.1193, 'longitude' => 6.1757],
            ['name' => 'Thionville', 'population' => 42001, 'latitude' => 49.3578, 'longitude' => 6.1681],
            
            // Nord (59)
            ['name' => 'Lille', 'population' => 236234, 'latitude' => 50.6292, 'longitude' => 3.0573],
            ['name' => 'Roubaix', 'population' => 98828, 'latitude' => 50.6942, 'longitude' => 3.1747],
            
            // Oise (60)
            ['name' => 'Beauvais', 'population' => 56254, 'latitude' => 49.4294, 'longitude' => 2.0811],
            ['name' => 'Compiègne', 'population' => 40258, 'latitude' => 49.4175, 'longitude' => 2.8256],
            
            // Pas-de-Calais (62)
            ['name' => 'Calais', 'population' => 67544, 'latitude' => 50.9481, 'longitude' => 1.8581],
            ['name' => 'Arras', 'population' => 42030, 'latitude' => 50.2892, 'longitude' => 2.7733],
            
            // Puy-de-Dôme (63)
            ['name' => 'Clermont-Ferrand', 'population' => 147284, 'latitude' => 45.7797, 'longitude' => 3.0892],
            ['name' => 'Riom', 'population' => 19610, 'latitude' => 45.8944, 'longitude' => 3.1122],
            
            // Pyrénées-Atlantiques (64)
            ['name' => 'Pau', 'population' => 75665, 'latitude' => 43.2951, 'longitude' => -0.3708],
            ['name' => 'Bayonne', 'population' => 52006, 'latitude' => 43.4833, 'longitude' => -1.4833],
            
            // Pyrénées-Orientales (66)
            ['name' => 'Perpignan', 'population' => 119188, 'latitude' => 42.6886, 'longitude' => 2.8948],
            ['name' => 'Canet-en-Roussillon', 'population' => 14187, 'latitude' => 42.6989, 'longitude' => 3.0381],
            
            // Bas-Rhin (67)
            ['name' => 'Strasbourg', 'population' => 287228, 'latitude' => 48.5734, 'longitude' => 7.7521],
            ['name' => 'Haguenau', 'population' => 35353, 'latitude' => 48.8144, 'longitude' => 7.7906],
            
            // Haut-Rhin (68)
            ['name' => 'Mulhouse', 'population' => 108038, 'latitude' => 47.7508, 'longitude' => 7.3358],
            ['name' => 'Colmar', 'population' => 68784, 'latitude' => 48.0794, 'longitude' => 7.3589],
            
            // Rhône (69)
            ['name' => 'Lyon', 'population' => 522679, 'latitude' => 45.7640, 'longitude' => 4.8357],
            ['name' => 'Villeurbanne', 'population' => 155027, 'latitude' => 45.7667, 'longitude' => 4.8833],
            
            // Saône-et-Loire (71)
            ['name' => 'Chalon-sur-Saône', 'population' => 44360, 'latitude' => 46.7806, 'longitude' => 4.8553],
            ['name' => 'Mâcon', 'population' => 33908, 'latitude' => 46.3067, 'longitude' => 4.8281],
            
            // Savoie (73)
            ['name' => 'Chambéry', 'population' => 60119, 'latitude' => 45.5646, 'longitude' => 5.9178],
            ['name' => 'Aix-les-Bains', 'population' => 30291, 'latitude' => 45.6889, 'longitude' => 5.9153],
            
            // Haute-Savoie (74)
            ['name' => 'Annecy', 'population' => 130721, 'latitude' => 45.8992, 'longitude' => 6.1294],
            ['name' => 'Thonon-les-Bains', 'population' => 35752, 'latitude' => 46.3714, 'longitude' => 6.4789],
            
            // Paris (75)
            ['name' => 'Paris', 'population' => 2145906, 'latitude' => 48.8566, 'longitude' => 2.3522],
            ['name' => 'Paris 15e', 'population' => 233484, 'latitude' => 48.8400, 'longitude' => 2.3000],
            
            // Seine-Maritime (76)
            ['name' => 'Le Havre', 'population' => 165830, 'latitude' => 49.4944, 'longitude' => 0.1079],
            ['name' => 'Rouen', 'population' => 112321, 'latitude' => 49.4431, 'longitude' => 1.0993],
            
            // Seine-et-Marne (77)
            ['name' => 'Meaux', 'population' => 56257, 'latitude' => 48.9608, 'longitude' => 2.8789],
            ['name' => 'Melun', 'population' => 41415, 'latitude' => 48.5392, 'longitude' => 2.6661],
            
            // Yvelines (78)
            ['name' => 'Versailles', 'population' => 84808, 'latitude' => 48.8047, 'longitude' => 2.1204],
            ['name' => 'Sartrouville', 'population' => 51599, 'latitude' => 48.9394, 'longitude' => 2.1594],
            
            // Somme (80)
            ['name' => 'Amiens', 'population' => 133625, 'latitude' => 49.8941, 'longitude' => 2.2958],
            ['name' => 'Abbeville', 'population' => 22776, 'latitude' => 50.1056, 'longitude' => 1.8347],
            
            // Var (83)
            ['name' => 'Toulon', 'population' => 176198, 'latitude' => 43.1242, 'longitude' => 5.9280],
            ['name' => 'Fréjus', 'population' => 54458, 'latitude' => 43.4331, 'longitude' => 6.7369],
            
            // Vaucluse (84)
            ['name' => 'Avignon', 'population' => 90109, 'latitude' => 43.9493, 'longitude' => 4.8059],
            ['name' => 'Orange', 'population' => 28919, 'latitude' => 44.1364, 'longitude' => 4.8081],
            
            // Vienne (86)
            ['name' => 'Poitiers', 'population' => 90033, 'latitude' => 46.5802, 'longitude' => 0.3404],
            ['name' => 'Châtellerault', 'population' => 31511, 'latitude' => 46.8175, 'longitude' => 0.5464],
            
            // Haute-Vienne (87)
            ['name' => 'Limoges', 'population' => 130592, 'latitude' => 45.8336, 'longitude' => 1.2611],
            ['name' => 'Saint-Junien', 'population' => 11145, 'latitude' => 45.8864, 'longitude' => 0.9014],
            
            // Vosges (88)
            ['name' => 'Épinal', 'population' => 30777, 'latitude' => 48.1719, 'longitude' => 6.4506],
            ['name' => 'Saint-Dié-des-Vosges', 'population' => 19724, 'latitude' => 48.2897, 'longitude' => 6.9469],
            
            // Essonne (91)
            ['name' => 'Évry-Courcouronnes', 'population' => 67131, 'latitude' => 48.6278, 'longitude' => 2.4422],
            ['name' => 'Massy', 'population' => 50644, 'latitude' => 48.7306, 'longitude' => 2.2869],
            
            // Hauts-de-Seine (92)
            ['name' => 'Boulogne-Billancourt', 'population' => 121334, 'latitude' => 48.8356, 'longitude' => 2.2419],
            ['name' => 'Nanterre', 'population' => 96683, 'latitude' => 48.8922, 'longitude' => 2.2069],
            
            // Seine-Saint-Denis (93)
            ['name' => 'Montreuil', 'population' => 111367, 'latitude' => 48.8642, 'longitude' => 2.4422],
            ['name' => 'Saint-Denis', 'population' => 113154, 'latitude' => 48.9356, 'longitude' => 2.3539],
            
            // Val-de-Marne (94)
            ['name' => 'Créteil', 'population' => 92265, 'latitude' => 48.7900, 'longitude' => 2.4553],
            ['name' => 'Vitry-sur-Seine', 'population' => 94649, 'latitude' => 48.7872, 'longitude' => 2.4033],
            
            // Val-d'Oise (95)
            ['name' => 'Argenteuil', 'population' => 110388, 'latitude' => 48.9478, 'longitude' => 2.2486],
            ['name' => 'Cergy', 'population' => 66322, 'latitude' => 49.0400, 'longitude' => 2.0775],
            
            // Corse-du-Sud (2A)
            ['name' => 'Ajaccio', 'population' => 71361, 'latitude' => 41.9278, 'longitude' => 8.7369],
            ['name' => 'Porto-Vecchio', 'population' => 11574, 'latitude' => 41.5911, 'longitude' => 9.2806],
            
            // Haute-Corse (2B)
            ['name' => 'Bastia', 'population' => 48574, 'latitude' => 42.7028, 'longitude' => 9.4508],
            ['name' => 'Corte', 'population' => 7507, 'latitude' => 42.3058, 'longitude' => 9.1506],
            
            // Guadeloupe (971)
            ['name' => 'Les Abymes', 'population' => 53836, 'latitude' => 16.2669, 'longitude' => -61.5100],
            ['name' => 'Pointe-à-Pitre', 'population' => 15410, 'latitude' => 16.2411, 'longitude' => -61.5339],
            
            // Martinique (972)
            ['name' => 'Fort-de-France', 'population' => 75516, 'latitude' => 14.6037, 'longitude' => -61.0594],
            ['name' => 'Le Lamentin', 'population' => 40554, 'latitude' => 14.6011, 'longitude' => -60.9994],
            
            // Guyane (973)
            ['name' => 'Cayenne', 'population' => 65493, 'latitude' => 4.9331, 'longitude' => -52.3264],
            ['name' => 'Saint-Laurent-du-Maroni', 'population' => 47621, 'latitude' => 5.5000, 'longitude' => -54.0333],
            
            // La Réunion (974)
            ['name' => 'Saint-Denis', 'population' => 153810, 'latitude' => -20.8823, 'longitude' => 55.4504],
            ['name' => 'Saint-Pierre', 'population' => 85961, 'latitude' => -21.3393, 'longitude' => 55.4781],
            
            // Mayotte (976)
            ['name' => 'Mamoudzou', 'population' => 71437, 'latitude' => -12.7806, 'longitude' => 45.2278],
            ['name' => 'Koungou', 'population' => 32156, 'latitude' => -12.7333, 'longitude' => 45.2042],
            
            // Terres australes et antarctiques françaises (984)
            ['name' => 'Port-aux-Français', 'population' => 50, 'latitude' => -49.3506, 'longitude' => 70.2167],
            ['name' => 'Alfred-Faure', 'population' => 25, 'latitude' => -46.4306, 'longitude' => 51.8533],
            
            // Wallis-et-Futuna (986)
            ['name' => 'Mata-Utu', 'population' => 1029, 'latitude' => -13.2817, 'longitude' => -176.1761],
            ['name' => 'Leava', 'population' => 479, 'latitude' => -14.2939, 'longitude' => -178.1300],
            
            // Polynésie française (987)
            ['name' => 'Papeete', 'population' => 26926, 'latitude' => -17.5516, 'longitude' => -149.5585],
            ['name' => 'Faaa', 'population' => 29506, 'latitude' => -17.5561, 'longitude' => -149.6089],
            
            // Nouvelle-Calédonie (988)
            ['name' => 'Nouméa', 'population' => 94285, 'latitude' => -22.2758, 'longitude' => 166.4581],
            ['name' => 'Dumbéa', 'population' => 35873, 'latitude' => -22.1500, 'longitude' => 166.4667],
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
                        'latitude' => $cities[$cityIndex]['latitude'] ?? null,
                        'longitude' => $cities[$cityIndex]['longitude'] ?? null,
                    ]);
                    $cityIndex++;
                }
            }
        }
    }
}