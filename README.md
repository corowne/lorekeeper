# Lorekeeper

Lorekeeper is a framework for managing deviantART-based ARPGs/closed species masterlists coded using the Laravel framework. In simple terms - you will be able to make a copy of the site, do some minor setup/enter data about your species and game, and it'll provide you with the automation to keep track of your species, players and ARPG submissions.

## Modified Main

This version of Lorekeeper is modified. It contains several extensions-- additional or modified parts of core Lorekeeper which change or add functions and behavior-- selected for their wide applicability, and for falling into one of the following categories: 
- An unavoidable change in behavior, but one that is arguably a net quality-of-life improvement. May also be highly useful as a base for other extensions to build upon.
- Opt-in/must be deliberately enabled for significant changes in the behavior of the site to occur. No more obtrusive than effectively optional functions in core Lorekeeper if not in use. 

It also serves as a base for developing extensions, providing several common 'dependencies'.

**Reference Links:**
- Demo site: [http://lorekeeper.me/](http://lorekeeper.me/)
- Wiki for users: [http://lorekeeper-arpg.wikidot.com/](http://lorekeeper-arpg.wikidot.com/)
- Extensions wiki: [http://wiki.lorekeeper.me/index.php?title=Category:Extensions](http://wiki.lorekeeper.me/index.php?title=Category:Extensions)

# Features

- **Core Lorekeeper:** Please see [the Readme](https://github.com/corowne/lorekeeper/blob/master/README.md) and [feature documentation](http://lorekeeper-arpg.wikidot.com/) for more information.
- **Grouped Notifications:** To account for the potentially large variety and potentially volume of notifications, they are grouped by notification type and collapse when there are more than 5 notifications of a type.
- **Toggleable Comments on Site Pages:** Adds a toggle to site pages which enables/disables commenting on them. Disabled by default.
- **Extension Service:** A utility for use by extension developers. By default, facilitates adjusting notification type IDs in a site's DB to comply with the [community notification standard](http://wiki.lorekeeper.me/index.php?title=Community_Notification_Standard). See the [this command](https://github.com/itinerare/lorekeeper/blob/15f9ba0a750f4a08d1e3e07139ad32a0b3c7fc9f/app/Console/Commands/FixCharItemNotifs.php) (made for Character Items) for an example of how to use this functionality.

## Extensions Included

Please see the associated wiki page for each extension for more information on their functionality.

- [Draginraptor](https://github.com/Draginraptor) : [Stacked Inventories](http://wiki.lorekeeper.me/index.php?title=Extensions:Stacked_Inventories)
- [itinerare](https://github.com/itinerare) : [Submission Addons](http://wiki.lorekeeper.me/index.php?title=Extensions:Submission_Addons)
- [itinerare](https://github.com/itinerare) : [Character Items](http://wiki.lorekeeper.me/index.php?title=Extensions:Character_Items)
- [Preimpression](https://github.com/preimpression) : [Bootstrap Tables](http://wiki.lorekeeper.me/index.php?title=Extensions:Bootstrap_Tables)
- [itinerare](https://github.com/itinerare) : [Item Entry Expansion (Stacked Inventories version)](http://wiki.lorekeeper.me/index.php?title=Extensions:Item_Entry_Expansion)
- [itinerare](https://github.com/itinerare) : [Watermarking](http://wiki.lorekeeper.me/index.php?title=Extensions:Watermarking)
- [itinerare](https://github.com/itinerare) : [Separate Prompts](http://wiki.lorekeeper.me/index.php?title=Extensions:Separate_Prompts)
- [Preimpression](https://github.com/preimpression) & [Ne-wt](https://github.com/Ne-wt) : [Comments](http://wiki.lorekeeper.me/index.php?title=Extensions:Comments)
- [Ne-wt](https://github.com/Ne-wt) : [Reports](https://github.com/Ne-wt/lorekeeper/tree/reports) : Adds the ability for users to submit general and bug reports, as well as providing a visible list of current, non-sensitive (exploit) bugs.

# Setup

Please see [the Readme](https://github.com/corowne/lorekeeper/blob/master/README.md) or refer to the [Wiki](http://lorekeeper-arpg.wikidot.com/) for general instructions. It is **highly recommended** that you set up core Lorekeeper first and make sure it's functional before modifying it in any fashion.

## Updating from Core or a Previously Modified Version of Core

Depending on how modified your site's copy is, you may be able to simply pull this branch without any resulting conflicts. 

In the case that conflicts do result, or if you need further information, see [Junijwi](https://github.com/juniJwi)'s [tutorial on installing extensions](http://wiki.lorekeeper.me/index.php?title=Tutorial:_Installing_Extensions) for information on resolving conflicts and generally installing extensions. As you will already have added the core Lorekeeper repository as a remote in order to set up Lorekeeper, you can skip to step 2, pulling this branch ("modified-main").

### Extension-specific instructions:

- **Stacked Inventories:** Existing user items in the database with identical owner, source, and notes are not automatically combined; these must be manually edited to be combined if desired. Not doing so will not cause issues, however.
- **Submission Addons:** Before installing, **process any remaining unprocessed submissions**. Otherwise, you will need to edit their data in the database before they can be processed.
- **Character Items:** If you installed this extension already, and did so prior to September 10th, 2020, run `php artisan fix-char-item-notifs`.
- **Comments:** The version included in this branch has been separated from the original package. If you installed the prior version of the extension, run `php artisan view:clear` after installation to clear your view caches.

When ready, run `php artisan migrate` and `php artisan add-site-settings`.

Run `composer update`/`composer install`. You may need to first run `composer update` locally and upload the `composer.lock` file to your site's server if you are on a limited hosting plan/composer requires more memory than you can spare.

## Configuration

- Admin account ID. Notifications for comments on pages are sent to this account. | Default: 1 | Configured in: Site Settings admin panel

### Per-Extension Configuration

**Stacked Inventories:** 
- The maximum number of items purchaseable at once from a shop (in a single transaction) | Default: 99 | Configured in: config/lorekeeper/settings.php

**Character Items:**
- Whether or not items in a category can be held by characters | Default: no | Configured in: Creating/editing an item category
- Whether there is a limit on the number of items of a category a character can own/what that limit is (Note: Admin grants direct to a character do not check against this) | Default: 0/infinite | Configured in: Creating/editing an item category
- Whether stacks in a category can be "named" when in a character's inventory (for instance, in the case of pets) | Default: no | Configured in: Creating/editing an item category

**Watermarking:**

All settings are configured in 'config/lorekeeper/settings.php' and disabled by default.
- Whether or not masterlist images are watermarked | Default: 0/No
- Dimension (in px) to scale the shorter dimension (between width/height) of masterlist images to. Set to 0 to disable resizing. |  Default: 0/Disabled
- Format to convert masterlist images to. Set to null to disable conversion. | Default: null
- Color (hex code) to fill the background of non-png image types when converting images to file formats that do not support transparency. Set to null to disable. Only takes effect when converting to a file format that isn't png. | Default: #ffffff
- Whether to store the full size of masterlist images (relevant if resizing and/or watermarking are enabled). Set to 0 to disable. Not retroactive either way. | Default: 0/Disabled
- Size (in px) to cap full-sized masterlist images at. Images above this cap in either dimension will be resized, retaining aspect ratio. Set to 0 to disable. | Default: 0/Disabled
- Whether or not to watermark masterlist thumbnails. Expects the whole of the character to be visible, so it is recommended to use the thumbnail behavior/disable this if that isn't standard for your site. Set to 0 to disable. | Default: 0/Disabled

**Separate Prompts:**
- It's recommended to customize the index page and/or sidebar for the new prompts section. | Configured in: resources/views/prompts

**Reports:**
- Whether or not reports are open | Default: 1/open | Configured in: Site settings admin panel