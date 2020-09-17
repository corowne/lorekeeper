<?php

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
|
| Miscellaneous helper functions, primarily used for formatting.
|
*/

/**
 * Returns class name if the current URL corresponds to the given path.
 *
 * @param  string  $path
 * @param  string  $class
 * @return string
 */
function set_active($path, $class = 'active') {
    return call_user_func_array('Request::is', (array)$path) ? $class : '';
}

/**
 * Adds a help icon with a tooltip.
 *
 * @param  string  $text
 * @return string
 */
function add_help($text) {
    return '<i class="fas fa-question-circle help-icon" data-toggle="tooltip" title="'.$text.'"></i>';
}

/**
 * Uses the given array to generate breadcrumb links.
 *
 * @param  array  $links
 * @return string
 */
function breadcrumbs($links) {
    $ret = '<nav><ol class="breadcrumb">';
    $count = 0;
    $ret .= '<li class="breadcrumb-item"><a href="'.url('/').'">'.config('lorekeeper.settings.site_name', 'Lorekeeper').'</a></li>';
    foreach($links as $key => $link) {
        $isLast = ($count == count($links) - 1);

        $ret .= '<li class="breadcrumb-item ';
        if($isLast) $ret .= 'active';
        $ret .= '">';

        if(!$isLast) $ret .= '<a href="'.url($link).'">';
        $ret .=  $key;
        if(!$isLast) $ret .= '</a>';

        $ret .= '</li>';

        $count++;
    }
    $ret .= '</ol></nav>';

    return $ret;
}

/**
 * Formats the timestamp to a standard format.
 *
 * @param  \Illuminate\Support\Carbon\Carbon  $timestamp
 * @return string
 */
function format_date($timestamp, $showTime = true) {
    return $timestamp->format('j F Y' . ($showTime ? ', H:i:s' : '')) . ($showTime ? ' <abbr data-toggle="tooltip" title="UTC'.$timestamp->timezone->toOffsetName().'">' . strtoupper($timestamp->timezone->getAbbreviatedName($timestamp->isDST())) . '</abbr>' : '');
}

function pretty_date($timestamp, $showTime = true) {
   return '<abbr data-toggle="tooltip" title="' . $timestamp->format('F j Y' . ($showTime ? ', H:i:s' : '')) . ' ' . strtoupper($timestamp->timezone->getAbbreviatedName($timestamp->isDST())).'">' .$timestamp->diffForHumans() . '</abbr>';
}

/**
 * Formats a number to fit the number of digits given,
 * for generating masterlist numbers.
 *
 * @param  \Illuminate\Support\Carbon\Carbon  $timestamp
 * @return string
 */
function format_masterlist_number($number, $digits) {
    return sprintf('%0'.$digits.'d', $number);
}

/**
 * Parses a piece of user-entered text for HTML output and optionally gets pings.
 *
 * @param  string  $text
 * @param  array   $pings
 * @return string
 */
function parse($text, &$pings = null) {
    if(!$text) return null;

    require_once(base_path().'/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php');

    $config = HTMLPurifier_Config::createDefault();
    $config->set('Attr.EnableID', true);
    $config->set('HTML.DefinitionID', 'include');
    $config->set('HTML.DefinitionRev', 2);
	$config->set('Cache.DefinitionImpl', null); // TODO: remove this later!
    if ($def = $config->maybeGetRawHTMLDefinition()) {
        $def->addElement('include', 'Block', 'Empty', 'Common', array('file*' => 'URI', 'height' => 'Text', 'width' => 'Text'));
		$def->addAttribute('a', 'data-toggle', 'Enum#collapse,tab');
		$def->addAttribute('a', 'aria-expanded', 'Enum#true,false');
		$def->addAttribute('a', 'data-target', 'Text');
		$def->addAttribute('div', 'data-parent', 'Text');

    }

    $purifier = new HTMLPurifier($config);
    $text = $purifier->purify($text);

    $users = $characters = null;
    $text = parseUsers($text, $users);
    $text = parseCharacters($text, $characters);
    if($pings) $pings = ['users' => $users, 'characters' => $characters];

    return $text;
}

/**
 * Parses a piece of user-entered text to match user mentions
 * and replace with a link.
 *
 * @param  string  $text
 * @param  mixed   $users
 * @return string
 */
function parseUsers($text, &$users) {
    $matches = null;
    $users = [];
    $count = preg_match_all('/\B@([A-Za-z0-9_-]+)/', $text, $matches);
    if($count) {
        $matches = array_unique($matches[1]);
        foreach($matches as $match) {
            $user = \App\Models\User\User::where('name', $match)->first();
            if($user) {
                $users[] = $user;
                $text = preg_replace('/\B@'.$match.'/', $user->displayName, $text);
            }
        }
    }

    return $text;
}

/**
 * Parses a piece of user-entered text to match character mentions
 * and replace with a link.
 *
 * @param  string  $text
 * @param  mixed   $characters
 * @return string
 */
function parseCharacters($text, &$characters) {
    $matches = null;
    $characters = [];
    $count = preg_match_all('/\[character=([^\[\]&<>?"\']+)\]/', $text, $matches);
    if($count) {
        $matches = array_unique($matches[1]);
        foreach($matches as $match) {
            $character = \App\Models\Character\Character::where('slug', $match)->first();
            if($character) {
                $characters[] = $character;
                $text = preg_replace('/\[character='.$match.'\]/', $character->displayName, $text);
            }
        }
    }

    return $text;
}

/**
 * Generates a string of random characters of the specified length.
 *
 * @param  int  $characters
 * @return string
 */
function randomString($characters)
{
    $src = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $code = '';
    for ($i = 0; $i < $characters; $i++) $code .= $src[mt_rand(0, strlen($src) - 1)];
    return $code;
}
