<?php
$title = 'Incident';

use App\Models\User;
use App\Models\Agent;
use App\Models\Guichet;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'operator/variables.php';

/** @var User    */ $user    = $user;
/** @var Agent   */ $agent   = $agent;
/** @var Guichet */ $guichet = $guichet;

/* ═══════════════════════════════════════════════════
   TRAITEMENT DU FORMULAIRE
════════════════════════════════════════════════════ */
$form_success = false;
$form_error   = null;
$posted_type  = $_POST['type']          ?? '';
$posted_immat = $_POST['immatriculation'] ?? '';
$posted_desc  = $_POST['description']   ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!$agent->is_en_cours()) {
    $form_error = "Action impossible : la voie est actuellement FERMÉE.";
  } elseif (!$guichet) {
    $form_error = "Aucun guichet assigné à cet opérateur.";
  } else {

    $immatriculation = strtoupper(trim($posted_immat));
    $type            = trim($posted_type);
    $description     = trim($posted_desc) ?: null;
    $url_image       = null;

    /* ── Validation basique ── */
    if (!$immatriculation || !$type) {
      $form_error = "La plaque d'immatriculation et le type d'incident sont obligatoires.";
    } else {

      /* ── Upload de l'image (optionnel) ── */
      if (!empty($_FILES['image']['name'])) {
        $allowed   = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo     = finfo_open(FILEINFO_MIME_TYPE);
        $mime      = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
          $form_error = "Format d'image non accepté (JPG, PNG, GIF, WEBP uniquement).";
        } elseif ($_FILES['image']['size'] > 10 * 1024 * 1024) {
          $form_error = "L'image ne doit pas dépasser 10 Mo.";
        } else {
          $ext        = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
          $filename   = 'incident_' . uniqid() . '.' . $ext;
          $uploadDir  = dirname(__DIR__, 2) . '/public/uploads/incidents/';

          if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
          }

          if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
            $url_image = '/uploads/incidents/' . $filename;
          } else {
            $form_error = "Échec de l'enregistrement de l'image. Vérifiez les permissions du dossier.";
          }
        }
      }

      /* ── Insertion en base si pas d'erreur image ── */
      if (!$form_error) {
        try {
          /* Trouver ou créer le véhicule */
          $stmtV = $pdo->prepare("SELECT id FROM vehicule WHERE immatriculation = :immat LIMIT 1");
          $stmtV->execute(['immat' => $immatriculation]);
          $vehicule = $stmtV->fetch(PDO::FETCH_OBJ);

          if (!$vehicule) {
            /* Type générique (id=1) si inconnu – l'admin pourra corriger */
            $stmtI = $pdo->prepare(
              "INSERT INTO vehicule (immatriculation, type_vehicule_id) VALUES (:immat, 1)"
            );
            $stmtI->execute(['immat' => $immatriculation]);
            $vehicule_id = (int) $pdo->lastInsertId();
          } else {
            $vehicule_id = (int) $vehicule->id;
          }

          /* Insérer l'incident */
          $stmtInc = $pdo->prepare(
            "INSERT INTO incident (vehicule_id, guichet_id, type, description, url_image)
            VALUES (:v, :g, :t, :d, :img)"
          );
          $stmtInc->execute([
            'v'   => $vehicule_id,
            'g'   => $guichet->id,
            't'   => $type,
            'd'   => $description,
            'img' => $url_image,
          ]);

          $form_success = true;
          /* Réinitialiser les champs après succès */
          $posted_type  = '';
          $posted_immat = '';
          $posted_desc  = '';
        } catch (\Exception $e) {
          $form_error = $e->getMessage();
        }
      }
    }
  }
}

/* ── Historique des 5 derniers incidents du guichet ── */
$incidents_recents = [];
if ($guichet) {
  $stmtH = $pdo->prepare(
    "SELECT i.id, i.type, i.description, i.created_at,
            v.immatriculation
    FROM incident i
    JOIN vehicule v ON i.vehicule_id = v.id
    WHERE i.guichet_id = :g
    ORDER BY i.created_at DESC
    LIMIT 12"
  );
  $stmtH->execute(['g' => $guichet->id]);
  $incidents_recents = $stmtH->fetchAll(PDO::FETCH_OBJ);
}

