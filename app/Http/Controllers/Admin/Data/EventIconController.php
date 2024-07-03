<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Services\EventIconManager;
use Illuminate\Http\Request;
use App\Models\EventIcon\EventIcon;
use Auth;

class EventIconController extends Controller {
    /**
     * Shows the files index.
     *
     * @param string $folder
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex($folder = null) {


        return view('admin.event_icon.index', [
            'eventIcons' => EventIcon::all()
        ]);
    }

    /**
     * Uploads a site image file.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUploadEventIcon(Request $request, EventIconManager $service) {
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
     * Creates or edits an item.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteEventIcon(Request $request, EventIconManager $service, $id) {
        if ($id && $service->deleteEventIcon(EventIcon::find($id), Auth::user())) {
            flash('EventIcon deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/event-icon');
    }

    public function getDeleteEventIcon($id) {
        $eventIcon = EventIcon::find($id);

        return view('admin.event_icon._delete_event_icon', [
            'eventIcon' => $eventIcon,
        ]);
    }
}
