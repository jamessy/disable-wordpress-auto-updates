# Disable WordPress Auto Updates

**Disable Auto Updates** — WordPress plugin to easily enable or disable all core, plugin, and theme updates with an admin toggle. Includes logging of blocked update attempts.

## Features

- Adds a toggle under **Settings → Disable Auto Updates** to enable or disable updates
- When updates are disabled:
  - Blocks all automatic updates (core, plugins, themes)
  - Blocks manual updates (including admin dashboard updates)
  - Blocks update checks to WordPress.org
  - Logs all blocked update attempts to `wp-content/sg-update-attempts.log`
- Lightweight and simple to use

## How it works

When updates are disabled:
- The plugin applies filters and constants to block all WordPress-based update attempts
- Manual updates (via admin) and automatic updates are stopped before they can run
- All blocked attempts are logged with timestamps

⚠ **Note:**  
This plugin cannot block updates triggered by server-level tools (e.g. `wp-cli` or SiteGround scripts that operate outside WordPress).

## Installation

1. Download the plugin as a ZIP file.
2. In your WordPress admin panel, go to **Plugins → Add New → Upload Plugin**.
3. Upload the ZIP file and activate the plugin.
4. Go to **Settings → Disable Auto Updates** to toggle updates on or off.

## Log file

All blocked update attempts are recorded in:

