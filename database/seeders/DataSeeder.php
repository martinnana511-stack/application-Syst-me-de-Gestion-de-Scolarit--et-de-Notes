<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\ReportCard;
use App\Models\Term;
use App\Models\User;

class DataSeeder extends Seeder
{
    // =========================================================================
    // Nom des élèves
    // =========================================================================
    private array $nomsGarcons = [
        'OUEDRAOGO', 'KABORÉ', 'TRAORÉ', 'SAWADOGO', 'ZOUNGRANA',
        'COMPAORÉ', 'TAPSOBA', 'BELEM', 'NIKIEMA', 'SOME',
        'DIABATÉ', 'COULIBALY', 'DIALLO', 'KONATÉ', 'SANÉ',
        'TOE', 'HIEN', 'DA', 'KOALA', 'ZONGO',
        'ILBOUDO', 'BARRO', 'NIGNAN', 'SANKARA', 'TIENDRÉBÉOGO',
    ];

    private array $nomsFilles = [
        'OUEDRAOGO', 'KABORÉ', 'TRAORÉ', 'SAWADOGO', 'ZOUNGRANA',
        'COMPAORÉ', 'TAPSOBA', 'BELEM', 'NIKIEMA', 'SOME',
        'DIABATÉ', 'COULIBALY', 'DIALLO', 'KONATÉ', 'SANÉ',
    ];

    private array $prenomsGarcons = [
        'Issouf', 'Moussa', 'Ibrahim', 'Adama', 'Seydou',
        'Hamidou', 'Boureima', 'Abdoul', 'Yacouba', 'Souleymane',
        'Drissa', 'Lassana', 'Mamadou', 'Boubacar', 'Saidou',
        'Arouna', 'Issa', 'Soumana', 'Kassoum', 'Daouda',
        'Wendyam', 'Pingdwendé', 'Rasmané', 'Télesphore', 'Patrice',
    ];

    private array $prenomsFilles = [
        'Aminata', 'Fatoumata', 'Mariam', 'Aïssata', 'Salimata',
        'Kadiatou', 'Rokia', 'Bintou', 'Haoua', 'Ramata',
        'Clarisse', 'Pascaline', 'Victorine', 'Sandrine', 'Joëlle',
        'Grâce', 'Béatrice', 'Cécile', 'Chantal', 'Nathalie',
        'Wendyam', 'Pingdwendé', 'Rasmata', 'Albertine', 'Brigitte',
    ];

    private array $lieuxNaissance = [
        'Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora',
        'Ouahigouya', 'Kaya', 'Tenkodogo', 'Fada N\'Gourma',
        'Dédougou', 'Kongoussi', 'Ziniaré', 'Manga', 'Léo',
    ];

    private array $adresses = [
        'Secteur 15, Ouagadougou', 'Secteur 22, Ouagadougou',
        'Secteur 8, Ouagadougou', 'Quartier Gounghin',
        'Quartier Pissy', 'Quartier Tampouy', 'Quartier Zogona',
        'Quartier Paspanga', 'Secteur 30, Ouagadougou',
        'Quartier Dassasgho', 'Secteur 17, Ouagadougou',
    ];

    // =========================================================================
    // Méthode principale
    // =========================================================================

    public function run(): void
    {
        $this->command->info('🚀 Démarrage du DataSeeder...');

        // 1. Année scolaire
        $year = $this->createAcademicYear();

        // 2. Trimestres
        $terms = $this->createTerms($year);

        // 3. Utilisateurs
        [$gestionnaire, $enseignants] = $this->createUsers();

        // 4. Classes
        $classes = $this->createClasses($year, $enseignants);

        // 5. Matières
        $subjects = $this->createSubjects();

        // 6. Associer matières aux classes
        $this->attachSubjectsToClasses($classes, $subjects);

        // 7. Élèves + Inscriptions
        $enrollments = $this->createStudentsAndEnrollments($classes, $year, $gestionnaire);

        // 8. Paiements (frais d'inscription)
        $this->createPayments($enrollments, $gestionnaire);

        // 9. Notes
        $this->createGrades($enrollments, $subjects, $terms, $gestionnaire);

        // 10. Calcul des moyennes
        $this->calculateAverages($enrollments, $terms);

        $this->command->info('✅ DataSeeder terminé avec succès !');
    }

