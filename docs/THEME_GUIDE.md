# CMS Theme & CSS Animation Design System Guide

## Campus Edition — September 2026

### Harivandana identity preview

`public/css/harivandana-preview.css` is loaded only on the login page and the dashboard route. It applies deep purple, lavender and white based on the supplied college logo, stored unchanged at `public/images/harivandana-logo.png`. Other routes retain Campus Edition until the preview is approved. The complete logo appears on a light surface; the dashboard's light/dark toggle and student chart use the preview palette.

The active presentation layer is now `public/css/campus.css`, loaded **after** the existing page styles. The older design documentation below describes the base stylesheet; Campus Edition overrides its purple palette with forest green (`#245c4b`), warm ivory (`#f6f5f0`), and muted gold (`#b4975d`). Saved legacy accent values no longer change this palette. The existing light/dark preference remains available.

- Sign-in uses a two-column layout, serif editorial headings, and the local `public/images/campus-architecture.svg` illustration. Demo account selection, password visibility, validation, and authentication routes remain connected. The language menu now loads the Bootstrap script needed to open it.
- All pages using the application layout share the refined cards, tables, controls, date/context line, and 280px desktop sidebar. Navigation links and expandable section headings use 16px text, with aligned icons and indented submenus. Below 768px, the workspace navigation opens with a dedicated button.
- The management dashboard includes a welcome panel. The public homepage, admission application, account chooser, and password page load the same theme.
- Student and faculty dashboards include distinct welcome sections and role-specific quick-access links through `components/campus-shortcuts.blade.php`. The student attendance chart uses the campus palette, adapts its axes to dark mode, and respects reduced motion. Sidebar groups use native buttons for keyboard access.
- Font Awesome and Bootstrap retain their existing CDN dependencies. The campus illustration requires no external service.
- Maintain future visual changes in `campus.css`. Keep it last in the stylesheet order; existing styles include high-specificity and `!important` rules. Its asset URL includes a modification timestamp to refresh cached styles after changes.
- Reduced-motion preferences and print layouts are supported. PDF-specific templates are unchanged.

---

Welcome to the **College Management System (CMS) Professional Theme & CSS Animation Design System**. This guide provides an overview of the design tokens, color accent palettes, keyframe animations, component patterns, and dark mode features implemented in `public/css/theme.css`.

---

## 1. Design Philosophy & Token Architecture

The theme layers cleanly over **Bootstrap 5.3**, introducing custom CSS Custom Properties (`:root`) for colors, shadows, glassmorphism, radii, transitions, and micro-interactions.

### Brand Palette Presets (`data-cms-accent`)
The design system supports 5 dynamic color accents selectable in real-time from the navbar palette picker:

| Accent | Attribute | Primary Color | Nav Gradient | Purpose |
| :--- | :--- | :--- | :--- | :--- |
| **Royal Purple** *(Default)* | `data-cms-accent="purple"` | `#6d3fa0` | Deep Purple to Navy | Official Harivandana College Identity |
| **Ocean Blue** | `data-cms-accent="ocean"` | `#0284c7` | Deep Navy to Ocean Blue | Technology & Computer Science Focus |
| **Emerald Green** | `data-cms-accent="emerald"` | `#059669` | Forest Green to Teal | Campus Life, Ecology & Growth |
| **Sunset Rose** | `data-cms-accent="sunset"` | `#e11d48` | Crimson to Dark Burgundy | Energy, Sports & Events |
| **Amber Gold** | `data-cms-accent="amber"` | `#d97706` | Warm Bronze to Amber | Executive Leadership & Awards |

---

## 2. CSS Keyframe Animations & Utilities

### Available Keyframes
| Keyframe Name | Description | Effect |
| :--- | :--- | :--- |
| `cmsFadeIn` | Subtle opacity transition | Smooth appearance without motion |
| `cmsFadeInUp` | Slide up from bottom + opacity | Standard card & container entry |
| `cmsFadeInDown` | Slide down from top + opacity | Header and alert banner entry |
| `cmsScaleIn` | Gentle spring scale entrance | Dialogs, modals, and popups |
| `cmsFloat` | Floating continuous hover | Interactive icons & featured badges |
| `cmsPulseGlow` | Breathing brand halo | Call-to-action buttons |
| `cmsPulseLive` | Live status pulse dot | Real-time indicators |
| `cmsGlowBorder` | Ambient border glow pulse | Featured high-priority cards |
| `cmsShimmer` | Skeleton shimmer sweep | Loading placeholders |
| `cmsGradientShift`| Dynamic continuous color flow | Brand strip & hero accents |

