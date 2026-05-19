============================================================
  PLAN DE TRAVAIL FITSPACE — CI4 + SQLite
  ORDRE CHRONOLOGIQUE STRICT — MENTALITÉ BULLDOZER
============================================================

LÉGENDE :
  [ENSEMBLE]  = les deux font ça en même temps, un écran
  [TOI]       = espace client (feature/client)
  [AMIE]      = espace admin (feature/admin)
  ✅           = checkpoint — vérifier avant de continuer

============================================================
PHASE 0 — FONDATIONS COMMUNES (déjà fait normalement)
============================================================

  ✅ Migrations faites (php spark migrate)
  ✅ Seeder fait (php spark db:seed MainSeeder)
  ✅ 4 Models créés (User, Ressource, Crenau, Reservation)
  ✅ Fichier writable/reservation.db visible

  Si pas encore fait → voir le plan précédent avant de continuer.

============================================================
PHASE 1 — PRÉPARATION COMMUNE AVANT SÉPARATION
============================================================

  [ENSEMBLE] — Étape A : Découper le HTML en vues layout
  -------------------------------------------------------
  Créer ces 2 fichiers dans app/Views/layout/ :

  1. header.php
     → Copier le bloc <head> complet du HTML statique
     → Copier la balise <style> complète
     → Copier la nav (nav-public OU sidebar selon la page)
     NOTE : on fera 2 versions — header_public.php et header_app.php

  2. footer.php
     → Le bloc <footer> + le <script> Bootstrap
     → Le script de navigation demo (on le supprimera après)

  Structure finale à créer maintenant :
  app/Views/
    layout/
      header_public.php   ← nav publique (accueil, login, register)
      header_app.php      ← sidebar + topbar (dashboard client/admin)
      footer.php          ← footer + scripts JS

  ✅ Les 2 fichiers layout existent → on continue

  [ENSEMBLE] — Étape B : Créer AuthFilter + Routes
  -------------------------------------------------
  1. php spark make:filter AuthFilter
     → Copier le code AuthFilter fourni précédemment
     → L'enregistrer dans app/Config/Filters.php

  2. Ouvrir app/Config/Routes.php et écrire TOUTES les routes
     maintenant (même celles pas encore codées) :

     // Routes publiques
     $routes->get('/',         'Auth::index');
     $routes->get('/login',    'Auth::login');
     $routes->post('/login',   'Auth::loginPost');
     $routes->get('/register', 'Auth::register');
     $routes->post('/register','Auth::registerPost');
     $routes->get('/logout',   'Auth::logout');

     // Routes client (protégées)
     $routes->group('client', ['filter' => 'auth:client'], function($routes) {
         $routes->get('creneaux',           'Client\Creneaux::index');
         $routes->post('reserver',          'Client\Creneaux::reserver');
         $routes->get('reservations',       'Client\Reservations::index');
         $routes->post('annuler/(:num)',    'Client\Reservations::annuler/$1');
     });

     // Routes admin (protégées)
     $routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
         $routes->get('/',                         'Admin\Dashboard::index');
         $routes->get('creneaux',                  'Admin\Creneaux::index');
         $routes->get('creneaux/create',           'Admin\Creneaux::create');
         $routes->post('creneaux/create',          'Admin\Creneaux::store');
         $routes->get('creneaux/edit/(:num)',      'Admin\Creneaux::edit/$1');
         $routes->post('creneaux/edit/(:num)',     'Admin\Creneaux::update/$1');
         $routes->post('creneaux/delete/(:num)',   'Admin\Creneaux::delete/$1');
         $routes->get('reservations',              'Admin\Reservations::index');
         $routes->post('reservations/statut/(:num)', 'Admin\Reservations::statut/$1');
         $routes->get('ressources',                'Admin\Ressources::index');
         $routes->post('ressources/create',        'Admin\Ressources::store');
     });

  ✅ Routes écrites, AuthFilter enregistré → COMMIT

  [ENSEMBLE] — Étape C : Commit et séparation des branches
  ---------------------------------------------------------
  git add .
  git commit -m "feat: layout views + AuthFilter + routes"
  git push origin dev

  Toi :   git checkout -b feature/client
  Amie :  git checkout -b feature/admin

  ============================================================
  À PARTIR D'ICI VOUS TRAVAILLEZ EN PARALLÈLE
  ============================================================

