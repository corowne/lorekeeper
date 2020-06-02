# Lorekeeper

Lorekeeper is a framework for managing deviantART-based ARPGs/closed species masterlists coded using the Laravel framework. In simple terms - you will be able to make a copy of the site, do some minor setup/enter data about your species and game, and it'll provide you with the automation to keep track of your species, players and ARPG submissions.

Demo site: [http://lorekeeper.me/](http://lorekeeper.me/)
Wiki for users: [http://lorekeeper-arpg.wikidot.com/](http://lorekeeper-arpg.wikidot.com/)
Original git repository: [https://github.com/corowne/lorekeeper](https://github.com/corowne/lorekeeper)

# Info

This fork was set up for the purpose of sharing some of the changes I made. These changes are usually merged to master, but can also be found in their own branches.

## inventory_stacks

Special thanks to [itinerare](https://github.com/itinerare) for isolating and testing the changes.

This changes the default inventory in Lorekeeper from displaying each user_item row as a stack of 1, and instead condenses duplicate entries into stacks. This has affected Inventory, Trades, and Design Updates the most, though there could still be remnants of code that still aren't using the new system.

Once the changes are pulled, the database needs to be updated as well - this can be done with:

```
$ php artisan migrate
```

The migrations will add 2 new columns to user_items: trade_count and update_count, for tracking items held in trades and updates respectively. It will also change the default value of count in user_items to 0.

Note that existing data in the database will need to be edited such that duplicate entries (where the item_id, user_id, and data are the same) need to be combined separately.

You could just update each row's count column to reflect the total count at that point in time, leaving the duplicate entries alone. I'm unsure if it will break anything, but I don't think so.

You can also delete the duplicate rows once the count column is updated. However, this will probably require deleting the item logs associated with the affected stacks, unless you create your own workaround.

I have included some SQL that you can reference in creating a query, but it is unlikely to work out of the box. Alternatively, you can also edit the database manually. Either way, ALWAYS backup your database before making changes.

The migrations do not remove holding_type and holding_id, which are not used in this version of the inventory; these may be left in or removed on your own.

## embed_service

This adds the EmbedController and EmbedService, which makes use of [oscarotero/Embed](https://github.com/oscarotero/Embed) library.

You will need to install the above library and have at least one of [these PSR-7 libraries](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations). The composer.json has already been updated to include these libraries, so if you don't want to customise, just run `composer update` after pulling the branch.

### How to use

For server-side queries, add the EmbedService to the target file. Create an instance of the service to call getEmbed(), which only takes one argument: an URL. It will return an OEmbed response if it finds one. The library is able to return a variety of different responses, so don't be afraid to read up the documentation and change it to suit your needs!

For client-side queries, you can use jQuery's get() function to query the controller, which will handle the communication between the client and service. The controller also does validation to ensure that the input is actually in a URL format, and is from an accepted domain. 
Currently, it only accepts dA URLs, but can be have other sites added, or just have that part of the validation removed entirely.
The controller will also process the response to return only the image URL and thumbnail URL - you can configure these to your needs as necessary. 