    // =========================================================================
    // Année scolaire
    // =========================================================================

    private function createAcademicYear(): AcademicYear
    {
        $this->command->info('📅 Création de l\'année scolaire...');

        return AcademicYear::create([
            'libelle'    => '2025-2026',
            'date_debut' => '2025-10-01',
            'date_fin'   => '2026-07-31',
            'is_active'  => true,
        ]);
    }

    // =========================================================================
    // Trimestres
    // =========================================================================

    private function createTerms(AcademicYear $year): Collection
    {
        $this->command->info('📆 Création des trimestres...');

        $termsData = [
            ['numero' => '1', 'libelle' => '1er Trimestre', 'date_debut' => '2025-10-01', 'date_fin' => '2025-12-20', 'is_closed' => true],
            ['numero' => '2', 'libelle' => '2ème Trimestre', 'date_debut' => '2026-01-06', 'date_fin' => '2026-03-28', 'is_closed' => false],
            ['numero' => '3', 'libelle' => '3ème Trimestre', 'date_debut' => '2026-04-07', 'date_fin' => '2026-07-11', 'is_closed' => false],
        ];

        $terms = collect();
        foreach ($termsData as $data) {
            $terms->push(\App\Models\Term::create(array_merge($data, ['academic_year_id' => $year->id])));
        }

        return $terms;
    }

    // =========================================================================
    // Utilisateurs
    // =========================================================================

    private function createUsers(): array
    {
        $this->command->info('👤 Création des utilisateurs...');

        $gestionnaire = User::create([
            'name'       => 'Directeur Admin',
            'email'      => 'adminecole@gmail.com',
            'password'   => Hash::make('password'),
            'role'       => 'gestionnaire',
            'telephone'  => '+226 70 00 00 01',
            'is_active'  => true,
        ]);

        $enseignantsData = [
            ['name' => 'Jean OUEDRAOGO',    'email' => 'jeanecole@gmail.com',    'telephone' => '+226 70 11 11 11'],
            ['name' => 'Marie KABORÉ',      'email' => 'marieecole@gmail.com',   'telephone' => '+226 70 22 22 22'],
            ['name' => 'Paul TRAORÉ',       'email' => 'paulecole@gmail.com',    'telephone' => '+226 70 33 33 33'],
            ['name' => 'Fatima SAWADOGO',   'email' => 'fatimaecole@gmail.com',  'telephone' => '+226 70 44 44 44'],
            ['name' => 'Pierre COMPAORÉ',   'email' => 'pierreecole@gmail.com',  'telephone' => '+226 70 55 55 55'],
            ['name' => 'Awa TAPSOBA',       'email' => 'awaecole@gmail.com',     'telephone' => '+226 70 66 66 66'],
        ];

        $enseignants = collect();
        foreach ($enseignantsData as $data) {
            $enseignants->push(User::create(array_merge($data, [
                'password'  => Hash::make('password'),
                'role'      => 'enseignant',
                'is_active' => true,
            ])));
        }

        return [$gestionnaire, $enseignants];
    }

    // =========================================================================
    // Classes
    // =========================================================================

    private function createClasses(AcademicYear $year, $enseignants): \Illuminate\Support\Collection
    {
        $this->command->info('🏫 Création des classes...');

        $classesData = [
            ['niveau' => 'CP1', 'nom' => 'CP1-A', 'frais_inscription' => 5000,  'frais_scolarite_annuel' => 60000],
            ['niveau' => 'CP2', 'nom' => 'CP2-A', 'frais_inscription' => 5000,  'frais_scolarite_annuel' => 65000],
            ['niveau' => 'CE1', 'nom' => 'CE1-A', 'frais_inscription' => 5000,  'frais_scolarite_annuel' => 70000],
            ['niveau' => 'CE2', 'nom' => 'CE2-A', 'frais_inscription' => 5000,  'frais_scolarite_annuel' => 70000],
            ['niveau' => 'CM1', 'nom' => 'CM1-A', 'frais_inscription' => 7500,  'frais_scolarite_annuel' => 80000],
            ['niveau' => 'CM2', 'nom' => 'CM2-A', 'frais_inscription' => 7500,  'frais_scolarite_annuel' => 80000],
        ];

        $classes = collect();
        foreach ($classesData as $i => $data) {
            $classes->push(SchoolClass::create(array_merge($data, [
                'academic_year_id' => $year->id,
                'teacher_id'       => $enseignants[$i]->id,
                'effectif_max'     => 40,
            ])));
        }

        return $classes;
    }

