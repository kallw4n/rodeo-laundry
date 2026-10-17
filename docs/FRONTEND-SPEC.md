# Frontend & UI Specification — Rodeo Laundry Malang

## 1. Brand Identity & Design Tokens

### 1.1 Color Palette
- **Primary Navy / Deep Slate:** `#1A1743` (Headers, Primary Buttons, High Emphasis Text)
- **Secondary Navy:** `#2F2D59` (Cards background, Hover states, Icon backdrops)
- **Accent Lime / Neon Electric:** `#BFF108` (CTA highlights, Brand dot, Success accents)
- **Sub-Accent Olive Lime:** `#A7D717` (Border highlights, Active tabs)
- **Muted Sage:** `#4E6700` (Sub-text contrast, WhatsApp badge)
- **Background Soft Lilac:** `#FAF8FF` (Page body, card tint)
- **Card Surface & Pill Nav:** `#F2F3FF` and `#EAEDFF`
- **Border Neutral:** `#E2E7FF`
- **Text Body / Muted:** `#47464E` and `#131B2E`

### 1.2 Typography
- **Primary Font:** `Figtree`, `Inter`, sans-serif
- **Scale:**
  - Display / Hero Title: `text-3xl md:text-5xl lg:text-6xl font-extrabold tracking-tight`
  - Section Heading: `text-2xl md:text-3xl font-bold`
  - Subheading / Card Title: `text-lg md:text-xl font-semibold`
  - Body: `text-sm md:text-base leading-relaxed text-[#47464E]`
  - Caption / Micro-badges: `text-xs font-bold uppercase tracking-wider`

---

## 2. Component System Specification

### 2.1 Navigation Bar (`resources/views/layouts/navigation.blade.php` / Header)
- **Desktop:**
  - Brand Logo + City Tagline (`Rodeo Laundry. Malang`).
  - Pill Navigation (`Beranda`, `Layanan`, `Pesanan`).
  - Action Group: WhatsApp quick-chat link + Login / User Dropdown.
- **Mobile:**
  - Fixed top bar with backdrop blur (`backdrop-blur-md`).
  - Hamburger toggle with slide/fade animation via Alpine.js.
  - Full-width touch-friendly menu drawer.

### 2.2 Service Card Component
- Interactive card featuring:
  - Icon / Thumbnail representation of service.
  - Title, Unit badge (`kg`, `pcs`, `psg`, `meter`).
  - Formatted price in IDR (`Rp 8.000 / kg`).
  - Short feature list / description.
  - Interactive Counter (`-` `qty` `+`) with direct update to state.

### 2.3 Order Status Stepper
- 4-stage visual progress tracker:
  1. `Diterima / Pending` (Icon: Document / Clock)
  2. `Sedang Dicuci / Processing` (Icon: Washing Machine / Sparkles)
  3. `Siap Diantar / Ready` (Icon: Package / Motorcycle)
  4. `Selesai / Completed` (Icon: Check Circle)

### 2.4 Checkout Summary Drawer
- Sticky bottom on mobile (`bottom-0 fixed`), sticky right sidebar on desktop (`sticky top-24`).
- Real-time subtotal calculation:
  - Items Total
  - Estimated Delivery Fee (Free pickup promo or fixed tariff)
  - Grand Total
- Primary CTA button leading to the next wizard step.

---

## 3. Responsive Breakpoints
- **Mobile (`< 640px`):** Single column, bottom-anchored action bars, touch target min 44x44px.
- **Tablet (`640px - 1024px`):** 2-column grid for services, hybrid navigation.
- **Desktop (`> 1024px`):** Max container width `1440px`, multi-column cards, sticky order summary sidebars.
