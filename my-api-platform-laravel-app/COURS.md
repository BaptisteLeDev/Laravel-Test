# COURS Laravel API (Version Resumee)

## 1. Routes: role et structure
Un fichier de routes dit a Laravel: "si telle URL arrive, appelle telle methode de tel controleur".

Exemple dans ce projet:
- `routes/api.php`
- Prefixe global: `/api`

Structure conseillee:
- Routes publiques (auth de base): register/login
- Routes protegees (`auth:sanctum`): logout/me + CRUD metier

```php
Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('chats', ChatController::class);
    Route::apiResource('ecoles', EcoleController::class);
    Route::apiResource('ecoles.classes', ClasseController::class)->scoped();
    Route::apiResource('users', UserController::class);
    Route::patch('users/{user}/role', [UserController::class, 'updateRole']);
});
```

Points cle:
- `prefix('auth')`: groupe les URL sous `/api/auth/...`
- `middleware('auth:sanctum')`: token obligatoire
- `apiResource`: cree automatiquement index/store/show/update/destroy
- `ecoles.classes`: ressource imbriquee (une classe appartient a une ecole)

## 2. ORM Eloquent: ce que ca t'apporte
L'ORM transforme les tables SQL en objets PHP.

- Table `users` <-> modele `User`
- Table `ecoles` <-> modele `Ecole`
- Table `classes` <-> modele `Classe`
- Table `chats` <-> modele `Chat`

Exemples:
```php
$users = User::where('type', 'etudiant')->get();
$ecole = Ecole::findOrFail(1);
$classes = $ecole->classes;
```

## 3. Relations Eloquent (dans ton projet)
- `Ecole hasMany Classe`
- `Classe belongsTo Ecole`
- `Classe hasMany User` (eleves)
- `User belongsTo Classe` (optionnel via `classe_id`)
- `Classe belongsToMany Formation` via `classe_formation`
- `User hasMany Progression`
- `Chat belongsTo User`

Tu manipules alors des relations lisibles:
```php
$ecole->classes;
$classe->eleves;
$classe->formations;
$user->progressions;
```

## 4. API Platform: quand l'utiliser
API Platform genere des endpoints automatiquement depuis `#[ApiResource]`.

Regle pratique:
- CRUD standard simple: API Platform
- Logique metier specifique (auth, role, chat IA): routes + controleurs manuels

Important:
- Eviter les doublons de routes entre API Platform et `Route::apiResource(...)` sur la meme ressource.

## 5. Migrations: versionner la base
Une migration = un changement de schema versionne.

- `up()`: applique
- `down()`: annule

Commandes utiles:
```bash
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh
php artisan migrate:status
php artisan make:migration nom_de_la_migration
```

Dans ce projet, la table `chats` est creee par:
- `database/migrations/2026_03_11_124427_create_chats_table.php`

## 6. Form Requests (bonne pratique)
Un Form Request sert a:
- valider les donnees
- centraliser les regles
- alleger les controleurs

Pattern:
```php
public function update(UpdateUserRequest $request, User $user)
{
    $user->update($request->validated());
    return response()->json($user);
}
```

## 7. Controleurs: responsabilite claire
Un controleur:
- recoit la requete
- appelle le modele/relations
- renvoie une `JsonResponse`

Codes HTTP conseilles:
- `200` lecture/update
- `201` creation
- `204` suppression sans contenu
- `401/403/404/422` selon erreur auth/droit/not found/validation

## 8. Checklist coherence rapide
Avant de continuer le dev:
1. `php artisan route:list --path=api`
2. verifier qu'il n'y a pas de doublons de routes pour une meme ressource
3. `php artisan migrate:status`
4. `php artisan migrate` si une migration est en attente

## 9. Etat actuel (apres correction)
- Chat: modele + controleur API + routes + migration OK
- Ecole: modele + controleur API + routes OK
- Classes imbriquees sous ecole: routes + controleur API OK
- Routes manuelles `users/ecoles/chats`: OK (sans doublon API Platform)

---

Si tu veux, prochaine etape: je te mets des Form Requests partout (`Auth`, `User`, `Ecole`, `Chat`, `Classe`) pour avoir une validation propre et uniforme.
