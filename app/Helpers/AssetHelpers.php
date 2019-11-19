<?php

function getAssetKeys($isCharacter = false)
{
    if(!$isCharacter) return ['items', 'currencies', 'raffle_tickets', 'loot_tables', 'user_items', 'characters'];
    else return ['currencies'];
}

function getAssetModelString($type, $namespaced = true)
{
    switch($type)
    {
        case 'items':
            if($namespaced) return '\App\Models\Item\Item';
            else return 'Item';
            break;
        
        case 'currencies':
            if($namespaced) return '\App\Models\Currency\Currency';
            else return 'Currency';
            break;
            
        case 'raffle_tickets':
            if($namespaced) return '\App\Models\Raffle\Raffle';
            else return 'Raffle';
            break;

        case 'loot_tables':
            if($namespaced) return '\App\Models\Loot\LootTable';
            else return 'LootTable';
            break;
            
        case 'user_items':
            if($namespaced) return '\App\Models\User\UserItem';
            else return 'UserItem';
            break;
            
        case 'characters':
            if($namespaced) return '\App\Models\Character\Character';
            else return 'Character';
            break;
    }
    return null;
}

function createAssetsArray($isCharacter = false)
{
    $keys = getAssetKeys($isCharacter);
    $assets = [];
    foreach($keys as $key) $assets[$key] = [];
    return $assets;
}

function mergeAssetsArrays($first, $second)
{
    $keys = getAssetKeys();
    foreach($keys as $key)
        foreach($second[$key] as $item)
            addAsset($first, $item['asset'], $item['quantity']);
    return $first;
}

function addAsset(&$array, $asset, $quantity = 1)
{
    if(isset($array[$asset->assetType][$asset->id])) $array[$asset->assetType][$asset->id]['quantity'] += $quantity;
    else $array[$asset->assetType][$asset->id] = ['asset' => $asset, 'quantity' => $quantity];
}

// Get a clean version of the asset array to store in DB,
// where each asset is listed in [id => quantity] format
function getDataReadyAssets($array, $isCharacter = false)
{
    $result = [];
    foreach($array as $key => $type)
    {
        if($type && !isset($result[$key])) $result[$key] = [];
        foreach($type as $assetId => $assetData)
        {
            $result[$key][$assetId] = $assetData['quantity'];
        }
    }
    return $result;
}

// Retrieves the data associated with an asset array,
// basically reverses the above function
function parseAssetData($array)
{
    $assets = createAssetsArray();
    foreach($array as $key => $contents)
    {
        $model = getAssetModelString($key);
        if($model)
        {
            foreach($contents as $id => $quantity)
            {
                $assets[$key][$id] = [
                    'asset' => $model::find($id),
                    'quantity' => $quantity
                ];
            }

        }
    }
    return $assets;
}

// Distributes the assets in an array to the given recipient.
// Loot tables will be rolled before distribution.
function fillUserAssets($assets, $sender, $recipient, $logType, $data)
{
    // Roll on any loot tables
    if(isset($assets['loot_tables']))
    {
        foreach($assets['loot_tables'] as $table)
        {
            $assets = mergeAssetsArrays($assets, $table['asset']->roll($table['quantity']));
        }
        unset($assets['loot_tables']);
    }

    foreach($assets as $key => $contents)
    {
        if($key == 'items' && count($contents))
        {
            $service = new \App\Services\InventoryManager;
            foreach($contents as $asset)
                if(!$service->creditItem($sender, $recipient, $logType, $data, $asset['asset'], $asset['quantity'])) return false;
        }
        elseif($key == 'currencies' && count($contents))
        {
            $service = new \App\Services\CurrencyManager;
            foreach($contents as $asset)
                if(!$service->creditCurrency($sender, $recipient, $logType, $data['data'], $asset['asset'], $asset['quantity'])) return false;
        }
        elseif($key == 'raffle_tickets' && count($contents))
        {
            $service = new \App\Services\RaffleManager;
            foreach($contents as $asset)
                if(!$service->addTicket($recipient, $asset['asset'], $asset['quantity'])) return false;
        }
        elseif($key == 'user_items' && count($contents))
        {
            $service = new \App\Services\InventoryManager;
            foreach($contents as $asset)
                if(!$service->moveStack($sender, $recipient, $logType, $data, $asset['asset'])) return false;
        }
        elseif($key == 'characters' && count($contents))
        {
            $service = new \App\Services\CharacterManager;
            foreach($contents as $asset)
                if(!$service->moveCharacter($asset['asset'], $recipient, $data, $asset['quantity'], $logType)) return false;
        }
    }
    return $assets;
}

// Distributes the assets in an array to the given character.
function fillCharacterAssets($assets, $sender, $recipient, $logType, $data)
{
    foreach($assets as $key => $contents)
    {
        if($key == 'currencies' && count($contents))
        {
            $service = new \App\Services\CurrencyManager;
            foreach($contents as $asset)
                if(!$service->creditCurrency($sender, $recipient, $logType, $data['data'], $asset['asset'], $asset['quantity'])) return false;
        }
    }
    return true;
}