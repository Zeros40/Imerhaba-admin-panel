# 🎨 Lovable.dev Build Prompt

**Copy this entire prompt to Lovable.dev to build a similar system**

---

## Project Name
**Imerhaba Design Studio** - AI Marketing Design Generator

---

## Main Prompt

```
Build a modern design automation platform for creating bulk social media posts and landing pages.

CORE FEATURES:

1. BULK POST GENERATOR
   - User pastes 50-100 text ideas (one per line)
   - Select brand theme (3 options with different color schemes)
   - Choose format: 1:1 square, 4:5 portrait, 9:16 story
   - Click "Generate All" → Creates all designs automatically
   - Shows real-time progress bar
   - Preview grid shows all generated designs
   - Export as ZIP with all images

2. LANDING PAGE BUILDER
   - Left sidebar: Draggable section components
     - Hero banner, Features grid, ROI calculator, CTA, Contact form, Testimonials
   - Center: Visual canvas (drag sections here)
   - Right sidebar: Settings (colors, SEO, export)
   - Live preview mode
   - Export as HTML file or publish

3. BRAND SYSTEM
   Brand 1 - Imerhaba (Navy #0B132B + Gold #D9A441)
   Brand 2 - Zodiac (Blue #0424BB + Silver #E6E9EF)
   Brand 3 - Ruya (Emerald #0F4D3F + Sand #E1C699)

   Each brand has:
   - Color palette (primary, secondary, accent)
   - Font pairing (Poppins + Inter)
   - Design mood keywords

4. DASHBOARD
   - Stats cards: total designs, campaigns, templates
   - Recent designs grid with thumbnails
   - Quick action cards for common tasks
   - Clean, modern UI with glassmorphism effects

5. AI IMAGE GENERATION
   - Integrate Replicate API (Stable Diffusion XL)
   - Auto-generate backgrounds for posts
   - Brand-aware prompt engineering
   - Fallback to placeholder if no API key

6. EXPORT SYSTEM
   - Export individual designs as PNG
   - Batch export as ZIP
   - Export landing pages as HTML
   - Save all to database

TECH STACK:
- React + TypeScript
- Tailwind CSS
- Supabase (database)
- Replicate API (AI images)
- Zustand (state management)
- html-to-image (for PNG export)
- React DnD (drag and drop)

UI DESIGN:
- Dark theme with gradient backgrounds
- Glassmorphism cards (backdrop blur, transparent)
- Gold accent colors for CTAs
- Smooth animations and transitions
- Responsive (mobile-first)
- Modern sans-serif fonts (Inter, Poppins)

DATABASE SCHEMA:
- designs (id, brand, title, ratio, design_data, created_at)
- templates (id, name, type, layout_data)
- landing_pages (id, name, slug, page_data, seo_title)
- campaigns (id, name, brand, status)
- settings (key, value)

USER FLOW:
1. Dashboard → Click "Bulk Generator"
2. Select brand → Paste 100 post ideas
3. Click "Generate" → Watch progress bar
4. Preview all 100 designs in grid
5. Click "Export ZIP" → Download all

DESIGN SYSTEM:
- Primary buttons: Gold gradient with shadow
- Secondary buttons: Transparent with border
- Cards: Dark background with blur effect
- Text: White with varying opacity
- Spacing: Generous padding, rounded corners
- Icons: Emoji-based for quick recognition

GENERATE FULL APP WITH ALL FILES READY TO DEPLOY.
```

---

## Additional Instructions

### Phase 1: Core Setup
```
First create:
1. Dashboard with stats and navigation
2. Brand configuration system
3. Database schema and setup
```

### Phase 2: Generators
```
Then build:
1. Bulk post generator with progress tracking
2. Real-time preview grid
3. Export functionality
```

### Phase 3: Landing Builder
```
Finally add:
1. Drag-and-drop page builder
2. Section templates
3. Live preview and export
```

### Phase 4: Polish
```
Add finishing touches:
1. Animations and transitions
2. Error handling
3. Loading states
4. Mobile responsiveness
```

---

## Key Components to Build

### 1. BulkGenerator.tsx
```typescript
interface Design {
  id: string;
  brand: string;
  text: string;
  ratio: '1:1' | '4:5' | '9:16';
  imageUrl?: string;
  status: 'pending' | 'generating' | 'done';
}

- Textarea for bulk input
- Brand selector
- Ratio buttons
- Progress bar
- Preview grid
- Export button
```

### 2. LandingBuilder.tsx
```typescript
interface Section {
  id: string;
  type: 'hero' | 'features' | 'cta' | 'contact';
  content: Record<string, any>;
  position: number;
}

- Section library sidebar
- Canvas with drag-drop
- Settings panel
- Preview modal
```

### 3. DesignCard.tsx
```typescript
- Preview canvas
- Brand badge
- Headline text
- CTA button
- Status indicator
```

---

## Sample Data

**Test Post Ideas:**
```
Invest €10,000 and earn 35-50% ROI in Bosnia
Explore AI-powered glamping investments
Smart real estate opportunities in Sarajevo
Book your luxury vacation home today
Experience the future of business automation
```

---

## Visual References

**Dashboard Style:**
- Background: Dark gradient (navy to dark blue)
- Cards: Semi-transparent white with backdrop blur
- Accents: Gold (#D9A441) for important CTAs
- Typography: Large bold headings, clean body text

**Generator Style:**
- Two-column layout (form | preview)
- Smooth progress animations
- Real-time preview updates
- Grid of generated designs

**Landing Builder:**
- Three-column (library | canvas | settings)
- Visual drag indicators
- Hoverable section controls
- Clean export options

---

## Final Output

Should include:
- ✅ Complete React application
- ✅ All components and pages
- ✅ Supabase integration
- ✅ API key configuration
- ✅ Export functionality
- ✅ Responsive design
- ✅ Error handling
- ✅ Loading states
- ✅ README with setup instructions

---

**Time Estimate on Lovable: 15-30 minutes**

Just paste this prompt and let Lovable build everything automatically! 🚀
