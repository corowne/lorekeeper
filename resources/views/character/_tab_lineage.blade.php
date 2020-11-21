@if($character->lineage !== null)
    <?php $line = $character->lineage; ?>
    @include('character._lineage_tree', [
        'line' => [
            'sire' =>           $line->getDisplaySire(),
            'sire_sire' =>      $line->getDisplaySireSire(),
            'sire_sire_sire' => $line->getDisplaySireSireSire(),
            'sire_sire_dam' =>  $line->getDisplaySireSireDam(),
            'sire_dam' =>       $line->getDisplaySireDam(),
            'sire_dam_sire' =>  $line->getDisplaySireDamSire(),
            'sire_dam_dam' =>   $line->getDisplaySireDamDam(),
            'dam' =>            $line->getDisplayDam(),
            'dam_sire' =>       $line->getDisplayDamSire(),
            'dam_sire_sire' =>  $line->getDisplayDamSireSire(),
            'dam_sire_dam' =>   $line->getDisplayDamSireDam(),
            'dam_dam' =>        $line->getDisplayDamDam(),
            'dam_dam_sire' =>   $line->getDisplayDamDamSire(),
            'dam_dam_dam' =>    $line->getDisplayDamDamDam(),
        ]])
@else
    @include('character._lineage_tree', [
        'line' => [
            'sire' => "Unknown",
            'sire_sire' => "Unknown",
            'sire_sire_sire' => "Unknown",
            'sire_sire_dam' => "Unknown",
            'sire_dam' => "Unknown",
            'sire_dam_sire' => "Unknown",
            'sire_dam_dam' => "Unknown",
            'dam' => "Unknown",
            'dam_sire' => "Unknown",
            'dam_sire_sire' => "Unknown",
            'dam_sire_dam' => "Unknown",
            'dam_dam' => "Unknown",
            'dam_dam_sire' => "Unknown",
            'dam_dam_dam' => "Unknown",
        ]])
@endif
@if(Auth::check() && Auth::user()->hasPower('manage_characters'))
    <div class="mt-3">
        <a href="#" class="btn btn-outline-info btn-sm edit-lineage" data-{{ $character->is_myo_slot ? 'id' : 'slug' }}="{{ $character->is_myo_slot ? $character->id : $character->slug }}"><i class="fas fa-cog"></i> Edit</a>
    </div>
@endif
