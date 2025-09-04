import { createI18n } from 'vue-i18n';
import fr from './fr.json';
import en from './en.json';

const messages = { fr, en };

const i18n = createI18n({
  legacy: false,
  locale: window.LARAVEL_LOCALE || 'fr',
  fallbackLocale: 'en',
  messages,
});

export default i18n; 