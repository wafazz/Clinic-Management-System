<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Membership Tier</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('membership-tiers.store') }}">
            @csrf
            @include('membership-tiers._form', ['tier' => null])
            <div class="d-flex justify-content-end">
                <a href="{{ route('membership-tiers.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Create</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
