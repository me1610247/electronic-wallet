<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800">
            {{ __('Dashboard') }}
        </h2>
        <div id="notification" class="fixed top-4 left-1/2 transform -translate-x-1/2 text-center hidden px-4 py-2 rounded-md shadow-lg transition duration-300">
            <p id="notification-message" class="text-white"></p>
        </div>
        <section class="py-6">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-md rounded-md p-6">
                    <header class="mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ __('Wallet Actions') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Manage your wallet and perform transactions efficiently.') }}
                        </p>
                    </header>
    
                    <!-- Wallet Balance Button -->
                    <div class="mt-4">
                        <button 
                            id="show-balance" 
                            class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-dark font-medium rounded-md hover:bg-blue-700 transition duration-150"
                            onclick="fetchWalletBalance()"
                        >
                            {{ __('Show Balance') }}
                        </button>
                        <p id="wallet-balance-display" class="mt-4 text-lg font-medium text-gray-800"></p>
                    </div>
    
                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-6"></div>
    
                    <!-- Pay Cart Button -->
                    <div>
                        <button 
                            id="pay-cart" 
                            class="w-full sm:w-auto px-4 py-2 bg-green-600 text-dark font-medium rounded-md hover:bg-green-700 transition duration-150"
                            onclick="payCart(10)" 
                        >
                            {{ __('Pay for Cart ($10)') }}
                        </button>
                    </div>
                </div>
            </div>
        </section>
    
        <script>
            // Function to display notifications
            function showNotification(message, type = 'success') {
                const notification = document.getElementById('notification');
                const notificationMessage = document.getElementById('notification-message');
    
                notificationMessage.textContent = message;

                // Apply styles based on the notification type
                if (type === 'success') {
                    notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 px-4 py-2 rounded-md shadow-lg bg-green-600 text-dark transition duration-300';
                } else if (type === 'error') {
                    notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 px-4 py-2 rounded-md shadow-lg bg-red-600 text-dark transition duration-300';
                } else if (type === 'warning') {
                    notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 px-4 py-2 rounded-md shadow-lg bg-yellow-600 text-dark transition duration-300';
                }

                // Show the notification
                notification.style.display = 'block';

                // Hide the notification after 3 seconds
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 3000);
            }
    
            // Function to Fetch Wallet Balance
            async function fetchWalletBalance() {
                try {
                    const response = await fetch('{{ route('wallet.balance') }}', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
    
                    if (response.ok) {
                        const data = await response.json();
                        document.getElementById('wallet-balance-display').textContent = `Current Balance: $${data.balance}`;
                    } else {
                        showNotification('Failed to fetch balance. Please try again.', 'error');
                    }
                } catch (error) {
                    console.error('Error fetching wallet balance:', error);
                    showNotification('An error occurred while fetching the balance. Please try again.', 'error');
                }
            }
    
            // Function to Pay for the Cart
            async function payCart(amount) {
                try {
                    const response = await fetch('{{ route('wallet.deduct') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ amount: amount, description: 'Cart Payment' })
                    });
    
                    if (response.ok) {
                        const data = await response.json();
                        showNotification('Payment successful!', 'success'); // Show success notification
                        fetchWalletBalance(); // Update displayed balance
                    } else {
                        const errorData = await response.json();
                        showNotification(errorData.error || 'Failed to process payment.', 'error');
                    }
                } catch (error) {
                    console.error('Error processing payment:', error);
                    showNotification('An error occurred while processing the payment. Please try again.', 'error');
                }
            }
        </script>
    </x-slot>

    <div class="py-8 bg-gray-100">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-md overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __("You're logged in!") }}</h3>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>