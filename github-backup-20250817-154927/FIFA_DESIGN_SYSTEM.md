# FIFA Design System - FIT Platform

## 🎯 Vue d'ensemble

Le système de design FIFA est une architecture CSS modulaire inspirée de l'univers FIFA, conçue pour créer des interfaces professionnelles, modernes et cohérentes pour la plateforme FIT (Football Intelligence & Tracking).

## 🎨 Palette de Couleurs

### Couleurs Principales

```css
--fifa-blue-primary: #1e3a8a; /* Bleu profond FIFA */
--fifa-blue-secondary: #3b82f6; /* Bleu accent */
--fifa-blue-light: #dbeafe; /* Bleu clair */
--fifa-gold: #f59e0b; /* Or FIFA */
--fifa-gold-light: #fef3c7; /* Or clair */
--fifa-white: #ffffff; /* Blanc */
```

### Couleurs Neutres

```css
--fifa-gray-50: #f9fafb; /* Fond très clair */
--fifa-gray-100: #f3f4f6; /* Fond clair */
--fifa-gray-200: #e5e7eb; /* Bordures */
--fifa-gray-300: #d1d5db; /* Bordures plus foncées */
--fifa-gray-400: #9ca3af; /* Texte secondaire */
--fifa-gray-500: #6b7280; /* Texte tertiaire */
--fifa-gray-600: #4b5563; /* Texte principal */
--fifa-gray-700: #374151; /* Texte important */
--fifa-gray-800: #1f2937; /* Texte très important */
--fifa-gray-900: #111827; /* Texte principal */
```

### Couleurs d'État

```css
--fifa-success: #10b981; /* Succès */
--fifa-success-light: #d1fae5; /* Succès clair */
--fifa-error: #ef4444; /* Erreur */
--fifa-error-light: #fee2e2; /* Erreur clair */
--fifa-warning: #f59e0b; /* Avertissement */
--fifa-warning-light: #fef3c7; /* Avertissement clair */
```

## 📝 Typographie

### Famille de Police

```css
--fifa-font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    sans-serif;
```

### Tailles de Police

```css
--fifa-text-xs: 0.75rem; /* 12px */
--fifa-text-sm: 0.875rem; /* 14px */
--fifa-text-base: 1rem; /* 16px */
--fifa-text-lg: 1.125rem; /* 18px */
--fifa-text-xl: 1.25rem; /* 20px */
--fifa-text-2xl: 1.5rem; /* 24px */
--fifa-text-3xl: 1.875rem; /* 30px */
--fifa-text-4xl: 2.25rem; /* 36px */
```

### Poids de Police

```css
--fifa-font-weight-light: 300;
--fifa-font-weight-normal: 400;
--fifa-font-weight-medium: 500;
--fifa-font-weight-semibold: 600;
--fifa-font-weight-bold: 700;
--fifa-font-weight-black: 900;
```

## 📏 Espacement

```css
--fifa-spacing-xs: 0.25rem; /* 4px */
--fifa-spacing-sm: 0.5rem; /* 8px */
--fifa-spacing-md: 0.75rem; /* 12px */
--fifa-spacing-lg: 1rem; /* 16px */
--fifa-spacing-xl: 1.5rem; /* 24px */
--fifa-spacing-2xl: 2rem; /* 32px */
--fifa-spacing-3xl: 3rem; /* 48px */
```

## 🔄 Transitions

```css
--fifa-transition-fast: 150ms ease-in-out;
--fifa-transition-normal: 250ms ease-in-out;
--fifa-transition-slow: 350ms ease-in-out;
```

## 🎭 Ombres

```css
--fifa-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--fifa-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
--fifa-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
--fifa-shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
```

## 🔘 Rayons de Bordure

```css
--fifa-radius-sm: 0.25rem; /* 4px */
--fifa-radius-md: 0.375rem; /* 6px */
--fifa-radius-lg: 0.5rem; /* 8px */
--fifa-radius-xl: 0.75rem; /* 12px */
--fifa-radius-full: 9999px; /* Circulaire */
```

## 🎯 Composants Vue.js

### FifaNavigation

Navigation latérale avec style FIFA moderne.

**Props:**

-   `isCollapsed` (Boolean): État de réduction de la navigation
-   `menuItems` (Array): Éléments du menu
-   `userAvatar` (String): URL de l'avatar utilisateur
-   `userName` (String): Nom de l'utilisateur
-   `userRole` (String): Rôle de l'utilisateur

**Événements:**

-   `@navigation`: Navigation vers une page
-   `@logout`: Déconnexion
-   `@toggle-collapse`: Basculer la réduction

### FifaCard

Carte réutilisable avec variantes de style.

**Props:**

-   `title` (String): Titre de la carte
-   `variant` (String): Variante de style (default, primary, success, warning, error, info)
-   `hoverable` (Boolean): Effet de survol
-   `loading` (Boolean): État de chargement
-   `clickable` (Boolean): Clicable

**Slots:**

-   `default`: Contenu principal
-   `header`: En-tête personnalisé
-   `headerActions`: Actions dans l'en-tête
-   `footer`: Pied de page

