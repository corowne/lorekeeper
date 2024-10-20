<?php

namespace App\Models\Prompt;

use Config;
use App\Models\Model;

class PromptExpReward extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prompt_id', 'user_exp', 'user_points', 'chara_exp',  'chara_points'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prompt_exp_rewards';
}
