<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\YoutubeVideo;

class Section extends Model
{
    use HasFactory;

	protected $fillable = [
		'section_number',
		'title',
		'text',
		'youtube_video_id',
	];

	public function youtube_video()
	{
		return $this->belongsTo(YoutubeVideo::class);
	}

	public function summaries()
	{
		return $this->hasMany(Summary::class);
	}
}
