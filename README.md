# Lorekeeper

Lorekeeper is a framework for managing deviantART-based ARPGs/closed species masterlists coded using the Laravel framework. In simple terms - you will be able to make a copy of the site, do some minor setup/enter data about your species and game, and it'll provide you with the automation to keep track of your species, players and ARPG submissions.

## Modified Main

This version of Lorekeeper is modified. It contains several extensions-- additional or modified parts of core Lorekeeper which change or add functions and behavior-- selected for their wide applicability, and for being no more obtrusive than effectively optional functions in core Lorekeeper if not in use. It also serves as a base for developing extensions, providing several common 'dependencies'.

- Demo site: [http://lorekeeper.me/](http://lorekeeper.me/)
- Wiki for users: [http://lorekeeper-arpg.wikidot.com/](http://lorekeeper-arpg.wikidot.com/)
- Extensions wiki: [http://wiki.lorekeeper.me/index.php?title=Category:Extensions](http://wiki.lorekeeper.me/index.php?title=Category:Extensions)

# Features

- **Core Lorekeeper:** Please see [the Readme](https://github.com/corowne/lorekeeper/blob/master/README.md) and [feature documentation](http://lorekeeper-arpg.wikidot.com/) for more information.
- **Grouped Notifications:** To account for the potentially large variety and potentially volume of notifications, they are grouped by notification type and collapse when there are more than 5 notifications of a type.
- **Extension Service:** A utility for use by extension developers. By default, facilitates adjusting notification type IDs in a site's DB to comply with [community notification standards](). See the [this command]() (made for Character Items) for an example of how to use this functionality.

## Extensions Included

- **@Draginraptor : [Stacked Inventories](http://wiki.lorekeeper.me/index.php?title=Extensions:Stacked_Inventories)**: Changes the default behavior of items from displaying each copy of an item individually to "stacking" them. The inventory stack modal-- which by default displays source and note information of the item-- contains a table with the source, notes, and quantities of all stacks of the item, including the quantities of any items held in design updates or trades (and where they're held). Also allows users to purchase multiple of an item from shops in one transaction.
- 

# Setup

Please see [the Readme](https://github.com/corowne/lorekeeper/blob/master/README.md) or refer to the [Wiki](http://lorekeeper-arpg.wikidot.com/) for general instructions. It is recommended that you set up core Lorekeeper first and make sure it's functional before modifying it in any fashion.

## Updating from Core or a Previously Modified Version of Core

Depending on how modified your site's copy is, you may be able to simply pull this branch without any resulting conflicts. 

In the case that conflicts do result, or if you need further information, see @junijwi's [tutorial on installing extensions](http://wiki.lorekeeper.me/index.php?title=Tutorial:_Installing_Extensions) for information on resolving conflicts and generally installing extensions. As you will already have added the core Lorekeeper repository as a remote in order to set up Lorekeeper, you can skip to step 2, pulling this branch ("modified-main").

## Per-Extension Configuration

**Stacked Inventories:** 
- The maximum number of items purchaseable at once from a shop (in a single transaction)
- Default: 99
- Configured in: config/lorekeeper/settings.php