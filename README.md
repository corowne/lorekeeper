# Lorekeeper

Lorekeeper is a framework for managing deviantART-based ARPGs/closed species masterlists coded using the Laravel framework. In simple terms - you will be able to make a copy of the site, do some minor setup/enter data about your species and game, and it'll provide you with the automation to keep track of your species, players and ARPG submissions.

Demo site: [http://lorekeeper.me/](http://lorekeeper.me/)
Wiki for users: [http://lorekeeper-arpg.wikidot.com/](http://lorekeeper-arpg.wikidot.com/)

# Info

This is one of several branches I maintain for sharing modifications or projects I've made. For my general fixes branch, see master.

# submission-addons

Many thanks to to [draginraptor](https://github.com/Draginraptor), whose [inventory_stacks](https://github.com/Draginraptor/lorekeeper/tree/inventory_stacks) branch this builds uoon!

This adds the option for users to attach items to submissions (both via prompts and claims) to be consumed. As with design updates and trades, items are only temporarily "held" and are returned if the submission is rejected. In the event that the submission is accepted, the usual item logs are made both for any awarded and any consumed items, and record of the attached item(s) remains visible on the submission's page-- including source and notes.

This requires running

```
$ php artisan migrate
```

This adds an additional column to user_items, `submission_count`, which tracks items held in submissions.
