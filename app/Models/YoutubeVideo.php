<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Section;


class YoutubeVideo extends Model
{
    use HasFactory;

	protected $fillable = [
		'video_id',
	];

	public function sections()
	{
		return $this->hasMany(Section::class);
	}
}
