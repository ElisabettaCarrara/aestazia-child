# Aestazia Child Theme

**Aestazia Child** is a lightweight, beautifully styled child theme for the Aestazia ClassicPress theme. It introduces a vibrant new color palette, refined typography, enhanced widget areas, a dynamic category color system, a live Customizer design panel, and dedicated page templates to give your site a distinct, modern look while maintaining the robust structural foundation of its parent.

## Features

- **Vibrant Color System**: Replaces the parent theme's default colors with a striking modern palette (`#E34A6F` primary, `#279D9F` secondary, dark accents, and warm highlights), built on a full CSS custom property token system for easy customisation.
- **Customizer Design Panel**: A dedicated **Theme Design** panel in the WordPress Customizer groups all visual configuration in one place, with sub-sections for Bootstrap Palette, Typography, Category Colors, and Category Color Application.
- **Live Bootstrap Palette Overrides**: Override Bootstrap core CSS variables (`--bs-primary`, `--bs-secondary`, `--bs-body-bg`, `--bs-body-color`, `--bs-heading-color`, `--color-accent`) directly from the Customizer without writing any CSS.
- **GDPR-Compliant Typography Font Picker**: Choose fonts independently for body text, headings H1–H3, and headings H4–H6 from the full [Bunny Fonts](https://fonts.bunny.net) catalogue (a privacy-friendly alternative to Google Fonts). On Publish, selected fonts are **downloaded and served locally** from your own uploads directory — no external font requests are ever made to visitors' browsers. Fonts are applied via CSS custom properties (`--font-body`, `--font-heading-main`, `--font-heading-sub`).
- **Category Color System**: Assign individual colors to each category directly from the Customizer. Colors are output as scoped CSS variables on `.post-card.primary-cat-{slug}` elements and can be selectively targeted to card borders, post titles, and Read More links.
- **Alternating Post Layout**: Archive, search, and home loop views automatically alternate between `layout-left` and `layout-right` post card arrangements for a dynamic, magazine-style appearance on wider screens.
- **Post Card Layout**: Post list views use a two-column card layout — thumbnail on one side, excerpt and Read More link on the other — responsive stack on mobile, side-by-side on `sm+` breakpoint.
- **Sticky Post Indicator**: Sticky posts are marked with a 📌 pin icon appended to the post title in loop views.
- **Custom Page Templates**: Includes built-in "No Title" templates for both default layout with sidebar and full-width layout, perfect for landing pages and homepages.
- **Enhanced Footer Layout**: A 3-column widgetized footer area (`Footer Column 1, 2, and 3`) spanning full width with dedicated styling.
- **Refined Typography & UI**: Custom styling for blockquotes, tags, badges, buttons, pagination, and code blocks for a polished user experience.
- **Accessible Interactions**: Clear hover, focus, and active states applied to links, buttons, and form elements.
- **Fully Responsive**: Inherits the responsive Bootstrap grid system from Aestazia.
- **CI Quality Checks**: Automated ClassicPress Coding Standards (CPCS) checks run on every commit via GitHub Actions.

## Requirements

- **ClassicPress:** Version 2.0 or higher (tested up to 2.7.0)
- **PHP:** Version 8.0 or higher
- **Parent Theme:** [Aestazia](https://directory.classicpress.net/themes/aestazia) must be installed.

## Installation

### Via ClassicPress Dashboard (Recommended)
1. Make sure the **Aestazia** parent theme is already installed on your ClassicPress site.
2. Download the latest `aestazia-child.zip` release file.
3. Log in to your ClassicPress admin dashboard.
4. Navigate to **Appearance > Themes**.
5. Click **Add New**, then **Upload Theme**.
6. Select the `aestazia-child.zip` file and click **Install Now**.
7. Once installed, click **Activate**.

### Via WP-CLI
```bash
wp theme install https://github.com/ElisabettaCarrara/aestazia-child/releases/latest/download/aestazia-child.zip --activate
```
*(Note: Ensure the parent theme Aestazia is installed first).*

## Setup & Configuration

Once activated, navigate to **Appearance > Customize** and open the **Theme Design** panel to access all visual settings.

### Bootstrap Palette
To override the site's core colors:
1. Navigate to **Appearance > Customize > Theme Design > Bootstrap Palette**.
2. Use the color pickers to set values for **Primary Color**, **Secondary Color**, **Body Background**, **Body Text**, **Heading Color**, and **Accent Color**.
3. Changes are applied immediately as CSS custom properties in `:root` via an inline `<style id="aestazia-design-system">` block injected into `wp_head`.
4. Click **Publish**.

Leaving any picker empty preserves the default value defined in `style.css`.

### Typography
To change the fonts used across the site:
1. Navigate to **Appearance > Customize > Theme Design > Typography**.
2. Select a font for **Body Font**, **Headings H1–H3**, and **Headings H4–H6** from the dropdown. The full Bunny Fonts catalogue is available; the dropdown falls back to a curated list (*Inter*, *Roboto*, *Lora*, *Playfair Display*, *Poppins*, *Montserrat*) if the Bunny Fonts API is unreachable.
3. Click **Publish**.

On Publish, the theme checks whether the selected fonts are already present in `wp-content/uploads/aestazia-fonts/`. If the selection has changed since the last save, it downloads the font files and a rewritten `fonts.css` from Bunny Fonts and stores them locally. Subsequent page loads serve fonts entirely from your own server — no external requests reach visitors' browsers, keeping the setup fully GDPR-compliant without any cookie banner for fonts.

If the download fails (e.g. a temporary network error), the theme falls back to loading fonts directly from the Bunny Fonts CDN until the next successful Publish. The error is logged to the PHP error log with the prefix `Aestazia Child —` for easy tracing.

### Footer Widgets
To use the 3-column footer:
1. Navigate to **Appearance > Widgets**.
2. Drag and drop your desired widgets into **Footer Column 1**, **Footer Column 2**, and **Footer Column 3**.

### Category Colors
To assign colors to categories:
1. Navigate to **Appearance > Customize > Theme Design > Category Colors**.
2. Pick a color for each category listed.
3. Open **Theme Design > Category Color Application** and check which elements the color should be applied to: **Card Border**, **Post Title**, and/or **Read More Link**.
4. Click **Publish**.

Colors are output as scoped CSS variables (e.g. `--post-card-border`, `--bs-link-color`, `--bs-btn-bg`) on `.post-card.primary-cat-{slug}` selectors. The `primary-cat-{slug}` class is injected automatically on each post's `<article>` element by the `post_class` filter in `functions.php`, using the first assigned category as the primary one.

### Page Templates
To hide the page title on specific pages:
1. Edit a page.
2. In the right-hand sidebar under **Page Attributes**, locate the **Template** dropdown.
3. Select either **Default Page - No Title** (keeps the sidebar) or **Full-Width Template - No Title** (removes the sidebar).
4. Update/Publish the page.

## Development & Contribution

Contributions and bug reports are welcome! If you would like to contribute:
1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Submit a Pull Request.

Please ensure any changes pass the ClassicPress Coding Standards (CPCS). Checks run automatically on every commit via GitHub Actions. A CPCS summary is appended to the GitHub Actions step summary for each run.

On every published release, a GitHub Actions workflow automatically packages the theme into an installable `aestazia-child-{version}.zip` and attaches it to the release as a downloadable asset.

## Changelog

### 1.2.1
- Fix: Category Color Application settings in the Customizer were not persisting after save due to improper boolean sanitization.
- Improvement: Standardized checkbox settings to use `absint` with explicit `theme_mod` type for reliable storage.

### 1.2.0
- **Fix**: Font-family CSS declarations were broken by incorrect `esc_html()` wrapping of the inline `<style>` block — quotes were converted to HTML entities, making the browser ignore all font selections.
- **Fix**: Bunny Fonts downloader used `post_value()` which is only valid during the Customizer preview phase; switched to `get_theme_mod()` so the correct newly-saved fonts are always downloaded.
- **Fix**: Font downloader now checks whether the selected fonts are already present locally before re-downloading, using a `fonts-meta.json` sidecar file. Unchanged selections on Publish make no HTTP requests.
- **Fix**: Local font CSS used root-relative URLs that broke on subdirectory installs; switched to absolute `baseurl` from `wp_upload_dir()`.
- **Fix**: Duplicate `post_class` filter caused `layout-left`/`layout-right` classes to be added twice to every post card; removed the redundant anonymous filter.
- **Improvement**: Font download failures now log a descriptive message to the PHP error log instead of failing silently.
- **Improvement**: SSL verification re-enabled on all Bunny Fonts HTTP requests.
- **Improvement**: WP_Filesystem bootstrap extracted into a shared helper to remove duplicated code.
- **Improvement**: `register_sidebar()` strings are now translatable via `esc_html__()`.

## License

Aestazia Child is free software licensed under the terms of the [GNU General Public License, version 2](https://www.gnu.org/licenses/gpl-2.0.txt) (GPLv2) or later.
