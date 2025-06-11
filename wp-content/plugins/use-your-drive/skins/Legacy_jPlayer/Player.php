<?php

namespace TheLion\UseyourDrive\MediaPlayers;

use TheLion\UseyourDrive\MediaplayerSkin;

defined('ABSPATH') || exit;

class Legacy_jPlayer extends MediaplayerSkin
{
    public $url;
    public $template_path = __DIR__.'/Template.php';

    public function __construct()
    {
        $this->url = plugins_url('', __FILE__);
    }

    public function load_scripts()
    {
        if (defined('USEYOURDRIVE_SCRIPTS'.__CLASS__.'_LOADED')) {
            return;
        }

        wp_register_script('Legacy_jPlayer.Playlist', $this->get_url().'/js/jplayer.playlist.min.js', ['jquery'], USEYOURDRIVE_VERSION);
        wp_register_script('UseyourDrive.Legacy_jPlayer.jPlayer', $this->get_url().'/js/jquery.jplayer.min.js', ['jquery', 'Legacy_jPlayer.Playlist'], USEYOURDRIVE_VERSION, true);
        wp_register_script('UseyourDrive.Legacy_jPlayer.Player', $this->get_url().'/js/Player.js', ['UseyourDrive.Legacy_jPlayer.jPlayer', 'jquery-ui-slider', 'UseyourDrive'], USEYOURDRIVE_VERSION, true);

        wp_enqueue_script('UseyourDrive.Legacy_jPlayer.Player');

        $localize_mediaplayer = [
            'player_url' => $this->get_url(),
        ];

        wp_localize_script('UseyourDrive.Legacy_jPlayer.Player', 'Legacy_jPlayer_vars', $localize_mediaplayer);

        define('USEYOURDRIVE_SCRIPTS'.__CLASS__.'_LOADED', true);
    }

    public function load_styles()
    {
        wp_register_style('UseyourDrive.Legacy_jPlayer.Player.CSS', $this->get_url().'/css/style.css', false, USEYOURDRIVE_VERSION);
        wp_enqueue_style('UseyourDrive.Legacy_jPlayer.Player.CSS');
    }
}