    // =========================================================================
    // Matières
    // =========================================================================

    private function createSubjects(): \Illuminate\Support\Collection
    {
        $this->command->info('📚 Création des matières...');

        $subjectsData = [
            ['nom' => 'Lecture / Écriture',    'code' => 'LECT',  'coefficient' => 3.00],
            ['nom' => 'Mathématiques',          'code' => 'MATH',  'coefficient' => 3.00],
            ['nom' => 'Français (Grammaire)',   'code' => 'FR',    'coefficient' => 2.00],
            ['nom' => 'Sciences & Éveil',       'code' => 'SCI',   'coefficient' => 2.00],
            ['nom' => 'Histoire - Géographie',  'code' => 'HISTG', 'coefficient' => 1.50],
            ['nom' => 'Éducation Civique',      'code' => 'EDCIV', 'coefficient' => 1.00],
            ['nom' => 'Éducation Physique',     'code' => 'EPS',   'coefficient' => 1.00],
            ['nom' => 'Dessin / Arts',          'code' => 'ART',   'coefficient' => 0.50],
        ];

        $subjects = collect();
        foreach ($subjectsData as $data) {
            $subjects->push(Subject::create(array_merge($data, [
                'note_max'  => 20,
                'is_active' => true,
            ])));
        }

        return $subjects;
    }

    // =========================================================================
    // Association matières → classes
    // =========================================================================

    private function attachSubjectsToClasses($classes, $subjects): void
        {
            $this->command->info('🔗 Association matières aux classes...');

            // Indexer les matières par code pour faciliter la sélection
            $subjectsByCode = $subjects->keyBy('code');

            // Définir les matières par niveau
            $matieresByNiveau = [
                'CP1' => ['LECT', 'MATH', 'ART', 'EPS', 'EDCIV'],
                'CP2' => ['LECT', 'MATH', 'ART', 'EPS', 'EDCIV'],
                'CE1' => ['LECT', 'MATH', 'FR', 'SCI', 'ART', 'EPS', 'EDCIV'],
                'CE2' => ['LECT', 'MATH', 'FR', 'SCI', 'ART', 'EPS', 'EDCIV'],
                'CM1' => ['LECT', 'MATH', 'FR', 'SCI', 'HISTG', 'ART', 'EPS', 'EDCIV'],
                'CM2' => ['LECT', 'MATH', 'FR', 'SCI', 'HISTG', 'ART', 'EPS', 'EDCIV'],
            ];

            foreach ($classes as $classe) {
                $codes = $matieresByNiveau[$classe->niveau] ?? [];

                foreach ($codes as $code) {
                    $subject = $subjectsByCode->get($code);
                    if ($subject) {
                        $classe->subjects()->attach($subject->id, [
                            'coefficient' => $subject->coefficient,
                            'teacher_id'  => $classe->teacher_id,
                        ]);
                    }
                }
            }
        }

    // =========================================================================
    // Élèves + Inscriptions (25 élèves par classe)
    // =========================================================================

