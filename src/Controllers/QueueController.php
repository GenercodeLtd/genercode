<?php

namespace GenerCode\Controllers;

use \GenerCode\Models\Queue;

class QueueController
{
    
    public function status($id)
    {
        $queue = Queue::find($id);
        if (!$queue->exists) {
            return response("success");
        } else {
            return response($queue->progress);
        }
    }
}
