@extends('layouts.app')

@section('title')
    Credits
@endsection

@section('content')
    {!! breadcrumbs(['Credits' => url('credits')]) !!}
    <h1>Credits</h1>

    <div class="site-page-content parsed-text">
        {!! $credits->parsed_text !!}
    </div>

    <hr>

    <h4 class="mb-0">Core Extensions</h4>
    <p class="mb-2">These extensions were coded by the Lorekeeper community and are now a part of core Lorekeeper.</p>
    <div class="extensions row no-gutters">
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Bootstrap_Tables"><strong>Bootstrap Tables</strong></a> by <a href="https://github.com/preimpression">Preimpression</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Captcha"><strong>Captcha</strong> (v3)</a> by <a href="https://github.com/itinerare">itinerare</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Character_Items"><strong>Character Items</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Comments"><strong>Comments</strong></a> by <a href="https://github.com/preimpression">Preimpression</a> & <a href="https://github.com/Ne-wt">Ne-wt</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Deactivate_Account"><strong>Deactivate Account</strong></a> by <a href="https://github.com/preimpression">Preimpression</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Extended_Mentions"><strong>Extended Mentions</strong></a> by <a href="https://github.com/SpeedyD">Speedy</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Extension_Tracker"><strong>Extension Tracker</strong></a> by <a href="https://github.com/preimpression">Preimpression</a> (This page/the section below!)
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Galleries"><strong>Galleries</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Masterlist_Sublists"><strong>Masterlist Sublists</strong></a> by <a href="https://github.com/junijwi">Junijwi</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:MYO_Item_Tag"><strong>MYO Item Tag</strong></a> by <a href="https://github.com/junijwi">Junijwi</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Pagination_Page_Select"><strong>Pagination Page Selector</strong></a> by <a href="https://github.com/SpeedyD">Speedy</a> and <a href="https://github.com/AW0005">Moif</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Reports"><strong>Reports</strong></a> by <a href="https://github.com/Ne-wt">Ne-wt</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Separate_Prompts"><strong>Separate Prompts</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Stacked_Inventories"><strong>Stacked Inventories</strong></a> by <a href="https://github.com/Draginraptor">Draginraptor</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Submission_Addons"><strong>Submission Addons</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:User_Character_Minibadges"><strong>User Character Minibadges</strong></a> by <a href="https://github.com/SpeedyD">Speedy</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:User_Transfer_Reasons"><strong>User Transfer Reasons</strong></a> by <a href="https://github.com/snupsplus">Snupsplus</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:90_Percent_Watermark"><strong>Watermark Resizing</strong></a> by <a href="https://github.com/SpeedyD">Speedy</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Watermarking"><strong>Watermarking</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Watermarking_Image_Automation"><strong>Watermarking - Image Automation</strong></a> by <a href="https://github.com/SpeedyD">Speedy</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Aliases_on_Userpage"><strong>Aliases on Userpage</strong></a> by <a href="https://github.com/SpeedyD">Speedy</a>
            ({{ config('lorekeeper.extensions.aliases_on_userpage') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Autopopulate_New_Image_Traits"><strong>Auto-populate New Image Traits</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
            ({{ config('lorekeeper.extensions.autopopulate_image_features') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Character_Prev_and_Next"><strong>Character Prev and Next</strong></a> by <a href="https://github.com/SpeedyD">Speedy</a>
            ({{ config('lorekeeper.extensions.previous_and_next_characters.display') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Character_Rewards"><strong>Character Rewards</strong></a> by <a href="https://github.com/preimpression">Preimpression</a>
            ({{ config('lorekeeper.extensions.character_reward_expansion.expanded') ? 'Enabled' : 'Disabled' }}/{{ config('lorekeeper.extensions.character_reward_expansion.default_recipient') ? 'Submitter' : 'Character Owner' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Character_Status_Badges"><strong>Character Status Badges</strong></a> by <a href="https://github.com/junijwi">Junijwi</a>
            ({{ config('lorekeeper.extensions.character_status_badges') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Character_TH_Profile_Link"><strong>Character TH Profile Link</strong></a> by <a href="https://github.com/junijwi">Junijwi</a>
            ({{ config('lorekeeper.extensions.character_TH_profile_link') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Collapsible_Admin_Sidebar"><strong>Collapsible Admin Sidebar</strong></a> by <a href="https://github.com/ne-wt/">Newt</a>
            ({{ config('lorekeeper.extensions.collapsible_admin_sidebar') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Design_Update_Voting"><strong>Design Update Voting</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
            ({{ config('lorekeeper.extensions.design_update_voting') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Gravatar"><strong>Gravatar</strong></a> by <a href="https://github.com/ne-wt/">Newt</a> ({{ config('lorekeeper.extensions.use_gravatar') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Group_Traits_by_Category"><strong>Group Traits by Category</strong></a> by <a href="https://github.com/preimpression">Preimpression</a>
            ({{ config('lorekeeper.extensions.traits_by_category') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Item_Entry_Expansion"><strong>Item Entry Expansion</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
            ({{ config('lorekeeper.extensions.item_entry_expansion.extra_fields') ? 'Enabled' : 'Disabled' }}/{{ config('lorekeeper.extensions.item_entry_expansion.resale_function') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:MYO_Image_Removal"><strong>MYO Image Removal</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
            ({{ config('lorekeeper.extensions.remove_myo_image') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Navbar_News_Notif"><strong>Navbar News Notif</strong></a> by <a href="https://github.com/junijwi">Junijwi</a>
            ({{ config('lorekeeper.extensions.navbar_news_notif') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Organised_Traits_Dropdown"><strong>Organised Traits Dropdown</strong></a> by <a href="https://github.com/draginraptor">Draginraptor</a>
            ({{ config('lorekeeper.extensions.organised_traits_dropdown') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Gallery_Recent_Submissions"><strong>Show All Recent Submissions</strong></a> by <a href="https://github.com/SpeedyD">Speedy</a>
            ({{ config('lorekeeper.extensions.show_all_recent_submissions.enable') ? 'Enabled' . ((config('lorekeeper.extensions.show_all_recent_submissions.links.sidebar') && config('lorekeeper.extensions.show_all_recent_submissions.links.indexbutton') ? '/Both' : (config('lorekeeper.extensions.show_all_recent_submissions.links.sidebar') ? '/Sidebar' : '') . (config('lorekeeper.extensions.show_all_recent_submissions.links.indexbutton') ? '/Button' : '')) . (config('lorekeeper.extensions.show_all_recent_submissions.section_on_front') ? '/Front' : '')) : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Species_Trait_Index"><strong>Species Trait Index</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
            ({{ config('lorekeeper.extensions.species_trait_index.enable') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Staff_Rewards"><strong>Staff Rewards</strong></a> by <a href="https://github.com/itinerare">itinerare</a>
            ({{ config('lorekeeper.extensions.staff_rewards.enabled') ? 'Enabled' : 'Disabled' }})
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Submission_Drafts"><strong>Submission Drafts</strong></a> by <a href="https://github.com/preimpression/">Preimpression</a>
        </p>
        <p class="mb-0 col-md-4">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:Universal_Trait_Index"><strong>Universal Trait Index</strong></a> by <a href="https://github.com/CH3RVB">CH3RVB</a>
            ({{ config('lorekeeper.extensions.universal_trait_index.enable') ? 'Enabled' : 'Disabled' }})
        </p>
    </div>

    <hr />
    <h4 class="mb-0">Installed Extensions</h4>
    <p class="mb-2">These extensions have been added to this site.</p>

    @if ($extensions->count())
        <div class="extensions row no-gutters">
            @foreach ($extensions as $extension)
                <p class="mb-0 col-md-4">
                    <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:{{ $extension->wiki_key }}">
                        <strong>{{ str_replace('_', ' ', $extension->wiki_key) }}</strong>
                        <small>v. {{ $extension->version }}</small>
                    </a>
                    by
                    <?php $extension->array = json_decode($extension->creators, true);
                    $extension->end = end($extension->array); ?>
                    @foreach (json_decode($extension->creators, true) as $name => $url)
                        <a href="{{ $url }}">{{ $name }}</a>{{ $extension->end != $extension->array[$name] ? ',' : '' }}
                    @endforeach
                </p>
            @endforeach
        </div>
    @else
        <p>No extensions found.</p>
    @endif

@endsection
