<?php

namespace TheLion\UseyourDrive\MediaPlayers;

// Exit if accessed directly.
defined('ABSPATH') || exit;

use TheLion\UseyourDrive\Processor;

$shortcode = Processor::instance()->get_shortcode();

$mode = $shortcode['mode'];

$classes = '';
if ('0' === $shortcode['playlistthumbnails']) {
    $classes .= 'no-thumbnails ';
}

if ('0' === $shortcode['show_filedate']) {
    $classes .= 'no-date ';
}

if ('audio' === $shortcode['mode']) {
    $classes .= 'nocover ';
}

$max_width = $shortcode['maxwidth'];
$extensions = (null !== $shortcode['extensions']) ? $shortcode['extensions'] : [];
$show_playlist = $shortcode['showplaylist'];
$show_playlistonstart = $shortcode['showplaylistonstart'];
$controls = implode(',', $shortcode['mediabuttons']);
$autoplay = $shortcode['autoplay'];

// Set extensions if still present in shortcode
$mp4key = array_search('mp4', $extensions);
if (false !== $mp4key) {
    unset($extensions[$mp4key]);
    if ('video' === $shortcode['mode']) {
        if (!in_array('m4v', $extensions)) {
            $extensions[] = 'm4v';
        }
    } else {
        if (!in_array('m4a', $extensions)) {
            $extensions[] = 'm4a';
        }
    }
}

$oggkey = array_search('ogg', $extensions);
if (false !== $oggkey) {
    unset($extensions[$oggkey]);
    if ('video' === $shortcode['mode']) {
        if (!in_array('ogv', $extensions)) {
            $extensions[] = 'ogv';
        }
    } else {
        if (!in_array('oga', $extensions)) {
            $extensions[] = 'oga';
        }
    }
}

if (empty($extensions)) {
    $extensions = ('audio' === $shortcode['mode']) ? ['mp3'] : ['m4v'];
}

$extensions = join(',', $extensions);
?>
<div id="jp_container_<?php echo Processor::instance()->get_listtoken(); ?>" class="jp_container jp-video <?php echo $classes; ?>" style="width:<?php echo $max_width; ?>;max-width:<?php echo $max_width; ?>;" data-autoplay="<?php echo $autoplay; ?>" data-extensions="<?php echo $extensions; ?>">
    <!--container in which our video will be played-->
    <div id="jquery_jplayer_<?php echo Processor::instance()->get_listtoken(); ?>" class="jp-jplayer"></div>

    <div class="playerScreen">
        <div tabindex="0" href="#" class="jp-video-play noload" style="display: none;">
            <div class="jp-video-play-button" role="button" tabindex="0" aria-label="Play" aria-pressed="false"></div>
        </div>
    </div>

    <!--main containers for our controls-->
    <div class="jp-gui">
        <div class="gui-container">
            <div tabindex="0" href="#" class="jp-gui-button jp-play left"></div>
            <div tabindex="0" href="#" class="jp-gui-button jp-pause left" style="display:none"></div>
            <div tabindex="0" href="#" class="jp-gui-button jp-next left"></div>

            <div class="volumecontrol left">
                <div tabindex="0" href="#" class="jp-gui-button jp-mute left"></div>
                <div tabindex="0" href="#" class="jp-gui-button jp-unmute left"></div>
                <div class="jp-volume-bar">
                    <div class="currentVolume">
                        <div class="jp-volume-bar-value"></div>
                    </div>
                </div>
            </div>

            <div class="jp-timer">
                <div class="jp-current-time">00:00</div>
                <div class="seperate">/</div>
                <div class="jp-duration">00:00</div>
            </div>

            <div href="#" tabindex="0" class="jp-gui-button jp-playlist-toggle right"></div>

            <div href="#" tabindex="0" class="jp-gui-button jp-full-screen right"></div>
            <div href="#" tabindex="0" class="jp-gui-button jp-restore-screen right" style="display:none"></div>

            <div href="#" tabindex="0" class="jp-gui-button jp-repeat right"></div>
            <div href="#" tabindex="0" class="jp-gui-button jp-repeat-off right" style="display:none"></div>

        </div>
        <!--end jp-gui-->
    </div>

    <div class="jp-progress">
        <div class="jp-seek-bar">
            <div class="jp-play-bar"></div>
        </div>
    </div>

    <div class="jp-playlist <?php echo ('1' === $show_playlistonstart) ? '' : 'hideonstart'; ?>" style="display:none;">
        <ul data-folder>
            <!-- The method Playlist.displayPlaylist() uses this unordered list -->
            <li></li>
        </ul>
    </div>
</div>