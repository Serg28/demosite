<div x-data="{ messages: [], remove(message) { this.messages.splice(this.messages.indexOf(message), 1) } }"
     @notify.window="let message = $event.detail; key: Date.now(), messages.push(message); setTimeout(() => { remove(message.key) }, 5000)"
     class="notification">
    <template x-for="(message, messageIndex) in messages" :key="messageIndex" hidden>
        <div
             :class="`bg-${message.type}`"
             class="popup"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-transition:enter.scale.80
             x-transition:leave.scale.90
            >
                        <div class="icon">
                            <svg x-show="message.type === 'success'" class="success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>

                            <svg x-show="message.type === 'error'" class="error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>

                            <svg x-show="message.type === 'info'" class="info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>

                            <svg x-show="message.type === 'warning'" class="warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01" />
                            </svg>
                        </div>

                        <div class="content">
                            <p x-html="message.title" class="notification-title"></p>
                            <p x-html="message.message" class="notification-message"></p>
                        </div>
                        <div class="closer">
                            <button @click="remove(message.key)" class="">
                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
        </div>
    </template>
</div>
@assets
<link rel="stylesheet" href="{{mix('/assets/css/notification.css')}}">
@endassets