============================================================
PHASE 2 — TOI : ESPACE CLIENT (feature/client)
============================================================

  Ordre strict : F1 → F2 → F3 → F4
  Ne pas sauter une étape — chaque étape dépend de la précédente.

  ── F1 : INSCRIPTION + CONNEXION + DÉCONNEXION ──────────────

  ÉTAPE F1.1 — Créer le controller Auth
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  php spark make:controller Auth

  Méthodes à coder dans app/Controllers/Auth.php :
    - index()      → GET /         → charge Views/auth/login.php
    - login()      → GET /login    → charge Views/auth/login.php
    - loginPost()  → POST /login   → vérifie email+password en BDD
                                      si ok : session()->set([...]) + redirect /client/creneaux
                                      si non : flashdata('error') + redirect /login
    - register()   → GET /register → charge Views/auth/register.php
    - registerPost()→ POST /register → insère en BDD + redirect /login
    - logout()     → GET /logout   → session()->destroy() + redirect /login

  ÉTAPE F1.2 — Créer les vues Auth
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  Créer app/Views/auth/login.php :
    → Copier la section id="page-login" du HTML statique
    → Remplacer <nav> par <?= view('layout/header_public') ?>
    → Ajouter <?= view('layout/footer') ?> à la fin
    → Brancher le formulaire : action="/login" method="POST"
    → Ajouter <?= csrf_field() ?> dans le form
    → Afficher flashdata erreur en PHP :
       <?php if(session()->getFlashdata('error')): ?>
         <div class="flash-message flash-error">
           <?= session()->getFlashdata('error') ?>
         </div>
       <?php endif; ?>

  Créer app/Views/auth/register.php :
    → Copier la section id="page-inscription" du HTML statique
    → Même logique : header_public + footer + form action="/register"
    → Afficher erreurs de validation :
       <?= validation_show_error('email') ?>

  ✅ TEST F1 : aller sur /register → créer un compte
              aller sur /login → se connecter
              vérifier que session()->get('user_id') existe
              vérifier /logout détruit la session

  ── F2 : LISTE DES CRÉNEAUX ─────────────────────────────────

  ÉTAPE F2.1 — Créer le controller Client/Creneaux
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  php spark make:controller Client/Creneaux --namespace App\Controllers\Client

  Méthode index() :
    $model = new \App\Models\CrenauModel();
    $data['creneaux'] = $model->where('actif', 1)->findAll();
    return view('client/creneaux', $data);

  ÉTAPE F2.2 — Créer la vue client/creneaux.php
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  Créer app/Views/client/creneaux.php :
    → Copier la section id="page-creneaux" du HTML statique
    → Remplacer la sidebar par <?= view('layout/header_app') ?>
    → Remplacer les cartes en dur par une boucle PHP :
       <?php foreach($creneaux as $c): ?>
         <div class="creneau-card <?= $c['places_dispo'] == 0 ? 'full' : '' ?>">
           <p class="creneau-title"><?= $c['nom'] ?? 'Créneau' ?></p>
           <div class="meta-row"><i class="bi bi-clock"></i> <?= $c['date_debut'] ?></div>
           <div class="places-label"><?= $c['places_dispo'] ?> places restantes</div>
           <?php if($c['places_dispo'] > 0): ?>
             <form action="/client/reserver" method="POST">
               <?= csrf_field() ?>
               <input type="hidden" name="creneau_id" value="<?= $c['id'] ?>">
               <button class="btn-reserver">Réserver ce créneau</button>
             </form>
           <?php else: ?>
             <button class="btn-reserver disabled" disabled>Complet</button>
           <?php endif; ?>
         </div>
       <?php endforeach; ?>

  ✅ TEST F2 : aller sur /client/creneaux
              vérifier que les 3 créneaux du seeder s'affichent
              vérifier que les boutons apparaissent

  ── F3 : RÉSERVER UN CRÉNEAU ────────────────────────────────

  ÉTAPE F3 — Ajouter méthode reserver() dans Client/Creneaux
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  Dans le même controller App\Controllers\Client\Creneaux :

  public function reserver() {
    $creneau_id = $this->request->getPost('creneau_id');
    $user_id    = session()->get('user_id');

    $crenauModel = new \App\Models\CrenauModel();
    $creneau = $crenauModel->find($creneau_id);

    // Vérifier places dispo
    if (!$creneau || $creneau['places_dispo'] <= 0) {
      session()->setFlashdata('error', 'Plus de places disponibles.');
      return redirect()->to('/client/creneaux');
    }

    // Insérer la réservation
    $resaModel = new \App\Models\ReservationModel();
    $resaModel->insert([
      'user_id'    => $user_id,
      'creneau_id' => $creneau_id,
      'statut'     => 'en_attente',
      'created_at' => date('Y-m-d H:i:s'),
    ]);

    // Décrémenter places_dispo
    $crenauModel->update($creneau_id, [
      'places_dispo' => $creneau['places_dispo'] - 1
    ]);

    session()->setFlashdata('success', 'Réservation enregistrée !');
    return redirect()->to('/client/reservations');
  }

  ✅ TEST F3 : cliquer "Réserver" sur un créneau
              vérifier qu'une ligne apparaît dans reservations (BDD)
              vérifier que places_dispo a diminué de 1

  ── F4 : MES RÉSERVATIONS + ANNULER ────────────────────────

  ÉTAPE F4.1 — Créer controller Client/Reservations
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  php spark make:controller Client/Reservations --namespace App\Controllers\Client

  Méthode index() :
    $model = new \App\Models\ReservationModel();
    $data['reservations'] = $model
        ->where('user_id', session()->get('user_id'))
        ->findAll();
    return view('client/reservations', $data);

  Méthode annuler($id) :
    $model = new \App\Models\ReservationModel();
    $resa  = $model->find($id);
    // Vérifier que c'est bien la résa de cet user
    if ($resa && $resa['user_id'] == session()->get('user_id')) {
      $model->update($id, ['statut' => 'annulee']);
    }
    return redirect()->to('/client/reservations');

  ÉTAPE F4.2 — Créer la vue client/reservations.php
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  Créer app/Views/client/reservations.php :
    → Copier la section id="page-mes-reservations" du HTML statique
    → Header_app + footer
    → Remplacer les lignes en dur par une boucle :
       <?php foreach($reservations as $r): ?>
         <tr>
           <td><?= $r['creneau_id'] ?></td>
           <td><span class="badge-statut s-<?= $r['statut'] ?>"><?= $r['statut'] ?></span></td>
           <td>
             <?php if($r['statut'] === 'en_attente'): ?>
               <form action="/client/annuler/<?= $r['id'] ?>" method="POST">
                 <?= csrf_field() ?>
                 <button class="btn-sm-custom btn-cancel">Annuler</button>
               </form>
             <?php endif; ?>
           </td>
         </tr>
       <?php endforeach; ?>

  ✅ TEST F4 : aller sur /client/reservations
              vérifier que les réservations s'affichent
              cliquer Annuler → vérifier statut passe à "annulee"

  [COMMIT TOI]
  git add .
  git commit -m "feat: espace client complet F1-F4"
  git push origin feature/client

