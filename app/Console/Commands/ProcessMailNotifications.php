<?php

namespace App\Console\Commands;

use App\Http\Controllers\MailProcessingController;
use Illuminate\Console\Command;

class ProcessMailNotifications extends Command
{
    protected $signature = 'mail:process-notifications';
    protected $description = 'Process pending mail notifications';

    public function handle(): int
    {
        $controller = new MailProcessingController();
        $result = $controller->processPendingMails();
        
        $data = $result->getData();
        
        $this->info("Processed {$data->processed} emails, {$data->failed} failed");
        
        return Command::SUCCESS;
    }
}