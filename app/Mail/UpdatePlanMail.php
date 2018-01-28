<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Plan;
use App\User;

class UpdatePlanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plan;
    /**
     * Create a new message instance.
     *
     * @return void
     */
     public function __construct(User $user, Plan $plan)
     {
         $this->user=$user;
         $this->plan=$plan;
     }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Workout plan updated')->view('updatemail');
    }
}
