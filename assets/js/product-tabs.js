document.addEventListener('DOMContentLoaded', function() {
  const tabButtons = document.querySelectorAll('.tab-button');
  const tabContents = document.querySelectorAll('.tab-content');
  tabButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      const tab = this.getAttribute('data-tab');
      tabButtons.forEach(b => {
        b.classList.remove('border-[#FF3A5E]', 'text-[#FF3A5E]');
        b.setAttribute('aria-selected', 'false');
      });
      this.classList.add('border-[#FF3A5E]', 'text-[#FF3A5E]');
      this.setAttribute('aria-selected', 'true');
      tabContents.forEach(c => c.classList.add('hidden'));
      document.getElementById(tab + '-tab').classList.remove('hidden');
    });
  });
}); 