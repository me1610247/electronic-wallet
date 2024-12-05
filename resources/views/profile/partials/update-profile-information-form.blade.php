<section class="bg-white shadow sm:rounded-lg p-8">
    <header class="mb-6 border-b pb-4">
        <h2 class="text-3xl font-semibold text-gray-900">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Name Field -->
        <div class="grid gap-2">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input 
                id="name" 
                name="name" 
                type="text" 
                class="block w-full border-gray-300 focus:border-indigo-600 focus:ring-indigo-600 rounded-md shadow-sm" 
                :value="old('name', $user->name)" 
                required 
            />
            <x-input-error class="text-red-600" :messages="$errors->get('name')" />
        </div>

        <!-- Email Field -->
        <div class="grid gap-2">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input 
                id="email" 
                name="email" 
                type="email" 
                class="block w-full border-gray-300 focus:border-indigo-600 focus:ring-indigo-600 rounded-md shadow-sm" 
                :value="old('email', $user->email)" 
                required 
            />
            <x-input-error class="text-red-600" :messages="$errors->get('email')" />
        </div>

        <!-- Wallet Balance -->
        <div class="bg-gray-50 p-4 rounded-lg shadow-md">
            <x-input-label for="wallet-balance" :value="__('Wallet Balance')" class="text-xl font-bold text-gray-900" />
            <p id="wallet-balance" class="mt-2 text-lg font-semibold text-green-600">
                {{ __('Current Balance: $') . number_format($wallet->balance, 2) }}
            </p>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Use the form below to add funds to your wallet.') }}
            </p>
        </div>

        <!-- Save Button -->
        <div class="flex items-center gap-4">
            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                {{ __('Save') }}
            </x-primary-button>
            @if (session('status') === 'profile-updated')
                <p 
                    x-data="{ show: true }" 
                    x-show="show" 
                    x-transition 
                    x-init="setTimeout(() => show = false, 2000)" 
                    class="text-sm text-green-600">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>

    <!-- Add Funds to Wallet -->
    <div class="mt-8 bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            {{ __('Add Funds to Your Wallet') }}
        </h3>
        <form method="POST" action="{{ route('wallet.paypal') }}" class="space-y-4">
            @csrf
            <div class="grid gap-2">
                <x-input-label for="amount" :value="__('Amount to Add')" />
                <x-text-input 
                    id="amount" 
                    name="amount" 
                    type="number" 
                    step="0.01" 
                    class="block w-full border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm" 
                    placeholder="Enter amount in USD" 
                    required 
                />
            </div>
            <x-primary-button class="w-full bg-blue-500 hover:bg-blue-600">
                {{ __('Pay with PayPal') }}
            </x-primary-button>
        </form>
    </div>
</section>