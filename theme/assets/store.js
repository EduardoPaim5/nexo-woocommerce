document.querySelector('.menu-toggle')?.addEventListener('click', event => {
  const button = event.currentTarget;
  const open = button.getAttribute('aria-expanded') !== 'true';
  button.setAttribute('aria-expanded', String(open));
  button.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
  document.getElementById('main-nav').classList.toggle('is-open', open);
});
document.addEventListener('keydown', event => {
  if (event.key === 'Escape') {
    const button = document.querySelector('.menu-toggle');
    if (button?.getAttribute('aria-expanded') === 'true') {
      button.click();
      button.focus();
    }
  }
});
