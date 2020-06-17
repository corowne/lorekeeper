# Lorekeeper

Lorekeeper is a framework for managing deviantART-based ARPGs/closed species masterlists coded using the Laravel framework. In simple terms - you will be able to make a copy of the site, do some minor setup/enter data about your species and game, and it'll provide you with the automation to keep track of your species, players and ARPG submissions.

Demo site: [http://lorekeeper.me/](http://lorekeeper.me/)
Wiki for users: [http://lorekeeper-arpg.wikidot.com/](http://lorekeeper-arpg.wikidot.com/)

# Info

This is one of several branches I maintain for sharing modifications or projects I've made. For my general fixes branch, see main.

# character-items

This adds item inventories to characters, as well as management of character items. This includes:

* Character inventory pages
* Character inventory logs
* Granting of items directly to characters
* Attachment of items to characters (via the inv select widget)
* A separate inventory stack modal for characters, which handles:
* Transferal of items back to the owning user
* Deletion of items direct from character inventory

It also changes item logging to handle this by making it akin to currency logging. That is, the existing user_items_log table is renamed to items_log, and has sender and recipient type columns added. Existing log entries are updated in-place and treated as user <-> user.

Character items are also held in their own table. Transferring items between user and character maintains source and notes-- which makes for some slightly strange logs, as the source is re-logged (though the log type is clearly labeled as user <-> character depending on the nature of the transfer).

Items are not automatically available to be held by characters. Doing so is done via item categories; these now have a toggle on their admin panel interface for the purpose. Additionally, limits may be set per category for the number of items a character may hold. By default/if left empty, it is set to 0/infinite. This is also done via the admin panel interface.

See [here](http://wiki.lorekeeper.me/index.php?title=Extensions:Character_Items) for extended information and instructions!