import youtubePlayerControls from './youtube-player-controls';

export default function topTenPlayer(queue) {
    return {
        ...youtubePlayerControls(),
        queue,
        index: 0,
        player: null,
        ready: false,

        get current() {
            return this.queue[this.index] ?? null;
        },

        init() {
            if (this.queue.length === 0) {
                return;
            }

            if (window.YT && window.YT.Player) {
                this.createPlayer();

                return;
            }

            window.onYouTubeIframeAPIReady = () => this.createPlayer();

            if (!document.getElementById('youtube-iframe-api')) {
                const tag = document.createElement('script');
                tag.id = 'youtube-iframe-api';
                tag.src = 'https://www.youtube.com/iframe_api';
                document.head.appendChild(tag);
            }
        },

        createPlayer() {
            this.player = new YT.Player('youtube-player', {
                videoId: this.current.videoId,
                playerVars: { rel: 0, controls: 0 },
                events: {
                    onReady: () => {
                        this.ready = true;
                        this.bindPlayerControlEvents(this.player);
                    },
                    onStateChange: (event) => {
                        this.onPlayerStateChange(event);

                        if (event.data === YT.PlayerState.ENDED) {
                            this.playNext();
                        }
                    },
                    // Unavailable video or embedding disabled — skip it rather than stall the queue.
                    onError: () => {
                        this.playNext();
                    },
                },
            });
        },

        playAt(index) {
            if (index < 0 || index >= this.queue.length) {
                return;
            }

            this.index = index;

            if (this.player && this.ready) {
                this.player.loadVideoById(this.current.videoId);
            }
        },

        playNext() {
            if (this.index + 1 < this.queue.length) {
                this.playAt(this.index + 1);
            }
        },

        playPrevious() {
            if (this.index > 0) {
                this.playAt(this.index - 1);
            }
        },
    };
}
