<?php

namespace App\Events;

use App\User;
use App\Office;
use App\PaymentMethod;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LoanAccountPayment implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;

    public $office_id;
    public $msg;
    
    public function __construct($payload,$office_id,$user_id,$payment_method_id)
    {
     
        $office = Office::find($office_id)->name;
        $by = User::find($user_id)->full_name;
        $payment = PaymentMethod::find($payment_method_id)->name;
        $payload['msg'] = 'Repayment '. money($payload['amount'],2) .' at ' . $office .' by ' . $by. ' ['.$payment.'].';
        $this->data = $payload;
        $this->office_id = $office_id;
        
    }
    

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('dashboard.charts.repayment.'.$this->office_id),
            new PrivateChannel('dashboard.notifications.'.$this->office_id)
        ];
    }
    public function broadcastAs(){
        return 'loan-payment';
    }
}
