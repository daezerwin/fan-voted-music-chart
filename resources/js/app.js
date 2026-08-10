import Alpine from 'alpinejs';
import topTenPlayer from './top-ten-player';

window.Alpine = Alpine;
Alpine.data('topTenPlayer', topTenPlayer);
Alpine.start();
