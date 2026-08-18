{{-- Custom control bar for a YouTube embed with playerVars.controls = 0. The
     parent element must carry `group relative` — visibility is pure CSS
     :hover with no transition, so it appears/disappears instantly. --}}
<div class="absolute inset-0 cursor-pointer" @click="togglePlay()"></div>

<div
    class="pointer-events-none absolute inset-x-0 bottom-0 flex flex-col gap-2 bg-gradient-to-t from-black/85 to-transparent px-3 pb-2 pt-8 opacity-0 group-hover:pointer-events-auto group-hover:opacity-100 group-focus-within:pointer-events-auto group-focus-within:opacity-100"
    @click.stop
>
    <input
        type="range"
        min="0"
        :max="duration || 0"
        step="0.1"
        :value="currentTime"
        @input="onScrubInput($event)"
        @change="onScrubEnd($event)"
        class="h-1 w-full cursor-pointer accent-primary"
    >

    <div class="flex items-center gap-3">
        <button type="button" @click="togglePlay()" class="text-white hover:text-primary">
            <svg x-show="!playing" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M8 5v14l11-7z" /></svg>
            <svg x-show="playing" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M6 5h4v14H6zM14 5h4v14h-4z" /></svg>
        </button>

        <button type="button" @click="playNext()" class="text-white hover:text-primary">
            <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M6 5l8.5 7L6 19V5zM16 5h2v14h-2z" /></svg>
        </button>

        <span class="text-xs tabular-nums text-white/70" x-text="`${formatTime(currentTime)} / ${formatTime(duration)}`"></span>

        <div class="ml-auto flex items-center gap-2">
            <button type="button" @click="toggleMute()" class="text-white hover:text-primary">
                <svg x-show="!muted" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                    <path d="M3 10v4h4l5 5V5L7 10H3z" />
                    <path d="M16.5 12a4.5 4.5 0 0 0-2.5-4.03v8.06A4.5 4.5 0 0 0 16.5 12z" />
                </svg>
                <svg x-show="muted" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                    <path d="M3 10v4h4l5 5V5L7 10H3z" />
                    <path d="M19.8 9.6l-1.4-1.4-2.1 2.1-2.1-2.1-1.4 1.4 2.1 2.1-2.1 2.1 1.4 1.4 2.1-2.1 2.1 2.1 1.4-1.4-2.1-2.1z" />
                </svg>
            </button>
            <input
                type="range"
                min="0"
                max="100"
                :value="volume"
                @input="onVolumeInput($event)"
                class="h-1 w-16 cursor-pointer accent-primary"
            >

            <button type="button" @click="toggleFullscreen()" class="text-white hover:text-primary">
                <svg x-show="!fullscreen" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M4 4h6v2H6v4H4V4zm10 0h6v6h-2V6h-4V4zM4 14h2v4h4v2H4v-6zm16 0v6h-6v-2h4v-4h2z" /></svg>
                <svg x-show="fullscreen" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M9 4h2v6H5V8h4V4zm6 0h2v4h4v2h-6V4zM9 14v6H7v-4H3v-2h6zm6 0h6v2h-4v4h-2v-6z" /></svg>
            </button>
        </div>
    </div>
</div>
