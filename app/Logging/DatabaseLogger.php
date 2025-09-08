<?php

namespace App\Logging;

use Illuminate\Support\Facades\DB;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DatabaseLogger 
{
   final static function log(array $record): void
    {
        DB::table('logs')->insert([
            'level' => $record['level_name'],
            'user' => $record['user'],
            'ip' => $record['ip'],
            'message' => $record['message'],
            'entity_type' => $record['entity_type'],
            'entity_id' => $record['entity_id'],
            'context' => json_encode($record['context']),
            'created_at' => now(),
        ]);
    }
    

    static function write(array $record): void
    {
        DB::table('logs')->insert([
            'level' => $record['level_name'],
            'user' => $record['user'],
            'message' => $record['message'],
            'context' => json_encode($record['context']),
            'created_at' => now(),
        ]);
    }

    static function info(array $record): void
    {
        DB::table('logs')->insert([
            'level' => 'INFO',
            'user' => $record['user'],
            'message' => $record['message'],
            'context' => json_encode($record['context']),
            'created_at' => now(),
        ]);
    }

    static function create(array $record): void
    {
        DB::table('logs')->insert([
            'level' => 'CREATE',
            'user' => $record['user'],
            'message' => $record['message'],
            'context' => json_encode($record['context']),
            'created_at' => now(),
        ]);
    }

    static function update(array $record): void
    {
        DB::table('logs')->insert([
            'level' => "UPDATE",
            'user' => $record['user'],
            'message' => $record['message'],
            'context' => json_encode($record['context']),
            'created_at' => now(),
        ]);
    }

    static function delete(array $record): void
    {
        DB::table('logs')->insert([
            'level' => 'DELETE',
            'user' => $record['user'],
            'message' => $record['message'],
            'context' => json_encode($record['context']),
            'created_at' => now(),
        ]);
    }

    static function view(array $record): void
    {
        DB::table('logs')->insert([
            'level' => 'VIEW',
            'user' => $record['user'],
            'message' => $record['message'],
            'context' => json_encode($record['context']),
            'created_at' => now(),
        ]);
    }

    
}
