<?php

namespace App\Http\Controllers;

use App\Models\SitePage;
use DB;

class PageController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Page Controller
    |--------------------------------------------------------------------------
    |
    | Displays site pages, editable from the admin panel.
    |
    */

    /**
     * Shows the page with the given key.
     *
     * @param string $key
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPage($key) {
        $page = SitePage::where('key', $key)->where('is_visible', 1)->first();
        if (!$page) {
            abort(404);
        }

        return view('pages.page', ['page' => $page]);
    }

    /**
     * Shows the credits page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreditsPage() {
        return view('pages.credits', [
            'credits'    => SitePage::where('key', 'credits')->first(),
            'extensions' => DB::table('site_extensions')->get(),
        ]);
    }

    /**
     * Shows the site calendar page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCalendarPage() {
        return view('pages.calendar');
    }

    /**
     * Returns all events from news, sales and prompts formatted as json.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarEvents() {
        $start_at = request()->get('start');
        $end_at = request()->get('end');

        // get all prompts within time frame, if hide_before_start is set or hide_after_end is set, filter them out if they are not within the prompt time frame
        $prompts = \App\Models\Prompt\Prompt::where('is_active', 1)->where('start_at', '>', $start_at)->where('end_at', '<', $end_at)
            ->where(function ($query) {
                $query->where('hide_before_start', 0)->orWhere('hide_after_end', 0)
                    ->orWhere(function ($query) {
                        $query->where('hide_before_start', 1)->where('start_at', '<=', now());
                    })
                    ->orWhere(function ($query) {
                        $query->where('hide_after_end', 1)->where('end_at', '>=', now());
                    });
            })->sortStart()->get();
        // get all news as long as either created_at is within timeframe, or if post_at is set, if post_at is within timeframe
        $news = \App\Models\News::visible()->where(function ($query) use ($start_at, $end_at) {
            $query->where('created_at', '>', $start_at)->where('created_at', '<', $end_at)
                ->orWhere(function ($query) use ($start_at, $end_at) {
                    $query->where('post_at', '>', $start_at)->where('post_at', '<', $end_at);
                });
        })->get();
        // get all sales as long as either created_at is within timeframe, or if post_at is set, if post_at is within timeframe
        $sales = \App\Models\Sales\Sales::visible()->where(function ($query) use ($start_at, $end_at) {
            $query->where('created_at', '>', $start_at)->where('created_at', '<', $end_at)
                ->orWhere(function ($query) use ($start_at, $end_at) {
                    $query->where('post_at', '>', $start_at)->where('post_at', '<', $end_at);
                });
        })->get();

        $array = [];
        foreach ($prompts as $prompt) {
            $array[] = [
                'id'    => $prompt->id,
                'title' => $prompt->name,
                'url'   => $prompt->url,
                'start' => $prompt->start_at->toW3cString(),
                'end'   => $prompt->end_at->toW3cString(),
                'color' => '#4c96d4',
            ];
        }
        // set start to start of 'post_at' day, if post_at is set, else use created_at
        // set end to end of 'post_at' day, if post_at is set, else use created_at
        foreach ($news as $news_item) {
            $array[] = [
                'id'     => $news_item->id,
                'title'  => $news_item->title,
                'url'    => $news_item->url,
                'start'  => $news_item->post_at ? $news_item->post_at->startOfDay()->toW3cString() : $news_item->created_at->startOfDay()->toW3cString(),
                'allDay' => "true",
                'color'  => '#ad283e',
            ];
        }
        foreach ($sales as $sale) {
            $array[] = [
                'id'     => $sale->id,
                'title'  => $sale->title,
                'url'    => $sale->url,
                'start'  => $sale->post_at ? $sale->post_at->startOfDay()->toW3cString() : $sale->created_at->startOfDay()->toW3cString(),
                // if there is a comment_open_at set, set end to that, else set end to start of next day
                'end'    => $sale->comments_open_at ? $sale->comments_open_at->endOfDay()->toW3cString() : null,
                'allDay' => "true",
                'color'  => '#f5a623',
            ];
        }

        return response()->json($array);
    }
}