============================================================
PHASE 3 — AMIE : ESPACE ADMIN (feature/admin)
============================================================

  Ordre strict : F5 → F6 → F7

  ── F5 : CRUD CRÉNEAUX ──────────────────────────────────────

  ÉTAPE F5.1 — Créer controller Admin/Creneaux
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  php spark make:controller Admin/Creneaux --namespace App\Controllers\Admin

  Méthodes :
    index()         → liste tous les créneaux → view('admin/creneaux/index')
    create()        → affiche formulaire      → view('admin/creneaux/create')
    store()         → POST → insert en BDD    → redirect /admin/creneaux
    edit($id)       → affiche formulaire edit → view('admin/creneaux/edit')
    update($id)     → POST → update en BDD   → redirect /admin/creneaux
    delete($id)     → POST → delete en BDD   → redirect /admin/creneaux

  ÉTAPE F5.2 — Créer les vues admin/creneaux/
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  Créer app/Views/admin/creneaux/index.php :
    → Copier la section id="page-admin-creneaux" du HTML statique
    → Header_app + footer
    → Tableau : boucle PHP sur $creneaux
    → Boutons éditer/supprimer avec forms POST + csrf

  Créer app/Views/admin/creneaux/create.php :
    → Formulaire d'ajout (déjà dans le HTML statique)
    → action="/admin/creneaux/create" method="POST"
    → Select ressources dynamique depuis BDD

  Créer app/Views/admin/creneaux/edit.php :
    → Même formulaire mais pré-rempli avec les valeurs du créneau
    → action="/admin/creneaux/edit/<?= $creneau['id'] ?>"

  ✅ TEST F5 : aller sur /admin/creneaux
              créer un créneau → vérifier en BDD
              éditer → vérifier update en BDD
              supprimer → vérifier suppression

  ── F6 : TOUTES LES RÉSERVATIONS + STATUT ───────────────────

  ÉTAPE F6.1 — Créer controller Admin/Reservations
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  php spark make:controller Admin/Reservations --namespace App\Controllers\Admin

  Méthode index() :
    → Récupérer toutes les réservations (jointure avec users et creneaux)
    → view('admin/reservations/index', $data)

  Méthode statut($id) :
    $nouveau_statut = $this->request->getPost('statut'); // 'confirmee' ou 'refusee'
    $model = new \App\Models\ReservationModel();
    $model->update($id, ['statut' => $nouveau_statut]);
    return redirect()->to('/admin/reservations');

  ÉTAPE F6.2 — Créer la vue admin/reservations/index.php
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    → Copier section id="page-admin-reservations" du HTML statique
    → Header_app + footer
    → Boucle sur $reservations
    → Boutons Confirmer/Refuser = 2 forms POST séparés :
       <form action="/admin/reservations/statut/<?= $r['id'] ?>" method="POST">
         <?= csrf_field() ?>
         <input type="hidden" name="statut" value="confirmee">
         <button class="btn-sm-custom btn-confirm">Confirmer</button>
       </form>
       <form action="/admin/reservations/statut/<?= $r['id'] ?>" method="POST">
         <?= csrf_field() ?>
         <input type="hidden" name="statut" value="refusee">
         <button class="btn-sm-custom btn-refuse">Refuser</button>
       </form>

  ✅ TEST F6 : aller sur /admin/reservations
              cliquer Confirmer → statut passe à "confirmee"
              cliquer Refuser   → statut passe à "refusee"

  ── F7 : GESTION RESSOURCES ─────────────────────────────────

  ÉTAPE F7.1 — Créer controller Admin/Ressources
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  php spark make:controller Admin/Ressources --namespace App\Controllers\Admin

  Méthode index() :
    $data['ressources'] = (new \App\Models\RessourceModel())->findAll();
    return view('admin/ressources/index', $data);

  Méthode store() :
    $model = new \App\Models\RessourceModel();
    $model->insert([
      'nom'         => $this->request->getPost('nom'),
      'type'        => $this->request->getPost('type'),
      'capacite'    => $this->request->getPost('capacite'),
      'description' => $this->request->getPost('description'),
    ]);
    return redirect()->to('/admin/ressources');

  ÉTAPE F7.2 — Créer la vue admin/ressources/index.php
  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    → Liste des ressources + formulaire d'ajout sur la même page
    → Même structure que page-admin-creneaux mais pour ressources

  ✅ TEST F7 : aller sur /admin/ressources
              ajouter une ressource → vérifier en BDD

  [COMMIT AMIE]
  git add .
  git commit -m "feat: espace admin complet F5-F6-F7"
  git push origin feature/admin

