<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Chapitre;
use App\Models\Classe;
use App\Models\Ecole;
use App\Models\Formation;
use App\Models\Progression;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Users ────────────────────────────────────────────────────────
        $prof = User::create([
            'name'     => 'Prof Demo',
            'email'    => 'prof@demo.test',
            'password' => Hash::make('password'),
            'type'     => 'prof',
        ]);

        $ecoleUser1 = User::create([
            'name'     => 'Directeur École Alpha',
            'email'    => 'directeur.alpha@demo.test',
            'password' => Hash::make('password'),
            'type'     => 'ecole',
        ]);

        $ecoleUser2 = User::create([
            'name'     => 'Directeur École Beta',
            'email'    => 'directeur.beta@demo.test',
            'password' => Hash::make('password'),
            'type'     => 'ecole',
        ]);

        $etudiant = User::create([
            'name'     => 'Etudiant Demo',
            'email'    => 'etudiant@demo.test',
            'password' => Hash::make('password'),
            'type'     => 'etudiant',
        ]);

        // ── 2. Écoles ───────────────────────────────────────────────────────
        $ecoleAlpha = Ecole::create([
            'nom'     => 'École Alpha',
            'user_id' => $ecoleUser1->id,
        ]);

        $ecoleBeta = Ecole::create([
            'nom'     => 'École Beta',
            'user_id' => $ecoleUser2->id,
        ]);

        // ── 3. Classes ──────────────────────────────────────────────────────
        $classeA = Classe::create(['nom' => 'Terminale A', 'ecole_id' => $ecoleAlpha->id]);
        $classeB = Classe::create(['nom' => 'BTS 1',       'ecole_id' => $ecoleBeta->id]);

        // Assign etudiant to a class
        $etudiant->update(['classe_id' => $classeA->id]);

        // ── 4. Formations ───────────────────────────────────────────────────
        $formation1 = Formation::create([
            'titre'       => 'Introduction à PHP',
            'description' => 'Apprenez les bases du langage PHP : syntaxe, fonctions, tableaux et formulaires.',
            'user_id'     => $prof->id,
        ]);

        $formation2 = Formation::create([
            'titre'       => 'Laravel pour débutants',
            'description' => 'Découvrez le framework Laravel : routing, Eloquent, Blade et API REST.',
            'user_id'     => $prof->id,
        ]);

        $formation3 = Formation::create([
            'titre'       => 'Bases de données relationnelles',
            'description' => 'Maîtrisez SQL, la modélisation de données et les jointures.',
            'user_id'     => $prof->id,
        ]);

        // ── 5. Chapitres & Articles ─────────────────────────────────────────
        foreach ([$formation1, $formation2, $formation3] as $formation) {
            foreach (range(1, 2) as $chapOrdre) {
                $chapitre = Chapitre::create([
                    'titre'        => "Chapitre {$chapOrdre} — {$formation->titre}",
                    'ordre'        => $chapOrdre,
                    'formation_id' => $formation->id,
                ]);

                foreach (range(1, 2) as $artOrdre) {
                    Article::create([
                        'titre'       => "Article {$artOrdre}",
                        'contenu'     => "Contenu de l'article {$artOrdre} du chapitre {$chapOrdre} ({$formation->titre}).",
                        'ordre'       => $artOrdre,
                        'chapitre_id' => $chapitre->id,
                    ]);
                }
            }
        }

        // ── 6. Pivot classe_formation ───────────────────────────────────────
        $classeA->formations()->attach([$formation1->id, $formation2->id]);
        $classeB->formations()->attach([$formation2->id, $formation3->id]);

        // ── 7. Progressions pour l'étudiant ─────────────────────────────────
        $articles = Article::whereHas('chapitre.formation', function ($q) use ($formation1) {
            $q->where('id', $formation1->id);
        })->get();

        foreach ($articles as $article) {
            Progression::create([
                'user_id'    => $etudiant->id,
                'article_id' => $article->id,
                'termine'    => true,
                'termine_at' => now(),
            ]);
        }
    }
}

