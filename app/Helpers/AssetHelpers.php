<?php

function getAssetKeys()
{
    return ['items', 'currencies'];
}

function createAssetsArray()
{
    $keys = getAssetKeys();
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
    else $array[$asset->assetType][] = ['asset' => $asset, 'quantity' => $quantity];
}