    private function createStudentsAndEnrollments($classes, AcademicYear $year, User $gestionnaire): \Illuminate\Support\Collection
    {
        $this->command->info('👦 Création des élèves (25 par classe)...');

        $allEnrollments = collect();
        $counter = 1;

        foreach ($classes as $classe) {
            $this->command->info("   → Classe {$classe->nom}...");

            for ($i = 0; $i < 25; $i++) {
                // Alterner garçons et filles
                $sexe = ($i % 2 === 0) ? 'M' : 'F';

                $nom    = $sexe === 'M'
                    ? $this->nomsGarcons[array_rand($this->nomsGarcons)]
                    : $this->nomsFilles[array_rand($this->nomsFilles)];

                $prenom = $sexe === 'M'
                    ? $this->prenomsGarcons[array_rand($this->prenomsGarcons)]
                    : $this->prenomsFilles[array_rand($this->prenomsFilles)];

                // Âge selon le niveau
                $ageMin = match($classe->niveau) {
                    'CP1' => 6, 'CP2' => 7, 'CE1' => 8,
                    'CE2' => 9, 'CM1' => 10, 'CM2' => 11,
                    default => 8
                };

                $dateNaissance = now()->subYears($ageMin + rand(0, 1))
                                     ->subMonths(rand(0, 11))
                                     ->subDays(rand(0, 28))
                                     ->format('Y-m-d');

                $student = Student::create([
                    'matricule'         => 'EL-2025-' . str_pad($counter, 5, '0', STR_PAD_LEFT),
                    'nom'               => $nom,
                    'prenom'            => $prenom,
                    'date_naissance'    => $dateNaissance,
                    'lieu_naissance'    => $this->lieuxNaissance[array_rand($this->lieuxNaissance)],
                    'sexe'              => $sexe,
                    'nom_pere'          => $this->nomsGarcons[array_rand($this->nomsGarcons)] . ' ' . $this->prenomsGarcons[array_rand($this->prenomsGarcons)],
                    'nom_mere'          => $this->nomsFilles[array_rand($this->nomsFilles)] . ' ' . $this->prenomsFilles[array_rand($this->prenomsFilles)],
                    'tuteur_telephone'  => '+226 7' . rand(0,9) . ' ' . rand(10,99) . ' ' . rand(10,99) . ' ' . rand(10,99),
                    'adresse'           => $this->adresses[array_rand($this->adresses)],
                    'is_active'         => true,
                ]);

                $enrollment = Enrollment::create([
                    'student_id'       => $student->id,
                    'class_id'         => $classe->id,
                    'academic_year_id' => $year->id,
                    'date_inscription' => '2024-10-01',
                    'statut'           => 'actif',
                    'created_by'       => $gestionnaire->id,
                ]);

                $allEnrollments->push($enrollment);
                $counter++;
            }
        }

        $this->command->info("   ✓ " . $allEnrollments->count() . " élèves créés.");
        return $allEnrollments;
    }

    // =========================================================================
    // Paiements — frais d'inscription pour chaque élève
    // =========================================================================

    private function createPayments($enrollments, User $gestionnaire): void
    {
        $this->command->info('💰 Création des paiements...');

        $recuCounter = 1;

        foreach ($enrollments as $enrollment) {
            $enrollment->load('schoolClass');
            $fraisInscription  = $enrollment->schoolClass->frais_inscription;
            $fraisScolarite    = $enrollment->schoolClass->frais_scolarite_annuel;

            // Montant déjà payé : inscription + entre 0 et 2 versements de scolarité
            $nbVersements = rand(0, 2);
            $montantScolaritePaye = 0;

            // 1. Paiement frais d'inscription
            Payment::create([
                'enrollment_id'   => $enrollment->id,
                'numero_recu'     => 'REC-2024-' . str_pad($recuCounter++, 6, '0', STR_PAD_LEFT),
                'type_paiement'   => 'inscription',
                'montant_verse'   => $fraisInscription,
                'montant_du'      => $fraisScolarite,
                'montant_restant' => $fraisScolarite,
                'mode_paiement'   => $this->randomModePaiement(),
                'date_paiement'   => '2024-10-' . str_pad(rand(1, 15), 2, '0', STR_PAD_LEFT),
                'created_by'      => $gestionnaire->id,
                'is_annule'       => false,
            ]);

            // 2. Versements de scolarité (aléatoires)
            for ($v = 0; $v < $nbVersements; $v++) {
                $montantVersement = rand(1, 3) * 10000; // 10000, 20000 ou 30000 F
                $montantScolaritePaye += $montantVersement;
                $restant = max(0, $fraisScolarite - $montantScolaritePaye);

                Payment::create([
                    'enrollment_id'   => $enrollment->id,
                    'numero_recu'     => 'REC-2024-' . str_pad($recuCounter++, 6, '0', STR_PAD_LEFT),
                    'type_paiement'   => 'scolarite',
                    'montant_verse'   => $montantVersement,
                    'montant_du'      => $fraisScolarite,
                    'montant_restant' => $restant,
                    'mode_paiement'   => $this->randomModePaiement(),
                    'date_paiement'   => '2024-' . rand(10, 12) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'created_by'      => $gestionnaire->id,
                    'is_annule'       => false,
                ]);
            }
        }

        $this->command->info("   ✓ Paiements créés.");
    }

