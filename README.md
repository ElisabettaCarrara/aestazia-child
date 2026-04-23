# Aestazia Child Theme

**Aestazia Child** is a lightweight, beautifully styled child theme for the Aestazia ClassicPress theme. It introduces a vibrant new color palette, refined typography, enhanced widget areas, a dynamic category color system, and dedicated page templates to give your site a distinct, modern look while maintaining the robust structural foundation of its parent.

## Features

- **Vibrant Color System**: Replaces the parent theme's default colors with a striking modern palette (`#E34A6F` primary, `#279D9F` secondary, dark accents, and warm highlights), built on a full CSS custom property token system for easy customisation.
- **Category Color System**: Assign individual colors to each category directly from the Customizer. Colors are applied via CSS variables (`--cat-color`) and can be selectively targeted to card borders, post titles, and Read More links.
- **Alternating Post Layout**: Archive, search, and home loop views automatically alternate between `layout-left` and `layout-right` post card arrangements for a dynamic, magazine-style appearance on wider screens.
- **Post Card Layout**: Post list views use a two-column card layout — thumbnail on one side, excerpt and Read More link on the other — using Bootstrap's responsive grid (`col-12 / col-sm-5` + `col-sm-7`).
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

Once activated, you can begin configuring the theme:

### Footer Widgets
To use the 3-column footer:
1. Navigate to **Appearance > Widgets**.
2. Drag and drop your desired widgets into **Footer Column 1**, **Footer Column 2**, and **Footer Column 3**.

### Category Colors
To assign colors to categories:
1. Navigate to **Appearance > Customize**.
2. Open the **Category Colors** section and pick a color for each category.
3. Open the **Category Color Application** section and check which elements the color should be applied to: **Card Border**, **Post Title**, and/or **Read More Link**.
4. Click **Publish**.

Colors are output as the CSS variable `--cat-color` on each post's article element (via the `primary-cat-{slug}` class), so they can also be referenced in custom CSS rules.

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

Please ensure any changes pass the ClassicPress Coding Standards (CPCS). Checks run automatically on every commit via GitHub Actions.

## License

Aestazia Child is free software licensed under the terms of the [GNU General Public License, version 2](https://www.gnu.org/licenses/gpl-2.0.txt) (GPLv2) or later.
