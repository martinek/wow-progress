# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Is

A WordPress sidebar widget plugin for tracking World of Warcraft guild raid progress. Displays boss kills across Normal, Heroic, and Mythic difficulties for all WoW raid instances. No build system — pure PHP + CSS + minimal jQuery.

## Development

No build step, no package manager, no test suite. Development is direct file editing.

**WordPress dev server:** Drop the plugin into `/wp-content/plugins/wow-progress/` and activate it. The plugin works out of the box once activated.

**Deployment:** This repo is simultaneously a git repo (GitHub) and an SVN working copy (WordPress.org plugin directory at `http://plugins.svn.wordpress.org/wow-progress`). The repo root IS the SVN working copy — `trunk/` and `tags/` are SVN subdirectories tracked here.

## Architecture

### Core Files

- **`wowprogress.php`** — Main plugin file. One class `wowprogress_widget extends WP_Widget`. Handles widget rendering, admin form, and option saving. Also defines all constants and asset helpers.
- **`raids.json`** — The raid database (70+ raids, 500+ bosses). Single source of truth for all raid/boss data. Adding a new raid means editing this file only.
- **`inc/admin.php`** — WordPress admin settings page (Settings → WoW Progress). Controls global options: theme, which raids are available in widgets, difficulty display mode.

### Data Flow

```
raids.json → parsed at widget init
    → global admin page: which raids are enabled
    → per-widget form: kill checkboxes per boss/difficulty + video URLs
    → widget instance options stored in WP options table
    → frontend HTML rendered with CSS overlay styling
```

### Theme/Asset Resolution

Assets are resolved with `asset_url()` / `asset_path()` which first checks the active WordPress theme directory (`/wp-content/themes/[theme]/wow-progress/`) before falling back to the plugin directory. This lets site themes override images and CSS without touching plugin files.

### Widget Options Schema

Per-widget instance options follow this naming convention:
- `[raid_tag]_show` — show this raid
- `[raid_tag]_expand` — expand boss list by default
- `[raid_tag]_[boss_index]` — normal kill
- `[raid_tag]_[boss_index]_hc` — heroic kill
- `[raid_tag]_[boss_index]_myth` — mythic kill
- `[raid_tag]_[boss_index]_vid` — video URL
- `[raid_tag]_time` — achievement timestamp

### raids.json Structure

```json
{
    "achievement": 12345,       // WoWHead achievement ID (used for links)
    "background": "moq",        // filename (without ext) in images/raids/
    "bosses": ["Boss1", ...],   // ordered boss list
    "exp": "midnight",          // expansion tag (matches images/exp/ filename)
    "name": "March on Quel'Danas",
    "tag": "moq"                // unique ID used as option key prefix
}
```

## Release Process

The repo root is an SVN working copy of the WordPress.org plugin. `trunk/` is the live development branch; `tags/X.Y.Z/` are immutable release snapshots. Git is used in parallel for GitHub.

### Steps

1. **Make changes** in `trunk/` (edit files directly there, not in the repo root)

2. **Bump the version** in two places:
   - `trunk/wowprogress.php` — `WOWPROGRESS_VERSION` constant and the `Version:` plugin header
   - `trunk/readme.txt` — `Stable tag:` field and add a `== Changelog ==` entry

3. **Commit trunk to SVN:**
   ```bash
   svn commit trunk/ -m "v1.X.Y - Description"
   ```

4. **Create the SVN tag** (copy from trunk):
   ```bash
   svn copy http://plugins.svn.wordpress.org/wow-progress/trunk \
            http://plugins.svn.wordpress.org/wow-progress/tags/1.X.Y \
            -m "Add svn tag"
   ```

5. **Update local tags directory** so git tracks the tag snapshot:
   ```bash
   svn update tags/1.X.Y
   ```

6. **Commit to git:**
   ```bash
   git add trunk/ tags/1.X.Y/
   git commit -m "v1.X.Y - Description"
   git tag 1.X.Y
   git push && git push --tags
   ```

### Adding a New Raid

1. Add entry to `raids.json`
2. Add background image to `images/raids/[tag].png`
3. Add expansion header image to `images/exp/[exp].png` if new expansion
4. Bump version in `wowprogress.php` (`WOWPROGRESS_VERSION`) and `readme.txt`
5. Add changelog entry to `readme.txt`

### Themes

CSS themes are in `themes/` (light, dark, erebos). They override base styles in `wowprogress.css`. Selected via global admin setting, loaded via `wp_enqueue_style`.

## Key Helper Functions

- `wowp_get($arr, $key, $default)` — safe array access (pre-PHP-7 null-coalescing)
- `asset_url($path)` / `asset_path($path)` — theme-aware asset resolution
- `form_boss($raid_tag, $boss_index, $boss_name, $instance)` — renders 3 difficulty checkboxes + video input for one boss
- `difficulty_letter($diff)` — converts `'hc'`/`'myth'`/`''` to display letter