/* ── Helpers ── */
$typeIcon = fn(string $t) => match (strtolower($t)) {
  'panne'             => 'car_repair',
  'refus'             => 'block',
  'fraude'            => 'person_off',
  'barrière bloquée'  => 'traffic',
  'urgence'           => 'emergency',
  'technique'         => 'build',
  default             => 'report',
};

$sessionId = 'TX-' . strtoupper(substr(md5($guichet->id . date('Ymd')), 0, 3))
  . '-' . date('d')
  . '-' . strtoupper(substr(md5($user->id), 0, 1));
?>

<?php if ($form_success) : ?>
  <!-- ── Bannière succès ── -->
  <div class="mt-16 bg-brand-success py-3 px-6 flex items-center justify-center gap-3 shadow-lg z-40 relative anim-slide-down">
    <span class="material-symbols-outlined text-white" style="font-variation-settings:'FILL' 1;">check_circle</span>
    <span class="text-white font-headline font-extrabold tracking-widest text-sm uppercase">Incident enregistré avec succès</span>
  </div>
<?php elseif ($form_error) : ?>
  <!-- ── Bannière erreur ── -->
  <div class="mt-16 bg-error py-3 px-6 flex items-center justify-center gap-3 shadow-lg z-40 relative anim-slide-down">
    <span class="material-symbols-outlined text-white" style="font-variation-settings:'FILL' 1;">cancel</span>
    <span class="text-white font-headline font-extrabold tracking-widest text-sm uppercase"><?= htmlspecialchars($form_error) ?></span>
  </div>
<?php else : ?>
  <!-- ── Bannière alerte neutre ── -->
  <div class="mt-16 bg-[#FF6B6B] py-3 px-6 flex items-center justify-center gap-3 shadow-lg z-40 relative">
    <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">warning</span>
    <span class="text-white font-headline font-extrabold tracking-widest text-sm uppercase">⚠️ SIGNALEMENT EN COURS</span>
  </div>
<?php endif; ?>

