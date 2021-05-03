{!! Form::label('Group (Optional)') !!} {!! add_help('This is used for character drops. If no value is set, it will be randomly rolled from the species\' groups.') !!}
{!! Form::select('parameters', $parameters, old('parameters'), ['class' => 'form-control', 'id' => 'parameter']) !!}
