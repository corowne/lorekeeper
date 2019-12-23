<h1>Welcome, {!! Auth::user()->displayName !!}!</h1>
<div class="card mb-4">
    <div class="card-body">
        <i class="far fa-clock"></i> {{ format_date(Carbon\Carbon::now()) }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('images/account.png') }}" />
                <h5 class="card-title">Account</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Profile</li>
                <li class="list-group-item">User Settings</li>
                <li class="list-group-item">Trades</li>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('images/characters.png') }}" />
                <h5 class="card-title">Characters</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">My Characters</li>
                <li class="list-group-item">Character Transfer Log</li>
                <li class="list-group-item">Bookmarks</li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('images/inventory.png') }}" />
                <h5 class="card-title">Inventory</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">My Inventory</li>
                <li class="list-group-item">Inventory Log</li>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ asset('images/currency.png') }}" />
                <h5 class="card-title">Bank</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Bank</li>
                <li class="list-group-item">Currency Log</li>
            </ul>
        </div>
    </div>
</div>