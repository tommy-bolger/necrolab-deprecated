<?php
namespace Modules\Necrolab\Models;

use \Exception;
use \DateTime;
use \DateInterval;
use \PhpAmqpLib\Connection\AMQPStreamConnection;
use \PhpAmqpLib\Message\AMQPMessage;
use \Framework\Core\Framework;
use \Framework\Modules\Module;
use \Framework\Core\Loader;

class Necrolab {
    protected static $lua_script_path;
    
    protected static $queue_server_connection;
    
    public static function getFullClassName() {
        return get_called_class();
    }
    
    public static function getDatesFromRange(DateTime $start_date, DateTime $end_date) {
        $current_date = clone $start_date;
        
        $dates = array();
        
        while($current_date <= $end_date) {
            $dates[] = $current_date->format('Y-m-d');
        
            $current_date->add(new DateInterval('P1D'));
        }
        
        return $dates;
    }
    
    protected static function getPartitionTableNames($base_name, $start_date, $end_date) {
        $partition_table_names = array();
        
        $current_date = new DateTime($start_date->format('Y-m-01'));
        
        while($current_date <= $end_date) {
            $partition_table_names[] = "{$base_name}_{$current_date->format('Y_m')}";
        
            $current_date->add(new DateInterval('P1M'));
        }
        
        return $partition_table_names;
    }
    
    public static function getLuaScriptPath() {
        if(empty(self::$lua_script_path)) {
            self::$lua_script_path = Module::getInstance('necrolab')->getScriptFilePath() . '/lua';
        }
        
        return self::$lua_script_path;
    }
    
    public static function getQueueServerConnection() {
        if(!isset(self::$queue_server_connection)) {
            Loader::load('autoload.php', true, false);
            
            self::$queue_server_connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        }
        
        return self::$queue_server_connection;
    }
    
    public static function getNewQueueServerMessageInstance($message) {
        return new AMQPMessage($message);
    }
    
    public static function addDateToQueue($queue_name, DateTime $date) {
        $queue_server_connection = static::getQueueServerConnection();
        $queue_server_channel = $queue_server_connection->channel();
        
        $queue_server_channel->queue_declare($queue_name, false, false, false, false);
        
        $queue_message = static::getNewQueueServerMessageInstance($date->format('Y-m-d'));
        $queue_server_channel->basic_publish($queue_message, '', $queue_name);
    }
    
    public static function runQueue($queue_name, $callback) {
        $queue_server_connection = static::getQueueServerConnection();
        $queue_server_channel = $queue_server_connection->channel();

        $queue_server_channel->queue_declare($queue_name, false, false, false, false);
        
        $queue_server_channel->basic_consume($queue_name, '', false, true, false, false, $callback);
        
        while(count($queue_server_channel->callbacks)) {
            $queue_server_channel->wait();
        }
        
        $queue_server_channel->close();
        $queue_server_connection->close();
    }
    
    public static function generateRankPoints($rank) {
        return 1.7 / (log($rank / 100 + 1.03) / log(10));
    }
    
    public static function encodeRecord(array $record) {
        return base64_encode(gzencode(json_encode($record, JSON_UNESCAPED_UNICODE), 4));
    }
    
    public static function decodeRecord($encoded_record) {
        return json_decode(gzdecode(base64_decode($encoded_record)), true);
    }
}