    // =========================================================================
    // Notes — pour le 1er trimestre (clôturé)
    // =========================================================================

    private function createGrades($enrollments, $subjects, $terms, User $gestionnaire): void
    {
        $this->command->info('📝 Saisie des notes (1er et 2ème trimestre)...');

        // Saisir les notes pour les 2 premiers trimestres
        $termsToFill = $terms->take(2);

        foreach ($termsToFill as $term) {
            $this->command->info("   → Trimestre : {$term->libelle}");

            foreach ($enrollments as $enrollment) {
                foreach ($subjects as $subject) {
                    // 5% de chance d'être absent
                    $isAbsent = (rand(1, 100) <= 5);

                    // Note aléatoire entre 5 et 20 (réaliste)
                    $note = $isAbsent ? 0 : $this->generateNote();

                    Grade::create([
                        'enrollment_id' => $enrollment->id,
                        'subject_id'    => $subject->id,
                        'term_id'       => $term->id,
                        'note'          => $note,
                        'note_max'      => 20,
                        'appreciation'  => $this->getAppreciation($note, $isAbsent),
                        'is_absent'     => $isAbsent,
                        'created_by'    => $gestionnaire->id,
                        'updated_by'    => $gestionnaire->id,
                    ]);
                }
            }
        }

        $this->command->info("   ✓ Notes saisies.");
    }

    // =========================================================================
    // Calcul des moyennes et rangs
    // =========================================================================

    private function calculateAverages($enrollments, $terms): void
    {
        $this->command->info('📊 Calcul des moyennes et rangs...');

        $gradeService = new \App\Services\GradeService();
        $termsToCalc  = $terms->take(2);

        foreach ($termsToCalc as $term) {
            $this->command->info("   → {$term->libelle}...");

            foreach ($enrollments as $enrollment) {
                $gradeService->recalculerMoyenne($enrollment->id, $term->id);
            }

            // Recalculer les rangs par classe
            $classIds = $enrollments->pluck('class_id')->unique();
            foreach ($classIds as $classId) {
                ReportCard::recalculateRanks($classId, $term->id);
            }
        }

        $this->command->info("   ✓ Moyennes et rangs calculés.");
    }

    // =========================================================================
    // Méthodes utilitaires
    // =========================================================================

    /** Génère une note réaliste entre 5 et 20 */
    private function generateNote(): float
    {
        // Distribution réaliste : majorité entre 10 et 17
        $rand = rand(1, 100);
        if ($rand <= 10) return rand(5, 9) + (rand(0, 3) * 0.25);      // 10% faibles
        if ($rand <= 30) return rand(10, 12) + (rand(0, 3) * 0.25);    // 20% passable
        if ($rand <= 60) return rand(12, 15) + (rand(0, 3) * 0.25);    // 30% bien
        if ($rand <= 85) return rand(15, 18) + (rand(0, 3) * 0.25);    // 25% très bien
        return rand(18, 20) + (rand(0, 0) * 0.25);                      // 15% excellent
    }

    /** Retourne une appréciation selon la note */
    private function getAppreciation(float $note, bool $isAbsent): string
    {
        if ($isAbsent) return 'Absent lors de l\'évaluation';
        if ($note >= 18) return 'Excellent travail, continuez ainsi !';
        if ($note >= 16) return 'Très bon travail';
        if ($note >= 14) return 'Bon travail';
        if ($note >= 12) return 'Assez bien, peut mieux faire';
        if ($note >= 10) return 'Travail passable, des efforts nécessaires';
        return 'Insuffisant, beaucoup d\'efforts à fournir';
    }

    /** Retourne un mode de paiement aléatoire */
    private function randomModePaiement(): string
    {
        $modes = ['especes', 'especes', 'especes', 'mobile_money', 'mobile_money', 'cheque'];
        return $modes[array_rand($modes)];
    }
}