============================================================
PHASE 4 — MERGE FINAL (ensemble)
============================================================

  1. git checkout dev
     git merge feature/client
     git merge feature/admin

  2. Tester en croisant :
     - Se connecter en tant que client → réserver → vérifier admin voit la resa
     - Admin confirme → vérifier que client voit "confirmée"
     - Admin crée créneau → vérifier client voit le nouveau créneau

  3. Si tout marche :
     git checkout main
     git merge dev
     git push origin main

  4. Écrire le README.md :
     - Comment installer (composer install, cp .env.example .env, php spark migrate, php spark db:seed)
     - Compte admin de test : admin@test.com / admin123
     - Compte client de test : alice@test.com / alice123

============================================================
RÉSUMÉ VISUEL DE L'ORDRE
============================================================

[ENSEMBLE]  Découper header_public.php + header_app.php + footer.php
[ENSEMBLE]  Créer AuthFilter + toutes les Routes
[ENSEMBLE]  Commit → push dev → chacun pull → créer sa branche

[TOI]       F1 : Auth controller + vues login/register  ← TESTER
[TOI]       F2 : Client/Creneaux index + vue creneaux   ← TESTER
[TOI]       F3 : Méthode reserver() dans même controller ← TESTER
[TOI]       F4 : Client/Reservations + vue + annuler    ← TESTER
[TOI]       Commit + push feature/client

[AMIE]      F5 : Admin/Creneaux CRUD complet            ← TESTER
[AMIE]      F6 : Admin/Reservations + changer statut    ← TESTER
[AMIE]      F7 : Admin/Ressources liste + ajout         ← TESTER
[AMIE]      Commit + push feature/admin

[ENSEMBLE]  Merge feature/client + feature/admin → dev
[ENSEMBLE]  Tests croisés
[ENSEMBLE]  Merge dev → main + README

============================================================
RÈGLE D'OR : ne jamais passer à l'étape suivante
sans avoir testé l'étape en cours dans le navigateur.
============================================================