<?php

namespace App\Policies\Wiki;

use App\User;
use App\WikiArticle;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can list articles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->hasPermission('wiki.view');
    }

    /**
     * Determine whether the user can view the wiki article.
     *
     * @param  \App\User  $user
     * @param  \App\WikiArticle  $wikiArticle
     * @return mixed
     */
    public function view(User $user, WikiArticle $wikiArticle)
    {
        return $user->hasPermission('wiki.view');
    }

    /**
     * Determine whether the user can create wiki articles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('wiki.edit');
    }

    /**
     * Determine whether the user can update the wiki article.
     *
     * @param  \App\User  $user
     * @param  \App\WikiArticle  $wikiArticle
     * @return mixed
     */
    public function update(User $user, WikiArticle $wikiArticle)
    {
        return $user->hasPermission('wiki.edit');
    }

    /**
     * Determine whether the user can delete the wiki article.
     *
     * @param  \App\User  $user
     * @param  \App\WikiArticle  $wikiArticle
     * @return mixed
     */
    public function delete(User $user, WikiArticle $wikiArticle)
    {
        return $user->hasPermission('wiki.delete');
    }
}