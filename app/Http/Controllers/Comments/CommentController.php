<?php

namespace App\Http\Controllers\Comments;

use App\Models\Comment;
use App\Models\Gallery\GallerySubmission;
use App\Models\News;
use App\Models\Report\Report;
use App\Models\Sales\Sales;
use App\Models\SitePage;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Notifications;
use Settings;
use Spatie\Honeypot\ProtectAgainstSpam;

class CommentController extends Controller implements CommentControllerInterface
{
    public function __construct()
    {
        $this->middleware('web');

        if (Config::get('comments.guest_commenting') == true) {
            $this->middleware('auth')->except('store');
            $this->middleware(ProtectAgainstSpam::class)->only('store');
        } else {
            $this->middleware('auth');
        }
    }

    /**
     * Creates a new comment for given model.
     */
    public function store(Request $request)
    {

        // If guest commenting is turned off, authorize this action.
        if (Config::get('comments.guest_commenting') == false) {
            Gate::authorize('create-comment', Comment::class);
        }

        // Define guest rules if user is not logged in.
        if (!Auth::check()) {
            $guest_rules = [
                'guest_name'  => 'required|string|max:255',
                'guest_email' => 'required|string|email|max:255',
            ];
        }

        // Merge guest rules, if any, with normal validation rules.
        Validator::make($request->all(), array_merge($guest_rules ?? [], [
            'commentable_type' => 'required|string',
            'commentable_id'   => 'required|string|min:1',
            'message'          => 'required|string',
        ]))->validate();

        $model = $request->commentable_type::findOrFail($request->commentable_id);

        $commentClass = Config::get('comments.model');
        $comment = new $commentClass;

        if (!Auth::check()) {
            $comment->guest_name = $request->guest_name;
            $comment->guest_email = $request->guest_email;
        } else {
            $comment->commenter()->associate(Auth::user());
        }

        $comment->commentable()->associate($model);
        $comment->comment = $request->message;
        $comment->approved = !Config::get('comments.approval_required');
        $comment->type = isset($request['type']) && $request['type'] ? $request['type'] : 'User-User';
        $comment->save();

        $recipient = null;
        $post = null;
        $model_type = $comment->commentable_type;
        //getting user who commented
        $sender = User::find($comment->commenter_id);
        $type = $comment->type;

        switch ($model_type) {
            case 'App\Models\User\UserProfile':
                $recipient = User::find($comment->commentable_id);
                $post = 'your profile';
                $link = $recipient->url.'/#comment-'.$comment->getKey();
                break;
            case 'App\Models\Sales\Sales':
                $sale = Sales::find($comment->commentable_id);
                $recipient = $sale->user; // User that has been commented on (or owner of sale post)
                $post = 'your sales post'; // Simple message to show if it's profile/sales/news
                $link = $sale->url.'/#comment-'.$comment->getKey();
                break;
            case 'App\Models\News':
                $news = News::find($comment->commentable_id);
                $recipient = $news->user; // User that has been commented on (or owner of sale post)
                $post = 'your news post'; // Simple message to show if it's profile/sales/news
                $link = $news->url.'/#comment-'.$comment->getKey();
                break;
            case 'App\Models\Report\Report':
                $report = Report::find($comment->commentable_id);
                $recipients = $report->user; // User that has been commented on (or owner of sale post)
                $post = 'your report'; // Simple message to show if it's profile/sales/news
                $link = 'reports/view/'.$report->id.'/#comment-'.$comment->getKey();
                if ($recipients == $sender) {
                    $recipient = (isset($report->staff_id) ? $report->staff : User::find(Settings::get('admin_user')));
                } else {
                    $recipient = $recipients;
                }
                break;
            case 'App\Models\SitePage':
                $page = SitePage::find($comment->commentable_id);
                $recipient = User::find(Settings::get('admin_user'));
                $post = 'your site page';
                $link = $page->url.'/#comment-'.$comment->getKey();
                break;
            case 'App\Models\Gallery\GallerySubmission':
                $submission = GallerySubmission::find($comment->commentable_id);
                if ($type == 'Staff-Staff') {
                    $recipient = User::find(Settings::get('admin_user'));
                } else {
                    $recipient = $submission->user;
                }
                $post = (($type != 'User-User') ? 'your gallery submission\'s staff comments' : 'your gallery submission');
                $link = (($type != 'User-User') ? $submission->queueUrl.'/#comment-'.$comment->getKey() : $submission->url.'/#comment-'.$comment->getKey());
                break;
            }

        if ($recipient != $sender) {
            Notifications::create('COMMENT_MADE', $recipient, [
                'comment_url' => $link,
                'post_type'   => $post,
                'sender'      => $sender->name,
                'sender_url'  => $sender->url,
            ]);
        }

        return Redirect::to(URL::previous().'#comment-'.$comment->getKey());
    }

    /**
     * Updates the message of the comment.
     */
    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('edit-comment', $comment);

        Validator::make($request->all(), [
            'message' => 'required|string',
        ])->validate();

        $comment->update([
            'comment' => $request->message,
        ]);

        return Redirect::to(URL::previous().'#comment-'.$comment->getKey());
    }

    /**
     * Deletes a comment.
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete-comment', $comment);

        if (Config::get('comments.soft_deletes') == true) {
            $comment->delete();
        } else {
            $comment->forceDelete();
        }

        return Redirect::back();
    }

    /**
     * Creates a reply "comment" to a comment.
     */
    public function reply(Request $request, Comment $comment)
    {
        Gate::authorize('reply-to-comment', $comment);

        Validator::make($request->all(), [
            'message' => 'required|string',
        ])->validate();

        $commentClass = Config::get('comments.model');
        $reply = new $commentClass;
        $reply->commenter()->associate(Auth::user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        $reply->comment = $request->message;
        $reply->type = $comment->type;
        $reply->approved = !Config::get('comments.approval_required');
        $reply->save();

        // url = url('comments/32')

        $sender = User::find($reply->commenter_id);
        $recipient = User::find($comment->commenter_id);

        // if($sender == $recipient)
        if ($recipient != $sender) {
            Notifications::create('COMMENT_REPLY', $recipient, [
            'sender_url'  => $sender->url,
            'sender'      => $sender->name,
            'comment_url' => $comment->id,
            ]);
        }

        return Redirect::to(URL::previous().'#comment-'.$reply->getKey());
    }

    /**
     * Is featured for comments.
     *
     * @param mixed $id
     */
    public function feature($id)
    {
        $comment = Comment::find($id);
        if ($comment->is_featured == 0) {
            $comment->update(['is_featured' => 1]);
        } else {
            $comment->update(['is_featured' => 0]);
        }

        return Redirect::to(URL::previous().'#comment-'.$comment->getKey());
    }
}
