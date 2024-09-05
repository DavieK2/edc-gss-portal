<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Command;

class SyncSchemesToSessionCommand extends Command
{
   
    protected $signature = 'command:name';

   
    protected $description = 'Command description';

   
    public function handle()
    {
        $schemes = ['EDC', 'GSS'];
        $sessions = Session::withoutGlobalScopes()->get();
    
    
        $sessions->each(function($session) use($schemes){
            $session->update(['can_register' => $schemes ]);
        });
    }
}
