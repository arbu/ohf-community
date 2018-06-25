<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Iatstuti\Database\Support\NullableFields;

class WikiArticleAddon extends Model
{
	use NullableFields;

	protected $nullable = [
		'caption',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'args' => 'array',
    ];

    public function article() {
        return $this->belongsTo('App\WikiArticle', 'article_id');
    }
}
