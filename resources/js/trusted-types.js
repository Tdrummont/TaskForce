// Trusted Types Policy para resolver violações de segurança
if (window.trustedTypes && window.trustedTypes.createPolicy) {
  // Criar uma política padrão que permite HTML seguro
  window.trustedTypes.createPolicy('default', {
    createHTML: (string) => {
      // Sanitizar HTML removendo scripts e eventos perigosos
      const div = document.createElement('div');
      div.textContent = string;
      return div.innerHTML;
    },
    createScript: (string) => {
      // Para scripts, retornar string vazia por segurança
      return '';
    },
    createScriptURL: (string) => {
      // Para URLs de script, validar se é segura
      try {
        const url = new URL(string, window.location.origin);
        if (url.protocol === 'https:' || url.protocol === 'http:') {
          return url.toString();
        }
      } catch (e) {
        // URL inválida
      }
      return '';
    }
  });
}