### Animation Utility Classes
- `.animate-fade-in` — 250ms smooth fade.
- `.animate-fade-up` — 450ms smooth upward entrance.
- `.animate-fade-down` — 450ms smooth downward entrance.
- `.animate-scale-in` — 350ms bounce scale entrance.
- `.animate-float` — 3s infinite floating icon animation.
- `.animate-pulse-glow` — 2s pulsing halo.
- `.animate-glow-border` — 3s ambient border pulse.

### Staggered Entrance Delays
Combine with `.animate-fade-up` or `.animate-fade-in` for staggered grid effects:
- `.delay-50` (50ms)
- `.delay-100` (100ms)
- `.delay-150` (150ms)
- `.delay-200` (200ms)
- `.delay-250` (250ms)
- `.delay-300` (300ms)

---

## 3. UI Component Patterns

### A. Stat Cards (`.stat-card`)
```html
<div class="stat-card stat-card-primary animate-fade-up delay-50">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <div class="text-muted small fw-bold text-uppercase">Total Students</div>
            <div class="fs-2 fw-bold text-dark mt-1">1,248</div>
        </div>
        <div class="stat-icon bg-primary-subtle text-primary">
            <i class="fa-solid fa-user-graduate"></i>
        </div>
    </div>
    <div class="mt-2 text-success small fw-semibold">
        <span class="status-dot status-dot-pulse bg-success me-1"></span> Active Enrollments
    </div>
</div>
```
*Variants:* `.stat-card-primary`, `.stat-card-success`, `.stat-card-warning`, `.stat-card-danger`.

### B. Interactive Hover Cards (`.card-hover-lift`)
```html
<div class="card card-hover-lift animate-fade-up delay-200">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Card Title</h5>
    </div>
    <div class="card-body">
        Card body content...
    </div>
</div>
```

### C. Gradient & Micro-interaction Buttons
```html
<!-- Gradient Primary Button -->
<a href="#" class="btn btn-gradient-primary shadow-sm">
    <i class="fa-solid fa-paper-plane me-1"></i> Action Button
</a>

<!-- Outline Primary with Lift -->
<a href="#" class="btn btn-outline-primary shadow-xs">
    <i class="fa-solid fa-eye me-1"></i> View Details
</a>
```

### D. Live Status Dots (`.status-dot`, `.status-dot-pulse`)
```html
<span class="status-dot status-dot-pulse bg-success"></span>
```

---

## 4. Dark Theme Architecture

Dark mode is activated via `data-bs-theme="dark"` on `<html>`.

- Background transitions to deep navy/slate `#0b0f19` (not flat black).
- Cards adapt to `#131a29` with ambient border `#1e293b`.
- Modals, tables, inputs, and scrollbars automatically adapt without custom per-page code.
- Instant `localStorage` preservation with zero flash on page loads.

---

## 5. Summary of Updated Files
- `public/css/theme.css` *(Design tokens, 5 color accent presets, keyframes, components, dark mode)*
- `resources/views/layouts/app.blade.php` *(Global stylesheet link, palette picker dropdown, theme toggle, container entrance animation)*
- `resources/views/auth/login.blade.php` *(Modern ambient glow, card animation, gradient button)*
- `resources/views/auth/change-password.blade.php` *(Theme tokens + modern layout)*
- `resources/views/auth/choose-account.blade.php` *(Interactive animated account buttons)*
- `resources/views/admission_applications/apply.blade.php` *(Public admission portal with theme tokens, card lift, and gradient submit)*
- `resources/views/dashboards/management.blade.php` *(Staggered stat cards, hover lift cards, pulse badges)*
- `resources/views/dashboards/student.blade.php` *(Micro-interactions, attendance indicators, smooth cards)*
- `resources/views/dashboards/staff.blade.php` *(Timetable polish, department stat cards, staggered animations)*
