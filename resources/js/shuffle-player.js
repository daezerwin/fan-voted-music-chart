export default function shufflePlayer(videoId, nextUrl) {
    return {
        videoId,
        nextUrl,
        player: null,
        ready: false,

        init() {
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
            this.player = new YT.Player('shuffle-youtube-player', {
                videoId: this.videoId,
                playerVars: { rel: 0, autoplay: 1 },
                events: {
                    onReady: () => {
                        this.ready = true;
                    },
                    onStateChange: (event) => {
                        if (event.data === YT.PlayerState.ENDED) {
                            this.playNext();
                        }
                    },
                    // Unavailable video or embedding disabled — skip it rather than stall.
                    onError: () => {
                        this.playNext();
                    },
                },
            });
        },

        playNext() {
            window.location.href = this.nextUrl;
        },
    };
}
