# Apple Inspired Design System

> "Radical subtraction, cinematic full-viewport photography, and uncompromising typography."

This document serves as the visual instruction manual for any AI coding agent building interfaces for this project.

## 1. Visual Theme & Atmosphere
- **Vibe:** Premium, cinematic, minimal, precise.
- **Core Philosophy:** Content is the interface. The chrome (nav, borders) must vanish.
- **Spacing:** Extreme use of negative space to frame content as art.
- **Motion:** Parallax scrolling, subtle scaling on hover, smooth fade-ins.

## 2. Color Palette & Roles

| Token | Hex/RGB | Role |
|-------|---------|------|
| `bg-primary` | `#FFFFFF` | Default page background |
| `bg-secondary` | `#F5F5F7` | Section backgrounds, large cards |
| `text-primary` | `#1D1D1F` | Main headings and body text |
| `text-secondary` | `#86868B` | Subtitles, helper text |
| `accent-blue` | `#0066CC` | Primary CTA buttons, active links |
| `border-subtle` | `rgba(0,0,0,0.08)` | Dividers, subtle outlines |

*(Dark Mode overrides: `bg-primary: #000000`, `bg-secondary: #1C1C1E`, `text-primary: #F5F5F7`)*

## 3. Typography Rules

- **Font Family:** `Inter`, `-apple-system`, `BlinkMacSystemFont`, `San Francisco`, `Helvetica Neue`, sans-serif.
- **Weight:** Use medium (500) and semibold (600) heavily. Avoid thin weights.
- **Tracking (Letter Spacing):** Tighter tracking (-0.01em to -0.02em) on large headings.
- **Hierarchy:**
  - `Display`: 56px to 80px, bold, tight tracking.
  - `H1`: 40px to 48px, bold.
  - `H2`: 32px, semibold.
  - `Body`: 17px, regular, 1.47 line-height.
  - `Caption`: 12px, medium, `#86868B`.

## 4. Component Stylings

### Buttons
- **Primary:** Background `#0066CC`, text `#FFFFFF`, `border-radius: 980px` (pill shape), padding `12px 24px`, no shadow.
- **Secondary:** Background `rgba(0,0,0,0.08)`, text `#1D1D1F`, `border-radius: 980px`.
- **Hover States:** Slight opacity drop (e.g., `opacity-90`), scale down slightly (`scale-95`).

### Cards & Surfaces
- **Radius:** Apple uses specific continuous curves. Use `rounded-2xl` (16px) or `rounded-3xl` (24px).
- **Backgrounds:** Use `#F5F5F7` for cards on white backgrounds.
- **Shadows:** Avoid harsh box-shadows. If used, make it very soft: `box-shadow: 0 4px 24px rgba(0,0,0,0.06)`.

### Navigation (Glassmorphism)
- Navbars should use a blur effect.
- `background-color: rgba(255, 255, 255, 0.72); backdrop-filter: saturate(180%) blur(20px);`
- Border bottom: `1px solid rgba(0,0,0,0.16)`.

## 5. Layout Principles
- **Grid:** Center-aligned content columns. Maximum width typically 980px or 1200px.
- **Whitespace:** Use massive paddings between sections (e.g., `py-24` or `py-32`).
- **Imagery:** Edge-to-edge (full bleed) images where possible. Images should have `object-fit: cover`.

## 6. Do's and Don'ts
- **DO:** Let the product photography do the talking.
- **DO:** Use gradients only inside text (`background-clip: text`) for special hero headings.
- **DON'T:** Use sharp borders or harsh primary colors (red, green) unless for specific alerts.
- **DON'T:** Cram information. If a page feels empty, make the text bigger, do not add more boxes.

## 7. AI Prompt Guide

When asking AI to build a new component, append this:
> "Build a [component] using the Apple DESIGN.md guidelines. Use Inter/San Francisco font, #F5F5F7 backgrounds for cards, rounded-3xl borders, and a pill-shaped #0066CC primary CTA button. Keep padding massive and text minimal."