### FifaButton

Bouton avec multiples variantes et états.

**Props:**

-   `variant` (String): Variante (primary, secondary, success, warning, error, ghost, outline)
-   `size` (String): Taille (xs, sm, md, lg, xl)
-   `disabled` (Boolean): Désactivé
-   `loading` (Boolean): État de chargement
-   `fullWidth` (Boolean): Pleine largeur
-   `rounded` (Boolean): Bordure arrondie
-   `iconLeft` (Component): Icône à gauche
-   `iconRight` (Component): Icône à droite

## 🎨 Utilisation des Variables CSS

### Modification des Couleurs

Pour changer la palette de couleurs, modifiez les variables dans `fifa-design-system.css`:

```css
:root {
    --fifa-blue-primary: #your-color;
    --fifa-blue-secondary: #your-color;
    /* ... autres couleurs */
}
```

### Création de Thèmes

Pour créer un thème sombre, ajoutez:

```css
[data-theme="dark"] {
    --fifa-white: #1f2937;
    --fifa-gray-50: #111827;
    --fifa-gray-900: #ffffff;
    /* ... autres variables */
}
```

### Responsive Design

Les composants incluent automatiquement le responsive design:

```css
@media (max-width: 768px) {
    /* Styles mobiles automatiques */
}
```

## 🔧 Personnalisation

### Ajout de Nouvelles Variantes

Pour ajouter une nouvelle variante de bouton:

```css
.fifa-btn--custom {
    background: var(--fifa-custom-color);
    color: var(--fifa-white);
    border-color: var(--fifa-custom-color);
}

.fifa-btn--custom:hover:not(:disabled) {
    background: var(--fifa-custom-hover);
    border-color: var(--fifa-custom-hover);
}
```

### Création de Composants Personnalisés

Utilisez les variables CSS pour créer de nouveaux composants:

```vue
<template>
    <div class="custom-component">
        <h2 class="custom-title">{{ title }}</h2>
        <p class="custom-text">{{ content }}</p>
    </div>
</template>

<style scoped>
.custom-component {
    background: var(--fifa-white);
    border-radius: var(--fifa-radius-lg);
    padding: var(--fifa-spacing-lg);
    box-shadow: var(--fifa-shadow-md);
}

.custom-title {
    font-size: var(--fifa-text-xl);
    font-weight: var(--fifa-font-weight-bold);
    color: var(--fifa-gray-900);
    margin-bottom: var(--fifa-spacing-md);
}

.custom-text {
    font-size: var(--fifa-text-base);
    color: var(--fifa-gray-600);
    line-height: var(--fifa-line-height-normal);
}
</style>
```

## 📱 Responsive Design

Le système inclut des breakpoints automatiques:

-   **Mobile**: < 768px
-   **Tablet**: 768px - 1024px
-   **Desktop**: > 1024px

### Classes Utilitaires

```css
.fifa-hidden-mobile    /* Masqué sur mobile */
/* Masqué sur mobile */
/* Masqué sur mobile */
/* Masqué sur mobile */
/* Masqué sur mobile */
/* Masqué sur mobile */
/* Masqué sur mobile */
/* Masqué sur mobile */
.fifa-hidden-desktop   /* Masqué sur desktop */
.fifa-print-only       /* Visible uniquement à l'impression */
.fifa-no-print; /* Masqué à l'impression */
```

## 🎭 Animations

### Transitions Disponibles

```css
.fade-enter-active,
.fade-leave-active .slide-enter-active,
.slide-leave-active;
```

### Animations CSS

```css
@keyframes fifa-spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
```

## 🚀 Performance

### Optimisations Incluses

-   Variables CSS pour la cohérence
-   Transitions optimisées
-   Responsive design natif
-   Composants modulaires
-   Lazy loading des routes

### Bonnes Pratiques

1. Utilisez toujours les variables CSS pour la cohérence
2. Préférez les composants Vue.js aux classes CSS personnalisées
3. Testez sur mobile et desktop
4. Utilisez les transitions pour une meilleure UX
5. Optimisez les images et icônes

## 🔄 Migration depuis React

### Changements Principaux

1. **Alpine.js → Vue 3**: Migration complète vers Vue 3 Composition API
2. **CSS Modules → Variables CSS**: Système de design centralisé
3. **React Components → Vue Components**: Composants réécrits en Vue
4. **React Router → Vue Router**: Navigation Vue native

### Avantages de la Migration

-   Performance améliorée
-   Meilleure maintenabilité
-   Système de design cohérent
-   Responsive design natif
-   Animations fluides

## 📚 Ressources

-   [Documentation Vue 3](https://vuejs.org/)
-   [Vue Router](https://router.vuejs.org/)
-   [Pinia](https://pinia.vuejs.org/)
-   [Tailwind CSS](https://tailwindcss.com/)
-   [Inter Font](https://rsms.me/inter/)

## 🤝 Contribution

Pour contribuer au système de design:

1. Suivez les conventions de nommage
2. Utilisez les variables CSS existantes
3. Testez sur tous les appareils
4. Documentez les nouveaux composants
5. Maintenez la cohérence visuelle
