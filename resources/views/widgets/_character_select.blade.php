<div id="characterComponents" class="hide">
    <div class="submission-character mb-3 card">
        <div class="card-body">
            <div class="text-right"><a href="#" class="remove-character text-muted"><i class="fas fa-times"></i></a></div>
            <div class="row">
                <div class="col-md-2 align-items-stretch d-flex">
                    <div class="d-flex text-center align-items-center">
                        <div class="character-image-blank">Enter character code.</div>
                        <div class="character-image-loaded hide"></div>
                    </div>
                </div>
                <div class="col-md-10">
                    <a href="#" class="float-right fas fa-close"></a>
                    <div class="form-group">
                        {!! Form::label('slug[]', 'Character Code') !!}
                        {!! Form::text('slug[]', null, ['class' => 'form-control character-code']) !!}
                    </div>
                    <div class="character-rewards hide">
                        <h4>Character Rewards</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="70%">Reward</th>
                                    <th width="30%">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="character-rewards">
                            </tbody>
                        </table>
                        <div class="text-right">
                            <a href="#" class="btn btn-outline-primary btn-sm add-reward">Add Reward</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <table>
        <tr class="character-reward-row">
            <td>
                {!! Form::select('character_currency_id[]', $characterCurrencies, 0, ['class' => 'form-control currency-id']) !!}
            </td>
            <td class="d-flex align-items-center">
                {!! Form::text('character_quantity[]', 0, ['class' => 'form-control mr-2 quantity']) !!}
                <a href="#" class="remove-reward d-block"><i class="fas fa-times text-muted"></i></a>
            </td>
        </tr>
    </table>
</div>