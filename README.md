# Aestazia Child Theme

**Aestazia Child** is a lightweight, beautifully styled child theme for the Aestazia ClassicPress theme. It introduces a vibrant new color palette, refined typography, enhanced widget areas, a dynamic category color system, a live Customizer design panel, and dedicated page templates to give your site a distinct, modern look while maintaining the robust structural foundation of its parent.

## Features

- **Vibrant Color System**: Replaces the parent theme's default colors with a striking modern palette (`#E34A6F` primary, `#279D9F` secondary, dark accents, and warm highlights), built on a full CSS custom property token system for easy customisation.
- **Customizer Design Panel**: A dedicated **Theme Design** panel in the WordPress Customizer groups all visual configuration in one place, with sub-sections for Bootstrap Palette, Typography, Category Colors, and Category Color Application.
- **Live Bootstrap Palette Overrides**: Override Bootstrap core CSS variables (`--bs-primary`, `--bs-secondary`, `--bs-body-bg`, `--bs-body-color`, `--bs-heading-color`, `--color-accent`) directly from the Customizer without writing any CSS.
- **Typography Font Picker**: Choose Google/Bunny Fonts independently for body text, headings H1–H3, and headings H4–H6. Selected fonts are loaded from [Bunny Fonts](https://fonts.bunny.net) (a privacy-friendly alternative to Google Fonts) and applied via CSS custom properties (`--font-body`, `--font-heading-main`, `--font-heading-sub`).
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
2. Select a font for **Body Font**, **Headings H1–H3**, and **Headings H4–H6** from the dropdown. Available choices: *Inter*, *Roboto*, *Lora*, *Playfair Display*, *Poppins*, *Montserrat*, or **Default (Theme)**.
3. Selected fonts are loaded automatically from [Bunny Fonts](https://fonts.bunny.net) and applied via `--font-body`, `--font-heading-main`, and `--font-heading-sub` CSS custom properties.
4. Click **Publish**.

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

## License

Aestazia Child is free software licensed under the terms of the [GNU General Public License, version 2](https://www.gnu.org/licenses/gpl-2.0.txt) (GPLv2) or later.
