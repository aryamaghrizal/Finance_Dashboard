<x-filament-panels::page>
    <style>
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 75vh;
        }
        .chat-box {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        .message {
            margin-bottom: 1rem;
            display: flex;
            max-width: 90%;
        }
        .message.user {
            justify-content: flex-end;
            margin-left: auto;
        }
        .message.assistant {
            justify-content: flex-start;
            margin-right: auto;
        }
        .bubble {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            line-height: 1.5;
            word-wrap: break-word;
        }
        .bubble.user {
            background-color: rgb(59 130 246); /* Warna primer Filament */
            color: white;
        }
        .bubble.assistant {
            background-color: #e5e7eb; /* bg-gray-200 */
            color: #1f2937; /* text-gray-800 */
        }
        .dark .bubble.assistant {
            background-color: #374151; /* dark:bg-gray-700 */
            color: #f3f4f6; /* dark:text-gray-200 */
        }
        /* Styling untuk konten markdown dari AI */
        .prose-styles > *:first-child { margin-top: 0; }
        .prose-styles > *:last-child { margin-bottom: 0; }
        .prose-styles p { margin-bottom: 0.75em; }
        .prose-styles ul { list-style-type: disc; margin-left: 20px; margin-bottom: 0.75em; }
        .prose-styles ol { list-style-type: decimal; margin-left: 20px; margin-bottom: 0.75em; }
        .prose-styles strong, .prose-styles b { font-weight: 600; }
        .prose-styles code {
            font-family: monospace;
            background-color: #d1d5db;
            color: #111827;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 0.875em;
        }
        .dark .prose-styles code {
            background-color: #4b5563;
            color: #e5e7eb;
        }
    </style>

    <div
        class="chat-container bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10"
        x-data="{
            init() {
                this.scrollToBottom();
                Livewire.hook('element.updated', (el, component) => {
                    if (component.id === @js($this->getId())) {
                        this.scrollToBottom();
                    }
                });
            },
            scrollToBottom() {
                const chatBox = this.$refs.chatBox;
                // Sedikit delay agar DOM sempat update sebelum scroll
                setTimeout(() => {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }, 1);
            }
        }"
    >
        {{-- Kotak untuk menampilkan percakapan --}}
        <div x-ref="chatBox" class="chat-box">
            @foreach ($percakapan as $pesan)
                <div class="message {{ $pesan['role'] }}">
                    <div class="bubble {{ $pesan['role'] }}">
                        <div class="prose-styles">
                            {!! Str::markdown($pesan['content']) !!}
                        </div>
                    </div>
                </div>
            @endforeach

            @if($isLoading)
                 <div class="message assistant">
                    <div class="bubble assistant">
                        <x-filament::loading-indicator class="h-6 w-6" />
                    </div>
                </div>
            @endif
        </div>

        {{-- Form untuk mengirim pertanyaan --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <form wire:submit="submit" class="flex items-center gap-x-3">
                <div class="flex-grow">
                    <input
                        type="text"
                        wire:model="pertanyaan"
                        placeholder="Tulis pertanyaan Anda..."
                        class="fi-input block w-full border-gray-300 rounded-lg shadow-sm transition duration-75 focus:ring-primary-500 focus:border-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-primary-500"
                        autocomplete="off"
                        required
                        wire:loading.attr="disabled"
                        wire:target="submit"
                    >
                </div>
                <button
                    type="submit"
                    class="fi-btn fi-btn-size-md inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="submit"
                >
                    Kirim
                </button>
            </form>
        </div>
    </div>
</x-filament-panels::page>