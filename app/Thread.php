<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use App\Events\ThreadReceivedNewReply;


class Thread extends Model
{
    use RecordActivity;

    protected $fillable = [
        'channel_id', 'user_id', 'title', 'body',
    ];
    protected $with = ['creator', 'channel'];

    protected $guarded = [];
    protected $appends = ['isSubscribedTo'];

    public static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
//        static::addGlobalScope('replyCount', function ($builder) {
//            $builder->withCount('replies');
//        });
        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });
    }

    public
    function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public
    function replies()
    {
        return $this->hasMany(Reply::class)
            ->withCount('favorites')
            ->with('owner');
    }

    public
    function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public
    function channel()
    {
        return $this->belongsTo('App\Channel', 'channel_id');
    }

    public
    function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }


//    public function notifySubscribers($reply)
//    {
//        $this->subscriptions
//            ->where('user_id', '!=', $reply->user_id)
//            ->each
//            ->notify($reply);
//    }

    public
    function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    /**
     * @param int|null $userId
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);
        return $this;
    }

    /**
     *
     * @param int|null $userId
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * .
     *
     * @return boolean
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }
    public function hasUpdatesFor($user)
    {
        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }
}
