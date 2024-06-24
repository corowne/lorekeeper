<?php

namespace App\Http\Controllers\Comments;

use App\Facades\Notifications;
use App\Facades\Settings;
use App\Models\Comment\Comment;
use App\Models\Gallery\GallerySubmission;
use App\Models\News;
use App\Models\Report\Report;
use App\Models\Sales\Sales;
use App\Models\SitePage;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Spatie\Honeypot\ProtectAgainstSpam;

class CommentController extends Controller {
    public function __construct() {
        $this->middleware('web');

        if (config('comments.guest_commenting') == true) {
            $this->middleware('auth')->except('store');
            $this->middleware(ProtectAgainstSpam::class)->only('store');
        } else {
            $this->middleware('auth');
        }
    }

    /**
     * Creates a new comment for given model.
     *
     * @param mixed $model
     * @param mixed $id
     */
    public function store(Request $request, $model, $id) {
        $model = urldecode(base64_decode($model));

        $accepted_models = config('lorekeeper.allowed_comment_models');
        if (!count($accepted_models)) {
            flash('Invalid Models')->error();

            return redirect()->back();
        }

        if (!in_array($model, $accepted_models)) {
            abort(404);
        }

        // If guest commenting is turned off, authorize this action.
        if (config('comments.guest_commenting') == false) {
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
            'message'          => 'required|string',
        ]))->validate();

        $base = $model::findOrFail($id);
        if (isset($base->is_visible) && !$base->is_visible) {
            flash('Invalid Model')->error();

            return redirect()->back();
        }

        $commentClass = config('comments.model');
        $comment = new $commentClass;

        if (!Auth::check()) {
            $comment->guest_name = $request->guest_name;
            $comment->guest_email = $request->guest_email;
        } else {
            $comment->commenter()->associate(Auth::user());
        }

        $comment->commentable()->associate($base);

        $comment->comment = config('lorekeeper.settings.wysiwyg_comments') ? parse($request->message) : $request->message;
        $comment->approved = !config('comments.approval_required');

        $comment->type = isset($request['type']) && $request['type'] ? $request['type'] : 'User-User';
        $comment->title = isset($request['title']) && $request['title'] ? $request['title'] : null;
        $comment->save();

        $recipient = null;
        $post = null;
        $model_type = $comment->commentable_type;
        //getting user who commented
        $sender = User::find($comment->commenter_id);
        $type = $comment->type;

        switch ($model) {
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
            case 'App\Models\Forum':
                flash('Thread created successfully.')->success();

                return redirect('/forum/'.$comment->commentable_id.'/~'.$comment->id);
                break;
            default:
                throw new \Exception('Comment type not supported.');
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
    public function update(Request $request, Comment $comment) {
        Gate::authorize('edit-comment', $comment);

        Validator::make($request->all(), [
            'message' => 'required|string',
            'title'   => 'nullable|string',
        ])->validate();

        // add history
        $comment->edits()->create([
            'user_id'    => Auth::user()->id,
            'comment_id' => $comment->id,
            'data'       => json_encode([
                'action'      => 'edit',
                'old_comment' => config('lorekeeper.settings.wysiwyg_comments') ? parse($comment->comment) : $comment->comment,
                'new_comment' => config('lorekeeper.settings.wysiwyg_comments') ? parse($request->message) : $request->message,
            ]),
        ]);

        $comment->update([
            'comment' => config('lorekeeper.settings.wysiwyg_comments') ? parse($request->message) : $request->message,
            'title'   => $request->title ?? null,
        ]);

        return Redirect::to(URL::previous().'#comment-'.$comment->getKey());
    }

    /**
     * Deletes a comment.
     */
    public function destroy(Comment $comment) {
        Gate::authorize('delete-comment', $comment);

        $forum = null;
        if ($comment->commentable_type == 'App\Models\Forum' && $comment->children) {
            if (isset($comment->child_id)) {
                foreach ($comment->children as $child) {
                    $child->child_id = $comment->child_id;
                    $child->save();
                }
            } else {
                foreach ($comment->children as $child) {
                    $child->delete();
                }
            }
        } // You may want to remove the inner if statement and move the isset into the container if statement if you don't want comments deleted when their threads are deleted.

        if ($comment->commentable_type == 'App\Models\Forum') {
            if ($comment->parent) {
                $forum = $comment->parent->threadUrl;
                flash('Reply deleted successfully.')->success();
            } else {
                $forum = $comment->commentable->url;
                flash('Thread deleted successfully.')->success();
            }
        }

        if (config('comments.soft_deletes') == true) {
            $comment->delete();
        } else {
            $comment->forceDelete();
        }
        if (isset($forum)) {
            return redirect($forum);
        } else {
            return redirect()->back();
        }
    }

    /**
     * Creates a reply "comment" to a comment.
     */
    public function reply(Request $request, Comment $comment) {
        Gate::authorize('reply-to-comment', $comment);

        Validator::make($request->all(), [
            'message' => 'required|string',
        ])->validate();

        $commentClass = config('comments.model');
        $reply = new $commentClass;
        $reply->commenter()->associate(Auth::user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        $reply->comment = config('lorekeeper.settings.wysiwyg_comments') ? parse($request->message) : $request->message;
        $reply->type = $comment->type;
        $reply->approved = !config('comments.approval_required');
        $reply->save();

        // url = url('comments/32')

        $sender = User::find($reply->commenter_id);
        $recipient = User::find($comment->commenter_id);

        if ($reply->commentable_type == 'App\Models\Forum' && $recipient != $sender) {
            Notifications::create('THREAD_REPLY', $recipient, [
                'sender_url'   => $sender->url,
                'sender'       => $sender->name,
                'comment_url'  => $reply->id,
                'thread_url'   => $comment->topComment->id,
                'thread_title' => $comment->topComment->title,
                'forum_url'    => $comment->commentable_id,
                'forum_name'   => $comment->commentable->name,
            ]);

            return redirect(URL::previous().'#comment-'.$reply->getKey());
        } elseif ($recipient != $sender) {
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
    public function feature($id) {
        $comment = Comment::find($id);
        if ($comment->is_featured == 0) {
            $comment->update(['is_featured' => 1]);
        } else {
            $comment->update(['is_featured' => 0]);
        }
        $comment->timestamps = true;

        return Redirect::to(URL::previous().'#comment-'.$comment->getKey());
    }

    /**
     * Is featured for comments.
     *
     * @param mixed $id
     */
    public function lock($id) {
        $comment = Comment::find($id);
        $comment->timestamps = false;
        if ($comment->is_locked == 0) {
            $comment->update(['is_locked' => 1]);
        } else {
            $comment->update(['is_locked' => 0]);
        }
        $comment->timestamps = true;

        return Redirect::to(URL::previous().'#comment-'.$comment->getKey());
    }

    /**
     * Likes / Unlikes a comment.
     *
     * @param mixed $id
     * @param mixed $action
     */
    public function like(Request $request, $id, $action = 1) {
        $user = Auth::user();
        if (!$user) {
            return Redirect::back();
        }
        $comment = Comment::findOrFail($id);

        if ($comment->likes()->where('user_id', $user->id)->exists()) {
            if ($action == $comment->likes()->where('user_id', $user->id)->first()->is_like) {
                $comment->likes()->where('user_id', $user->id)->delete();
            }
            // else invert the bool
            else {
                $comment->likes()->where('user_id', $user->id)->update(['is_like' => !$comment->likes()->where('user_id', $user->id)->first()->is_like]);
            }

            return Redirect::to(URL::previous().'#comment-'.$comment->getKey());
        }

        $comment->likes()->create([
            'user_id' => $user->id,
            'is_like' => $action,
        ]);

        return Redirect::to(URL::previous().'#comment-'.$comment->getKey());
    }

    /**
     * Shows a user's liked comments.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLikedComments(Request $request) {
        return view('home.liked_comments', [
            'user' => Auth::user(),
        ]);
    }
}
