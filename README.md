# Aestazia Child Theme

**Aestazia Child** is a lightweight, beautifully styled child theme for the Aestazia ClassicPress theme. It introduces a vibrant new color palette, refined typography, enhanced widget areas, and dedicated page templates to give your site a distinct, modern look while maintaining the robust structural foundation of its parent.

## Features

- **Vibrant Color System**: Replaces the parent theme's default colors with a striking modern palette (`#E34A6F` primary, `#279D9F` secondary, dark accents, and warm highlights).
- **Custom Page Templates**: Includes built-in "No Title" templates for both default layout with sidebars and full-width layouts, perfect for landing pages and homepages.
- **Enhanced Footer Layout**: Introduces a 3-column widgetized footer area (`Footer Column 1, 2, and 3`) that seamlessly spans full width with dedicated styling.
- **Refined Typography & UI**: Custom styling for blockquotes, tags, badges, buttons, pagination, and code blocks for a polished user experience.
- **Accessible Interactions**: Clear hover, focus, and active states applied to links, buttons, and form elements.
- **Fully Responsive**: Inherits the responsive Bootstrap grid system from Aestazia.

## Requirements

- **ClassicPress:** Version 2.0 or higher
- **PHP:** Version 7.4 or higher
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
To use the new 3-column footer:
1. Navigate to **Appearance > Widgets**.
2. Drag and drop your desired widgets into **Footer Column 1**, **Footer Column 2**, and **Footer Column 3**.

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

Please ensure any changes pass the ClassicPress Coding Standards (CPCS).

## License

Aestazia Child is free software licensed under the terms of the [GNU General Public License, version 2](https://www.gnu.org/licenses/gpl-2.0.txt) (GPLv2) or later.
