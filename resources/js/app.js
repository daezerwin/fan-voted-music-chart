import Alpine from 'alpinejs';
import topTenPlayer from './top-ten-player';
import shufflePlayer from './shuffle-player';

window.Alpine = Alpine;
Alpine.data('topTenPlayer', topTenPlayer);
Alpine.data('shufflePlayer', shufflePlayer);
Alpine.start();
