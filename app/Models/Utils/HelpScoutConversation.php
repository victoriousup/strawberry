<?php

namespace App\Models\Utils;

use Illuminate\Database\Eloquent\Model;

class HelpScoutConversation extends Model
{
	protected $table = 'helpscout_conversations';

	protected $guarded = ['id'];

	public $timestamps = false;
}
