<?php

function getAssetKeys($isCharacter = false)
{
    if(!$isCharacter) return ['items', 'currencies', 'raffle_tickets', 'loot_tables'];
    else return ['currencies'];
}

function getAssetModelString($type)
{
    switch($type)
    {
        case 'items':
            return '\App\Models\Item\Item';
            break;
        
        case 'currencies':
            return '\App\Models\Currency\Currency';
            break;
            
        case 'raffle_tickets':
            return '\App\Models\Raffle\Raffle';
            break;

        case 'loot_tables':
            return '\App\Models\Loot\LootTable';
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