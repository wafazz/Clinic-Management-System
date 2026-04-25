<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit Tier — {{ $membershipTier->name }}</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('membership-tiers.update', $membershipTier) }}">
            @csrf @method('PUT')
            @include('membership-tiers._form', ['tier' => $membershipTier])
            <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" id="active" class="form-check-input" {{ $membershipTier->is_active ? 'checked' : '' }}><label for="active" class="form-check-label">Active</label></div>
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('membership-tiers.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div></div>
</x-app-layout>
