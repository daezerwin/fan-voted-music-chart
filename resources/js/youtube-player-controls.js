// Shared Alpine state/methods for a custom YouTube control bar. The native
// embedded controls live inside a cross-origin iframe, so their own
// show/hide timing can't be retuned from the parent page — this mixin backs
// a custom bar instead, toggled purely by CSS :hover for an instant show/hide.
export default function youtubePlayerControls() {
    return {
        playing: false,
        duration: 0,
        currentTime: 0,
        volume: 100,
        muted: false,
        scrubbing: false,
        progressTimer: null,
        fullscreen: false,
        showControls: false,
        hideControlsTimer: null,

        bindPlayerControlEvents(player) {
            this.volume = player.getVolume();
            this.muted = player.isMuted();
        },

        onPlayerStateChange(event) {
            this.playing = event.data === YT.PlayerState.PLAYING;

            if (this.playing) {
                this.duration = this.player.getDuration();
                this.startProgressTimer();
            } else {
                this.stopProgressTimer();
            }
        },

        startProgressTimer() {
            this.stopProgressTimer();

            this.progressTimer = setInterval(() => {
                if (!this.scrubbing && this.player) {
                    this.currentTime = this.player.getCurrentTime();
                }
            }, 250);
        },

        stopProgressTimer() {
            clearInterval(this.progressTimer);
            this.progressTimer = null;
        },

        togglePlay() {
            if (!this.player || !this.ready) {
                return;
            }

            if (this.playing) {
                this.player.pauseVideo();
            } else {
                this.player.playVideo();
            }
        },

        onScrubInput(event) {
            this.scrubbing = true;
            this.currentTime = Number(event.target.value);
        },

        onScrubEnd(event) {
            this.scrubbing = false;
            this.player?.seekTo(Number(event.target.value), true);
        },

        toggleMute() {
            if (!this.player) {
                return;
            }

            if (this.muted) {
                this.player.unMute();
            } else {
                this.player.mute();
            }

            this.muted = this.player.isMuted();
        },

        onVolumeInput(event) {
            const value = Number(event.target.value);
            this.volume = value;
            this.player?.setVolume(value);
            this.muted = value === 0;

            if (value > 0 && this.player?.isMuted()) {
                this.player.unMute();
            }
        },

        toggleFullscreen() {
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                this.$refs.player?.requestFullscreen?.();
            }
        },

        onFullscreenChange() {
            this.fullscreen = document.fullscreenElement !== null;
        },

        // :hover/:focus-within reveal the bar on desktop; touchscreens have
        // neither, so a tap has to pin it open the same way, with a timeout
        // standing in for "stopped hovering".
        revealControls() {
            this.showControls = true;
            clearTimeout(this.hideControlsTimer);
            this.hideControlsTimer = setTimeout(() => {
                this.showControls = false;
            }, 3000);
        },

        formatTime(seconds) {
            if (!Number.isFinite(seconds) || seconds < 0) {
                return '0:00';
            }

            const whole = Math.floor(seconds);
            const minutes = Math.floor(whole / 60);
            const remainder = whole % 60;

            return `${minutes}:${String(remainder).padStart(2, '0')}`;
        },
    };
}
