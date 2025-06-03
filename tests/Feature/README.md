# Structure des Tests Feature

## Organisation

Les tests sont organisés dans les dossiers suivants :

### `/Auth`
Tests liés à l'authentification et aux rôles :
- `RoleAccessTest.php` : Tests complets d'accès selon les rôles (admin/user)
- `DepartmentRoleAccessTest.php` : Tests spécifiques aux rôles pour les départements
- `SimpleRoleTest.php` : Tests simples du middleware de rôles

### `/Controllers`
Tests des contrôleurs :
- `CityControllerTest.php` : Tests CRUD de base pour les villes
- `CityControllerAdvancedTest.php` : Tests avancés (validations complexes, cas limites)
- `DepartmentControllerTest.php` : Tests CRUD de base pour les départements
- `DepartmentControllerAdvancedTest.php` : Tests avancés pour les départements

### `/Validation`
Tests de validation et cas limites :
- `ValidationEdgeCasesTest.php` : Tests des cas limites de validation (injections SQL, XSS, caractères spéciaux, etc.)

## Conventions

- Les tests de base (`*ControllerTest.php`) couvrent les opérations CRUD standard
- Les tests avancés (`*ControllerAdvancedTest.php`) couvrent les cas limites et validations complexes
- Les tests de rôles vérifient que seuls les admins peuvent accéder aux ressources protégées
- Tous les tests utilisent `RefreshDatabase` pour une base de données propre

## Exécution

Pour exécuter tous les tests :
```bash
php artisan test
```

Pour exécuter un dossier spécifique :
```bash
php artisan test tests/Feature/Controllers
php artisan test tests/Feature/Auth
php artisan test tests/Feature/Validation
```

Pour exécuter un test spécifique :
```bash
php artisan test tests/Feature/Controllers/CityControllerTest.php
```