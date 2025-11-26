# 🇸🇳 Thème Sénégal - Mairie de Khombole

## Vue d'ensemble

Ce thème a été spécialement conçu pour intégrer les couleurs nationales du Sénégal (vert, jaune, rouge) tout en maintenant une **dominance absolue du blanc** pour garantir l'accessibilité aux personnes qui ne perçoivent que cette couleur.

## 🎨 Palette de Couleurs

### Couleurs Officielles du Sénégal
- **Vert** : `#00853f` - Couleur principale, représente l'espoir et la nature
- **Jaune** : `#fdef42` - Couleur secondaire, symbolise la richesse et le travail
- **Rouge** : `#e31e24` - Couleur d'accent, évoque le sacrifice et la détermination

### Accessibilité - Dominance du Blanc
- **Blanc principal** : `#ffffff` - Fond dominant sur 90% de l'interface
- **Blanc cassé** : `#fefefe` - Variations subtiles
- **Gris très clair** : `#f8f9fa` - Zones de séparation discrètes

## ♿ Fonctionnalités d'Accessibilité

### Pour les Personnes qui ne Voient que le Blanc

1. **Fond blanc dominant** : Toute l'interface utilise un fond blanc comme base
2. **Contraste élevé** : Ratio minimum de 7:1 pour tous les textes
3. **Bordures visibles** : Contours nets pour délimiter les éléments
4. **Typographie claire** : Police Poppins, tailles adaptées (≥16px)
5. **Espacement généreux** : Éléments bien séparés visuellement

### Conformité WCAG 2.1
- ✅ **Niveau AA** : Contraste minimum respecté
- ✅ **Niveau AAA** : Contraste renforcé sur les éléments critiques
- ✅ **Focus visible** : Contour jaune sénégalais sur focus
- ✅ **Navigation clavier** : Tous les éléments accessibles au clavier

## 📁 Structure des Fichiers

```
assets/css/senegal-theme.css    # Fichier CSS principal du thème
test-accessibilite-senegal.php  # Page de test d'accessibilité
public_header.php               # Header modifié avec le thème
admin_header.php                # Header admin modifié
menu.php                        # Page d'accueil avec le thème
```

## 🚀 Intégration

### Dans les Headers
```html
<!-- Thème Sénégal -->
<link href="assets/css/senegal-theme.css" rel="stylesheet">
```

### Variables CSS Disponibles
```css
:root {
    /* Couleurs Sénégal */
    --senegal-vert: #00853f;
    --senegal-jaune: #fdef42;
    --senegal-rouge: #e31e24;
    
    /* Blanc dominant */
    --blanc-principal: #ffffff;
    --blanc-casse: #fefefe;
    --gris-tres-clair: #f8f9fa;
}
```

## 🎯 Éléments Stylisés

### Navigation
- **Drapeau en dégradé** : La navbar utilise les trois couleurs en bandes horizontales
- **Liens blancs** : Texte blanc sur le drapeau coloré
- **Hover blanc** : Les liens deviennent blancs avec texte vert au survol

### Boutons
- **Primaire** : Vert sénégalais avec texte blanc
- **Secondaire** : Jaune sénégalais avec texte noir
- **Danger** : Rouge sénégalais avec texte blanc
- **Contours** : Bordures colorées sur fond blanc

### Cartes et Conteneurs
- **Fond blanc** : Toutes les cartes sur fond blanc
- **Bordures colorées** : Utilisation du drapeau en bordure
- **Ombres subtiles** : Effets d'élévation discrets

## 🧪 Tests d'Accessibilité

### Page de Test
Visitez `test-accessibilite-senegal.php` pour :
- Vérifier les contrastes de couleurs
- Tester la navigation au clavier
- Valider la lisibilité des textes
- Contrôler l'affichage des formulaires

### Outils Recommandés
- **Contrast Checker** : Vérification des ratios de contraste
- **WAVE** : Analyse d'accessibilité web
- **axe DevTools** : Extension navigateur pour tests automatisés
- **Lecteur d'écran** : Test avec NVDA ou JAWS

## 📱 Responsive Design

### Points de Rupture
- **Mobile** : < 768px - Navigation simplifiée
- **Tablette** : 768px - 1024px - Adaptation des grilles
- **Desktop** : > 1024px - Affichage complet

### Adaptations Mobiles
- Navbar collapsible avec fond blanc
- Cartes empilées verticalement
- Boutons pleine largeur
- Espacement réduit mais suffisant

## 🔧 Personnalisation

### Modifier les Couleurs
```css
:root {
    --senegal-vert: #votre-vert;
    --senegal-jaune: #votre-jaune;
    --senegal-rouge: #votre-rouge;
}
```

### Ajouter des Variantes
```css
.ma-classe-personnalisee {
    background: var(--blanc-principal);
    border: 2px solid var(--senegal-vert);
    color: var(--texte-fonce);
}
```

## 🌟 Bonnes Pratiques

### Utilisation des Couleurs
1. **Blanc en priorité** : Toujours utiliser le blanc comme couleur de base
2. **Couleurs en accent** : Utiliser les couleurs sénégalaises pour les éléments importants
3. **Contraste respecté** : Vérifier le ratio avant d'appliquer une couleur
4. **Cohérence** : Utiliser les variables CSS définies

### Accessibilité
1. **Texte alternatif** : Toujours fournir des alternatives textuelles
2. **Labels explicites** : Formulaires avec labels clairs
3. **Focus visible** : Ne jamais masquer les indicateurs de focus
4. **Ordre logique** : Navigation cohérente et prévisible

## 📞 Support

Pour toute question concernant ce thème :
- **Email** : etat.civil@mairiedekhombole.sn
- **Téléphone** : +221 33 624 52 13 63

## 📄 Licence

Ce thème est développé pour la Mairie de Khombole, République du Sénégal.
Utilisation libre pour les administrations publiques sénégalaises.

---

**Développé avec ❤️ pour l'accessibilité universelle**
*Mairie de Khombole - République du Sénégal*
