<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
// use App\Services\EventIconManager;
use App\Services\EventIconService;
use Log;
use Illuminate\Http\Request;
use App\Models\EventIcon\EventIcon;
use Auth;

class EventIconController extends Controller {
    /**
     * Shows the event icons index.
     *
     * @param string $folder
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex($folder = null) {


        return view('admin.event_icon.index', [
            'eventIcons' => EventIcon::orderBy('sort', 'DESC')->get()
        ]);
    }

    /**
     * Uploads a event icon image file.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUploadEventIcon(Request $request, EventIconService $service) {
        $request->validate(EventIcon::$createRules);
        $data = $request->only('image','alt_text','link');

        if ($service->createEventIcon($data, Auth::user())) {
            flash('New eventIcon section uploaded successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Creates or edits an event icon.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteEventIcon(Request $request, EventIconService $service, $id) {
        if ($id && $service->deleteEventIcon(EventIcon::find($id), Auth::user())) {
            flash('EventIcon deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/event-icon');
    }

    public function getEditEventIcon($id) {
        $eventIcon = EventIcon::find($id);

        return view('admin.event_icon._edit_event_icon', [
            'eventIcon' => $eventIcon,
        ]);
    }

    /**
     * Creates or edits a event icon.
     *
     * @param App\Services\EventIconService $service
     * @param int|null                 $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditEventIcon(Request $request, EventIconService $service, $id = null) {
        $request->validate(EventIcon::$updateRules);
        $data = $request->only('image', 'alt_text', 'link', 'is_visible');
        if ($id && $service->updateEventIcon(EventIcon::find($id), $data, Auth::user())) {
            flash('Event Icon updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Sorts event icons.
     *
     * @param App\Services\EventIconService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortEventIcon(Request $request, EventIconService $service) {
        if ($service->sortEventIcon($request->get('sort'))) {
            flash('Event Icon order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    public function getDeleteEventIcon($id) {
        $eventIcon = EventIcon::find($id);

        return view('admin.event_icon._delete_event_icon', [
            'eventIcon' => $eventIcon,
        ]);
    }
}