<main class="max-w-3xl mx-auto px-6 py-8">

  <!-- ── En-tête ── -->
  <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
      <h1 class="font-headline text-4xl font-extrabold text-primary tracking-tight leading-none mb-2">Signaler un Incident</h1>
      <p class="text-on-surface-variant font-body">Saisie opérationnelle pour la maintenance et la sécurité.</p>
    </div>
    <div class="bg-surface-container-highest px-4 py-2 border-l-4 border-primary-container rounded-r-md">
      <span class="block text-[10px] uppercase font-bold text-on-surface-variant opacity-70">Identifiant de Session</span>
      <span class="font-mono text-lg font-bold text-primary"><?= htmlspecialchars($sessionId) ?></span>
    </div>
  </div>

  <form action="/operator/incident" method="post" enctype="multipart/form-data" class="space-y-10">

    <!-- ── Immatriculation ── -->
    <section>
      <div class="flex items-center gap-2 mb-5">
        <div class="diamond-indicator w-3 h-3 bg-secondary"></div>
        <h2 class="font-headline font-bold text-xl uppercase tracking-wider text-primary">Véhicule concerné</h2>
      </div>
      <input
        type="text"
        name="immatriculation"
        id="immatriculation"
        value="<?= htmlspecialchars(strtoupper($posted_immat)) ?>"
        placeholder="ex : AB-123-CD"
        autocomplete="off"
        class="w-full px-4 py-3 bg-surface-container-lowest border-0 ring-1
               <?= ($form_error && !$posted_immat) ? 'ring-2 ring-error' : 'ring-outline-variant/30' ?>
               focus:ring-2 focus:ring-secondary-container rounded-lg font-mono uppercase tracking-widest
               transition-all duration-200 outline-none text-primary placeholder:normal-case placeholder:tracking-normal placeholder:font-body"
        required />
    </section>

    <!-- ── Type d'incident ── -->
    <section>
      <div class="flex items-center gap-2 mb-6">
        <div class="diamond-indicator w-3 h-3 bg-secondary"></div>
        <h2 class="font-headline font-bold text-xl uppercase tracking-wider text-primary">Nature de l'incident</h2>
      </div>

      <!-- Champ caché alimenté par JS -->
      <input type="hidden" name="type" id="incident_type" value="<?= htmlspecialchars($posted_type) ?>" required />

      <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="type-grid">
        <?php
        $types = [
          ['Panne',            'car_repair'],
          ['Refus',            'block'],
          ['Fraude',           'person_off'],
          ['Barrière bloquée', 'traffic'],
          ['Urgence',          'emergency'],
          ['Technique',        'build'],
        ];
        foreach ($types as [$label, $icon]) :
          $isSelected = strtolower($posted_type) === strtolower($label);
        ?>
          <button
            type="button"
            data-type="<?= htmlspecialchars($label) ?>"
            class="type-card group flex flex-col items-center justify-center p-6
                 bg-surface-container-lowest rounded-xl
                 shadow-[0_4px_20px_rgba(13,31,60,0.05)]
                 border-b-4 transition-all duration-200
                 <?= $isSelected ? 'border-secondary ring-2 ring-secondary/20' : 'border-transparent hover:border-secondary' ?>">
            <span
              class="material-symbols-outlined text-4xl mb-3 transition-colors
                   <?= $isSelected ? 'text-secondary' : 'text-primary group-hover:text-secondary' ?>"
              style="font-variation-settings: 'FILL' <?= $isSelected ? '1' : '0' ?>"><?= $icon ?></span>
            <span class="font-headline font-bold text-sm text-primary"><?= htmlspecialchars($label) ?></span>
          </button>
        <?php endforeach; ?>
      </div>

      <?php if ($form_error && !$posted_type) : ?>
        <p class="mt-2 text-xs text-error font-label">Veuillez sélectionner un type d'incident.</p>
      <?php endif; ?>
    </section>

    <!-- ── Description & Photo ── -->
    <section>
      <div
        class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_4px_20px_rgba(13,31,60,0.05)] relative overflow-hidden space-y-6">
        <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
          <span class="material-symbols-outlined text-8xl">description</span>
        </div>

        <!-- Description -->
        <div>
          <label class="block font-headline font-bold text-sm uppercase tracking-widest text-primary mb-3">
            Description détaillée
          </label>
          <textarea
            name="description"
            rows="4"
            placeholder="Décrivez l'incident de manière précise..."
            class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2
                   focus:ring-secondary-container transition-all p-4 font-body
                   placeholder:opacity-50 outline-none resize-none"><?= htmlspecialchars($posted_desc) ?></textarea>
        </div>

        <!-- Upload image -->
        <div>
          <label class="block font-headline font-bold text-sm uppercase tracking-widest text-primary mb-3">
            Preuves Visuelles (Photo)
          </label>
          <label for="image_upload"
            class="border-2 border-dashed border-outline-variant rounded-xl p-10
                   flex flex-col items-center justify-center bg-surface-container-low
                   group cursor-pointer hover:bg-surface-container-high transition-all">
            <span
              class="material-symbols-outlined text-5xl text-on-surface-variant mb-4 group-hover:scale-110 transition-transform">add_a_photo</span>
            <p class="font-body text-sm text-on-surface-variant font-semibold" id="upload-label">
              Capturer ou déposer une image
            </p>
            <p class="font-body text-[10px] text-on-surface-variant opacity-60 mt-1">PNG, JPG, GIF, WEBP — max 10 Mo</p>
          </label>
          <input
            type="file"
            name="image"
            id="image_upload"
            accept="image/jpeg,image/png,image/gif,image/webp"
            class="hidden" />
        </div>
      </div>
    </section>

    <!-- ── Bouton submit ── -->
    <div>
      <button
        type="submit"
        id="submit-btn"
        class="w-full bg-[#FF6B6B] hover:bg-[#fa5252] text-white font-headline font-extrabold
               text-lg py-5 rounded-xl shadow-[0_10px_25px_rgba(255,107,107,0.3)]
               transition-all active:scale-95 flex items-center justify-center gap-3
               disabled:opacity-50 disabled:cursor-not-allowed">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">send</span>
        SIGNALER L'INCIDENT
      </button>
      <p class="text-center mt-6 text-on-surface-variant opacity-50 text-[10px] uppercase font-bold tracking-[0.2em]">
        Enregistrement horodaté certifié par TollOps Monolith
      </p>
    </div>

  </form>

  <!-- ── Historique récent ── -->
  <?php if (!empty($incidents_recents)) : ?>
    <section class="mt-16">
      <div class="flex items-center gap-2 mb-6">
        <div class="diamond-indicator w-3 h-3 bg-error"></div>
        <h2 class="font-headline font-bold text-xl uppercase tracking-wider text-primary">Incidents récents — Poste #<?= $guichet->id ?></h2>
      </div>
      <div class="space-y-3">
        <?php foreach ($incidents_recents as $inc) : ?>
          <div class="flex items-start gap-4 bg-surface-container-lowest p-4 rounded-xl
                  shadow-[0_2px_12px_rgba(13,31,60,0.04)] border-l-4 border-[#FF6B6B]">
            <span class="material-symbols-outlined text-[#FF6B6B] mt-0.5 shrink-0"
              style="font-variation-settings:'FILL' 1;"><?= $typeIcon($inc->type) ?></span>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2 flex-wrap">
                <span class="font-mono font-bold text-primary text-sm"><?= htmlspecialchars($inc->immatriculation) ?></span>
                <span class="text-[10px] uppercase font-bold tracking-widest text-on-surface-variant opacity-60 font-mono">
                  <?= date('H:i', strtotime($inc->created_at)) ?>
                </span>
              </div>
              <span class="inline-block mt-1 bg-error/10 text-error text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded">
                <?= htmlspecialchars($inc->type) ?>
              </span>
              <?php if ($inc->description) : ?>
                <p class="text-on-surface-variant text-xs mt-1 line-clamp-2 font-body">
                  <?= htmlspecialchars($inc->description) ?>
                </p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

</main>

<script>
  /* ── Sélection du type d'incident ── */
  const hiddenType = document.getElementById('incident_type');
  const cards = document.querySelectorAll('.type-card');
  const submitBtn = document.getElementById('submit-btn');

  function selectCard(btn) {
    cards.forEach(c => {
      const icon = c.querySelector('.material-symbols-outlined');
      c.classList.remove('border-secondary', 'ring-2', 'ring-secondary/20');
      c.classList.add('border-transparent');
      icon.style.fontVariationSettings = "'FILL' 0";
      icon.classList.remove('text-secondary');
      icon.classList.add('text-primary');
    });

    const icon = btn.querySelector('.material-symbols-outlined');
    btn.classList.add('border-secondary', 'ring-2', 'ring-secondary/20');
    btn.classList.remove('border-transparent');
    icon.style.fontVariationSettings = "'FILL' 1";
    icon.classList.add('text-secondary');
    icon.classList.remove('text-primary');

    hiddenType.value = btn.dataset.type;
  }

  cards.forEach(card => card.addEventListener('click', () => selectCard(card)));

  /* Restaurer la sélection après erreur POST */
  if (hiddenType.value) {
    const match = [...cards].find(c => c.dataset.type === hiddenType.value);
    if (match) selectCard(match);
  }

  /* ── Aperçu du nom de fichier sélectionné ── */
  document.getElementById('image_upload').addEventListener('change', function() {
    const label = document.getElementById('upload-label');
    label.textContent = this.files[0] ? this.files[0].name : 'Capturer ou déposer une image';
  });
</script>