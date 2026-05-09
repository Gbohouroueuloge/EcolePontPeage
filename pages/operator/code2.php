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