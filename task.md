# Mây Mơ Book - Design Context

## Design Context

### Users
**Primary Audience**: All ages (general audience)
- Vietnamese readers seeking convenient book rentals
- Context: Browsing for new discoveries, managing active rentals, leaving reviews
- Job to be done: Find books easily, rent with confidence, manage rental dates, discover recommendations
- Use cases: Students researching, professionals commuting, families exploring together, casual readers discovering

### Brand Personality
**3 Words**: Playful • Energetic • Youthful

**Voice & Tone**: Approachable, encouraging, joyful—the interface should delight users while making book discovery and renting feel effortless and fun.

**Emotional Goals**: Users should feel:
- Inspired to discover new books
- Confident in the rental process
- Delighted by small interactive moments
- Part of a vibrant reading community

### Aesthetic Direction
**Visual Tone**: Modern, contemporary design with vibrant accent colors and clean lines. Moving away from traditional warm/editorial toward a fresher, more energetic palette suitable for a general audience.

**Color Strategy**: 
- Shift from oxblood + gold toward brighter, more contemporary accent colors
- Maintain accessible contrast ratios (WCAG AA minimum)
- Use modern CSS color functions (oklch, color-mix) for perceptual uniformity
- Consider colors that feel playful and youthful without sacrificing readability

**Typography**:
- Primary font: Be Vietnam Pro (Vietnamese language support)
- Maintain fluid, modular scale using clamp() for responsive sizing
- Use weight and size variation for clear hierarchy

**Component Philosophy**:
- Clean, minimal card layouts (avoid nested cards)
- Asymmetrical spacing and layouts for visual interest
- High-impact moments: smooth transitions, purposeful animations
- Empty states that teach the interface, not just say "nothing here"
- Progressive disclosure: basic options first, advanced features accessible through interaction

**What This Is NOT**:
- Not overly minimalist or austere—playfulness should come through
- Not dark mode (light mode only, but designed for comfortable extended reading)
- Not glassmorphism or blur effects for decoration
- Not generic template design or "AI slop"
- Not cards with icons above headings repeated endlessly
- Not unnecessarily rounded or decorated elements

### Design Principles

1. **Make it playful, not formal**
   - Use dynamic spacing, unexpected layouts, and delightful micro-interactions
   - Animations should convey state changes and feedback, not distract
   - Prefer exponential easing (ease-out-quart) for natural deceleration

2. **Prioritize discovery and browsing**
   - Hero section should inspire exploration
   - Book grid should be scannable with clear visual separation
   - Filter and search should feel responsive and instant

3. **Make rental & cart interaction seamless**
   - Use optimistic UI—update cart immediately, sync later
   - Fly-to-cart animations provide visual feedback without blocking interaction
   - Forms should work progressively (no JS dependency, AJAX enhancement)

4. **Honor the brand colors with intention**
   - Shift toward vibrant, contemporary accents
   - Every color choice should serve a purpose (not decoration)
   - Maintain sufficient contrast for accessibility

5. **Respect user preferences**
   - All animations should respect prefers-reduced-motion (disable on user preference)
   - Support keyboard navigation and screen readers
   - Load fast, behave predictably, adapt to different contexts

### Technical Constraints
- **Backend**: PHP 7.4+, MySQL, MVC-like architecture
- **Frontend**: Vanilla HTML5, CSS3, JavaScript (no build system, no frameworks)
- **Browser Support**: Modern browsers supporting CSS custom properties, clamp(), oklch color space
- **Performance**: Load fast, especially on first visit (book-heavy site)
- **Accessibility**: WCAG AA minimum; prefers-reduced-motion support required

### Design Decisions Made

**Color Palette**:
- Current: Oxblood (#7A1F11) + Gold (#C27E3A) + light neutrals
- Direction: Shift toward brighter, more vibrant accents that feel contemporary and energetic
- Approach: Use oklch color space for perceptually uniform adjustments

**Typography**:
- Fluid type scale (clamp) for responsive sizing across viewport widths
- Be Vietnam Pro for Vietnamese language support
- Weight variation (600 for emphasis, 400 for body) creates hierarchy

**Spacing & Layout**:
- Varied spacing (not uniform) creates visual rhythm
- Asymmetrical compositions feel more designed
- Breakpoints: 768px (tablet), 1024px (desktop)

**Micro-interactions**:
- Fly-to-cart animation with image clone provides visual feedback
- Particle burst on wishlist add creates moment of delight
- All animations check prefers-reduced-motion before executing
- Use transform + opacity only (no layout animations)

**Component Approach**:
- Semantic class naming (e.g., `.book-detail-image-wrapper`, `.rental-actions`)
- impeccable.css loads AFTER style.css to provide override layer
- Responsive utilities (e.g., `.mt-12`, `.section-header--center`)

---

## Next Steps for Implementation

When updating the design:

1. **Color Palette Refresh** (Priority: High)
   - Define new vibrant accents replacing oxblood/gold
   - Update CSS variables in impeccable.css
   - Test contrast ratios against light backgrounds

2. **Polish Micro-interactions** (Priority: Medium)
   - Refine fly-to-cart animation timing
   - Add feedback animations for form submission
   - Test reduced-motion compliance across all interactions

3. **Component Refinement** (Priority: Medium)
   - Clean up any remaining inline styles
   - Standardize admin panel design
   - Add empty state illustrations/guidance

4. **Accessibility Audit** (Priority: High)
   - Verify WCAG AA compliance across all pages
   - Test keyboard navigation
   - Validate color contrast in new palette

5. **Mobile Responsiveness** (Priority: Medium)
   - Test book grid on tablet/phone breakpoints
   - Ensure touch targets are 48px minimum
   - Verify form usability on small screens

---

**Project Repository**: [Web-2](https://github.com/22070362-collab/Web-2) • Branch: Nam • Active PR: #6
**Platform Name**: Mây Mơ Book (Vietnamese Book Rental Platform)
**Design Context Created**: May 16, 2026
