<?php
$vals = [true, false, false];
if (isset($lineageBlacklist))
{
    $vals[0] = false;
    $vals[$lineageBlacklist->complete_removal ? 2 : 1] = true;
}
$opts = ['category', 'species', 'subtype', 'rarity'];
foreach ($opts as $key => $value) {
    if ($value == $type) unset($opts[$key]);
}
$opts = array_values($opts);
?>
<h3>Lineage Blacklist</h3>
<div class="form-check mb-1">
    <label class="form-check-label">
        {!! Form::radio('lineage-blacklist', '0', $vals[0], ['class' => 'mr-1']) !!}
        No restriction.
        <span class="text-muted font-italic">
            Characters will have lineage as long as the {{ $opts[0] }}, {{ $opts[1] }} and {{ $opts[2] }} also allow it.
        </span>
    </label>
</div>
<div class="form-check mb-1">
    <label class="form-check-label">
        {!! Form::radio('lineage-blacklist', '1', $vals[1], ['class' => 'mr-1']) !!}
        Characters with this {{ $type }} can have ancestors but not descendants.
        <span class="text-muted font-italic">Such as mules, hybrids, children, etc.</span>
    </label>
</div>
<div class="form-check disabled mb-2">
    <label class="form-check-label">
        {!! Form::radio('lineage-blacklist', '2', $vals[2], ['class' => 'mr-1']) !!}
        Characters with this {{ $type }} cannot have lineages at all.
        <span class="text-muted font-italic">Such as locations, artifacts, etc.</span>
    </label>
</div>
