{!! Form::label('Subtype (Optional)') !!} {!! add_help('This is cosmetic and does not limit choice of traits in selections.') !!}
{!! Form::select('subtype_id', $subtypes, $subtype_id, ['class' => 'form-control', 'id' => 'subtype']) !!}
