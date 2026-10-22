// Keep small, progressive enhancements separate from PHP templates.
document.querySelectorAll('form').forEach((form) => {
  form.addEventListener('submit', () => {
    const button = form.querySelector('button[type="submit"], button:not([type])');
    if (button && form.checkValidity()) {
      button.dataset.originalText ??= button.textContent;
      // Avoid duplicate clicks on checkout without changing the visible label.
      if (form.action.includes('/checkout')) button.disabled = true;
    }
  });
});
