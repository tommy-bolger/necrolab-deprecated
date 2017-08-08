<?php
namespace Modules\Necrolab\Controllers\Cli;

use \DateTime;
use \Discord\Discord;
use \Discord\DiscordCommandClient;
use \Framework\Core\Controllers\Cli;
use \Framework\Utilities\Encryption;
use \Framework\Core\Loader;

class DiscordBot
extends Cli {     
    public function init() {
        Loader::load('autoload.php', true, false);
    }
    
    public function actionRun() {
        $discord = new DiscordCommandClient([
            'token' => '',
            'prefix' => '!nb '
        ]);

        $discord->registerCommand('ping', function ($message) {
            return 'pong!';
        }, [
            'description' => 'pong!',
        ]);

        $discord->run();
    }
}