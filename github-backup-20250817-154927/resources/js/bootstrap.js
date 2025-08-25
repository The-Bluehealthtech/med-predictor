import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Laravel Echo configuration for real-time updates
// Only initialize if Pusher environment variables are available
if (import.meta.env.VITE_PUSHER_APP_KEY && import.meta.env.VITE_PUSHER_APP_CLUSTER) {
    // Use dynamic imports to avoid issues with missing modules
    Promise.all([
        import('laravel-echo'),
        import('pusher-js')
    ]).then(([EchoModule, PusherModule]) => {
        const Echo = EchoModule.default;
        const Pusher = PusherModule.default;
        
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true
        });
    }).catch(error => {
        console.log('Failed to load Pusher modules:', error);
        createMockEcho();
    });
} else {
    console.log('Pusher configuration not found - real-time features disabled');
    createMockEcho();
}

function createMockEcho() {  // Create a mock Echo object to prevent errors
    window.Echo = {
        channel: () => ({
            listen: () => ({ stop: () => {} }),
            subscribed: () => false
        }),
        private: () => ({
            listen: () => ({ stop: () => {} }),
            subscribed: () => false
        })
    };
}
