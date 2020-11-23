{!! Form::open(['url' => 'admin/'. ($isMyo ? 'myo/'.$character->id : 'character/'.$character->slug) .'/lineage']) !!}
    <div class="alert alert-warning">Custom ancestor names are only used when there is no live character ID set for that ancestor. DO NOT use it if there is no ancestor, leave it blank. Ancestor names and "unknown"s will be generated automatically.</div>

    <?php
        // Reduce errors and repetition
        $k = [
            'sire',
            'sire_sire',
            'sire_sire_sire',
            'sire_sire_dam',
            'sire_dam',
            'sire_dam_sire',
            'sire_dam_dam',
            'dam',
            'dam_sire',
            'dam_sire_sire',
            'dam_sire_dam',
            'dam_dam',
            'dam_dam_sire',
            'dam_dam_dam'
        ];
        // Human-readable names for the things
        $j = [
            "Sire",
            "Sire's Sire",
            "Sire's Sire's Sire",
            "Sire's Sire's Dam",
            "Sire's Dam",
            "Sire's Dam's Sire",
            "Sire's Dam's Dam",
            "Dam",
            "Dam's Sire",
            "Dam's Sire's Sire",
            "Dam's Sire's Dam",
            "Dam's Dam",
            "Dam's Dam's Sire",
            "Dam's Dam's Dam",
        ];
        ?>
    <div class="row">
        <div class="col-lg-6">
            @for ($i=0; $i < 14; $i++)
                <?php $em = ($i < 2 || $i == 4 || ($i > 6 && $i < 9) || $i == 11); ?>
                <div class="form-group text-center {{ $em ? 'pb-1 border-bottom' : '' }}">
                    {!! Form::label($j[$i], null, ['class' => $em ? 'font-weight-bold' : '']) !!}
                    <div class="row">
                        <div class="col-sm-6 pr-sm-1">
                            {!! Form::select($k[$i].'_id', $characterOptions, !old($k[$i].'_id') ? $lineage[$k[$i].'_id'] : old($k[$i].'_id'), ['class' => 'form-control text-left character-select mb-1', 'placeholder' => 'None']) !!}
                        </div>
                        <div class="col-sm-6 pl-sm-1">
                            {!! Form::text($k[$i].'_name', !old($k[$i].'_name') ? $lineage[$k[$i].'_name'] : old($k[$i].'_name'), ['class' => 'form-control mb-1']) !!}
                        </div>
                    </div>
                </div>
                @if ($i == 6)
                    </div>
                    <div class="col-lg-6">
                @endif
            @endfor
        </div>
    </div>

    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="generate_ancestors" value="true" id="generate_ancestors">
        <label class="form-check-label" for="generate_ancestors">
            autofill lineage based on the currently input ancestors?
        </label>
    </div>

    @if (!$isMyo)
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="update_descendants" value="true" id="update_descendants">
            <label class="form-check-label" for="update_descendants">
                find all the descendants of this character and update their lineages with these changes?
            </label>
        </div>
    @endif

    <div class="text-right">
        {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}
<script>
    $(document).ready(function() {
        // lets ancestor ids be searched through typing, very nice
        $('.character-select').selectize();
    });
</script>
